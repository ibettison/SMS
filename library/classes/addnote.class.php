<?php
class addnote {
	/* declare local statics variables*/
	static public $table;
	static public $varName;
	static public $varContent;
	static public $link;
	static public $addLink;
	static public $editLink;
	static public $title;
	static public $div;
	static public $editdiv;
	static public $addform;
	static public $editform;
	static public $returnlink;
	static public $linkholder;
	static public $linkid;
	static public $lastpopup;
	
	/*construct method
		$table 			: the note table that will be updated
		$varName		: the variable name to use for adding a new note (Clinician Name)
		$varContent	: the variable name to use for adding a new note (Message Content)
		$addLink 		: used to pass the index to the `prepare_popup` method for the addition of a note
		$editLink		: used to pass the index to the `prepare_popup` method for the editing of the notes
		$div				: is the div ID passed to the `show_note` method for the Add dialog box
		$editdiv			: is the div ID passed to the `edit_notes` method for the  edit dialog box
		$addform		: the name of the form for the Add dialog (show_note() method)
		$editform		: the name of the form for the edit dialog (edit_notes() method)
		$returnlink	: is the div ID that the ajax.php writes to after the updates have taken place
		$linkholder	: id the name of the div ID used inside the `show_noteLink` method that is removed in the method `prepare_popup` before the HTML is returned to the page
		
	*/
	
	function __construct( $table, $varName, $varContent, $addLink, $editLink, $div, $editdiv, $addform, $editform, $returnlink, $linkholder) {
		
		self::$table 			= $table;
		self::$varName			= $varName;
		self::$varContent		= $varContent;
		self::$addLink 			= $addLink;
		self::$editLink 		= $editLink;
		self::$div 				= $div;
		self::$editdiv			= $editdiv;
		self::$addform 			= $addform;
		self::$editform			= $editform;
		self::$returnlink 		= $returnlink;
		self::$linkholder 		= $linkholder;
	}
	
	static function set_title( $title ) {
		self::$title = $title;
	}
	
	function get_title( ) {
		return self::$title;
	}
	
	static function set_linkid( $linkid ) {
		self::$linkid = $linkid;
	}
	
	static function get_linkid( ) {
		return self::$linkid;
	}
	
	static function set_editlink( $link ) {
		self::$editLink = $link;
	}
	
	function get_editlink( ) {
		return self::$editLink;
	}
	
	static function prepare_popup( $function, $index, $button_index, $form, $height, $width, $resetFunc ) {
		?>
		<script type="text/javascript">
			$(document).ready(function() {
				
				$("#<?php echo $button_index?>").click(function(event) {
					event.preventDefault();
					$("#<?php echo $index?>").dialog({ 
						autoOpen: true,
						height: <?php echo $height ?>,
						width: <?php echo $width ?>,
						modal: true,
						buttons: [{
							text: "Save",
							click: function(){
								var func =  "<?php echo $function?>";
								var table = '<?php echo self::$table?>';
								var returnLink = '<?php echo self::$returnlink?>';
								var message = $('#<?php echo self::$varContent?>').val();
								var shortTitle = $('#<?php echo self::$varName?>').val();
								var linkid = '<?php echo self::get_linkid() ?>';
								var editLink = '<?php echo self::$editLink?>';
								var resetFunc = '<?php echo $resetFunc?>';
								var values = $('#<?php echo $form ?>').serializeArray();

								$.post(
									"ajax.php",
									{ func: func,
										table: table,
										returnLink: returnLink,
										message: message,
										short_title: shortTitle,
										linkid: linkid,
										editLink: editLink,											
										resetFunc: resetFunc,
										values: values
										
									},
									function (data)
									{
										$("#<?php echo $index?>").dialog("destroy");
										$("#<?php echo self::$editdiv?>").remove();
										$("#<?php echo self::$div?>").remove();
										$("#<?php echo self::$linkholder?>").remove();
										$('#<?php echo self::$returnlink?>').html(data);
										$("#note_content").val("");
										$("#note_name").val("");
									});								
							}
						},
						{
							text: "Cancel",
							click: function(){
								$("#<?php echo $index?>").dialog("destroy");
								$("#note_content").val("");
								$("#note_name").val("");
							}
						}],
						close: function() {
							$("#<?php echo $index?>").dialog("destroy");
							$("#note_content").val("");
							$("#note_name").val("");
						}
						
					});
				});
			})

		</script>
		<?php
	}

	static function show_note(){
		echo "<div id='".self::$div."' style='display: none;' title='".self::$title."'>";
		$noteform = new form( self::$addform, "index.php");
		echo $noteform->show_form();
		$field = new fields("Message Short Title", "text", "greyInput", 30, "", self::$varName);
		echo"<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo"<span class='form_field'>".$field->show_field()."</span><BR />";
		$field = new textarea("Text Message", "textarea", "greyTextAreaInput", "", "", self::$varContent, 41, 4);
		echo"<span class='form_prompt'>".$field->show_prompt()."</span>";
		echo"<span class='form_field'>".$field->show_field()."</span><BR />";
		echo "<span class='form_prompt' style='padding-left:338px;'>Characters: <em><span id='counter'></span></em></span>";
		echo $noteform->close_form();
		?>
		<script>
		$(document).ready(function()
		{
			var max_length = 160;
		 
			//run listen key press
			whenkeydown(max_length);
		});
		 
		whenkeydown = function(max_length)
		{
			$("#<?php echo self::$varContent ?>").unbind().keyup(function()
			{
				//check if the appropriate text area is being typed into
				if(document.activeElement.id === "<?php echo self::$varContent ?>")
				{
					//get the data in the field
					var text = $(this).val();
		 
					//set number of characters
					var numofchars = text.length;
		 
					if(numofchars <= max_length)
					{
						//set the length of the text into the counter span
						$("#counter").html("").html(text.length);
					}
					else
					{
						//make sure string gets trimmed to max character length
						$(this).val(text.substring(0, max_length));
					}
				}
			});
		}
		</script>
		<?php
		echo "<BR>Create a new text message to send to your patients. Use the character count as a guide only if using personalised information.";
		echo "<P>Use the following text inside your message to include personalised information.</P>";
		echo "<P>%Patient_Name% - This is the first and lastname of the patient.</P>";
		echo "<P>%Appointment_Date% - This is the appointment date.</P>";
		echo "<P>%Appointment_Time% - This is the time of the Appointment.</P>";
		echo"</div>";	
	}

	static function edit_notes() {
		echo "<div id='".self::$editdiv."' style='display: none;' title='".self::$title."'>";
		$noteform = new form( self::$editform, "index.php");
		echo $noteform->show_form();
		echo "<div class='searchHeader' style='margin-left:40px; width: 128px;'>Date</div><div class='searchHeader' style='width: 205px;'>Clinician Name</div><div class='searchHeader' style='width: 260px;'>Note</div><div class='searchHeader' style='width: 50px;'>Delete</div>";
		echo "<ul class='pagelist'>";
		$notes = dl::select(self::$table, "PatientID=".$_SESSION["Patient_No"]);
		foreach( $notes as $note ) {
			$actualNote = dl::select("messages", "idNotes = ".$note["Notes_idNotes"]);
			echo "<li class='ui-state-default'>".$actualNote[0]["date_timestamp"];
			$field = new fields("", "text", "greyInput", 30, stripslashes($actualNote[0]["ClinName"]), "note_name".$actualNote[0]["idNotes"],"");
			echo $field->show_field();
			$field = new textarea("", "textarea", "greyTextAreaInput", "", stripslashes($actualNote[0]["Notes"]), "note_content".$actualNote[0]["idNotes"], 41, 3);
			echo $field->show_field();
			$field = new checkbox("deletelist", "checkbox", "greyInput", 5, "", "managed".$note["Notes_idNotes"], "",  "No");
			echo "<span class='check_box'><span class='form_field'>".$field->show_field()."</span></span>";
			echo "</li>";
		}
		echo "</ul>";
		echo $noteform->close_form();		
		echo"</div>";	
	}

	static function show_noteLink($table){
		echo "<div id='".self::$linkholder."'>";
		/*$notes = dl::select($table, "PatientID=".$_SESSION["patient_id"]);
		$msg="";
		if( !empty($notes) ) {
			$count=0;
			foreach( $notes as $note ) {
				$count++;
			}
			$msg = "<BR>View Notes (<a href='index.php' id='".self::$editLink."'>".$count."</a>)"; 		
		}
		echo $msg;*/	
		self::show_note();
		self::edit_notes();
		echo "</div>";
	}

	static function add_notes_to($table) {
		dl::insert($table, array("m_short_title"=>$_POST["short_title"], "m_message"=>$_POST["message"]));
	}

	static function manage_notes($table, $linkId) {
		foreach( $_POST["values"] as $posted ) {
			switch( substr($posted["name"],0,7) ){
				case "note_na":
					$id = substr($posted["name"],9,strlen($posted["name"]));
					dl::update("notes", array("ClinName"=>$posted["value"]), "idNotes =".$id);
				break;
				case "note_co":
					$id = substr($posted["name"],12,strlen($posted["name"]));
					dl::update("notes", array("Notes"=>$posted["value"]), "idNotes =".$id);
				break;
				case "managed":
					$id = substr($posted["name"],7,strlen($posted["name"]));
					//now can delete the records matching the id
					dl::delete($table, $linkId." =".$id);
					dl::delete("notes", "idNotes =". $id );
				break;
			}
		}
	}
}
?>