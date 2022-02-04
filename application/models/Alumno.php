<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
global $DBSito;
require_once __DIR__ . "/../libraries/BD.php";
class Alumno 
{
	private $_usuario;
	private $_matricula;
	private $_nombre;
	private $_cve_status;
	private $_estatus;
	private $_esAlumno;
	private $_all_checked;	
	
	public function setUsuario($usuario) {
		$this->_usuario = $usuario;
	}
	
	public function getUsuario() {
		return $this->_usuario;
	}
	
	public function setAlumno($matricula) {
		global $DBSito;
		//$DBSito->debug = true;
		// nombre - matricula - estatus - cve_status
		$sql = "select (p.nombre + ' ' + p.apellido_paterno + ' ' + p.apellido_materno) as nombre
				,a.matricula				
				,sa.descripcion as estatus
				,sa.cve_status as cve_status
				from persona p
				inner join alumno a on p.cve_persona = a.cve_alumno
				inner join status_alumno sa on sa.nombre = a.[status] 
				where a.matricula = {$matricula}
		";
		$rs = $DBSito->Execute ( $sql );
		$this->_esAlumno = ($rs && $rs->RecordCount () > 0) ? true : false;
		if ($this->_esAlumno){
			$this->_matricula = $rs->fields ['matricula'];
			$this->_nombre = $rs->fields ['nombre'];
			$this->_estatus = $rs->fields ['estatus'];
			$this->_cve_status = $rs->fields ['cve_status'];
		}
	}
		
	public function esAlumno(){
		return $this->_esAlumno;	
	}
	
	public function getNombre() {
		return $this->_nombre;
	}
	
	public function getMatricula() {
		return $this->_matricula;
	}
	
	public function baja(){
		return (($this->_cve_status == 7)||($this->_cve_status == 8))?true:false;
	}
	
	public function estatusAlumno(){
		return $this->_estatus;
	}
}
