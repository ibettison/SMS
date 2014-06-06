<?php
/*  
admin.class.php:		to create the fields required for the purpose of administration / maintenance of the admin lists contained in the database  
Created by: 				Ian Betison
Date: 						14th March 2012
*/


class editlist {
	public		$id;
	public		$description;
	public 		$tablename;
	private 	$fieldlist;	
	public	 	$addform;

	function __construct( $list_tablename, $list_id, $list_description ) {

		$this->tablename 	= $list_tablename;
		$this->id 			= $list_id;
		$this->description 	= $list_description;
	}

	function showList( $link ) {
		$this->addform = new form( "additions_form", $link);
		echo $this->addform->show_form();
		$list = dl::select($this->tablename);
		foreach( $list as  $this->fieldlist) {
			$field = new checkbox("select", "checkbox", "greyInput", 6, "", "delete".$this->fieldlist[$this->id], $text_class);
			echo "<span>".$field->show_field()."</span>";
			$field = new textarea("Description", "textarea", "greyTextAreaInput", "", $this->fieldlist[$this->description], "description".$this->fieldlist[$this->id], 41,2);
			echo "<span class='form_field'>".$field->show_field()."</span><BR />";
		}
		echo "Add a new list item:<BR />";
		$field = new fields("New Items", "text", "greyInput", "40", "", "newItem");
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";
		//hidden fields to pass the field values to the $_POST array to allow writing of the records.
		$field = new fields("Table", "hidden", "greyInput", "25", $this->tablename, "hiddenTable");
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";
		$field = new fields("Id_Field", "hidden", "greyInput", "25", $this->id, "hiddenId");
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";
		$field = new fields("Description_Field", "hidden", "greyInput", "25", $this->description, "hiddenDescription");
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";
	}
	
	function addtolist(  ) {
		foreach( $_POST as $listing=>$values ) {
			if( substr($listing,0,6) == "delete" ) {
				//get the id value from the end of the variable
				$id = substr($listing,6, strlen($listing));
			}elseif( substr($listing,0, 11) == "description" ) {
				$descId = substr($listing,11, strlen($listing)) ;
				if($id != $descId) {
					dl::update($this->tablename, array($this->description=>$values), $this->id."=".$descId);
				}else{
					dl::delete($this->tablename, $this->id."=".$descId);
				}
			}
		}
		if( !empty($_POST["newItem"]) ) {
			dl::insert($this->tablename, array($this->description=>$_POST["newItem"]));
		}
	}
	
	function showtitle( $title1, $width1, $title2, $width2, $class ) {
		echo "<h3>List Management</h3>";
		$this->displaylist();
		echo "<b>Warning:</b> selecting to delete an item will remove all of the links<BR /> to it within the database. <BR /><BR /><u>Please delete items with caution</u><BR /><BR />";
		echo "<span class='$class' style='width:".$width1."px;'>".$title1."</span><span class='$class'  style='width:".$width2."px;'>".$title2."</span><BR />";
	}
	
	function displaylist() {
		$selList = new selectlist( "sms_lists", "index.php?func=chooseList" );
		$selList->showlist();
	}
	
	function showsubmit() {
		$button = new fields("submit Button", "submit", "bluebutton", 10, "Manage List","");
		echo $button->show_field();
		echo $this->addform->close_form();
	}
}

class selectlist{
	
	public $table_name;
	public $list;
	public $link;
	public $values;
	
	
	function __construct( $list_tablename, $list_link ) {
		$this->table_name = $list_tablename;
		$this->link = $list_link;
		$this->open_table();
	}
	
	function open_table() {
		$this->list = dl::select( $this->table_name );
		foreach( $this->list as $l ) {
			$this->values[] = $l["list_tablename"];
		}
	}
	
	function showlist() {
		$listform = new form( "list_form", $this->link);
		echo $listform->show_form();
		echo "Select the table you would like to edit.<BR/><BR />";
		$selection = new selection("List tablename","text", "greyInput", "20", "" , "listSelect", $this->values, $_POST["listSelect"],"0");
		echo "<span class='form_prompt'>".$selection->show_prompt()."</span>";
		echo "<span class='form_field'>".$selection->show_field()."</span>";
		$button = new fields("submit Button", "submit", "bluebutton", 10, "Choose List","");
		echo $button->show_field();
		echo $listform->close_form();
	}
	
}

class user {
	
	public 	$user_types;
	public 	$permissions;
	public 	$valid_required;
	public 	$valid_pass;
	public 	$valid_email;
	public 	$message_header;
	public 	$message_body;
	private $subject;
	private $body;
	
	function __construct(  ) {
		$this->valid_required = array(array("type"=>"required","value"=>"true"));
		$this->valid_pass = array(array( "type"=>"required", "value"=>"true" ), array( "type"=>"minlength", "value"=>6) );
		$this->password_match = array(array("type"=>"required", "value"=>"true"), array("type"=>"equalTo", "value"=>"\"#password\""), array("type"=>"minlength", "value"=>6));
		$this->valid_email = array(array( "type"=>"required", "value"=>"true" ), array( "type"=>"email", "value"=>"true" ) );
	}
	
	function new_user( $link ) {
		$userform = new form( "newuser_form", $link);
		echo $userform->show_form();
		validation::validate_form("newuser_form");
		$types = dl::select("sms_user_types_name");
		foreach( $types as $type ) {
			$this->user_types[] = $type["type_name"];
		}
		echo "<fieldset>";
		echo "<legend>Add a New User</legend>";
		$field = new fields("Name", "text", "greyInput", "40", "", "user_name");
		echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";

		$field = new fields("Email Address", "text", "greyInput", "40", $this->tablename, "email");
		echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";

		$field = new fields("Password", "password", "greyInput", "25", $this->id, "password");
		echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";

		$field = new fields("Retype password", "password", "greyInput", "25", $this->description, "password2");
		echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";

		$selection = new selection("User Types","text", "greyInput", "20", "" , "userTypeSelect", $this->user_types, "", "0");
		echo "<span class='form_prompt'>".$selection->show_prompt()."</span>";
		echo "<span class='form_field'>".$selection->show_field()."</span>";
		$button = new fields("submit Button", "submit", "bluebutton", 10, "Create User","");
		echo $button->show_field();
		echo "<input type=\"submit\" class=\"bluebutton\" name=\"manage_user\" id=\"manage_user\" value=\"Manage Users\" />";
		validation::assign_click("manage_user");
		echo $userform->close_form();
		echo "</fieldset>";
	}
	
	function setMessageContent( $subject, $body ) {
		$this->subject 	= 	$subject;
		$this->body		= $body;
	}
	
	function write_user() {
		if( !defined('SALT') ) {
			 define( 'SALT', "AnH1xAf&-+]qtyUUOppI6672mNNbT1098114&&`^`!GHo0o0{}..>ftABB");
		}
		if( empty($_POST["manage_user"]) ) {
			if( !empty($_POST["user_name"]) and !empty($_POST["email"]) ) {
				if(!empty($_POST["password"]) and !empty($_POST["password2"])) {
					if($_POST["password"] == $_POST["password2"]) {
						$types = dl::select("sms_user_types_name", "type_name='".$_POST["userTypeSelect"]."'");
						dl::insert("sms_user", array("user_name"=>$_POST["user_name"], "user_email_address"=>$_POST["email"]));
						$maxId = dl::getId();
						dl::insert("sms_user_types", array("user_id"=>$maxId, "user_types_name_id"=>$types[0]["type_name_id"]));
						$sql = "INSERT into sms_security (security_password, user_id) VALUES (MD5('".SALT.$_POST["password"]."'), ".$maxId.")";
						dl::setQuery($sql);
						//new user created lets email them confirmation
						$message = new message("ian.bettison@ncl.ac.uk");
						$message->set_message($this->subject, $this->body);
						$message->set_To( array(array(email=>$_POST["email"], name=>$_POST["user_name"]) ));
						$message->send();
					}
				}
			}
		}
	}
	
	function manage_users($link) {
		$manageuserform = new form( "user_form", $link);
		echo $manageuserform->show_form();
		validation::validate_form("user_form");
		$users = dl::select("sms_user");
		$types = dl::select("sms_user_types_name");
		foreach( $types as $type ) {
			$this->user_types[] = $type["type_name"];
		}
		echo "<fieldset>";
		echo "<legend>User List</legend>";
		echo "<BR>";
		echo "<span style='width:40px;'>Del</span><span style='width:270px'>User Name</span><span style='width:270px;'>Email Address</span><span>User Types</span><BR />";
		foreach( $users  as $user) {
			$selection = new checkbox("","checkbox", "greyInput", "20", $user["user_name"] , "delete".$user["user_id"], "");
			echo "<span class='form_field'>".$selection->show_field()." ".$permission["permission_name"]."</span>";
			$field = new fields("User Name", "text", "greyInput", "40", $user["user_name"], "user".$user["user_id"]);
			echo "<span class='form_field'>".$field->show_field()."</span>";

			$field = new fields("Email", "text", "greyInput", "40", $user["user_email_address"], "email".$user["user_id"]);
			echo "<span class='form_field'>".$field->show_field()."</span>";

			$user_types = dl::select("sms_user_types", "user_id =". $user["user_id"]);
			$user_types_name = dl::select("sms_user_types_name", "type_name_id =".$user_types[0]["user_types_name_id"]);
			$selection = new selection("User Types","text", "greyInput", "20", "" , "type".$user["user_id"], $this->user_types, $user_types_name[0]["type_name"], "0");
			echo "<span class='form_field'>".$selection->show_field()."</span><BR />";
		}
		$button1 = new fields("submit Button", "submit", "bluebutton", 10, "Update Users", "");
		echo $button1->show_field();
		echo "<input type=\"submit\" class=\"bluebutton\" name=\"Cancel\" id=\"Cancel\" value=\"Cancel\" />";
		validation::assign_click("Cancel");
		echo $manageuserform->close_form();
		echo "</fieldset>";
	}
	
	function update_users() {		
		foreach( $_POST as $user=>$values ) {
			switch( substr($user,0,4) ) {
				case "user":
					$userId = substr($user,4,strlen($user));
					dl::update("sms_user", array("user_name"=>$values), "user_id=".$userId);
				break;
				case "emai":
					$emailId = substr($user,5,strlen($user));
					dl::update("sms_user", array("user_email_address"=>$values), "user_id=".$emailId);
				break;
				case "type":
				$types = dl::select("sms_user_types_name", "type_name='".$values."'");
				$typeId = substr($user,4, strlen($user));
				dl::update("sms_user_types", array("user_types_name_id"=>$types[0]["type_name_id"]), "user_types_id = ".$typeId);
				break;
				case "dele":
				$delId = substr($user,6, strlen($user));
				dl::delete("sms_user_types", "user_id = ".$delId);
				dl::delete("sms_user", "user_id = ".$delId);
				break;
			}
			
		}
	}
	
	function new_user_type( $link ) {
		$usertypeform = new form( "newtype_form", $link);
		echo $usertypeform->show_form();
		validation::validate_form("newtype_form");
		$permissions = dl::select("sms_permissions");
		echo "<fieldset>";
		echo "<legend>New User Type</legend>";
		$field = new fields("Type Name", "text", "greyInput", "40", "", "type_name");
		echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";

		echo "<h3>Permissions</h3>";
		foreach( $permissions as $permission ) {
			$selection = new checkbox("","checkbox", "greyInput", "20", $permission["permission_name"] , "permission".$permission["permission_id"], $this->user_types);
			echo "<span class='form_prompt'>".$selection->show_prompt()."</span>";
			echo "<span class='form_field'>".$selection->show_field()." ".$permission["permission_name"]."</span><BR />";
		}
		$button1 = new fields("submit Button", "submit", "bluebutton", 10, "Add User Type","");
		echo $button1->show_field();
		echo "<input type=\"submit\" class=\"bluebutton\" name=\"manage_types\" id=\"manage_types\" value=\"Manage User Types\" />";
		validation::assign_click("manage_types");
		echo $usertypeform->close_form();
		echo "</fieldset>";
	}
	
	function write_usertype() {
		if(empty($_POST["manage_types"])) {
			dl::insert("sms_user_types_name", array("type_name"=>$_POST["type_name"]));
			$sql = "select MAX(type_name_id) as maxType from user_types_name";
			$maxId = dl::getQuery($sql);
			$permissions = dl::select("sms_permissions");
			foreach( $permissions as $permission) {
				$found = false;
				foreach( $_POST as $type=>$keys ) {
					switch( substr($type,0,10)) {
						case "permission":
							if($permission["permission_id"] == substr($type,10,strlen($type))) {
								$found=true;
								dl::insert("sms_permission_user", array("user_types_name_id"=>$maxId[0]["maxType"],"permission_id"=>$permission["permission_id"], "permission_value"=>1));							
							}
						break;
					}
				}
				if(!$found) {
					dl::insert("sms_permission_user", array("user_types_name_id"=>$maxId[0]["maxType"],"permission_id"=>$permission["permission_id"], "permission_value"=>0));		
				}
			}
		}
	}
	
	function manage_types($link) {
		$managetypeform = new form( "managetype_form", $link);
		echo $managetypeform->show_form();
		validation::validate_form("managetype_form");
		$types = dl::select("sms_user_types_name");
		$permissions = dl::select("sms_permissions");
		echo "<fieldset>";
		echo "<legend>Manage User Types</legend>";
		echo "<BR>";
		echo "<span style='width:40px;'>Del</span><span style='width:270px'>User Type Name</span><BR />";
		foreach( $types as $type ) {
			$selection = new checkbox("","checkbox", "greyInput", "20", "", "delete".$type["type_name_id"], $this->user_types);
			echo "<span class='form_field'>".$selection->show_field()."</span> ";
			$field = new fields("Type Name", "text", "greyInput", "40", $type["type_name"], "type_name".$type["type_name_id"]);
			echo "<span class='form_field'>".$field->show_field()."</span><BR />";

			foreach( $permissions as $permission ) {
				$permUsers = dl::select("sms_permission_user", "user_types_name_id = ".$type["type_name_id"]." and permission_id = ".$permission["permission_id"]);
				if($permUsers[0]["permission_value"] == 1) {
					$selected = "Yes";
				}else{
					$selected = "No";
				}
				$selection = new checkbox("","checkbox", "greyInput", "20", $permission["permission_name"] , $type["type_name_id"]."_permission_".$permission["permission_id"], $this->user_types, $selected);
				echo "<span class='form_prompt'>".$selection->show_prompt()."</span>";
				echo "<span class='form_field'>".$selection->show_field()." ".$permission["permission_name"]."</span><BR />";
			}
		}
		$button2 = new fields("submit Button", "submit", "bluebutton", 10, "Manage Types","");
		echo $button2->show_field();
		echo "<input type=\"submit\" class=\"bluebutton\" name=\"Cancel\" id=\"Cancel\" value=\"Cancel\"/>";
		validation::assign_click("Cancel");
		echo $managetypeform->close_form();
		echo "</fieldset>";
	}
	
	function update_types() {
		//delete the table contents of permission_user
		dl::delete("sms_permission_user");
		$types = dl::select("sms_user_types_name", "type_name_id");
		$permissions = dl::select("sms_permissions", "permission_id");
		foreach($types as $t) {
			foreach($permissions as $p) {
				dl::insert("sms_permission_user", array("user_types_name_id"=>$t["type_name_id"], "permission_id"=>$p["permission_id"]));
			}
		}
		foreach( $_POST as $permissions=>$values) {
			switch ( substr($permissions,0,6) ) {
				case "type_n":
					$id = substr($permissions,9, strlen($permissions)); //this is the typename Id
					$typeName = dl::update("sms_user_types_name", array("type_name"=>$values), "type_name_id=".$id);
				break;
				case "delete":
					$delId = substr($permissions,6, strlen($permissions));
					//check to see if any users are using this user type first and warn if they are aborting the deletion
					$delCheck = dl::select("sms_user_types", "user_types_name_id = ".$delId);
					if(empty($delCheck)) {
						dl::delete("sms_user_types_name", "type_name_id = ".$delId);
					}else{
						//now need to inform the user that the user type is still being used and cannot be deleted
						?>
					<script>
						alert('The user type that you have attempted to delete is still in use.\nPlease check the users and select a different user type for them.\n This will enable you to delete this user type. Thank you.');
					</script>
					<?php
					}
				break;
				default:
					$pLoc = strrpos($permissions, "_");
					$pId = substr($permissions,$pLoc+1, strlen($permissions));
					dl::update("sms_permission_user",array("permission_value"=>1), "permission_id =".$pId." and user_types_name_id = ".$id);
			}
		}
	}
	
	function new_permission( $link ) {
		$newpermform = new form( "newperm_form", $link);
		echo $newpermform->show_form();
		validation::validate_form("newperm_form");
		echo "<fieldset>";
		echo "<legend>New Permission</legend>";
		$field = new fields("Permission Name", "text", "greyInput", "40", "", "permission_name");
		echo "<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";

		$button1 = new fields("submit Button", "submit", "bluebutton", 10, "Add Permission","");
		echo $button1->show_field();
		echo "<input type=\"submit\" class=\"bluebutton\" name=\"manage_permission\" id=\"manage_permission\" value=\"Manage Permissions\" />";
		validation::assign_click("manage_permission");
		echo $newpermform->close_form();
		echo "</fieldset>";
	}
	
	function write_permission() {
		if(empty($_POST["manage_permission"])) {
		
			dl::insert("sms_permissions", array("permission_name"=>$_POST["permission_name"]));
			$sql = "select MAX(permission_id) as maxP from sms_permissions";
			$permission = dl::getQuery($sql);
			//get the new permission id as you now need to add the permission to all of the userTypeSelect
			$types = dl::select("sms_user_types_name");
			foreach( $types as $type ) {
				dl::insert("sms_permission_user", array("user_types_name_id"=>$type["type_name_id"], "permission_id"=>$permission[0]["maxP"], "permission_value"=>0));
			}
			//now need to inform the user to edit the user types to add this new permission to the right user types
				?>
			<script>
				alert('New Permission has been added.\nYou will now need to check the user types and\n add this new permission to the types that require it.');
			</script>
			<?php
		}
	}
	
	function manage_permissions($link) {
		$managepermform = new form( "perm_form", $link);
		echo $managepermform->show_form();
		validation::validate_form("perm_form");
		$permissions = dl::select("sms_permissions");
		echo "<fieldset>";
		echo "<legend>Manage Permissions</legend>";
		echo "<BR />Make changes to the permission names or select the checkbox to delete a permission.<BR /><BR />";
		echo "<span style='width:40px;'>Del</span><span>Permissions</span><BR />";
		foreach( $permissions as $permission ) {
			$selection = new checkbox("","checkbox", "greyInput", "20", $permission["permission_name"] , "delete".$permission["permission_id"], $this->user_types, $selected);
			echo "<span class='form_field'>".$selection->show_field()."</span>";
			$field = new fields("Permission Name", "text", "greyInput", "40", $permission["permission_name"], "permission".$permission["permission_id"]);
		echo "<span class='form_field'>".$field->show_field()."</span><BR />";
		validation::add_rules("permission".$permission["permission_id"], $this->valid_required);
		}
		$button2 = new fields("submit Button", "submit", "bluebutton", 10, "Save Permissions","");
		echo $button2->show_field();
		echo "<input type=\"submit\" class=\"bluebutton\" name=\"Cancel\" id=\"Cancel\" value=\"Cancel\" />";
		validation::assign_click("Cancel");
		echo $managepermform->close_form();
		echo "</fieldset>";
	}
	
	function update_permissions() {
		foreach($_POST as $permissions=>$values) {
			switch( substr($permissions,0,6) ) {
				case "delete":
					$id = substr($permissions,6,strlen($permissions));
					dl::delete("sms_permissions", "permission_id =".$id);// delete the permission
					dl::delete("sms_permission_user", "permission_id=".$id); // delete the permission from each user type.
				break;
				case "permis":
					$permId = substr($permissions,10, strlen($permissions));
					if($id <> $permId) {
						dl::update("sms_permissions", array("permission_name"=>$values), "permission_id =".$permId);
					}
				break;
			}
		}
	
	}
	
}

class message {

	public $message;
	public $subject;
	public $mail;
	
	function __construct( $from_name ) {
		$this->mail 				= new PHPMailer();
		$this->mail->IsMail(); 							// use PHP mail() function
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