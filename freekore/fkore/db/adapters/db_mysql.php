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
 **  Programa:  mysql.php
 **  Descripcion: Funciones de base de datos adaptador MySql
 **  proyecto    fecha      por         descripcion
 **  ----------  ---------  ----------- ----------------
 **  00000001    30/04/10   mmendoza    Creado.
 **************************************************************/

/**
 *@package db_mysql
 *@desc  MySql Object to ejecutes MySql Operations
 *@since v0.1 beta
 * */

class db_mysql implements db_interface{

	public $resource;
	public static $host = HOST;
	public static $user = USER;
	public static $pass = PASSWORD;
	public static $database = SYSTEM_DB;
	public  $sql_query='';
	public  $primary_key_id = array();
	private $query_is_assoc = false;
	public static $is_connected = false;

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
	 *@package db_mysql
	 *@method connect()
	 *@desc Open a connection to a MySQL Server
	 *@since v0.1 beta
	 * */
	public static function connect($p_host = NULL,$p_user = NULL,$p_pass = NULL,$p_db = NULL) {

		$H = isset($p_host)? $p_host : self::$host;
		$U = isset($p_user)? $p_user : self::$user;
		$P = isset($p_pass)? $p_pass : self::$pass;
		$D = isset($p_db)  ? $p_db   : self::$database;


		if(@mysql_connect($H,$U,$P)==false){

			try{
				throw new FkException("Error al conectar a la db ");
			}catch(FkException $e){
				$e->description='Mysql Respondi&oacute;:'. mysql_error().'</b>';
				$e->solution='Verifique la conexion, posiblemente el archivo /app/config/environment.ini no contiene los datos de conexion correctos. Vea ejemplo:';
				$e->solution_code= fk_str_format('[development]
db_host = localhost
db_username = tester
db_password = test
db_name = freekore_dev
db_type = mysql','html');
				$e-> error_code = 'DB000002';
				$e->show('code_help');

			}
		}

		if(mysql_select_db($D)!=false){
			self::$is_connected = true;

		}else{

			try{
				throw new FkException("Error al Seleccionar la db: ");
			}catch(FkException $e){
				$e->description='Mysql Respondi&oacute;:'. mysql_error().'</b>';
				$e->solution='Verifique el nombre de la base de datos, posiblemente el archivo /app/config/environment.ini no contiene el nombre de la db correcto. Vea ejemplo:';
				$e->solution_code= fk_str_format('[development]
db_host = localhost
db_username = tester
db_password = test
db_name = freekore_dev
db_type = mysql','html');
				$e-> error_code = 'DB000003';
				$e->show('code_help');

			}
		} // End else


			
	}
	public static function verfy_connection($p_host = NULL,$p_user = NULL,$p_pass = NULL,$p_db = NULL) {

		$error = false;
		$error_code = '';
		$H = isset($p_host)? $p_host : self::$host;
		$U = isset($p_user)? $p_user : self::$user;
		$P = isset($p_pass)? $p_pass : self::$pass;
		$D = isset($p_db)  ? $p_db   : self::$database;


		if(@mysql_connect($H,$U,$P)==true){

			if(mysql_select_db($D)==false){
				$error = true;
				$error_code = 'SELECT_DB_ERROR';

			}

		}else{
			$error = true;
			$error_code = 'CONNECT_ERROR';

		}

		$arr_error['error'] = $error;
		$arr_error['code'] = $error_code;

		return $arr_error;
			
	}

	/**
	 *@package db_mysql
	 *@method close()
	 *@desc Close MySQL connection
	 *@since v0.1 beta
	 * */
	public function close() {
		if(self::$is_connected){
			mysql_close();
			self::$is_connected = false;
		}
	}
	/**
	 *@package db_mysql
	 *@method query()
	 *@desc Send a MySQL query
	 *@since v0.1 beta
	 *@return bool & Populates $this->resource
	 * */
	public function query($query){

		$this->sql_query = $query ;

		if($this->resource = mysql_query($query)){
			return TRUE;
		}else{
			// is hanled error
			$error_no = mysql_errno();

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
					throw new FkException("Mysql Error");
				}catch(FkException $e){
					$e->description='Mysql Respondi&oacute;:'. mysql_error().'</b>';
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
	 *@package db_mysql
	 *@method query_assoc()
	 *@desc Send a MySQL query in assoc mode
	 *@since v0.1 beta
	 * */
	public function query_assoc($query){
		$this->query_is_assoc = true;
		$this->query($query);

	}

	/**
	 *@package db_mysql
	 *@method num_rows()
	 *@desc Get number of rows in result
	 *@since v0.1 beta
	 * */
	public function num_rows($rs = null){
		$Resource = ( $rs!=NULL? $rs : $this->resource);
		return mysql_num_rows($Resource);

	}
	/**
	 *@package db_mysql
	 *@method next()
	 *@desc Fetch a result row as an associative array, a numeric array, or both depending on query() or query_assoc() method
	 *@since v0.1 beta
	 * */
	public function next($rs = ''){
			
		$Resource = ( $rs!=''? $rs : $this->resource);

		if($this->query_is_assoc==true){
			return mysql_fetch_assoc($Resource);
		}else{return mysql_fetch_array($Resource);}
			
	}
	/**
	 *@package db_mysql
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
	 *@package db_mysql
	 *@method inserted_id()
	 *@desc Get the ID generated in the last query
	 *@since v0.1 beta
	 * */
	public function inserted_id(){
		return mysql_insert_id();
	}

	/**
	 *@package db_mysql
	 *@method describe_table()
	 *@desc describes a table
	 *@since v0.1 beta
	 * */
	public function describe_table($table){

		$t_fields = array();
		$sql = ' DESC '.$table.';';
		$this->query_assoc($sql);

		while($rec = $this->next()){
			$fld=$rec['Field'];
			$t_fields[$fld] = $rec;

			//------------------
			//primary key id
			//------------------
			if($rec['Key']=='PRI'){
				$this->primary_key_id = $rec;
			}

		}
		return $t_fields;
	} // describe_table

	public function insert($table,$array_fields,$id_field_name,$form_fields){
			
		$fields_list = '';
		$fields_vals = '';



		foreach($array_fields as $f_name=>$f_val){

			if($f_name!=$id_field_name && trim($f_name)!=''){

				$FieldType = isset($form_fields[$f_name]['Type'])?strtolower($form_fields[$f_name]['Type']):'';
				$FieldTypeXp = explode('(', $FieldType);
				if(count($FieldTypeXp)>1){ $FieldType = $FieldTypeXp[0];}




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
				}elseif($FieldType=='timestamp'){
					// Exepcion date
					if($f_val===NULL || trim($f_val)==''){
						$fields_vals .= " NULL ,";
					}else{
						$f_val_time = substr($f_val, 11); // agregar tiempo
						$fields_vals .= "CONCAT(STR_TO_DATE('".$this->escape_string($f_val)."', '".DB_DATE_FORMAT."'),' ','".$f_val_time."'),";
					}
				}elseif($FieldType=='int' || $FieldType=='decimal' || $FieldType=='money' && trim($f_val)!=''){
					// Exception numeric
					$fields_vals .= "'".str_replace(',', '', $f_val)."',";
				}else{
					if($f_val===NULL){
						$fields_vals .= " NULL ,";
					}else{
						$fields_vals .= "'".$this->escape_string(stripslashes($f_val))."',";
					}

				}
				$fields_list .= ' `'.$f_name.'` ,';

			}

		}

		$fields_list = trim($fields_list,',');
		$fields_vals = trim($fields_vals,',');

		$primary_fields = '';
		$primary_vals = '';
		if($id_field_name!=NULL){
			$primary_fields = '`'.$id_field_name.'`,';
			$primary_vals = 'NULL,';
		}

		$sql = 'INSERT INTO '.$table.' ('.$primary_fields.''.$fields_list.')
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

		$WHERE = '';

		if( $this->sql_where != ''){
			$WHERE = ' WHERE ( '.$this->sql_where.' ) ';

		}else{

			if($id_field_name==NULL){
				$form_fields = $this->describe_table($table);

				$id_field_name = isset($this->primary_key_id['Field'])?$this->primary_key_id['Field']:NULL;
			}

			if($id_field_name!=NULL){
				$WHERE = ' WHERE '.$id_field_name.' = \''.$array_fields[$id_field_name].'\' ';
			}

		}





		foreach($array_fields as $f_name=>$f_val){
			if($f_name!=$id_field_name){

				$FieldType = isset($form_fields[$f_name]['Type'])?strtolower($form_fields[$f_name]['Type']):'';
				$FieldTypeXp = explode('(', $FieldType);
				if(count($FieldTypeXp)>1){ $FieldType = $FieldTypeXp[0];}



				if($FieldType=='password'){
					// Exepcion password
					if(trim($f_val)!=''){
						$set_fields .= " `".$f_name."` = '".md5($f_val)."',";
					}
				}elseif($FieldType=='date'){
					// Exepcion date
					if($f_val===NULL || trim($f_val)==''){
						$set_fields .= " `".$f_name."` = NULL ,";
					}else{
						$set_fields .= " `".$f_name."` = STR_TO_DATE('".$this->escape_string($f_val)."', '".DB_DATE_FORMAT."'),";
					}
				}elseif($FieldType=='timestamp'){
					// Exepcion timestamp
					if($f_val===NULL || trim($f_val)==''){
						$set_fields .= " `".$f_name."` = NULL ,";
					}else{
						$f_val_time = substr($f_val, 11); // agregar tiempo
						$set_fields .= " `".$f_name."` = CONCAT(STR_TO_DATE('".$this->escape_string($f_val)."', '".DB_DATE_FORMAT."'),' ','".$f_val_time."'),";
					}
				}elseif($FieldType=='int' || $FieldType=='decimal' || $FieldType=='money' && trim($f_val)!=''){
					// Exception numeric
					$set_fields .= " `".$f_name."` = '".$this->escape_string(stripslashes(str_replace(',', '', $f_val)))."',";

				}else{
					if($f_val===NULL){
						$set_fields .= " `".$f_name."` = NULL ,";
					}else{
						$set_fields .= " `".$f_name."` = '".$this->escape_string(stripslashes($f_val))."',";
					}

				}
					
			}
		}
		$set_fields = trim($set_fields,',');



		if($WHERE!=''){

			$SET = ' SET '.$set_fields;
			$sql = 'UPDATE '.$table.' '.$SET.' '.$WHERE.'  LIMIT 1';

			$rs = $this->query($sql);


		}else{

			echo ' WHERE Required. Use: $db->set_where(" field = \'1\'") ';
			die();

		}


		return $rs;

	}


	/**
	 *@package db_mysql
	 *@method fetch_array()
	 *@desc Fetch a result row as an associative array, a numeric array, or both depending on query() or query_assoc() method
	 *@since v0.1 beta
	 * */
	public function fetch_array($rs = ''){
			
		$Resource = ( $rs!='' ? $rs : $this->resource );

		if($this->query_is_assoc==true){
			return mysql_fetch_assoc($Resource);
		}else{return mysql_fetch_array($Resource);}

	} // fetch_array(){

	/**
	 *@package db_mysql
	 *@method escape_string()
	 *@desc returns escape strings
	 *@since v0.1 beta
	 * */
	public function escape_string($str){

		return mysql_real_escape_string($str);

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



}