<?php 
	class questionnaire{
		static private $q_name;
		static private $q_status;
		static private $q_tab;
		static public $page;
		static public $rows;
		static private $sql;
		static private $template_list;
		static private $question_list;
		static private $fieldIds;
		static private $fieldPrompt;
		static private $fieldMeasures;
		static private $fieldTypeId;
		static private $fieldOptions;
				
		function __construct(  ) {
			//load the questionnaire_name
			$q = dl::select("questionnaire");
			if(!empty( $q )) {
				self::set_questionnaire_name( $q[0]["qn_title"] );
				self::set_questionnaire_status( $q[0]["qn_status"] );
				self::set_questionnaire_tab( $q[0]["qn_tablink"] );
			}
		}
		
		static public function check_status() {
			$q = dl::select("questionnaire");
			if(!empty( $q )) {
				self::set_questionnaire_name( $q[0]["qn_title"] );
				self::set_questionnaire_status( $q[0]["qn_status"] );
				self::set_questionnaire_tab( $q[0]["qn_tablink"] );
			}
		}
		
		static public function prepare_popup( $index, $button_index ) {
			?>
			<script type="text/javascript">
				$(document).ready(function() {
					$("#<?php echo $button_index?>").click(function(event) {
						$("#<?php echo $index?>").dialog({ 
							autoOpen: true,
							height: 500,
							width: 500,
							modal: true,
							close: function(ev, ui) {
								$(this).dialog("destroy");
								$("#result").html("");
							}
							
						})
						var ind =  $(this).attr('id');
						$.get(
							"ajax.php",
							{ id: ind},
							function (data)
							{
								$('#result').html(data);
						});
					});
				})

			</script>
			<?php
		}
		
		static public function prepare_delfield_button( $func, $index, $button_index, $result_div) {
			?>
			<script type="text/javascript">
				$(document).ready(function() {
					$("#<?php echo $button_index?>").click(function(event) {
						var func =  "<?php echo $func?>";
						var aChecked = new Array();
						$(".<?php echo $index?>").each(function() {
							if(this.checked) {
								aChecked.push($(this).val());
							}
						});
						$.post(
							"confirm_update.php",
							{ func: func,
								checked: aChecked
							},
							function (data)
							{
								$('#<?php echo $result_div?>').html(data);
						});
					});
				})

			</script>
			<?php
		}
		
		static public function prepare_add_button( $index, $button_index ) {
			?>
			<script type="text/javascript">
				$(document).ready(function() {
					$("#<?php echo $button_index?>").click(function(event) {
						var bContinue = true;
						if(($('#newFieldType').val() == "dropdown") || ($('#newFieldType').val() == "checkbox") || ($('#newFieldType').val() == "radio") ) {
							if( $('#fieldOptions').val() == "" ) {
								bContinue = false;
								alert("Please add options for the selection field separated by a semicolon (;) \n\r eg: 100; 200; 300 ");
							}
						}
						if( $('#questionTitle').val() !== "" ) {
							if( ($('#newQuestion').val() !== "") && bContinue) {
								var func =  "Add_Question";
								$.post(
									"confirm_update.php",
									{ func: func,
										newQuestion: $('#newQuestion').val(),
										newMeasureUnit: $('#newMeasureUnit').val(),
										newFieldType: $('#newFieldType').val(),
										fieldOptions: $('#fieldOptions').val(),
										questionTitle: $('#questionTitle').val(),
										questionDesc: $('#questionDesc').val(),
										notesRequired: $('#notesRequired').val()
									
									},
									function (data)
									{
										$('#<?php echo $index?>').html(data);
										$('#newQuestion').val("");
										$('#newMeasureUnit').val("");
										$('#newFieldType option:first').attr('selected', 'selected');
										$('#fieldOptions').val("");
								});

							}else{
								if(bContinue) {
									alert("Please enter a question prompt!");
								};
								
							};
						}else{
							alert("Please enter a Question Title");
						}
					});
				})

			</script>
			<?php
		}
		
		static public function sort_list( $index, $table, $message_div ) {
			?>
			<script type="text/javascript">
				$(document).ready(function() {
					var table = "<?php echo $table?>";
					var func = "sort";
					
					$('#<?php echo $message_div?>').fadeIn();
					$(function() { 
						$("#<?php echo $index?>").sortable({ 
							opacity: 0.6,
							update: function(event) {
							var aNames = new Array();
							$("#<?php echo $index?> :input[type=text]").each(function() {
								aNames.push($(this).attr("name"));
							});
							$.post(
									"confirm_update.php",
									{ func: func,
										table: table,
										names: aNames
									},
									function (data)
									{
										$('#<?php echo $message_div?>').html(data);
										$('#<?php echo $message_div?>').delay(200).fadeOut(2000);
										$('#<?php echo $message_div?>').show();
								});
							}
						});
					});
				})
			</script>
			<?php
		}
		
		static public function icon_button( $icon, $index ) {
		?>
			<script type="text/javascript">
				$(document).ready(function() {
					$(function() {
						$("#<?php echo $index?>").button({ 
							icons: {
								primary: "<?php echo $icon?>"
							},
							text:false
						})
					})
				})
			</script>
			<?php
		}
		
		static public function check_fieldVal($fieldVal) {
			$fields = dl::select("field_properties", "tfp_name_id ='".$fieldVal."'");
			$tempFields = dl::select("temp_field_properties", "tfp_name_id ='".$fieldVal."'");
			if(!empty($fields) or !empty($tempFields) ) {
				?>
			<script type="text/javascript">
				$(document).ready(function() {
					$("#field_name").attr('class', 'redInput');
					$("#fieldProperties").prop("disabled", true);
				});
			</script>
			<?php
			}else{
				?>
			<script type="text/javascript">
				$(document).ready(function() {
					$("#field_name").attr('class', 'greenInput');
					$("#fieldProperties").prop("disabled", false);
				});
			</script>
			<?php
			}
		}
		
		static public function confirm_dialog( $index, $text, $type="Ok") {
		?>
			<script type="text/javascript">
				$(document).ready(function() {
					$("#<?php echo $index?>").text('');
					$("#<?php echo $index?>").text('<?php echo $text?>');
					$("#<?php echo $index?>").dialog({ 
						height: 300,
						width: 300,
						autoOpen: true,
						modal: true,
						title: 'Confirmation',
						<?php if($type == "Confirm") {?>
						
						buttons: [{
							id: 		'btConfirm',
							text: 	'Confirm',
							click: 	function() {
								var update = 'Confirmed';
								$.post(
								"confirm_update.php",
								{ id: update,
								
								<?php 
								foreach( $_POST as $posted=>$values) {
									$string .=  $posted.": '".$values."', ";
								}
								$string = substr($string,0,strlen($string)-2);
								echo $string;
								?>
								
								},
								function (data)
								{
									$('#listFields').html(data);
								});
								$(this).dialog("destroy");
							}
						},{
							id: 		'btCancel',
							text:		'Cancel',
							click:	function() {
								alert("Cancelled");
								$(this).dialog("destroy");
							}
						}]
						<?php }else{?>
						title: 'Warning',
						buttons: {
							"Ok" : function() {
								$(this).dialog("destroy");
							}
						}
						<?php }?>
					})
				})
			</script>
			<?php
		}
		
		function update_status( ) {
			$fields = array( "qn_title", "qn_status", "qn_tablink");
			$values = array($_POST["qTitle"], $_POST["status"], $_POST["qTab"]);
			$writeln = array_combine($fields, $values);
			$quest_exists = dl::select("questionnaire");
			if(!empty($quest_exists)) {
				dl::update("questionnaire", $writeln);
			}else{
				dl::insert("questionnaire", $writeln);
			}
		}
		
		static public function set_questionnaire_name( $questionnaire_name ) {
			self::$q_name = $questionnaire_name;
		}
		
		static public function set_questionnaire_status( $questionnaire_status ) {
			self::$q_status = $questionnaire_status;
		}
		
		static public function set_questionnaire_tab( $questionnaire_tab ) {
			self::$q_tab = $questionnaire_tab;
		}
		
		static public function get_questionnaire_name(  ) {
			return self::$q_name;
		}
		
		static public function get_questionnaire_status(  ) {
			return self::$q_status;
		}
		
		static public function get_questionnaire_tab(  ) {
			return self::$q_tab;
		}
		
		static function load_templates(  ) {
			self::$sql 					= "select * from template_fields order by tf_prompt ASC ";
			self::$template_list 	= dl::getQuery( self::$sql );
			return self::$template_list;
			
		}
		
		function sort_questions(  ) {
			$order = $_POST["names"];
			$count = 1;
			foreach($order as $ord) {
				$id = substr($ord,8,strlen($ord));
				dl::update("question", array("q_order"=>$count), "q_id = ".$id);
				$count++;
			}
		}
		
		function sort_fields(  ) {
			$order = $_POST["names"];
			$count = 1;
			foreach($order as $ord) {
				if(substr($ord,0,11) == "fieldPrompt") {
					$id = substr($ord,11,strlen($ord));
					dl::update("temp_question_content", array("qc_order"=>$count), "qc_id = ".$id);
					$count++;
				}
			}
		}
		
		function load_questions(  ) {
		
			self::$question_list = dl::select("question", "","q_order ASC");
			
			return self::$question_list;
		
		}
		
		function load_question_fields(  ) {
			foreach( $_POST as $post=>$values ) {
				if($post=="checked"){
					foreach($values as $load){
						if(substr($load, 0, 6)  == "manage") {
							$id = substr($load,6,strlen($load));
						}
						$questions = dl::select("question", "q_id = ".$id);
						$questionContent = dl::select("question_content", "q_id = ".$id);
						?>
						<script type="text/javascript">
							$(document).ready(function() {
								$(function() {
									$('#questionTitle').val("<?php echo $questions[0]["q_title"]?>");
									$('#questionDesc').val("<?php echo $questions[0]["q_desc"]?>");
									$('#notesRequired option:contains("<?php echo $questions[0]["q_notes"]?>")').prop('selected', true);
									$('#field_message').html("Fields loaded");
									$('#field_message').delay(200).fadeOut(2000);
									$('#field_message').show(); 
								})
							})
						</script>
						<?php
						dl::insert("temp_question", array("q_order"=>$questions[0]["q_order"], "q_title"=>$questions[0]["q_title"], "q_desc"=>$questions[0]["q_desc"], "q_notes"=>$questions[0]["q_notes"]));
						//get max id from temp_question
						$max_id 					= dl::getId();
						$aQCTempFields 		= array("qc_prompt", "qc_measure_unit", "q_id", "ft_id");
						$aTempProperties 		= array("tfp_required", "tfp_size", "tfp_name_id", "tfp_range_from", "tfp_range_to", "tfp_numeric", "tfp_maxlength", "tfp_minlength", "qc_id");
						foreach($questionContent as $qc) {
							$values 				= array($qc["qc_prompt"], $qc["qc_measure_unit"], $max_id, $qc["ft_id"] );
							$writeln 				= array_combine($aQCTempFields, $values);
							dl::insert("temp_question_content", $writeln);
							//now need id to write to other temp tables
							$maxId 				= dl::getId();
							$fieldOptions 		= dl::select("field_options", "qc_id = ". $qc["qc_id"]);
							$fieldProperties 		= dl::select("field_properties", "qc_id = ". $qc["qc_id"]);
							//write details to the temporary tables
							$writeln 				= array("tfo_options"=>$fieldOptions[0]["tfo_options"], "qc_id"=>$maxId);
							dl::insert("temp_field_options", $writeln);
							$pValues 				= array($fieldProperties[0]["tfp_required"], $fieldProperties[0]["tfp_size"], $fieldProperties[0]["tfp_name_id"], $fieldProperties[0]["tfp_range_from"], $fieldProperties[0]["tfp_range_to"], $fieldProperties[0]["tfp_numeric"], $fieldProperties[0]["tfp_maxlength"], $fieldProperties[0]["tfp_minlength"], $maxId);
							$writeln 				= array_combine($aTempProperties, $pValues);
							dl::insert("temp_field_properties", $writeln);
						}
					}
				}
			}
		}
		
		function display_templates (  ) {
		
		}
		
				
		function new_question(  ) {
			$fields 			= array("q_desc", "q_title", "q_notes");
			$posted 		=array($_POST["questionDesc"], $_POST["questionTitle"], $_POST["notesRequired"]);
			$writeln 		= array_combine($fields, $posted);
			$question 		= dl::select("temp_question");
			
			if(empty($question)) { //determine if the temporary question record exists or not
				dl::insert("temp_question", $writeln);
				//get max id from temp_question
				$max_id 	= dl::getId();
			}else{
				dl::update("temp_question", $writeln, "q_id = ".$question[0]["q_id"]);
				$max_id 	= $question[0]["q_id"];
			}
			
			$fieldTypeID 	= dl::select("field_types", "ft_description = '".$_POST["newFieldType"]."'");
			$fields 			= array("qc_prompt", "qc_measure_unit", "q_id", "ft_id");
			$posted 		= array($_POST["newQuestion"], $_POST["newMeasureUnit"], $max_id, $fieldTypeID[0]["ft_id"]);
			$writeln 		= array_combine($fields, $posted);
			dl::insert("temp_question_content", $writeln);
			//get max id from temp_question
				$max_id 	= dl::getId();
				$options 	= dl::select("temp_field_options", "qc_id =".$max_id);
			if(empty($options)) {
				dl::insert("temp_field_options", array("tfo_options"=>$_POST["fieldOptions"], "qc_id"=>$max_id));
			}
			?>
			<script type="text/javascript">
				$(document).ready(function() {
					$(function() {
						$('#field_message').html("Field Added");
						$('#field_message').delay(200).fadeOut(2000);
						$('#field_message').show();
					})
				})
			</script>
			<?php
		}
		
		function del_field( ) {
			foreach( $_POST as $post=>$values ) {
				if($post=="checked"){
					foreach($values as $delete){
						if(substr($delete, 0, 6)  == "manage") {
							$del_id = substr($delete,6,strlen($delete));
						}
						dl::delete("temp_field_properties", "qc_id = ".$del_id);
						dl::delete("temp_question_content", "qc_id = ".$del_id);
						dl::delete("temp_field_options", "qc_id = ".$del_id);
						?>
						<script type="text/javascript">
							$(document).ready(function() {
								$(function() {
									$('#field_message').html("Field Deleted");
									$('#field_message').delay(200).fadeOut(2000);
									$('#field_message').show();
								})
							})
						</script>
					<?php
					}
				}
			}
			$checkContent = dl::select("temp_question_content");
			if(empty($checkContent)) {
				dl::delete("temp_question");
				?>
			<script type="text/javascript">
				$(document).ready(function() {
					$(function() {
						$('#questionTitle').val("");
						$('#questionDesc').val("");
						$('#notesRequired option:first').attr('selected', 'selected');
						$('#field_message').html("Field Deleted");
						$('#field_message').delay(200).fadeOut(2000);
						$('#field_message').show();
					})
				})
			</script>
			<?php
			}
		}
		
		function del_question( ) {
			foreach( $_POST as $post=>$values ) {
				if($post=="checked"){
					foreach($values as $delete){
						if(substr($delete, 0, 6)  == "manage") {
							$del_id = substr($delete,6,strlen($delete));
						}
						$contentId = dl::select("question_content", "q_id = ".$del_id);
						dl::delete("question", "q_id =".$del_id);
						dl::delete("field_properties", "qc_id = ".$contentId[0]["qc_id"]);
						dl::delete("field_options", "qc_id = ".$contentId[0]["qc_id"]);
						dl::delete("question_content", "q_id = ".$del_id);
					}
				}
			}
			?>
			<script type="text/javascript">
				$(document).ready(function() {
					$(function() {
						$('#quest_message').html("Question Deleted");
						$('#quest_message').delay(200).fadeOut(2000);
						$('#quest_message').show();
					})
				})
			</script>
			<?php
		}
		
		function del_template( ) {
			foreach( $_POST as $post=>$values ) {
				if($post=="checked"){
					foreach($values as $delete){
						if(substr($delete, 0, 8)  == "template") {
							$del_id = substr($delete,8,strlen($delete));
						}
						dl::delete("template_field_properties", "tf_id = ".$del_id);
						dl::delete("field_options", "tf_id = ".$del_id);
						dl::delete("template_fields", "tf_id = ".$del_id);
					}
				}
			}
			?>
			<script type="text/javascript">
				$(document).ready(function() {
					$(function() {
						$('#template_message').html("Template(s) Deleted");
						$('#template_message').delay(200).fadeOut(2000);
						$('#template_message').show();
					})
				})
			</script>
			<?php
		}
		
		function process_question(  ) {
			$check = true;
			foreach( $_POST as $posted=>$values ) {
				if(substr($posted,0,11) == "fieldPrompt" ) {
					$id = substr($posted,11,strlen($posted));
					//update the temporary records just in case the fields have been edited
					$type = dl::select("field_types", "ft_description= '".$_POST["fieldType".$id]."'");
					$fields = array("qc_prompt", "qc_measure_unit", "ft_id");
					$values = array($_POST["fieldPrompt".$id], $_POST["measure".$id], $type[0]["ft_id"]);
					$writeln = array_combine($fields, $values);
					dl::update("temp_question_content", $writeln, "qc_id =".$id);
					dl::update("temp_field_options", array("tfo_options"=>$_POST["options".$id]), "qc_id = ".$id);
				}
				
				if(is_numeric($id)) {
					
					$properties = dl::select("temp_field_properties", "qc_id= ".$id);
					if(empty($properties)) {
						$check = false;
					}else{
					//check to see if the name_id is not empty. This is a purposefull check after a template addition to the question
						foreach($properties as $props) {
							if(empty($properties[0]["tfp_name_id"]) ) {
								$check = false;
							}
						}
					}
				}
			}
			if($check== false) {
				self::confirm_dialog("confirm_dialog", "Please ensure all fields have their properties defined.");
			
				return;
			}else{
				//all properties have been saved so lets go and save the question and the template if selected
				if($_POST["savetemplate"] == "savetemplate") {
					//need to check to see if anyfields where checked to be saved to the template
					$selMan= false;
					foreach( $_POST as $posted=>$values) {
						if( substr($posted,0, 11) == "manageField" ) {
							$selMan = true;
						}
					}
					if(!$selMan) {
						self::confirm_dialog("confirm_dialog", "There were no questions selected to save to the template. Please select the questions you would like to save as a template and try again.");
						return;
					}else{
						//save to the template
						self::save_templates();
					}
				}
				//save to the question tables from the temporary tables
				self::save_questions();
			}
		
		}
		function save_questions( ){
		
			$question = dl::select("question", "q_title = '".$_POST["questionTitle"]."'");
			if(!empty($question)) {
				self::confirm_dialog("confirm_dialog", "The question already exists do you want to overwrite it?", "Confirm");
			}else{
				self::update_questions();
			}		
		}
		
		static private function save_templates() {
			foreach( $_POST as $posted=>$values ) {
				if( substr($posted,0,11) == "manageField" ) {
					$id = substr($values,6,strlen($values));
					dl::insert("template_fields", array("tf_prompt"=>""));
					//get last added id
					$last_id				= dl::getId();
					//now can get the field properties
					$tProp 				= dl::select("temp_field_properties", "qc_id = ".$id);
					if( !empty($tProp) ) {
						$fields 			= array("tfp_required", "tfp_size", "tfp_name_id", "tfp_range_from", "tfp_range_to", "tfp_numeric", "tfp_maxlength", "tfp_minlength", "tf_id");
						$values 		= array($tProp[0]["tfp_required"], $tProp[0]["tfp_size"], $tProp[0]["tfp_name_id"], $tProp[0]["tfp_range_from"], $tProp[0]["tfp_range_to"], $tProp[0]["tfp_numeric"], $tProp[0]["tfp_maxlength"], $tProp[0]["tfp_minlength"], $last_id);
						$writeln 		= array_combine($fields,$values);
						dl::insert("template_field_properties", $writeln);
					}
					
				}
				switch( substr($posted,0,7) ) {
					case "fieldPr":
						if(substr($posted,11, strlen($posted)) == $id) {
							dl::update("template_fields", array("tf_prompt"=>$values), "tf_id = ". $last_id);
						}
					break;
					case "measure":
						if(substr($posted,7, strlen($posted)) == $id) {
							dl::update("template_fields", array("tf_measure_unit"=>$values), "tf_id = ". $last_id);
						}
					break;
					case "fieldTy":
						if(substr($posted,9, strlen($posted)) == $id) {
							//get the field_types Id
							$ft = dl::select("field_types", "ft_description = '".$values."'");
							dl::update("template_fields", array("ft_id"=>$ft[0]["ft_id"]), "tf_id = ". $last_id);
						}
					break;
					case "options":
						if(substr($posted,7, strlen($posted)) == $id) {
							dl::insert("template_field_options", array("tfo_options"=>$values, "tf_id"=>$last_id));
						}
					break;
				}
			}
		}
		
		static public function new_template() {
			foreach($_POST["checked"] as $posted) {
				if(substr($posted,0,8) == "template") {
					$id 					= substr($posted,8,strlen($posted));
				}
				$question 				= dl::select("template_fields", "tf_id = ".$id);
				if(!empty($question)) {
					//need to check if there is a question already loaded: if not create a new question so...
					//need to get the current id of the current temp_question if any
					$temp_question = dl::select("temp_question");
					if(!empty($temp_question)) {
						$temp_id 		= $temp_question[0]["q_id"];
					}else{
						//not found a question so lets create a temporary question and then get the id
						dl::insert("temp_question", array("q_title"=>"Temporary Title", "q_desc"=>"Temporary Description", "q_notes"=>"No"));
						$max			= dl::setQuery("select MAX(q_id) as maxId from temp_question");
						$temp_id		= $max[0]["maxId"];
						//now populate the fields with the temporary content
						?>
						<script type="text/javascript">
							$(document).ready(function() {
									$(function() {
									$('#questionTitle').val("Temporary Title");
									$('#questionDesc').val("Temporary Description");
									$('#notesRequired option:contains("No")').prop('selected', true);
								})
							})
						</script>
						<?php
					}
					$fields				= array("qc_prompt", "qc_measure_unit","q_id","ft_id");
					$values				= array($question[0]["tf_prompt"], $question[0]["tf_measure_unit"], $temp_id, $question[0]["ft_id"]);
					$writeln				= array_combine($fields, $values);
					dl::insert("temp_question_content", $writeln);
					//get the id from the question_content table
					$last_id				= dl::getId();
					$props 				= dl::select("template_field_properties", "tf_id=".$id);
					$fields 				= array("tfp_required", "tfp_size", "tfp_name_id", "tfp_range_from", "tfp_range_to", "tfp_numeric", "tfp_maxlength", "tfp_minlength", "qc_id");
					//need to leave out the name_id so as not to have duplicate name_id.
					$values 			= array($props[0]["tfp_required"], $props[0]["tfp_size"], "", $props[0]["tfp_range_from"], $props[0]["tfp_range_to"], $props[0]["tfp_numeric"], $props[0]["tfp_maxlength"], $props[0]["tfp_minlength"], $last_id);
					$writeln 			= array_combine($fields,$values);
					dl::insert("temp_field_properties", $writeln);
					$options = dl::select("template_field_options", "tf_id =".$id);
					dl::insert("temp_field_options", array("tfo_options"=>$options[0]["tfo_options"], "qc_id"=>$last_id));
					?>
						<script type="text/javascript">
							$(document).ready(function() {
									$(function() {
									$('#field_message').html("Template Field Added");
									$('#field_message').delay(200).fadeOut(2000);
									$('#field_message').show();
								})
							})
						</script>
						<?php
				}
			}
			
		}
		
		static public function setValues($id, $tempId) {
			
			//get the temp_question_content Id so as to connect to the temp_field_properties and copy the details into field_properties.
			$content_id 				= dl::select("temp_question_content", "q_id = ".$tempId, "qc_order");
			$opts 						= dl::select("question_content", "q_id=".$id);
			//delete any existing records as this could be an update
			foreach($opts as $opt) {
				dl::delete("field_options", "qc_id = ".$opt["qc_id"]);
				dl::delete("field_properties", "qc_id = ".$opt["qc_id"]);
			}
			dl::delete("question_content", "q_id = ".$id);
			
			$optionFields 			= array("tfo_options", "qc_id");
			foreach( $content_id as $cId ) {
			
				//create question_content records
				$fields 					= array("qc_prompt", "qc_measure_unit", "q_id", "ft_id");
				$values 				= array($cId["qc_prompt"], $cId["qc_measure_unit"], $id, $cId["ft_id"]);
				$write 					= array_combine($fields, $values);
				dl::insert("question_content", $write);
				
				//get the id from the question_content table
				$last_id					= dl::getId();
				
				//find the related properties for the question content record and copy it into the field_properties table
				$temp_properties 	= dl::select("temp_field_properties", "qc_id = ".$cId["qc_id"]);
				$fields 					= array("tfp_required", "tfp_size", "tfp_name_id", "tfp_range_from", "tfp_range_to", "tfp_numeric", "tfp_maxlength", "tfp_minlength", "qc_id");
				$values 				= array($temp_properties[0]["tfp_required"], $temp_properties[0]["tfp_size"],$temp_properties[0]["tfp_name_id"], $temp_properties[0]["tfp_range_from"], $temp_properties[0]["tfp_range_to"], $temp_properties[0]["tfp_numeric"], $temp_properties[0]["tfp_maxlength"],$temp_properties[0]["tfp_minlength"], $last_id);
				$writeln 				= array_combine($fields, $values);
				dl::insert("field_properties", $writeln);
				
				//find the options from temp_field_options and copy to field_options
				$options 				= dl::select("temp_field_options", "qc_id = ".$cId["qc_id"]);
				if(!empty($options) ) {
					$values 			= array($options[0]["tfo_options"], $max[0]["maxId"]);
					$write 				= array_combine($optionFields, $values);
					dl::insert("field_options", $write);
				}
			}
			//delete all of the existing records for the id.
			dl::delete("temp_question_content");
			dl::delete("temp_question");
			dl::delete("temp_field_properties");
			dl::delete("temp_field_options");
		}
		
		static public function update_questions() {
			//get the q-id from temp_question
			$tempQuestion 		= dl::select("temp_question");
			$tq_id 					= $tempQuestion[0]["q_id"];
			$question_fields 	= array("q_title","q_desc","q_notes");
			$question_values 	= array($_POST["questionTitle"],$_POST["questionDesc"], $_POST["notesRequired"]);
			$question_write 	= array_combine($question_fields, $question_values);
			$found 					= dl::select("question", "q_title='".$_POST["questionTitle"]."'");
			$contentFields 		= array("qc_prompt", "qc_measure_unit", "q_id", "ft_id");
			$optionFields			= array("tfo_options", "qc_id");
			$proprtiesFields		= array("tfp_required", "tfp_size", "tfp_name_id", "tfp_range_from", "tfp_range_to", "tfp_numeric", "tfp_maxlength", "tfp_minlength");
			if(empty($found)) {
				dl::insert("question", $question_write);
				//get last added id
				$id 					= dl::getId(); 
				self::setValues($id, $tq_id);
			}else{
				dl::update("question", $question_write, "q_id=".$found[0]["q_id"] );
				$id=$found[0]["q_id"];
				self::setValues($id, $tq_id);
			}
			//delete all of the existing records for the id.
			dl::delete("temp_question_content");
			dl::delete("temp_question");
			dl::delete("temp_field_properties");
			dl::delete("temp_field_options");
			?>
			<script type="text/javascript">
				$(document).ready(function() {
					$(function() {
						$('#questionTitle').val("");
						$('#questionDesc').val("");
						$('#notesRequired option:first').attr('selected', 'selected');
						$('#quest_message').html("Question Added");
						$('#quest_message').delay(200).fadeOut(2000);
						$('#quest_message').show();
					})
				})
			</script>
			<?php
		}
		
		function save_properties(  ) {
			$fields 			= array("tfp_required", "tfp_size", "tfp_name_id","tfp_range_from","tfp_range_to","tfp_numeric","tfp_maxlength","tfp_minlength", "qc_id" );
			$posted 		=array(self::binary($_POST["field_required"]), $_POST["field_size"], $_POST["field_name"],$_POST["field_range_from"], $_POST["field_range_to"], self::binary($_POST["field_numeric"]),$_POST["field_maxlength"], $_POST["field_minlength"], $_GET["id"]);
			$writeln 		= array_combine($fields, $posted);
			$properties 	= dl::select("temp_field_properties", "qc_id = ".$_GET["id"]);
			if(empty($properties)) {
				dl::insert("temp_field_properties", $writeln);
			}else{
				dl::update("temp_field_properties", $writeln, "qc_id = ".$_GET["id"]);
			}
		}
		
		static private function binary( $response ) {
			if($response == "Yes" ){
				return 1;
			}else{
				return 0;
			}
		}
		
	}
?>