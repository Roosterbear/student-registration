<?php defined('BASEPATH') OR exit('No direct script access allowed');

/** 
 * @author jguerrero
 * 
 */
class PagoBanBajio extends \CI_Controller
{
    
	/**
	 *
	 * @var BBEnvioBanco
	 * @var BBEnvioCliente
	 * @var BanBajioLib
	 * 
	 */
	public $moduloCliente, $moduloBanco, $lib;
	
    public function __construct()
    {	
    	parent::__construct();
        $this->load->library("BanBajio/BanBajioLib",null, "lib");
        
    }
    
    public function index(){
        /*
        $envio= new BBEnvioCliente();
        $envio->setFolio(1);
        $envio->setReferencia(1234567890);
        $envio->setMonto(100.15);
        $envio->setConcepto("02");
        $envio->setServicio("01");        
        //$envio->setHash( base64_encode($envio->GenerarCadena()));
        
        
        $this->lib->FirmarEnvio($envio);
        
        pre($envio);
        
        $ok=$this->lib->verificarEnvioCliente($envio);
        var_dump($ok);
        */
        $cadenaEncriptada =   "123|456|485.22|01|03|";
        $base64= "CJ580qUKzlH3wxhgT9vy9T_VNCNg_c4BO8zxVrzhbqo4MjkG-h2-ClFyXlimxbVUiP3Kuw0YhlT0q8yOBcd3U84LYphA6-wcI0UtGjP6Es57HTu05_T5B2UtQs6Tb5QhGtvfkuEybd4TO8oJ5QODuu-36OWIA4fMYTTSG80kaVs,";
        $expression=$this->lib->VerifyData($base64, $cadenaEncriptada, $this->lib->getPublicKey());
        var_dump($expression);
    }
    
    public function InsertarDatosCliente(){
    	$ins= new BBEnvioCliente();
    	$ins->setFolio(1);
    	$ins->setReferencia(1234567890);
    	$ins->setMonto(100.15);
    	$ins->setConcepto("02");
    	$ins->setServicio("01");
    	$ins->setHash( base64_encode($ins->GenerarCadena()));
    	    	
    	$this->lib->insertData($ins);
    }
    
    public function ActualizarDatosCliente(){
    	$ins= new BBEnvioBanco();
    	$ins->setFolio(1);
    	$ins->setFechaPago();
    	$ins->setTipoPago();
    	$ins->setStatus();
    	$ins->setHash( base64_encode($ins->GenerarCadena()));
    
    	$this->lib->updateData($ins);
    }
    
    public function VerFormulario(){
        pre($_REQUEST);
    }
    
    public function Consolidar(){
        if ($_REQUEST) {
            $envio = new BBEnvioBanco();
            $envio->setFolio($_REQUEST['cl_folio']);
            //$envio->setReferencia($_REQUEST['cl_referencia']);
            //$envio->setMonto($_REQUEST['dl_monto']);
            $envio->setFechaPago($_REQUEST['dt_fechaPago']);
            $envio->setTipoPago($_REQUEST['nl_tipoPago']);
            $envio->setStatus($_REQUEST['nl_status']);
            $envio->setHash($_REQUEST['hash']);
            
            $this->lib->ActualizarEnvioBanco($envio);    
        }
    }
    
    /*public function verFormularioBanco(){
    	$this->load->view("datosBanco");
    }*/
}

function pre($var){
    echo "<pre>".print_r($var,true)."</pre>";
}
