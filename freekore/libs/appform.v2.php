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
 **  Programa:    AppForm.class.php
 **  Descripcion: Clase para Formularios dinamicos en freekore
 **  proyecto    fecha      por         descripcion
 **  ----------  ---------  ----------- ----------------
 **  00000001    11/10/10   mmendoza    Creado.
 **************************************************************/
/**
 *@package appform
 *@since v0.1 beta
 *@desc  Application Form
 *@version 2.0
 **/

//Requires freekore/libs
load::freekoreLib('security');
load::freekoreLib('ajax');
load::freekoreLib('ajax_submit');


class AppForm{

	private $model = '';
	public  $DbRecord;
	private $IdObject = '';
	private $ObjectCode = '';
	private $CurrentPage='';
	private $IdRecord = 0;
	private $record_to_update='';
	private $record_to_add_new='';
	private $url_redirect_onCancel = ''; // Url redirect after "cancel"
	private $url_redirect_onDelete = ''; // Url redirect after "delete"
	private $url_redirect_onSave = ''; // Url redirect after "save"
	private $edit_mode = false;
	private $default_form_values = '';
	public  $sql_list='';
	private $total_records = 0;
	private $record_number = 1;



	/**
	 * @desc It removes the html &lt;form&gt; tag to use it under other appform.<br>
	 * Use it for multiple forms inside the main appform
	 * */
	public $remove_form = FALSE;

	public $auto_save = FALSE;
	public $auto_save_time = 40000;
	public $save_from_new = false;

	public $id_user = 0;

	public  $multipartForm = FALSE;

	public  $autoclose_messages = TRUE;

	public  $Labels =array();
	private $ShowFields = array();
	private $HideFields = array();
	private $ShowOrHide = '';

	private $relation_options = array();
	private $extra_form_vars = array(); // Extra Variables send

	private $operation = '';

	// Validate
	private $ValidationFields = array();
	private $ValidationMaskFields = array();

	private $DisplayNavigationBar = true; // Barra de navegacion

	public  $toolbar_visible = true;
	public  $toolbar_position = 'top';

	// botones
	private $btn_view = '';
	private $btn_export = '';
	private $btn_new = '';
	private $btn_edit = '';
	private $btn_duplic = '';
	private $btn_save = '';
	private $btn_savedit = '';
	private $btn_cancel = '';
	private $btn_delete = '';
	private $btn_first = '';
	private $btn_prev = '';
	private $btn_next = '';
	private $btn_last = '';
	private $btn_search = '';
	private $records_of ='';

	public $btn_view_disabled = false;
	public $btn_export_disabled = false;
	public $btn_new_disabled = false;
	public $btn_edit_disabled = false;
	public $btn_duplic_disabled = FALSE;
	public $btn_save_disabled = false;
	public $btn_savedit_disabled = false;
	public $btn_cancel_disabled = false;
	public $btn_delete_disabled = false;
	public $btn_first_disabled = false;
	public $btn_prev_disabled = false;
	public $btn_next_disabled = false;
	public $btn_last_disabled = false;
	public $btn_search_disabled = false;

	public $btn_view_visible = true;
	public $btn_export_visible = true;
	public $btn_new_visible = true;
	public $btn_edit_visible = true;
	public $btn_duplic_visible = TRUE;
	public $btn_save_visible = FALSE;
	public $btn_savedit_visible = FALSE;
	public $btn_cancel_visible = FALSE;
	public $btn_delete_visible = true;
	public $btn_first_visible = true;
	public $btn_prev_visible = true;
	public $records_of_visible = true;
	public $btn_next_visible = true;
	public $btn_last_visible = true;
	public $btn_search_visible = true;

	// Use template
	private $use_template = FALSE;
	private $template = '';
	private $ArrReplaceTpl = array();
	private $ArrSearchTpl = array();
	private $ArrData = array();

	// applist
	public $templateList = '';
	public $reg_x_page = 50;
	public $list_btn_delete_visible = true;
	public $list_btn_edit_visible = true;

	public $title = '';

	// Field mode
	// $FieldMode_EditView = true : shows fields as view-edit
	// $FieldMode_EditView = false : shows fields as update
	public $FieldMode_EditView = FALSE;
	public $view_as_list = false;
	private $autocomplete = array();

	//View as
	private $view_as = 'F'; // F = form | L = list
	/**
	 * @desc readonly | edit
	 * */
	public $form_mode = 'readonly'; // readonly | edit

	// Error handling
	public $db_error_code = '';


	// Security
	/**
	 *@package AppForm
	 *@since v0.1
	 *@var $encode_fields
	 *@desc Implements Security to AppForm encoding the html fields
	 * */
	public $encode_fields = FALSE;
	private $privileges_enabled = FALSE;

	// Change submit action.
	private $custom_submit = FALSE;
	private $submit_url = '';
	private $submit_method = 'GET';

	//Search settings
	public $SearchCustomFields = '*';
	public $SearchByList = array(); // Para customizar la lista de buscar por

	//Mensajes
	/**
	 * @desc Mensaje -Nuevo Registro
	 * */
	public $Message_001 = 'Nuevo Registro';
	/**
	 * Mensaje -Registro guardado
	 * */
	public $Message_002 = 'Registro guardado correctamente';
	/**
	 * Mensaje -Error Registro no eliminado, existen referencias
	 * */
	public $Message_003 = 'Registro no eliminado, existen referencias';
	/**
	 * Mensaje -Error Registro no agregado, debido a que la referencia con otra tabla
	 *          no existe
	 * */
	public $Message_004 = 'Registro no agregado, debido a que la referencia con otra tabla no existe';
	/**
	 * Mensaje -Error Registro duplicado
	 * */
	public $Message_005 = 'Ya existe un registro con dicha informaci&oacute;n, no se puede duplicar';
	/**
	 * Mensaje -Registro eliminado
	 * */
	public $Message_006 = 'Registro eliminado';
	/**
	 * Mensaje - Registro copiado...
	 * */
	public $Message_007 = 'Registro copiado... modifique y guarde el nuevo registro ';

	/**
	 * Mensaje - Error de campos requeridos
	 * */
	public $Message_008 = '<b>Error:</b> Por favor, llena los campos obligatorios';



	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method __construct()
	 * */
	public function __construct($table='',$CodeName = ''){
		// Table name
		if($table!=''){
			$m_name = $table.'Form';
		}else{
			$m_name = get_class($this);
		}


		// Set Op
		if( isset($_POST['op']) ){
			$this->operation = decode($_POST['op']);
		}elseif(isset($_GET['op'])){
			$this->operation = decode($_GET['op']);
		}




		// No display header
		if(@$_POST['ajax'] == '1' || $this->operation=='export'){
			fk_no_display_header();
		}

		$m_name_tot = strlen($m_name);


		// Remove 'Form' word from ModelNameForm
		$this->model=substr($m_name,0,($m_name_tot-4));
			
		$this-> IdObject = self::getFormId($this-> model).$CodeName;
		$this-> ObjectCode = $CodeName;


		// Crear DbRecord para el Modelo
		$this->DbRecord = new db_record($this->model);

		$this->DbRecord->prefix_field = $this->IdObject;

		$this->sql_list = "SELECT * FROM  ".$this-> model;

		//id user
		$this->id_user = isset($_SESSION['id_usuario'])?$_SESSION['id_usuario']:0;


	} // Construct
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method updateRecord()
	 *@desc  Finds the Id record to Update
	 * */
	public function updateRecord($id,$urlRedirectAfterSave='',$urlRedirectAfterCancel='',$urlRedirectAfterDelete='',$edit_mode=true){
		$this->IdRecord = $id;
		$this->record_to_update = TRUE;
		$this->url_redirect_onSave = $urlRedirectAfterSave;
		$this->url_redirect_onCancel = $urlRedirectAfterCancel;
		$this->url_redirect_onDelete = $urlRedirectAfterDelete;
		$this->edit_mode = $edit_mode;

	} // find

	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method newRecord($fields_default_value)
	 *@desc  Prepares the form to add a new record
	 * */
	public function newRecord($default_form_values = array(),$urlRedirectAfterSave='',$urlRedirectAfterCancel='',$urlRedirectAfterDelete=''){

		$this->default_form_values = $default_form_values;
		$this->record_to_add_new = TRUE;
		$this->url_redirect_onSave = $urlRedirectAfterSave;
		$this->url_redirect_onCancel = $urlRedirectAfterCancel;
		$this->url_redirect_onDelete = $urlRedirectAfterDelete;


	} // find
	private function SetNewRecordFields(){



		foreach ($this->DbRecord->form_fields as $f=>$v){

			if(isset($this->default_form_values[$f])){
				$this->DbRecord-> fields[$f] = $this->default_form_values[$f];
			}else{
				$this->DbRecord-> fields[$f] = '';
			}
		}

	} // find
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method render()
	 *@desc  Returns the Record Form
	 *@example render($this->getCurrentUrl)
	 * */
	public function render($getCurrentUrl){




		//Not return if is not AppFormID

		if($this->remove_form==FALSE){
			if(isset($_POST['AppFormID'])){
				if($_POST['AppFormID']!=$this->IdObject){
					return 'Error: Invalid AppFormID '.$this->IdObject.' requerido ('.$_POST['AppFormID'].' enviado)';
				}
			}
		}else{
			// Forzar a usar update como operacion cuando navega next last....

			$arOpera = array('first','prev','next','last');

			if(in_array($this->operation, $arOpera)){
				$this->operation = 'preview-record';
			}

			if($this->operation==''){ $this->operation = 'preview-record';}

		}




		$rs =''; // Resultado

		$this->CurrentPage = $getCurrentUrl;

		$operation = $this->operation;

		// Set Id Record & record number

		if($this->remove_form==FALSE){
			if(isset($_POST[$this->IdObject.'-i'])){
				$this->IdRecord = $_POST[$this->IdObject.'-i'];
				$this->record_number = isset($_POST[$this->IdObject.'-i_n'])?$_POST[$this->IdObject.'-i_n']:1;
			}
		}



		// Field mode
		$this->DbRecord-> field_mode = ($this->FieldMode_EditView==TRUE ? 'view-edit':'update');

		//Default View as List or Form
		if($this->view_as_list == true){
			$this-> view_as = 'L';
		}else{
			$this-> view_as = 'F';
		}


		// Add new or update
		if($this-> record_to_add_new == TRUE || $this->record_to_update==TRUE){

			if(self::get_operation()=='save'){
				// Return to the url_redirect specified
				if($this->url_redirect_onSave!=''){echo fk_header_location_js($this->url_redirect_onSave);}

			}elseif (self::get_operation()=='cancel'){
				// Return to the url_redirect specified
				if($this->url_redirect_onCancel!=''){
					echo fk_header_location_js($this->url_redirect_onCancel);
				}
			}elseif(self::get_operation()=='del'){
				// Return to the url_redirect specified
				if($this->url_redirect_onDelete!=''){
					echo fk_header_location_js($this->url_redirect_onDelete);
				}
			}elseif(self::get_operation()==''){



				// Case add: prepare for new
				if($this->record_to_add_new==TRUE){
					$this->form_mode = 'edit';
					$this->SetNewRecordFields(); $operation='edit';
				}

				// Case update
				if($this->record_to_update==TRUE){
					if($this->edit_mode ==true){
						$this->form_mode = 'edit'; $operation='edit';
					}else{
						// Read only mode
						$this->DbRecord->find($this->IdRecord);
						$operation='';
					}


				}
			}

		}

		//set total records
		$this->set_total_records();
		if($this->total_records==0){

			$this->btn_delete_visible = false;
			$this->btn_search_visible = false;
			$this->btn_export_visible = false;
			$this->btn_edit_visible = false;
			$this->btn_duplic_visible = false;
			$this->record_number = 0;

		}


		// Creacion de opciones de select
		$this->createSelectRecords($operation);
		$this->createConsecutiveField($operation);

		

		switch ($operation) {
			case 'new':
				
				/*if( $this->save_from_new==true){
					$this->savEdit();
				}*/
				
				
				
				$this->form_mode = 'edit';
				$this->total_records++;
				$this->record_number = $this->total_records;
				$this->DbRecord-> find(0);
				// default values
				if($this-> record_to_add_new == TRUE){ $this->SetNewRecordFields();	}
				$this->btn_new_visible = FALSE;
				$this->btn_edit_visible = FALSE;
				$this->btn_duplic_visible = FALSE;
				$this->btn_delete_visible = FALSE;
				$this->btn_save_visible = TRUE;
				//$this->btn_savedit_visible = TRUE;
				$this->btn_cancel_visible = TRUE;
				$this->update_nav_buttons('disable-all');

				$rs .= __('<div class="alert alert-info">'.$this->Message_001.'</div>');
				$rs .=$this-> PrintView('form');

				break;
			case 'edit':

				// If record to add new = true do not find the IdRecord, and takes the input from defined vars
				if($this->record_to_add_new==false){ $this->DbRecord->find($this->IdRecord);}



				$this->form_mode = 'edit';
				$this->btn_new_visible = FALSE;
				$this->btn_edit_visible = FALSE;
				$this->btn_duplic_visible = FALSE;
				$this->btn_delete_visible = FALSE;
				$this->btn_save_visible = TRUE;
				//$this->btn_savedit_visible = TRUE;
				$this->btn_cancel_visible = TRUE;
				$this->update_nav_buttons('disable-all');
				$rs .=$this-> PrintView('form');
				break;
			case 'duplic':

				$this->DuplicateRecord();
				$this->form_mode = 'edit';
				$this->btn_new_visible = FALSE;
				$this->btn_edit_visible = FALSE;
				$this->btn_duplic_visible = FALSE;
				$this->btn_delete_visible = FALSE;
				$this->btn_save_visible = TRUE;
				//$this->btn_savedit_visible = TRUE;
				$this->btn_cancel_visible = TRUE;
				$this->update_nav_buttons('disable-all');
				$rs .= __('<div class="alert alert-info">'.$this->Message_007.'</div>');
				$rs .=$this-> PrintView('form');
				break;
			case 'save':
					
				$rs .= $this->SaveRecord();

				if($this->view_as_list){
					$rs .=$this-> PrintView('list');
				}else{
					// Refresh data
					$this->update_nav_buttons();
					$rs .=$this-> PrintView('form');

				}



				break;
			case 'savedit':

				$rs .= $this->SavEdit();

				break;
			case 'del':

				$rs .= $this->DeleteRecord();


				$this->record_number = 1;
				$this->IdRecord = $this->get_id_record($this->record_number);
				$this->DbRecord-> find($this->IdRecord);
				$this->set_total_records();
				if($this->total_records==0){
					$this->btn_delete_visible = false;
					$this->btn_search_visible = false;
					$this->btn_export_visible = false;
					$this->btn_edit_visible = false;
					$this->btn_duplic_visible = false;
					$this->record_number = 0;
					$this->update_nav_buttons('disable-all');
				}
				$this->update_nav_buttons('');
				$this->DbRecord-> find($this->IdRecord,'first');
				if(fk_post('viewas')=='list'){
					$rs .=$this-> PrintView('list');
				}else{
					$rs .=$this-> PrintView('form');
				}


				break;
			case 'view-1':
				if($this-> record_to_update == FALSE){
					$this->record_number = 1;
					$this->IdRecord = $this->get_id_record($this->record_number);
					$this->DbRecord-> find($this->IdRecord);
				}

				$this->update_nav_buttons('disable-all');

				$rs .=$this-> PrintView('form');
				break;
			case 'view-2':
				$rs .=$this-> PrintView('list');
				break;
			case 'fill-list':
				$rs .=$this-> generateDatatableJSON();
				break;
			case 'export':
				$rs .=$this->ExportToExcel();
				break;

			default:
			case 'first':
				$this->record_number = 1;
				$this->IdRecord = $this->get_id_record($this->record_number);
				$this->DbRecord-> find($this->IdRecord);

				$this->update_nav_buttons();

				$rs .=$this-> PrintView('form');
				break;
			case 'prev':
				$this->record_number = $this->record_number - 1;
				$this->IdRecord = $this->get_id_record($this->record_number);
				$this->DbRecord-> find($this->IdRecord);

				$this->update_nav_buttons();

				$rs .=$this-> PrintView('form');
				break;
			case 'next':
				$this->record_number = $this->record_number + 1;
				$this->IdRecord = $this->get_id_record($this->record_number);
				$this->DbRecord-> find($this->IdRecord);

				$this->update_nav_buttons();

				$rs .=$this-> PrintView('form');
				break;
			case 'last':
				$this->record_number = $this->total_records;
				$this->IdRecord = $this->get_id_record($this->record_number);
				$this->DbRecord-> find($this->IdRecord);

				$this->update_nav_buttons();

				$rs .=$this-> PrintView('form');
				break;
			case 'search':
				$rs .= $this-> PrintSeach();
				break;
			case 'search-results':
				$rs .= $this-> PrintSeachResults();
				break;
			case 'selec-record':
				$this->DbRecord-> find($_POST['rec-srch']);
				$rs .=$this-> PrintView('form');
				break;
			case 'preview-record':

				$this->DbRecord-> find($this->IdRecord);
				$rs .=$this-> PrintView('form');
				break;

			case 'cancel':

				if($this->view_as_list){
					$rs .=$this-> PrintView('list');
				}else{
					// Search the record id of record number only when is no update mode
					if($this->record_to_update==FALSE){$this->IdRecord = $this->get_id_record($this->record_number);}

					$this->DbRecord-> find($this->IdRecord);
					$this->update_nav_buttons();
					$rs .=$this-> PrintView('form');
				}


				break;

			default:



				// Print Form
				if($this-> record_to_update == FALSE){
					$this->record_number = 1;
					$this->IdRecord = $this->get_id_record($this->record_number);
					$this->DbRecord-> find($this->IdRecord);

				}
				if($this-> record_to_add_new == TRUE){
					$this->SetNewRecordFields();
				}

				$this->update_nav_buttons();

				if($this->view_as=="F"){
					$rs .=$this-> PrintView('form');
				}else{
					$rs .=$this-> PrintView('list');
				}

				break;
		} // End switch



		return $rs;

	} // render()

	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method SaveRecord()
	 *@desc  Saves the record
	 * */
	private function SaveRecord(){


			


		// si es update; obtener los datos del registro
		// para que si no vienen no se remplacen por vacios
		if(isset($_POST[$this->IdObject.'-i']) && trim($_POST[$this->IdObject.'-i'])!= '' && $_POST[$this->IdObject.'-i'] > 0){
			$this->DbRecord->find($_POST[$this->IdObject.'-i']);
			$is_update = true;

		}else{
			$is_update = false;
		}


		
		
		

		//Update Record
		foreach($this->DbRecord->form_fields as $f=>$arr ){

			
			$is_required = FALSE;
			$is_email = FALSE;
			
			
			//Validate
			if(isset($this->ValidationFields[$arr['Field']])){

				if(isset($this->ValidationFields[$arr['Field']]['required'])){
					$is_required = TRUE;
				}
			}

			$k=$arr['Field'];
			if($this->encode_fields==TRUE){
				$k_post_code = encode($k.$this->ObjectCode);
			}else{
				$k_post_code = $k.$this->ObjectCode;
			}
			$post_value =  isset($_POST[$k_post_code])?$_POST[$k_post_code]:'';
			$post_value = utf8_decode($post_value);


			//Null o value
			if(trim($post_value)==''){
				IF($this->DbRecord->form_fields[$k]['Null'] == 'YES'){
					$post_value = NULL;
				}
				if ($is_required){ echo fk_alert_message($this->Message_008,false); die();}
			}



			if( isset($_POST[$k_post_code])){
				$this-> DbRecord -> fields[$k]=$post_value;
			}else{
				if($this->DbRecord->form_fields[$k]['Key']=='PRI'){
					$this-> DbRecord -> fields[$k]=$this->IdRecord;
				}else{
					// Si no existe y no es update; crear nuevo con valores de form
					if( !isset($this-> DbRecord -> fields[$k]) && ($is_update == false)  ){
						$this-> DbRecord -> fields[$k]=$post_value;
					}
				}

			}

		} //endforeach;



			
			

		// si es update
		if($is_update==true){
			$res_exec = $this-> DbRecord -> update();

			if($res_exec==false){
				$this->db_error_code =  $this->DbRecord->error_code;
			}

			$id_record = $_POST[$this->IdObject.'-i'];
		}else{
			// si es insert
			$res_exec = $this-> DbRecord -> insert();

			if($res_exec==false){
				$this->db_error_code =  $this->DbRecord->error_code;
			}

			if($res_exec==true){
				$this->DbRecord->fields[$this->DbRecord->id_field_name] = $this-> DbRecord -> inserted_id();
				$id_record = $this-> DbRecord -> inserted_id();
				$_SESSION['appform:last-inserted-id'][$this->model] = $id_record;

				// move to inserted record
				$this->set_total_records();
				$this->record_number = $this->total_records;
			}

		}

		// Actualizar el id record
		$this->IdRecord = $id_record;

		// Actualizar con los datos de la db
		if($res_exec==true){
			$this-> DbRecord->find($id_record);
		}

		if($res_exec==true){
			echo fk_ok_message($this->Message_002,$this->autoclose_messages);
		}else{
			$msg=$this->get_handled_errors_message($this->db_error_code);
			echo fk_alert_message_dialog($msg,FALSE);
		}

			
	} // End SaveRecord

	public static function getLastInsertedId($tableName){
		return isset($_SESSION['appform:last-inserted-id'][$tableName])?$_SESSION['appform:last-inserted-id'][$tableName]:'';
	}

	/**
	 *@package AppForm
	 *@since v0.1
	 *@method DuplicateRecord()
	 *@desc  Duplicates the record
	 * */
	private function DuplicateRecord(){
			
		//Update Record
		foreach($this->DbRecord->form_fields as $f=>$arr ):

		$k=$arr['Field'];
		if($this->encode_fields==TRUE){
			$k_post_code = encode($k);
		}else{
			$k_post_code = $k;
		}

		$post_value =  isset($_POST[$k_post_code])?$_POST[$k_post_code]:'';
		$post_value = utf8_decode($post_value);

		if( isset($_POST[$k_post_code])){
			$this-> DbRecord -> fields[$k]=$post_value;
		}else{
			if($this->DbRecord->form_fields[$k]['Key']=='PRI'){
				$this-> DbRecord -> fields[$k]=0;
			}else{
				// Si no existe crear nuevo con valores de form
				if( !isset($this-> DbRecord -> fields[$k]) ){
					$this-> DbRecord -> fields[$k]=$post_value;
				}
			}

		}

		endforeach;

		//set empty value for hidden "i"
		$this->DbRecord->fields[$this->DbRecord->id_field_name]=0;

	} //DuplicateRecord
	/**
	 *@package AppForm
	 *@since v0.1
	 *@method DeleteRecord()
	 *@desc  Deletes the record
	 * */
	private function DeleteRecord(){
			
		$this->DbRecord-> find($this->IdRecord);
		$res_exec = $this->DbRecord->delete();


		if($res_exec==false){
			$this->db_error_code =  $this->DbRecord->error_code;
		}

		if($res_exec==true){
			echo fk_ok_message($this->Message_006);
		}else{
			$msg=$this->get_handled_errors_message($this->db_error_code);
			echo fk_alert_message_dialog($msg,FALSE);
		}

			
	} // End DeleteRecord

	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method PrintToolBar()
	 *@desc  Returns the toolbar
	 * */
	private function PrintToolBar(){

		$toolBar = '';

		// Save Cancel Butons
		$fx_view_1 = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('view-1').'&AppFormID='.$this-> IdObject.';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_view_2 = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('view-2').'&AppFormID='.$this-> IdObject.';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_export = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('export').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_new = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('new').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_edit = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('edit').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_duplic = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('duplic').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_save = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('save').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_savedit = new ajax('submit', 'pannel-2-'.$this-> IdObject, 'args:op='.encode('savedit').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_cancel = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('cancel').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_del = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('del').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_first = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('first').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_prev = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('prev').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_next = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('next').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_last = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('last').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_search = new ajax('submit', 'pannel-srch-'.$this-> IdObject, 'args:op='.encode('search').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);

		// Enabled disabled
		$disabled['view']='';
		$disabled['export']='';
		$disabled['new']='';
		$disabled['edit']='';
		$disabled['duplic']='';
		$disabled['save']='';
		$disabled['savedit']='';
		$disabled['cancel']='';
		$disabled['delete']='';
		$disabled['first']='';
		$disabled['prev']='';
		$disabled['next']='';
		$disabled['last']='';
		$disabled['search']='';

		$di_css['view']='';
		$di_css['export']='';
		$di_css['new']='';
		$di_css['edit']='';
		$di_css['duplic']='';
		$di_css['save']='';
		$di_css['savedit']='';
		$di_css['cancel']='';
		$di_css['delete']='';
		$di_css['first']='';
		$di_css['prev']='';
		$di_css['next']='';
		$di_css['last']='';
		$di_css['search']='';


		if($this->btn_view_disabled == true){  $disabled['view']='disabled="true"'; $di_css['view']='disabled';	}
		if($this->btn_export_disabled == true){  $disabled['export']='disabled="true"'; $di_css['export']='disabled';	}
		if($this->btn_new_disabled == true){  $disabled['new']='disabled="true"'; $di_css['new']='disabled';	}
		if($this->btn_edit_disabled == true){  $disabled['edit']='disabled="true"';  $di_css['edit']='disabled';	}
		if($this->btn_duplic_disabled == true){  $disabled['duplic']='disabled="true"';  $di_css['duplic']='disabled';	}
		if($this->btn_save_disabled == true){  $disabled['save']='disabled="true"';  $di_css['save']='disabled';	}
		if($this->btn_savedit_disabled == true){  $disabled['savedit']='disabled="true"';  $di_css['savedit']='disabled';	}
		if($this->btn_cancel_disabled == true){  $disabled['cancel']='disabled="true"';  $di_css['cancel']='disabled';	}
		if($this->btn_delete_disabled == true){  $disabled['delete']='disabled="true"';  $di_css['delete']='disabled';	}
		if($this->btn_first_disabled == true){  $disabled['first']='disabled="true"';  $di_css['first']='disabled';	}
		if($this->btn_prev_disabled == true){  $disabled['prev']='disabled="true"';  $di_css['prev']='disabled';	}
		if($this->btn_next_disabled == true){  $disabled['next']='disabled="true"';  $di_css['next']='disabled';	}
		if($this->btn_last_disabled == true){  $disabled['last']='disabled="true"';  $di_css['last']='disabled';	}
		if($this->btn_search_disabled == true){  $disabled['search']='disabled="true"';  $di_css['new']='disabled';	}


		// Render butons

		//view
		if($this->btn_view_visible==TRUE){
			if($this->view_as == 'F'){
				$this->btn_view = '
				<div class="nav-bar ui-corner-all btn-group right">
 <a href="javascript:void(0)" class="btn btn-primary active view-as-form" alt="Formulario" title="Formulario" onclick="'. $fx_view_1->render().'" > <i class="fa fa-list-alt"></i> </a>
 <a href="javascript:void(0)" class="btn btn-primary view-as-list" alt="Listado" title="Listado" onclick="'. $fx_view_2->render().'"> <i class="fa fa-bars"></i></a>
</div><div class="clear"></div>
			                ';
			}else{
				$this->btn_view = '
				<div class="nav-bar ui-corner-all btn-group right">
 <a href="javascript:void(0)" class="btn btn-primary view-as-form" alt="Formulario" title="Formulario" onclick="'. $fx_view_1->render().'" > <i class="fa fa-list-alt"></i></a>
 <a href="javascript:void(0)" class="btn btn-primary active view-as-list" alt="Listado" title="Listado" onclick="'. $fx_view_2->render().'"> <i class="fa fa-bars"></i></a>
</div>
<div class="clear"></div>
			                ';
			}

		}
		//export
		if($this->btn_export_visible==TRUE){
			$this->btn_export = '<input class="btn btn-primary export '.@$di_css['export'].'" type="button" value="Exportar .xls" onclick="window.open(\''.fk_link().$this->CurrentPage.'&op='.encode('export').'\')" '.$disabled['export'].'>';
			//$this->btn_export = '<img class="btn export '.@$di_css['export'].'" '.$disabled['export'].' style="width:35px;display:inline-tabl;" src="'.HTTP.'/_HTML/img/export_ico.jpg" title="Exportar excel" onclick="window.open(\''.fk_link().$this->CurrentPage.'&op='.encode('export').'\')" >';

		}

		//new
		if($this->btn_new_visible==TRUE){
			$this->btn_new ='<input class="btn btn-primary new '.$di_css['new'].'" type="button" value="'. __('Nuevo').'" onclick="'. $fx_new->render().'" '.$disabled['new'].'>';
		}

		//edit
		if($this->btn_edit_visible==TRUE){
			$this->btn_edit ='<input class="btn btn-info edit '.$di_css['edit'].'" type="button" value="'. __('Editar').'" onclick="'. $fx_edit->render().'" '.$disabled['edit'].'>';
		}

		//duplic
		if($this->btn_duplic_visible==TRUE){
			$this->btn_duplic ='<input class="btn btn-primary duplic '.$di_css['duplic'].'" type="button" value="'. __('Duplicar').'" onclick="'. $fx_duplic->render().'" '.$disabled['duplic'].'>';
		}

		//save
		if($this->btn_save_visible==TRUE){
			if($this->use_template==true){
				$this->btn_save ='<input class="btn btn-success save '.$di_css['save'].'" type="submit" value="'. __('Guardar').'" '.$disabled['save'].'>';
			}else{
				$this->btn_save ='<input class="btn btn-success save '.$di_css['save'].'" type="submit" value="'. __('Guardar').'" '.$disabled['save'].'>';
				//$this->btn_save ='<input class="btn ui-corner-all '.$di_css['save'].'" type="button" value="'. __('Guardar').'" onclick="'. $fx_save->render().'" '.$disabled['save'].'>';
			}

		}
		//savedit
		if($this->btn_savedit_visible==TRUE){

			$this->btn_savedit ='<input class="btn btn-success save autosave '.$di_css['savedit'].'" type="button" value="'. __('Guardar y continuar').'" onclick="'. $fx_savedit->render().'" '.$disabled['savedit'].'>';

			//$this->btn_save ='<input class="btn btn-success save '.$di_css['savedit'].'" type="submit" value="'. __('Guardar y continuar').'" '.$disabled['savedit'].'>';
			//$this->btn_save ='<input class="btn btn-primary ui-corner-all '.$di_css['save'].'" type="button" value="'. __('Guardar').'" onclick="'. $fx_save->render().'" '.$disabled['save'].'>';
		}

		//cancel
		if($this->btn_cancel_visible==TRUE){
			$this->btn_cancel ='<input class="btn btn-default cancel '.$di_css['cancel'].'" type="button" value="'. __('&laquo; Regresar').'" onclick="'. $fx_cancel->render().'" '.$disabled['cancel'].'>';
		}

		//delete
		if($this->btn_delete_visible==TRUE){
			$this->btn_delete ='<input class="btn btn-danger delete '.$di_css['delete'].'" type="button" value="'. __('Eliminar').'" onclick="if(confirm(\''.__('Eliminar Registro?').'\')){'. $fx_del->render().'}" '.$disabled['delete'].'>';
		}

		//first
		if($this->btn_first_visible==TRUE){
			$this->btn_first ='<input class="btn btn-primary first '.$di_css['first'].'" type="button" value="|&laquo;" onclick="'. $fx_first->render().'" '.$disabled['first'].'>';
		}

		//prev
		if($this->btn_prev_visible==TRUE){
			$this->btn_prev ='<input class="btn btn-primary prev '.$di_css['prev'].'" type="button" value="&laquo;" onclick="'. $fx_prev->render().'" '.$disabled['prev'].'>';

		}

		// records_of
		if($this->records_of_visible==TRUE){
			$oper = isset($_POST['op'])? decode($_POST['op']):'';
			if($oper=='selec-record'){
				$this->records_of = '<div class="btn btn-default">&nbsp;- -&nbsp;</div>';
			}else{
				$this->records_of = ' <div class="btn btn-default">'.$this->record_number.' de '.$this->total_records.'</div>';
			}
		}



		//next
		if($this->btn_next_visible==TRUE){
			$this->btn_next ='<input class="btn btn-primary next '.$di_css['next'].'" type="button" value="&raquo;" onclick="'. $fx_next->render().'" '.$disabled['next'].'>';
		}

		//last
		if($this->btn_last_visible==TRUE){
			$this->btn_last ='<input class="btn btn-primary last '.$di_css['last'].'" type="button" value="&raquo;|" onclick="'. $fx_last->render().'" '.$disabled['last'].'>';
		}

		//search
		if($this->btn_search_visible==TRUE){
			//$this->btn_search ='<input class="btn btn-primary search '.$di_css['search'].'" type="button" value="'. __('Buscar').'" onclick="'. $fx_search->render().'" '.$disabled['search'].'>';
			$this->btn_search ='';
		}

		$toolBar .= '<div class="TopToolBar ">';
		$toolBar .= $this->btn_view;


		$toolBarDv = $this->records_of;
		$toolBarDv .= $this->btn_first;
		$toolBarDv .= $this->btn_prev;

		$toolBarDv .= ' '.$this->btn_next;
		$toolBarDv .= $this->btn_last;
		$toolBarDv .= '  '.$this->btn_search;
		$toolBarDv .= ' '.$this->btn_export;

		$toolBarDv = trim($toolBarDv);

		if($toolBarDv!=' '){
			$toolBar .='<div class="nav-bar ui-corner-all">';
			$toolBar .= ''.$toolBarDv.'';
			$toolBar .='</div>';
		}




		$toolBar .='<div class="btn-bar">';
		$toolBar .= ' '.$this->btn_new;
		$toolBar .= ' '.$this->btn_edit;
		$toolBar .= ' '.$this->btn_duplic;
		$toolBar .= ' '.$this->btn_save;
		$toolBar .= ' '.$this->btn_savedit;
		$toolBar .= ' '.$this->btn_cancel;
		$toolBar .= ' '.$this->btn_delete;
		$toolBar .='</div>';
		$toolBar .='</div>'; // TopToolBar

		return $toolBar;

	}
	/**
	 *@package AppForm
	 *@since v0.1
	 *@method PrintView(list|form)
	 *@desc  Returns the Form or Data List regarding $type parametter
	 * */
	private function PrintView($type = 'form'){
		$html = '';
		if($type=='form'){
			$this-> view_as = 'F';
			$html = $this->PrintForm();
		}elseif($type=='list'){
			$this-> view_as = 'L';
			$this->btn_delete_visible = false;
			$this->btn_edit_visible = false;
			$this->btn_duplic_visible = false;
			$this->btn_save_visible = false;
			//$this->btn_savedit_visible = false;
			$this->btn_cancel_visible = false;
			$this->btn_first_visible = false;
			$this->btn_prev_visible = false;
			$this->btn_next_visible = false;
			$this->btn_last_visible = false;
			$html = $this->PrintList();
		}
		// Add contenetors
		$html_data = '';
		$html_data ='<div id="pannel-1-'.$this->IdObject.'" class="appform">';
		$html_data .= '<div id="pannel-2-'. $this-> IdObject.'"></div>';
		$html_data .= '<div id="pannel-srch-'.$this-> IdObject.'"></div>';
		$html_data .= $html;
		$html_data .='</div>'; // Pannel-1


		return $html_data;

	}
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method PrintForm()
	 *@desc  Returns the Form
	 * */
	private function PrintForm(){

		// Field mode

		if($this->use_template==TRUE){
			$form = $this-> PrintTemplateForm();
		}else{
			$form = $this-> PrintCommonForm();
		}
		return $form;
	}
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method PrintTemplateForm()
	 *@desc  Returns the Form from a given template
	 * */
	private function PrintTemplateForm(){

		if($this->custom_submit==true){
			$action_sbmt = $this-> submit_url;
			$method = $this-> submit_method;
		}else{
			$action_sbmt = $this-> CurrentPage;
			$method = 'POST';
		}

		$form = '';


		if($this->multipartForm==TRUE){
			$multipart='enctype="multipart/form-data"';
		}else{$multipart='';}

		if($this->remove_form==FALSE){
			$form .= '<form id="'. $this-> IdObject.'" name="'. $this-> IdObject.'" '.$multipart.' action="'.$action_sbmt.'" method="'.$method.'" class="form cmxform" >';
		}

		// Print ToolBar
		$toolbar = $this->PrintToolBar();
		$this->PutTemplateContent('{appform:toolbar}', $toolbar);





		// Process Fields
		$this->ProcessFields();

		// Print Buttons

		$this->PutTemplateContent('{appform:btn-view}', $this->btn_view);
		$this->PutTemplateContent('{appform:btn-new}', $this->btn_new);
		$this->PutTemplateContent('{appform:btn-edit}', $this->btn_edit);
		$this->PutTemplateContent('{appform:btn-duplic}', $this->btn_duplic);
		$this->PutTemplateContent('{appform:btn-save}', $this->btn_save);
		$this->PutTemplateContent('{appform:btn-savedit}', $this->btn_savedit);
		$this->PutTemplateContent('{appform:btn-cancel}', $this->btn_cancel);
		$this->PutTemplateContent('{appform:btn-delete}', $this->btn_delete);
		$this->PutTemplateContent('{appform:btn-first}', $this->btn_first);
		$this->PutTemplateContent('{appform:btn-prev}', $this->btn_prev);
		$this->PutTemplateContent('{appform:records-of}', $this->records_of);
		$this->PutTemplateContent('{appform:btn-next}', $this->btn_next);
		$this->PutTemplateContent('{appform:btn-last}', $this->btn_last);
		$this->PutTemplateContent('{appform:btn-search}', $this->btn_search);
		$this->PutTemplateContent('{appform:btn-export}', $this->btn_export);

		// hidden ids
		$form .= '<input type="hidden" value="'.@$this->DbRecord->fields[$this->DbRecord->id_field_name].'" name="'.$this->IdObject.'-i" id="'.$this->IdObject.'-i">';
		$form .= '<input type="hidden" value="'.$this->record_number.'" name="'.$this->IdObject.'-i_n" id="'.$this->IdObject.'-i_n">';
		if($this->remove_form==FALSE){
			$form .= '<input type="hidden" value="'.$this->IdObject.'" name="AppFormID">';
		}

		// Execute extraccion Template
		$templateProcessed = '';
		$templateProcessed = str_replace($this-> ArrSearchTpl, $this->ArrReplaceTpl, $this->template);
		$form .= $templateProcessed;

		if($this->remove_form==FALSE){
			$form .='</form>';
		}
		// Validation & Masks
		// Form ajax con validate incluido
		if($this->custom_submit==true){
			$AjaxSubmitForm = '<script type="text/javascript">$("#'.$this-> IdObject.'").validate()</script>';
		}else{
			$fxSubmit = new ajax_submit();
			$fxSubmit -> Form($this-> IdObject);
			$fxSubmit -> ResponseLocation('pannel-1-'.$this->IdObject);
			$fxSubmit -> Args('op='.encode('save'));
			$fxSubmit -> Url($this->CurrentPage);
			$AjaxSubmitForm=$fxSubmit->Render();
		}



		// Ajax y js
		$form .='
         <script language="javascript" type="text/javascript" >
	 	 jQuery(function($){
	 		'.$this-> PrintValidationMask().'
	 		'.$this->PrintAutocompleteFields().'
	 		'.$this->ProcessAutoSaveJs().'
	 	});
	 	</script>'.'
	 	'.$AjaxSubmitForm.'';




		// Array de variables enviado por funcion
		if(count($this->ArrData)>0){
			foreach ($this->ArrData as $k => $v){
				$$k = $v;
			}
		}

		// Returns result
		ob_start();
		eval('?>' . $form . '<?php ');
		echo $ViewResult = ob_get_contents();
		ob_end_clean();

		return $ViewResult;

	} // PrintTemplateForm

	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method PrintCommonForm()
	 *@desc  Returns the Form without layout
	 * */
	private function PrintCommonForm(){

		$form = '';



		if($this->remove_form==FALSE){
			$form .= '<form id="'. $this-> IdObject.'" name="'. $this-> IdObject.'" class="form">';
		}

		// Print ToolBar on TOP
		if($this-> toolbar_visible == true && $this->toolbar_position == 'top'){
			$form .= $this->PrintToolBar();
		}

		$form .= '<table class="app-tbl" cellspacing="0" cellpadding="0">';

		// Print the form fields
		$form .= $this->ProcessFields();


		$form .= '</table>';

		// hidden ids
		$form .= '<input type="hidden" value="'.@$this->DbRecord->fields[$this->DbRecord->id_field_name].'" name="'.$this->IdObject.'-i" id="'.$this->IdObject.'-i">';
		$form .= '<input type="hidden" value="'.$this->record_number.'" name="'.$this->IdObject.'-i_n" id="'.$this->IdObject.'-i_n">';
		if($this->remove_form==FALSE){
			$form .= '<input type="hidden" value="'.$this->IdObject.'" name="AppFormID">';
		}





		// Print ToolBar on BOTTOM
		if($this-> toolbar_visible == true && $this->toolbar_position == 'bottom'){
			$form .= $this->PrintToolBar();
		}

		if($this->remove_form==FALSE){
			$form .='</form>';
		}

		// Ajax Submit
		$fxSubmit = new ajax_submit();
		$fxSubmit -> Form($this-> IdObject);
		$fxSubmit -> ResponseLocation('pannel-1-'.$this->IdObject);
		$fxSubmit -> Args('op='.encode('save'));
		$fxSubmit -> Url($this->CurrentPage);
		$AjaxSubmitForm=$fxSubmit->Render();

		// Validation Masks
		$form .='';
		$form .='<script language="javascript" type="text/javascript" >
	 	 jQuery(function($){
	 		'.$this-> PrintValidationMask().'
	 		'.$this->PrintAutocompleteFields().'
	 	});
	 	</script>';
		$form .=$AjaxSubmitForm;

		return $form;

	} // PrintCommonForm

	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method PrintSearch()
	 *@desc  Returns the search Form
	 * */
	private function PrintSeach(){

		//$fx_find = new ajax('submit', 'pannel-srch-res-'.$this-> IdObject, 'args:op='.encode('search-results').';form:fsrch-'.$this-> IdObject.';url:'.$this->CurrentPage);
		$fx_find = new ajax_submit();
		$fx_find-> Args('op='.encode('search-results'));
		$fx_find-> Form('fsrch-'.$this-> IdObject);
		$fx_find-> ResponseLocation('pannel-srch-res-'.$this-> IdObject);
		$fx_find->Url($this->CurrentPage);

		$arrayFields = array();

		foreach ($this->DbRecord->form_fields as $f){

			if(!isset($this->Labels[$f['Field']])){
				$this->Labels[$f['Field']] = ucwords(strtolower(strtr($f['Field'], '_', ' ')));
			}

			$arrayFields[$f['Field']] = $this->Labels[$f['Field']];
		}

		//Si hay utilizar la lista existente
		//si no hay SearchByList utilizar el default
		if(count($this->SearchByList)==0){
			$this->SearchByList=$arrayFields;
		}

		$sFrm = '<fieldset style="background:#fff;">
<legend>  B&uacute;squeda </legend>
<form id="fsrch-'. $this-> IdObject.'" name="fsrch-'. $this-> IdObject.'" action="'.$this->CurrentPage.'" method="POST" >
<table border="0"  >
  <tbody>
  <tr>
    <th scope="row">Por:</th>
    <td>
      <select name="srch-by-field" id="src-field">
      <option value="0"> Cualquier campo </option>
      '.fk_select_options_r($this->SearchByList).'
      </select>
    </td>
    <td><input id="srcwrd-'.$this->IdObject.'" name="srch-word" type="text"></td>
    <td><input name="srch-btn" type="submit" value="Ir &raquo;" class="btn" > <a href="javascript:void(0)" onclick="$('.$this->IdObject.').show();$(\'#pannel-srch-'.$this-> IdObject.'\').html(\'\')">Cancelar</a></td>
  </tr>
  </tbody>
</table>
</form>
<div id="pannel-srch-res-'.$this-> IdObject.'"></div>
</fieldset><script> $("#srcwrd-'.$this->IdObject.'").focus(); $('.$this->IdObject.').hide();</script>'.$fx_find-> Render();

		return $sFrm;

	} // End PrintSeach

	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method PrintSearchResult()
	 *@desc  Returns the search result
	 * */
	private function PrintSeachResults(){


		$db=new db();
		$db->connect();
		$proyection_fields = $this->SearchCustomFields;
		if(trim($this->SearchCustomFields)!='*'){
			$proyection_fields = $this->DbRecord->id_field_name.' as Id, '.$this->SearchCustomFields;
		}

		//pa($this->DbRecord-> form_fields);

		$where = '';
		if($_POST['srch-by-field']=='0'){
			$where_fields = '';
			foreach ($this->DbRecord-> form_fields as $f){
				$where_fields .= $f['Field'].' like "%'.$_POST['srch-word'].'%" OR ';
			}
			$where_fields = substr($where_fields, 0,-3);
			$where = ' WHERE ('.$where_fields.')';
		}else{
			$where = ' WHERE ('.$_POST['srch-by-field'].' like "%'.$_POST['srch-word'].'%") ';
		}

		$sqlSrch = 'SELECT '.$proyection_fields.' FROM '.$this-> model.' '.$where.' '.$this->DbRecord->SqlAnd.' ';
		$db->query_assoc($sqlSrch);

		$data_rs = '';
		$tot = $db->num_rows();
		if($tot==0){
			$data_rs .= fk_alert_message($tot.' resultados',false);

		}else{
			$data_rs .= fk_ok_message($tot.' resultados',false);
		}

		$data_rs .= '<table class="tbl-1" style="width:100%" border="0" cellspacing="0" cellpadding="0" >';
		$cnt = 0;
		while($rows = $db->next()){
			$cnt++;
			//pa($rows);
			if($cnt%2==0){
				$even_odd = 'even';
			}else{
				$even_odd = 'odd';
			}

			if($cnt==1){
				// Header
				$data_rs .= '<tr><th>&nbsp</th>';
				foreach ($rows as $col=>$val){
					$data_rs .= '<th>'.ucwords(strtolower(strtr($col, '_', ' '))).'</th>';
				}
				$data_rs .= '</tr>';
			}
			if(trim($this->SearchCustomFields)!='*'){
				$id_record = $rows['Id'];
			}else{$id_record = $rows[$this->DbRecord->id_field_name];}

			$fx_sel_rec = new ajax('url', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('selec-record').'&rec-srch='.$id_record.';form:'.$this-> IdObject.';url:'.$this->CurrentPage);


			$data_rs .= '<tr class="'.$even_odd.'" onclick="'.$fx_sel_rec->render().'"><td><a href="javascript:void(0)">Ver</a></td>';
			foreach ($rows as $col=>$val){
				$data_rs .= '<td>'.$val.'</td>';
			}
			$data_rs .= '</tr>';


		}
		$data_rs .= '</table>';

		echo $data_rs;

	} // End PrintSeachResults()

	/**
	 *@package AppForm
	 *@since v0.1
	 *@method PrintList()
	 *@desc  Returns the records as a table
	 * */
	private function PrintListOld(){

		$html = '';

		$db = new db();
		$db->connect();

		$db->query_assoc($this->sql_list);

		$row_cnt = 0;
		$tot_cols = 0;

		// Toolbar
		$toolbar = $this->PrintToolBar();

		$html .= $this->title;
		$html .= $toolbar;

		$html.= '<table cellpadding="0" cellspacing="0" border="0" class="tbl-list display" id="lst-'.$this->IdObject.'">';
		while($rec=$db->next()){


			$row_cnt++;
			if($row_cnt==1){
				$tot_cols = count($rec);
				//Llenar las columnas
				$html .='<thead><tr>';

				$html.= '<th>&nbsp;</th>';
				if( $this->btn_edit_disabled==FALSE ){ $html.= '<th>&nbsp;</th>';}
				if( $this->btn_delete_disabled==FALSE ){ $html.= '<th>&nbsp;</th>';}
					
				$cnt_cols = 0;
				foreach ($rec as $k => $v){
					$cnt_cols++;
					if($cnt_cols>1){
						$html.= '<th>'.ucwords(strtolower(strtr($k, '_', ' '))).'</th>';
					}

				}



				$html .='</tr></thead>
			  <tbody>';
			}
			$id_field_name = $this->DbRecord->id_field_name ;



			//$html.= '<tr onclick="'.$fx_sel_rec->render().'">';
			$html.= '<tr>';

			$fx_sel_rec = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('selec-record').'&rec-srch='.$rec[$id_field_name].';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
			$html.= '<td><a href="javascript:void(0)" onclick="'.$fx_sel_rec->render().'"><img src="'.HTTP.'/_HTML/img/ico_view.png"></a></td>';

			// BTN EDIT
			if( $this->btn_edit_disabled==FALSE ){
				$fx_edit_rec = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('edit').'&i='.$rec[$id_field_name].';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
				$html.= '<td><a href="javascript:void(0)" onclick="'.$fx_edit_rec->render().'"><img src="'.HTTP.'/_HTML/img/ico_edit.png"></a></td>';
			}

			// BTN DELETE
			if($this->btn_delete_disabled==FALSE ){
				$fx_del_rec = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('del').'&i='.$rec[$id_field_name].'&viewas=list;form:'.$this-> IdObject.';url:'.$this->CurrentPage);
				$html.= '<td><a href="javascript:void(0)" onclick="if(confirm(\''.__('Eliminar Registro?').'\')){'.$fx_del_rec->render().';}"><img src="'.HTTP.'/_HTML/img/ico_delete.png"></a></td>';
			}


			$cnt_cols = 0;
			foreach ($rec as $k => $v){
				$cnt_cols ++;
				if($cnt_cols>1){ $html.= '<td>'.utf8_encode($rec[$k]).'</td>'; }
			}


			$html.= '</tr>';



		}
		$html.= '</tbody></table>';

		//
		$fx_table = new ajax('submit', 'pannel-1-'.$this-> IdObject, 'args:op='.encode('fill-list').';form:'.$this-> IdObject.';url:'.$this->CurrentPage);
		$html.= '<script>
		  $(document).ready(function(){
		  $("#lst-'.$this->IdObject.'").dataTable( {
		 "sPaginationType": "full_numbers",
		 "bJQueryUI": false,
		 "iDisplayLength": 100,
		 "aaSorting": [[0,"asc"]]
		 } );
});</script>
		';
		/*$("#lst-'.$this->IdObject.'").dataTable({
		 "bJQueryUI": true,
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": HTTP+"'.$this->CurrentPage.'",
			"sPaginationType": "full_numbers",
			"aaSorting": [[ 0, "desc" ]],
			"iDisplayLength": 10,
			"fnServerData": function ( sSource, aoData, fnCallback ) {
			// Add some extra data to the sender
			aoData.push( { "name": "op", "value": "'.encode('fill-list').'" } );
			$.getJSON( sSource, aoData, function (json) {
			// Do whatever additional processing you want on the callback, then tell DataTables
			fnCallback(json);
			} );
			}
			} );*/
		/* */


		// Returns result
		ob_start();
		eval('?>' . $html . '<?php ');
		echo $ViewResult = ob_get_contents();
		ob_end_clean();

		return $ViewResult;

	} // PrintList

	/**
	 *@package AppForm
	 *@since v0.1
	 *@method PrintList()
	 *@desc  Returns the records as a table
	 * */
	private function PrintList(){



		$html = '';

		$db = new db();
		$appListFormId = 'apFrmLst'.$this->IdObject;
		$divPannel = 'pannel-1-'.$this-> IdObject;



		$L = new AppList($this->sql_list);
		$L->formId = $appListFormId;
		$L->printFormTag = false;
		$L->reg_x_pag = $this->reg_x_page;
		$L->enable_buttons = true;
		$L->btn_delete_visible = $this->list_btn_delete_visible;
		$L->btn_edit_visible = $this->list_btn_edit_visible;
		$L->butons_IdObject = $this->IdObject;
		$L->divPannel = $divPannel;
		$L->id_field_name = $this->DbRecord->id_field_name;

		$data['formId'] = $appListFormId;
		$data['divPannel'] = $divPannel;
		$data['url'] = $this->CurrentPage;

		if($this->templateList!=''){$L->useTemplateView($this->templateList,$data);}



		// Toolbar
		$toolbar = $this->PrintToolBar();



		$html .=$toolbar;
		$html .='<div class="clear"></div>';
		$html .='<form name="'.$appListFormId.'" id="'.$appListFormId.'">';
		$html .='<input type="hidden" name="op" value="'.encode('view-2').'">';

		$ajaxSubmit = new ajax_submit();


		//$html .='<textarea style="width:97%;height:400px">';
		$html .=$ajaxSubmit->Render($L->formId, $divPannel, $this->CurrentPage);
		$html .= utf8_encode($L->Render($this->CurrentPage));
		//$html .='</textarea>';
		$html .='</form>';



			
		// Returns result
		ob_start();
		eval('?>' . $html . '<?php ');
		echo $ViewResult = ob_get_contents();
		ob_end_clean();

		return $ViewResult;

	} // PrintList


	/**
	 * @package AppForm
	 * @version v0.1
	 * @method generateDatatableJSON
	 * @desc generates the Json Data to fill de data table
	 * */
	private function generateDatatableJSON(){
		// no mostrar errores como warnings, ya que afecta el resultado y marca error en {json}
		ini_set('display_errors', 0);
			

		$out_ini='';
		$out_regs='';
		$out_fin='';
		#--------------------------------------
		# REGISTROS
		#--------------------------------------


		//$out_regs .= '["a","B","c","D","e"],["a","B","c","D","e"]';
		$out_regs .= '["a","B","c","D"],["a","B","c","D"]';
		//$out_regs .= '["a"],["a"]';

		$iTotal = 2;
		$iFilteredTotal=2;

		$out_ini .= '{';
		$out_ini .= '"sEcho": '.intval(@$_GET['sEcho']).', ';
		$out_ini .= '"iTotalRecords": '.$iTotal.', ';
		$out_ini .= '"iTotalDisplayRecords": '.$iFilteredTotal.', ';
		$out_ini .= '"aaData": [ ';

		#--------------------------------------
		# Cerrar cadena output
		#--------------------------------------
		$out_fin .= '] }';


		#--------------------------------------
		# FORTAMEAR output
		#--------------------------------------

		$sOutput = $out_ini . $out_regs . $out_fin;

		return $sOutput;

	}
	/**
	 *@package AppForm
	 *@since v0.1
	 *@method ExportToExcel()
	 *@desc  Exports data to excel file
	 * */
	private function ExportToExcel(){

		$html = '';

		$db = new db();
		$db->connect();

		$db->query_assoc($this->sql_list);

		$row_cnt = 0;
		$tot_cols = 0;

		// Toolbar
		//$toolbar = $this->PrintToolBar();
		//$html .= $toolbar;

		$html.= '<table cellpadding="0" cellspacing="0" border="1" class="display" id="lst-'.$this->IdObject.'">';
		while($rec=$db->next()){

			$row_cnt++;
			if($row_cnt==1){
				$tot_cols = count($rec);
				//Llenar las columnas
				$html .='<thead><tr>';

				foreach ($rec as $k => $v){
					$html.= '<th style="background:#003366;color:#ffffff;">'.ucwords(strtolower(strtr($k, '_', ' '))).'</th>';
				}

				$html .='</tr></thead>
			  <tbody>';
			}
			$id_field_name = $this->DbRecord->id_field_name ;


			$html.= '<tr>';

			foreach ($rec as $k => $v){

				$html.= '<td>'.htmlentities($rec[$k]).'</td>';
			}

			$html.= '</tr>';



		}
		$html.= '</tbody></table>';



		fk_export_excel($html, $this->model.'-data-'.(date('Y-m-d.His')).'');

		// Returns result


		return '';

	} // ExportToExcel


	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method showFields($Array)
	 *@desc  define the record files to show when Render()
	 * */
	public function showFields($ar){
		$this->ShowFields = $ar;
		$this->ShowOrHide = 'show';

	} // ShowFields($ar){
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method hideFields($Array)
	 *@desc  define the record fields to hide
	 * */
	public function hideFields($ar){
		$this->HideFields = $ar;
		$this->ShowOrHide = 'hide';

	} // HideFields($ar)
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method createRelationship()
	 *@desc  Creates database dependency with another table
	 *@example 1) <b>Simple</b> createRelationship('id_category','SELECT id_category,category FROM categories');
	 *         field current_record.id_category has many -> categories
	 *         <br>
	 *         2) <b>Complex</b>  createRelationship('id_category','SELECT t1.id,concat(t2.field1,t1.field1) FROM table1 t1,table2 t2 WHERE t1.id = t2.fkid ','SELECT t1.id,concat(t2.field1,t1.field1) FROM table1 t1,table2 t2 WHERE t1.id = t2.fkid and t1.id = "{0}"');
	 *
	 * */
	public function createRelationship($field_name,$sql_rel,$id_sel=NULL, $sql_complex = NULL,$AddNewParameters=array()){

		$this->DbRecord->form_fields[$field_name]['Type']='select';
		$this->DbRecord->form_fields[$field_name]['sql_options']=$sql_rel;
		if($id_sel!=NULL){
			$this->DbRecord->form_fields[$field_name]['selected_option']=$id_sel;
		}

		if($sql_complex!=NULL){
			$this->DbRecord->form_fields[$field_name]['sql_complex']=$sql_complex;
		}

		if(count($AddNewParameters)>0){
			$this->DbRecord->form_fields[$field_name]['add_new']=$AddNewParameters;
		}


	} // End CreateRelationship
	/**
	 *@package AppForm
	 *@since v0.1
	 *@method setAutoSearchField()
	 *@desc   converts the field to AutoSerchable Object
	 *@example setAutoSearchField('customer_code','SELECT id_customer,customer,code FROM customers','code');
	 *         field current_record.id_category has many -> categories
	 *
	 * */
	public function setAutoSearchField($field_name,$sql,$sql_for_text,$onclick='',$id_sel=NULL ){

		$this->DbRecord->form_fields[$field_name]['Type']='search_field';
		$this->DbRecord->form_fields[$field_name]['sql_options']=$sql;
		$_SESSION['FK']['appform'][$this-> model]['auto_search_field'][$field_name.$this->ObjectCode]['sql'] = $sql;

		if($id_sel!=NULL){
			$this->DbRecord->form_fields[$field_name]['selected_option']=$id_sel;
		}

		if($sql_for_text!=NULL){
			$this->DbRecord->form_fields[$field_name]['sql_complex']=$sql_for_text;
		}
		$_SESSION['FK']['appform'][$this-> model]['auto_search_field'][$field_name.$this->ObjectCode]['onclick'] = $onclick;

	} // End setAutoSearchField
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method addFormVar()
	 *@desc  Add a new variable to use on the form
	 * */
	public function addFormVar($variable,$value){
		// Add a new variable to use on the form
		$this->extra_form_vars[$variable]=$value;

	} // End AddFormVar
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method GetExtraFormVariables()
	 *@desc  Get the variables added with  AddFormVar() method
	 * */
	private function GetExtraFormVariables(){
		$extraVars = '';
		if(count($this->extra_form_vars)>0){
			foreach ($this-> extra_form_vars as $k => $v){

				$extraVars = '<input type="text" id="'.$k.'" name="'.$k.'" value="'.$v.'" >';

			} // End Foreach
		} // End if

		return $extraVars;

	} // End GetExtraFormVariables
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method validateField()
	 *@desc  Validate Form Fields
	 *       1) Types : [ integer | decimal | date | text | username | email]
	 *@example validateField($FieldName,$Type,$Mask = NULL)
	 *
	 * */
	public function validateField($FieldName,$Type,$Mask = NULL){

		// ej: ValidateField('field','required;email;url;min:100;max:200');

		$Type = explode(';',$Type);

		if(count($Type)>0){
			foreach($Type as $k=>$v){

				if(strpos($v,':')>0){
					// Min Max
					$minmax = explode(':',$v);

					if(trim(strtolower($minmax[0]))=='min'){

						$this->validate_MinLength($FieldName, $minmax[1]);
					}
					if(trim(strtolower($minmax[0]))=='max'){
						$this->validate_MaxLength($FieldName, $minmax[1]);
					}

				}else{
					if(trim(strtolower($v))=='required'){
						$this->validate_RequiredField($FieldName);
					}
					if(trim(strtolower($v))=='email'){
						$this->validate_EmailFormat($FieldName);

					}
					if(trim(strtolower($v))=='url'){
						$this->validate_UrlFormat($FieldName);
					}
					if(trim(strtolower($v))=='number'){
						$validcode = trim(strtolower($v));
						$this->ValidationFields[$FieldName][$validcode] = true;
							
					}
				}


			}

		}

		if($Mask!=NULL){
			$this->ValidationMaskFields[$FieldName]=$Mask;
		}


	} // End ValidateField
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method PrintValidations()
	 *@desc  prints the field mask set on the validations
	 *
	 * */
	private function PrintValidationMask(){


		$var = '';
		foreach ($this->ValidationMaskFields as $k => $v){
			$var .= '$("#'.$k.'").inputmask({mask:"'.$v.'"});';
		} // End Foreach

		return $var;


	} // SetValidations()

	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method useTemplateView()
	 * */
	public function useTemplateView($tpl,$ArrayData = array()){

		$this->ArrData = $ArrayData;

		$v_path =  'app/views';
		$file_path = $v_path.'/'.$tpl;

		if(fk_file_exists($file_path)==TRUE){
			// Get Template
			$this->use_template = TRUE;
			$this-> template = file_get_contents(SYSTEM_PATH.$file_path);

		}else{
			try{
				throw new FkException("El template ".$tpl." no existe");
			}catch(FkException $e){



				$e->description="El template ".$v_path."/".$tpl." no existe. ";
				$e->solution='Verifique que este bien escrito y que exista
				<h2><a href="'.fk_link('FkDev/creaAppFormtemplate/'.encode($v_path)).'/'.encode($tpl).'/" target="_blank">Crear template ahora</a></h2>
				';
				$e->error_code='AF000001';
				$e->show();
			}
		}


	} // enD UseTempleteView
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method useTemplateView()
	 * */
	private function PutTemplateContent($Index,$Content){

		$this->ArrSearchTpl[] = $Index;
		$this->ArrReplaceTpl[] = $Content;

	}
	/**
	 *@package AppForm
	 *@since v0.1 beta
	 *@method ProcessFields()
	 * */
	private function ProcessFields(){
		$fields = '';


		

		

		// Print the form fields
		foreach($this->DbRecord->form_fields as $k=>$v ):

		
		



		// Default mostrar todos
		$displayField = true;

		$useArrDisp = false;
		if($this->ShowOrHide == 'show' ){ $arrDisp = $this->ShowFields; $useArrDisp = true;}
		if($this->ShowOrHide == 'hide' ){ $arrDisp = $this->HideFields; $useArrDisp = true;}

		// En caso que se use hide o show
		if($useArrDisp==true){
			//Aplicar filtro de mostrar u ocultar
			if($this->ShowOrHide == 'show'){
				if(in_array($k,$arrDisp)){$displayField = true;}else{$displayField = false;}
			}elseif($this->ShowOrHide == 'hide'){
				if(in_array($k,$arrDisp)){$displayField = false;}else{$displayField = true;}
			}
		}
		
		
		

		// Mostrar field
		if($displayField==true){

			if(!isset($this->Labels[$k])){ $this->Labels[$k] = ucwords(strtolower(strtr($k, '_', ' ')));   }
			$validation=$this-> validate_getValidation($k);

			

			//-----------------------
			// get privileges of user
			//-----------------------

			//Default privileges (full access)
			$FldPriv['access']=true;
			$FldPriv['read_only'] = false;

			// If privileges are enabled, get the privileges
			if($this->privileges_enabled==true){ $FldPriv=Security::hasPriv_Field($this->id_user, $this->DbRecord-> table,$k); }


			if($this->form_mode == 'readonly'){
				// Force to readonly
				$FldPriv['read_only'] = true;
			}


			
			$HTMLField = $this->DbRecord->print_form_field($k,$validation['class'],$validation['size'],$this->encode_fields,$FldPriv['access'],$FldPriv['read_only'],$this->ObjectCode);			
			$LabelVal = ($FldPriv['access']==1)?$this->Labels[$k]:'';

			if($this->use_template==true){
				if($FldPriv['access']==1){
					$this->PutTemplateContent('{'.$k.'}', $HTMLField);
					$this->PutTemplateContent('{label:'.$k.'}', $LabelVal);
					$this->PutTemplateContent('{value:'.$k.'}', @$this->DbRecord->fields[$k]);
				}else{
					$this->PutTemplateContent('{'.$k.'}', '');
					$this->PutTemplateContent('{label:'.$k.'}', '');
					$this->PutTemplateContent('{value:'.$k.'}', '');
				}

			}else{
				if($FldPriv['access']==1){
					$fields .= '<tr><th>'.$LabelVal.'</th><td>'.$HTMLField .'</td></tr>';
				}

			}
			

		}
		
		endforeach; // End print fields

		

		return $fields;

	} // End ProcessFields

	/**
	 * validation
	 * */
	public function validateRequiredField($id_field){
		$this->ValidationFields[$id_field]['required'] = TRUE;
	}
	/**
	 * @deprecated use validateRequiredField instead
	 * */
	public function validate_RequiredField($id_field){
		$this->validateRequiredField($id_field);
	}

	public function validateMinLength($id_field,$minLength){
		$this->ValidationFields[$id_field]['min_length'] = $minLength;
	}
	/**
	 * @deprecated use validateMinLength instead
	 * */
	public function validate_MinLength($id_field,$minLength){
		$this->validateMinLength($id_field, $minLength);
	}
	public function validateMaxLength($id_field,$maxLength){
		$this->ValidationFields[$id_field]['max_length'] = $maxLength;
	}
	/**
	 * @deprecated use validateMaxLength instead
	 * */
	public function validate_MaxLength($id_field,$maxLength){
		$this->validateMaxLength($id_field, $maxLength);
	}
	public function validateEmailFormat($id_field){
		$this->ValidationFields[$id_field]['email'] = true;
	}
	/**
	 * @deprecated use validateEmailFormat instead
	 * */
	public function validate_EmailFormat($id_field){
		$this->validateEmailFormat($id_field);
	}
	public function validateUrlFormat($id_field){
		$this->ValidationFields[$id_field]['url'] = true;
	}
	public function validateNumberFormat($id_field){
		$this->ValidationFields[$id_field]['number'] = true;
	}
	/**
	 * @deprecated use validateEmailFormat instead
	 * */
	public function validate_UrlFormat($id_field){
		$this->validateUrlFormat($id_field);
	}



	private function validate_getValidation($id_field){
		if(@$this->ValidationFields[$id_field]['required']){
			$required = ' required ';
		}
		if(@$this->ValidationFields[$id_field]['email']){
			$email = ' email ';
		}
		if(@$this->ValidationFields[$id_field]['url']){
			$url = ' url ';
		}
		if(@$this->ValidationFields[$id_field]['number']){
			$number = ' number ';
		}
		if(isset($this->ValidationFields[$id_field]['max_length'])){
			$max = 'maxlength="'.$this->ValidationFields[$id_field]['max_length'].'" ';
		}

		if(isset($this->ValidationFields[$id_field]['min_length'])){
			$min = ' minlength="'.$this->ValidationFields[$id_field]['min_length'].'" ';
		}


		$class = @$required.@$url.@$email.@$number;
		$size = @$max.@$min;
		$res['class'] = $class;
		$res['size'] = $size;

		return $res;

	}
	/**
	 *@package AppForm
	 *@method setHtmlFieldType()
	 *@desc Sets the HTML Field Type ej. input,password,textarea etc
	 *@since v0.1 beta
	 * */
	public function setHtmlFieldType($field_name,$HtmlType,$properties=NULL){

		$this->DbRecord->form_fields[$field_name]['Type']=$HtmlType;
		if($properties!=NULL){$this->DbRecord->form_fields[$field_name]['properties']=$properties;}
	}

	/**
	 *@package AppForm
	 *@method submitTo()
	 *@desc Changes the default ajax behavior to a clasic <b>SUBMIT FORM</b> behavior.
	 *      Use it to customize the submit action
	 *      and save or update the record mannualy.
	 *      Allows to have flexible programming ussing AppForm.
	 *@since v0.1
	 * */
	public function submitTo($NewUrl,$Method='GET'){

		$this-> custom_submit = TRUE;
		$this-> submit_url =  $NewUrl;
		$this-> submit_method = $Method;

	} // End SubmitTo

	/**
	 *@package AppForm
	 *@method getDecodedFields()
	 *@desc When <b>AppForm->encode_fields is set to TRUE</b>
	 *      returns an array with the $_POST OR $_GET  variables decoded.
	 *      Allows to implement <b>Security</b> to the programs ussing AppForm.
	 *@since v0.1
	 * */
	public function getDecodedFields($Method = 'GET'){
		$flds = array();

		if($this-> encode_fields){
			foreach ($this->DbRecord->form_fields as $f){
				if($Method=='POST'){
					$flds[$f['Field']] = @$_POST[encode($f['Field'])];
				}else{
					$flds[$f['Field']] = @$_GET[encode($f['Field'])];
				}

			}

		}

		return $flds;

	} // end getDecodedFields

	private function get_handled_errors_message($err_code){

		$message = '';

		switch ($err_code) {
			case 'ER_ROW_IS_REFERENCED_2':
				$message = $this->Message_003;
					
				break;
					
			case 'ER_NO_REFERENCED_ROW_2':
				$message = $this->Message_004;
					
				break;
					
			case 'ER_DUP_ENTRY':
				$message = $this->Message_005;
					
				break;
					
			default:
				$message = $this->Message_006;

				break;
		}

		return $message;

	} //  get_handled_errors_message

	/**
	 *@package AppForm
	 *@method get_total_records($id_record)
	 *@desc sets total records
	 *@since v0.1
	 * */
	private function set_total_records(){

		$db = new db();

		// Get total records
		// SELECT COUNT(*) FROM TABLE WHERE 1=1 {AND}
		$db->set_select('count(*)');
		$db->set_table($this->model);
		$db->set_where(' 1=1 ');
		if($this->DbRecord->SqlAnd!=''){$db->add_and($this->DbRecord->SqlAnd);}
		$db-> query();
		$row = $db->next();
		$this->total_records = $row[0];

		if($this->total_records<2 ){ $this->record_number=$this->total_records;}



	} // end set_total_records

	/**
	 *@package AppForm
	 *@method get_id_record($id_record)
	 *@desc returns id record related to $record_num
	 *@since v0.1
	 **/
	private function get_id_record($record_num = 1){

		$db = new db();
		$id_record = 0;

		// Get
		$record_num = ($record_num - 1>0)?$record_num - 1:0;
		// SELECT {id} FROM TABLE WHERE 1=1 {AND} limit {record_num},1
		$db->set_select($this->DbRecord->id_field_name);
		$db->set_table($this->model);
		$db->set_where(' 1 = 1 ');
		if($this->DbRecord->SqlAnd!=''){$db->add_and($this->DbRecord->SqlAnd);}
		$db->set_limit(1, $record_num);
		$db-> query();
		if($row = $db->next()){
			$id_record = $row[0];
		}else{
			// Find first
			$this->record_number = 1;
			if($this->total_records==0){$this->record_number=0;}
			//select ID_USUARIO from USUARIOS WHERE TRUE  LIMIT 0,1
			$db->set_select($this->DbRecord->id_field_name);
			$db->set_table($this->model);
			$db->set_where(' 1=1 ');
			if($this->DbRecord->SqlAnd!=''){ $db->add_and($this->DbRecord->SqlAnd);}
			$db->set_limit(1, 0);

			$db-> query();
			if($row = $db->next()){
				$id_record = $row[0];
			}
		}




		return $id_record;

	} // end getDecodedFields

	private function update_nav_buttons($action = ''){

		//if($this->total_records==0){
		//
		//	$this->btn_delete_visible = false;
		//	$this->btn_search_visible = false;
		//	$this->btn_export_visible = false;
		//	$this->btn_edit_visible = false;
		//	$this->record_number = 0;
		//
		//}else{
		//	if($this->operation !='new' && $this->operation !='edit'){
		//		$this->btn_delete_visible = true;
		//		$this->btn_search_visible = true;
		//		$this->btn_export_visible = true;
		//		$this->btn_edit_visible = true;
		//	}
		//
		//
		//}

		if($action=='disable-all'){
			$this->btn_first_disabled = true;
			$this->btn_prev_disabled = true;
			$this->btn_last_disabled = true;
			$this->btn_next_disabled = true;
		}


		// Si ya no hay mas registros
		if($this->record_number==1){
			$this->btn_first_disabled = true;
			$this->btn_prev_disabled = true;
			if($this->total_records>0){
				$this->btn_last_disabled = false;
				$this->btn_next_disabled = false;
			}
		}

		// Si ya no hay mas registros
		if($this->record_number==$this->total_records){
			$this->btn_last_disabled = true;
			$this->btn_next_disabled = true;
			if($this->total_records>1){
				$this->btn_first_disabled = false;
				$this->btn_prev_disabled = false;
			}

		}

	} // update_nav_buttons

	/**
	 *@package AppForm
	 *@method setAutocompleteField($field,)
	 *@desc sets text field autocompletable
	 *@since v0.1
	 **/
	public function setAutocompleteField($field,$from_table,$from_field){
		$this->autocomplete[$field]['table'] = $from_table;
		$this->autocomplete[$field]['field'] = $from_field;
	} // end setAutocompleteField

	private function PrintAutocompleteFields() {
		$html = '';
		foreach ($this->autocomplete as $field=>$data){
			$html .=  '$( "#'.$field.'" ).autocomplete({source: HTTP+"FkMaster/autocomplete/'.encode($data['table']).'/'.encode($data['field']).'/"});';
		}
		return $html;
	} // End srch_w

	/**
	 *@package AppForm
	 *@method get_operation() static
	 *@desc returns the string operation
	 *@since v0.2
	 **/
	public static function get_operation(){
		return decode(fk_post('op'));
	}
	/**
	 *@package AppForm
	 *@method getFormId() static
	 *@desc returns the string "form id"
	 *@since v0.2
	 **/
	public static function getFormId($table_name){
		return substr(sha1($table_name.'Form'), 0,10);
	}

	/**
	 *@package AppForm
	 *@method enablePrivileges()
	 *@desc set privileges enabled as true
	 *@since v0.3.1 beta
	 **/
	public function enablePrivileges(){
		$this->privileges_enabled=TRUE;
	}

	/**
	 *@package AppForm
	 *@method createSelectRecords()
	 *@desc allows adding records to a select list from appform
	 *@since v0.3.4
	 **/
	private function createSelectRecords($operation){
		//crear opciones de <select> nuevas

		if($operation=='save' || $operation=='savedit'){

			foreach ($this->DbRecord->form_fields as $field=>$properties){

				$post_field_name = ($this->encode_fields)?encode($field):$field;

				if($properties['Type']=='select' && fk_post($post_field_name)=='new'){
					// if allows adding new


					if(isset($properties['add_new'])){
						//

						$Ar = new ActiveRecord($properties['add_new']['table_name']);
						foreach ($properties['add_new']['field'] as $new_field => $new_val){
							$Ar->fields[$new_field] = utf8_decode($new_val);
						}
						$Ar->insert();
						$_POST[$post_field_name] = $Ar->inserted_id();
					}

				} // if allows adding new
					
			} // foreach field
		} // if save




	} // createSelectRecords


	/**
	 *@package AppForm
	 *@method createConsecutiveField()
	 *@desc creates consecutive value field
	 *@since v0.3.4
	 **/
	private function createConsecutiveField($operation){
		//crear opciones de <select> nuevas
		


		if($operation=='save' || $operation=='savedit'){

			foreach ($this->DbRecord->form_fields as $field=>$properties){

				$post_field_name = ($this->encode_fields)?encode($field):$field;

				//if($properties['Type']=='consecutive' && fk_post($post_field_name)==''){
				if($properties['Type']=='consecutive' && fk_post($post_field_name)==''){

					// create consecutive
					if(isset($properties['properties'])){

						
							
						$consecutive = self::generarConsecutivo($properties['properties']['config_code'],true);

						if(isset($properties['properties']['zerofill'])){
							$consecutive = zerofill($consecutive, $properties['properties']['zerofill']);
						}

						// Actualiza el campo
						$_POST[$post_field_name] = $properties['properties']['prefix'].$consecutive.$properties['properties']['sufix'];
					}

				} // if allows adding new
					
			} // foreach field
		} // if save




	} // createConsecutiveField

	public static function generarConsecutivo($codigo,$increment=true){
			

		// GenerarConsecutivo
		$Configsys = new ActiveRecord('config_sys');
		$tot_contador = $Configsys->find_where('config_code = "'.$codigo.'" ');


		if ($tot_contador==0){
			// No existe
			$numero_consecutivo = 1;
			if($increment==true){
				$Configsys->fields['config_code'] = $codigo;
				$Configsys->fields['config_value'] = $numero_consecutivo;
				$Configsys->insert();
			}

		}else{
			// Si existe
			$numero_consecutivo = $Configsys->fields['config_value']+1;
			// INCREMENTA
			if($increment==true){
				$Configsys->fields['config_value']=$numero_consecutivo;
				$numero_consecutivo = $Configsys->fields['config_value'];
				$Configsys->update();
			}

		}

			
		return $numero_consecutivo;
	} // End generarConsecutivo


	/**
	 *@package AppForm
	 *@method get_post_record_id()
	 *@desc returns de id record
	 *@since v0.3.4
	 **/
	public static function get_post_record_id(){

		$id_form = fk_post('AppFormID');
		$RecordId = '';
		if(fk_post($id_form.'-i')!=''){
			$RecordId = fk_post($id_form.'-i');
		}

		return $RecordId;


	}
	/**
	 *@package AppForm
	 *@method ProcessAutoSaveJs()
	 *@desc procesa auto save js
	 *@since v0.3.5
	 **/
	private function ProcessAutoSaveJs(){

		$autosavejs = '';

		if($this->auto_save==TRUE){
			$autosavejs .= 'clearInterval(globalTimeInterval);';
			$autosavejs .= 'globalTimeInterval = setInterval(function(){$(".btn.save.autosave").trigger("click")},'.$this->auto_save_time.');';

		}

		return $autosavejs;

	}

	function savEdit(){
		
		$rs = $this->SaveRecord();
		//echo $this->IdRecord;
		//pa($this-> DbRecord);

		// If record to add new = true do not find the IdRecord,
		//and takes the input from defined vars
		if($this->record_to_add_new==false){
			//$this->DbRecord->find($this->IdRecord);
		}

		$this->form_mode = 'edit';
		$this->btn_new_visible = FALSE;
		$this->btn_edit_visible = FALSE;
		$this->btn_duplic_visible = FALSE;
		$this->btn_delete_visible = FALSE;
		$this->btn_save_visible = TRUE;
		//$this->btn_savedit_visible = TRUE;
		$this->btn_cancel_visible = TRUE;
		$this->update_nav_buttons('disable-all');

		// Actualizar $_POST[i] con el id del nuevo registro
		//$rs.='<script>$("#'.$this->IdObject.'-i").val("'.$this->IdRecord.'");</script>';


		// json
		
		$json['js'] = 'alert("hola");';

		$rs = json_encode($json);

		//$rs .=$this-> PrintView('form');

		/*// Refresh data
		 $this->update_nav_buttons();
		 $rs .=$this-> PrintView('form');*/
		return $rs;
	}



} // End class
