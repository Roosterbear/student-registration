<?php
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );

require_once __DIR__ . "/../models/Alumno.php";
require_once __DIR__ . "/AlumnoDatosEscolares.php";
require_once __DIR__ . "/AlumnoAdeudosController.php";
require_once __DIR__ . "/AlumnoDocumentosController.php";
require_once __DIR__ . "/AlumnoCalificacionesController.php";
require_once __DIR__ . "/../models/Institucion.php";

class SystemController
{
	private $_all_checked;
	private $_documentos;
	private $_calificaciones;
	private $_adeudos;
	private $_baja;
	
	public $alumno;
	public $alumno_datos_escolares;
	public $alumno_adeudos_controller;
	public $alumno_documentos_controller;
	public $alumno_calificaciones_controller;	
	public $institucion;
	

	public function __construct(){
		$this->alumno = new Alumno();
		$this->alumno_datos_escolares = new AlumnoDatosEscolares();
		$this->alumno_adeudos_controller = new AlumnoAdeudosController();
		$this->alumno_documentos_controller = new AlumnoDocumentosController();
		$this->alumno_calificaciones_controller = new AlumnoCalificacionesController();
		$this->institucion = new Institucion();
		
	}
	
	public function setAllChecked(){
		$this->_documentos = @$this->alumno_documentos_controller->alumno_documentos->allChecked();
		$this->_calificaciones = @$this->alumno_calificaciones_controller->alumno_calificaciones->allChecked();
		$this->_adeudos = @$this->alumno_adeudos_controller->alumno_adeudos->allChecked();
		$this->_baja = @$this->alumno->baja();
		$this->_all_checked = (($this->_documentos)&&($this->_calificaciones)&&($this->_adeudos)&&(!$this->_baja))?true:false;
	}
	
	public function allChecked(){
		return $this->_all_checked;
	}
	
	public function getMensajeCompleted(){
		$mensaje = !$this->_baja?'':'<span class="rojo">El alumno est&aacute; dado de baja <i class="fa fa-times"></i></span>';
		return $mensaje;
	}	
	
	public function getPeriodoAdmisionActual(){
		return @$this->institucion->getPeriodoAdmisionActual();
	}
	
	public function getInstituciones(){
		return @$this->institucion->getInstituciones();
	}
	
	public function getHowToEnter(){
		return '5010control35';
	}
}