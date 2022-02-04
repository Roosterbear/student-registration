<?php
session_start();
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );

require_once __DIR__ . "/../libraries/SystemController.php";

class Convenios extends CI_Controller
{
	//private $_usuarioSITO;	
	
	
	public $system_controller;
	
	public function __construct(){
		parent::__construct ();
		$this->system_controller = new SystemController();
	//	$this->_usuarioSITO = @$_REQUEST ['uid'];  // Usuario que viene de SITO
	}
	
	public function index(){
		$data['grupos'] = $this->system_controller->alumno_adeudos_controller->alumno_adeudos->getTodosLosGruposConceptos();
		$data['instituciones'] = $this->system_controller->getInstituciones();
		
		$this->load->view('header');
		$this->load->view('conveniosVw',$data);
		$this->load->view('footer');
	}
	
	public function getConceptosGrupos(){
		if($_REQUEST){
			$grupo = $_REQUEST['grupo'];
		}else{
			$grupo = '';
		}
	
		echo @$this->system_controller->alumno_adeudos_controller->alumno_adeudos->printConceptosGrupos($grupo);
	}
	
	public function altaInstitucion(){
		if($_REQUEST){
			$institucion = $_REQUEST['institucion'];
		}else{
			$institucion = '';
		}
		
		@$this->system_controller->institucion->insertarInstitucion($institucion);
	}
	
}
