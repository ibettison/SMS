 <?php
/* DataLayer.php*/
class dl {
   static public $link;  
   static public $errors = array();
   static public $debug = false;
   public $database;
 
   function __construct( ) {
   }
   
   public static function connect( $server, $uId, $pass, $db ) {
	$link = mysql_connect( $server, $uId, $pass);
	if( !$link ) {
		 self::setError("Couldn't connect to database server");
		 return false;
	}
	if( !mysql_select_db( $db, $link ) ) {
		 self::setError( "Couldn't select database: $db");
		 return false;
	}
	$database = $db;
     self::$link = $link;
     return true;  
   }
 
   function getError( ) {
     return self::$errors[count(self::errors)-1];
   }
 
   function setError( $str ) {
     array_push( self::$errors, $str );
   }
  
    function _query( $query ) {
      if ( ! self::$link ) {
        self::setError("No active db connection");
        return false;
      }
      $result = mysql_query( $query, self::$link );
      if ( ! $result )
        self::setError("error: ".mysql_error());
      return $result;
    }
  
    public static function setQuery( $query ) {
        if (! $result = self::_query( $query ) ) 
        return false;  
      return $result;
    }
  
    public static function getQuery( $query ) {
        if (! $result = self::_query( $query ) ) 
        return false;  
      $ret = array();
      while ( $row = mysql_fetch_assoc( $result ) )
        $ret[] = $row;
      return $ret;
    }
  
    function getResource( ) {
      return self::$link;
    }
  
    public static function select( $table, $condition="", $sort="" ) {
          $query = "SELECT * FROM $table";
      $query .= self::_makeWhereList( $condition );  
      if ( $sort != "" )
        $query .= " order by $sort";
      self::debug( $query );
          return self::getQuery( $query, self::$errors );
    }
  
    public static function insert( $table, $add_array ) {
          $add_array = self::_quote_vals( $add_array );
          $keys = "(".implode( array_keys( $add_array ), ", ").")";
          $values = "values (".implode( array_values( $add_array ), ", ").")";
          $query = "INSERT INTO $table $keys $values";
		  self::debug( $query );
          return self::setQuery( $query );
    }
  
    public static function update( $table, $update_array, $condition="" ) {
      $update_pairs=array();
      foreach( $update_array as $field=>$val )
        array_push( $update_pairs, "$field=".self::_quote_val( $val ) );
          $query = "UPDATE $table set ";
		 $query .= implode( ", ", $update_pairs );
		 $query .= self::_makeWhereList( $condition );  
		 self::debug( $query );
          return self::setQuery( $query );
    }
  
    public static function delete( $table, $condition="" ) {
          $query = "DELETE FROM $table";
		 $query .= self::_makeWhereList( $condition );  
		 self::debug( $query );
          return self::setQuery( $query, $error );
    }
	
   public static function debug( $msg ) {
     if ( self::$debug )
       print "$msg<br>";
   }

   static function _makeWhereList( $condition ) {
     if ( empty( $condition ) )
		 return "";
     $retstr = " WHERE ";
     if ( is_array( $condition ) ) {
       $cond_pairs=array();
       foreach( $condition as $field=>$val )
         array_push( $cond_pairs, "$field=".self::_quote_val( $val ) );
       $retstr .= implode( " and ", $cond_pairs );
     } elseif ( is_string( $condition ) && ! empty( $condition ) )
       $retstr .= $condition;
     return $retstr;
   }
 
   static function _quote_val( $val ) {
     if ( is_numeric( $val ) )
       return $val;
     return "'".addslashes($val)."'";
   }
 
   static function _quote_vals( $array ) {
     foreach( $array as $key=>$val )
       $ret[$key]=self::_quote_val( $val );
     return $ret;
   }
   
	function _exists( $table, $database ) {
   		$tables = mysql_list_tables( $database );
		while ( list($temp) = mysql_fetch_array( $tables )) {
			if( $temp == $table ) {
				return true;
			}
		}
		return false;	
   	}
   
   public static function closedb() {
   	mysql_close( self::$link );
   }	
 }
 ?>
