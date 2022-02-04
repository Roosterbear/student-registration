<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	global $DBSito;
	require_once __DIR__ . "/../libraries/BD.php";
class AlumnoDatosEscolares
{
	private $_cve_grupo;
	private $_cve_carrera;
	private $_grupo;
	private $_carrera;
	private $_tsu;
	private $_termino_carrera;
	private $_termino_estadia;
		
	public function setAlumnoDatosEscolares($matricula) {
		global $DBSito;
	
		$sql = "select 	g.cve_grupo
				,g.cve_periodo
				,g.cve_especialidad
				,g.nombre as grupo
				,g.numero_cuatrimestre
				,c.cve_carrera
				,c.nombre as carrera
				,pe.no_cuatrimestres as total_cuatrimestres
				,(select cve_proceso = (case 
					when c.nombre like 'TSU%' then 1					
					else 0 end)) as tsu
				from grupo g
				inner join carrera c on c.cve_carrera = g.cve_carrera
				inner join plan_estudio pe on pe.cve_plan_estudio  = g.cve_plan_estudio  
				where g.cve_grupo = (select max(cve_grupo) from alumno_grupo where matricula = {$matricula})					
		";
		$rs = $DBSito->Execute ( $sql );
		$this->_cve_grupo = $rs->fields ['cve_grupo'];
		$this->_cve_carrera = $rs->fields ['cve_carrera'];
		$this->_grupo = $rs->fields ['grupo'];
		$this->_carrera = $rs->fields ['carrera'];
		$this->_tsu = ($rs->fields ['tsu'] == 1)?true:false;
		$this->_termino_estadia = $this->terminoEstadia($matricula);
		$this->_termino_carrera = (($rs->fields ['total_cuatrimestres']) == ($rs->fields ['numero_cuatrimestre']))?(($this->_termino_estadia)?true:false):false; //
		
	}
	
	
	public function terminoEstadia($matricula){
		global $DBSito;
		//$DBSito->debug = true;
		$sql = "select 
		ac.cve_status id
		from materia m 
		inner join clase c on c.cve_materia = m.cve_materia 
		inner join alumno_clase ac on c.cve_clase = ac.cve_clase and ac.cve_docente = c.cve_docente
		inner join materia_plan_estudio mpe on mpe.cve_plan_estudio = c.cve_plan_estudio and mpe.cve_materia = c.cve_materia
		inner join status_calificacion sc on ac.cve_status = sc.cve_status
		where c.cve_materia = 381 
		and ac.calificacion_final > 7
		and c.cve_periodo = (select top 1 cve_periodo from periodo where activo = 1)
		and ac.matricula = {$matricula}
		";
		$rs = $DBSito->Execute ( $sql );
		$id = $rs->fields ['id'];
		return $id == 1?true:false;
	}
	
	public function getCveGrupo(){
		return $this->_cve_grupo;
	}
	
	public function getCveCarrera(){
		return $this->_cve_carrera;
	}
	
	public function getGrupo(){
		return $this->_grupo;		
	}
	
	public function getCarrera(){
		return $this->_carrera;
	}
	
	public function getTSU(){
		return $this->_tsu;
	}
	
	public function terminoCarrera(){
		return $this->_termino_carrera;
	}
	
		
	public function getSelector($matricula, $periodo){
		global $DBSito;
		
		$sql = "select cve_selector from alumno_selector where matricula='{$matricula}' and cve_periodo={$periodo}";
		
		$rs = $DBSito->Execute($sql);
		
		return $rs->fields['cve_selector'];
	}
	
	public function getTituloPlanSelector($selector){
		global $DBSito;
	
		$sql = "select p.nombre as nombre_plan, s.nombre as nombre_selector
		from selector s
		inner join [plan] p on p.cve_plan=s.cve_plan
		where s.cve_selector= {$selector}";
	
		$rs = $DBSito->Execute($sql);
	
		return $rs->fields['nombre_plan'].' - '.$rs->fields['nombre_selector'];
	}
	
	
	public function estaInscrito($matricula, $periodo){
		global $DBSito;
		
		$sql = "select numero_cuatrimestre as nc from grupo g
				inner join alumno_grupo ag on ag.cve_grupo=g.cve_grupo
				inner join plan_estudio pe on pe.cve_plan_estudio=g.cve_plan_Estudio
				inner join inscripcion i on i.cve_periodo=g.cve_periodo and i.matricula=ag.matricula
				where ag.matricula={$matricula} and g.cve_periodo={$periodo}
				";
		$rs = $DBSito->Execute($sql);
		
		return $rs->fields['nc'];
	}
	
	public function tieneBeca($matricula, $periodo){
		global $DBSito;
		
		$sql = "select cve_beca_cat as id from beca_asignada where cve_periodo = {$periodo} and matricula = {$matricula} and [estatus]<>5";
		
		$rs = $DBSito->Execute($sql);
		
		return $rs->fields['id'];
	}
	
	public function getTipoBeca($id){
		global $DBSito;
		
		$sql = "select nombre from beca_cat where cve_beca_cat = {$id}";
		
		$rs = $DBSito->Execute($sql);
		
		return $rs->fields['nombre'];
	}
	
	public function getPorcentajeBeca($id){
		global $DBSito;
		
		$sql = "select porcentaje from beca_cat where cve_beca_cat = {$id}";
		
		$rs = $DBSito->Execute($sql);
		
		return $rs->fields['porcentaje'];
	}
	
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	//                             PERIODO
	// @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	
	public function getIdPeriodoActual(){
		global $DBSito;
	
		$sql = "select top 1 cve_periodo as periodo from periodo where activo = 1";
	
		$rs = $DBSito->Execute($sql);
	
		return $rs->fields['periodo'];
	}
	
	
	public function getYear($id){
		global $DBSito;
	
		$sql = "select convert(char(4),(select top 1 fecha_inicio from periodo where cve_periodo = {$id}),126) as year";
	
		$rs = $DBSito->Execute($sql);
	
		return $rs->fields['year'];
	}
	
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
	
	public function getPeriodoActual(){
		$id = @$this->getIdPeriodoActual();
		$year = @$this->getYear($id);
		$np = @$this->getIdNumeroPeriodo($id);
		$text = @$this->getTextPeriodoNumber($np);
		$periodo = "".$text." ".$year;
	
		return $periodo;
	}
	
	public function getColegiatura($matricula,$cve_concepto) {
		global $DBSito;
		// nombre - cve_concepto - monto
		$sql="select 'Colegiatura '+ISNULL(c.nombre,'')+' - '+ISNULL(s.nombre,'')+' '+ISNULL(p.nombre,'') as nombre
		,cs.cve_concepto as cve_concepto
		,cs.monto as monto
		from [plan] p
		inner join selector s on s.cve_plan=p.cve_plan
		inner join concepto_selector cs on cs.cve_selector=s.cve_selector
		inner join concepto c on c.cve_concepto=cs.cve_concepto
		inner join alumno_selector a on a.cve_selector=cs.cve_selector and a.cve_periodo=cs.cve_periodo
		inner join inscripcion i on i.cve_periodo=a.cve_periodo and i.matricula=a.matricula
		where  a.matricula = {$matricula} and c.cve_concepto = {$cve_concepto}
		";
		
		$rs = $DBSito->Execute($sql);
				
		return $rs->fields;
	
	}
	
}





