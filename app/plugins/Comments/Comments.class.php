<?php
/****************************************************************************************
 **  fkControl : Comments Koomoni
 **  Version   : 0.0.1
 **  Author    : Mmendoza000 mmendoza000@hotmail.com (Dic-2010)
 **  Licence   : Privado
 **
 **  Descripcion: Control Comentarios  para Koomoni
 **  proyecto    fecha      por         descripcion
 **  ----------  ---------  ----------- ----------------
 **  DC          24/09/10   mmendoza    Creado.
 *****************************************************************************************/

class Comments
{
	private $page = '';
	private $id_obj = 'obj1';
	private $code = 'videos';
	private $id_tab_val = '0';


	private $allow_anonimo = false;
	private $auth_mode = false; //$auth_mode (1 [auto] | 2 [moderar] | 3 [ moderar anonimos])

	// Estructura tabla comentarios
	private $table = 'comentarios'; // tabla
	private $table_id_field	= 'id_comentario'; // id , llave primaria
	private $code_field = 'codigo_tabla'; // Codigo de referencia, que se comenta
	private $id_table2coment_field = 'id_val_tabla'; // Id referencia , id de lo que se comenta
	private $comment_field = 'comentario';	// texto del comentario
	public  $id_user_field = 'id_usuario';// Id usuario
	private $name_field = 'nombre';	// nombre de quien comenta
	private $email_field = 'email';	// Email de quien comenta
	private $webpage_field = 'web_site';	// Pagina web de quien comenta

	// Campos requeridos
	public $show_name_field = true;
	public $show_email_field = true;
	public $show_webpage_field = true;

	//Valores campos
	public $name_value = '';
	public $email_value = '';
	public $webpage_value = '';
	public $id_user_value = 0;



	public function __construct($CurrentPage = '',$IdObj = '',$Code='',$IdVal='',$IdUser=0){
		try{
			// Page
			if($IdVal==''){
				$IdVal =( isset($_POST['id-t-val']) ? $_POST['id-t-val'] : '' );
			}

			if($CurrentPage=='' || $IdObj=='' || $Code=='' || $IdVal=='' || $IdUser==''){
				throw new FkException(' Es requerido  {CurrentPage,IdObj,Code,idVal,idUser}');
			}else{
				$this->page = $CurrentPage;
				$this->id_obj = $IdObj;
				$this->code = $Code;
				$this->id_tab_val = $IdVal;
				$this->id_user_value = $IdUser;
			}
		}catch(FkException $e){
			$e->description=' fkComments() requiere variables para inicializacion';
			$e->solution='
			           Ej: new fkComments($this->getCurrentPage(),"miObj","comm-producto","1","1")<br><br>
					   $this->getCurrentPage() = la pagina actual;<br>
   					   "miObj" = El nombre que se le quiera dar al objeto;<br>
					   "comm-producto" = codigo referencia, pudiera ser el nombre de la tabla comentada;<br>
   					   "1" = id de referencia, que registro de la tabla fue comentado;
   					   "1" = id del usuario en session (el que comenta);								   
				 ';
			$e->show();
		}

		// Contolar inicializacion plugin
		$oper = (isset($_POST['op'])) ? $_POST['op']: '';
		switch ($oper) {
			case 'save':
				// No imprimir header y footer cuando operacion = save
				fk_no_display_header();
				break;
			case 'del':
				// No imprimir header y footer cuando operacion = del
				fk_no_display_header();
				break;
		}

		$Load = new Load();
		$Load -> Model('Usuarios');



	}
	public function render(){

		$oper = (isset($_POST['op'])) ? $_POST['op']: '';
		switch($oper){
			case 'save':
				#--------------------------
				# Guardar comments
				#--------------------------

				$comment = new ActiveRecord($this->table); // Crea dbRecord para la tabla Comentario

				$comment -> fields[$this->comment_field] = $_POST['comment'];
				$comment -> fields[$this->code_field] = $_POST['code'];
				$comment -> fields[$this->id_table2coment_field] = $_POST['id-t-val'];
				$comment -> fields[$this->id_user_field] = $this->id_user_value;
				$comment -> fields[$this->name_field] = ( $this->name_value != '' ? $this->name_value : @$_POST['name']);
				$comment -> fields[$this->email_field] = ( $this->email_value != '' ? $this->email_value : @$_POST['email']);
				$comment -> fields[$this->webpage_field] = ( $this->webpage_value != '' ? $this->webpage_value : @$_POST['web']);
				$comment -> insert();

				// Encentra el ultimo y muestralo
				//$comment->findLast() :: crear esta function
				$db = new db();
				$db->connect();
				$sql = 'SELECT *,now() as ahora FROM '.$this->table.' WHERE '.$this->code_field.' =  "'.$this->code.'"
		             AND  '.$this->id_table2coment_field.' = "'.$this->id_tab_val.'"
					 ORDER BY '.$this->table_id_field.' DESC LIMIT 1 ;
					 ';

				$db->query( $sql);
				if($rec=$db->next()){
					$this->printOneComent($rec,TRUE);
				}

				break;
			case 'del':
				#--------------------------
				# Guardar comments
				#--------------------------

				$db = new db();
				$db->connect();
				$sql = 'DELETE FROM '.$this->table.'
				        WHERE '.$this->table_id_field.' = '.$db-> escape_string(decode($_POST['i'])).'
				        AND '.$this->id_user_field.' = "'.$_SESSION['id_usuario'].'"';
				$db->query( $sql);
				break;
			default:
				#--------------------------
				# Load Comments
				#--------------------------
				?>
<ul id="fk-commenter-<?=$this->id_obj?>" class="fk-comments">
	<div id="oper-comments-<?=$this->id_obj?>"></div>
	<div id="comments-<?=$this->id_obj?>"><? $this->printComments(); ?></div>
	<div id="new-comments-<?=$this->id_obj?>"></div>
	<div id="leave-<?=$this->id_obj?>"><? $this->leaveAComment(); ?></div>
</ul>
				<?php

				?>
<script language="javascript" type="text/javascript">
 fkComments('<?=$this->id_obj?>','<?=$this->page?>','<?=$this->code?>','<?=$this->id_tab_val?>');
</script>
				<?
				break;
		}

	} // render()

	private function printComments(){
		$db = new db();
		$db->connect();
		$sql = 'SELECT *,now() as ahora FROM '.$this->table.' WHERE '.$this->code_field.' =  "'.$this->code.'"
		             AND  '.$this->id_table2coment_field.' = "'.$this->id_tab_val.'"';
		$db->query_assoc($sql);
		while($rec=$db->next()){
			$this->printOneComent($rec);
		}


	} // End printComments()
	private function printOneComent($rec,$ShowEffect = FALSE){
		//pa($rec);
		$id_obj_com=encode($rec['id_comentario']).'-'.$this-> id_obj;
		$empr = Usuarios::getGeneralData($rec[$this->id_user_field]);
		//$EmprMDL = new EmpresaModel();
		//$empr = $EmprMDL->getEmpresaDataOfUser($rec[$this->id_user_field]);
		
		
			
		?>
<li id="com-<?php echo $id_obj_com;?>">
<div class="user-img"><img style="width: 50px; height: 50px;"
	src="<?php echo $empr['http_imagen'];?>"></div>
		<?php
		if($rec[$this->id_user_field]==$_SESSION['id_usuario']){
			?><a class="del-btn" href="javascript:void(0)"
	onclick="del_coment({o:'<?php echo $this->id_obj;?>',i:'<?php echo encode($rec['id_comentario']);?>',u:'<?php echo $this-> page?>',t:'<?php echo $this->code?>',it:'<?php echo $this->id_tab_val?>'})">Eliminar</a>

			<?php
		}
		?>
<div class="time">Hace <? fk_lapse_of_time($rec['fec_reg'],$rec['ahora']); ?></div>
<b><?php echo @$empr['nombre']?></b>: <?=$rec[$this->comment_field]?></li>
		<?
		if($ShowEffect==TRUE){
			?><script>fk_show("com-<?php echo $id_obj_com;?>");</script><?php

		}

	}

	private function leaveAComment(){

		$usr = Usuarios::getGeneralData($this->id_user_value);

		?>
<li id="li-lv-<?=$this->id_obj?>" class="leave-comment">
<div class="user-img"><img style="width: 50px; height: 50px;"
	src="<?php echo $usr['http_imagen_perfil'];?>"></div>
<table class="user-data">
	<tr>
		<td colspan="2">
		<div id="message-err-<?=$this->id_obj?>" class="fk-error-message"
			style="display: none"></div>
		</td>
	</tr>
	<?php
	if($this->show_name_field == TRUE){
		?>
	<tr>
		<td>Nombre(Requerido):</td>
		<td><input type="text" id="name-user-<?=$this->id_obj?>"
			name="name-user-<?=$this->id_obj?>" value="" /></td>
	</tr>
	<?php
	}

	if($this->show_email_field == TRUE){
		?>
	<tr>
		<td>Email(Requerido):</td>
		<td><input type="text" id="email-user-<?=$this->id_obj?>"
			name="email-user<?=$this->id_obj?>" value="" /></td>
	</tr>
	<?php
	}
	if($this->show_webpage_field == TRUE){
		?>
	<tr>
		<td>Sitio web:</td>
		<td><input type="text" id="web-user-<?=$this->id_obj?>"
			name="web-user<?=$this->id_obj?>" value="" /></td>
	</tr>
	<?php
	}
	?>
</table>
<table class="txt-data">
	<tr>
		<td><textarea id="leave-comment-<?=$this->id_obj?>"></textarea></td>
	</tr>
</table>


<a id="leave-comment-btn-<?=$this->id_obj?>" href="javascript:void(0)"
	class="btn-link1 btn">Comentar</a></li>
	<?


	} // End leaveAComment()
	
	private function leaveAComment(){

		$usr = Usuarios::getGeneralData($this->id_user_value);

		?>
<li id="li-lv-<?=$this->id_obj?>" class="leave-comment">
<div class="user-img"><img style="width: 50px; height: 50px;"
	src="<?php echo $usr['http_imagen_perfil'];?>"></div>
<table class="user-data">
	<tr>
		<td colspan="2">
		<div id="message-err-<?=$this->id_obj?>" class="fk-error-message"
			style="display: none"></div>
		</td>
	</tr>
	<?php
	if($this->show_name_field == TRUE){
		?>
	<tr>
		<td>Nombre(Requerido):</td>
		<td><input type="text" id="name-user-<?=$this->id_obj?>"
			name="name-user-<?=$this->id_obj?>" value="" /></td>
	</tr>
	<?php
	}

	if($this->show_email_field == TRUE){
		?>
	<tr>
		<td>Email(Requerido):</td>
		<td><input type="text" id="email-user-<?=$this->id_obj?>"
			name="email-user<?=$this->id_obj?>" value="" /></td>
	</tr>
	<?php
	}
	if($this->show_webpage_field == TRUE){
		?>
	<tr>
		<td>Sitio web:</td>
		<td><input type="text" id="web-user-<?=$this->id_obj?>"
			name="web-user<?=$this->id_obj?>" value="" /></td>
	</tr>
	<?php
	}
	?>
</table>
<table class="txt-data">
	<tr>
		<td><textarea id="leave-comment-<?=$this->id_obj?>"></textarea></td>
	</tr>
</table>


<a id="leave-comment-btn-<?=$this->id_obj?>" href="javascript:void(0)"
	class="btn-link1 btn">Comentar</a></li>
	<?


	} // End leaveAComment()


}

?>