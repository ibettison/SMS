<?php 
	class validation {
		
		static public $valid_formname;
		static public $valid_rules;
		static public $valid_element;
		static public $element_count;
		
		function __construct( ) {
		}
		
		static function validate_form($formname) {
			
			self::$valid_formname	= $formname;
			?>
			<script>
			jQuery.fn.stopbind_<?php echo self::$valid_formname?>=function(){
					return $("#<?php echo self::$valid_formname?>").unbind("submit");
			};
			$(document).ready(function(){
				$("#<?php echo self::$valid_formname?>").validate();
			});
			</script>
			<?php
		}
		
		static function add_rules( $element, $rule_array) {
			$counter = 1;
			self::$valid_element 		= $element;
			self::$valid_rules			= $rule_array;			
			self::$element_count 		= count(self::$valid_rules);
			?>
			<script>
			$(document).ready(function(){
				$("#<?php echo self::$valid_element?>").rules("add", {
				<?php
				foreach( self::$valid_rules as $rule ){
					echo $rule["type"].": ".$rule["value"];
					if( $counter < self::$element_count ) {
						echo ", ";
						$counter++;
					}
					echo "\r";
				}
				?>
				});
			});
			</script>
			<?php
			
			return true;
		}
		static function assign_click( $element ) {
			?>
			<script>
				$("#<?php echo $element ?>").click(function ()
				{
					$(this).stopbind_<?php echo self::$valid_formname?>();
				});
			</script>
			<?php
		}
	}
?>