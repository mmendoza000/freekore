<?php
class FkUploader{

	public  $CurrentPage='';
	private $Action = '';
	public $Directory ='';
	public $HttpDirectory = '';
	public $IdObj='';
	public $subfolder = '';

	public $uploadMethod = '';


	// FIle
	public $file_name = "";
	public $file_size = "";
	public $file_type = "";
	public $file_ext = "";
	public $file_title = "";
	public $file_leyend = "";
	public $file_desc = "";

	// Table db
	private $table = 'uploads';
	private $fld_id = 'id_uploads';
	private $fdl_id_user = 'id_usuario';
	private $fdl_id_cuenta = 'id_cuenta';
	private $fld_path = 'ruta';
	private $fld_type = 'tipo';
	private $fld_file_ext = 'file_ext';

	private $fld_size = 'size';
	private $fld_file = 'archivo';
	private $fld_title = 'titulo';
	private $fld_leyend = 'leyenda';
	private $fld_desc = 'descripcion';

	public $record_id = 0;

	private $fld_upload_date = 'fecha_reg';
	private $fld_updated_date = 'fecha_mod';


	private $Form = '';

	public $FileRecord ="";

	public $UploadDetails = array(); // Datos del archivo subido

	public $AllowedFiles = array('jpeg','jpg','jpe','png','tif','bmp','gif','doc','docx','pdf','xls','xlsx','ppt','pptx','odt','xml'); // Default allowed files

	/**
	 * The accepted file/mime types
	 * @access public
	 * @var array
	 */
	public $mimeTypes = array(
		'image' => array(
			'bmp'	=> 'image/bmp',
			'gif'	=> 'image/gif',
			'jpe'	=> 'image/jpeg',
			'jpg'	=> array('image/jpeg', 'image/pjpeg'),
			'jpeg'	=> 'image/jpeg',
			'pjpeg'	=> 'image/pjpeg',
			'svg'	=> 'image/svg+xml',
			'svgz'	=> 'image/svg+xml',
			'tif'	=> 'image/tiff',
			'tiff'	=> 'image/tiff',
			'ico'	=> 'image/vnd.microsoft.icon',
			'png'	=> array('image/png', 'image/x-png'),
			'xpng'	=> 'image/x-png'
			),
		'text' => array(
			'txt' 	=> 'text/plain',
			'asc' 	=> 'text/plain', 
			'css' 	=> 'text/css',  
			'csv'	=> 'text/csv',
			'htm' 	=> 'text/html',
			'html' 	=> 'text/html', 
			'stm' 	=> 'text/html', 
			'rtf' 	=> 'text/rtf', 
			'rtx' 	=> 'text/richtext', 
			'sgm' 	=> 'text/sgml',
			'sgml' 	=> 'text/sgml', 
			'tsv' 	=> 'text/tab-separated-values', 
			'tpl' 	=> 'text/template', 
			'xml' 	=> 'text/xml',
			'js'	=> 'text/javascript',
			'xhtml'	=> 'application/xhtml+xml',
			'xht'	=> 'application/xhtml+xml',
			'json'	=> 'application/json'
			),
		'archive' => array(
			'gz'	=> 'application/x-gzip',
			'gtar'	=> 'application/x-gtar',
			'z'		=> 'application/x-compress',
			'tgz'	=> 'application/x-compressed',
			'zip'	=> 'application/zip',
			'rar'	=> 'application/x-rar-compressed',
			'rev'	=> 'application/x-rar-compressed',
			'tar'	=> 'application/x-tar'
			),
		'audio' => array(
			'aif' 	=> 'audio/x-aiff', 
			'aifc' 	=> 'audio/x-aiff',
			'aiff' 	=> 'audio/x-aiff', 
			'au' 	=> 'audio/basic', 
			'kar' 	=> 'audio/midi', 
			'mid' 	=> 'audio/midi',
			'midi' 	=> 'audio/midi', 
			'mp2' 	=> 'audio/mpeg', 
			'mp3' 	=> 'audio/mpeg', 
			'mpga' 	=> 'audio/mpeg',
			'ra' 	=> 'audio/x-realaudio', 
			'ram' 	=> 'audio/x-pn-realaudio', 
			'rm' 	=> 'audio/x-pn-realaudio',
			'rpm' 	=> 'audio/x-pn-realaudio-plugin', 
			'snd' 	=> 'audio/basic', 
			'tsi' 	=> 'audio/TSP-audio', 
			'wav' 	=> 'audio/x-wav',
			'wma'	=> 'audio/x-ms-wma'
			),
		'video' => array(
			'flv' 	=> 'video/x-flv',
			'fli' 	=> 'video/x-fli', 
			'avi' 	=> 'video/x-msvideo', 
			'qt' 	=> 'video/quicktime',
			'mov' 	=> 'video/quicktime',
			'movie' => 'video/x-sgi-movie',
			'mp2' 	=> 'video/mpeg', 
			'mpa' 	=> 'video/mpeg', 
			'mpv2' 	=> 'video/mpeg',  
			'mpe' 	=> 'video/mpeg', 
			'mpeg' 	=> 'video/mpeg', 
			'mpg' 	=> 'video/mpeg', 
			'mp4'	=> 'video/mp4',
			'viv' 	=> 'video/vnd.vivo', 
			'vivo' 	=> 'video/vnd.vivo',
			'wmv'	=> 'video/x-ms-wmv'
			),
		'application' => array(
			'js'	=> 'application/x-javascript',
			'xlc' 	=> 'application/vnd.ms-excel',
			'xll' 	=> 'application/vnd.ms-excel', 
			'xlm' 	=> 'application/vnd.ms-excel', 
			'xls' 	=> 'application/vnd.ms-excel',
			'xlw' 	=> 'application/vnd.ms-excel',
			'doc'	=> 'application/msword',
			'dot'	=> 'application/msword',
			'pdf' 	=> 'application/pdf',
			'psd' 	=> 'image/vnd.adobe.photoshop',
			'ai' 	=> 'application/postscript',
			'eps' 	=> 'application/postscript',
			'ps' 	=> 'application/postscript'
			)
			);

			function __construct(){

				if($_SERVER['REQUEST_METHOD']=="GET"){
					$Act = @$_GET["action"];
				}else{
					$Act = @$_POST["action"];
				}
				$this->Action = $Act;




				// Casos en los que no muestra header 6 footer
				if($this->Action== 'upload'){
					//fk_header_blank(); // Muestra template en blanco
				}
				if($this->Action== 'form'){
					fk_header_blank(); // Muestra template en blanco
				}
				if($this->Action== 'update-file-desc'){
					fk_header_blank(); // Muestra template en blanco
				}

				// Crear DbRecord obj
				$this->FileRecord = new ActiveRecord($this->table);

			}

			public function Render($GetCurrentPage,$id_obj){

				$this->IdObj = $id_obj;
				$result = '';

				$this->CurrentPage = $GetCurrentPage;

				switch ($this->Action) {
					case 'upload':

						// Accion Upload

						$result .= $this->upload_file(); // Sube archivo
						$result .= $this->upload_form(); // Muestra form uploader
						$result .= $this->uploaded_file(); // Muestra archivo subido

						break;
					case 'form':

						//Accion formulario
						$result = $this->upload_form();
						break;

					case 'update-file-desc':

						//Accion formulario
						$result = $this->update_file_data(); // Actualiza datos de archivo
						$result .= $this->upload_form(); // Muestra form uploader
						$result .= $this->uploaded_file(); // Muestra archivo subido
						break;
					default:

						//Accion formulario
						$result = $this->print_iframe();
						break;
				}

				return $result;

			} // End Render

			public function upload_file($ShowUpload=TRUE){

				$id_file_cnt = 1; // Contador de archivos subidos

				if($this->Directory!= ''){

					// obtenemos los datos del archivo
					$file_ext = fkore::file_ext($_FILES["archivo"]["name"]);
					$id_usr = isset($_SESSION['id_usuario'])?$_SESSION['id_usuario']:'0';
					$archivo = $id_usr.'_'.date('YmdHis').'_'.substr(encode($_FILES["archivo"]['name']), 0,6).'.'.$file_ext;



					$this->file_name = $archivo;
					$this->file_size = $_FILES["archivo"]['size'];
					$this->file_type = $_FILES["archivo"]['type'];
					$this->file_ext  = $file_ext;
					$this->file_title = $_FILES["archivo"]["name"];

					//$tamano = $_FILES["archivo"]['size'];
					//$tipo = $_FILES["archivo"]['type'];


					$file_extencion_allowed = $this-> verifyAllowedFileType();

					if ($archivo != "" && $file_extencion_allowed == TRUE) {

						// (mmendoza )Removido
						//$this->forsa_creacion_dir($this-> Directory);
						$subfolder = $this->subfolder_creation();

						// guardamos el archivo a la carpeta files
						$destino =  $this-> Directory."/".$subfolder.$archivo;

						if (copy($_FILES['archivo']['tmp_name'],$destino)) {
							// Guarda referencia en la db
							//$this->FileRecord-> fields[$this->fld_id] = 0; es new
							$this->FileRecord -> fields[$this->fdl_id_user] = $_SESSION['id_usuario'];
							$this->FileRecord -> fields[$this->fdl_id_cuenta] = $_SESSION['id_cuenta'];
							$this->FileRecord-> fields[$this->fld_path] = $this->Directory;
							$this->FileRecord-> fields[$this->fld_type] = $this->file_type;
							$this->FileRecord-> fields[$this->fld_file_ext] = $this->file_ext;
							$this->FileRecord-> fields[$this->fld_size] = $this->file_size;
							$this->FileRecord -> fields[$this->fld_file] = $this->file_name;
							$this->FileRecord -> fields[$this->fld_title] = $this->file_title;
							$this->FileRecord -> fields[$this->fld_leyend] = $this->file_leyend;
							$this->FileRecord -> fields[$this->fld_desc] = $this->file_desc;
							$this->FileRecord -> fields[$this->fld_updated_date] = date('Y-m-d H:i:s');
							$this->FileRecord -> fields['upload_method'] = $this->uploadMethod;
							$this->FileRecord->insert();

							$this->record_id =  $this->FileRecord-> inserted_id();

							$this->FileRecord-> fields[$this->fld_id] = $this->FileRecord-> inserted_id();


							if($ShowUpload==TRUE){echo fk_ok_message(__('El archivo subió correctamente'));}

							$this->UploadDetails[$id_file_cnt]['file'] = $this->FileRecord -> fields;
							$this->UploadDetails[$id_file_cnt]['file']['http_path'] = $this-> HttpDirectory.'/'.$archivo;

						} else {

							try {
								throw new FkException('Error al subir archivo');
							} catch (FkException $e) {
								$e ->  show();
							}
						}
					} else {
						echo fk_alert_message('Hubo un error al subir archivo');
						$this->UploadDetails[$id_file_cnt]['error'][] = 'Extencion invalida o no hay archivo';
					}


				}else{
					try {
						throw new FkException('$this->Directory no esta definido');
					} catch (FkException $e) {
						$e ->  show();
					}
				}


			} // End

			private function forsa_creacion_dir($dir){
				$temp_dir='';
				$d_arr = explode('/',$dir);
				$d_arr_2 = explode('\\',$dir);
				if($d_arr>$d_arr_2){
					$explode_char = '/';
				}else{
					$explode_char = '\\';
					$d_arr = $d_arr_2;
				}

				foreach($d_arr as $k => $v){
					echo $temp_dir = $temp_dir.$explode_char.$v;
					echo '<hr>';

					$temp_dir = ltrim($temp_dir,$explode_char);
					if(!is_dir($temp_dir)){
						mkdir($temp_dir, 0755);
						//echo 'creando'.$temp_dir;
					}

				}

			} // forsa_creacion_dir
			public function upload_form(){

				$HTMLForm='';
				$this->Form = 'frm-upload'.$this->IdObj;

				$HTMLForm = '<form id="'.$this->Form.'" name="'.$this->Form.'" target="frame-upload-'.$this->IdObj.'" action="'.fk_link($this->CurrentPage).'" method="post" enctype="multipart/form-data">
      <input name="archivo"  id="archivo" type="file" size="100" />
      <input name="btn-subir"  id="btn-subir" type="submit" value="Subir" />
      <input name="file-name" id="file-name" type="hidden" value="" />
	  <input name="action" type="hidden" value="upload" />	  
	</form>
	
	
	<div id="upload-result"></div>
	<script>
	$("#archivo").change(function() {
	  $("#'.$this->Form.'").submit();
    });
	</script>';


				return $HTMLForm;

			} // upload_form




			public function print_iframe(){
				$iframe =  '<iframe name="frame-upload-'.$this->IdObj .'" src="'.fk_link($this->CurrentPage).'?action=form"  id="frame-upload-'.$this->IdObj.'" frameborder="0" style="width:100%;height:200px;">
	          </iframe>';

				return $iframe;

			}

			private function uploaded_file(){
				$rs = $this-> file_form();
				return $rs;
			}

			private function file_form(){

				$fileForm = '<form id="'.$this->Form.'" name="'.$this->Form.'" target="frame-upload-'.$this->IdObj.'" action="'.fk_link($this->CurrentPage).'" method="post" >
								<table width="736" class="table-upload" style="display:none;" border="0" cellpadding="0" cellspacing="0" >
				  <tr>
				    <td width="93" rowspan="3" scope="col">
				    '.$this->HttpDirectory.'
				    <img  src="x"/>
				  <td width="633" scope="col"><strong>Nombre de archivo:</strong> '.$this->FileRecord-> fields[$this->fld_file].'  </tr>
				  <tr>
				    <td><strong>Tipo de archivo:</strong>'.$this->FileRecord-> fields[$this->fld_type].'</td>
				  </tr>
				  <tr>
				    <td><strong>Tama&ntilde;o:</strong>'.$this->FileRecord-> fields[$this->fld_size].'</td>
				  </tr>
				  <tr>
				    <td>Tutulo:</td>
				    <td><span class="field">
				      <input name="title" id="title" type="text" class="text" value="'.$this->FileRecord-> fields[$this->fld_title].'" />
				    </span></td>
				  </tr>
				  <tr>
				    <td>Leyenda:</td>
				    <td><span class="field">
				      <input name="leyend" id="leyend" type="text" class="text" value="'.$this->FileRecord-> fields[$this->fld_leyend].'" />
				    </span></td>
				  </tr>
				  <tr>
				    <td>Desc:</td>
				    <td><span class="field">
				      <input type="text" name="desc" id="desc" value="'.$this->FileRecord-> fields[$this->fld_desc].'"/>
				    </span></td>
				  </tr>  
				  <tr>
				    <td>&nbsp;</td>
				    <td><label>
				      <input type="submit" name="save" id="save" value="Guardar" />
				      <input type="button" name="usethis" id="usethis" value="Seleccionar" onclick="alert(123)" />
				    </label></td>
				  </tr>
				</table>
				<input name="action" type="hidden" value="update-file-desc" />
				<input name="id" type="hidden" value="'.$this-> record_id .'" />
				</form>
				<script>
				var options = {};
				$( ".table-upload" ).show( "blind", options, 500);
				</script>
				
				';

				return $fileForm;

			}
			private function update_file_data(){

				// Guarda referencia en la db
				$this->record_id = $_POST['id'];
				$this->FileRecord->find($_POST['id']);
				//$this->FileRecord-> fields[$this->fld_id] = $_POST['id'];
				//$this->FileRecord -> fields[$this->fdl_id_user] = $_SESSION['id_usuario'];
				//$this->FileRecord-> fields[$this->fld_path] = $this->Directory;
				//$this->FileRecord-> fields[$this->fld_type] = $this->file_type;
				//$this->FileRecord -> fields[$this->fld_file] = $this->file_name;
				$this->FileRecord -> fields[$this->fld_title] = $_POST['title'];
				$this->FileRecord -> fields[$this->fld_leyend] = $_POST['leyend'];
				$this->FileRecord -> fields[$this->fld_desc] = $_POST['desc'];
				$this->FileRecord -> fields[$this->fld_updated_date] = date('Y-m-d H:i:s');
				$this->FileRecord-> update();

				echo fk_ok_message(__('Cambios Guardados'));
			}

			public function getFileList(){

				$db = new db();
				$db-> connect();

				$db->query('SELECT * FROM '.$this->table.' ;');
				$list = '';
				while($rec = $db->next()){

					$list .=  $rec['archivo'].'<br>';

				}

				$db->close();

				return $list;
					
			}
			private function verifyAllowedFileType(){
				$rs = FALSE;


				foreach ($this->AllowedFiles as $allowed_ext){
					$arr_allowed_files[] = strtolower($allowed_ext);
				}
				$this-> file_ext = strtolower($this-> file_ext);

				if(in_array($this-> file_ext, $arr_allowed_files)){
					$rs =TRUE;
				}

				return $rs;
					
			}

			public function delete_file($id){

				$rs = false;
				$File = new ActiveRecord('uploads');
				if($File->find($id)){
					$subfolder = ($File->fields['folder']!='')?$File->fields['folder'].'/':'';
					$path  = $File->fields['ruta'].$subfolder.$File->fields['archivo'];
					$File->delete();
					if(@unlink($path)){
						$rs = true;
					}
				}

				return $rs;
					
			} // delete_file

			private function subfolder_creation(){

				$this->subfolder = trim($this->subfolder);

				if($this->subfolder!=''){
					$folder = $this->Directory.$this->subfolder;
					//check if directory exist
					if (!is_dir($folder)){
						// Crear directorio
						mkdir($folder, 0700);
					}
					return $this->subfolder.'/'; // divicion para el archivo folder/archivo
				}else{
					return $this->subfolder;
				}


					
			} // subfolder_creation

}