<?php
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );
global $DBSito;
require_once __DIR__ . "/../libraries/BD.php";

class AlumnoDocumentos
{
	private $_documento;
	private $_cve_documento;
	private $_entregado;
	private $_numero_de_registros;
		
	public function setAlumnoDocumentos($matricula){
		global $DBSito;
		
		// documento - cve_documento - entregado
		$sql = "select distinct d.nombre as documento
				,al.cve_documento
				,case  when  al.estatus like 'N' then 'No' else 'Si'end as entregado
				from documento_proceso dp
				inner join documento d on d.cve_documento = dp.cve_documento
				inner join ( select cve_proceso = (case	when c.nombre like 'ing%' then 10 when c.nombre like 'lic%' then 10 when c.nombre like 'conta%' then 10	else 1 end)
							from grupo g
							inner join carrera c on c.cve_carrera = g.cve_carrera
							where g.cve_grupo = ( select max (cve_grupo) from alumno_grupo where matricula = {$matricula}) ) 
				dt on dt.cve_proceso = dp.cve_proceso 
				left outer join (select cve_documento ,cve_proceso ,estatus from documento_entregado de inner join alumno a on de.cve_persona=a.cve_alumno and a.matricula = {$matricula}
				and de.cve_proceso = (select cve_proceso = (case	when c.nombre like 'ing%' then 10 when c.nombre like 'lic%' then 10	else 1 end)
							from grupo g
							inner join carrera c on c.cve_carrera = g.cve_carrera
							where g.cve_grupo = ( select max (cve_grupo) from alumno_grupo where matricula = {$matricula})))
				as al on al.cve_documento=d.cve_documento 
				where dp.activo = 1
		";
		
		$rs = $DBSito->Execute ( $sql );
		$registros = 0;
		while(!$rs->EOF){
			$registros = $registros + 1;
			$this->_documento[$registros] = $rs->fields ['documento'];
			$this->_cve_documento[$registros] = $rs->fields ['cve_documento'];
			$this->_entregado[$registros] = $rs->fields ['entregado'];
			$this->_numero_de_registros [$registros] = $registros; 
			$rs->moveNext();
		}
	}
	
	public function printDocumentosToTable($clase = ''){
		$color = '';
		$sign = '';
		$tabla = "<table class=\"{$clase}\">";
		$tabla.= "<tr>";
		$tabla.= "<th>#</th>";
		$tabla.= "<th>Documento</th>";
		$tabla.= "<th>Entregado</th>";
		$tabla.= "</tr>";
		
		foreach ($this->_numero_de_registros as $n){
			$documento = $this->_entregado[$n] == 'Si'?'blackened':'resaltado';
			$sign = $this->_entregado[$n] == 'Si'?'check':'times';
			$estado = $this->_entregado[$n] == 'Si'?'verde':'rojo';
			$tabla.= "<tr>";
			$tabla.= "<td class=\"centrado\">$n</td>";
			$nombre_documento = utf8_encode($this->_documento[$n]);
			$tabla.= "<td class=\"{$documento}\">{$nombre_documento}</td>";
			$tabla.= "<td class=\"{$estado}\"><i class=\"fa fa-{$sign}\"></i></td>";
			$tabla.= "</tr>";
		}
				
		$tabla.= "</table>";		
		return $tabla;
	}	
	
	public function allChecked(){
		if ($this->_numero_de_registros < 1){return false;}
		foreach ($this->_numero_de_registros as $n){
			if (($this->_entregado[$n]) == 'No'){
				return false;
			}		
		}
		return true;
	}
}
