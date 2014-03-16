<?php
/**
 * FreeKore Php Framework
 * Version: 0.3.2
 *
 * @Author     M. Angel Mendoza  email:mmendoza@freekore.com
 * @copyright  Copyright (c) 2011 Freekore PHP Team
 * @license    GNU GPL License
 */
/*************************************************************
 **  Programa:    AppSpreadSheet.php
 **  Descripcion: Clase para Listados dinamicos como hoja de celdas en freekore
 **  proyecto    fecha      por         descripcion
 **  ----------  ---------  ----------- ----------------
 **  00000001    10/05/12   mmendoza    Creado.
 **************************************************************/
/**
 *@package AppList
 *@since v0.2
 *@desc  Application List
 **/
class AppSpreadSheet extends AppList{

	private $spreadseet_footer = '';
	private $tot_rows = 0;
	private $jsDat = '';
	public $tableId = 'sprd-01';
	public $new_line_html = '';
	public $record_id_name = '';

	//Btns
	public $btn_add_visible = true;


	//SAVE
	public $save_function = NULL;
	public $fields_to_save = array();
	public $table_name = 'sprd_01';
	public $save_by_submit = false;


	public function __construct($sql) {

		parent::__construct($sql);
	}

	/**
	 *@package AppList
	 *@since v0.2
	 *@method Render()
	 * */
	public function Render($getCurrentUrl){

		$this->currentUrl = $getCurrentUrl; // Parent CurrentUrl

		$rs = '';
		$this->formaLimit();

		// Reasignar op si viene tbl_op
		
		$op_2 = fk_post_get($this->tableId.'_op');
		if(trim($op_2)!=''){ $this->operation = decode($op_2);}
		


		switch ($this->operation) {
			case 'add-lines':

				$this->processNewLineHtml();
				$rs .= $this->new_line_html;
				break;
			case 'save-lines':

				if($this->save_function!=NULL){
					// Custom function
					call_user_func_array($this->save_function['function'], $this->save_function['parrameters']);
					$rs .= $this->PrintList();
				}else{
					// Default save function
					$this->saveLines();
					$rs .= fk_ok_message('Informaci&oacute;n guardada');
					$rs .= $this->PrintList();
				}


				break;
			case 'delete-line':
				$this->deleteLine();
				break;

			default:
				$rs .= $this->PrintList();
				break;
		} // End switch

		return $rs;

	} // render()

	/**
	 *@package AppSpreadSheet
	 *@since v0.3.2
	 *@method PrintTemplateList()
	 * */
	protected function PrintTemplateList(){


		$html ='';
		$DataRows = '';


		// Run the process to execute query, pagger variables
		$this->runQueryProcess();

		//jsDat
		$this->jsDat = '{id:\''.$this->tableId.'\',cUrl:\''.$this->currentUrl.'\',formId:\''.$this->formId.'\'}';

		//Get templete row
		$TemplateRow = $this->extractTemplateRow();

		// Array de variables enviado por funcion
		if(count($this->ArrData)>0){
			foreach ($this->ArrData as $k => $v){
				$$k = $v;
			}
		}


		$cntRowNo_spsh = 0;
		while($row = $this->db_queryapplist->next()){
			$tot_cols = count($row);

			$cntRowNo_spsh++;

			// Print header

			//Print rows
			$row_arr_srch = array();
			$row_arr_repl = array();
			foreach ( $row as $k => $v ){

				if($this->setHtmlspecialchars){
					$v = htmlspecialchars($v,ENT_QUOTES);
				}

				$row_arr_srch[] = '{'.$k.'}';
				$row_arr_repl[] = '<input class="sheetFld" type="text" name="'.$this->tableId.'_'.$k.'-'.$cntRowNo_spsh.'" id="'.$this->tableId.'_'.$k.'-'.$cntRowNo_spsh.'" value="'.$v.'">';

				$row_arr_srch[] = '{value:'.$k.'}';
				$row_arr_repl[] = $v;


			}

			$row_arr_srch[] = '{table-id}';
			$row_arr_repl[] = $this->tableId.'_';

			$row_arr_srch[] = '{row-id}';
			$row_arr_repl[] = $this->tableId.'_rw-'.$cntRowNo_spsh;

			$row_arr_srch[] = '{row-number}';
			$row_arr_repl[] = $cntRowNo_spsh;

			$row_arr_srch[] = '{record-id}';
			$row_arr_repl[] = '<input type="hidden" name="'.$this->tableId.'_recId-'.$cntRowNo_spsh.'" value="'.$row[$this->record_id_name].'" >';

			$row_arr_srch[] = '{delete-row}';
			$row_arr_repl[] = 'deleteRow('.$cntRowNo_spsh.','.$this->jsDat.')';




			$RowCode = str_replace($row_arr_srch, $row_arr_repl, $TemplateRow);




			// Returns result
			$ProccesedRow = '';
			ob_start();
			eval(' ?>' . $RowCode . '<?php ');
			$ProccesedRow = ob_get_contents();
			ob_end_clean();


			$DataRows .= $ProccesedRow;

		} // end while

		$this->tot_rows = $cntRowNo_spsh;




		$this->createFilter();
		$this->createPagger();
		$this->createSpreadSheetFooter();


		$this->PutTemplateContent('{table-id}', $this->tableId);
		$this->PutTemplateContent('{TEMPLATE-ROWS}', $DataRows);
		$this->PutTemplateContent('{applist:pagger}', $this->pagger);
		$this->PutTemplateContent('{applist:filter}', $this->filter);
		$this->PutTemplateContent('{applist:spreadsheet-footer}', $this->spreadseet_footer);

		$this->PutTemplateContent('{record-start}', $this->record_start);
		$this->PutTemplateContent('{record-end}', $this->record_end);
		$this->PutTemplateContent('{total-records}', $this->tot_regs);



		// Execute extraccion Template
		$templateProcessed = '';
		$templateProcessed = str_ireplace($this-> ArrSearchTpl, $this->ArrReplaceTpl, $this->template);
		$html .= '<div id="'.$this->tableId.'_main-spreadsheet">';
		$html .= $templateProcessed;
		$html .= '</div>';

		// Returns result
		ob_start();
		eval(' ?>' . $html . '<?php ');
		$html = ob_get_contents();
		ob_end_clean();


		return $html;
	} //PrintList


	private function createSpreadSheetFooter(){

		$jsDat = $this->jsDat;


		$this->spreadseet_footer = '<div class="tfoot">';

		if($this->btn_add_visible==true){
			$this->spreadseet_footer .= '<input type="button" class="btn" onclick="addLines('.$jsDat.')" value="[+]">';
			$this->spreadseet_footer .= '<input type="text" id="'.$this->tableId.'_add-tot-lines" name="'.$this->tableId.'_add-tot-lines" class="rwsMore" value="1">';
		}


		$this->spreadseet_footer .= '<input type="hidden" id="'.$this->tableId.'_tot" name="'.$this->tableId.'_tot" value="'.$this->tot_rows.'">';
		$this->spreadseet_footer .= '<input type="hidden" id="'.$this->tableId.'_op" name="'.$this->tableId.'_op" value="">';
		$this->spreadseet_footer .= '<input type="button" class="btn btn-save-data" onclick="saveLines('.$jsDat.')" value="Guardar datos">';
		$this->spreadseet_footer .= '<div class="clear"></div></div>';
		$this->spreadseet_footer .= '<div id="'.$this->tableId.'-opers"></div>';
		
		$this->spreadseet_footer .='<script type="text/javascript">
function incrementTotPts(dat){
	var total = $("#"+dat.id+"_tot").val();
	var new_val = total*1+1; 
	$("#"+dat.id+"_tot").val(new_val);
}';
		if($this->save_by_submit){
			$this->spreadseet_footer .='function saveLines(dat){
			    $("#"+dat.id+"_op").val("'.encode('save-lines').'");
				$("#"+dat.formId).submit();	
			}';		
		}else{
		
			$this->spreadseet_footer .='function saveLines(dat){
               var pArgs = {pDiv:dat.id+"_main-spreadsheet", 
						  pUrl:dat.cUrl,
						  pForm:dat.formId,
						  pArgs:"tId="+dat.id+"&op='.encode('save-lines').'",
						  pUrlAfter:"", 
						  insertMode:""
						  };
	fk_ajax_submit(pArgs);
}';
		}

$this->spreadseet_footer .='function addLines(dat){
	var totLimit = 10;
	var totLines = $("#"+dat.id+"_add-tot-lines").val();
	if(totLines>totLimit){totLines=totLimit;$("#"+dat.id+"_add-tot-lines_").val(totLimit);}
	for(i=1;i<=totLines;i++){
	
		incrementTotPts(dat);
		      var pArgs = {pDiv:dat.id, 
						  pUrl:dat.cUrl,
						  pForm:dat.formId,
						  pArgs:"tId="+dat.id+"&op='.encode('add-lines').'",
						  pUrlAfter:"", 
						  insertMode:"bottom"
						  };
			  fk_ajax_submit(pArgs);
	}
}
function deleteRow(RowNumber,dat){
	
	if(confirm("Eliminar linea?")){
		      var pArgs = {pDiv:dat.id+"-opers", 
						  pUrl:dat.cUrl,
						  pForm:dat.formId,
						  pArgs:"tId="+dat.id+"&op='.encode('delete-line').'&recIdToDel="+RowNumber,
						  pUrlAfter:"", 
						  insertMode:""
						  };
			  fk_ajax_submit(pArgs);
			  $("#"+dat.id+"_rw-"+RowNumber).remove();
	}
}
</script>';

	} // createSpreadSheeetFooter

	private function processNewLineHtml(){

		$tableId = fk_post('tId');
		$row_cnt = fk_post($tableId.'_tot');

		// Replace
		$search[] = '{table-id}';
		$replace[] = $tableId.'_';

		$search[] = '{row-id}';
		$replace[] = '-'.$row_cnt;

		$search[] = '{record-id}';
		$replace[] = '<input type="hidden" name="'.$tableId.'_recId-'.$row_cnt.'" id="'.$tableId.'_recId-'.$row_cnt.'" value="0" >';


		$this->new_line_html = str_replace($search, $replace, $this->new_line_html);

	} // End processNewLineHtml


	private function deleteLine(){

		$tableId = fk_post('tId');
		$id = fk_post($tableId.'_recId-'.fk_post('recIdToDel'));

		if($id>0){
			$Ar = new ActiveRecord($this->table_name);
			$Ar->find($id);
			$Ar->delete();

		}


	}
	private function saveLines(){

		

		$tableId = $this->tableId;
		$total = fk_post($tableId.'_tot');
		
		

		for($i=1;$i<=$total;$i++){
			
			$id_rec = fk_post($tableId.'_recId-'.$i);

			$Ar = new ActiveRecord($this->table_name);
			if($id_rec>0){
				//UPDATE
				
				//procesar fields to save
				foreach ($this->fields_to_save as $key => $value_from){

					$arr_src = array('{table-id}','{row-id}');
					$arr_repl = array($tableId.'_','-'.$i);
					$value = str_replace($arr_src, $arr_repl, $value_from);


					$Ar->fields[$key] = fk_post($value);
				}
				$Ar->fields[$this->record_id_name] = $id_rec;
				$Ar->display_queries = true;
				$Ar->update();

					
			}else{
				//INSERT
				//procesar fields to save
				foreach ($this->fields_to_save as $key => $value_from){
					$arr_src = array('{table-id}','{row-id}');
					$arr_repl = array($tableId.'_','-'.$i);
					$value = str_replace($arr_src, $arr_repl, $value_from);

					$Ar->fields[$key] = fk_post($value);
				}
				$Ar->insert();
					
			}


		}


	} // End saveLines

	/**
	 *@package AppSpreadSheet
	 *@since v0.3.2
	 *@method on_save_do_function()
	 *@desc executes call_user_func_array() ;
	 *      <br>more info http://www.php.net/manual/en/function.call-user-func-array.php
	 * */
	function on_save_do_function($function,$arr_parameters){

		$this->save_function['function'] = $function;
		$this->save_function['parrameters'] = $arr_parameters;

	}




}