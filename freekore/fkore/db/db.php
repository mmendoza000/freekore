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
 *@package db base
 *@desc Main class of database control
 *@since v0.1 beta
 * */
class db{

	public $db_type;  // db_type, defined at  config/environment.ini
	public $db_obj; // Database object type db_type
	public $resource;

	public $error_code = '';

	/**
	 * @package db
	 * @method  __construct()
	 * @since v0.1 beta
	 * */
	public function __construct($p_db_type = NULL){
			
	 if($p_db_type != NULL){
	 	$this->db_type = $p_db_type;
	 }else{
	 	$this->db_type = $GLOBALS['FKORE']['RUNNING']['db']['db_type'];
	 }

	 self::create_db_object();

	}
	/**
	 * @package db
	 * @method  create_db_object()
	 * @desc creates a db_{adapter-database} object
	 * @since v0.1 beta
	 * */
	private function create_db_object(){

		// build the object: new db_mysql(); or new db_oracle(); etc...
		$obj = 'db_'.$this->db_type;
		$this->db_obj = new $obj;
			
	}

	/**
	 * @package db
	 * @method  connect()
	 * @desc connects to a database
	 * @since v0.1 beta
	 * */
	public  function connect($p_host = NULL,$p_user = NULL,$p_pass = NULL,$p_db = NULL) {

		$this->db_obj->connect($p_host,$p_user,$p_pass,$p_db);

	}

	/**
	 * @package db
	 * @method  connect()
	 * @desc verify database connection
	 * @since v0.1
	 * */
	public function verfy_connection($p_host = NULL,$p_user = NULL,$p_pass = NULL,$p_db = NULL) {

		$res=$this->db_obj->verfy_connection($p_host,$p_user,$p_pass,$p_db);

		return $res;

	}

	/**
	 * @package db
	 * @method  close()
	 * @desc close database conection
	 * @since v0.1 beta
	 * */
	public function close() {
		$this->db_obj->close();
	}
	/**
	 * @package db
	 * @method  query()
	 * @desc executes database query
	 * @since v0.1 beta
	 * */
	public function query($query=NULL){

		if($query==NULL){
			$query = $this->db_obj->get_sql_string();
		}

		$rs = $this->db_obj->query($query);
		if($rs==FALSE){
			$this->error_code = $this->db_obj->error_code;
		}
		$this->resource=$this->db_obj->resource;
		return $rs;
	}
	/**
	 * @package db
	 * @method  query_assoc()
	 * @desc executes query un assoc mode
	 * @since v0.1 beta
	 * */
	public function query_assoc($query){
		$this->db_obj->query_assoc($query);
		$this->resource=$this->db_obj->resource;
	}
	/**
	 * @package db
	 * @method  num_rows()
	 * @desc returns the num_rows of last query
	 * @since v0.1 beta
	 * */
	public function num_rows(){
		return $this->db_obj->num_rows();

	}
	/**
	 * @package db
	 * @method  next()
	 * @desc fetchs the database resource of last query execution
	 * @since v0.1 beta
	 * */
	public function next(){

		return $this->db_obj->next();

	}
	/**
	 * @package db
	 * @method  find_last()
	 * @desc finds the last record from a given table and id value, [where optional argument]
	 * @since v0.1 beta
	 * */
	public function find_last($TABLE,$ID,$WHERE = NULL){
		return $this->db_obj->find_last($TABLE,$ID,$WHERE);
	}
	/**
	 * @package db
	 * @method  inserted_id()
	 * @desc returns the inserted id of last query
	 * @since v0.1 beta
	 * */
	public function inserted_id(){
		return $this->db_obj->inserted_id();
	}
	/**
	 * @package db
	 * @method  describe_table()
	 * @since v0.1 beta
	 * */
	public function describe_table($table){
		return $this->db_obj->describe_table($table);
	}

	/**
	 * @package db
	 * @method  insert()
	 * @since v0.1 beta
	 * */
	public function insert($table,$array_fields,$id_field_name=NULL,$form_fields=array()){
		$rs = $this->db_obj->insert($table,$array_fields,$id_field_name,$form_fields);
		if($rs==FALSE){
			$this->error_code = $this->db_obj->error_code;
		}
		return $rs;
	}

	/**
	 * @package db
	 * @method  insert()
	 * @since v0.1 beta
	 * */
	public function update($table,$array_fields,$id_field_name=NULL,$form_fields=array()){
		$rs = $this->db_obj->update($table,$array_fields,$id_field_name,$form_fields);
		if($rs==FALSE){
			$this->error_code = $this->db_obj->error_code;
		}

		return $rs;
	}
	/**
	 * @package db
	 * @method  get_tablename()
	 * @desc returns the tablename
	 * @since v0.1 beta
	 * */
	public function get_tablename(){
		return $this->db_obj->get_tablename();
	}
	/**
	 * @package db
	 * @method  get_primary_key_id()
	 * @desc returns the primary key field name
	 * @since v0.1 beta
	 * */
	public function get_primary_key_id(){
		return $this->db_obj->primary_key_id;
	}
	/**
	 * @package db
	 * @method  set_primary_key_id()
	 * @desc sets the primary key field name
	 * @since v0.3.4 
	 * */
	public function set_primary_key_id($field_name){
		$this->db_obj->primary_key_id = $field_name;
	}

	/**
	 *@package db
	 *@method escape_string()
	 *@desc returns escape strings
	 *@since v0.1 beta
	 * */
	public function escape_string($str){
		return $this->db_obj->escape_string($str);
	} // escape_string()

	//-------------------
	// SQL - INTERPRETER
	//-------------------


	/**
	 *@package db
	 *@method set_select()
	 *@desc sets the proyection of query SELECT {$fields} from ....
	 *@since v0.3.1
	 * */
	public function set_select($fields){
		return $this->db_obj->set_select($fields);
	} // set_select()

	/**
	 *@package db
	 *@method set_select_distinct()
	 *@desc sets the proyection of query SELECT DISTINCT {$fields} from ....
	 *@since v0.3.1
	 * */
	public function set_select_distinct($fields){
		return $this->db_obj->set_select_distinct($fields);
	} // set_select_distinct()

	/**
	 *@package db
	 *@method set_table()
	 *@desc sets the table name
	 *@since v0.3.1
	 * */
	public function set_table($table){
		return $this->db_obj->set_table($table);
	} // set_table()

	/**
	 *@package db
	 *@method set_where()
	 *@desc sets  where condition
	 *@since v0.3.1
	 * */
	public function set_where($where){
		return $this->db_obj->set_where($where);
	} // set_where()

	/**
	 *@package db
	 *@method add_and()
	 *@desc adds and condition
	 *@since v0.3.1
	 * */
	public function add_and($and){
		return $this->db_obj->add_and($and);
	} // add_and()

	/**
	 *@package db
	 *@method set_group_by()
	 *@desc sets group by statement
	 *@since v0.3.1
	 * */
	public function set_group_by($group_by){
		return $this->db_obj->set_group_by($group_by);
	} // set_group_by()

	/**
	 *@package db
	 *@method add_order_by()
	 *@desc adds order by statement
	 *@since v0.3.1
	 * */
	public function add_order_by($field,$asc_desc){
		return $this->db_obj->add_order_by($field,$asc_desc);
	} // add_order_by()

	/**
	 *@package db
	 *@method set_limit()
	 *@desc sets limit by statement
	 *@since v0.3.1
	 * */
	public function set_limit($total_records,$skip=0){
		return $this->db_obj->set_limit($total_records,$skip);
	} // set_limit()

	/**
	 * @package db
	 * @method  result()
	 * @desc executes database query and returns the records into an array
	 * @since v0.1 beta
	 * */
	public function result($sql = NULL){

		$rs = $this->query($sql);

		$records = array();

		while($row = $this->next()){
			$records[]=$row;
		}


		return $records;
	}

	/**
	 * @package db
	 * @method  result_assoc()
	 * @desc executes database query and returns the records into an array
	 * @since v0.3.2
	 * */
	public function result_assoc($sql = NULL){

		$rs = $this->query_assoc($sql);

		$records = array();

		while($row = $this->next()){
			$records[]=$row;
		}


		return $records;
	}


} // End class db