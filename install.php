<?php
/*

  ----------------------------------------------------------------------
 GLPI - Gestionnaire libre de parc informatique
 Copyright (C) 2002 by the INDEPNET Development Team.
 Bazile Lebeau, baaz@indepnet.net - Jean-Mathieu Dol�ans, jmd@indepnet.net
 http://indepnet.net/   http://glpi.indepnet.org
 ----------------------------------------------------------------------
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
 ----------------------------------------------------------------------
 Original Author of file:
 Purpose of file:
 ----------------------------------------------------------------------
 */

// Ce script g�n�re ses propres messages d'erreur 
// Pas besoin des warnings de PHP
error_reporting(0);

// en test !!
// d�but d'internationalisation
// il reste � faire choisir la langue c'est pas impl�ment� encore 
include("glpi/dicts/french.php");
// pour l'instant je bosse avec le french
//



//Print a correct  Html header for application
function header_html($etape)
{

        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">";
        echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"fr\" lang=\"fr\">";
        echo "<head>";
        echo " <meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\" />";
        echo "<meta http-equiv=\"Content-Script-Type\" content=\"text/javascript\" /> ";
        echo "<meta http-equiv=\"Content-Style-Type\" content=\"text/css\" /> ";
        echo "<meta http-equiv=\"Content-Language\" content=\"fr\" /> ";
        echo "<meta name=\"generator\" content=\"\" />";
        echo "<meta name=\"DC.Language\" content=\"fr\" scheme=\"RFC1766\" />";
        echo "<title>Setup GLPI</title>";
       
        echo "<style type=\"text/css\">";
        echo "<!--

        /*  ... Definition des styles ... */

        body {
        background-color:#C5DAC8;
        color:#000000; }
        
       .principal {
        background-color: #ffffff;
        font-family: Verdana;font-size:12px;
        text-align: justify ; 
        -moz-border-radius: 4px;
	border: 1px solid #FFC65D;
         margin: 40px; 
         padding: 40px 40px 10px 40px;
       }

       table {
       text-align:center;
       border: 0;
       margin: 20px;
       margin-left: auto;
       margin-right: auto;
       width: 90%;}

       .red { color:red;}
       .green {color:green;}
       
       h2 {
        color:#FFC65D;
        text-align:center;}

       h3 {
        text-align:center;}

        input {border: 1px solid #ccc;}

        fieldset {
        padding: 20px;
          border: 1px dotted #ccc;
        font-size: 12px;
        font-weight:200;}

        .submit { text-align:center;}
       
        input.submit {
        border:1px solid #000000;
        background-color:#eeeeee;
        }
        
        input.submit:hover {
        border:1px solid #cccccc;
       background-color:#ffffff;
        }

        -->  ";
        echo "</style>";
         echo "</head>";
        echo "<body>";
	echo "<div class=\"principal\">";
        echo "<h2>GLPI SETUP</h2>";
	echo "<br/><h3>". $etape ."</h3>";
}

//Display a great footer.
function footer_html()
{
		echo "</div></body></html>";
}


//confirm install form
function step0()
{

global $lang;

echo "<h3>".$lang["install"][0]."</h3>";
echo "<p>".$lang["install"][1]."</p>";
echo "<p> ".$lang["install"][2]."</p>";
echo "<form action=\"install.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"update\" value=\"no\" />";
echo "<p class=\"submit\"><input type=\"hidden\" name=\"install\" value=\"Etape_0\" />";
echo "<input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][3]."\" /></p>";
echo "</form>";
echo "<form action=\"install.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"update\" value=\"yes\" />";
echo "<p class=\"submit\"><input type=\"hidden\" name=\"install\" value=\"Etape_0\" />";
echo "<input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][4]."\" /></p>";
echo "</form>";
}

//Step 1 checking some compatibilty issue and some write tests.
function step1($update)
{
global $lang;

	$error = 0;
	echo "<h3>".$lang["install"][5]."</h3>";
	echo "<table>";
	echo "<tr><th>".$lang["install"][6]."</th><th >".$lang["install"][7]."</th></tr>";
// Parser test
	echo "<tr><td><h4>".$lang["install"][8]."</h4></td>";
// PHP Version  - exclude PHP3
	if (substr(phpversion(),0,1) == "3") {
		$error = 2;
		echo "<td>".$lang["install"][9]."</a>.\n</td>";
	}
	elseif (substr(phpversion(),0,3) == "4.0" and ereg("0|1",substr(phpversion(),4,1))) {
		echo "<td><span class='wrong'>&nbsp;<td>".$lang["install"][10]."<td>";
		if($error != 2) $error = 1;
	}
	else {
		echo "<td>".$lang["install"][11]."</td></tr>";
	}
// end parser test
//	echo "<h2>GLPI environment test</h2>";
//	echo "<table>";
//	echo "<tr><th>Test effectu�</th><th colspan='2'>R�sultats</th></tr>";
// session test
	echo "<tr><td><h4>".$lang["install"][12]."</h4></td>";



  // check whether session are enabled at all!!
	if (!extension_loaded('session')) {
		$error = 2;
		echo "<td><h2>".$lang["install"][13]."</h2></td></tr>";
	} 
	if ($_SESSION["Test_session_GLPI"] == 1) {
		echo "<td><i>".$lang["install"][14]."</i></td></tr>";
	}
	else {
		if($error != 2) $error = 1;
		echo "<td>".$lang["install"][15]."</td></tr>";
	}


// *********
// file test

// il faut un test dans /dump et un dans /tmp pour phpexcel et /glpi/config/

	echo "<tr><td><h4>".$lang["install"][16]."</h4></td>";
	
	$fp = fopen("backups/dump/test_glpi.txt",'w');
	if (empty($fp)) {
		echo "<td><p class='red'>".$lang["install"][17]."</p> ".$lang["install"][18]."</td></tr>";
		$error = 2;
	}
	else {
		$fw = fwrite($fp,"This file was created for testing reasons. ");
		fclose($fp);
		$delete = unlink("backups/dump/test_glpi.txt");
		if (!$delete) {
			echo "<td>".$lang["install"][19]."</td></tr>";
			if($error != 2) $error = 1;
		}
		else {
			echo "<td>".$lang["install"][20]."</td></tr>";

		}
	}
	echo "<tr><td><h4>".$lang["install"][21]."</h4></td>";
		$fp = fopen("reports/reports/convexcel/tmp/test_glpi.txt",'w');
	if (empty($fp)) {
		echo "<td><p class='red'>".$lang["install"][17]."</p>". $lang["install"][22]."</td></tr>";
		$error = 2;
	}
	else {
		$fw = fwrite($fp,"This file was created for testing reasons. ");
		fclose($fp);
		$delete = unlink("reports/reports/convexcel/tmp/test_glpi.txt");
		if (!$delete) {
			echo "<td>".$lang["install"][19]."</td></tr>";
			if($error != 2) $error = 1;
		}
		else {
			echo "<td>".$lang["install"][20]."</td></tr>";
		}
	}
	echo "<tr><td><h4>".$lang["install"][23]."</h4></td>";
	$fp = fopen("glpi/config/test_glpi.txt",'w');
	if (empty($fp)) {
		echo "<td><p class='red'>".$lang["install"][17]."</p>". $lang["install"][24]."</td></tr>";
		$error = 2;
	}
	else {
		$fw = fwrite($fp,"This file was created for testing reasons. ");
		fclose($fp);
		$delete = unlink("glpi/config/test_glpi.txt");
		if (!$delete) {
			echo "<td>".$lang["install"][19]."</td></tr>";
			if($error != 2) $error = 1;
		}
		else {
			echo "<td>".$lang["install"][20]."</td></tr>";
		}
	}
	echo "</table>";
        switch ($error) {
		case 0 :       
        	echo "<h3>".$lang["install"][25]."</h3>";
		echo "<form action=\"install.php\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"update\" value=\"". $update."\" />";
		echo "<p class=\"submit\"><input type=\"hidden\" name=\"install\" value=\"Etape_1\" />";
		echo "<input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][26]."\" /></p>";
		echo "</form>";
		break;
		case 1 :       
        	echo "<h3>".$lang["install"][25]."</h3>";
		echo "<form action=\"install.php\" method=\"post\">";
		echo "<p class=\"submit\"><input type=\"hidden\" name=\"install\" value=\"Etape_1\" />";
		echo "<input type=\"hidden\" name=\"update\" value=\"". $update."\" />";
		echo "<input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][26]."\" /></p>";
		echo "</form> &nbsp;&nbsp;";
		echo "<form action=\"install.php\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"update\" value=\"". $update."\" />";
		echo "<p class=\"submit\"><input type=\"hidden\" name=\"install\" value=\"Etape_0\" />";
		echo "<input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][27]."\" /></p>";
		echo "</form>";
		break;
		case 2 :       
        	echo "<h3>".$lang["install"][25]."</h3>";
		echo "<form action=\"install.php\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"update\" value=\"".$update."\" />";
		echo "<p class=\"submit\"><input type=\"hidden\" name=\"install\" value=\"Etape_0\" />";
		echo "<input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][27]."\" /></p>";
		echo "</form>";
		break;
	}
	

}

//step 2 import mysql settings.
function step2($update)
{
		global $lang;
		echo "<p>".$lang["install"][28]."</p>";
		echo "<form action=\"install.php\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"update\" value=\"".$update."\" />";
		echo "<fieldset><legend>".$lang["install"][29]."</legend>";
                echo "<p><label>".$lang["install"][30] .": <input type=\"text\" name=\"db_host\" /></label></p>";
		echo "<p ><label>".$lang["install"][31] .": <input type=\"text\" name=\"db_user\" /></label></p>";
		echo "<p ><label>".$lang["install"][32]." : <input type=\"password\" name=\"db_pass\" /></label></p></fieldset>";
		echo "<input type=\"hidden\" name=\"install\" value=\"Etape_2\" />";
		echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][26]."\" /></p>";
	
}

//step 3 test mysql settings and select database.
function step3($host,$user,$password,$update)
{

	global $lang;
	
	error_reporting(16);
	echo "<h3>".$lang["install"][34]."</h3>";
	$link = mysql_connect($host,$user,$password);
	if (!$link || empty($host) || empty($user)) {
		echo "".$lang["install"][35]." : \n
		<br />".$lang["install"][36]." : ".mysql_error();
		if(empty($host) || empty($user)) {
			echo $lang["install"][37];
		}
		echo "<form action=\"install.php\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"update\" value=\"".$update."\" />";
		echo "<input type=\"hidden\" name=\"install\" value=\"Etape_1\" />";
		echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" class=\"submit\"  value=\"".$lang["install"][33]."\" /></p>";
		echo "</form>";
                //     BUG
                // IL y a un pb ici la validation du bouton devrait nous faire revenir sur l'�tape 2 hors on reste bloqu�... pas trouv� pourquoi.
                // END BUG
	}
	else {
		echo "Connection r�ussie !! <br />";
		if($update == "no") {
			echo " Veuillez selectionner une base de donn�es : ";
			echo "<form action=\"install.php\" method=\"post\">";
			$db_list = mysql_list_dbs($link);
			while ($row = mysql_fetch_object($db_list)) {
				echo "<p><input type=\"radio\" name=\"databasename\" value=\"". $row->Database ."\" />$row->Database.</p>";
			}
			echo "<p><input type=\"radio\" name=\"databasename\" value=\"0\" />Cr�er une nouvelle base : ";
			echo "<input type=\"text\" name=\"newdatabasename\"/></p>";
			echo "<input type=\"hidden\" name=\"db_host\" value=\"". $host ."\" />";
			echo "<input type=\"hidden\" name=\"db_user\" value=\"". $user ."\" />";
			echo "<input type=\"hidden\" name=\"db_pass\" value=\"". $password ."\" />";
			echo "<input type=\"hidden\" name=\"install\" value=\"Etape_3\" />";
			echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][26]."\" /></p>";
			mysql_close($link);
			echo "</form>";
		}
		elseif($update == "yes") {
			echo " Veuillez selectionner la base de donn�es  � mettre � jour : ";
			echo "<form action=\"install.php\" method=\"post\">";
			$db_list = mysql_list_dbs($link);
			while ($row = mysql_fetch_object($db_list)) {
				echo "<p><input type=\"radio\" name=\"databasename\" value=\"". $row->Database ."\" />$row->Database.</p>";
			}
			echo "<input type=\"hidden\" name=\"db_host\" value=\"". $host ."\" />";
			echo "<input type=\"hidden\" name=\"db_user\" value=\"". $user ."\" />";
			echo "<input type=\"hidden\" name=\"db_pass\" value=\"". $password ."\" />";
			echo "<input type=\"hidden\" name=\"install\" value=\"update_1\" />";
			echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][26]."\" /></p>";
			mysql_close($link);
			echo "</form>";
			
		}
        }
}



//Step 4 Create and fill database.
function step4 ($host,$user,$password,$databasename,$newdatabasename)
{
	global $lang;
	//display the form to return to the previous step.
	
	function prev_form() {
		echo "<br /><form action=\"install.php\" method=\"post\">";
		echo "Mysql server : <input type=\"hidden\" name=\"db_host\" value=\"". $host ."\"/><br />";
		echo "Mysql user : <input type=\"hidden\" name=\"db_user\" value=\"". $user ."\"/>";
		echo "Mysql pass : <input type=\"hidden\" name=\"db_pass\" value=\"". $password ."\" />";
		echo "<input type=\"hidden\" name=\"install\" value=\"Etape_2\" />";
		echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][33]."\" /></p>";
		echo "</form>";
	}
	//Display the form to go to the next page
	function next_form()
	{
		global $lang;
		
		echo "<br /><form action=\"install.php\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"install\" value=\"Etape_4\" />";
		echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][26]."\" /></p>";
		echo "</form>";
	}
	
	//Fill the database
	function fill_db()
	{
		global $lang;
		
		include ("_relpos.php");
		include ($phproot . "/glpi/includes.php");
		$db = new DB;
		$db_file = $phproot ."/mysql/glpi-0.4-default.sql";
		$dbf_handle = fopen($db_file, "rt");
		$sql_query = fread($dbf_handle, filesize($db_file));
		fclose($dbf_handle);
		foreach ( explode(";\n", "$sql_query") as $sql_line) {
			$db->query($sql_line);
		}
	
	}
	$link = mysql_connect($host,$user,$password);
	if(!empty($databasename)) {
		$db_selected = mysql_select_db($databasename, $link);
		if (!$db_selected) {
			echo "Impossible d\'utiliser la base : ";
			echo "<br />Le serveur � r�pondu " . mysql_error();
			prev_form();
		}
		else {
			if (create_conn_file($host,$user,$password,$databasename)) {
				fill_db();
				echo "<p>OK - La base a bien �t� initialis�e</p>";
				echo "<p>Des valeurs par d�faut ont �t� entr�es, n'h�sitez pas � supprimer ces derni�res</p>";
				echo "<p>Ne supprimez pas l'utilisateur \"helpdesk\"</p>";
				echo "<p>A la premi�re connection vous pouvez utiliser le login \"glpi\" et le mot de passe \"glpi\" pour acc�der � l'application avec des droits administrateur</p>";
				next_form();
			}
			else {
				echo "<p>Impossible d'�crire le fichier de configuration de votre base de donn�es</p>";
				prev_form();
			}
		}
		mysql_close($link);
	}
	elseif(!empty($newdatabasename)) {
		// BUG cette fonction est obsol�te je l'ai remplac� par la nouvelle
                //if (mysql_create_db($newdatabasename)) {
		// END BUG
		if (mysql_query("CREATE DATABASE ".$newdatabasename)){

			echo "<p>Base de donn�es cr��e </p>";
			mysql_select_db($newdatabasename, $link);
			if (create_conn_file($host,$user,$password,$newdatabasename)) {
				fill_db();
				echo "<p>OK - La base a bien �t� initialis�e</p>";
				echo "<p>Des valeurs par defaut on �t� entr�es, n'h�sitez pas � supprimer ces derni�res</p>";
				echo "<p>Ne supprimez pas l'utilisateur \"helpdesk\"</p>";
				echo "<p>A la premi�re connection vous pouvez utiliser le login \"glpi\" et le mot de passe \"glpi\" pour acc�der � l'application avec des droits administrateur</p>";
				next_form();
			}
			else {
				echo "<p>Impossible d'�crire le fichiers de configuration de votre base de donn�es</p>";
				prev_form();
			}
		}
		else {
			echo "Erreur lors de la cr�ation de la base !";
			echo "<br />Le serveur a r�pondu : " . mysql_error();
			prev_form();
		}
		mysql_close($link);
	}
	else {
		echo "<p>Vous n'avez pas s�l�ctionn� de base de donn�es !</p>";
		prev_form();
		mysql_close($link);
	}
	
}

// Step 5 Start the glpi configuration
//
function step5()
{
	global $lang;
	
	include ("_relpos.php");
	include ($phproot . "/glpi/includes.php");
	$db = new DB;
	$query = "select * from glpi_config where ID = 1";
	$result = $db->query($query);
	echo "Configuration de GLPI : ";
	echo "<p>Les valeurs pr�s�lectionn�es sont les valeurs par defaut, il est recommand� de laisser ces valeurs</p>";
	echo "<form action=\"install.php\" method=\"post\">";
	$root_doc = ereg_replace("/install.php","",$_SERVER['REQUEST_URI']);
	echo "<p><label>".$lang["setup"][101]." : <input type=\"text\" name=\"root_doc\" value=\"". $root_doc ."\"></label></p>";
	echo "<p><label>".$lang["setup"][102]." <select name=\"event_loglevel\"><label></p>";
	$level=$db->result($result,0,"event_loglevel");
	echo "<option value=\"1\"";  if($level==1){ echo "selected";} echo ">".$lang["setup"][103]." </option>";
	echo "<option value=\"2\"";  if($level==2){ echo "selected";} echo ">".$lang["setup"][104]."</option>";
	echo "<option value=\"3\"";  if($level==3){ echo "selected";} echo ">".$lang["setup"][105]."</option>";
	echo "<option value=\"4\"";  if($level==4){ echo "selected";} echo ">".$lang["setup"][106]." </option>";
	echo "<option value=\"5\">".$lang["setup"][107]."</option>";
	echo "</select>";
	echo "<p><label>".$lang["setup"][108]." : <input type=\"text\" name=\"num_of_events\" value=\"". $db->result($result,0,"num_of_events") ."\"><label></p>";
	echo "<p><label>".$lang["setup"][109]." : <input type=\"text\" name=\"expire_events\" value=\"". $db->result($result,0,"expire_events") ."\"><label></p>";
	echo "<p> ".$lang["setup"][110]." :  <input type=\"radio\" name=\"jobs_at_login\" value=\"1\" checked /><label>".$lang["choice"][0]."</label>";
	echo " <input type=\"radio\" name=\"jobs_at_login\" value=\"0\" /><label>".$lang["choice"][1] ."</label></p>";
	echo "<p><label>".$lang["setup"][111]."  : <input type=\"text\" name=\"list_limit\" value=\"". $db->result($result,0,"list_limit") ."\"><label></p>";
	echo "<p><label>".$lang["setup"][112]." : <input type=\"text\" name=\"cut\" value=\"". $db->result($result,0,"cut") ."\"><label></p>";
	echo "<input type=\"hidden\" name=\"install\" value=\"Etape_5\" />";
	echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][26]."\" /></p>";
	echo "</form>";
}

// STEP 6 Get the config and fill database
// Config the mailing features if enabled by user
function step6($root_doc, $event_loglevel, $num_of_events, $expire_events,$jobs_at_login, $list_limit, $cut, $mailing)
{
	global $lang;
	
	include ("_relpos.php");
	require_once ($phproot . "/glpi/includes.php");
	$db = new DB;
	$query = "update glpi_config set root_doc = '". $root_doc ."', event_loglevel = '". $event_loglevel ."', num_of_events = '". $num_of_events ."', jobs_at_login = '". $jobs_at_login ."', list_limit = '". $list_limit ."', cut = '". $cut ."'"; 
	$db->query($query);
	echo "Votre configuration a bien �t� enregistr�e";
	echo "<br />Cliquer sur 'Continuer' pour terminer l'installation";
	echo "<br /><form action=\"install.php\" method=\"post\">";
	echo "<input type=\"hidden\" name=\"install\" value=\"Etape_6\" />";
	echo "<p class=\"submit\"><input type=\"submit\" name=\"submit\" class=\"submit\" value=\"".$lang["install"][26]."\" /></p>";
	echo "</form>";
}



function step7() {

	global $lang;
	
	echo "<h2>L'installation s'est bien termin�e </h2>";
	echo "<p>Il est recommand� maintenant d'appliquer un chmod+0 sur les fichiers install.php et update.php</p>";
	echo "<p>Vous pouvez utiliser l'application en cliquant <a href=\"index.php\">sur ce lien </a>.</p>";
	echo "<p>Les logins mots de passes par defauts sont :</p>";
	echo "<p>&nbsp;<li> glpi/glpi pour le compte administrateur</li>";
	echo "&nbsp;<li>tech/tech pour le compte technicien</li>";
	echo "&nbsp;<li>normal pour le compte normal</li>";
	echo "&nbsp;<li>post-only/post-only pour le compte postonly</li></p>";
	echo "<p>Vous pouvez supprimer ces comptes ainsi que les premi�res entr�es dans la base de donn�es.</p>";
	echo "<p>Attention tout de m�me NE SUPPRIMEZ PAS l'utilisateur HELPDESK.</p>";
}

//Create the file glpi/config/config_db.php
// an fill it with user connections info.
function create_conn_file($host,$user,$password,$dbname)
{
	$db_str = "<?php \n class DB extends DBmysql { \n var \$dbhost	= \"". $host ."\"; \n var \$dbuser 	= \"". $user ."\"; \n var \$dbpassword= \"". $password ."\"; \n var \$dbdefault	= \"". $dbname ."\"; \n } \n ?>";
	include ("_relpos.php");
	$fp = fopen($phproot ."/glpi/config/config_db.php",'wt');
	if($fp) {
		$fw = fwrite($fp,$db_str);
		fclose($fp);
		return true;
	}
	else return false;
}

function update1($host,$user,$password,$dbname) {
	
	global $lang;	

	if(create_conn_file($host,$user,$password,$dbname)) {
		//echo "bla";
		include("update.php");
		
	}
	else {
		echo "Impossible de creer le fichier de connection � la base de donn�es, reverifiez vos droits sur les fichiers";
		echo "<h3>Continuer ?</h3>";
		echo "<form action=\"install.php\" method=\"post\">";
		echo "<input type=\"hidden\" name=\"update\" value=\"yes\" />";
		echo "<p class=\"submit\"><input type=\"hidden\" name=\"install\" value=\"Etape_0\" />";
		echo "<input type=\"submit\" name=\"submit\" class=\"submit\" value=\"Re-essayer\" /></p>";
		echo "</form>";
	}
	
	
}




//------------Start of install script---------------------------
include ("_relpos.php");
	if(!isset($_POST["install"])) {
		if(file_exists($phproot ."/glpi/config/config_db.php")) {
			include($phproot ."/index.php");
			die();
		}
		else {
			header_html("D�but de l'installation");
			step0();
		}
	}
	else {
		switch ($_POST["install"]) {
			case "Etape_0" :
				session_start();
				header_html("Etape 0");
				$_SESSION["Test_session_GLPI"] = 1;
				session_destroy();
				step1($_POST["update"]);
				break;
			case "Etape_1" :
				header_html("Etape 1");
				step2($_POST["update"]);
				break;
			case "Etape_2" :
				header_html("Etape 2");
				step3($_POST["db_host"],$_POST["db_user"],$_POST["db_pass"],$_POST["update"]);
				break;
			case "Etape_3" :
				header_html("Etape 3");
				if(empty($_POST["databasename"])) $_POST["databasename"] ="";
				if(empty($_POST["newdatabasename"])) $_POST["newdatabasename"] ="";
				step4($_POST["db_host"],$_POST["db_user"],$_POST["db_pass"],$_POST["databasename"],$_POST["newdatabasename"]);
				break;
			case "Etape_4" :
				header_html("Etape 4");
				step5();
				break;
			case "Etape_5" :
				header_html("Etape 5");
				step6($_POST["root_doc"], $_POST["event_loglevel"], $_POST["num_of_events"], $_POST["expire_events"], $_POST["jobs_at_login"],$_POST["list_limit"], $_POST["cut"]);
				break;
			case "Etape_6" :
				header_html("Etape 6");
				step7();
				break;
			case "update_1" : 
				update1($_POST["db_host"],$_POST["db_user"],$_POST["db_pass"],$_POST["databasename"]);
				break;
		}
	}
	footer_html();
//FIn du script
?>
