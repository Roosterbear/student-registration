<?php
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );

require_once __DIR__ . "/../libraries/SystemController.php";

class DataConfiguration extends CI_Controller
{	
	private $_usuarioSITO;
	private $_clave_acceso;
	private $_usuario_autorizado;
	
	public $system_controller;
	
	// Se utiliza la tabla: configReinscripcion
	
	public function __construct(){
		parent::__construct ();
		$this->system_controller = new SystemController();
		
		
		/*
		 * --------- DESARROLLO [SOLO PRUEBAS]
		 */
		$this->_usuarioSITO = isset($_REQUEST ['uid'])?$_REQUEST ['uid']:'externo';
		
		
		/*
		 * --------- PRODUCCION
		 */
		//$this->_usuarioSITO = $_REQUEST ['uid'];
		
		
		
		/*
		 * PRIMER ACCESO POR USUARIO
		 * 
		 */
		$this->_usuario_autorizado = in_array($this->_usuarioSITO, $this->system_controller->institucion->getPermitidos());
		
		
		/*
		 * 
		 * SEGUNDO ACCESO POR CLAVE
		 */
		$this->_clave_acceso = $this->system_controller->institucion->getAcceso();
		
	}
	
	public function index(){
		$this->load->view('headerConfig');	
		
 		
		if ($this->_usuario_autorizado){
 
			$clave_acceso_enviada = isset($_REQUEST['acceso'])?$_REQUEST['acceso']:0;			 
			if ($clave_acceso_enviada === $this->_clave_acceso){
				
				$data['periodos'] = $this->system_controller->institucion->getAllPeriodosText();
				$data['listado_multas'] = $this->getListadoMultasReinscripcion();
				$data['ultimo_periodo'] = $creado = $this->system_controller->institucion->getUltimoPeriodo();
		
				
				/*
				 * Para obtener las fechas guardadas del periodo mas reciente
				 * (se extrae solo la parte de la fecha [primeros 10 caracteres])
				 *  
				 */
				 
				$data['fecha_inicio'] = substr($this->system_controller->institucion->getFechaInicioReinscripcion(),0,10);
				$data['fecha_fin'] = substr($this->system_controller->institucion->getFechaFinReinscripcion(),0,10);
				
				/*
				 * SE CARGA SISTEMA DE CONFIGURACIÓN
				 */
				$this->load->view('dataConfigVw',$data);
			}else{
				/*
				 * SE PIDE LA CLAVE CADA VEZ QUE NO SE INGRESA LA CORRECTA				 * 
				 */
				$this->load->view('introConfigVw');				
			}
		}else{
			echo '<h2 class="alerta">Tu usuario en SITO no est&aacute; autorizado</h2>';
		}
			$this->load->view('footer');
			
	}
	
	
	
	/*
	 * -----CREAR 
	 */
	public function crearPeriodo(){
		$id = $_REQUEST['id'];		
		
		if ($this->isThisPeriodoCreated($id)){
			echo '<div class="mensaje_dinamico alerta">Este periodo ya ha sido creado anteriormente</div>';
		}else{
			
			$this->system_controller->institucion->crearPeriodoReinscripcion($id);
			echo '<div class="mensaje_dinamico informacion">Periodo creado</div>';
		}
	}	
	
	
	public function getUltimoPeriodo(){
		echo $this->system_controller->institucion->getUltimoPeriodo();
	}
	
	
	public function isThisPeriodoCreated($id){
		$creado = $this->system_controller->institucion->checarSiPeriodoEstaActivo($id);
		return $creado<>''?1:0;
	}
	
	
	/*
	 * -----ACTIVAR
	 */
	public function activarReinscripciones(){				
		echo $this->system_controller->institucion->activarReinscripcion();
	}
	
	public function desactivarReinscripciones(){
		echo $this->system_controller->institucion->desactivarReinscripcion();
	}
	
	public function reinscripcionActiva(){		
		echo @$this->system_controller->institucion->checarEstadoReinscripcion();
	}
	
	
	/*
	 * -----MULTAS
	 */
	public function activarMultas(){
		echo $this->system_controller->institucion->activarMulta();
	}
	
	public function desactivarMultas(){
		echo $this->system_controller->institucion->desactivarMulta();
	}
	
	public function multaActiva(){
		echo @$this->system_controller->institucion->checarEstadoMulta();
	}
	
	public function getListadoMultasReinscripcion(){
		return $this->system_controller->institucion->listadoMultasReinscripcion();
	}
	
	public function setMulta(){
		$multa = $_REQUEST['multa'];
		if ($this->system_controller->institucion->cambiarMulta($multa)){
			echo '<div class="mensaje_dinamico informacion">Multa cambiada</div>';
		}else{
			echo '<div class="mensaje_dinamico alerta">Ocurri&oacute; un ERROR</div>';
		}
		
	}
	
	/*
	 * -----MODIFICAR
	 */
	public function eliminarPeriodo(){
		$this->system_controller->institucion->eliminarPeriodo();
	
		echo '<div class="mensaje_dinamico alerta">Periodo eliminado!</div>';
	}
	
	public function actualizarFecha(){
		$fecha_inicio = $_REQUEST['fecha_inicio'];
		$fecha_fin = $_REQUEST['fecha_fin'];
	
		$this->system_controller->institucion->updateFechaInicio($fecha_inicio);
		$this->system_controller->institucion->updateFechaFin($fecha_fin);
	
	
		echo '<div class="mensaje_dinamico informacion">Fechas cambiadas</div>';
	}
}
