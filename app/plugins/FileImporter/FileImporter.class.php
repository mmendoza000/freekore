<?php
class FileImporter{

	var $result_message = '';
	var $registros_importados = 0;
	var $tableName = '';
	var $tableFields = array();
	var $fixedC = array();
	var $display_rules = false;
	var $rules = array();
	var $currentUrl = '';

	public function addFixedCol($col,$val){
		if( trim($col) != '' && trim($val) != '' ){
			$this->fixedC[$col] = $val;
		}
	}

	public function importToDB(){

		$GLOBALS['FORCE_INSERT_ID'] = true;
		
		$rows = fk_post('rows');

		$Ar = new ActiveRecord($this->tableName);

		$tot_importados=0;

		

		if(count($rows)>0){



			foreach ($rows as $lineNo){

				$cols = fk_post('txt_'.$lineNo);

				// Campos importados
				foreach ($cols as $colNo=>$colVal){

					// procesar reglas
					$fieldName = fk_post('col-'.$colNo);
					$colVal = $this->getVal($colVal,$fieldName);

					if($fieldName!=''){
						$Ar->fields[$fieldName] = utf8_decode($colVal);
						//echo $colVal;
					}

				}
				// Campos fijos [SOBRE ESCRIBE]
				foreach ($this->fixedC as $fixedColNo=>$FixedColVal){
					$Ar->fields[$fixedColNo] = $FixedColVal;
				}

				if($Ar->insert()){
					$this->registros_importados++;
				}

			}

			return true;

		}else{
			$this->result_message = 'No se importo ningun registro';
			return false;
		}

	} // end import

	/**
	 * @desc Procesa el valor del la columna
	 * */
	function getVal($val,$fieldName){

		$this_rule = array();

		if(isset($this->rules[$fieldName])){

			$rules_prev = $this->rules[$fieldName];
			$rules_prev = explode(';', $rules_prev);
			foreach ($rules_prev as $rule){
				if(trim($rule)!=''){
					$rule = explode(':',$rule);
					$this_rule[$rule[0]] =$rule[1];
				}

			}

		}






		if(isset($this_rule['mode'])){
			$val=$this->procesaRegla($this_rule,$val);
		}


		return $val;

	} // End getVal

	/**
	 * @desc Procesa regla ej:
	 *       mode:relation;table:cat_generico;id:id;where:tipo="AP10-ESTATUS" and clave="{0}";
	 * */
	function procesaRegla($rule,$val){



		switch ($rule['mode']) {
			case 'relation':

				$Ar = new ActiveRecord($rule['table']);

				$where = stripcslashes($rule['where']);
				$where = str_replace('{0}', $val, $where);

				if($Ar->find_where($where)){
					$val = $Ar->fields[$rule['id']];
				}


				break;

			default:
				;
				break;
		}

		return $val;
	} // End procesaRegla

	public function importExcelData($file,$tablefields){

		// Test CVS
		set_time_limit(0);
		//require_once 'Excel/reader.php';
		Load::library('phpExcelReader/Excel/reader');

		// ExcelFile($filename, $encoding);
		$data = new Spreadsheet_Excel_Reader();


		// Set output Encoding.
		$data->setOutputEncoding('CP1251');

		/***
		 * if you want you can change 'iconv' to mb_convert_encoding:
		 * $data->setUTFEncoder('mb');
		 *
		 **/

		/***
		 * By default rows & cols indeces start with 1
		 * For change initial index use:
		 * $data->setRowColOffset(0);
		 *
		 **/



		/***
		 *  Some function for formatting output.
		 * $data->setDefaultFormat('%.2f');
		 * setDefaultFormat - set format for columns with unknown formatting
		 *
		 * $data->setColumnFormat(4, '%.3f');
		 * setColumnFormat - set format for column (apply only to number fields)
		 *
		 **/

		//$data->read(SYSTEM_PATH.'app/libraries/phpExcelReader/RP 030 LG13 0206WM MBA1PR COLOTLAN columnas.xls');
		$data->read($file);

		/*


		$data->sheets[0]['numRows'] - count rows
		$data->sheets[0]['numCols'] - count columns
		$data->sheets[0]['cells'][$i][$j] - data from $i-row $j-column

		$data->sheets[0]['cellsInfo'][$i][$j] - extended info about cell

		$data->sheets[0]['cellsInfo'][$i][$j]['type'] = "date" | "number" | "unknown"
		if 'type' == "unknown" - use 'raw' value, because  cell contain value with format '0.00';
		$data->sheets[0]['cellsInfo'][$i][$j]['raw'] = value if cell without format
		$data->sheets[0]['cellsInfo'][$i][$j]['colspan']
		$data->sheets[0]['cellsInfo'][$i][$j]['rowspan']
		*/

		error_reporting(E_ALL ^ E_NOTICE);

		echo '<table border="1" class="tbl-1">';


		for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {

			if($i==1){
				// HEader

				echo '<thead><tr>';
				echo '<th><input type="checkbox" name="chkAll" id="chkAll" onclick="checkAll($(this),\'chkRow\');" value="all" ></th>';
				for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {

					foreach ($tablefields as $fieldName => $fieldLabel){
						$array[$fieldName] = $fieldLabel;
					}



					echo "<th>";
					echo '<select name="col-'.($j-1).'"><option value="">[X]Ignorar</option>'.fk_select_options_r($array).'</select>';
					if($this->display_rules){ echo '<textarea name="rules[]" placeholder="Reglas"></textarea>';		}
					echo "</th>";
				}
				echo '</tr></thead>';
			}

			$trTieneDatos =0;
			
			$tr = '<tr>';
			$tr .= '<th><input type="checkbox" class="chkRow" name="rows[]" value="'.$i.'" ></th>';
			for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
				$tr .= '<td>';
				$tr .= utf8_encode($data->sheets[0]['cells'][$i][$j]);
				//$tr .= '<input type="hidden" name="txt_'.$i.'[]" value="'.$data->sheets[0]['cells'][$i][$j].'">';
				$tr .= '<textarea style="display:none" name="txt_'.$i.'[]">'.utf8_encode($data->sheets[0]['cells'][$i][$j]).'</textarea>';
				$tr .= '</td>';
				if(trim($data->sheets[0]['cells'][$i][$j])!=''){$trTieneDatos++;}
			}
			$tr .= '</tr>';
			
			if($trTieneDatos>0){ echo $tr;}

		}
		echo '</table>';
		echo '<h3> '.$data->sheets[0]['numRows'].' registros encontrados</h3>';


	} // end importExcelData

	/**
	 * @desc agregar regla ej:
	 *       mode:relation;table:cat_generico;id:id;where:tipo="AP10-ESTATUS" and clave="{0}";
	 * */

	public function addRule($id_campo,$regla){
		if( $id_campo != '' && $regla != ''){
			$this->rules[$id_campo] = $regla;
		}
	} //End addRule

	public function renderAllComponent($getCurrentUrl){

		$this->currentUrl = $getCurrentUrl;
		$GLOBALS['currentUrl'] = $this->currentUrl;
		$GLOBALS['tableFields'] = $this->tableFields;
		$GLOBALS['tableName'] = $this->tableName;
		$GLOBALS['fixedC'] = $this->fixedC;
		$GLOBALS['rules'] = $this->rules;

		$oper = fk_post('oper');

		if($oper==''){
			// Default
			//fk_header_blank();
			fk_header();
			fkore::_use('app/plugins/FileImporter/import1.php');
			fk_footer();

		}elseif($oper=='step2'){
				
				
			//fk_header_blank();
			//fk_header();
			fkore::_use('app/plugins/FileImporter/import2.php');
			//fk_footer();

		}elseif($oper=='step3'){
			//fk_header_blank();
			//fk_header();
			fkore::_use('app/plugins/FileImporter/import3.php');
			//fk_footer();
		}


	} // end renderAllComponent

}