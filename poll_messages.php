<?php
date_default_timezone_set("Europe/London");


/* poll_messages.php 
Creation Date:29/10/2012
updated by : Ian Bettison
Purpose : To POLL the SMS system to check if there are messages to send to patients*/

define( "LIBRARYPATH", "./library/" );
define( "LOCALIMAGEPATH", "./images/" );
define( "IMAGEPATH", "./library/images/" );
define( "CSSPATH", "./css/" );
define( "CLASSPATH", "./library/classes/" );
define( "JAVAPATH", "./library/js/");

require(LIBRARYPATH.'mysqli_datalayer.php');
include('connection.php');

// required if email function is available.
require(CLASSPATH.'phpmailer/class.phpmailer.php');
include("functions.php");
//use join to get the list of appointmnts that havent been sent yet
$sql = "SELECT * FROM `sms_message_appointment` as ma 
		JOIN
		`sms_appointments` as a on (a.a_id=ma.appointment_id)
		LEFT JOIN
		`sms_sent_messages` as sm on (sm.message_appointment_id=ma.ma_id)
		WHERE sm.sm_timestamp is NULL";
$appointments = dl::getQuery($sql);
$message = new message("ian.bettison@ncl.ac.uk");
//send notification of text messaging poll to me
$message->set_message("cron job ran", date("d-m-Y"));
$message->set_To( array(array("email"=>"ian.bettison@ncl.ac.uk", "name"=>"Ian Bettison")));
$message->send();
unset($message);
if( !empty($appointments) ) {
	dl::$debug=true;
	$today = date("Y-m-d");
	
	foreach( $appointments as $app ) {
		//loop through all of the appointments that haven't been sent
		$year 				= substr($app["app_date"],0,4);
		$month 				= substr($app["app_date"],5,2);
		$day				= substr($app["app_date"],8,2);
		$appointment_date 	= substr($app["app_date"],0,10);
		$notification_time 	= $app["value"];
		$notification_type 	= $app["type"];
		switch( $notification_type ) {
			case "Day(s)":
				$checkTime 	= mktime(0,0,0,$month, $day-$notification_time, $year);
				break;
			case "Week(s)":
				$checkTime 	= mktime(0,0,0,$month, $day-($notification_time*7), $year);
				break;
			case "Month(s)":
				$checkTime 	= mktime(0,0,0,$month-$notification_time, $day, $year);
				break;
		}
		echo "<BR>".date("Y-m-d", $checkTime);
		
		if($today == date("Y-m-d", $checkTime)) {
			
			//there is a message required
			echo "<BR>Sending Message<BR>";
			$patient_info 	= dl::select("sms_patient_contact_details", "pcd_id =".$app["patient_id"]);
			$patient_name 	= $patient_info[0]["pcd_firstname"]." ".$patient_info[0]["pcd_lastname"];
			$patientMobile 	= $patient_info[0]["pcd_mobile_number"];
			$messages 		= dl::select("sms_messages", "m_id =".$app["message_id"]);
			$messageToSend 	= $messages[0]["m_message"];
			$messageSubject = $messages[0]["m_short_title"];
			
			//lets update the message to include the personalised information
			$messageToSend 	= str_replace("%Patient_Name%", $patient_name, $messageToSend);
			$messageToSend 	= str_replace("%Appointment_Date%", date("d/m/Y", strtotime($appointment_date)), $messageToSend);
			$messageToSend 	= str_replace("%Appointment_Time%", $app["app_time"], $messageToSend);
			$message = new message("ian.bettison@ncl.ac.uk");
			$message->set_message($messageSubject, $messageToSend);
			$message->set_To( array(array("email"=>$patientMobile."@sms.textapp.net", "name"=>$patient_name), array("email"=>"ian.bettison@ncl.ac.uk", "name"=>"Ian Bettison") ));
			echo $messageToSend, $messageSubject, $patientMobile;
			
			// send the message
			$message->send();
			// now lets update the sms_sent_message table
			//this creates a timestamp of the sent message so it is not NULL in the getQuery above.
			dl::insert("sms_sent_messages", array("message_appointment_id"=>$app["ma_id"]));
			$notifyList = array(array("email"=>"dianne.wake@ncl.ac.uk", "name"=>"Dianne Wake"), array("email"=>"kevin.short@ncl.ac.uk", "name"=>"Kevin Short"));
			$messageToSend	= $patient_name." was just sent a text message. The message subject was <i>'".$messageSubject."'</i> and was sent to this mobile number - ".$patientMobile.".";
			$message = new message("ian.bettison@ncl.ac.uk");
			$message->set_message("Text message sent!", $messageToSend);
			$message->set_To($notifyList);
			echo "Sending Notification";
			$message->send();
		}
	}
}

	
class message {

	public $message;
	public $subject;
	public $mail;
	
	function __construct( $from_name ) {
		$this->mail 				= new PHPMailer();
		$this->mail->IsMail(); 		// use PHP mail() function
		$this->set_From( $from_name );
	}
	
	
	function set_message( $subject, $message ) {
		$this->message 				= $message;
		$this->subject 				= $subject;
		$this->mail->MsgHTML($message);
		$this->mail->Subject    	= $subject;
	}
	
	function set_From( $from ) {
		$this->mail->SetFrom( $from );
		$this->mail->AddReplyTo($from);
	}
	
	function set_To( $arrayTo ) {
		/*
		This function takes an array as the To parameter and is in the format of:
			array("email"=>"email@address.co.uk", "name"=>"Email Sender1",
					"email"=>"email2@address.co.uk", "name"=>"Email Sender2")
		*/
		foreach( $arrayTo as $sendTo ) {
			$this->mail->AddAddress($sendTo["email"], $sendTo["name"]);
		}
	}
	
	function set_CC( $arrayCC ) {
		/*
		This function takes an array as the CC parameter and is in the format of:
			array("email"=>"email@address.co.uk", "name"=>"Email Sender1",
					"email"=>"email2@address.co.uk", "name"=>"Email Sender2")
		*/
		foreach( $arrayCC as $CC ) {
			$this->mail->AddCC($CC["email"], $CC["name"]);
		}
	}
	
	function set_BCC( $arrayBCC ) {
		/*
		This function takes an array as the BCC parameter and is in the format of:
			array("email"=>"email@address.co.uk", "name"=>"Email Sender1",
					"email"=>"email2@address.co.uk", "name"=>"Email Sender2")
		*/
		foreach( $arrayBCC as $BCC ) {
			$this->mail->AddBCC($BCC["email"], $BCC["name"]);
		}
	}
	
	function send( ) {
		if(!$this->mail->Send()) {
			echo "Mailer Error: " . $this->mail->ErrorInfo;
		}
	}
}
?>