<?php 
function check_permissions( $accessRight ){
	/*pass the accessRight to this function to check if the user has the right to perform the process
	the permissions passed are:
	View Reports
	View Patients
	Add Patients
	Add Users
	Manage Lists
	*/
	//serch the array for the access right passed
	$permissionValue = 0;
	//find the actual element
	foreach( $_SESSION["settings"] as $settings ) {
		if( $settings["permission_name"] == $accessRight) {
			$permissionValue = $settings["permission_value"];
		}
	}
	return $permissionValue;
}

function create_tabs( $tabnames, $selectedtab=0) {
	$tabs = new tabs($tabnames, "tabId", $selectedtab);
	echo "<div id='tabId'>";
		echo $tabs->create_tabs();
		foreach( $tabnames as $tnames ) {
			$tabs->show_content($tnames["link"], $tnames["func"]);
		}
	echo "</div>";
}

function login_form( $confirmed=1 ) {
	include("validation_rules.php");
	/* This is the function to display the login form that prompts for an id and password to allow access
	to the Fetal medicine application. */
		if($confirmed) {
			$form = new form( "login_form", "index.php?func=login");
			echo $form->show_form();			
			validation::validate_form("login_form");
			$field = new fields("Email Address", "text", "greyInput", 30, "", "email_address", "type your email address");
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."</span><BR />";
			validation::add_rules("email_address", $rule_email_required);
			$field = new fields("Password", "password", "greyInput", 30, "", "password");
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."</span><BR /><br />";
			validation::add_rules("password", $rule_minlength6_required);
		}else{
			$form = new form( "login_form", "index.php?func=confirm");
			echo $form->show_form();
			validation::validate_form("login_form");
			echo "<div style='margin:5px; color:#fff; background-color:#f07c4a; border: 1px solid #804040;'>Your user account has not yet been confirmed. Type your email address and the password you were sent in the notification email, then create a new password for your account.</div>";
			$field = new fields("Email Address", "text", "greyInput", 30, "", "email_address", "type your email address");
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."</span><BR />";
			validation::add_rules("email_address", $rule_email_required);
			$field = new fields("Password", "password", "greyInput", 30, "", "password");
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."</span><BR />";
			validation::add_rules("password", $rule_minlength6_required);
			$field = new fields("New Password", "password", "greyInput", 30, "", "newPassword");
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."</span><BR />";
			validation::add_rules("newPassword", $rule_minlength6_required);
			$field = new fields("Confirm Password", "password", "greyInput", 30, "", "confirmPassword");
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."</span><BR /><br /><br />";
			//create rule for validation of the password : must equal password above
			$password_match = array(array("type"=>"required", "value"=>"true"), array("type"=>"equalTo", "value"=>"\"#newPassword\""), array("type"=>"minlength", "value"=>6));
			validation::add_rules("confirmPassword", $password_match);
		}
		
		$button = new fields("submit Button", "submit", "bluebutton", 20, "Login", "");
		echo "</div>";// sidebarline
			echo "<div class='sidebar'><center>";
				echo $button->show_field();
			echo "</center>";
			echo $form->close_form();
		echo "</div></div>";
}

function disp_tabs( $selected_tab ) {
	$tabnames = array(array("link"=>"tab1", "tabname"=>"Patient Details", "func"=>"add_patient"), array("link"=>"tab2", "tabname"=>"Appointments", "func"=>"show_appointments") );
	if(check_permissions("Add Users")) {
		array_push($tabnames, array("link"=>"tab7", "tabname"=>"User Management", "func"=>"user_management") );
	}
	if(check_permissions("Manage Lists")) {
		array_push($tabnames, array("link"=>"tab8", "tabname"=>"Lists", "func"=>"managelists") );
	}
	create_tabs( $tabnames,  $selected_tab);
}

function infoBar( $text ) {
	echo "<div class='infobar'>";
		echo "<div class='infoimg'>";
			echo "<div class='info'>".$text;
			echo "</div>";
		echo "</div>";
	echo "</div>";
}

function calcAge($dob) {
	$today = date('Y-m-d');
	$age = date("Y") - date("Y", strtotime($dob));
	if( date('m') < date("m", strtotime($dob)) ) {
		$age--;
	} 
	if( date('m') == date("m", strtotime($dob)) ) {
		if(date('d') < date("d", strtotime($dob)) ) {
			$age--;
		}
	}
	return($age);
}
function add_patient() {
	include("validation_rules.php");
	dl::$debug=false;
	
	$patients = dl::select("sms_patient_contact_details");
	if(!empty($patients)) {
		
		foreach( $patients as $patient ) {
			$arrPatients[] = $patient["pcd_mobile_number"]." ".$patient["pcd_firstname"]." ".$patient["pcd_lastname"];
			
		}
		$addform = new form( "patient_select", "index.php?func=select_patient");
		echo $addform->show_form();
		
		echo "<fieldset>";
			echo "<legend>Patient Selection</legend>";
			echo "<BR />Start typing the Patients name to auto search for them.<BR /><BR />";
			$field = new selection("Patient Name ", "text", "greyInput", 40, "", "sel_name",$arrPatients, "", "1");
			$field->setTabIndex(1);
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."<span id='show_delIcon' style='display:none;'><a href='#' id='delLink' border='0' style='text-decoration: none;' ><img src='images/DeleteRed.png' title='Click to delete'></a></span><BR />";
			echo "<div id='dialog_confirm' style='display:none;'>Confirm deletion</div>";
		echo "</fieldset>";
		echo "<div style='clear:left;'></div>";
		$button = new fields("submit Button", "submit", "bluebutton", 30, "Select Patient","");
		echo "<center>";
		echo $button->show_field();
		echo "</center>";
		echo $addform->close_form();
		echo "<BR><BR>OR<BR><BR>";
		$manage_list = new list_deletion( "sel_name", "show_delIcon", "delLink", "dialog_confirm", "delete_patient", "index.php" );
		list_deletion::manage_list();
	}
	$addform = new form( "patient_entry", "index.php?func=add_patient" );
	echo $addform->show_form();
	validation::validate_form("patient_entry");
	echo "<fieldset>";
		echo "<legend>Patient Information</legend>";
		$field = new fields("First Name *", "text", "greyInput", 40, "", "firstname");
		$field->setTabIndex(2);
		echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo "<span class='form_field'>".$field->show_field()."<BR />";
		validation::add_rules("firstname", $rule_required);
		
		$field = new fields("Last Name *", "text", "greyInput", 40, "", "lastname");
		$field->setTabIndex(3);
		echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";
		validation::add_rules("lastname", $rule_required);
		$field = new fields("Mobile No. *", "text", "greyInput", 40, "", "mobile");
		$field->setTabIndex(4);
		echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";
		validation::add_rules("mobile", $rule_number_required);
	echo "</fieldset>";
	echo "<div style='clear:left;'></div>";
	$button = new fields("submit Button", "submit", "bluebutton", 30, "Add Patient","");
	echo "<center>";
	echo $button->show_field();
	echo "</center>";
	echo $addform->close_form();
}

function select_patient() {
	$selected = substr($_POST["sel_name"],0,strpos($_POST["sel_name"]," "));
	$patient_id = dl::select("sms_patient_contact_details", "pcd_mobile_number = '$selected'");
	$id = $patient_id[0]["pcd_id"];
	$_SESSION["patient_id"] = $id;
	$_SESSION["patient_name"] = $patient_id[0]["pcd_firstname"]." ".$patient_id[0]["pcd_lastname"];
}

function add_patient_record( ) {
	if( check_permissions("Add Patients") ) {
		//check to see if the Patient record exists if it does don't add it again
		//add the patient details and the address details including contacts here
		$patient_details 			= array($_POST["firstname"], $_POST["lastname"],$_POST["mobile"]);
		$patient_fields 			= array("pcd_firstname", "pcd_lastname", "pcd_mobile_number");
		$write_line 	 			= array_combine($patient_fields, $patient_details);
		dl::insert("sms_patient_contact_details", $write_line);
		$_SESSION["patient_id"]		= dl::getId();
		$_SESSION["patient_name"] 	= $_POST["firstname"]." ".$_POST["lastname"];
	}
}

function editPhone($phoneNumber) {
	dl::update("sms_patient_contact_details", array("pcd_mobile_number"=>$phoneNumber), "pcd_id = ".$_SESSION["patient_id"]);
}

function show_appointments() {
	include("validation_rules.php");
	dl::$debug=false;
	echo "<div style='height:3em;cursor:pointer;'>Patient Name = ".$_SESSION["patient_name"]." <img id='edit_patient' src='images/TextEdit.png' /></div>";
	$phoneNo = dl::select("sms_patient_contact_details", "pcd_id =".$_SESSION["patient_id"]);
	echo "<div id='edit_phone' style='display:none;'>";
	$addform = new form( "edit_phone_number", "index.php?func=edit_phone");
	echo $addform->show_form();
	echo "<fieldset>";
		echo "<legend>Change Phone Number</legend>";
		$field = new fields("Phone Number *", "text", "greyInput", 60, $phoneNo[0]["pcd_mobile_number"], "pNum");
		echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo "<span class='form_field'>".$field->show_field()."<BR />";
		$button = new fields("submit Button", "submit", "bluebutton", 30, "Amend Phone Number","amend_phone");
		echo "<center>";
		echo $button->show_field();
		echo "</center>";
		echo $addform->close_form();
	echo "</fieldset>";
	echo "</div>";
	$appointments = dl::select("sms_appointments");
	
	$notification_types = dl::select("sms_notification_type");
	foreach( $notification_types as $nots ) {
		$arrNots[] = $nots["nt_description"];
	}
	$addform = new form( "appointment_entry", "index.php?func=add_appointment");
	echo $addform->show_form();
	validation::validate_form("appointment_entry");
	echo "<fieldset>";
		echo "<legend>Add Appointments</legend>";			
			$field = new fields("Appointment *", "text", "greyInput", 60, "", "desc");
			$field->setTabIndex(2);
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."<BR />";
			validation::add_rules("desc", $rule_required);
			$field = new dates("Appointment Date *", "date", "greyInput", 15, "", "app_date","app_date");
			$field->setTabIndex(3);
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."</span><BR />";
			validation::add_rules("app_date", $rule_required);
			$field = new timepicker("Appointment Time *", "text", "greyInput", 15, "", "field_time");
			$field->setTabIndex(4);
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."</span><BR />";
			validation::add_rules("field_time", $rule_required);
			$field = new fields("Notification time *", "text", "greyInput", 2, "", "value");
			$field->setTabIndex(5);
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."</span>";
			validation::add_rules("value", $rule_number_required);
			$field = new selection("Notification Type *", "text", "greyInput", 10, "", "type", $arrNots,"", "0");
			$field->setTabIndex(6);
			echo "<span class='form_field'>".$field->show_field()."</span><BR />";
			validation::add_rules("type", $rule_required);
					  
			echo "<div id='mlink'>";
			setup_Message();
			echo "</div>";
	?>
		<script type="text/javascript">
			$(document).ready(function() {
				$("#edit_patient").click(function(event) {
					$("#edit_phone").slideToggle();
				});
				$("#amend_phone").click(function(event) {
					$("#edit_phone_number").submit();
				});
				$("#add_app").click(function(event) {
					$("#appointment_entry").submit();
				});
			});
		</script>
	<?php
	$button = new fields("submit Button", "submit", "bluebutton", 30, "Add Appointment","add_app");
	echo "<center>";
	echo $button->show_field();
	echo "</center>";
	echo $addform->close_form();
	echo "</fieldset>";
	?>
	<script type="text/javascript">
		$(document).ready(function() {
			$("#message").click(function(event) {
				$("#message").blur(function() {
					$.post(
						"ajax.php",
					{
						func: "show_message",
						messVal: $(this).val()
					},
					function (data)
					{
						$("#disp_mess").html(data);
					});
				});
			});
		});
	</script>
	<?php
	//div to allow message display
	echo "<div id='disp_mess'>";
	echo "</div>";
	echo "<div id='message_disp'>";
	echo "</div>";
	echo "<div id='appointment_disp'>";
	appointment_body();
	echo "</div>";
}

function appointment_body(){
	echo "<fieldset>";
	echo "<legend>Appointments</legend>";
	$sql = "select * from sms_message_appointment as ma 
			left join
			sms_sent_messages as sm on (ma.ma_id = sm.message_appointment_id) 
			where
			patient_id = ".$_SESSION["patient_id"]."
			order by sm.sm_timestamp ASC
			";
	$apps = dl::getQuery( $sql );
	if(!empty($apps)){
		echo "<span class='searchHeader' style='width:300px'>Appointment Description</span><span class='searchHeader' style='width:80px'>Date</span><span class='searchHeader' style='width:70px'>Time</span><span class='searchHeader' style='width:90px'>Notify Time</span><span class='searchHeader' style='width:90px'>Notify Type</span><span class='searchHeader' style='width:300px'>Message</span><span class='searchHeader' style='width:45px'>Delete</span><BR>";
		foreach($apps as $app){
			$appointment = dl::select("sms_appointments", "a_id = ".$app["appointment_id"]);
			$message = dl::select("sms_messages", "m_id = ".$app["message_id"] );
			$sent = dl::select("sms_sent_messages", "message_appointment_id = ".$app["ma_id"]);
			if(empty($sent)) {
				$class = "searchBody";
			}else{
				$class = "sentBody";
			}
			echo "<span class='$class' style='width:300px; padding-bottom:11px; padding-top:11px;'>".substr($appointment[0]["a_description"],0,50)."</span><span class='$class' style='width:80px; padding-bottom:11px; padding-top:11px;'>".substr($appointment[0]["app_date"],0,10)."</span><span class='$class' style='width:70px; padding-bottom:11px; padding-top:11px;'>".$appointment[0]["app_time"]."</span><span class='$class' style='width:90px; padding-bottom:11px; padding-top:11px;'>".$appointment[0]["value"]."</span><span class='$class' style='width:90px; padding-bottom:11px; padding-top:11px;'>".$appointment[0]["type"]."</span><span class='$class' style='width:300px; padding-bottom:11px; padding-top:11px;'><a href='#' id='message".$app["ma_id"]."'>".substr($message[0]["m_short_title"],0,60)."...</a></span><span class='$class' style='width:45px; padding-bottom:11px; padding-top:11px;'><a href='#' id='delete".$app["ma_id"]."'>Delete</a></span><BR>";
			?>
			<script type="text/javascript">
			$(document).ready(function() {
				$("#message<?php echo $app["ma_id"]?>").click(function(event) {
					var func = "setMessageId";
					$.post(
						"ajax.php",
					{
						func: func,
						mId: <?php echo $app["ma_id"]?>
					},
					function (data)
					{
						$("#message_disp").html(data);
						$("#message_disp").dialog({ 
							autoOpen	: true,
							height		: 300,
							width		: 400,
							modal		: true,
							buttons: 
							[{
								text	: "Cancel",
								click	: function(){
									$("#message_disp").dialog("destroy");
								}
							}],
							close: function() {
								$("#message_disp").dialog("destroy");
							}
							
						});
					});
					
				});
				$("#delete<?php echo $app["ma_id"]?>").click(function(event) {
					alert("Deleting");
					var func = "delMessageId";
					$.post(
						"ajax.php",
					{
						func: func,
						dId: <?php echo $app["ma_id"]?>
					},
						function (data)
					{
						$("#appointment_disp").html(data);
					});
				});
			})

			</script>
			<?php
		}
		
	}else{
		echo "The patient currently has no appointments";
	}
	echo "</fieldset>";
}

function setup_Message(){
		include("validation_rules.php");
		$messages = dl::select("sms_messages");
		foreach( $messages as $mess ) {
			$arrMess[] = $mess["m_short_title"];
		}
		if(!empty($arrMess)) {
			$field = new selection("Message *", "text", "greyInput", 40, "", "message", $arrMess, "", "0");
			$field->setTabIndex(7);
			echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
			echo "<span class='form_field'>".$field->show_field()."</span>"."<span id='show_delMessIcon' style='display:none;'><a href='#' id='delMessLink' border='0' style='text-decoration: none;' ><img src='images/DeleteRed.png' title='Click to delete'></a></span><BR />";
			validation::add_rules("message", $rule_required);
			$manage_list = new list_deletion( "message", "show_delMessIcon", "delMessLink", "dialog_confirm", "delete_message", "index.php?func=select_patient" );
			list_deletion::manage_list();
		}else{
			echo "There are no messges in the system, please add a message";
		}
	echo "<BR><a href='#' id='mAddNote'>Add a Message</a>";
	$addNote = new addnote( "sms_messages", "mName", "mContent", "mAddNote", "mEditNote", "mAddDiv", "mEditDiv", "mAddForm", "mEditForm", "mlink", "mlinkholder");
	addnote::set_title("Add New Text Message");
	addnote::set_linkid( "m_id" ); //id of index field in file
	addnote::prepare_popup("Add_Note", addnote::$div, addnote::$addLink, addnote::$addform, 420, 510, "setup_Message"); //new Notes
	
	//addnote::set_title("Edit Text Messages");
	//addnote::prepare_popup("Manage_Notes", addnote::$editdiv, addnote::$editLink, addnote::$editform, 780, 750, "setup_Message"); //edit Notes
	addnote::show_noteLink(addnote::$table);
}


function add_appointment(){
	if( check_permissions("Add Appointments") ) {
		//check to see if the Patient record exists if it does don't add it again
		//add the patient details and the address details including contacts here
		$appointment_details 	= array($_POST["desc"], $_POST["app_date"],$_POST["field_time"],$_POST["value"],$_POST["type"]);
		$appointment_fields 	= array("a_description", "app_date", "app_time", "value", "type");
		$write_line 	 		= array_combine($appointment_fields, $appointment_details);
		dl::insert("sms_appointments", $write_line);
		//now link the message to the patient
		$appId 					= dl::getId();
		$messId 				= dl::select("sms_messages", "m_short_title ='".$_POST["message"]."'");
		dl::insert("sms_message_appointment", array("message_id"=>$messId[0]["m_id"], "patient_id"=>$_SESSION["patient_id"], "appointment_id"=>$appId));
	}
}

function managelists() {
	if(!empty($_POST["listSelect"]) ) {
		$table 				   		= dl::select("sms_lists", "list_tablename = '".$_POST["listSelect"]."'" );
		$tableName 			   		= $table[0]["list_tablename"];
		$tableFieldId 		   		= $table[0]["list_field_id"];
		$tableFieldDescription 		= $table[0]["list_field_description"];
	}else{
		$tableName 			   		= "sms_notification_type";
		$tableFieldId 		  		= "nt_id";
		$tableFieldDescription 		= "nt_description";
	}
	$list = new editlist($tableName, $tableFieldId, $tableFieldDescription);
	$list->showtitle("del", "40"," Description","250", "searchHeader");
	$list->showlist("index.php?func=manage_lists");
	$list->showsubmit();
}

function editlists() {
	if( check_permissions("Manage Lists") ) {
		$list = new editlist( $_POST["hiddenTable"], $_POST["hiddenId"], $_POST["hiddenDescription"] );
		$list->addtolist();			
	}else{
		?>
		<script>
			alert('Invalid action attempted - you will now be logged out.');
			window.location.href = "index.php";
		</script>
		<?php
		die();
	}
	
}

function user_management() {
	if( check_permissions("Add Users") ) {
		$users= new user();
		if($_POST["manage_user"] == "Manage Users") {
			$users->manage_users( "index.php?func=updateUsers" );
		}elseif( $_POST["manage_types"] == "Manage User Types") {
			$users->manage_types( "index.php?func=updateTypes");
		}elseif( $_POST["manage_permission"] == "Manage Permissions") {
			$users->manage_permissions( "index.php?func=updatePermissions" );
		}else{
			$users->new_user("index.php?func=addUser");			
			$users->new_user_type("index.php?func=addType");	
			$users->new_permission("index.php?func=addPermission");
		}
	}else{
		?>
		<script>
			alert('Invalid action attempted - you will now be logged out.');
			window.location.href = "index.php";
		</script>
		<?php
		die();
	}
}

function validate_fieldName($index) {

?>
<script type="text/javascript">
	$(document).ready(function() {
		var func =  "validate_field";
		$("#<?php echo $index?>").keypress(function(event) {
			var key = String.fromCharCode(event.which);

			$.post(
				"confirm_update.php",
				{ func: func,
					fieldVal: $("#<?php echo $index?>").val()+key,
					key: key
				},
				function (data)
				{
					$('#field_confirm').html(data);
			});
		});
	})

</script>
<?php

}

