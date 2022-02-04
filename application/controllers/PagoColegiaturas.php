<?php
session_start();
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );

require_once __DIR__ . "/../libraries/SystemController.php";

class PagoColegiaturas extends CI_Controller
{		
	private $_user = '';
	private $_sid = '';
	private $_hacker = '';
	
	
	public $system_controller;
	public $texto_pie_ficha = "No se aceptan pagos posteriores a la fecha establecida en este documento. 
		En caso de que la informaci&oacute;n de esta ficha sea incorrecta, evite realizar el pago y comun&iacute;quese al tel&eacute;fono 449.910.50.00 ext. 178 de lunes a viernes de 8 a 18 horas o env?e un correo a pagos@utags.edu.mx y solicite la correcci?n.
		<br>RECUERDA QUE AL REALIZAR TU PAGO REFERENCIADO SANTANDER O BANCOMER, SE VERA REFLEJADO EN TU HISTORIAL DEL SISTEMA SITO EN 24 A 48 HORAS HÁBILES, POSTERIORES AL PAGO.
        <br>Para el caso de ex&aacute;menes intercuatrimestrales, independientemente de la vigencia de este documento, el pago deber&aacute; estar realizado antes de su aplicaci&oacute;n.  
		<br>La alteraci&oacute;n y/o modificaci&oacute;n de este documento ocasionar&aacute; su invalidaci&oacute;n, as&iacute; mismo, cualquier pago o procedimiento derivado de dicha acci?n no ser? reconocido por esta instituci?n.
		<br>Para cualquier duda o aclaraci&oacute;n, favor de conservar este documento.
			";
	public $boton_imprimir = "<button type=\"button\" id=\"btn-imprimir\" class=\"btn btn-success\" onClick='#' value='Imprimir'>
		  		<i class=\"fa fa-print\"></i> <span> Imprimir</span></button>
			";
	public $numero_de_convenio;
	public function __construct(){
		parent::__construct ();
		$this->load->library("BanBajio/BanBajioLib");
		$this->load->library("bancos/MultipagosExpressBBVA");
		$this->system_controller = new SystemController();
		$this->_user = @$_REQUEST ['uid'];  // Usuario que viene de SITO
		$this->_sid = @$_REQUEST ['sid'];  // Sesion de SITO
		//$this->_hacker = $this->system_controller->institucion->checarSesion($this->_user, $this->_sid);
		
		/* TERMINAR TODO SI NO ES LA SESION ACTIVA */
		/*if ($this->_hacker){
			echo '<h1>Sesion terminada</h1>';
			exit('Buen intento ;)');
		}
		*/
	}
	
	public function index(){
		
		
		// Checar usuario cargado en SITO
		@$this->system_controller->alumno->setAlumno($this->_user);	/* DEPLOYMENT */				
		
		// Checar que sea alumno
		$es_alumno = @$this->system_controller->alumno->esAlumno();
		
		if ($es_alumno){
			// Obtener matricula
			$matricula = @$this->system_controller->alumno->getMatricula();
			$data['matricula'] = $matricula;
			
			$data['nombre'] = @$this->system_controller->alumno->getNombre();
			
			// Obtener periodo actual
			$periodo = @$this->system_controller->alumno_datos_escolares->getIdPeriodoActual();
			$data['periodo_text'] = @$this->system_controller->alumno_datos_escolares->getPeriodoActual();
			
			// Saber si esta inscrito en base al periodo actual 
			$data['esta_inscrito'] = @$this->system_controller->alumno_datos_escolares->estaInscrito($matricula, $periodo);
						
			// Saber si tiene Beca y su porcentaje
			$id_beca = @$this->system_controller->alumno_datos_escolares->tieneBeca($matricula, $periodo);			
			$data['tiene_beca'] = $id_beca>0?true:false;
			
			$data['tipo_beca'] = @$this->system_controller->alumno_datos_escolares->getTipoBeca($id_beca);
			$data['porcentaje_beca'] = @$this->system_controller->alumno_datos_escolares->getPorcentajeBeca($id_beca);

			// Obtener su selector
			$selector = @$this->system_controller->alumno_datos_escolares->getSelector($matricula, $periodo);
			
			// Titulo selector
			$titulo_selector = @$this->system_controller->alumno_datos_escolares->getTituloPlanSelector($selector);
			
			// Obtener el listado de colegiaturas a pagar
			$data['colegiaturas'] = @$this->system_controller->alumno_adeudos_controller->alumno_adeudos->getColegiaturas($periodo, $matricula);

			// TIENE ADEUDOS DE COLEGIATURA #_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#_#			
			$los_adeudos = @$this->system_controller->alumno_adeudos_controller->alumno_adeudos->tieneAdeudosColegiatura($matricula);
			$data['tiene_adeudos_colegiatura'] = false;
			
			foreach ($los_adeudos as $a){
				if ($a['grupo'] == '13'){
					$data['tiene_adeudos_colegiatura'] = true;
				}				
			}
			/*
			if($this->system_controller->alumno_adeudos_controller->alumno_adeudos->getColegiaturas($periodo, $matricula)){
			    $data['tiene_adeudos_colegiatura'] = false;
			}
			*/
			
			$data['mensaje_adeudo_colegiatura'] = "Para habilitar la impresi&oacute;n de fichas de las siguientes colegiaturas, debe estar registrado el pago del adeudo pendiente.";
			$data['adeudos'] = @$this->system_controller->alumno_adeudos_controller->alumno_adeudos->getAdeudos($matricula);
			
			$data['titulo_selector'] = $titulo_selector;
			
			// Otros pagos
			
			// cve_grupo_concepto - nombre
			$data['grupos'] = @$this->system_controller->alumno_adeudos_controller->alumno_adeudos->getGruposConceptosReferenciados();				
			
		}else{						
			die('Sesion terminada, necesita entrar al modulo con un usuario de alumno');			
		}				
		
		$this->load->view('header');
		$this->load->view('colegiaturasVw',$data);		
		$this->load->view('footer');
	}
	
	
	public function imprimirFicha($es_cxc,$cve,$matricula,$otro,$porcentaje=0,$cie=73431){
		$data['matricula'] = $matricula;		
		
		$data['cie'] = $cie;
		
		$data['texto_pie_ficha'] = $this->texto_pie_ficha;
		$data['boton_imprimir'] = $this->boton_imprimir;
		if ($es_cxc){
			$data['cve_cxc'] = $cve;
			$this->load->view('header');
			$this->load->view('ficha_de_pagoCXC',$data);
			$this->load->view('footer');
		}else{
			$data['cve'] = $cve;
			$datos_ficha = $otro?$this->system_controller->alumno_adeudos_controller->alumno_adeudos->getOtrosConceptos($cve):@$this->system_controller->alumno_datos_escolares->getColegiatura($matricula, $cve);
			
			$data['nombre'] = $datos_ficha['nombre'];
			$data['cve_concepto'] = $datos_ficha['cve_concepto'];
			$monto = $datos_ficha['monto'];
			$data['monto'] = @$this->getMontoDescuentoBeca($monto, $porcentaje);
			
			$data["monto"] = ceil($data['monto']);
			
			
			$this->load->view('header');
			//Excepcion para pruebas de ficha
			if($matricula==149999){//Excepcion para pruebas de ficha
				$this->load->view('ficha_de_pagoBancoAzteca',$data);
			}else{
				$this->load->view('ficha_de_pagoColegiatura',$data);
			}
			$this->load->view('footer');
		}			
	}
	
	public function getMontoDescuentoBeca($monto,$descuento){
		if ($descuento == 0){return ($monto);}
		if ($descuento == 1){return ($monto - (($monto)*0.5));}
		if ($descuento == 2){return ($monto - ($monto*0.75));}
		if ($descuento == 3){return 0;}
		return $monto;
	}
	
	public function desgloceMonto($monto, $monto_cargo){
		$data['monto'] = $monto;
		$data['monto_cargo'] = $monto_cargo;
				
		$this->load->view('detalle_monto',$data);
	}
	
	public function getConceptosReferenciados(){
		if($_REQUEST){
			$grupo = $_REQUEST['grupo']; 
		}else{
			$grupo = '';
		}
		
		echo @$this->system_controller->alumno_adeudos_controller->alumno_adeudos->printConceptosReferenciados($grupo); 
	}
	
	
	
}