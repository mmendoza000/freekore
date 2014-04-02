<?php
/**
 * FreeKore Php Framework
 * Version: 0.1 Beta
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2010 Freekore PHP Team
 * @license    GNU GPL License
 */
/*************************************************************
 **  Programa:  oracle.php
 **  Descripcion: Funciones de base de datos adaptador Oracle
 **  proyecto    fecha      por         descripcion
 **  ----------  ---------  ----------- ----------------
 **  00000001    19/06/10   mmendoza    Creado.
 **************************************************************/

/*
 Oracle adapter
 */

/**
 *@package db_oracle
 *@desc  Oracle Object to ejecutes Sql Operations
 *@since v0.1 beta
 * */
class db_oracle implements db_interface{
	// define db_interface...
	public $resource;
	public static $conn;
	public static $host = HOST;
	public static $user = USER;
	public static $pass = PASSWORD;
	public static $database = SYSTEM_DB;
	public  $sql_query = "";
	public  $primary_key_id = array();
	private $query_is_assoc = false;
	public static $is_connected = false;

	private $id_field_name = NULL;

	private $arr_handled_errors = array(
			'1062'=>'ER_DUP_ENTRY',
			'1451'=>'ER_ROW_IS_REFERENCED_2',
			'1452'=>'ER_NO_REFERENCED_ROW_2');

	public $error_code = '';

	// SQL STRING VARS
	private $sql_select = '*';
	private $sql_select_distinct = '';
	private $sql_table = '';
	private $sql_where = '';
	private $sql_and = '';
	private $sql_group_by = '';
	private $sql_order_by = array();
	private $sql_limit = '';

	/**
	 *@package db_oracle
	 *@method connect()
	 *@desc Open a connection to a Oracle Server
	 *@since v0.1 beta
	 * */
	public static function connect($p_host = NULL,$p_user = NULL,$p_pass = NULL,$p_db = NULL) {

		$H = isset($p_host)? $p_host : self::$host;
		$U = isset($p_user)? $p_user : self::$user;
		$P = isset($p_pass)? $p_pass : self::$pass;
		$D = isset($p_db)  ? $p_db   : self::$database;

		self::$conn = oci_connect($U, $P, $H);
		if (!self::$conn) {
			$e = oci_error();
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}else{
			self::$is_connected = true;
		}
			
	}
	/**
	 *@package db_oracle
	 *@method close()
	 *@desc Close Oracle connection
	 *@since v0.1 beta
	 * */
	public function close() {
			
		if(isset($this->resource)){
			oci_free_statement($this->resource);
		}
		if(self::$is_connected){
			oci_close(self::$conn);
			self::$is_connected = false;
		}

			
	}

	/**
	 *@package db_oracle
	 *@method query()
	 *@desc Send a Oracle query
	 *@since v0.1 beta
	 *@return bool & Populates $this->resource
	 * */
	public function query($query){

		$this->sql_query = $query ;

		// Prepare the statement
		$this->resource = oci_parse(self::$conn, $query);
		if (!$this->resource) {
			$e = oci_error($this->conn);
			trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
		}

		// Perform the logic of the query
		$ok = oci_execute($this->resource);
		if($ok){
			return TRUE;
		}else{

			//trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			// is hanled error
			
			$o_err = oci_error($this->resource);
						
			$is_handed = false;

			if(array_key_exists($o_err['code'], $this->arr_handled_errors)){
				$is_handed = true;
			}

			if($is_handed==true){
				$this->error_code = $this->arr_handled_errors[$o_err['code']];
				return FALSE;
			}else{
				$this->error_code = $o_err['code'];
				// if uknown error
				try{
					throw new FkException("Oracle Error");
				}catch(FkException $e){
					$e->description='Oracle Respondi&oacute;:'. $o_err['message'].'</b>';
					$e->solution='Verifique la consulta';
					$e->solution_code= fk_str_format($query,'html');
					$e->error_code=$o_err['code'];
					$e->show('code_help');
				}
				return FALSE;
			}
		}
		
			
		return $this->resource;

	}
	/**
	 *@package db_oracle
	 *@method query_assoc()
	 *@desc Send a Oracle query in assoc mode
	 *@since v0.1 beta
	 **/
	public function query_assoc($query){
		$this->query_is_assoc = true;
		$this->query($query);

	}

	/**
	 *@package db_oracle
	 *@method num_rows()
	 *@desc Get number of rows in result
	 *@since v0.1 beta
	 * */
	public function num_rows($rs = null){
		// Definir

	}
	/**
	 *@package db_oracle
	 *@method next()
	 *@desc Fetch a result row as an associative array, a numeric array, or both depending on query() or query_assoc() method
	 *@since v0.1 beta
	 * */
	public function next($rs = ''){

	 $Resource = ( $rs!='' ? $rs : $this->resource );
	 // Falta Validar si es assoc o no
	 return oci_fetch_array($Resource,OCI_ASSOC+OCI_RETURN_NULLS);
	}
	/**
	 *@package db_oracle
	 *@method find_last()
	 *@desc Fetch a result of last record as an associative array & numeric array
	 *@since v0.1 beta
	 * */
	public function find_last($TABLE,$ID,$WHERE = NULL){
	 $VAL = array();
	 if($WHERE!=NULL){$WHERE=$WHERE;}else{$WHERE='';}
	 $RS=$this->query("SELECT * FROM `".$TABLE."` ".$WHERE." ORDER BY ".$ID." DESC LIMIT 1 ;");
	 if($REC=mysql_fetch_array($RS)){$VAL=$REC;}
	 return $VAL;
	}
	/**
	 *@package db_oracle
	 *@method inserted_id()
	 *@desc Get the ID generated in the last query
	 *@since v0.1 beta
	 * */
	public function inserted_id(){
		if($this->id_field_name!=NULL){
			$this->query('SELECT MAX('.$this->id_field_name.') as LAST_ID FROM '.$this->sql_table);
			if($res= $this->next()){
				return $res['LAST_ID'];
			}else{
				return NULL;
			}
			
		}else{
			return NULL;
		}
		
		 
	}
	/**
	 *@package db_oracle
	 *@method describe_table()
	 *@desc describes a table
	 *@since v0.1 beta
	 * */
	public function describe_table($table){

		$t_fields = array();
		$sql = " SELECT
column_name \"Field\", 
concat(concat(concat(data_type,'('),data_length),')') \"Type\",
nullable \"Null?\"

FROM user_tab_columns
WHERE table_name='".strtoupper($table)."' ";

		$this->query($sql);

		while($rec = $this->next()){
			$fld=$rec['Field'];
			$t_fields[$fld] = $rec;
			//------------------
			//primary key id
			//------------------
			//if($rec['Key']=='PRI'){
			//$this->primary_key_id = $rec;
			//}
		}
		return $t_fields;
	} // describe_table

	public function insert($table,$array_fields,$id_field_name,$form_fields){
			
		$fields_list = '';
		$fields_vals = '';
		$this->id_field_name = $id_field_name;
		$this->sql_table = $table;

		foreach($array_fields as $f_name=>$f_val){

			if($f_name!=$this->id_field_name && trim($f_name)!=''){

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
						$fields_vals .= "STR_TO_DATE('".$this->escape_string($f_val)."', '".DB_DATE_FORMAT."'),";
					}

				}else{
					if($f_val===NULL){
						$fields_vals .= " NULL ,";
					}else{
						$fields_vals .= "'".$this->escape_string(stripslashes($f_val))."',";
					}

				}
				$fields_list .= ' '.$f_name.' ,';

			}

		}

		$fields_list = trim($fields_list,',');
		$fields_vals = trim($fields_vals,',');

		$primary_fields = '';
		$primary_vals = '';
		if($this->id_field_name!=NULL){
			$primary_fields = ''.$this->id_field_name.',';
			$primary_vals = 'NULL,';
		}

		$sql = 'INSERT INTO '.$this->sql_table.' ('.$primary_fields.''.$fields_list.')
  			   VALUES ('.$primary_vals.''.$fields_vals.')';



		$rs = $this->query($sql);
		return $rs;


	}

	/**
	 *@package db_mysql
	 *@method update()
	 *@desc updates record
	 *@since v0.3.1
	 * */
	public function update($table,$array_fields,$id_field_name,$form_fields){


		$set_fields = '';
		$this->id_field_name = $id_field_name;

		$WHERE = '';

		if( $this->sql_where != ''){
			$WHERE = ' WHERE ( '.$this->sql_where.' ) ';

		}else{

			if($this->id_field_name==NULL){
				$form_fields = $this->describe_table($table);

				$this->id_field_name = isset($this->primary_key_id['Field'])?$this->primary_key_id['Field']:NULL;
			}

			if($this->id_field_name!=NULL){
				$WHERE = ' WHERE '.$this->id_field_name.' = \''.$array_fields[$this->id_field_name].'\' ';
			}

		}


		foreach($array_fields as $f_name=>$f_val){
			if($f_name!=$this->id_field_name){

				$FieldType = isset($form_fields[$f_name]['Type'])?strtolower($form_fields[$f_name]['Type']):'';
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
						$set_fields .= " ".$f_name." = STR_TO_DATE('".$this->escape_string($f_val)."', '".DB_DATE_FORMAT."'),";
					}

				}else{
					if($f_val===NULL){
						$set_fields .= " ".$f_name." = NULL ,";
					}else{
						$set_fields .= " ".$f_name." = '".$this->escape_string(stripslashes($f_val))."',";
					}

				}
					
			}
		}
		$set_fields = trim($set_fields,',');



		if($WHERE!=''){

			$SET = ' SET '.$set_fields;
			$sql = 'UPDATE '.$table.' '.$SET.' '.$WHERE.' ';

			$rs = $this->query($sql);


		}else{

			echo ' WHERE Required. Use: $db->set_where(" field = \'1\'") ';
			die();

		}


		return $rs;

	}

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
			
		$this->sql_and .= $and;
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
		$this->sql_limit = $skip.', '.$total_records;
	} // set_limit()

	/**
	 *@package db
	 *@method get_sql_string()
	 *@desc returns the sql string
	 *@since v0.3.1
	 * */
	public function get_sql_string(){

		$sql = ' SELECT ';
		$sql .= ($this->sql_select_distinct!='') ? ' DISTINCT '.$this->sql_select_distinct : $this->sql_select;
		$sql .= ' FROM '.$this->sql_table;

		if(trim($this->sql_where)!=''){
			$sql .= ' WHERE ('.$this->sql_where.')';
		}else{
			$sql .= ' WHERE (1=1) ';
		}



		if(trim($this->sql_and)!=''){
			$sql .= ' '.$this->sql_and;
		}



		if(trim($this->sql_group_by)!=''){
			$sql .= ' GROUP BY '.$this->sql_group_by;
		}

		if(count($this->sql_order_by)>0){
			$this->sql_order_by = implode($this->sql_order_by, ', ');
			$sql .= ' ORDER BY '.$this->sql_order_by.'';
		}


		if(trim($this->sql_limit)!=''){
			$sql .= ' LIMIT  '.$this->sql_limit;
		}


		return $sql;

	} // get_sql_string()

	/**
	 *@package db_oracle
	 *@method escape_string()
	 *@desc returns escape strings
	 *@since v0.1 beta
	 * */
	public function escape_string($str){

		
		return $str;

	} // escape_string()

	/**
	 *@package db_oracle
	 *@method fetch_array()
	 *@desc Fetch a result row as an associative array, a numeric array, or both depending on query() or query_assoc() method
	 *@since v0.1 beta
	 * */
	public function fetch_array(){
			
	} // fetch_array(){


}