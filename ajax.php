<?php 
session_start();
date_default_timezone_set("Europe/London");


/* index.php 
Creation Date:12/07/2011
updated by : Ian Bettison
Purpose : To manage the Fetal Medicines database.*/

define( "LIBRARYPATH", "./library/" );
define( "LOCALIMAGEPATH", "/images/" );
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
include( "validation_rules.php" );

if($_POST["func"] == "setMessageId"){
	$ajax_message = dl::select("sms_message_appointment", "ma_id = ". $_POST["mId"]);
	if(!empty($ajax_message)) {
		$show = dl::select("sms_messages", "m_id = ". $ajax_message[0]["message_id"] );
		echo "<B>".$show[0]["m_short_title"]."</B><BR><BR>";
		echo $show[0]["m_message"];
	}
}

if($_POST["func"] == "delMessageId"){
	$delete = dl::select("sms_message_appointment", "ma_id = ". $_POST["dId"]);
	dl::delete("sms_appointments", "a_id = ".$delete[0]["appointment_id"]);
	dl::delete("sms_sent_messages", "message_appointment_id = ". $_POST["dId"]);
	dl::delete("sms_message_appointment", "ma_id = ".$_POST["dId"]);
	appointment_body();
}

if($_POST["func"] == "show_message") {
	$ajax_message = dl::select("sms_messages", "m_short_title = '".$_POST["messVal"]."'");
	if(!empty($ajax_message)){
		echo "<fieldset><legend>Message to be sent</legend>";
		echo $ajax_message[0]["m_message"];
		echo "</fieldset>";
	}
}

if( $_POST["func"] == "delete_patient" ){
	$mobile = substr($_POST["del"],0,strpos($_POST["del"]," "));
	//get patient Id
	$patient = dl::select("sms_patient_contact_details", "pcd_mobile_number = '".$mobile."'");
	if(!empty($patient)){
		$appointments = dl::select("sms_message_appointment", "patient_id = ". $patient[0]["pcd_id"]);
		foreach($appointments as $appts) {
			dl::delete("sms_appointments", "a_id = ".$appts["appointment_id"]);
		}
		dl::delete("sms_message_appointment", "patient_id = ". $patient[0]["pcd_id"]);
		dl::delete("sms_patient_contact_details", "pcd_id = ".$patient[0]["pcd_id"]);
		echo "Deleted";
	}else{
		echo "Not Found";
	}
	
}

if( $_POST["func"] == "delete_message" ) {
	$messages = dl::select( "sms_messages", "m_short_title = '".$_POST["del"]."'" );
	//now need to check if the message is queued to send. If not we will delete it
	$sql = "select * from sms_messages as m
		join sms_message_appointment as ma on (m.m_id=ma.message_id)
		left join
		sms_sent_messages as sm on (ma.ma_id=sm.message_appointment_id)
		where
		sm.sm_timestamp is NULL 
		and ma.message_id = ".$messages[0]["m_id"];
	$messQueued = dl::getQuery($sql);
	if(empty($messQueued)) {
		//we can safely delete the message
		dl::delete("sms_messages", "m_id = ". $messages[0]["m_id"]);
		echo "Deleted Message...";
	}else{
		echo "The message is queued for delivery, not deleted...";
	}
}


if( $_POST["func"] == "Add_Note") {	
	addnote::add_notes_to($_POST["table"]);
	call_user_func($_POST["resetFunc"]);
}

if( $_POST["func"] == "Manage_Notes") {	
	addnote::manage_notes($_POST["table"], $_POST["linkid"]);
	call_user_func($_POST["resetFunc"]);
}

?>
