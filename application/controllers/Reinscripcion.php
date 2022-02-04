<?php
session_start();
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );


require_once __DIR__ . "/../libraries/SystemController.php";

class Reinscripcion extends CI_Controller
{
	private $_usuarioSITO;	
	private $_generarAdeudo;
	private $_registro;
	private $_periodo_actual;
	
	/**
	 * 
	 * @var SystemController
	 */
	public $system_controller;
	
	public $texto_pie_ficha = "No se aceptan pagos posteriores a la fecha establecida en este documento. 
	En caso de que la informaci&oacute;n de esta ficha sea incorrecta, evite realizar el pago y comun&iacute;quese al tel&eacute;fono 449.910.50.00 ext. 178 de lunes a viernes de 8 a 18 horas o env?e un correo a pagos@utags.edu.mx y solicite la correcci?n.
	<br>Para el caso de ex&aacute;menes intercuatrimestrales, independientemente de la vigencia de este documento, el pago deber&aacute; estar realizado antes de su aplicaci&oacute;n. 
	<br>La alteraci&oacute;n y/o modificaci&oacute;n de este documento ocasionar&aacute; su invalidaci&oacute;n, as&iacute; mismo, cualquier pago o procedimiento derivado de dicha acci?n no ser? reconocido por esta instituci?n.
	<br>Para cualquier duda o aclaraci&oacute;n, favor de conservar este documento. 
			";
	
	public $boton_imprimir = "<button type=\"button\" id=\"btn-imprimir\" class=\"btn btn-success\" onClick='#' value='Imprimir'>
		  		<i class=\"fa fa-print\"></i> <span> Imprimir</span></button>
			";
	

	// CERRAR MODULO DE REINSCRIPCION
	public $cerrado;
	public $cerrado_manual;
	public $cerrado_por_fechas;	
	
	// GENERAR MULTA
	public $activar_multa;
	
	public function __construct(){	
		parent::__construct ();
		$this->system_controller = new SystemController();
		$this->load->library("BanBajio/BanBajioLib");
		$this->load->library("bancos/MultipagosExpressBBVA");
		$this->load->helper("tools");
		$this->_usuarioSITO = @$_REQUEST ['uid'];  // Usuario que viene de SITO
		
		
		/*
		 * ----- VERIFICAR SI ESTA CERRADO EL MODULO
		 */
		$this->cerrado_manual = $this->system_controller->institucion->checarEstadoReinscripcion();
		$this->cerrado_por_fechas = !$this->system_controller->institucion->enFechaDeReinscripcion();
		$this->cerrado = ($this->cerrado_manual || $this->cerrado_por_fechas)?1:0;		
		//$this->cerrado =  false;
		
		/*
		 * ----- VERIFICAR SI ESTAN ACTIVADAS LAS MULTAS
		 */
		$this->activar_multa = $this->system_controller->institucion->checarEstadoMulta();
		$this->_periodo_actual = $this->system_controller->institucion->getIdPeriodoActual();
						
		// BANDERA PARA DESACTIVAR MULTA MANUALMENTE							
		//$this->activar_multa = true;
		
	}
		
	public function index(){	

		$this->load->view('header');
		
		// Verificar si esta cerrado el modulo de REINSCRIPCION
		
		
		if ($this->cerrado){						
		
			die ('<h1>Modulo CERRADO</h1>');
		}
		
		// Checar usuario cargado en SITO		
		$this->system_controller->alumno->setAlumno($this->_usuarioSITO);	

		$es_alumno = $this->system_controller->alumno->esAlumno();
		
		// Bandera para generar multa
		$data['activar_multa'] = $this->activar_multa;
		
		if ($es_alumno){
			// Cargar Datos Escolares del alumno
			@$this->system_controller->alumno_datos_escolares->setAlumnoDatosEscolares($this->system_controller->alumno->getMatricula());
			// Cargar Documentos del alumno
			@$this->system_controller->alumno_documentos_controller->alumno_documentos->setAlumnoDocumentos($this->system_controller->alumno->getMatricula());
			// Cargar Calificaciones del alumno
			@$this->system_controller->alumno_calificaciones_controller->alumno_calificaciones->setAlumnoCalificaciones($this->system_controller->alumno->getMatricula());
			// Cargar Adeudos del alumno
			@$this->system_controller->alumno_adeudos_controller->alumno_adeudos->setAlumnoAdeudos($this->system_controller->alumno->getMatricula());
			// Checar si debe algo
			@$this->system_controller->setAllChecked();			
		}else{			
			die('Sesion terminada, necesita entrar al modulo con un usuario de alumno');			
		}			
		
		$_SESSION['tsu'] = @$this->system_controller->alumno_datos_escolares->getTSU(); 

		// Saber si es su ultimo cuatrimestre
		$data['fin'] = @$this->system_controller->alumno_datos_escolares->terminoCarrera();
		// Saber si es TSU
		$data['tsu'] = @$this->system_controller->alumno_datos_escolares->getTSU(); 
		
		// FUNCION para checar si esta Becado
		//$data['tiene_beca'] = @$this->system_controller->alumno_adeudos_controller->alumno_adeudos->tieneBeca();
		
		// -- Se deshabilitaron los privilegios a becados
		$data['tiene_beca'] = false;
		
		// Verificacion para agregar MULTA por pago extemporaneo
		$data['regular'] = @$this->system_controller->alumno_calificaciones_controller->alumno_calificaciones->esAlumnoRegular($this->system_controller->alumno->getMatricula());
		

		/* Obtener claves de reinscripcion actual */
		$cve_reinscripcion_ing = @$this->system_controller->institucion->getPeriodoReinscripcionING();
		$cve_reinscripcion_tsu = @$this->system_controller->institucion->getPeriodoReinscripcionTSU();
		
		$concepto_reinscripcion = $_SESSION['tsu']?$cve_reinscripcion_tsu:$cve_reinscripcion_ing;
		$data['ya_pago_reinscripcion'] = @$this->system_controller->alumno_adeudos_controller->alumno_adeudos->yaPagoReinscripcion($this->system_controller->alumno->getMatricula(), $this->_periodo_actual);
		$data['fecha_pago_reinscripcion'] = @$this->system_controller->alumno_adeudos_controller->alumno_adeudos->fechaPagoReinscripcion($this->system_controller->alumno->getMatricula(), $this->_periodo_actual);
		
		/*
		 * PARA ADMISION INGENIERÍA
		 */
		// Obtener periodo de admision actual para los de INGENIERIA
		// Esto es para el mensajito con enlace a inscripcion de los que van de TSU a la INGENIERIA
		$data['periodo_actual_admision'] = @$this->system_controller->getPeriodoAdmisionActual();
		// ------------------------------------------------------------------------------------------
		
		// Obtener la clave de concepto ACTUAL de Reinscripcion
		$data['cve_reinscripcion_ing'] = @$this->system_controller->institucion->getPeriodoReinscripcionING();
		$data['cve_reinscripcion_tsu'] = @$this->system_controller->institucion->getPeriodoReinscripcionTSU();								
		
		// Cargar Vista
		$es_alumno? @$this->load->view('reinscripcionVw',$data):@$this->load->view('no-es-alumnoVw');		
		$_SESSION['regular'] = $data['regular'];
		$this->load->view('footer');
	}
	
	
	
	/*
	 * GENERAR ADEUDO INTERCUATRIMESTRAL
	 * 
	 * 
	 * */
		
	public function generarAdeudo($n){				
		$this->_registro = $n;		
		$data['texto_pie_ficha'] = $this->texto_pie_ficha;
		$data['boton_imprimir'] = $this->boton_imprimir;
		$cliente = $this->system_controller->alumno_calificaciones_controller->alumno_calificaciones->getCliente($_SESSION['matricula']);		
		$descripcion = $_SESSION['materia'][$n];
		
		// datos pago extraordinario
		$datos[1] = $_SESSION['matricula'];
		$datos[2] = $_SESSION['materia'][$n];
		$datos[3] = $_SESSION['cve_materia'][$n];
		$datos[4] = $_SESSION['cve_periodo'][$n];
		$datos[5] = $_SESSION['cve_clase'][$n];
		$datos[6] = $_SESSION['cve_docente'][$n];
		$datos[7] = $descripcion;
		
		$pago_inter=$this->system_controller->alumno_adeudos_controller->alumno_adeudos->existeInter($_SESSION['matricula'], $datos[3], $datos[4]);
		if($pago_inter===false){
			$this->system_controller->alumno_adeudos_controller->alumno_adeudos->generarCxC($cliente, $descripcion);
			$id = $this->system_controller->alumno_adeudos_controller->alumno_adeudos->getIdCxC($cliente);
		}else{
			$id=$pago_inter->cve_pago_inter;
		}
		
		$data['cie'] = '73431';
		$datos[0] = $id;
		$this->system_controller->alumno_calificaciones_controller->alumno_calificaciones->generarPagoIntercuatrimestral($datos);
		
		$data['matricula'] = $_SESSION['matricula'];
		$data['cve_cxc'] = $id;
		$this->load->view('header');
		$this->load->view('ficha_de_pagoCXC',$data);
		$this->load->view('footer');	
	}	
	
	public function miModal($n){		
		$data['n'] = $n;
		$this->load->view('aviso',$data);
	}
		
	
	public function imprimirFicha($id){
		$data['matricula'] = $_SESSION['matricula'];
		$data['cve_cxc'] = $id;
		$data['cie'] = '73431';
		$data['texto_pie_ficha'] = $this->texto_pie_ficha;
		$data['boton_imprimir'] = $this->boton_imprimir;
		$this->load->view('header');
		$this->load->view('ficha_de_pagoCXC',$data);
		$this->load->view('footer');
	}
	
	
	public function fichaReinscripcion(){

		/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
		/* @@@    		CERRAR Reinscripcion    			 @@@@ */
		/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
		
		// --- Tambien se deshabilita el BOTON desde reinscripcionView <--
		//die("<h1>PERIODO DE REINSCRIPCION CERRADO</h1>");
		
		$data['matricula'] = $_SESSION['matricula'];
		$data['texto_pie_ficha'] = $this->texto_pie_ficha;
		$data['boton_imprimir'] = $this->boton_imprimir;
				
		$data['activar_multa'] = $this->activar_multa;
		
		
		/* Obtener claves de reinscripcion actual */				
		
		// --- Checar en models/institucion ---
		// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		// Estas ya no se van a utilizar, ya que se vera la tabla de inscripcion en lugar de la de pago
		// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@		
		$cve_reinscripcion_ing = @$this->system_controller->institucion->getPeriodoReinscripcionING();
		$cve_reinscripcion_tsu = @$this->system_controller->institucion->getPeriodoReinscripcionTSU();
			
				
		/* Aplicar clave de acuerdo a si es TSU o ING */		
		$data['cve'] = $_SESSION['tsu']?$cve_reinscripcion_tsu:$cve_reinscripcion_ing;				

		
//TODO
// Ya no se va a usar lo de regular?
// Preguntar a control escolar
		
		//$regular = $_SESSION['regular']; // original
		$regular = true; // @autor jguerrero para cobrarle multa a todos  
		$data['regular'] = $regular;
		
		
		/*
		 * ---------------------------------------------------------
		 * ---------------------------------------------------------
		 * --- AQUI ES DONDE SE HACEN LOS CARGOS DE  M U L T A S ---
		 * ---------------------------------------------------------
		 * ---------------------------------------------------------
		 */
		
		if (($regular)&&($this->activar_multa)){
		    
			$cliente = @$this->system_controller->alumno_calificaciones_controller->alumno_calificaciones->getCliente($data['matricula']);
			/* GENERAR MULTA POR PERIODO EXTRAORDINARIO */
			$data['cve_cxc']=$this->system_controller->alumno_adeudos_controller->alumno_adeudos->generarMulta($cliente);
		}
		
		/*
		 * ------------------------------------------------------------
		 */
		
		$this->load->view('header');
		$this->load->view('ficha_de_pagoReinscripcion',$data);
		$this->load->view('footer');
	}		
}