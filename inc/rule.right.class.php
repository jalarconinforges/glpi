<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2009 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file: Walid Nouh
// Purpose of file:
// ----------------------------------------------------------------------
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

/// Rule class for Rights management
class RightAffectRule extends Rule {

   /**
    * Constructor
   **/
   function __construct() {
      global $RULES_CRITERIAS;

      parent::__construct(RULE_AFFECT_RIGHTS);

      //Dynamically add all the ldap criterias to the current list of rule's criterias
      $this->addLdapCriteriasToArray();
      $this->right="rule_ldap";
      $this->orderby="name";
   }

   function preProcessPreviewResults($output) {
      return $output;
   }

   function maxActionsCount() {
      // Unlimited
      return 4;
   }

   /**
    * Display form to add rules
    * @param $target where to post form
    * @param $ID entity ID
    */
   function showAndAddRuleForm($target, $ID) {
      global $LANG, $CFG_GLPI;

      $canedit = haveRight($this->right, "w");
      echo "<form name='ldapaffectation_form' id='ldapaffectation_form' method='post' ".
             "action=\"$target\">";

      if ($canedit) {
         echo "<div class='center'>";
         echo "<table  class='tab_cadre_fixe'>";
         echo "<tr><th colspan='2'>" .$LANG['rulesengine'][19] . "</th></tr>\n";

         echo "<tr class='tab_bg_1'>";
         echo "<td>".$LANG['common'][16] . "&nbsp;:&nbsp;";
         autocompletionTextField("name", "glpi_rules", "name", "", 33);
         echo "&nbsp;&nbsp;&nbsp;".$LANG['joblist'][6] . "&nbsp;:&nbsp;";
         autocompletionTextField("description", "glpi_rules", "description", "", 33);
         echo "&nbsp;&nbsp;&nbsp;".$LANG['rulesengine'][9] . "&nbsp;:&nbsp;";
         $this->dropdownRulesMatch("match", "AND");
         echo "</td><td rowspan='2' class='tab_bg_2 center middle'>";
         echo "<input type=hidden name='sub_type' value=\"" . $this->sub_type . "\">";
         echo "<input type=hidden name='entities_id' value='-1'>";
         echo "<input type=hidden name='affectentity' value='$ID'>";
         echo "<input type='submit' name='add_user_rule' value=\"" . $LANG['buttons'][8] .
                "\" class='submit'>";
         echo "</td></tr>\n";

         echo "<tr class='tab_bg_1'>";
         echo "<td class='center'>".$LANG['profiles'][22] . "&nbsp;:&nbsp;";
         dropdownValue("glpi_profiles","profiles_id");
         echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$LANG['profiles'][28] . "&nbsp;:&nbsp;";
         dropdownYesNo("is_recursive",0);
         echo "</td></tr>\n";

         echo "</table></div><br>";
      }
      echo "<div class='center'><table class='tab_cadre_fixe'>";
      echo "<tr><th colspan='3'>" . $LANG['entity'][6] . "</th></tr>";
      //Get all rules and actions
      $rules = $this->getRulesForEntity( $ID, 0, 1);

      if (!empty ($rules)) {
         initNavigateListItems(RULE_TYPE,$LANG['entity'][0]."=".getDropdownName("glpi_entities",$ID),
                               $this->sub_type);

         foreach ($rules as $rule) {
            addToNavigateListItems(RULE_TYPE,$rule->fields["id"],$this->sub_type);
            echo "<tr class='tab_bg_1'>";
            if ($canedit) {
               echo "<td width='10'>";
               $sel = "";
               if (isset ($_GET["select"]) && $_GET["select"] == "all") {
                  $sel = "checked";
               }
               echo "<input type='checkbox' name='item[" . $rule->fields["id"] . "]' value='1' $sel>";
               echo "</td>";
            }
            if ($canedit) {
               echo "<td><a href=\"" . $CFG_GLPI["root_doc"] . "/front/rule.right.form.php?id=" .
                          $rule->fields["id"] . "&amp;onglet=1\">" . $rule->fields["name"] . "</a>";
               echo "</td>";
            } else {
               echo "<td>" . $rule->fields["name"] . "</td>";
            }
            echo "<td>" . $rule->fields["description"] . "</td>";
            echo "</tr>";
         }
      }
      echo "</table></div>";

      if ($canedit) {
         echo "<table class='tab_glpi' width='80%'>";
         echo "<tr><td><img src=\"" . $CFG_GLPI["root_doc"] . "/pics/arrow-left.png\" alt=''></td>";
         echo "<td class='center'>";
         echo "<a onclick= \"if ( markCheckboxes('ldapaffectation_form') ) return false;\" href='" .
                $_SERVER['PHP_SELF'] . "?id=$ID&amp;select=all'>" . $LANG['buttons'][18] . "</a>";
         echo "</td><td>/</td><td class='center'>";
         echo "<a onclick= \"if ( unMarkCheckboxes('ldapaffectation_form') ) return false;\" href='" .
                $_SERVER['PHP_SELF'] . "?id=$ID&amp;select=none'>" . $LANG['buttons'][19] . "</a>";
         echo "</td><td class='left' width='80%'>";
         echo "<input type='submit' name='delete_user_rule' value=\"" . $LANG['buttons'][6] .
                "\" class='submit'>";
         echo "</td></tr></table>";
      }
      echo "</form>";
   }

   /**
    * Get all ldap rules criterias from the DB and add them into the RULES_CRITERIAS
    */
   function addLdapCriteriasToArray() {
      global $DB,$RULES_CRITERIAS;

      $sql = "SELECT `name`, `value`
              FROM `glpi_rulesldapparameters`";
      $result = $DB->query($sql);
      while ($datas = $DB->fetch_array($result)) {
         $RULES_CRITERIAS[$this->sub_type][$datas["value"]]['name']=$datas["name"];
         $RULES_CRITERIAS[$this->sub_type][$datas["value"]]['field']=$datas["value"];
         $RULES_CRITERIAS[$this->sub_type][$datas["value"]]['linkfield']='';
         $RULES_CRITERIAS[$this->sub_type][$datas["value"]]['table']='';
      }
   }

   /**
    * Filter actions if needed
   *  @param $actions the actions array
   *  @param $new_action indicates if the function is called when adding a new action
   *  or when displaying an already added action
    * @return the filtered actions array
    */
   function filterActions($actions) {

      $RuleAction = new RuleAction;
      $this->actions = $RuleAction->getRuleActions($this->fields["id"]);
      foreach($this->actions as $action) {
         switch ($action->fields["field"]) {
            case "_affect_entity_by_dn" :
               unset($actions["_affect_entity_by_tag"]);
               unset($actions["entities_id"]);
               break;

            case "_affect_entity_by_tag" :
               unset($actions["_affect_entity_by_dn"]);
               unset($actions["entities_id"]);
               break;

            case "entities_id" :
               unset($actions["_affect_entity_by_tag"]);
               unset($actions["_affect_entity_by_dn"]);
               break;
         }
      }
      return $actions;
   }

   /**
   * Execute the actions as defined in the rule
   * @param $output the result of the actions
   * @param $params the parameters
   * @return the fields modified
   */
   function executeActions($output,$params,$regex_results) {
      global $CFG_GLPI;

      $entity='';
      $right='';
      $is_recursive = 0;
      $continue = true;
      $output_src = $output;

      if (count($this->actions)) {
         foreach ($this->actions as $action) {
            switch ($action->fields["action_type"]) {
               case "assign" :
                  switch ($action->fields["field"]) {
                     case "entities_id" :
                        $entity = $action->fields["value"];
                        break;

                     case "profiles_id" :
                        $right = $action->fields["value"];
                        break;

                     case "is_recursive" :
                        $is_recursive = $action->fields["value"];
                        break;

                     case "is_active" :
                        $output["is_active"] = $action->fields["value"];
                        break;

                     case "_ignore_user_import" :
                        $continue = false;
                        $output_src["_stop_import"] = true;
                        break;
                  } // switch (field)
                  break;

               case "regex_result" :
                  switch ($action->fields["field"]) {
                     case "_affect_entity_by_dn" :
                     case "_affect_entity_by_tag" :
                        $match_entity = false;
                        $entity = array();
                        foreach ($regex_results as $regex_result) {
                           $res = getRegexResultById($action->fields["value"],array($regex_result));
                           if ($res != null) {
                              if ($action->fields["field"] == "_affect_entity_by_dn") {
                                 $entity_found = getEntityIDByDN($res);
                              } else {
                                $entity_found = getEntityIDByTag($res);
                              }
                              //If an entity was found
                              if ($entity > -1) {
                                 array_push($entity, array($entity_found,
                                                           $is_recursive));
                                 $match_entity=true;
                              }
                           }
                        }
                        if (!$match_entity) {
                           //Not entity assigned : action processing must be stopped for this rule
                           $continue = false;
                        }
                        break;
                  } // switch (field)
                  break;
            } // switch (action_type)
         } // foreach (action)
      } // count (actions)

      if ($continue) {
         //Nothing to be returned by the function :
         //Store in session the entity and/or right
         if ($entity != '' && $right != '') {
            $output["_ldap_rules"]["rules_entities_rights"][] = array($entity,
                                                                      $right,
                                                                      $is_recursive);
         } else if ($entity != '') {
            if (!is_array($entity)) {
              $entities_array=array($entity,$is_recursive);
              $output["_ldap_rules"]["rules_entities"][]=array($entities_array);
            //If it comes from a regex with multiple results
            } else {
               $output["_ldap_rules"]["rules_entities"][] = $entity;
            }
         } else if ($right != '') {
            $output["_ldap_rules"]["rules_rights"][]=$right;
         }

         return $output;
      } else {
         return $output_src;
      }
   }

   /**
    * Return all rules from database
    * @param $ID of entity
    * @param $withcriterias import rules criterias too
    * @param $withactions import rules actions too
    */
   function getRulesForEntity($ID, $withcriterias, $withactions) {
      global $DB;

      $ldap_affect_user_rules = array ();

      //Get all the rules whose sub_type is $sub_type and entity is $ID
      $sql = "SELECT `glpi_rules`.`id`
              FROM `glpi_rulesactions`, `glpi_rules`
              WHERE `glpi_rulesactions`.`rules_id` = `glpi_rules`.`id`
                    AND `glpi_rulesactions`.`field` = 'entities_id'
                    AND `glpi_rules`.`sub_type` = '".$this->sub_type."'
                    AND `glpi_rulesactions`.`value` = '$ID'";
      $result = $DB->query($sql);
      while ($rule = $DB->fetch_array($result)) {
         $affect_rule = new Rule;
         $affect_rule->getRuleWithCriteriasAndActions($rule["id"], 0, 1);
         $ldap_affect_user_rules[] = $affect_rule;
      }
      return $ldap_affect_user_rules;
   }

   function getTitleRule($target) {
   }

   function getTitle() {
      global $LANG;

      return $LANG['entity'][6];
   }

}


/// Rule collection class for Rights management
class RightRuleCollection extends RuleCollection {

   /// Array containing results : entity + right
   var $rules_entity_rights = array();
   /// Array containing results : only entity
   var $rules_entity = array();
   /// Array containing results : only right
   var $rules_rights = array();

   /**
    * Constructor
   **/
   function __construct() {
      global $DB;

      $this->sub_type = RULE_AFFECT_RIGHTS;
      $this->rule_class_name = 'RightAffectRule';
      $this->stop_on_first_match=false;
      $this->right="rule_ldap";
      $this->orderby="name";
      $this->menu_option="right";
   }

   function getTitle() {
      global $LANG;

      return $LANG['rulesengine'][19];
   }

   function cleanTestOutputCriterias($output) {

      if (isset($output["_rule_process"])) {
         unset($output["_rule_process"]);
      }
      return $output;
   }

   function showTestResults($rule,$output,$global_result) {
      global $LANG,$RULES_ACTIONS;

      echo "<tr><th colspan='4'>" . $LANG['rulesengine'][81] . "</th></tr>";
      echo "<tr class='tab_bg_2'>";
      echo "<td class='center' colspan='4'>".$LANG['rulesengine'][41]." : <strong> ".
             getYesNo($global_result)."</strong></td>";

      if (isset($output["_ldap_rules"]["rules_entities"])) {
         echo "<tr class='tab_bg_2'>";
         echo "<td class='center' colspan='4'>".$LANG['rulesengine'][111]."</td>";
         foreach ($output["_ldap_rules"]["rules_entities"] as $entities) {
            foreach ($entities as $entity) {
               $this->displayActionByName("entity",$entity[0]);
               if (isset($entity[1])) {
                  $this->displayActionByName("recursive",$entity[1]);
               }
            }
         }
      }

      if (isset($output["_ldap_rules"]["rules_rights"])) {
         echo "<tr class='tab_bg_2'>";
         echo "<td colspan='4' class='center'>".$LANG['rulesengine'][110]."</td>";
         foreach ($output["_ldap_rules"]["rules_rights"] as $val) {
            $this->displayActionByName("profile",$val[0]);
         }
      }

      if (isset($output["_ldap_rules"]["rules_entities_rights"])) {
         echo "<tr  class='tab_bg_2'>";
         echo "<td colspan='4' class='center'>".$LANG['rulesengine'][112]."</td>";
         foreach ($output["_ldap_rules"]["rules_entities_rights"] as $val) {
            $this->displayActionByName("entity",$val[0]);
            if (isset($val[1])) {
               $this->displayActionByName("profile",$val[1]);
            }
            if (isset($val[2])) {
               $this->displayActionByName("is_recursive",$val[2]);
            }
         }
      }

      if (isset($output["_ldap_rules"])) {
         unset($output["_ldap_rules"]);
      }
      foreach ($output as $criteria => $value) {
         if (isset($RULES_ACTIONS[$this->sub_type][$criteria])) { // ignore _* fields
            echo "<tr class='tab_bg_2'>";
            echo "<td class='center'>".$RULES_ACTIONS[$this->sub_type][$criteria]["name"]."</td>";
            echo "<td class='center'>".$rule->getActionValue($criteria,$value)."</td></tr>\n";
         }
      }
      echo "</tr>";
   }

   /**
   * Display action using its name
   * @param $name action name
   * @param $value default value
   */
   function displayActionByName($name,$value) {
      global $LANG;

      echo "<tr class='tab_bg_2'>";
      switch ($name) {
         case "entity" :
            echo "<td class='center'>".$LANG['entity'][0]." </td>\n";
            echo "<td class='center'>".getDropdownName("glpi_entities",$value)."</td>";
            break;

         case "profile" :
            echo "<td class='center'>".$LANG['Menu'][35]." </td>\n";
            echo "<td class='center'>".getDropdownName("glpi_profiles",$value)."</td>";
            break;

         case "is_recursive" :
            echo "<td class='center'>".$LANG['profiles'][28]." </td>\n";
            echo "<td class='center'>".((!$value)?$LANG['choice'][0]:$LANG['choice'][1])."</td>";
            break;
      }
      echo "</tr>";
   }

   /**
    * Get all the fields needed to perform the rule
    */
   function getFieldsToLookFor() {
      global $DB;

      $params = array();
      $sql = "SELECT DISTINCT `value`
              FROM `glpi_rules`, `glpi_rulescriterias`, `glpi_rulesldapparameters`
              WHERE `glpi_rules`.`sub_type` = '".$this->sub_type."'
                    AND `glpi_rulescriterias`.`rules_id` = `glpi_rules`.`id`
                    AND `glpi_rulescriterias`.`criteria` = `glpi_rulesldapparameters`.`value`";
      $result = $DB->query($sql);

      while ($param = $DB->fetch_array($result)) {
         //Dn is alwsays retreived from ldap : don't need to ask for it !
         if ($param["value"] != "dn") {
            $params[]=utf8_strtolower($param["value"]);
         }
      }
      return $params;
   }

   /**
    * Get the attributes needed for processing the rules
    * @param $input input datas
    * @param $params extra parameters given
    * @return an array of attributes
    */
   function prepareInputDataForProcess($input,$params) {
      global $RULES_CRITERIAS;

      $rule_parameters = array();
      //LDAP type method
      if ($params["type"] == "LDAP") {
         //Get all the field to retrieve to be able to process rule matching
         $rule_fields = $this->getFieldsToLookFor();

         //Get all the datas we need from ldap to process the rules
         $sz = @ ldap_read($params["connection"], $params["userdn"], "objectClass=*", $rule_fields);
         $rule_input = ldap_get_entries($params["connection"], $sz);

         if (count($rule_input)) {
            if (isset($input)) {
               $groups = $input;
            } else {
               $groups = array();
            }
            $rule_input = $rule_input[0];
            //Get all the ldap fields
            $fields = $this->getFieldsForQuery();
            foreach ($fields as $field) {
               switch(utf8_strtoupper($field)) {
                  case "LDAP_SERVER" :
                     $rule_parameters["LDAP_SERVER"] = $params["ldap_server"];
                     break;

                  case "GROUPS" :
                     foreach ($groups as $group) {
                        $rule_parameters["GROUPS"][] = $group;
                     }
                     break;

                  default :
                     if (isset($rule_input[$field])) {
                        if (!is_array($rule_input[$field])) {
                           $rule_parameters[$field] = $rule_input[$field];
                        } else {
                           for ($i=0;$i < count($rule_input[$field]) -1;$i++) {
                              $rule_parameters[$field][] = $rule_input[$field][$i];
                           }
                        }
                     }
               }
            }
            return $rule_parameters;
         }
         return $rule_input;
      }
      //IMAP/POP login method
      $rule_parameters["MAIL_SERVER"] = $params["mail_server"];
      $rule_parameters["MAIL_EMAIL"] = $params["email"];
      return $rule_parameters;
   }

   /**
    * Get the list of fields to be retreived to process rules
    */
   function getFieldsForQuery() {
      global $RULES_CRITERIAS;

      $fields = array();
      foreach ($RULES_CRITERIAS[$this->sub_type] as $criteria) {
         if (isset($criteria['virtual']) && $criteria['virtual']) {
            $fields[]=$criteria['id'];
         } else {
            $fields[]=$criteria['field'];
         }
      }
      return $fields;
   }

   function title() {
      global $LANG,$CFG_GLPI;

      displayTitle('','','',array($CFG_GLPI["root_doc"].
                        "/front/ldap.parameters.php"=>$LANG['Menu'][26]." ".$LANG['ruleldap'][1]));
      echo "<br>";
   }

}
?>
