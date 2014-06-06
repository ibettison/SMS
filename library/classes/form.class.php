<?php 
	class form {
		public $form_name;
		public $form_action;
		public $form_method;
		
		function __construct( $form_name, $form_action, $form_method="POST" ) {
			$this->form_name		= $form_name;
			$this->form_action	= $form_action;
			$this->form_method	= $form_method;
		}
		
		function show_form( ) {
			return "<form id='$this->form_name' name='$this->form_name' action='$this->form_action' method='$this->form_method'>";
		}
		
		function close_form( ) {
			return "</form>";
		}
	}
?>