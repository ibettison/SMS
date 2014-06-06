<?php
class permission {
	public $user_id;
	public $user_field;
	public $user_type_table;
	public $user_type_field;
	public $permission_table;
	public $permission_field;
	public $permission_values = array();
	
	function __construct( $user_type_table, $user_type_field, $user_id, $user_field, $permission_field ) {
		$this->user_type_table		= $user_type_table;
		$this->user_type_field		= $user_type_field;
		$this->permission_field		= $permission_field;
		$this->user_id				= $user_id;
		$this->user_field			= $user_field;

		$usertype = dl::select( $this->user_type_table, $this->user_field."=".$this->user_id );
		if( ! empty( $usertype ) ) {
			$this->usertype_id = $usertype[0][$this->user_type_field];	
		}
	}
	
	function get_permissions( $permission_table, $permissions_list, $permission_name_field, $permission_value_field ) {
		if( ! empty($this->usertype_id ) ) {
			$permissions = dl::select( $permission_table, $this->user_type_field."=".$this->usertype_id );
			if( ! empty( $permissions ) ) {
				foreach($permissions as $permission) {
					$names = dl::select( $permissions_list, $this->permission_field."=".$permission[$this->permission_field] );
					$this->permission_values[]=array("permission_name"=>$names[0][$permission_name_field], "permission_value"=>$permission[$permission_value_field]);
				}
			}
			return $this->permission_values;
		}else{
			return false;
		}
	}
}

?>