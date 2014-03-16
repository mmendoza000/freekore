<?php
/**
 * FreeKore Php Framework
 * Version: 0.3.1
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2010 Freekore PHP Team
 * @license    GNU GPL License
 */
/*************************************************************
 **  Programa:  db_firebird.php
 **  Descripcion: Funciones de base de datos adaptador FirebirdInterbase
 **  fecha      por         descripcion
 **  ---------  ----------- ----------------
 **  02/03/12   mmendoza    Creado.
 **************************************************************/

/**
 *@package db_firebird
 *@desc  firebird/interbase database interactions
 *@since v0.3.1
 * */

class db_firebird implements db_interface{

	public $resource;
	public static $host = HOST;
	public static $user = USER;
	public static $pass = PASSWORD;
	public static $database = SYSTEM_DB;
	public  $sql_query='';
	public  $primary_key_id = array();
	private $query_is_assoc = false;
	public static $is_connected = false;

	// SQL STRING VARS
	private $sql_select = '*';
	private $sql_select_distinct = '';
	private $sql_table = '';
	private $sql_where = '';
	private $sql_and = array();
	private $sql_group_by = '';
	private $sql_order_by = array();
	private $sql_limit = '';

	private $arr_handled_errors = array(
			'1062'=>'ER_DUP_ENTRY',
			'1451'=>'ER_ROW_IS_REFERENCED_2',
			'1452'=>'ER_NO_REFERENCED_ROW_2');

	public $error_code = '';


	/**
	 *@package db_firebird
	 *@method connect()
	 *@desc Open a connection to a Firebird/Interbase DB
	 *@since v0.3.1
	 * */
	public static function connect($p_host = NULL,$p_user = NULL,$p_pass = NULL,$p_db = NULL) {

		$H = isset($p_host)? $p_host : self::$host;
		$U = isset($p_user)? $p_user : self::$user;
		$P = isset($p_pass)? $p_pass : self::$pass;
		$D = isset($p_db)  ? $p_db   : self::$database;


		$connection = @ibase_connect($H.':'.$D, $U, $P);

		if($connection==FALSE){

			try{
				throw new FkException("Error al conectar a la db ");
			}catch(FkException $e){
				$e->description='Firebird/Interbase Respondi&oacute;:'. ibase_errmsg().'</b>';
				$e->solution='Verifique la conexion, posiblemente el archivo /app/config/environment.ini no contiene los datos de conexion correctos. Vea ejemplo:';
				$e->solution_code= fk_str_format('[development]
db_host = localhost
db_username = tester
db_password = test
db_name = freekore_dev
db_type = firebird','html');
				$e-> error_code = 'DB000002';
				$e->show('code_help');

			}
		}else{
			self::$is_connected = true;
		}
			
	}


	/**
	 *@package db_firebird
	 *@method close()
	 *@desc Close Firebird/Interbase connection
	 *@since v0.3.1
	 * */
	public function close() {
		if(self::$is_connected){
			ibase_close();
			self::$is_connected = false;
		}
	}
	/**
	 *@package db_firebird
	 *@method query()
	 *@desc Send a Firebird/Interbase query
	 *@since v0.3.1
	 *@return bool & Populates $this->resource
	 * */
	public function query($query){

		$this->sql_query = $query ;

		if($this->resource = ibase_query($query)){
			return TRUE;
		}else{
			// is hanled error
			$error_no = ibase_errcode();

			$is_handed = false;

			if(array_key_exists($error_no, $this->arr_handled_errors)){
				$is_handed = true;
			}

			if($is_handed==true){
				$this->error_code = $this->arr_handled_errors[$error_no];
				return FALSE;
			}else{
				// if uknown error
				try{
					throw new FkException("Firebird/Interbase Error");
				}catch(FkException $e){
					$e->description='Firebird/Interbase Respondi&oacute;:'. ibase_errmsg().'</b>';
					$e->solution='Verifique la consulta';
					$e->solution_code= fk_str_format($query,'html');
					$e->error_code=$error_no;
					$e->show('code_help');
				}
				return FALSE;
			}



		} // End else
			

	}

	/**
	 *@package db_firebird
	 *@method query_assoc()
	 *@desc Send a Firebird/Interbase query in assoc mode
	 *@since v0.3.1
	 * */
	public function query_assoc($query){
		$this->query_is_assoc = true;
		$this->query($query);

	}

	/**
	 *@package db_firebird
	 *@method num_rows()
	 *@desc Get number of rows in result
	 *@since
	 * */
	public function num_rows($rs = null){
		// ibase_num_rows Not implemented yet
		return 0;

	}
	/**
	 *@package db_firebird
	 *@method next()
	 *@desc Fetch a result row as an associative array, a numeric array, or both depending on query() or query_assoc() method
	 *@since v0.3.1
	 * */
	public function next($rs = ''){
			
		$Resource = ( $rs!=''? $rs : $this->resource);

		if($this->query_is_assoc==true){
			return ibase_fetch_assoc($Resource);
		}else{return ibase_fetch_row($Resource);}
			
	}
	/**
	 *@package db_firebird
	 *@method find_last()
	 *@desc Fetch a result of last record as an associative array & numeric array
	 *@since v0.3.1
	 * */
	public function find_last($TABLE,$ID,$WHERE = NULL){
		$VAL = array();
		if($WHERE!=NULL){$WHERE=$WHERE;}else{$WHERE='';}

		$RS=$this->query("SELECT FIRST 1 * FROM ".$TABLE." ".$WHERE." ORDER BY ".$ID." DESC;");
		if($REC=mysql_fetch_array($RS)){$VAL=$REC;}
		return $VAL;
	}
	/**
	 *@package db_firebird
	 *@method inserted_id()
	 *@desc Get the ID generated in the last query
	 *@since v0.3.1
	 * */
	public function inserted_id(){
		// Not implemented yet
		return mysql_insert_id();
	}

	/**
	 *@package db_firebird
	 *@method describe_table()
	 *@desc describes a table
	 *@since v0.3.1
	 * */
	public function describe_table($table){

		$t_fields = array();

		$sql = ' SELECT
   RF.RDB$FIELD_NAME AS "Field"
   ,CASE F.RDB$FIELD_TYPE WHEN 261 THEN \'BLOB\'
          WHEN 14 THEN \'CHAR\'
          WHEN 40 THEN \'CSTRING\'
          WHEN 11 THEN \'D_FLOAT\'
          WHEN 27 THEN \'DOUBLE\'
          WHEN 10 THEN \'FLOAT\'
          WHEN 16 THEN \'INT64\'
          WHEN 8 THEN \'INTEGER\'
          WHEN 9 THEN \'QUAD\'
          WHEN 7 THEN \'SMALLINT\'
          WHEN 12 THEN \'DATE\'
          WHEN 13 THEN \'TIME\'
          WHEN 35 THEN \'TIMESTAMP\'
          WHEN 37 THEN \'VARCHAR\'
          ELSE \'UNKNOWN\'
     END AS "Type"
   ,RF.RDB$NULL_FLAG AS "Null"
   ,RF.RDB$DEFAULT_VALUE AS "Default"
   ,RC.RDB$CONSTRAINT_TYPE AS "Key"
FROM RDB$RELATION_FIELDS RF
  LEFT JOIN RDB$FIELDS F ON F.RDB$FIELD_NAME = RF.RDB$FIELD_SOURCE 
  LEFT JOIN RDB$INDEX_SEGMENTS IXS ON IXS.RDB$FIELD_NAME = RF.RDB$FIELD_NAME
  LEFT JOIN RDB$INDICES IX ON IX.RDB$INDEX_NAME = IXS.RDB$INDEX_NAME
  LEFT JOIN RDB$RELATION_CONSTRAINTS RC ON RC.RDB$INDEX_NAME = IX.RDB$INDEX_NAME  
WHERE RF.RDB$RELATION_NAME = \''.$table.'\'
AND( IX.RDB$RELATION_NAME IS NULL OR IX.RDB$RELATION_NAME= RF.RDB$RELATION_NAME) 
ORDER BY RF.RDB$FIELD_POSITION 
 '; 


		echo $sql;


		$this->query_assoc($sql);

		while($rec = $this->next()){
			$fld=trim($rec['Field']);
			$t_fields[$fld]['Field'] = trim($rec['Field']);
			$t_fields[$fld]['Type'] = trim($rec['Type']);
			$t_fields[$fld]['Null'] = trim($rec['Null']);
			$t_fields[$fld]['Default'] = trim($rec['Default']);
			$t_fields[$fld]['Key'] = trim($rec['Key']);
				


			//------------------
			//primary key id
			//------------------
			if($rec['Key']=='PRIMARY KEY'){
				$this->primary_key_id['Field'] = trim($rec['Field']);
				$this->primary_key_id['Type'] = trim($rec['Type']);
				$this->primary_key_id['Null'] = trim($rec['Null']);
				$this->primary_key_id['Default'] = trim($rec['Default']);
				$this->primary_key_id['Key'] = trim($rec['Key']);

			}

		}

		return $t_fields;
	} // describe_table

	/**
	 *@package db_firebird
	 *@method insert()
	 *@desc inserts record
	 *@since v0.3.1
	 * */
	public function insert($table,$array_fields,$id_field_name,$form_fields){

		$fields_list = '';
		$fields_vals = '';

		foreach($array_fields as $f_name=>$f_val){

			if($f_name!=$id_field_name && trim($f_name)!=''){

				$FieldType = isset($form_fields[$f_name]['Type'])?strtolower($form_fields[$f_name]['Type']):'';

				if($FieldType=='password'){
					// Exepcion password
					if(trim($f_val)!=''){
						$fields_vals .= "'".md5($f_val)."',";
					}else{$fields_vals .= "'',";}
				}elseif($FieldType=='date'){
					// Exepcion date
					if($f_val===NULL || trim($f_val)==''){
						$fields_vals .= " NULL ,";
					}else{
						$fields_vals .= "'".$this->escape_string($f_val)."',";
					}

				}else{
					if($f_val===NULL){
						$fields_vals .= " NULL ,";
					}else{
						$fields_vals .= "'".$this->escape_string($f_val)."',";
					}

				}
				$fields_list .= ' '.$f_name.' ,';

			}

		}

		$fields_list = trim($fields_list,',');
		$fields_vals = trim($fields_vals,',');

		$primary_fields = '';
		$primary_vals = '';
		if($id_field_name!=NULL){
			$primary_fields = ''.$id_field_name.',';
			$primary_vals = '\''.$array_fields[$id_field_name].'\',';
		}

		$sql = 'INSERT INTO '.$table.' ('.$primary_fields.''.$fields_list.')
  			   VALUES ('.$primary_vals.''.$fields_vals.')';



		$rs = $this->query($sql);
		return $rs;


	}

	/**
	 *@package db_firebird
	 *@method update()
	 *@desc updates record
	 *@since v0.3.1
	 * */
	public function update($table,$id_field_name,$array_fields,$form_fields){

		$set_fields = '';
		foreach($array_fields as $f_name=>$f_val){
			if($f_name!=$id_field_name){

				$FieldType = strtolower($form_fields[$f_name]['Type']);
				if($FieldType=='password'){
					// Exepcion password
					if(trim($f_val)!=''){
						$set_fields .= " ".$f_name." = '".md5($f_val)."',";
					}
				}elseif($FieldType=='date'){
					// Exepcion date
					if($f_val===NULL || trim($f_val)==''){
						$set_fields .= " ".$f_name." = NULL ,";
					}else{
						$set_fields .= " ".$f_name." = '".$this->escape_string($f_val)."',";
					}

				}else{
					if($f_val===NULL){
						$set_fields .= " ".$f_name." = NULL ,";
					}else{
						$set_fields .= " ".$f_name." = '".$this->escape_string($f_val)."',";
					}

				}
					
			}
		}
		$set_fields = trim($set_fields,',');



		$SET = ' SET '.$set_fields;
		$WHERE = ' WHERE '.$id_field_name.' = \''.$array_fields[$id_field_name].'\' ';

		$sql = 'UPDATE '.$table.' '.$SET.' '.$WHERE;

		$rs = $this->query($sql);

		return $rs;

	}

	/**
	 *@package db_firebird
	 *@method fetch_array()
	 *@desc Fetch a result row as an associative array, a numeric array, or both depending on query() or query_assoc() method
	 *@since v0.3.1
	 * */
	public function fetch_array($rs = ''){
			
		$Resource = ( $rs!='' ? $rs : $this->resource );

		if($this->query_is_assoc==true){
			return ibase_fetch_assoc($Resource);
		}else{return ibase_fetch_row($Resource);}

	} // fetch_array(){

	/**
	 *@package db_firebird
	 *@method escape_string()
	 *@desc returns escape strings
	 *@since v0.3.1
	 * */
	public function escape_string($str){

		//@todo mmendoza implementar

		//return mysql_real_escape_string($str);
		return ($str);

	} // escape_string()

	/**
	 *@package db
	 *@method set_select()
	 *@desc sets the proyection of query SELECT {$fields} from ....
	 *@since v0.3.1
	 * */
	public function set_select($fields){
		$this->sql_select = $fields;
	} // set_select()

	/**
	 *@package db
	 *@method set_select_distinct()
	 *@desc sets the proyection of query SELECT DISTINCT {$fields} from ....
	 *@since v0.3.1
	 * */
	public function set_select_distinct($fields){
		$this->sql_select_distinct = $fields;
	} // set_select_distinct()

	/**
	 *@package db
	 *@method set_table()
	 *@desc sets the table name
	 *@since v0.3.1
	 * */
	public function set_table($table){
		$this->sql_table = $table;
	} // set_table()

	/**
	 *@package db
	 *@method set_where()
	 *@desc sets  where condition
	 *@since v0.3.1
	 * */
	public function set_where($where){
		$this->sql_where = $where;
	} // set_where()

	/**
	 *@package db
	 *@method add_and()
	 *@desc adds and condition
	 *@since v0.3.1
	 * */
	public function add_and($and){
		$this->sql_and[] = $and;
	} // add_and()

	/**
	 *@package db
	 *@method set_group_by()
	 *@desc sets group by statement
	 *@since v0.3.1
	 * */
	public function set_group_by($group_by){
		$this->sql_group_by = $group_by;
	} // set_group_by()

	/**
	 *@package db
	 *@method add_order_by()
	 *@desc adds order by statement
	 *@since v0.3.1
	 * */
	public function add_order_by($field,$asc_desc){
		$this->sql_order_by[] = $field.' '.$asc_desc;
	} // add_order_by()

	/**
	 *@package db
	 *@method set_limit()
	 *@desc sets limit by statement
	 *@since v0.3.1
	 * */
	public function set_limit($total_records,$skip){
		$this->sql_limit = 'FIRST '.$total_records.' SKIP '.$skip.' ';
	} // set_limit()

	/**
	 *@package db
	 *@method get_sql_string()
	 *@desc returns the sql string
	 *@since v0.3.1
	 * */
	public function get_sql_string(){

		$sql = ' SELECT ';

		if(trim($this->sql_limit)!=''){
			$sql .= $this->sql_limit;
		}


		$sql .= ($this->sql_select_distinct!='') ? ' DISTINCT '.$this->sql_select_distinct : $this->sql_select;
		$sql .= ' FROM '.$this->sql_table;

		if(trim($this->sql_where)!=''){
			$sql .= ' WHERE ('.$this->sql_where.')';
		}

		if(count($this->sql_and)>0){
			$this->sql_and = implode($this->sql_and, ' AND ');
			$sql .= ' AND ('.$this->sql_and.')';
		}


		if(trim($this->sql_group_by)!=''){
			$sql .= ' GROUP BY '.$this->sql_group_by;
		}

		if(count($this->sql_order_by)>0){
			$this->sql_order_by = implode($this->sql_order_by, ', ');
			$sql .= ' ORDER BY '.$this->sql_order_by.'';
		}




		return $sql;

	} // set_limit()

}