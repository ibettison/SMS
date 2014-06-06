<?php
// validation rules
	$rule_required 						= array(array("type"=>"required","value"=>"true"));
	$rule_date_required 				= array(array( "type"=>"required", "value"=>"true" ), array( "type"=>"dateISO", "value"=>"true" ) );
	$rule_email_required 				= array(array( "type"=>"required", "value"=>"true" ), array( "type"=>"email", "value"=>"true" ) );
	$rule_minlength6_required 			= array(array( "type"=>"required", "value"=>"true" ), array( "type"=>"minlength", "value"=>6) );
	$rule_pcr_value_required 			= array(array("type"=>"required", "value"=>"true"), array("type"=>"range", "value"=>"[0.02 - 300]") );
	$rule_digits 						= array(array("type"=>"digits", "value"=>"true"));
	$rule_number						= array(array("type"=>"number", "value"=>"true"));
	$rule_number_required				= array(array("type"=>"required","value"=>"true"), array("type"=>"number", "value"=>"true"));
	$rule_length2 						= array(array("type"=>"maxlength", "value"=>2), array("type"=>"minlength", "value"=>2));
	$rule_digits_maxlength10_required 	= array(array("type"=>"required", "value"=>"true"), array("type"=>"digits", "value"=>"true") , array("type"=>"maxlength", "value"=>10), array("type"=>"minlength", "value"=>10) );
	$rule_required_dependant 			= array(array("type"=>"required", "value"=>"something"));
?>