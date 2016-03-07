<?php
/**
 * FreeKore Php Framework
 * Version: 0.2
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2011 Freekore PHP Team
 * @license    GNU GPL License
 */
/*************************************************************
 **  Programa:    AppList.class.php
 **  Descripcion: Clase para Listados dinamicos en freekore
 **  proyecto    fecha      por         descripcion
 **  ----------  ---------  ----------- ----------------
 **  00000001    02/09/11   mmendoza    Creado.
 **************************************************************/
/**
 *@package AppList
 *@since v0.2
 *@desc  Application List
 *@version 2.0
 **/
class AppList{

	protected $operation = '';
	private $sql = '';
	private $sql_exec = '';


	// Template
	private $use_template = FALSE;
	protected $template = '';
	protected $ArrReplaceTpl = array();
	protected $ArrSearchTpl = array();
	protected $ArrData = array();

	// Query
	protected $db_queryapplist = null;

	//Pagger
	protected $tot_regs = 0;
	public $reg_x_pag = 10;
	private $page = 1;
	private $tot_pages = 0;
	protected $pagger = ''; // html pagger
	protected $filter = ''; // html filter
	private $LIMIT = '';
	protected $record_start = 0;
	protected $record_end = 0;
	protected $currentUrl = '';
	protected $total_pagger_limit = 15;
	public $label_prev = '&laquo; Previo';
	public $label_next = 'Siguiente &raquo;';

	// APPFORM INTERACTION
	public $enable_buttons = false;
	public $butons_IdObject = '';

	public $divPannel = '';
	public $id_field_name = '';

	public $formId = ''; // Form ID
	public $printFormTag = true; // prints <form>table content</form>
	public $setHtmlspecialchars = true;

	//Buttons
	private $btn_export = '';
	private $btn_import = '';
	private $btn_print = '';

	public $btn_delete_visible = true;
	public $btn_edit_visible = true;


	/**
	 *@package AppList
	 *@since v0.2
	 *@method __construct()
	 * */
	public function __construct($sql){


		$this->sql = $sql;

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





	} // Construct

	/**
	 *@package AppList
	 *@since v0.2
	 *@method Render()
	 * */
	public function Render($getCurrentUrl){

		$this->currentUrl = $getCurrentUrl;


		$rs = '';
		$this->formaLimit();

		switch ($this->operation) {
			case 'new':
				$rs .= $this->PrintList();
				break;
			default:
				$rs .= $this->PrintList();
				break;
		} // End switch

		return $rs;

	} // render()

	/**
	 *@package AppForm
	 *@since v0.2
	 *@method PrintList()
	 * */
	protected function PrintList(){
		
		

		if($this->use_template==TRUE){
			
			$form = $this-> PrintTemplateList();
		}else{
			$form = $this-> PrintCommonList();
		}
		return $form;
	}

	protected function createFilter() {


		$this->filter = '
		<input type="hidden" name="page" id="page'.$this->formId.'" value="'.fk_form_var('page').'">
		<input type="hidden" name="sort" id="sort'.$this->formId.'" value="'.fk_form_var('sort').'">
		<input type="hidden" name="sorttyp" id="sorttyp'.$this->formId.'" value="'.fk_form_var('sorttyp').'">
		<div class="tbl-filter">
		
		<div class="input-append">
		<div class=" col-md-8 ">&nbsp;</div>
		<div class="input-group m-bot15 col-md-4">
                                <input type="text" class="form-control" name="p" id="p'.$this->formId.'" value="'.fk_form_var('p').'">
                                              <span class="input-group-btn">
                                                <input type="submit" class="btn btn-primary" value="Filtrar">
                                              </span>
                            </div></div></div>';

	}

	/**
	 *@package AppForm
	 *@since v0.2
	 *@method PrintTemplateList()
	 * */
	protected function PrintTemplateList(){



		$html ='';
		$DataRows = '';

			



		// Run the process to execute query, pagger variables
		$this->runQueryProcess();


		//Get templete row
		$TemplateRow = $this->extractTemplateRow();

		// Array de variables enviado por funcion
		if(count($this->ArrData)>0){
			foreach ($this->ArrData as $k => $v){
				$$k = $v;
			}
		}
		
		


		while($row = $this->db_queryapplist->next()){
			$tot_cols = count($row);

			// Print header

			//Print rows
			$row_arr_srch = array();
			$row_arr_repl = array();
			foreach ( $row as $k => $v ){

				if($this->setHtmlspecialchars){
					//$v = htmlspecialchars($v,ENT_QUOTES);
				}


				$row_arr_srch[] = '{'.$k.'}';
				$row_arr_repl[] = $v;
			}

			//botones
			$btns = $this->get_buttons($row);


			$row_arr_srch[] = '{row:buttons}';
			$row_arr_repl[] = $btns.'';



			$RowCode = str_replace($row_arr_srch, $row_arr_repl, $TemplateRow);




			// Returns result
			$ProccesedRow = '';
			ob_start();
			eval(' ?>' . $RowCode . '<?php ');
			$ProccesedRow = ob_get_contents();
			ob_end_clean();


			$DataRows .= $ProccesedRow;

		} // end while



		$this->createFilter();
		$this->createPagger();

		// Boton export
		if(trim($this->currentUrl)!=''){
			$_SESSION['FK']['applist'][encode($this->currentUrl)]['export-sql'] = $this->sql;
			$this->btn_export = '<input type="button" class="btn btn-primary" value="'.__('Exportar a excel').'" onclick="window.open(\''.fk_link('FkMaster/export-list/'.encode('export').'/'.encode($this->currentUrl).'/').'\');" />';
		}else{
			$this->btn_export = ' Btn export: currentUrl Required';
		}

		// Boton print
		if(trim($this->currentUrl)!=''){
			$_SESSION['FK']['applist'][encode($this->currentUrl)]['print-sql'] = $this->sql;
			$this->btn_print = '<input type="button" class="btn btn-primary" value="'.__('Imprimir').'" onclick="window.open(\''.fk_link('FkMaster/export-list/'.encode('print').'/'.encode($this->currentUrl).'/').'\');" />';
		}else{
			$this->btn_print = ' Btn print: currentUrl Required';
		}

		// Boton import
		if(trim($this->currentUrl)!=''){
			$_SESSION['FK']['applist'][encode($this->currentUrl)]['import'] = $this->sql;
			$this->btn_import = '<input type="button" class="btn btn-primary" value="'.__('Importar').'" onclick="window.open(\''.fk_link('FkMaster/import-list/'.encode('import').'/'.encode($this->currentUrl).'/').'\');" />';
		}else{
			$this->btn_import = ' Btn import: currentUrl Required';
		}


		$this->PutTemplateContent('{TEMPLATE-ROWS}', $DataRows);
		$this->PutTemplateContent('{applist:pagger}', $this->pagger);
		$this->PutTemplateContent('{applist:filter}', $this->filter);

		$this->PutTemplateContent('{record-start}', $this->record_start);
		$this->PutTemplateContent('{record-end}', $this->record_end);
		$this->PutTemplateContent('{total-records}', $this->tot_regs);

		$this->PutTemplateContent('{applist:btn-export}', $this->btn_export);
		$this->PutTemplateContent('{applist:btn-import}', $this->btn_import);
		$this->PutTemplateContent('{applist:btn-print}', $this->btn_print);



		// Execute extraccion Template
		$templateProcessed = '';
		$templateProcessed = str_ireplace($this-> ArrSearchTpl, $this->ArrReplaceTpl, $this->template);
		$html .= $templateProcessed;

		// Returns result
		ob_start();
		eval(' ?>' . $html . '<?php ');
		$html = ob_get_contents();
		ob_end_clean();


		return $html;
	} //PrintList

	private function get_buttons($row){

		$btns ='';

		if($this->enable_buttons){
			$url = $this->currentUrl;
			$divPannel = $this->divPannel;
			$xEdit = new ajax('url', $divPannel, 'url:'.$url.';args:op='.encode('edit').'&'.$this->butons_IdObject.'-i='.$row[$this->id_field_name]);
			$xView = new ajax('url', $divPannel, 'url:'.$url.';args:op='.encode('selec-record').'&rec-srch='.$row[$this->id_field_name]);
			$xDelete = new ajax('url', $divPannel, 'url:'.$url.';args:op='.encode('del').'&'.$this->butons_IdObject.'-i='.$row[$this->id_field_name].'&viewas=list');



			$btns .='<a href="javascript:void(0);" onclick="'.$xView->render().'"><img title="'.__('Ver').'" src="'.HTTP.'_HTML/img/ico_view.png" width="20"></a>';
			if($this->btn_edit_visible){
				$btns .='&nbsp; <a class="btnedit" href="javascript:void(0);" onclick="'.$xEdit->render().'"><img title="'.__('Editar').'" src="'.HTTP.'_HTML/img/ico_edit.png" width="20"></a>';
			}
			if($this->btn_delete_visible){
				$btns .='&nbsp; <a href="javascript:void(0);" onclick="if(confirm(\'Eliminar Registro?\')){'.$xDelete->render().'}"><img title="'.__('Eliminar').'" src="'.HTTP.'_HTML/img/ico_delete.png" width="20"></a>';
			}


		}
		return $btns;
	}
	/**
	 *@package AppList
	 *@since v0.2
	 *@method extractTemplateRow()
	 * */
	protected function extractTemplateRow(){

		$ext1 = explode('<applist:loop>', $this->template);
		$ext2 = explode('</applist:loop>', isset($ext1[1])?$ext1[1]:'');
		$templateRow = isset($ext2[0])?$ext2[0]:'';

		if(trim($templateRow)==''){

			try{
				throw new FkException("El template requiere de etiquetas &lt;applist:loop&gt; &lt;/applist:loop&gt;");
			}catch(FkException $e){
				$e->description="&lt;applist:loop&gt; &lt;/applist:loop&gt; no fueron encontradas en el template, son requeridas para applist ";
				$e->solution='Agrege las etiquetas xml <b>&lt;applist:loop&gt;</b> {nombre_campo1} | {nombre_campo2} ...etc   <b>&lt;/applist:loop&gt;</b> ';
				$e->error_code='AL000002';
				$e->show();
			}
		}

		$this->template = str_ireplace($templateRow, '{TEMPLATE-ROWS}', $this->template);
		$this->template = str_ireplace(array('<applist:loop>','</applist:loop>'), '', $this->template);

		return $templateRow;
	}

	protected function runQueryProcess(){

		$this->db_queryapplist = new db();
		$db_queryapplist_2 = new db(); // SELECT FOUND_ROWS()

		$this->ProcessSqlFormat();
		$this->db_queryapplist->query_assoc($this->sql_exec);

		// Obtener total de registros para paginador
		$db_queryapplist_2->query_assoc('SELECT FOUND_ROWS() as total');
		$rec_tot=$db_queryapplist_2->next();
		$this->tot_regs = $rec_tot['total'];
		$this->creaVariablesPaginador();

		if($this->page>$this->tot_pages){
			// Si el usuario mete valores mayores a la pagina, regresa a pagina 1
			$this->formaLimit(1);
			$this->ProcessSqlFormat();
			$this->db_queryapplist->query_assoc($this->sql_exec);
			$this->creaVariablesPaginador();

		}

	}

	/**
	 *@package AppList
	 *@since v0.2
	 *@method PrintCommonList()
	 * */
	private function PrintCommonList(){

		if($this->enable_buttons==true){
			if($this->butons_IdObject==''){
				echo 'Error:(AppList Object) $this->butons_IdObject no definido<br>';
				echo 'Cuando se habilita $this->enable_buttons==true debe enviarse el parametro $this->butons_IdObject = "El AppFormID del formulario" ';
				return '';
			}
		}


		$divContentId = 'dvApLstCont'.$this->formId;

		$html ='';



		// Run the process to execute query, pagger variables
		$this->runQueryProcess();



		$this->createFilter();
		$this->createPagger();

		$ajaxSubmitStr = '';
		if($this->printFormTag==true){
			$ajaxSubmit = new ajax_submit();
			$ajaxSubmitStr = $ajaxSubmit->Render($this->formId, $divContentId, $this->currentUrl);
		}


		$html .='<div id="'.$divContentId.'">';

		if($this->printFormTag==true){$html .='<form name="'.$this->formId.'" id="'.$this->formId.'">';}
		$html .=$this->filter.$ajaxSubmitStr;
		if($this->printFormTag==true){$html .= '</form>';}


			
		$html .='<table id="table_'.$this->formId.'" class="tbl-1 table table-striped table-condensed cf" style="width:100%">';

		$line = 0;
		while($row = $this->db_queryapplist->next()){
			$tot_cols = count($row)/2;
			$head_cols = array();

			// Print header
			if($line==0){
				$html .='<thead class="cf"><tr>';

				if($this->enable_buttons){$html .='<th style="width:100px;">Accion</th>';}

				$col_cnt = 0;
				foreach ( $row as $cols=>$val ){
					if($cols!=$this->id_field_name){
						$col_cnt++;
						$html .='<th>'.self::sort_column($col_cnt, $cols, $this->formId).'</th>';
						$head_cols = $cols;
					}

				}
				$html .='</tr></thead>';
			}


			//Print rows
			$html .='<tr>';
				
			// buttons
			$btns = $this->get_buttons($row);
				
			if($btns!=''){
				$html .='<td>';
				$html .= $btns;
				$html .='</td>';
			}
				
			foreach ( $row as $cols=>$val ){
				if($cols!=$this->id_field_name){
					$html .='<td data-title="'.ucwords($cols).'">'.$val.'</td>';
				}
			}
			$html .='</tr>';
			$line++;
		} // end while

		$html .= '</table>';



		$html .= $this->pagger;
		$html .='</div>'; // Contenedor

		$html .="<script>$(function(){ 
			$('#table_".$this->formId."').setecTable({callback:function(row,objId,rowId){
					href = row.find('td a.btnedit').click();
				}});
		});</script>";



		return $html;
	} //PrintList

	/**
	 *@package AppForm
	 *@since v0.2
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
				$e->solution='Verifique que este bien escrito y que exista';
				$e->error_code='AL000001';
				$e->show();
			}
		}

	} // enD UseTempleteView

	/**
	 *@package AppList
	 *@since v0.2
	 *@method PutTemplateContent()
	 * */
	protected function PutTemplateContent($Index,$Content){

		$this->ArrSearchTpl[] = $Index;
		$this->ArrReplaceTpl[] = $Content;

	}

	/**
	 *@package AppForm
	 *@since v0.2
	 *@method ProcessSqlFormat()
	 * */
	private function ProcessSqlFormat(){

		$sql = str_ireplace('SQL_CALC_FOUND_ROWS', '', $this->sql);
		$sql = str_ireplace('SELECT ', 'SELECT SQL_CALC_FOUND_ROWS ', $sql);
		$sql = str_ireplace('{SELECT} ', ' SELECT ', $sql);
		$sql = trim($sql,';');

		$this->sql_exec = $sql.' '.$this->LIMIT;
	}

	/**
	 *@package AppList
	 *@since v0.2
	 *@method creaVariablesPaginador()
	 * */
	private function creaVariablesPaginador(){

		$this->tot_pages = $this->tot_regs/$this->reg_x_pag;
		$this->tot_pages=explode('.',$this->tot_pages);
		$this->tot_pages=$this->tot_pages[0];
		if(($this->tot_regs%$this->reg_x_pag)>0){$this->tot_pages++;}

		$this->record_start = ( $this->page * $this->reg_x_pag) - $this->reg_x_pag + 1;
		$this->record_end = ( $this->page * $this->reg_x_pag);


		if($this->record_end > $this->tot_regs ){
			$this->record_end = $this->tot_regs;
		}

	}

	private function add_page($i){

		if($this->page==$i){
			$this->pagger .= ' <span class="btn btn-default">'.$i.'</span> ';
		}else{
			$this->pagger .= ' <a class="btn btn-primary" value="'.$i.'" href="?page='.$i.'">'.$i.'</a> ';
		}

	}

	protected function createPagger(){



		$this->pagger .= '<div id="pagger'.$this->formId.'" class="pagger btn-group">';

		if($this->page>1){
			$this->pagger .= ' <a class="btn btn-primary" value="'.($this->page-1).'" href="'.fk_link($this->currentUrl).'?page='.($this->page-1).'"> '.$this->label_prev.' </a> ';
		}else{
			//$this->pagger .= ' <span> '.$this->label_prev.' </span> ';
		}

		$add_first_page = false;
		$add_last_page = false;

		$ini = 1;
		$fin = $this->tot_pages;
		$balance = ($this->total_pagger_limit- ($this->total_pagger_limit%2)  ) / 2;


		if($fin>$this->total_pagger_limit){
			$fin = $this->total_pagger_limit;
			$add_last_page = true;

			if(($this->page - $balance)>1){

				$ini = $this->page - $balance;
				$fin = $this->page + $balance;
				$add_first_page = true;

				if($fin>$this->tot_pages){
					$fin = $this->tot_pages;
					$add_last_page = false;
				}

			}

		}


		if($add_first_page){	$this->add_page(1); $this->pagger .= '<span class="btn btn-default" >...</span>';	}
		for($i=$ini;$i<=$fin;$i++){ $this->add_page($i); }
		if($add_last_page){ $this->pagger .= '<span class="btn btn-default" >...</span>'; $this->add_page($this->tot_pages); }


		if($this->page<$this->tot_pages){
			$this->pagger .= '  <a class="btn btn-primary" value="'.($this->page+1).'" href="?page='.($this->page+1).'"> '.$this->label_next.' </a> ';
		}else{
			//$this->pagger .= ' <span> '.$this->label_next.' </span> ';
		}
		$this->pagger .= '</div>';

		if($this->formId!=''){
			$this->pagger .= '<script>$(document).ready(function(){  $("#pagger'.$this->formId.' a").click(function(){page = $(this).attr("value");
						$("#page'.$this->formId.'").val(page);$("#'.$this->formId.'").submit();return false;});});</script>';
		}


	}

	function formaLimit($argPage = NULL){
		$page = 0;
		if($argPage!=null){
			$page = $argPage;
		}else{
			$page = fk_form_var('page');
		}
		/*if($argPage!=null){
			$page = $argPage;
			}elseif(isset($_GET['page'])){
			$page = $_GET['page'];
			}elseif (isset($_POST['page'])){
			$page = $_POST['page'];
			}*/

		if($page>0){
			$INI = (($page-1)*$this->reg_x_pag);
			if(is_numeric($INI) && $INI > 0){
				$INI = $INI;
			}else{
				$INI = 0;
			}
			$this->page=$page;
		}else{
			$INI=0;
		}
		$this->LIMIT = $this->LIMIT = 'LIMIT '.$INI.' , '.$this->reg_x_pag;

	}

	/**
	 *@package sort_column
	 *@since v0.3.2
	 *@method sort_column()
	 * */
	public static function sort_column($val,$label,$formId){


		if(fk_form_var('sorttyp')==1){
			$ty = '&uarr;';
			$nxty = '0';
		}else{
			$ty = '&darr;';
			$nxty = '1';
		}

		if(fk_form_var('sort')==$val){
			$h = $ty.'<a title="Ordenar" alt="Ordenar" href="javascript:void(0)" onclick="$(\'#sort'.$formId.'\').val(\''.$val.'\');$(\'#sorttyp'.$formId.'\').val(\''.$nxty.'\');$(\'#'.$formId.'\').submit();">'.$label.'</a>';
		}else{
			$h = '<a title="Ordenar" alt="Ordenar" href="javascript:void(0)" onclick="$(\'#sort'.$formId.'\').val(\''.$val.'\');$(\'#sorttyp'.$formId.'\').val(\'0\');$(\'#'.$formId.'\').submit();">'.$label.'</a>';
		}
			
		return $h;


	} // sort_column

	/**
	 *@package AppList
	 *@since v0.3.2
	 *@method getOrderByProcesed()
	 * */
	public static function getOrderByProcesed($columns){

		$orderBy = '';
		$col = '';
		if(fk_form_var('sort')!=''){
			if(fk_form_var('sorttyp')==1){$ascDesc = ' desc ';}else{$ascDesc = ' asc ';}
			if(fk_form_var('sort')!=''){
				$kcol = fk_form_var('sort')-1;
				$col = $columns[$kcol];
				if($col!=''){$orderBy = ' order by '.$col.' '.$ascDesc;}

			}

		}


		return $orderBy;

	} // getOrderByProcesed

	/**
	 *@package AppList
	 *@since v0.3.2
	 *@method getOrderByProcesed()
	 * */
	public static function getFiltersProcesed($columns,$oper='WHERE'){



		$filters = '';
		if(fk_form_var('p')!=''){
			$p = str_replace(' ', '%', fk_form_var('p'));
			$filters .= ' '.$oper.' ( '.implode(' like "%'.$p.'%" or ', $columns).' like "%'.$p.'%" )';
		}




		return $filters;

	} // getOrderByProcesed

} // End class