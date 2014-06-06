<?php 
session_start();
?>
<SCRIPT src			="./library/js/jquery-1.8.3.min.js"></SCRIPT>
<script src			="./library/js/jquery-ui/js/jquery-ui-1.9.1.custom.min.js" type="text/javascript"></script>
<SCRIPT language	="JavaScript" src="./library/js/jquery.autoresize.js"></SCRIPT>
<SCRIPT language	="JavaScript" src="./library/js/jquery-validation/jquery.validate.js"></SCRIPT>
<SCRIPT language	="JavaScript" src="./library/js/jquery.flexipage.min.js"></SCRIPT>



<?php
date_default_timezone_set("Europe/London");


/* index.php 
Creation Date:22/10/2012
updated by : Ian Bettison
Purpose : To manage the creation of a patient SMS system*/

define( "LIBRARYPATH", "./library/" );
define( "LOCALIMAGEPATH", "./images/" );
define( "IMAGEPATH", "./library/images/" );
define( "CSSPATH", "./css/" );
define( "CLASSPATH", "./library/classes/" );
define( "JAVAPATH", "./library/js/");

require(LIBRARYPATH.'mysqli_datalayer.php');
require(CLASSPATH.'field.class.php');
require(CLASSPATH.'permission.class.php');
include('connection.php');
require(CLASSPATH.'login.class.php');
require(CLASSPATH.'form.class.php');
require(CLASSPATH.'tab.class.php');
require(CLASSPATH.'validate.class.php');
require(CLASSPATH.'admin.class.php');
require(CLASSPATH.'search.class.php');
require(CLASSPATH.'questionnaire.class.php');
require(CLASSPATH.'addnote.class.php');
require(CLASSPATH.'list.deletion.class.php');
// required if email function is available.
require(CLASSPATH.'phpmailer/class.phpmailer.php');
include("functions.php");

if($_GET["func"] == "logoff") {
	session_destroy();
	session_start();
	$_SESSION["loggedIn"] = "";
}

echo "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/xhtml1-strict.dtd'>";
echo "<html xmlns='http://www.w3.org/1999/xhtml'>";
echo "<head>";
echo "<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />"; 
echo "<title>Send SMS Messages to Patients</title> ";
echo "<link rel='StyleSheet' href='./library/js/jquery-ui/css/custom-theme/jquery-ui-1.9.1.custom.css' type='text/css' media='screen' />";
echo "<LINK REL='stylesheet/less' HREF='".CSSPATH."sms.less' TYPE='text/css' media='screen'>"; 
echo "<LINK REL='stylesheet/less' HREF='".JAVAPATH."jquery.ptTimeSelect.css' TYPE='text/css' media='screen'>";
echo "<SCRIPT src='./library/js/jquery.ptTimeSelect.js'></SCRIPT>";
//echo "<LINK REL='stylesheet/less' HREF='".CSSPATH."potn-prt.less' TYPE='text/css' media='print'>"; 
echo "<link REL='SHORTCUT ICON' HREF='".LOCALIMAGEPATH."favicon.ico'>"; 
echo "<SCRIPT src='".JAVAPATH."less/less-1.1.5.min.js'></SCRIPT>";
echo "</head>";
echo "<body>";
echo "<div id='container'>";
echo "<div class='header'>";
echo "<span class='logoffbtn'><a  class='logoff-link' href='index.php?func=logoff'> Logoff</a></span> ";
echo "</div>";
$notConfirmed = false;
if(!isset($_SESSION["loggedIn"])) {
	$_SESSION["loggedIn"] = "";
}

if($_GET["func"] == "login") { // attempt to login
	$email= strtolower(addslashes($_POST["email_address"]));
	$id = dl::select("sms_user", "user_email_address='".$email."'");
	$password=$_POST["password"];
	$login = new login( "user_id", $id[0]["user_id"], "sms_user", "sms_security", "security_password", $password, $id[0]["confirmed"]);
	if( $login->check_password() ) {
		//the userid and password are ok need to get the permissions
		$permissions = new permission( "sms_user_types", "user_types_name_id", $id[0]["user_id"], "user_id", "permission_id" );
		$settings = $permissions->get_permissions( "sms_permission_user", "sms_permissions", "permission_name", "permission_value" );
		$_SESSION["settings"] = $settings;
		if(!$login->check_confirmation()) {
			$notConfirmed = true;
		}else{
			$_SESSION["loggedIn"] = "true";
			$_SESSION["userId"] = $id[0]["user_id"];
		}
	}
}

if($_GET["func"] == "confirm") { // security confirmation
	$email	  = strtolower(addslashes($_POST["email_address"]));
	$id 	  = dl::select("sms_user", "user_email_address='".$email."'");
	$password =$_POST["password"];
	$login 	  = new login( "user_id", $id[0]["user_id"], "sms_user", "sms_security", "security_password", $password, $id[0]["confirmation"]);
	if( $login->check_password() ) {
		// the password and email address have been confirmed
		// now need to update the password and the confirmation flag
		$encrypt = $login->get_salt();
		dl::update("sms_user", array("confirmed"=>1), "user_email_address='".$email."'");
		dl::update("sms_security", array("security_password"=>MD5($encrypt.$_POST["newPassword"])), "user_id=".$id[0]["user_id"]);
		//the userid and password are ok need to get the permissions
		$permissions 		  = new permission( "sms_user_types", "user_types_name_id", $id[0]["user_id"], "user_id", "permission_id" );
		$settings 			  = $permissions->get_permissions( "sms_permission_user", "sms_permissions", "permission_name", "permission_value" );
		$_SESSION["settings"] = $settings;
		$_SESSION["loggedIn"] = "true";
		$message 			  = new message("ian.bettison@ncl.ac.uk");
		$subject 			  = "Patient SMS Reminder System - Account Confirmation";
		$body    			  = "<div style='font-family: arial; font-size: small;'>Dear ".$id[0]["user_name"]."<P>
		Your account has now been activated. You have confirmed your username and have changed your password, your password is not contained within this email for your security.<P>
		<P>If you have any problems please contact me on x4652 or DECT Phone 21552.<P>Thank you.
		<P>Ian Bettison.</div>";
		$message->set_message($subject, $body);
		$message->set_To( array(array(email=>$email, name=>$id[0]["user_name"]) ));
		$message->send();
	}
}

if( $_SESSION["loggedIn"] =="" ) {	
	echo "<div class='main'><center>";
		echo "<div class='sidebox'>";
			echo "<div class='sidebar'>";
				echo "<h3>User Login</h3>";
			
				if($notConfirmed) {
					login_form($login->check_confirmation());
				}else{
					login_form();
				}
				echo "</div>";
		echo "</div>";
		echo "</center>";

echo "</div>"; //container
}

if( $_SESSION["loggedIn"] == "true") {
	if( $_GET["func"] 	   == "add_patient" ) {
		echo "<div class='main'>";
		add_patient_record();
		disp_tabs(0);
		echo "</div>";
	}elseif( $_GET["func"] == "add_appointment"){
		add_appointment();
		echo "<div class='main'>";
		disp_tabs(1);
		echo "</div>";
	}elseif( $_GET["func"] == "select_patient"){
		select_patient();
		echo "<div class='main'>";
		disp_tabs(1);
		echo "</div>";
	}elseif( $_GET["func"] == "edit_phone"){
		editphone($_POST["pNum"]);
		echo "<div class='main'>";
		disp_tabs(1);
		echo "</div>";
	}elseif( $_GET["func"] == "manage_lists"){
		editlists();
		echo "<div class='main'>";
		disp_tabs(3);
		echo "</div>";
	}elseif( $_GET["func"] == "chooseList"){
		echo "<div class='main'>";
		disp_tabs(3);
		echo "</div>";
	}elseif( $_GET["func"] == "addPermission"){
		$users = new user();
		$users->write_permission();
		echo "<div class='main'>";
		disp_tabs(2);
		echo "</div>";
	}elseif( $_GET["func"] == "addType"){
		$users = new user();
		$users->write_usertype();
		echo "<div class='main'>";
		disp_tabs(2);
		echo "</div>";
	}elseif( $_GET["func"] == "addUser"){
		$users = new user();
		$body = "<div style='font-family: arial; font-size: small;'>Dear ".$_POST["user_name"]."<P>
						Your account has been created within the CRP Patient SMS Reminder System. You may <a href='http://crcsupport.ncl.ac.uk/SMS'>login</a> to the system using your <b>email address</b> as the user id and the password:<P>
						<B>".$_POST["password"]."</B><P>After you login for the first time you will be asked to change your password, please create a password you will remember.</P><P>If you have any problems please contact Ian Bettison on x4652.<P>Thank you.
						<P>Ian Bettison.</div>";
		$users->setMessageContent("New user registration", $body);
		$users->write_user();
		echo "<div class='main'>";
		disp_tabs(2);
		echo "</div>";
	}elseif( $_GET["func"] == "updatePermissions"){
		$users = new user();
		if($_POST["Cancel"]!="Cancel") {
			$users->update_permissions();
		}
		echo "<div class='main'>";
		disp_tabs(2);
		echo "</div>";
	}elseif( $_GET["func"] == "updateTypes"){
		$users = new user();
		if($_POST["Cancel"]!="Cancel") {
			$users->update_types();
		}
		echo "<div class='main'>";
		disp_tabs(2);
		echo "</div>";
	}elseif( $_GET["func"] == "updateUsers"){
		$users = new user();
		if($_POST["Cancel"]!="Cancel") {
			$users->update_users();
		}
		echo "<div class='main'>";
		disp_tabs(2);
		echo "</div>";
	
	}else{
		if($_GET["func"]   == "new") {
			unset($_SESSION["Patient_No"] );
		}
		echo "<div class='main'>";
			$tabnames = array();
			if(check_permissions("Add Patients") ) {
				array_push($tabnames, array("link"=>"tab1", "tabname"=>"Patient Details", "func"=>"add_patient"));
			}
			if(check_permissions("Add Appointments") ) {
				array_push($tabnames, array("link"=>"tab2", "tabname"=>"Appointments", "func"=>"show_appointments"));
			}
			if(check_permissions("Add Users")) {
				array_push($tabnames, array("link"=>"tab3", "tabname"=>"User Management", "func"=>"user_management") );
			}
			if(check_permissions("Manage Lists")) {
				array_push($tabnames, array("link"=>"tab4", "tabname"=>"Lists", "func"=>"managelists") );
			}
			create_tabs( $tabnames);
		echo "</div>";
	}
			
	echo "</div>";
}
echo "</div>"; // container end div
echo "</body>";
echo "</html>";
//lets remove the connection to the database here as it is connecting everytime anyway
dl::closedb();
?>