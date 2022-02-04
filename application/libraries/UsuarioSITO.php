<?php
global $DBSito;
require_once __DIR__."/BD.php";
/** 
 * @author jguerrero
 * 
 */
class UsuarioSITO {
	
	private $GPRUPO_SEGURIDAD=array(170=>"Administradores",222=>"Pruebas");
	
	private $username="";
	private $nombre="";
	private $cve_persona=null;
	private $sitoSession="";
	private $grupos=array();
	
	
	/**
	 */
	function __construct() {
		
		@session_start();		
	}
	
	public function login(){
		global $DBSito;
		

		//validamos si ya existe una session vigente
		if($this->getSession()){return true;}
		
		if(!$this->findUsuario()){
			return false;
		}
		
		
		$sql="select cve_persona from sito_utags.dbo.registro_sesion 
		where cve_persona={$this->cve_persona} and sesion='{$this->sitoSession}' and activo=1";
		$rs=$DBSito->Execute($sql);
		if($rs!==false && $rs->RecordCount()>0){
			$this->setSession();	
			return true;
		}
		
		return false;

		
	}
	
	protected function findUsuario(){
		global $DBSito;
		$sql="select p.cve_persona, nombre=p.nombre+' '+p.apellido_paterno+' '+p.apellido_materno
			,g.cve_grupo_seguridad
			from 
			sito_utags.dbo.persona p 
			inner join sito_utags.dbo.usuario u on u.cve_persona=p.cve_persona
			inner join sito_utags.dbo.usuario_grupo_seguridad g on u.cve_persona=g.cve_persona where u.[login]='{$this->username}' ";
		$rs=$DBSito->Execute($sql);
		
		if($rs!==false && $rs->RecordCount()>0){
			while(!$rs->EOF){
				$this->nombre=$rs->fields['nombre'];
				$this->cve_persona=$rs->fields['cve_persona'];
				$this->grupos[]=$rs->fields['cve_grupo_seguridad'];
				$rs->moveNext();
			}
			return true;
		}
		return false;
		
		
	}
	
	public function getSession(){
		if(isset($_SESSION["cve_persona"])){
			$this->cve_persona=$_SESSION['cve_persona'];
			$this->grupos=$_SESSION['cve_grupo_seguridad'];
			$this->nombre=$_SESSION['nombre'];
			$this->username=$_SESSION['username'];

			return true;
		}
		return false;
	}
	
	public function setSession(){
		if($this->cve_persona!==null){
			$_SESSION['nombre']=$this->nombre;
			$_SESSION['cve_persona']=$this->cve_persona;
			$_SESSION['username']=$this->username;
			$_SESSION["cve_grupo_seguridad"]=$this->grupos;
			
			return true;
		}
		
		return false;
	}
	
	public function destruirSesion(){
	    session_destroy();
	}
	/**
	 * @return the $username
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * @return the $nombre
	 */
	public function getNombre() {
		return $this->nombre;
	}

	/**
	 * @return the $cve_persona
	 */
	public function getCve_persona() {
		return $this->cve_persona;
	}

	/**
	 * @return the $sitoSession
	 */
	public function getSitoSession() {
		return $this->sitoSession;
	}

	/**
	 * @return the $grupos
	 */
	public function getGrupos() {
		return $this->grupos;
	}

	/**
	 * @param string $username
	 */
	public function setUsername($username) {
		$this->username = $username;
	}

	/**
	 * @param string $nombre
	 */
	public function setNombre($nombre) {
		$this->nombre = $nombre;
	}

	/**
	 * @param number $cve_persona
	 */
	public function setCve_persona($cve_persona) {
		if(is_numeric($cve_persona)){
			$this->cve_persona = $cve_persona;
		}
		
	}

	/**
	 * @param string $sitoSession
	 */
	public function setSitoSession($sitoSession) {
		$this->sitoSession = $sitoSession;
	}

	/**
	 * @param multitype: $grupos
	 */
	public function setGrupos($grupos) {
		$this->grupos = $grupos;
	}

}

?>