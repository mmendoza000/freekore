<?php
class IndexController extends AppController{

	public function index(){
		
		
	
		
			
		# Tittle pagina
		$this->page_title(' Freekore App');

		# Descripcion pagina
		$this->page_description('My Freekore Web App ');
			
		# Keywords pagina
		$this->page_keywords(' FreeKore , Programacion agil php');

		# Menu seleccionado
		$this->menu(1,'inicio');
		
		fk_header();
		$this->load->view('index/index.php');
		fk_footer();



	}

} // End IndexController