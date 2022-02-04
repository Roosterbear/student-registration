<?php
require_once __DIR__.'/../libraries/Cajas/Cajas.php';
//if($matricula!=141603){die("En Mantenimiento");}
$estudiante= Cajas::getEstudiante($matricula);
$info=Cajas::getConcepto($cve, $matricula);
$total=$info->monto;
$id=$info->id;
$concepto=$info->nombre." ".$info->descripcion;
$bypass=false;
$total_multa=0;
if($id==2010){$bypass=true;$total+=62;}
if($id==2009){$bypass=true;$total+=62;}
if($id==197){$bypass=true;$total+=62;}
if($id==198){$bypass=true;$total+=62;}

$fecha_limite = new DateTime();
/**
 * La ficha caducara el mismo dia que se imprima
 * solicitud: msolano a travez de olga
 *  @since 2018-10-24
 *  @author jguerrero
 */
//$fecha_limite->add(new DateInterval('P1D'));//original 1
$cadena=Cajas::generarReferencia('Bancomer10',$matricula, $id, $total, $fecha_limite->format('d'),$fecha_limite->format('m'),'N');
$adeudos= Cajas::findAdeudosGeneral($matricula);


/******* INFORMACIón PARA LA MULTA **************/

$info=Cajas::getConcepto(201, $matricula);// 201 = Multa Reinscripcion


$id_multa=@$cve_cxc;//Buscar CxC

$cadena_multa = Cajas::generarReferencia('Bancomer10', $id_multa, '00000', $total_multa, $fecha_limite->format('d'), $fecha_limite->format('m'),'N',"0");

?>

<div class="container">
	<div class="row text-center page-header">
		<center><img src="<?=base_url()?>application/assets/images/head.png" alt="UTAGS" style ="max-height:110px;" class="img-responsive"></center>
	</div>
	<div id="noPrinter" class="row">
		<div class="col-lg-12">
			<div class="col-lg-8"><p class="lead">Datos del Estudiante con</p></div>
			<div class="col-lg-4 derecha">
				<?=$boton_imprimir?>
		  	</div>
  		</div>
	</div>	
	<div class="espaciado"></div>
	<div class="row">
		<table class="table table-striped table-bordered lead table-condensed table-responsive" style="font-size:100%">
			<tr>
				<th >Nombre</th>
				<td><?=$estudiante->getNombre()?></td>
			</tr>
			<tr>
				<th >Matrícula</th>
				<td><?=$estudiante->getMatricula()?></td>
			</tr>
			<tr>
				<th>Carrera</th>
				<td><?=$estudiante->getCarrera()?></td>
			</tr>
			<tr>
				<th>Último perido registrado</th>
				<td><?=$estudiante->getCuatrimestre()?></td>
			</tr>
			<tr>
				<th>Último grupo registrado</th>
				<td><?=$estudiante->getGrupo()?></td>
			</tr>
		</table>
	</div>
	<div class="row">
		<p class="lead">
			Referencias de Pago
		</p>
		<table class="table table-striped table-bordered lead table-condensed table-responsive" style="font-size:100%">
			<tr>
				<th >Concepto</th>
				<td><?=$concepto?></td>
				
				  
				<th>Total</th>
			</tr>
			<tr>
				<th>Institución</th>
				<td colspan="2"><img src="<?=base_url()?>application/assets/images/Bancomer.ico" alt="Bancomer"> BANCOMER</td>
				<td></td>
			</tr>
			<tr>
				<th>Convenio</th>
				<td colspan="2">CIE 73431</td>
				<td></td>
			</tr>
			<tr>
				<th>Monto a pagar</th>

				 <td><?=money_format("$ %i", $total)?></td>
				 
				 
				 <th><?=money_format("$ %i", $total_multa+$total)?></th>
				   
			</tr>
			<tr>
				<th>Línea de captura</th>
				<td><?=$cadena?></td>
				
				<td></td>
			</tr>
			<tr>
				<th>Fecha límite de pago</th>
				
				<td colspan="2"><?=$fecha_limite->format("d/m/Y")?></td>
				<td></td>
			</tr>
		</table>
	</div>
	<div class="row">
		<p class="lead" style="font-size:70%">
		<?=$texto_pie_ficha?>
		</p>
		</div>
	</div>
<script type="text/javascript">
$('body').click(function(){
	$('#btn-imprimir').hide();
	window.print();
});
</script>
</body>
</html>

