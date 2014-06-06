<?php
/*
tab.class.php - PHP Class to create tabbed menus to use in HTML forms
Created By: 		Ian Bettison
Date:				29th November 2011
Version:			1.0

usage: $variable = new tabs( <an array of tab info first element = `link` and 2nd element =>'tabname', <the id name for the tab div> )
The create_tabs() method returns the text to output to the browser to display the tabs.
The show_content method displays the tabs content and is passed parameters of the tab_id and a function to call to display the data inside that tab.

*/

class tabs {
	public $field_tabs;
	public $field_id;
	public $field_selected;

	function __construct( $tab_names, $tab_id, $selected=0 ) {
	
		$this->field_tabs		= $tab_names;
		$this->field_id			= $tab_id;
		$this->field_selected	= $selected;
		
		if($this->field_selected != 0) {
			
			$this->select_tab( $this->field_selected );
		}else{
			echo "<script>";
			echo "$(document).ready(function() {";
			echo "$('#".$this->field_id."').tabs()});";
			echo "</script>";
		}
	}
	
	function create_tabs( ) {
		$prt_tabs = "<ul>";
		foreach( $this->field_tabs as $tab ) {
			$prt_tabs .= "<li><a href='#".$tab["link"]."'><span>".$tab["tabname"]."</span></a></li>";
		}
		$prt_tabs .= "</ul>";
		return $prt_tabs;
	}
	
	function show_content($tabId, $function) {
		echo "<div id='".$tabId."'>";
		$function();
		echo "</div>";
	}
	
	function select_tab( $whichtab ) {
		echo "<script>";
		echo "$(document).ready(function() {";
		echo "$('#".$this->field_id."').tabs();";
		echo "$('#".$this->field_id."').tabs('select', ".$whichtab.")});";
		echo "</script>";
	}
}
?>

