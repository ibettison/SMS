<?php 
	class search {
		
		public $where_statement;
		
		function __construct( ) {
		}
		
		function age( $age_from, $age_to) {
			$yesterday = date('Y-m-d', mktime(0,0,0, date("m"), date("d")-1, date("Y")));
			$ageFrom = date("Y") - $age_from;
			$ageTo = date("Y") - $age_to;
			$startDate = $ageTo.date("-m-d", strtotime($yesterday));
			$endDate = $ageFrom.date("-m-d", strtotime($yesterday));
			$dates = array("start"=>$startDate, "end"=>$endDate);
			return($dates);
		}
		
		function create_where( $field, $value, $operand="=", $operand2="" ) {
			if( empty($this->where_statement) ) {
				$this->where_statement = " where ";
			}
			if(is_numeric($value)) {
					if(strlen($this->where_statement) > 7 ) {
						$this->where_statement .= $operand2." ";
					}
					$this->where_statement .= $field." ".$operand." ".$value." ";
			}else{
					
					if(strlen($this->where_statement) > 7 ) {
						$this->where_statement .= $operand2." ";
					}
					$this->where_statement .= $field." ".$operand." '".$value."' ";			
			}
			
		}
		
		function get_where() {
			return $this->where_statement;
		}
		
		
	}
?>