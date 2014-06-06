<?php
/*
fields.class.php - PHP Class to create fields to use in HTML forms
Created By: 		Ian Bettison
Date:				2nd November 2011
Version:			1.0

usage: $variable = new fields( <the field prompt>, <the field type "text"|"password"|"hidden"|"submit">, <the class linked by the CSS for displaying the prompt>, <field Length>, <the value to show in the field>, <the name of the field>, <the title of the field> )
The show_prompt() method returns the prompt allowing the programmer to specify surrounding styles.
The show_field method returns the field but applies the styles contained within the $field_class parameter
The setTabIndex method allows the user to set the Tab Index for the fields

*/
class fields {
	public $field_prompt;
	public $field_type;
	public $field_class;
	public $field_length;
	public $field_value;
	public $field_name;
	public $field_validation;
	public $field_info;
	public $field_index;
	
	function __construct( $field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name) {
	
		$this->field_prompt			= $field_prompt;
		$this->field_type 			= $field_type;
		$this->field_class			= $field_class;
		$this->field_length			= $field_length;
		$this->field_value			= $field_value;
		$this->field_name			= $field_name;

		if($this->field_type != "submit" and $this->field_type != "textarea") {
			?>
			<script language="JavaScript">
			$(document).ready(function() {
				$("#<?php echo $this->field_name?>").focusin( function() {
				$("#<?php echo $this->field_name?>").addClass("inputselected");
				});
					$("#<?php echo $this->field_name?>").focusout( function() {
					$("#<?php echo $this->field_name?>").removeClass("inputselected").addClass("<?php echo $this->field_class ?>");
				});
			});
			</script>
			<?php
		}

	}
	
	function setTabIndex($index) {
		$this->field_index = $index;
	}
	
	function show_prompt( ) {
		 return $this->field_prompt;
	}
	
	function show_field( ) {
		 return $this->create_field( $this->field_type );
	}
	
	function create_field($type) {
	
		$prt = "<input id=\"".$this->field_name."\" class=\"".$this->field_class."\"  type=\"".$type."\" size=\"".$this->field_length."\" name=\"".$this->field_name."\" value=\"".$this->field_value."\" tabindex=\"".$this->field_index."\"  />";
		return $prt;
	}
}

class readOnlyField extends fields {
	function create_field($type) {
	$prt = "<input id=\"".$this->field_name."\" class=\"".$this->field_class."\"  type=\"".$type."\" size=\"".$this->field_length."\" name=\"".$this->field_name."\" readonly= \"readonly\" value=\"".$this->field_value."\" tabindex=\"".$this->field_index."\"  />";
	return $prt;
	}
}

/*
Radio Class

usage: $variable = new radio( <the field prompt>, <the field type "radio">, <the class linked by the CSS for displaying the prompt>, <field Length>, <the value to show in the field>, <the name of the field> )
The show_prompt() method returns the prompt allowing the programmer to specify surrounding styles.
The show_field method returns the field but applies the styles contained within the $field_class parameter

*/


class radio extends fields {
	public $text_class;
	public $field_selected;
	public $field_list;
	public $field_orientation;
	public $layout_class;
	
	function __construct( $field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name, $text_class, $layout_class, $field_list, $field_selected, $field_orientation ) {
		 parent::__construct ($field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name);
		 $this->field_selected			= $field_selected;
		 $this->field_list				= $field_list;
		 $this->field_orientation 		= $field_orientation;
		 $this->text_class				= $text_class;
		 $this->layout_class			= $layout_class;
		
	}
	
	function create_field($type) {
		 $prt = "";
		 foreach($this->field_list as $list) {
			if($this->field_selected == $list) {
				 $prt .= "<input id='".$this->field_name."' class='".$this->field_class."' type='$type' name='$this->field_name' value='$list' checked tabindex='".$this->field_index."' /> <span class='".$this->text_class."'>".$list."</span>"; 
			}else{
				 $prt .= "<input id='".$this->field_name."' class='".$this->field_class."' type='$type' name='$this->field_name' value='$list' tabindex='".$this->field_index."' /> <span class='".$this->text_class."'>".$list."</span>";
			}
			if( $this->field_orientation == "Horizontal" ) {
				 $prt .= "&nbsp;";
			}else{ 
				 $prt .= "<br />&nbsp;<span class='".$this->layout_class."'> </span>";
			}
		}
		return $prt;
	}
}

class checkbox extends fields {
	public $field_selected;
	public $field_orientation;
	
	function __construct( $field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name, $text_class, $field_selected="No" ) {
		 parent::__construct ($field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name );
		 $this->field_selected		= $field_selected;
		 $this->text_class		= $text_class;
	}
	
	function create_field($type) {
		$prt 		= "";
		if($this->field_selected == "Yes") {
			 $prt .= "<input id='".$this->field_name."' class='".$this->field_class."' type='".$type."' name='".$this->field_name."' value='".$this->field_value."' checked tabindex='".$this->field_index."' /> <span class='".$this->text_class."'></span>";
		}else{
			 $prt .= "<input id='".$this->field_name."' class='".$this->field_class."' type='".$type."' name='".$this->field_name."' value='".$this->field_value."' tabindex='".$this->field_index."' /> <span class='".$this->text_class."'></span>";
		}
		return $prt;
	}
}

class selection extends fields {
	public $field_selected;
	public $field_list;
	public $field_show;

	
	function __construct( $field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name, $field_list, $field_selected, $field_show ) {
		 parent::__construct ($field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name );
		 $this->field_selected		= $field_selected;
		 $this->field_list			= $field_list;
		 $this->field_show 			= $field_show;
	}

	function create_field($type) {
		//create comma delimited list from array
		echo "<input id=\"".$this->field_name."\" class=\"".$this->field_class."\"  type=\"".$type."\" size=\"".$this->field_length."\" name=\"".$this->field_name."\" value=\"".$this->field_selected."\" tabindex=\"".$this->field_index."\"  />";
		$list="";
		foreach($this->field_list as $arrList){
			$list .= "\"".$arrList."\", ";
		}
		$list = substr($list,0, strlen($list)-2);
		if($this->field_show == "0") {
			?>
			<script language="JavaScript">		
			$(document).ready(function() {			
				$("#<?php echo $this->field_name ?>").autocomplete({
					source: [<?php echo $list?>],
					minLength: 0 
				}).focus(function() {
					$("#<?php echo $this->field_name ?>").autocomplete("search", "");
				});
			});
			</script>
			<?php
		}else{
			?>
			<script language="JavaScript">		
			$(document).ready(function() {			
				$("#<?php echo $this->field_name ?>").autocomplete({
					source: [<?php echo $list?>]
				});
			});
			</script>
			<?php
		}
		
		/*$prt 		= "<select class='$this->field_class' id='".$this->field_name."' name='".$this->field_name."' tabindex='".$this->field_index."' >";
		 foreach($this->field_list as $list) {
			if($this->field_selected == $list) {
				 $prt .= "<option value='".$list."' selected>".$list."</option>";
			}else{
				 $prt .= "<option value='".$list."'>".$list."</option>";
			}
		}
		$prt .= "</select>";
		return $prt;*/
		
	}
}

class multiselection extends selection {

	function create_field($type) {
		$prt 		= "<select class='$this->field_class' id='".$this->field_name."' name='".$this->field_name."' tabindex='".$this->field_index."' multiple>";
		 foreach($this->field_list as $list) {
			if($this->field_selected == $list) {
				 $prt .= "<option value='".$list."' selected>".$list."</option>";
			}else{
				 $prt .= "<option value='".$list."'>".$list."</option>";
			}
		}
		$prt .= "</select>";
		return $prt;
	}
}

class textarea extends fields{
	public $field_cols;
	public $field_rows;
	
	function __construct( $field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name, $field_cols, $field_rows ) {
		 parent::__construct ($field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name );
		 $this->field_cols		= $field_cols;
		 $this->field_rows		= $field_rows;
		 $this->create_area($this->field_name);
	}
	
	function create_area($id) {
	?>
		<script language="JavaScript">
		$(document).ready(function() {
			$("#<?php echo $this->field_name?>").focusin( function() {
			$("#<?php echo $this->field_name?>").addClass("textAreaSelected");
			});
				$("#<?php echo $this->field_name?>").focusout( function() {
				$("#<?php echo $this->field_name?>").removeClass("textAreaSelected").addClass("<?php echo $this->field_class ?>");
			});
		});
		
		/*$(document).ready(function() {			
			$("#<?php echo $id ?>").autoResize();
				maxHeight: 200,
				minHeight: 100
		});*/
		</script>
		<?php
	}
	
	function create_field($type) {
		return "<textarea  id='$this->field_name' class='$this->field_class' name='$this->field_name'  rows='$this->field_rows' cols='$this->field_cols' tabindex='$this->field_index' >$this->field_value</textarea>";
	}
}

class dates extends fields {
	public $field_id;
	public $field_anim;
	public $field_image;
	public $field_year;
	
	function __construct( $field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name, $field_id, $field_year="false", $field_animation="blind", $field_image="./library/images/date_picker.gif" ) {
		 parent::__construct ($field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name);
		 $this->field_id		= $field_id;
		 $this->field_anim		= $field_animation;
		 $this->field_image		= $field_image;
		 $this->field_year		= $field_year;
		 $this->create_date($this->field_id);
	}
	
	function create_date($id) {
	?>
	<style>
	.ui-widget { font-family: Arial, Helvetica, Verdana, sans-serif; font-size: 12px; }
	</style>
		<script language="JavaScript">
							
			$(document).ready(function() {
				
				
				$("#<?php echo $id?>").datepicker( {
				dateFormat: 		'yy-mm-dd', 
				showAnim: 			'<?php echo $this->field_anim?>', 
				buttonImage: 		'<?php echo $this->field_image?>', 
				buttonImageOnly: 	true, 
				showButtonPanel: 	true,
				changeYear: 		<?php echo $this->field_year?>,
				yearRange:			'1900:<?php echo date('Y')?>',
				showOn: 			'both'
				});
				
			});
			
		</script>
<?php
	}
	
	function create_field($type) {
		return "<input id='".$this->field_name."' class='".$this->field_class."' type='text' id='".$this->field_id."' size= '".$this->field_length."', name='".$this->field_name."' value='".$this->field_value."' tabindex='".$this->field_index."'>";
	}
	
}

class timepicker extends fields {
	
	function __construct( $field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name ) {
		 parent::__construct ($field_prompt, $field_type, $field_class, $field_length, $field_value, $field_name);
		 $this->create_time($this->field_name);
	}
	
	function create_time($id) {
	?>
		<script language="JavaScript">
							
			$(document).ready(function() {
				$("#<?php echo $id?>").ptTimeSelect({
					popupImage: "<img src='./library/images/time.png' border='0' />"
				});
			});
			
		</script>
<?php
	}
	
	function create_field($type) {
		return "<input id='".$this->field_name."' class='".$this->field_class."' type='text' id='".$this->field_name."' size= '".$this->field_length."', name='".$this->field_name."' value='".$this->field_value."' tabindex='".$this->field_index."'>";
	}
	
}

class label {
	public $field_label;
	
	function __construct( $field_label ) {
		$this->field_label 		= $field_label;
	}
	
	function get_label( ) {
		 return $this->field_label;
	}
}
?>