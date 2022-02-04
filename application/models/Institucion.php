<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
global $DBSito;
require_once __DIR__ . "/../libraries/BD.php";
class Institucion 
{
	private $_nombre;
	
	public function insertarInstitucion($nombre){
		global $DBSito;
		
		$this->_nombre = $nombre;
	
		$sql = "insert into institucion_financiera (nombre) values ('{$nombre}') 
		";
		$rs = $DBSito->Execute ( $sql );
		
		return true;
	}		
	
	public function getInstituciones(){
		global $DBSito;
		
		$sql = "select * from institucion_financiera order by 1
		";
		
		$rs = $DBSito->Execute($sql);
		
		return $rs->getArray();
	}
	
	/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
	/* @@@@@@@@ C O N F I G U R A C I O N @@@@@@@@@ */
	/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
	public function checarSiPeriodoEstaActivo($id){
		global $DBSito;
				
		$sql = "select periodo from configreinscripcion where periodo = $id";
		
		$rs = $DBSito->Execute($sql);
		
		return $rs->fields['periodo'];
	}
	
	public function getUltimoPeriodo(){
		global $DBSito;
		
		$sql = "select top(1) periodo from configreinscripcion order by 1 desc";
		
		$rs = $DBSito->Execute($sql);
		
		$id = $rs->fields['periodo'];
		
		$year = @$this->getYear($id);
		$np = @$this->getIdNumeroPeriodo($id);
		$text = @$this->getTextPeriodoNumber($np);
		$periodo = "".$text." ".$year;				
		
		return $periodo;
	}
	
	public function getFechaHoy(){
		global $DBSito;
	
		$sql = "select convert(char(10),getdate(),126) as fecha";
	
		$rs = $DBSito->Execute($sql);
	
		return $rs->fields['fecha'];
	}
	
	/*
	 * 
	 * 
	 * Esto va a cambiar porque ya solo se toma en cuenta el periodo mas reciente creado
	 * 
	 * 
	 */
	
	
	public function crearPeriodoReinscripcion($id){
		global $DBSito;
		
		$inicio = $this->getFechaHoy().' 00:00:00.000';
		$fin = $this->getFechaHoy().' 23:59:59.000';
		$sql = "insert into configreinscripcion (periodo,activarReinscripcion, activarMulta, multa, fechaInicio, fechaFin) values ($id,0,0,75,'$inicio','$fin')
		";
		
		$this->desactivarTodosLosPeriodosReinscripcion();
		$this->activarPeriodoReinscripcion($id);
		
		
		$rs = $DBSito->Execute($sql);		
		
		return true;
	}
	
	
	/* ---------------------------------------------------------------- */
	/* -- Esta sirve para poner activo el periodo y la reinscripcion -- */
	/* ---------------------------------------------------------------- */
	public function actualizarReinscripcion(){
		global $DBSito;
		
		
	}
	
	public function cualEsElPeriodoActual(){
		global $DBSito;
		
		$sql = "select top 1 cve_periodo from periodo where activo = 1 order by 1 desc";
		
		$rs =  $DBSito->Execute($sql);
		
		return $rs;
	}
	
	/* ----------------------------------------------------------------- */
	
	public function desactivarTodosLosPeriodosReinscripcion(){
		global $DBSito;
		
		$sql = "update periodo set reinscripcion = 0";
		
		$rs = $DBSito->Execute($sql);
		
		return true;
	}
	
	public function activarPeriodoReinscripcion($id){
		global $DBSito;
		
		$sql = "update periodo set reinscripcion = 1 where cve_periodo = {$id}";
		
		$rs = $DBSito->Execute($sql);
		
		return true;
	}
	
	public function eliminarPeriodo(){
		global $DBSito;
				
		
		$sql = "delete from configreinscripcion where periodo = (select top(1) periodo from configreinscripcion order by 1 desc)";
		
		$rs = $DBSito->Execute($sql);
		
		return true;
	}
	
	/*
	 * 
	 * 
	 * 
	 */
	
	
	
	/* --------------------------------------- */
	/* ---------CIERRE DE REINSCRIPCION------- */
	/* --------------------------------------- */
	public function checarEstadoReinscripcion(){
		global $DBSito;
		
		$sql = "select activarReinscripcion from configReinscripcion where periodo = (select max(periodo) from configReinscripcion)";
		
		$rs = $DBSito->Execute($sql);
				
		return $rs->fields['activarReinscripcion'];
	}
	
	public function activarReinscripcion(){
		global $DBSito;
	
		$sql = "update configReinscripcion set activarReinscripcion = 1 where periodo = (select max(periodo) from configReinscripcion)
		";
		$rs = $DBSito->Execute ( $sql );
	
		return true;
	}
	
	public function desactivarReinscripcion(){
		global $DBSito;
	
		$sql = "update configReinscripcion set activarReinscripcion = 0 where periodo = (select max(periodo) from configReinscripcion)
		";
		$rs = $DBSito->Execute ( $sql );
	
		return false;
	}
	
	public function enFechaDeReinscripcion(){
		global $DBSito;
		
		$sql = "select periodo from configReinscripcion where periodo = (select max(periodo) from configReinscripcion)
				and (cast(getdate() as datetime) between  cast(fechaInicio as datetime) and cast(fechaFin as datetime)) 
		";
		
		$rs = $DBSito->Execute ( $sql );
		return $rs->fields['periodo'] > 0?1:0;
		
	}
	
	/* --------------------------------------- */
	/* --------------MULTAS------------------- */
	/* --------------------------------------- */
	public function checarEstadoMulta(){
		global $DBSito;
	
		$sql = "select activarMulta from configReinscripcion where periodo = (select max(periodo) from configReinscripcion)";
	
		$rs = $DBSito->Execute($sql);
	
		return $rs->fields['activarMulta'];
	}
	
	public function activarMulta(){
		global $DBSito;
	
		$sql = "update configReinscripcion set activarMulta = 1 where periodo = (select max(periodo) from configReinscripcion)
		";
		$rs = $DBSito->Execute ( $sql );
	
		return true;
	}
	
	public function desactivarMulta(){
		global $DBSito;
	
		$sql = "update configReinscripcion set activarMulta = 0 where periodo = (select max(periodo) from configReinscripcion)
		";
		$rs = $DBSito->Execute ( $sql );
	
		return false;
	}
	
	public function listadoMultasReinscripcion(){
		global $DBSito;
		
		$sql = "select cve_concepto as id, nombre, monto from concepto where cve_grupo_concepto = 12 and nombre like '%multa%insc%'
		";
		$rs = $DBSito->Execute($sql);
		
		return $rs->getArray();
	}
	
	public function cambiarMulta($multa){
		global $DBSito;
		
		if ($multa != 0){
			$sql = "update configReinscripcion set multa = $multa where periodo= (select max(periodo) from configReinscripcion)
			";
			$rs = $DBSito->Execute($sql);
		}else{
			return false;
		}
		
		return true;
		
	}
	
	
	/* --------------------------------------- */
	/* --------------PERIODOS----------------- */
	/* --------------------------------------- */	
	
	public function getPeriodoAdmisionActual(){
		global $DBSito;
		
		$sql = "select max (id) as periodo from admision.dbo.admisionPeriodo where idnivel = 2
		";
		$rs = $DBSito->Execute ( $sql );
		
		return $rs->fields['periodo'];
	}
	
	public function getPeriodoReinscripcionING(){
		global $DBSito;
		
		$sql = "select max (cve_concepto) cve_ing from concepto where nombre like 'Reinscripción ING%' and activo = 1 --and cve_grupo_concepto = 11		";
		$rs = $DBSito->Execute ( $sql );
		
		return $rs->fields['cve_ing'];
	}
	
	public function getPeriodoReinscripcionTSU(){
		global $DBSito;
		
		$sql = "select max (cve_concepto) cve_tsu from concepto where nombre like 'Reinscripción TSU%' and activo = 1 --and cve_grupo_concepto = 11		";
		$rs = $DBSito->Execute ( $sql );
		
		return $rs->fields['cve_tsu'];
	}
	
	public function checarSesion($id,$sid){
		global $DBSito;
	
		$sql = "select sesion from registro_sesion
		where cve_persona = (select cve_alumno from alumno where matricula = {$id})
		and activo = 1";
		$rs = $DBSito->Execute($sql);
		return $rs->fields['sesion'] === $sid?0:1;
	
	}
	
	// ID PERIODO ACTUAL
	public function getIdPeriodoActual(){
		global $DBSito;
	
		$sql = "select top 1 periodo from configreinscripcion order by 1 desc";
	
		$rs = $DBSito->Execute($sql);
	
		return $rs->fields['periodo'];
	}
	
	/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
	/* @@@@@@ OBTENER LOS PERIODOS @@@@@@ */
	/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */	
	public function getAllPeriodosText(){
		global $DBSito;
		
		// Solo se muestran los ultimos periodos mas nuevos [mas que suficientes]
		$sql = "select top(5) cve_periodo as id from periodo order by 1 desc";
		
		$rs = $DBSito->Execute($sql);								
		
		$los_periodos = $rs->getArray();;
		
		
		
		foreach ($los_periodos as $p){						
			$year = @$this->getYear($p['id']);
			$np = @$this->getIdNumeroPeriodo($p['id']);
			$text = @$this->getTextPeriodoNumber($np);
			$periodo = "".$text." ".$year;
						
			$textos[$p['id']] = $periodo;
		}
		return $textos;
		
	}
	
	/*
	 * DE ACUERDO AL PERIODO DADO, SACAMOS EL AÑO
	 */
	
	public function getYear($id){
		global $DBSito;
	
		$sql = "select convert(char(4),(select top 1 fecha_inicio from periodo where cve_periodo = {$id}),126) as year";
	
		$rs = $DBSito->Execute($sql);
	
		return $rs->fields['year'];
	}
	
	/*
	 * DE ACUERDO AL PERIODO DADO, SACAMOS EL NUMERO DE PERIODO
	 */
	public function getIdNumeroPeriodo($id){
		global $DBSito;
	
		$sql = "select top 1 numero_periodo as np from periodo where cve_periodo = {$id}";
	
		$rs = $DBSito->Execute($sql);
	
		return $rs->fields['np'];
	}
	
	
	public function getTextPeriodoNumber($np){
		if ($np == 1){
			return 'ENE - ABR';
		}
	
		if ($np == 2){
			return 'MAY - AGO';
		}
	
		if ($np == 3){
			return 'SEP - DIC';
		}
	}
	
	
	/*
	 * ----------------------------------------------
	 * FUNCION PARA SACAR EL TEXTO DE UN PERIODO DADO
	 * ----------------------------------------------
	 */
	public function getPeriodoTexto($id){
		$id = $this->id;
		$year = @$this->getYear($id);
		$np = @$this->getIdNumeroPeriodo($id);
		$text = @$this->getTextPeriodoNumber($np);
		$periodo = "".$text." ".$year;
			
		return $id;
	}
	
	/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
	/* @@@@@@@@@ OBTENER FECHAS @@@@@@@@@ */
	/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */	

	public function getFechaInicioReinscripcion(){
		global $DBSito;
		
		$sql = "select fechaInicio from configReinscripcion where periodo = (select max(periodo) from configReinscripcion) ";
		
		$rs = $DBSito->Execute($sql);
		
		return $rs->fields['fechaInicio'];
		
	}
	
	public function getFechaFinReinscripcion(){
		global $DBSito;
	
		$sql = "select fechaFin from configReinscripcion where periodo = (select max(periodo) from configReinscripcion) ";
	
		$rs = $DBSito->Execute($sql);
	
		return $rs->fields['fechaFin'];
	
	}
	
	public function updateFechaInicio($fecha){
		global $DBSito;
		
		$fecha = $fecha.' 00:00:00.000';
		$sql = "update configReinscripcion set fechaInicio = '$fecha'  where periodo = (select max(periodo) from configReinscripcion)";
		
		$rs = $DBSito->Execute($sql);
		return true;
	}
	
	public function updateFechaFin($fecha){
		global $DBSito;
		
		$fecha = $fecha.' 23:59:59.000';
		$sql = "update configReinscripcion set fechaFin = '$fecha'  where periodo = (select max(periodo) from configReinscripcion)";
		
		$rs = $DBSito->Execute($sql);
		return true;
	}
	
	
	/*
	 * ---------------------------
	 * PERMISOS PARA CONFIGURACION
	 * ---------------------------
	 */	
	public function getAcceso(){
		/*
		 * 
		 */
		//TODO Encriptar esto
		/*
		 * 
		 */
		return 'ctrl+u';
	}
	
	public function getPermitidos(){
		
		/*
		 * 
		 */
		//TODO crear una tabla de accesos
		/*
		 * 
		 */
		$permitidos = ['externo','A00453','B00621','B00421'];
		return $permitidos;
	}
}
