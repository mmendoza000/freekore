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
 **  Programa: db_interface.php
 **  Descripcion: Interface para bases de datos
 **  proyecto    fecha      por         descripcion
 **  ----------  ---------  ----------- ----------------
 **  00000001    19/06/10   mmendoza    Creado.
 **************************************************************/

/**
 *@package db_interface
 *@desc interface to interact with database adapters
 *@since v0.1 beta
 * */

interface db_interface{
	/**
	 *@package db_interface
	 *@method connect()
	 *@desc Open a connection to a Database Server
	 *@since v0.1 beta
	 * */
	public static function connect($p_host = NULL,$p_user = NULL,$p_pass = NULL,$p_db = NULL);
	/**
	 *@package db_interface
	 *@method close()
	 *@desc Close Database connection
	 *@since v0.1 beta
	 * */
	public function close();
	/**
	 *@package db_interface
	 *@method query()
	 *@desc Send a SQL query
	 *@since v0.1 beta
	 *@return bool & Populates $this->resource
	 * */
	public function query($query);
	/**
	 *@package db_interface
	 *@method query_assoc()
	 *@desc Send a SQL query in assoc mode
	 *@since v0.1 beta
	 * */
	public function query_assoc($query);
	/**
	 *@package db_interface
	 *@method num_rows()
	 *@desc Get number of rows in result
	 *@since v0.1 beta
	 * */
	public function num_rows($rs = NULL);
	/**
	 *@package db_interface
	 *@method next()
	 *@desc Fetch a result row as an associative array, a numeric array, or both depending on query() or query_assoc() method
	 *@since v0.1 beta
	 * */
	public function next();
	/**
	 *@package db_interface
	 *@method find_last()
	 *@desc Fetch a result of last record as an associative array & numeric array
	 *@since v0.1 beta
	 * */
	public function find_last($TABLE,$ID,$WHERE = NULL);
	/**
	 *@package db_interface
	 *@method inserted_id()
	 *@desc Get the ID generated in the last query
	 *@since v0.1 beta
	 * */
	public function inserted_id();
	/**
	 *@package db_interface
	 *@method describe_table()
	 *@desc describes a table
	 *@since v0.1 beta
	 * */
	public function describe_table($table);
	/**
	 *@package db_interface
	 *@method insert()
	 *@desc inserts record
	 *@since v0.3.1
	 * */
	public function insert($table,$array_fields,$id_field_name,$form_fields);
	
	/**
	 *@package db_interface
	 *@method update()
	 *@desc updates record
	 *@since v0.3.1
	 * */
	public function update($table,$array_fields,$id_field_name,$form_fields);
	
	/**
	 *@package db_interface
	 *@method fetch_array()
	 *@desc Fetch a result row as an associative array, a numeric array, or both depending on query() or query_assoc() method
	 *@since v0.1 beta
	 * */
	public function fetch_array();
	
	
	/**
	 *@package db
	 *@method set_select()
	 *@desc sets the proyection of query SELECT {$fields} from ....
	 *@since v0.3.1
	 * */
	public function set_select($fields);

	/**
	 *@package db
	 *@method set_select_distinct()
	 *@desc sets the proyection of query SELECT DISTINCT {$fields} from ....
	 *@since v0.3.1
	 * */
	public function set_select_distinct($fields);
	

	/**
	 *@package db
	 *@method set_table()
	 *@desc sets the table name
	 *@since v0.3.1
	 * */
	public function set_table($table);


	/**
	 *@package db
	 *@method set_where()
	 *@desc sets  where condition
	 *@since v0.3.1
	 * */
	public function set_where($where);

	/**
	 *@package db
	 *@method add_and()
	 *@desc adds and condition
	 *@since v0.3.1
	 * */
	public function add_and($and);

	/**
	 *@package db
	 *@method set_group_by()
	 *@desc sets group by statement
	 *@since v0.3.1
	 * */
	public function set_group_by($group_by);

	/**
	 *@package db
	 *@method add_order_by()
	 *@desc adds order by statement
	 *@since v0.3.1
	 * */
	
	public function add_order_by($field,$asc_desc);
	/**
	 *@package db
	 *@method set_limit()
	 *@desc sets limit by statement
	 *@since v0.3.1
	 * */
	public function set_limit($total_records,$skip);

	/**
	 *@package db
	 *@method get_sql_string()
	 *@desc gets the sql string 
	 *@since v0.3.1
	 * */
	public function get_sql_string();
	
}