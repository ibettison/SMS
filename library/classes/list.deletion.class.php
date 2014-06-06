<?php 
/*class definition 	: This class takes a text field populated with the autocomplete jquery function 
					and on the action blur allows the deletion of the selected item in the list
written by 			: Ian Bettison
date				: 08/11/2012*/

class list_deletion {

	// variables
	static public $field_name;
	static public $field_icon;
	static public $field_delLink;
	static public $field_dialog;
	static public $field_function;
	static public $field_refresh;

	function __construct( $fieldName, $fieldIcon, $fieldDelLink, $fieldDialog, $fieldFunction, $fieldRefresh ) {
		self::$field_name 		= $fieldName;
		self::$field_icon		= $fieldIcon;
		self::$field_delLink	= $fieldDelLink;
		self::$field_dialog		= $fieldDialog;
		self::$field_function	= $fieldFunction;
		self::$field_refresh	= $fieldRefresh;
		
	}
	
	static function manage_list ( ) {	
		?>
			<script type="text/javascript">
			$(document).ready(function() {
				$("#<?php echo self::$field_name?>").click(function(event) {
					$("#<?php echo self::$field_name?>").blur(function() {
						if($("#<?php echo self::$field_name?>").val()!= "") {
							$("#<?php echo self::$field_icon?>").show();
						}else{
							$("#<?php echo self::$field_icon?>").hide();
						}
					});
				});
				$("#<?php echo self::$field_delLink?>").click(function(event) {
					$("#<?php echo self::$field_dialog?>").dialog({ 
							height: 200,
							width: 200,
							autoOpen: true,
							modal: true,
							title: 'Confirmation',						
							buttons: [{
								id: 	'btConfirm',
								text: 	'Confirm',
								click: 	function() {
								$("#<?php echo self::$field_dialog?>").dialog("destroy");
								$.post(
									"ajax.php",
									{
											func: "<?php echo self::$field_function?>",
											del: $("#<?php echo self::$field_name?>").val()
									},
									function(data)
									{
										$("#<?php echo self::$field_delLink?>").html(data);
										$("#<?php echo self::$field_name?>").val("");
										$("#<?php echo self::$field_delLink?>").delay(200).fadeOut(2000);
										$("#<?php echo self::$field_delLink?>").show();
										setTimeout(function() {
											document.location = '<?php echo self::$field_refresh?>';
										}, 2000 );
									});	
								}
							},{
								id: 		'btCancel',
								text:		'Cancel',
								click:	function() {
									$("#<?php echo self::$field_dialog?>").dialog("destroy");
								}
							}]
						});
					});
					
				});
			</script>
		<?php
	}


}
?>