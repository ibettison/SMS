<?php

class login {
	public $user_field;
	public $user_id;
	public $user_table;
	public $security_table;
	public $security_field;
	private $password;
	private $existing_password;
	public $user_dataset;
	public $security_dataset;
	public $confirmation;
	

	function __construct( $user_field, $user_id, $user_table, $security_table, $security_field, $password, $confirmation) {
		$this->user_field			= $user_field;
		$this->user_id 				= $user_id;
		$this->user_table			= $user_table;
		$this->security_table		= $security_table;
		$this->security_field		= $security_field;
		$this->password				= $password;
		$this->confirmation			= $confirmation;
		
		if( !defined('SALT') ) {
			 define( 'SALT', "AnH1xAf&-+]qtyUUOppI6672mNNbT1098114&&`^`!GHo0o0{}..>ftABB");
		}
		 //Find the user id in the security table
		
		$this->security_dataset = dl::select( $this->security_table, $this->user_field." = ".$this->user_id ) ;

		if( !empty( $this->security_dataset ) ) {
			$this->existing_password = $this->security_dataset[0][$this->security_field];
		}
	}
	
	function check_password( ) {
		 if( $this->existing_password == MD5( SALT.$this->password ) ) {
			return true;
		}else{
			return false;
		}
	}
	function get_salt() {
		return SALT;
	}
	
	function check_confirmation() {
		return $this->confirmation;
	}
	
}
?>