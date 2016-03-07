<?php
/****************************************************************************************
 **  fkControl : FkComments
 **  Version   : 0.0.1
 **  Author    : Mmendoza000 mmendoza000@hotmail.com (Sep-2010)
 **  Licence   : GPL
 **
 **  Descripcion: Control Comentarios  para FreeKore
 **  proyecto    fecha      por         descripcion
 **  ----------  ---------  ----------- ----------------
 **  00000001    24/09/10   mmendoza    Creado.
 *****************************************************************************************/

class FkComments
{
	private $page = '';
	private $id_obj = 'obj1';
	private $code = 'videos';
	private $id_tab_val = '0';


	private $allow_anonimo = false;
	private $auth_mode = false; //$auth_mode (1 [auto] | 2 [moderar] | 3 [ moderar anonimos])
	public $show_detail = true;
	public $readonly = false;
	public $must_login = false;
	
	
	public $id_usuario = null;

	// Estructura tabla comentarios
	private $table = 'comentarios'; // tabla
	private $table_id_field	= 'id_comentario'; // id , llave primaria
	private $code_field = 'codigo_tabla'; // Codigo de referencia, que se comenta
	private $id_table2coment_field = 'id_val_tabla'; // Id referencia , id de lo que se comenta
	private $comment_field = 'comentario';	// texto del comentario
	private $name_field = 'nombre';	// nombre de quien comenta
	private $email_field = 'email';	// Email de quien comenta
	private $webpage_field = 'web_site';	// Pagina web de quien comenta







	public function __construct($CurrentPage = '',$IdObj = '',$Code='',$IdVal=''){
		try{
			// Page
			if($IdVal==''){
				$IdVal =( isset($_POST['id-t-val']) ? $_POST['id-t-val'] : '' );
			}

			if($CurrentPage=='' || $IdObj=='' || $Code=='' || $IdVal==''){
				throw new FkException(' Es requerido  {CurrentPage,IdObj,Code,idVal}');
			}else{
				$this->page = $CurrentPage;
				$this->id_obj = $IdObj;
				$this->code = $Code;
				$this->code = $Code;
				$this->id_tab_val = $IdVal;
			}
		}catch(FkException $e){
			$e->description=' fkComments() requiere variables para inicializacion';
			$e->solution='
			           Ej: new fkComments($this->getCurrentPage(),"miObj","comm-producto","1")<br><br>
					   $this->getCurrentPage() = la pagina actual;<br>
   					   "miObj" = El nombre que se le quiera dar al objeto;<br>
					   "comm-producto" = codigo referencia, pudiera ser el nombre de la tabla comentada;<br>
   					   "1" = id de referencia, que registro de la tabla fue comentado;					   
				 ';
			$e->show();
		}

	}
	public function render(){

		$oper = (isset($_POST['op'])) ? $_POST['op']: '';
		switch($oper){
			case 'save':
				#--------------------------
				# Guardar comments
				#--------------------------
				$comment = new db_record($this->table); // Crea dbRecord para la tabla Comentario
					

				$comment -> fields[$this->comment_field] = $_POST['comment'];
				$comment -> fields[$this->code_field] = $_POST['code'];
				$comment -> fields[$this->id_table2coment_field] = $_POST['id-t-val'];
				$comment -> fields[$this->name_field] = $_POST['name'];
				$comment -> fields[$this->email_field] = $_POST['email'];
				$comment -> fields[$this->webpage_field] = $_POST['web'];
				$comment -> fields['id_usuario'] = $this->id_usuario;

				$comment -> save();

				// Encentra el ultimo y muestralo
				//$comment->findLast() :: crear esta function
				$db = new db();
				
				$sql = 'SELECT *,now() as ahora FROM '.$this->table.' c
				left join usuarios usr on usr.id_usuario = c.id_usuario
				left join uploads upl on upl.id_upload = usr.imagen
				
				     WHERE '.$this->code_field.' =  "'.$this->code.'"
		             AND  '.$this->id_table2coment_field.' = "'.$this->id_tab_val.'"
					 ORDER BY '.$this->table_id_field.' DESC LIMIT 1 ;
					 ';

				$db->query( $sql);
				if($rec=$db->next()){ $this->printOneComent($rec); }

				break;
					
			default:
				#--------------------------
				# Load Comments
				#--------------------------
				?>
<ul id="fk-commenter-<?=$this->id_obj?>" class="fk-comments">
	
	<div id="new-comments-<?=$this->id_obj?>"></div>
	<?php if($this->readonly == false){?><div id="leave-<?=$this->id_obj?>"><? $this->leaveAComment(); ?></div><?php 
	}else{
		// si muestra form de envio aunque no este logeado
		if($this->must_login==true){
			?><div id="leave-<?=$this->id_obj?>"><? $this->leaveACommentDisabled(); ?></div><?php
		}
		
	}?>
	<div id="comments-<?=$this->id_obj?>"><? $this->printComments(); ?></div>
</ul>
<script language="javascript" type="text/javascript">
$(function(){
	fkComments('<?=$this->id_obj?>','<?=$this->page?>','<?=$this->code?>','<?=$this->id_tab_val?>');	
});
 
 
</script>
				<?
				break;
		}

	} // render()

	private function printComments(){
		$db = new db();
		
		/*
		$sql = 'SELECT *,now() as ahora FROM '.$this->table.' WHERE '.$this->code_field.' =  "'.$this->code.'"
		             AND  '.$this->id_table2coment_field.' = "'.$this->id_tab_val.'"
		             order by '.$this->table_id_field.' desc
		             ';
		*/
		
		$sql = 'SELECT *,now() as ahora FROM '.$this->table.' c
				left join usuarios usr on usr.id_usuario = c.id_usuario
				left join uploads upl on upl.id_upload = usr.imagen
				
				     WHERE '.$this->code_field.' =  "'.$this->code.'"
		             AND  '.$this->id_table2coment_field.' = "'.$this->id_tab_val.'"
					 ORDER BY '.$this->table_id_field.' DESC LIMIT 100 ;
					 ';
		
		
		$db->query($sql);
		while($rec=$db->next()){
			$this->printOneComent($rec);
		}


	} // End printComments()
	private function printOneComent($rec){
		//pa($rec);
		?>
<li>
<div class="c1_6 col-md-1"><?php 
if($rec['archivo']!=''){
	?><div class="user-img"><img src="<?php echo http_uploads().'/'.$rec['archivo'];?>"></div><?php 
}else{
	?><div class="user-img no-pho"></div><?php
}
?></div>
<div class="c5_6 col-md-11">
<div class="time">Hace <? fk_lapse_of_time($rec['fec_reg'],$rec['ahora']); ?></div>
<b><a href="<?php echo fk_link('perfil/!/'.$rec['usuario']);?>"><?php echo utf8_encode($rec[$this->name_field])?></a></b>: <?=$rec[$this->comment_field]?>
<?php /*<a href="javascript:void(0)" class="right"><i class="fa fa-trash-o"></i></a>*/ /*eliminar delete*/ ?>
</div>
<div class="clear"></div>
</li>
		<?
	}

	private function leaveAComment(){
		
		?>
<li class="leave-comment">

<div class="c1_6 col-md-1 hidden-xs">
<?php
if(Security::is_logged()){
	$imagen = '';
	$db = new db();
	$db->query_assoc('select * from usuarios usr
	left join uploads upl on upl.id_upload = usr.imagen 
	where usr.id_usuario = "'.$_SESSION['id_usuario'].'" 
	');
	if($rec=$db->next()){
		$imagen = $rec['archivo'];
	}
}else{
	$imagen = '';
} 

if($imagen!=''){
	?><div class="user-img"><img src="<?php echo http_uploads().'/'.$imagen;?>"></div><?php 
}else{
	?><div class="user-img no-pho"></div><?php
}?>
</div>
<div class="c5_6 col-md-11 ">
<?php 
if($this->show_detail){
?><table class="user-data">
	<tr>
		<td colspan="2">
		<div id="message-err-<?=$this->id_obj?>" class="fk-error-message"
			style="display: none"></div>
		</td>
	</tr>
	<tr>
		<td>Nombre(Requerido):</td>
		<td><input type="text" id="name-user-<?=$this->id_obj?>"
			name="name-user-<?=$this->id_obj?>" value="" /></td>
	</tr>
	<tr>
		<td>Email(Requerido):</td>
		<td><input type="text" id="email-user-<?=$this->id_obj?>"
			name="email-user<?=$this->id_obj?>" value="" /></td>
	</tr>
	<tr>
		<td>Sitio web:</td>
		<td><input type="text" id="web-user-<?=$this->id_obj?>"
			name="web-user<?=$this->id_obj?>" value="" /></td>
	</tr>
</table><?php	
}
?>
<table class="txt-data">
	<tr>
		<td><textarea id="leave-comment-<?=$this->id_obj?>" class="form-control"></textarea></td>
	</tr>
</table>
<button type="button" class="btn btn-danger btn-xs" id="leave-comment-btn-<?=$this->id_obj?>"> Comentar <i class="fa fa-comment"></i></button>


</div>
<div class="clear"></div>







</li>
		<?


	} // End leaveAComment()

	private function leaveACommentDisabled(){
		
		?>
<li class="leave-comment">

<div class="c1_6 col-md-1 hidden-xs">
<?php
if(Security::is_logged()){
	$imagen = '';
	$db = new db();
	$db->query_assoc('select * from usuarios usr
	left join uploads upl on upl.id_upload = usr.imagen 
	where usr.id_usuario = "'.$_SESSION['id_usuario'].'" 
	');
	if($rec=$db->next()){
		$imagen = $rec['archivo'];
	}
}else{
	$imagen = '';
} 

if($imagen!=''){
	?><div class="user-img"><img src="<?php echo http_uploads().'/'.$imagen;?>"></div><?php 
}else{
	?><div class="user-img no-pho"></div><?php
}?>
</div>
<div class="c5_6 col-md-11 ">
<?php 
if($this->show_detail){
?><table class="user-data">
	<tr>
		<td colspan="2">
		<div id="message-err-<?=$this->id_obj?>" class="fk-error-message"
			style="display: none"></div>
		</td>
	</tr>
	<tr>
		<td>Nombre(Requerido):</td>
		<td><input type="text" id="name-user-<?=$this->id_obj?>"
			name="name-user-<?=$this->id_obj?>" value="" /></td>
	</tr>
	<tr>
		<td>Email(Requerido):</td>
		<td><input type="text" id="email-user-<?=$this->id_obj?>"
			name="email-user<?=$this->id_obj?>" value="" /></td>
	</tr>
	<tr>
		<td>Sitio web:</td>
		<td><input type="text" id="web-user-<?=$this->id_obj?>"
			name="web-user<?=$this->id_obj?>" value="" /></td>
	</tr>
</table><?php	
}
?>
<table class="txt-data">
	<tr>
		<td><textarea id="leave-comment-<?=$this->id_obj?>-disabled" class="form-control disabled" disabled="disabled"> </textarea></td>
	</tr>
</table>
<button type="button" class="btn btn-danger btn-xs" id="leave-comment-btn-<?=$this->id_obj?>-disabled"> Comentar <i class="fa fa-comment"></i></button>


</div>
<div class="clear"></div>







</li>
		<?


	} // End leaveACommentDisabled()

}

?>