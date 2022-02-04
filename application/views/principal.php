<?php


require_once __DIR__.'/../libraries/Cajas/Cajas.php';
$matricula=140209;
$estudiante= Cajas::getEstudiante($matricula);


$esInscribible=$estudiante->getFin()!=1;
$id_inscripcion="NA";
?><!DOCTYPE html>
<html>
<head>
	<title>PAGOS-CAJAS UTA</title>
	<!-- 
	<meta name="viewport" content="width=device-width, initial-scale=1">
	 -->
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    
    <!-- Javascrpit files-->     
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>
    
    <!-- Style files-->
	

</head>
<body>
<div class="container">

<div class="row text-center page-header">
	<p class="lead">Universidad Tecnológica de Aguascalientes<br>
		<small>Sistema de pagos y reinscripciones</small><br>
	</p>
</div>

<div class="row">
	<p class="lead">Datos del Estudiante</p>
	<table class="table table-bordered table-striped">
		<tr>
			<th>Nombre</th>
			<th>Matricula</th>
			<th>Carrera</th>
			<th>Grupo Actual</th>
		</tr>
		<tr>
			<td><?=$estudiante->getNombre()?></td>
			<td><?=$estudiante->getMatricula()?></td>
			<td><?=$estudiante->getCarrera()?></td>
			<td><?=$estudiante->getGrupo()?></td>
		</tr>
	</table>
</div>
<div class="row">
	<p class="lead">Validación de la Reinscripción</p>
	<table class="table table-striped table-bordered table-condensed">
		<tr>
			<th>Evaluación</th>
			<th class="text-center" width="1%">Estatus</th>
		</tr>
		<?php 
		$adeudos= Cajas::findAdeudosGeneral($matricula);
		?>
		<tr class="<?=(isset($adeudos['calificacion'])?'danger':'success')?>">
			<td>Estatus de Calificaciones</td>
			<td class="text-center">
			<?php if(isset($adeudos['calificacion'])){ $esInscribible=false;?>
			<a title="mod" data-page="adeudo_calificacion.php" id="<?=$matricula?>" data-toggle="modal" data-target="#modal">
				<i class="fa fa-times-circle-o fa-2x text-danger"></i>
			</a><?php }else{?>
			<i class="fa fa-check-circle-o fa-2x"></i>
			<?php }?>
			</td>
		</tr>
		<tr class="<?=(isset($adeudos['documentos'])?'danger':'success')?>">
			<td>Entrega de Documentos</td>
			<td class="text-center">
			<?php if(isset($adeudos['documentos'])){ $esInscribible=false;?>
			<a title="mod" data-page="adeudo_documentos.php" id="<?=$matricula?>" data-toggle="modal" data-target="#modal">
				<i class="fa fa-times-circle-o fa-2x text-danger"></i>
			</a><?php }else{?>
			<i class="fa fa-check-circle-o fa-2x"></i>
			<?php }?>
			</td>
		</tr>
		<tr class="<?=(isset($adeudos['cajas'])?'danger':'success')?>">
			<td>Adeudos en Caja e Intercuatrimestrales</td>
			<td class="text-center">
			<?php if(isset($adeudos['cajas'])){ $esInscribible=Cajas::esBecaIDCSEA($matricula)?$esInscribible:false;?>
			<a title="mod" data-page="adeudo_cajas.php" id="<?=$matricula?>" data-toggle="modal" data-target="#modal">
				<i class="fa fa-times-circle-o fa-2x text-danger"></i>
			</a><?php }else{?>
			<i class="fa fa-check-circle-o fa-2x"></i>
			<?php }?>
			</td>
		</tr>
	</table>
<?php if($esInscribible){
	$id_inscripcion=Cajas::getReinscripcion($matricula);
	?>
	<a href="ficha_de_pago.php?id=<?=$id_inscripcion?>" class="btn btn-success btn-sm form-control" target="_blank">Inscribirme</a>
<?php }?>
</div>
			
<div id="footer" ></div>
<div class="modal fade" tabindex="-1" role="dialog" id="modal">
<div class="modal-dialog">
	<div class="modal-content" id="modal_html"></div></div>
</div>

</div>
<script type="text/javascript">
$("a[title=mod]").click(function(){
	web=$(this).attr("data-page");
	$("#modal_html").html('<center><i class="fa fa-refresh fa-spin"></i> Cargando...</center>');
	//alert(web);
	id=$(this).attr("id");
	$.post("mod/"+web+"",{matricula: id},function(data){
		$("#modal_html").html(data);
	});
	return true;
});
</script>
</body>
</html>