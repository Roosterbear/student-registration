<?php
require_once "/var/www/html/admision/application/libraries/Cajas/IntegrarPagos.php";//FIXME revisar la necesidad de copiar el codigo
session_start();
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );


class MultipagosExpress extends CI_Controller
{
	private $_usuarioSITO;	
	private $_generarAdeudo;
	private $_registro;
	private $_periodo_actual;
	

	public function __construct(){	
		parent::__construct ();
		$this->load->library("bancos/"."MultipagosExpressBBVA");
		$this->load->library("bancos/"."MultipagosExpressResponse");
		$this->load->helper("tools");
	}
		
	public function index(){
	    echo"<pre>";
	    //print_r($_REQUEST);

	    try {
	        if(count($_POST)<1){
	            //show_error("Solicitud no autorizada",401,"Acceso Denegado");
	            throw new Exception("Solicitud no autorizada",401);
	        }
	        //recorremos el request y asignamos los valores al objeto
	        $response= new MultipagosExpressResponse();
	        foreach ($_REQUEST as $var=>$val){
	            $metodo="set$var";
	            if(method_exists($response, $metodo)){
	                $response->$metodo($val);
	            }else{
	                $response->extra[$var]=$val;
	            }
	        }
	        
	        //Revisamos que el tipo de transaccion sea valida 
	        
	        
	        
	        //revisamos la validez de la firma
	        if($response->esFirmaValida()){
	            throw new Exception("Solicitud Invalida. Los datos proporcinados no coinciden. ");
	        }
	        
	        
	        //parseamos la informacion al objeto para su carga 
	        $pago=$response->convertirEnPago();
	        print_r($pago);
	        
	        //Cargamos el pago 
	        //$cajas=new IntegrarPagos();
	        //$cajas->procesar($pago);
	        
	        //Enviamos mensaje de 
	        
	        
	    }catch (Exception $e){
	        //ERRORES GLOBALES DE LA APLICACION no requieren interfaz
	        $codigo=$e->getCode()==null?500:$e->getCode();
	        show_error($e->getMessage(),$e->getCode());
	    }
	    
	}
	
	
}