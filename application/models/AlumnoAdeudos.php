<?php
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );
global $DBSito;
require_once __DIR__ . "/../libraries/BD.php";
require_once __DIR__ . "/../libraries/AlumnoDatosEscolares.php";


class AlumnoAdeudos
{	
	private $_id;
	private $_concepto;
	private $_adeudo;
	private $_cargo;
	private $_total;
	private $_alta;
	private $_fecha;
	private $_cve_concepto;
	private $_numero_de_registros;
	private $_tieneBeca;	
	private $_tieneConvenio;
	
	public $alumno_datos_escolares;
	public $system_controller;
	
	public function __construct(){	
		$this->alumno_datos_escolares = new AlumnoDatosEscolares();

	}

	public function setAlumnoAdeudos($matricula){
		global $DBSito;
		$_SESSION['matricula'] = $matricula;	
		/*		  
		 * @@@@@@@@@@ ADEUDOS DEL ALUMNO @@@@@@@@@@  
		 */
		
		// id - concepto - adeudo - cargo - total - alta - fecha - cve_concepto
		$sql = "select cxc.cve_cxc as id
			  ,c.nombre as concepto
			  ,cxc.monto_adeudo as adeudo
			  ,isnull(cxc.monto_cargo,0) as cargo
			  ,isnull(cxc.monto_adeudo,0) + isnull(cxc.monto_cargo,0) as total      
			  ,convert(char(10),cxc.fecha_alta,126) as alta
			  ,convert(char(10),cxc.fecha_act,126) as fecha
			  ,cxc.cve_concepto		
			  ,pi.observaciones
				  from cxc 
				  inner join alumno a on a.cve_alumno = cxc.cve_cliente
				  inner join concepto c on cxc.cve_concepto = c.cve_concepto
				  left outer join concepto_selector cs on cs.cve_concepto_selector = cxc.cve_concepto_selector
				  left outer join selector s on s.cve_selector = cs.cve_selector 
				  left outer join [plan] p on p.cve_plan=s.cve_plan
				  left outer join pago_intercuatrimestral pi on pi.cve_pago_inter = cxc.cve_cxc
			  where	a.matricula = {$matricula} and cxc.status != 0 and cxc.cve_concepto != 164 order by 5 desc
		";
	
		// OJO, se modifico la anterior consulta para que permita REINSCRIPCION aunque deba la MULTA
		// Esta validado para que no se genere doble multa al imprimir la ficha
		
		$rs = $DBSito->Execute ( $sql );
		$registros = 0;
		while(!$rs->EOF){
			$registros = $registros + 1;
			$this->_id[$registros] = $rs->fields['id'];
			$this->_concepto[$registros] = $rs->fields['concepto'].' '.$rs->fields['observaciones'];
			$this->_adeudo[$registros] = $rs->fields['adeudo'];
			$this->_cargo[$registros] = $rs->fields['cargo'];
			$this->_total[$registros] = $rs->fields['total'];
			$this->_alta[$registros] = $rs->fields['alta'];
			$this->_fecha[$registros] = $rs->fields['fecha'];
			$this->_cve_concepto[$registros] = $rs->fields['cve_concepto'];
			$this->_numero_de_registros [$registros] = $registros; 
			$rs->moveNext();
		}	
	
	}
	
	public function printAdeudosToTable($clase = ''){
		$tabla = "<table class=\"{$clase}\">";
		$tabla.= "<tr>";
		$tabla.= "<th>#</th>";
		$tabla.= "<th>Concepto</th>";
		$tabla.= "<th>Adeudo</th>";
		$tabla.= "<th>Cargo</th>";
		$tabla.= "<th>Total</th>";
		$tabla.= "<th>Alta</th>";
		$tabla.= "<th>Fecha</th>";
		$tabla.= "<th>Ficha</th>";
		$tabla.= "</tr>";
	
		foreach ($this->_numero_de_registros as $n){
			$tabla.= "<tr>";
			$tabla.= "<td  class=\"centrado \">{$n}</td>";
			$tabla.= "<td>{$this->_concepto[$n]}</td>";
			$tabla.= "<td class=\"centrado\">$ {$this->_adeudo[$n]}</td>";
			$tabla.= "<td class=\"centrado\">$ {$this->_cargo[$n]}</td>";
			$tabla.= "<td class=\"centrado resaltado\">$ {$this->_total[$n]}</td>";
			$tabla.= "<td class=\"centrado\">{$this->_alta[$n]}</td>";
			$tabla.= "<td class=\"centrado\">{$this->_fecha[$n]}</td>";
			$tabla.= "<td class=\"centrado\"><a href=\"".base_url()."index.php/Reinscripcion/imprimirFicha/{$this->_id[$n]}\"> <i class=\"fa fa-print\" aria-hidden=\"true\"></i></a></td>";
			$tabla.= "</tr>";
		}
	
		$tabla.= "</table>";
		return $tabla;
	}
	
	public function tieneBeca(){
		global $DBSito;
		
		$periodo = $this->alumno_datos_escolares->getIdPeriodoActual();
		// matricula
		$sql = "select distinct matricula
		from beca_asignada
		where (cve_beca_cat > 7)
		and matricula = {$_SESSION['matricula']}
		and cve_periodo = {$periodo}
		";			
		
		$rs = $DBSito->Execute ( $sql );
		$this->_tieneBeca = ($rs && $rs->RecordCount () > 0) ? true : false;
	
		return $this->_tieneBeca;
	}
	
	
	/* ----------------------------------------------------------------- */
	/* ------ CONVENIO POR CONTINGENCIA COVID-19 ----------------------- */
	/* ----------------------------------------------------------------- */
	public function tieneConvenio(){
		global $DBSito;
		$this->_tieneConvenio = 0;
		$periodo = $this->alumno_datos_escolares->getIdPeriodoActual();
		// matricula
		$sql = "select 1 as convenio
		from alumno_convenio
		where matricula = '{$_SESSION['matricula']}'
		and cve_periodo = {$periodo}
		";
	
		$rs = $DBSito->Execute ( $sql );
		$this->_tieneConvenio = ($rs && $rs->RecordCount () > 0) ? true : false;
		return $this->_tieneConvenio;
		//por indicacion del contador victor 26/08/2020
		//return true;
		
	}
	/* ----------------------------------------------------------------- */
	/* ----------------------------------------------------------------- */
	
	public function allChecked(){
		$periodo = $this->alumno_datos_escolares->getIdPeriodoActual();
		$all_checked = $this->_numero_de_registros>0?false:true;
			
			if ($this->tieneConvenio()){
				return true;
			}			
			
		return $all_checked;
	}
	
	// Para que no se hagan cargos dobles
	public function repetido($cliente,$descripcion){
		global $DBSito;
					
		$sql = "select * from cxc 
		where cve_cliente = {$cliente} 
		and descripcion = '{$descripcion}'
		";
		$rs = $DBSito->Execute ( $sql );
		
		return ($rs && $rs->RecordCount () > 0) ? true : false;
	}
	
	public function existeInter($matricula,$cve_materia,$cve_perido){
		global $DBSito;
			
		$sql = "select * From pago_intercuatrimestral where matricula=$matricula and cve_materia=$cve_materia and cve_periodo=$cve_perido";
		$rs = $DBSito->Execute ( $sql );
	
		if($rs && $rs->RecordCount () < 1){return false;}
		return $rs->FetchObj();
	}
	
	public function generarCxC($cliente,$descripcion){
		global $DBSito;		
		
		/*
		 * OJO
		 * Se modifico la forma de accesar a los extraordinarios de forma que busca el maximo que esta ACTIVO
		 * por lo que nos deben avisar si cambia el concepto para deshabilitar los demas
		 * 
		 * */
		$_SESSION['cliente'] = $cliente;
		
		$intercuatrimestral = $this->getIntercuatrimestral();
				
		$id_intercuatrimestral = $this->getIdIntercuatrimestral();
		$monto_intercuatrimestral = $this->getMonto($id_intercuatrimestral);
		$sql = "insert into cxc
				(cve_concepto,cve_cliente,monto_adeudo,monto_cargo,fecha_alta,fecha_act,[status],cve_cajero,descripcion)
				values
				({$id_intercuatrimestral},{$cliente},{$monto_intercuatrimestral},0,getdate(),getdate(),1,0,'{$descripcion}')
		";
		
		$repetido = $this->repetido($cliente, $descripcion);
		if (!$repetido){				
			$rs = $DBSito->Execute ( $sql );
			//echo $sql;
		}	
		return true;
	}	
	
	public function getIntercuatrimestral(){
		global $DBSito;		
		
		/*
		 * OJO
		 * Los conceptos de INTERCUATRIMESTRALES deben estar en Activo = 0 los que no se utilicen 
		 * Es necesario que nos avisen cuando cambien el concepto para dejar solo UNO activo
		 * 
		 * */
		$sql = "select cve_concepto, monto 
				from concepto 
				where cve_concepto = (select max(cve_concepto) as id from concepto where nombre like 'Ex% Intercuatrimestral' and activo = 1)";
		$rs = $DBSito->Execute($sql);
		return $rs->fields;
	}
	
	public function getIdCxC($cliente){
		global $DBSito;
		
		/*
		 * OJO
		 * Es importante que SIEMPRE nos avisen cuando hagan un cambio en INTERCUATRIMESTRALES
		 * Ya que los que sean antig�os y no se usen, deben estar en ACTIVO = 0
		 * porque se valida este campo para que no haya fallas y se haga autom�tico
		 * 
		 * */
		$sql = "select top 1 cve_cxc 
				from cxc where cve_cliente = ".$cliente." and cve_concepto = (select max(cve_concepto) as id from concepto where nombre like 'Ex% Intercuatrimestral' and activo = 1) order by cve_cxc desc
		";
		$rs = $DBSito->Execute ( $sql );
		$id = $rs->fields['cve_cxc'];
		return $id;
	}
	
	
	/*
	 * Las siguientes 2 funciones Automatizan lo del PAGO INTERCUATRIMESTRAL
	 * Siempre y cuando dejen en activo el concepto que se quiera manejar
	 * */
	/* --------------------------------------------------------------------------------------------------------------------- */
	
	public function getIdIntercuatrimestral(){
		global $DBSito;
	
		$sql = "select max(cve_concepto) as id from concepto where nombre like 'Ex% Intercuatrimestral' and activo = 1
		";
		$rs = $DBSito->Execute ( $sql );
		$id = $rs->fields['id'];
		return $id;
	}
	
	public function getMonto($id){
		global $DBSito;
	
		$sql = "select top 1 monto from concepto where cve_concepto = {$id}
		";
		$rs = $DBSito->Execute ( $sql );
		$monto = $rs->fields['monto'];
		return $monto;
	}
	
	/* --------------------------------------------------------------------------------------------------------------------- */
	
	public function getColegiaturas($periodo, $matricula){
		global $DBSito;
				
		// colegiatura - cve_concepto_selector - cve_concepto - fecha_vencimiento - monto - pagado - cuentaxCobrar - no_debe
		$sql = "select c.nombre as colegiatura 
				,cs.cve_concepto_selector
				,cs.cve_concepto
				,cs.fecha_vencimiento
				,cs.monto
				,pagado=isnull(pagado.cve_transaccion,0)
				,cuentaxCobrar=isnull(cxc.cve_cxc,0)
				,no_debe=isnull(no_debe.cve_cxc,0)
                from alumno a
                inner join alumno_selector als on a.matricula=als.matricula
                inner join  concepto_selector cs on cs.cve_selector = als.cve_selector and cs.cve_periodo = als.cve_periodo
                inner join concepto c on c.cve_concepto = cs.cve_concepto
				left outer join cxc on cxc.cve_cliente=a.cve_alumno and cxc.cve_concepto=cs.cve_concepto and cxc.[status]=1
				left outer join (
						select cve_cxc, cve_cliente, cve_concepto, [status] from cxc
						) no_debe on no_debe.cve_cliente = a.cve_alumno and cs.cve_concepto = no_debe.cve_concepto and no_debe.[status] = 0
				left outer join (
						select t.cve_cliente,m.* from transaccion t
						inner join movimiento m on m.cve_transaccion=t.cve_transaccion
						inner join recibo r on r.cve_transaccion=t.cve_transaccion
						where  tipo_cliente='A' and r.[status]='A' and isnull(m.es_cargo,0) = 0 
						) pagado on pagado.cve_cliente = a.cve_alumno and pagado.cve_concepto = cs.cve_concepto
				where als.cve_periodo={$periodo}  and als.matricula = {$matricula} order by 3
				";
		
		$rs = $DBSito->Execute($sql);
		return $rs->getArray();
	}
	
	public function getAdeudos($matricula){
		global $DBSito;
		
		$sql = "select cxc.cve_cxc as cve_cxc
				,cxc.cve_concepto as cve_concepto
				,c.nombre as concepto
				,cxc.fecha_alta
				,cxc.monto_adeudo as monto
				,cxc.monto_cargo
				,c.cve_grupo_concepto as grupo
				from cxc 
				inner join concepto c on c.cve_concepto = cxc.cve_concepto
				where cve_cliente = (select distinct cve_alumno from alumno where matricula = {$matricula})
				and cxc.[status] = 1				
				";
		
		$rs = $DBSito->Execute($sql);
		return $rs->getArray();
	}
	
	public function tieneAdeudosColegiatura($matricula){
		global $DBSito;
	
		$sql = "select c.cve_grupo_concepto as grupo	
				from cxc
				inner join concepto c on c.cve_concepto = cxc.cve_concepto
				where cve_cliente = (select distinct cve_alumno from alumno where matricula = {$matricula})
				and cxc.[status] = 1		
		";

		
		$rs = $DBSito->Execute($sql);
		return $rs->getArray();
	}
	
	public function tieneConvenioColegiatura($periodo,$matricula){
	    global $DBSito;
	    
	    $sql = "select top 1 ac.matricula  
        from alumno_convenio ac inner join periodo p on p.cve_periodo=ac.cve_periodo
        where ac.matricula='{$matricula}' and p.cve_periodo='$periodo' ";
	    
	    
	    $rs = $DBSito->Execute($sql);
	    return ($rs===false?false: $rs->getRowCount() > 0);
	}
	
	public function adeudoGenerado($id,$cliente){
		global $DBSito;
		
		$sql = "select cve_cxc from cxc where cve_concepto = {$id} and cve_cliente = {$cliente}
				";
		$rs = $DBSito->Execute($sql);
		return ($rs && $rs->RecordCount () > 0) ? true : false;
		
	}
	
	public function yaPagoReinscripcion($matricula,$periodo){
		global $DBSito;
		
		$sql = "select CONVERT(VARCHAR(10), fecha_pago, 103) as inscripcion from inscripcion
		where matricula = {$matricula}
		and cve_periodo = {$periodo}";
		
		$rs = $DBSito->Execute($sql);
		return $rs->fields['inscripcion']==''?0:1;
	}

	public function fechaPagoReinscripcion($matricula,$periodo){
		global $DBSito;
	
		$sql = "select CONVERT(VARCHAR(10), fecha_pago, 103) as inscripcion from inscripcion
		where matricula = {$matricula}
		and cve_periodo = {$periodo}";
	
		$rs = $DBSito->Execute($sql);
		return $rs->fields['inscripcion'];
	}
	
	// Esta funcion es para PAGO DE COLEGIATURAS
	// 
	public function generarAdeudoColegiatura($id,$cliente){
		global $DBSito;
		//$DBSito->debug = true;		
		$monto = $this->getMonto($id);
		$generado = $this->adeudoGenerado($id, $cliente);
		
		$sql = "insert into cxc
				(cve_concepto,cve_cliente,monto_adeudo,monto_cargo,fecha_alta,fecha_act,[status],cve_cajero,descripcion)
				values
				({$id},{$cliente},{$monto},0,getdate(),getdate(),1,0,'Colegiatura generada por alumno')
				";
		
		if (!$generado){
			//$rs = $DBSito->Execute($sql);
			return true;				
		}else{
			return false;
		}				
	}
	
	
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	// 					   MULTA
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
	
	public function generarMulta($cliente){
		global $DBSito;
		//$DBSito->debug = true;
		$cve_concepto = $this->getCveMulta();
		$monto_multa = $this->getMontoMulta();
		
		$sql="Select cve_cxc from cxc where cve_cliente=$cliente and cve_concepto=$cve_concepto and [status]=1";
		$rs = $DBSito->Execute($sql);
		if($rs->RecordCount()>0){
			return $rs->fields["cve_cxc"];
		}
		$sql = "insert into cxc
		(cve_concepto,cve_cliente,monto_adeudo,monto_cargo,fecha_alta,fecha_act,[status],cve_cajero,descripcion)
		values
		($cve_concepto,{$cliente},$monto_multa,0,getdate(),getdate(),1,0,'Multa por reinscripcion extemporanea')
		";
		//pre($sql);
		$rs = $DBSito->Execute($sql);
		return $DBSito->Insert_ID();
	}

	public function getMontoMulta(){
		global $DBSito;
	
		$cve = @$this->getCveMulta();
		$sql = "select monto from concepto where cve_concepto = $cve";
	
		$rs = $DBSito->Execute($sql);
	
		return $rs->fields['monto'];
	}
	
	
	// Muestra la clave de multa asignada en la tabla
	public function getCveMulta(){
		global $DBSito;
	
		$sql = "select multa from configReinscripcion where periodo = (select max(periodo) from configReinscripcion)
				";
	
		$rs = $DBSito->Execute($sql);
	
		return $rs->fields['multa'];
	}
	
		
	public function getGruposConceptosReferenciados(){
		global $DBSito;
		
		$sql = "select cve_grupo_concepto, nombre from grupo_concepto where pago_referenciado = 1 and activo = 1";
		
		$rs = $DBSito->Execute($sql);
		return $rs->getArray();
	}
	
	public function getTodosLosGruposConceptos(){
		global $DBSito;
	
		$sql = "select cve_grupo_concepto, nombre from grupo_concepto where activo = 1";
	
		$rs = $DBSito->Execute($sql);
		return $rs->getArray();
	}
	
	public function getConceptosReferenciados($grupo){
		global $DBSito;
		

		// Quitamos que se puedan ver PAGOS INTERCUATRIMESTRALES !!!!!!!!!!!!!!!!!!!!!!		
		$sql = "select c.cve_concepto, c.nombre, c.monto, isnull(cc.cve_cie,73431) as cie from concepto c
				inner join grupo_concepto gc on c.cve_grupo_concepto = gc.cve_grupo_concepto
				left outer join clave_cie cc on c.cve_grupo_concepto = cc.cve_grupo_concepto
				where c.cve_grupo_concepto = {$grupo} and c.nombre not like '%inter%' and c.activo = 1
				order by 2
				";
		// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!		

		$rs = $DBSito->Execute($sql);
		return $rs->getArray();
	}
	
	// Pagos Referenciados
	public function printConceptosReferenciados($grupo){
		$conceptos = @$this->getConceptosReferenciados($grupo);
		
		$html_code = '<h3>Concepto</h3>';
		$html_code .= '<select id="elegir_concepto" name="elegir_concepto" class="form-control">';
		$html_code .= '<option value="0">--- Selecciona un conceptos ---</option>';
		

		/* AQUI LE PASAMOS LOS VALORES IMPORTANTES EN DATA, COMO MONTO */
		foreach ($conceptos as $c){
			$html_code .= '<option value="'.$c['cve_concepto'].'" data-monto = "'.$c['monto'].'" data-cie = "'.$c['cie'].'">'.$c['nombre'].'</option>';
		}
		
		$html_code .= '</select>';
		
		return $html_code;
	}
	
	// Convenios
	public function printConceptosGrupos($grupo){
		$conceptos = @$this->getConceptosReferenciados($grupo);
	
		$html_code = '<h3 class="text-danger">Exclusiones</h3>';				
	
		$html_code .= "<table class=\"table \">";
		$html_code .= "<tr>";		
		$html_code .= "<th class=\"centrado \">Id</th>";
		$html_code .= "<th>Concepto</th>";
		$html_code .= "<th class=\"centrado \">Excluir</th>";
		
		$html_code .= "</tr>";
		
		foreach ($conceptos as $c){
			$html_code .= "<tr>";
			$html_code .= "<td  class=\"centrado \">{$c['cve_concepto']}</td>";
			$html_code .= "<td>{$c['nombre']}</td>";
			$html_code .= "<td class=\"centrado\">$ </td>";			
			$html_code .= "</tr>";
		}			
		$html_code .= "</table>";
		
		return $html_code;
	}
	
	public function getOtrosConceptos($cve){
		global $DBSito;
		
		$sql = "select cve_concepto, nombre, monto from concepto where cve_concepto = ".$cve;
		
		$rs = $DBSito->Execute($sql);
		return $rs->fields;
	}
	
}
