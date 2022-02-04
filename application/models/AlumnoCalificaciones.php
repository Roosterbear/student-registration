<?php
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );
global $DBSito;
require_once __DIR__ . "/../libraries/BD.php";

class AlumnoCalificaciones
{
	private $_matricula;
	private $_materia;
	private $_cve_materia;
	private $_cve_periodo;
	private $_cve_clase;
	private $_cve_docente;
	private $_calificacion_final;
	private $_grado;
	private $_estatus;
	private $_cve_status;
	private $_monto;
	private $_numero_de_registros;
	private $_generado;
	private $_cve_pago_inter;
	
	private $_cve_grupo;
	public $materiasPlanFaltantes;
	
	
	public function setAlumnoCalificaciones($matricula){
		global $DBSito;
		$this->_matricula = $matricula;
		// materia - cve_materia - cve_periodo - cve_clase - cve_docente - cf - grado - estatus - monto
		$sql = "select m.nombre as materia
				,c.cve_materia cve_materia
				,c.cve_periodo
				,c.cve_clase
				,c.cve_docente
				,ac.calificacion_final as cf
				,mpe.no_cuatrimestre as grado
				,sc.descripcion as estatus
				,ac.cve_status cve_status
				,(select monto from concepto where cve_concepto = (select max(cve_concepto) as id from concepto where nombre like 'Ex% Intercuatrimestral' and activo = 1)) as monto
				from materia m 
				inner join clase c on c.cve_materia = m.cve_materia 
				inner join alumno_clase ac on c.cve_clase = ac.cve_clase 
				inner join materia_plan_estudio mpe on mpe.cve_plan_estudio = c.cve_plan_estudio and mpe.cve_materia = c.cve_materia
				inner join status_calificacion sc on ac.cve_status = sc.cve_status
				where ac.cve_status in (3,4,5,6,7,11,12,13,14,16) and c.cve_periodo < (select cve_periodo from periodo where reinscripcion = 1) and ac.matricula = {$matricula}
";	
		$rs = $DBSito->Execute ( $sql );
		$registros = 0;
		while(!$rs->EOF){
			$registros = $registros + 1;
			$this->_materia[$registros] = $rs->fields ['materia'];
			$this->_cve_materia[$registros] = $rs->fields ['cve_materia'];
			$this->_cve_periodo[$registros] = $rs->fields ['cve_periodo'];
			$this->_cve_clase[$registros] = $rs->fields ['cve_clase'];
			$this->_cve_docente[$registros] = $rs->fields ['cve_docente'];
			$this->_calificacion_final[$registros] = $rs->fields ['cf'];
			$this->_grado[$registros] = $rs->fields ['grado'];
			$this->_estatus[$registros] = $rs->fields ['estatus'];
			$this->_cve_status[$registros] = $rs->fields ['cve_status'];
			$this->_monto[$registros] = $rs->fields ['monto'];
			$this->_numero_de_registros [$registros] = $registros; 
			$rs->moveNext();
		}	
		$_SESSION['matricula'] = $this->_matricula;
		$_SESSION['materia'] = $this->_materia;
		$_SESSION['cve_materia'] = $this->_cve_materia;
		$_SESSION['cve_periodo'] = $this->_cve_periodo;
		$_SESSION['cve_clase'] = $this->_cve_clase;
		$_SESSION['cve_docente'] = $this->_cve_docente;
		
		//
		$this->tieneGrupoAsignado($matricula);
		$this->getMateriasFaltantes($matricula, $this->_cve_grupo);
		
	}
	
	public function esAlumnoRegular($matricula){
		global $DBSito;
		$this->_matricula = $matricula;
		
		$sql = "select ac.calificacion_final as cf
		from materia m
		inner join clase c on c.cve_materia = m.cve_materia
		inner join alumno_clase ac on c.cve_clase = ac.cve_clase
		inner join materia_plan_estudio mpe on mpe.cve_plan_estudio = c.cve_plan_estudio and mpe.cve_materia = c.cve_materia
		inner join status_calificacion sc on ac.cve_status = sc.cve_status
		where ac.cve_status in (10) and c.cve_periodo = (select cve_periodo from periodo where reinscripcion = 1) and ac.matricula = {$matricula}
		";
		$rs = $DBSito->Execute ( $sql );
		$regular = '';
					
			$regular = $rs->fields ['cf'] == ''?'1':'0';			
			$rs->moveNext();
		
		 return $regular;
	}
	
	
	public function tieneGrupoAsignado($matricula){
	    global $DBSito;
	    $sql="select top 1 ag.cve_grupo from alumno_grupo ag 
        inner join grupo g on g.cve_grupo=ag.cve_grupo
        inner join periodo p on p.cve_periodo=g.cve_periodo
        WHERE ag.matricula={$matricula} and p.reinscripcion=1 order by 1";
	    $rs = $DBSito->GetOne( $sql );
	    if($rs!==false){
	        $this->_cve_grupo=$rs;
	        return $this->_cve_grupo;
	    }
	    return false;
	}
	
	
	
	public function getMateriasFaltantes($matricula,$cve_grupo){
	    global $DBSito;
	    
	    $sql="EXEC [dbo].[ReingresoValidaMateriasFaltantes] {$matricula}, $cve_grupo";
	    $rs = $DBSito->Execute($sql);
	    if($rs!==false && $rs->RecordCount()> 0 ){
	        $this->materiasPlanFaltantes=$rs->GetArray();
	        return $this->materiasPlanFaltantes;
	    }
	    return false;
	}
	
	
	
	public function getCvePagoInter(){
		global $DBSito;
		
		foreach ($this->_numero_de_registros as $n){
			// 
			$sql = "select top 1 cve_pago_inter
					from pago_intercuatrimestral
					where
					matricula = {$this->_matricula}
					and cve_materia = {$this->_cve_materia[$n]}
					and cve_periodo = {$this->_cve_periodo[$n]}
					and cve_clase = {$this->_cve_clase[$n]}
					and cve_docente = '{$this->_cve_docente[$n]}'
			";
			//
			$rs = $DBSito->Execute ( $sql );
			$this->_cve_pago_inter[$n] = $rs->fields['cve_pago_inter'];
		}			
	}
	
	public function printCalificacionesToTable($clase = '',$matricula=''){
		$this->getCvePagoInter();
		$this->tieneGrupoAsignado($matricula);
		
		$tabla = "<table class=\"{$clase}\">";
		$tabla = "<table class=\"{$clase}\">";
		$tabla.= "<tr>";
		$tabla.= "<th>#</th>";
		$tabla.= "<th>Materia</th>";
		$tabla.= "<th>CF</strong></th>";
		$tabla.= "<th>Estatus</th>";
		$tabla.= "<th>Monto</th>";
		$tabla.= "<th>Generar</th>";
		$tabla.= "</tr>";
	
		$todos = '';
		$total = 0;
		$debe = false;
		foreach ($this->_numero_de_registros as $n){			
			$reprobatorios = array(3,4,11,12,13,14,15,16); 
			$extraordinario = (in_array($this->_cve_status[$n], $reprobatorios))?true:false;
			$calificacion = $extraordinario?$this->_calificacion_final[$n]:'N/R';			
			$color = ($extraordinario)?'resaltado':'blackened'; // Color claro para materias cursando
			$this->_generado[$n] = ($this->_cve_pago_inter[$n]>0)?true:false; // Checa si ya se genero el adeudo
			$tabla.= "<tr>";
			$tabla.= "<td  class=\"centrado\">{$n}</td>";
			$nombre_materia = $this->_materia[$n];
			$tabla.= "<td class=\"{$color}\">{$nombre_materia}</td>";
			$tabla.= "<td class=\"centrado {$color}\">{$calificacion}</td>";
			$nombre_estatus = $this->_estatus[$n];
			$tabla.= "<td class=\"{$color}\">{$nombre_estatus}</td>";
			
			$monto = false;			
			$ficha = false;
			$debe_extraordinario_y_dinero = false;
			
			// Si por lo menos tiene una reprobada, NO PERMITIR la reinscripcion
			if ($extraordinario){
				$debe = true;
			}
										
			// Si el estatus es reprobatorio y no se ha generado, sumar el monto total
			if (($extraordinario) && !($this->_generado[$n])) {
				$total += $this->_monto[$n]; // Agregar valor de un extraordinario al total 
				$monto = true; // Mostrar monto
				$ficha = true; // Mostrar impresion ficha				
			}

			$tabla.= ($monto)?"<td class=\"centrado\">{$this->_monto[$n]}</td>":"<td></td>";	
			
			//
			// DESHABILITAR BOTON DE GENERAR SI DEBE DINERO
			//			
			$debe_dinero = $this->debeDinero($matricula);

			if ($ficha){
				if (!($debe_dinero)){
					$tabla.= "<td class=\"centrado\"> <a href=\"".base_url()."index.php/Reinscripcion/miModal/{$n}\" data-remote=\"false\" data-toggle=\"modal\" data-target=\"#modal_alumnos\"> <i class=\"fa fa-download\" aria-hidden=\"true\"></i></a></td>";
				}else{										
					$tabla.= "<td class=\"centrado text-danger\"> <i class=\"fa fa-ban\"></i> Tiene adeudos</td>";
				}
			}else{
				$tabla.="<td></td>";
			}
			
			$tabla.= "</tr>";
		}
	
		$textoTotal.= "<tr><td class=\"verdecito\" colspan=\"4\"><strong class=\"espaciado bigger-font\">Todas las materias</strong></td>";
		$textoTotal.= "<td class=\"centrado\"><strong>$ {$total}</strong></td>";
		$textoTotal.= "<td class=\"centrado\"><a href=\"#\">  </a></td></tr>";
		$tabla.= $debe ? $textoTotal:'';
		$tabla.= "</table>";	
		$tabla.= "<br>".alerta_r("Si necesitas reimprimir tu ficha, debes ir al apartado de ADEUDOS $", "danger");
		
		if($this->_cve_grupo > 0){
			$texto_grupo = '';
		}else{
			$texto_grupo = '<h1>Este alumno no tiene grupo</h1>';
		}
		$tabla.= $texto_grupo;		
		
		return $tabla;
	}
		
	public function getCliente($matricula){
		global $DBSito;
		
		$sql = "select cve_persona 
				from persona
				where 
				cve_persona = (select top 1 cve_alumno from alumno where matricula = ".$matricula.") 
				";
		$rs = $DBSito->Execute ($sql);
		return $rs->fields['cve_persona'];
		
	}
	
	//
	// FUNCION QUE NOS DICE SI NO DEBE DINERO PARA PODER CREAR ADEUDO DE INTERCUATRIMESTRAL
	// (No tome en cuenta la clave 205 de pago intercuatrimestral porque se genera de uno x uno)
	public function debeDinero($matricula){
		global $DBSito;
		
		$sql = "select count(cxc.cve_cxc) as debe		
		from cxc
		inner join concepto c on c.cve_concepto = cxc.cve_concepto
        left outer join beca_asignada ba on ba.cve_beca_cat = 10 and matricula={$matricula} and cve_periodo= (select top 1 cve_periodo from periodo where activo=1) 
		where cve_cliente = (select distinct cve_alumno from alumno where matricula = {$matricula})
		and cxc.[status] = 1 and cxc.cve_concepto != (select max(cve_concepto) as id from concepto where nombre like 'Ex% Intercuatrimestral' and activo = 1)
        and cxc.cve_concepto not in (157,390) 
        and ba.cve_beca_asignada is null -- beca IDSEA";
		
		$sql2 = "select 1 as convenio
				from alumno_convenio
				where matricula = '{$matricula}'
				and cve_periodo = (select top 1 cve_periodo from periodo where activo=1) ";
		
		$rs = $DBSito->Execute($sql);
		$debe = $rs->fields['debe'];
		
		// ---------------------------------------------------------------------------------
		// AQUI PONEMOS LA EXCEPCION DE ALUMNOS VULNERABLES
		// ---------------------------------------------------------------------------------
		//$rs = $DBSito->Execute($sql2);
		//$tiene_convenio = $rs->fields['convenio'];
		//if ($tiene_convenio) return 0;
		
		return $debe;
		//return 0;//modificacion solicitada por msoloan 02/04/2020
	}
	
	public function generarPagoIntercuatrimestral($datos){
		global $DBSito;		
		
		// extraer datos
		$id = $datos[0];
		$matricula = $datos[1];
		$materia = $datos[2];
		$cve_materia = $datos[3];
		$cve_periodo = $datos[4];
		$cve_clase = $datos[5];
		$cve_docente = $datos[6];
		
		
		$sql = "insert into pago_intercuatrimestral
				(cve_pago_inter,matricula,cve_materia,fecha,observaciones,cve_periodo,cve_clase,cve_docente)
				values
				($id,$matricula,$cve_materia,getdate(),'$materia',$cve_periodo,$cve_clase,'$cve_docente')
		";
		$rs = $DBSito->Execute ( $sql );
		return $cve_materia;
	}
	
	public function allChecked(){
		$all_checked = $this->_numero_de_registros>0?false:true;
		
		//--------------------------------------------------------------------------------------
		//--------------------------------------------------------------------------------------
		// VERIFICACION DE GRUPOS !!!
		//--------------------------------------------------------------------------------------
		//--------------------------------------------------------------------------------------
		
		
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		// 
		// ---																				 ---
		//      Verificacion de grupos, si no tiene grupo, comunicarse a CONTROL ESCOLAR
		// ---																				 ---
		//
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
		if($this->_cve_grupo==null){$all_checked=false; }
		
		if($this->materiasPlanFaltantes!=null){$all_checked=false; }
		
		return $all_checked;		
	}
}
