<?php

class ajax_submit{

	private $form='';
	private $response_location='';
	private $url='';
	private $args='';
	private $buttons=array();


	public function Render($form="",$response_location="",$url="",$args=""){

		$errorMsg = '';
		$errors = FALSE;
		$Load = new Load();
		
		if(function_exists('is_empty')==FALSE){
			// Load validate helper
				
			$Load -> Helper('validate');
		}

		if(is_empty($form)==FALSE){$this->form=$form; }
		if(is_empty($response_location)==FALSE){$this->response_location=$response_location; }
		if(is_empty($url)==FALSE){$this->url=$url; }
		if(is_empty($args)==FALSE){$this->args=$args; }

		if (is_empty($this->form)){
			$errorMsg .= ' form requerido :$obj-> Form("id-form")<br /> ';
			$errors = TRUE;
		}

		if(is_empty($this->response_location)){
			$errorMsg .= ' response_location requerido : $obj-> ResponseLocation("id-div-location")<br /> ';
			$errors = TRUE;
		}

		if(is_empty($this->url)){
			$errorMsg .= ' url requerida : $obj-> Url("Controller/Action")<br /> ';
			$errors = TRUE;
		}

		if($errors == TRUE){
			echo 'Error:<br />'.$errorMsg;
			return FALSE;
		}else{
			// Crear respuesta
			$butonsCode = $this->RenderButtons();
			$params = 'form:'.$this->form.';url:'.$this->url.';args:'.$this->args.';';
			$fx = new ajax('submit', $this->response_location, $params);
			$Result = ' <script type="text/javascript">
<!--
'.$butonsCode.'
$("#'.$this->form.'").validate({
	 submitHandler: function(form) {
		 '.$fx-> render().'
		 return false;
	 },invalidHandler: function(form, validator){
      var errors = validator.numberOfInvalids();
      if (errors) { alert("Por favor, llena los campos obligatorios");}
	 }
});
//-->
</script>'; 
			return $Result;

		}


	} // End Render

	function AddButton($idButton,$event='click'){
			
		$this-> buttons[] = array('btn'=>$idButton,'event'=>$event);

	}

	private function RenderButtons(){
		$js_buttons = '';
		if(count($this->buttons)>0){
			foreach($this->buttons as $btn){
				$js_buttons .='
				$("#'.$btn['btn'].'").'.$btn['event'].'(function(){
				   $("#'.$this->form.'").submit();
			    });';			
			}
		}
		return $js_buttons;
	}

	public function  Form($form){
		$this->form = $form;
	}

	public function  ResponseLocation($ResponseLocation){
		$this->response_location = $ResponseLocation;
	}

	public function  Url($Url){
		$this->url = $Url;
	}

	public function  Args($Args){
		$this->args = $Args;
	}

}