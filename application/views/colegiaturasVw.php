<div class="container-fluid no-printable">
	<div id="panelito" class="panel panel-default">
	    <div class="panel-heading">
	        <div class="panel-title"><h4>Pagos Referenciados </h4></div>
	    </div>
	    <div class="panel-body">
	    	<div class="row">
	    		<div class="col-lg-5">
	    			<h3>Colegiaturas <strong><?=$periodo_text?></strong></h3>
	    			<h4>Estatus del alumno: 
	    				<strong class="text-<?=$esta_inscrito?'success':'danger'?>"><?=$esta_inscrito?'INSCRITO':'NO INSCRITO'?> </strong>
	    				<span class="text-primary"><u><?=$tiene_beca?'Beca '.$tipo_beca:''?></u></span>
	    			</h4>
	    			<div>
	    				<h3><strong>Pagos cuatrimestrales</strong></h3>
	    				<h4><?=$titulo_selector?> </h4>
	    				<?php  
	    				if (!$tiene_adeudos_colegiatura){
	    				    
	    				?>
	    				<table class="table table-responsive table-condensed table-striped table-bordered table-hover <?=$esta_inscrito?'':'blackened'?>">
	    					<tr><th>No.</th><th>Concepto<th>Selector</th><th>Concepto</th><th>Fecha L&iacute;mite</th><th>Monto</th><th>Ficha</th></tr>
	    					<?php $contador = 1; $cve_oculta_de_porcentaje = 0;?>
	    						    					
		    				<?php foreach ($colegiaturas as $c) { ?>
			    				<?php if (($c['cuentaxCobrar'] == 0)||($c['pagado'] > 0)){ ?>
								<tr>	    											
								   	<td><?=$contador++?></td>
								   	<td>Colegiatura <?=$c['colegiatura']?></td>
								   	<td><?=$c['cve_concepto_selector']?></td>
								   	<td><?=$c['cve_concepto']?></td>
								   	<td><?=str_replace('00:00:00','',$c['fecha_vencimiento'])?></td>
								   	<?php 
								   	$pagado = false;
								   	if (($c['pagado'] > 0)||($c['no_debe'] > 0)){
								   		$pagado = true;
								   	}
								   	$descuento_beca = ($c['monto']*($porcentaje_beca / 100));
								   	if ($porcentaje_beca == 0){$cve_oculta_de_porcentaje = 0;}
								   	if ($porcentaje_beca == 50){$cve_oculta_de_porcentaje = 1;}
								   	if ($porcentaje_beca == 75){$cve_oculta_de_porcentaje = 2;}
								   	if ($porcentaje_beca == 100){$cve_oculta_de_porcentaje = 3;}
								   	$c['monto'] = $c['monto']-$descuento_beca;
								   	?>
								   	<!-- MONTO -->  
								   	<?php if ($pagado){ ?>
								   	<td class="text-center"><span class="text-success"><i class="fa fa-check" aria-hidden="true"></i></span></td>
								   	<?php }else{ ?>
								   	<td>$ <?=$c['monto']?></td>
								   	<?php } ?>
								   	<!-- @@@@@ -->  

								   	<!-- FICHA -->  
							   		<?php if ($esta_inscrito){?>
							   			<?php if ($pagado){ ?>
							   				<td class="text-center"><span class="text-success"><i class="fa fa-print" aria-hidden="true"></i></span></td>
							   				<?php }else{ ?>
							   				<td class="text-center">
							   				<a href="<?=base_url()?>index.php/PagoColegiaturas/imprimirFicha/0/<?=$c['cve_concepto']?>/<?=$matricula?>/0/<?=$cve_oculta_de_porcentaje?>/73431" target="_top">
								   			<i class="fa fa-print" aria-hidden="true"></i></a></span>
								   			<?php } ?>
								   		<?php }else{?>
								   		<i class="fa fa-ban" aria-hidden="true"></i>
								   		<?php } ?>
								   		
								   	</td>
								   	<!-- @@@@@ -->  

								</tr>
								<?php }  ?><!-- if -->  
							<?php }  ?> <!-- foreach -->  
						</table>
						
						<?php  
						}
	    				?>
						<h3 class="<?=$tiene_adeudos_colegiatura?'aviso':''?> text-justify text-warning"><strong>							
							<?=$tiene_adeudos_colegiatura?$mensaje_adeudo_colegiatura:''?>
						</strong></h3>	
	    			</div>	
	    			<div>
	    				<h3><strong>Adeudos</strong></h3>
	    				<table class="table table-responsive table-condensed table-striped table-bordered table-hover">
	    				<tr><th>No.</th><th>Concepto<th>Fecha Alta</th><th>Monto</th><th>Ficha</th></tr>
	    				<?php $contador = 1; ?>
	    				<?php foreach ($adeudos as $a) { ?>
						<tr>	    											
						   	<td><?=$contador++?></td>
						   	<td><?=$a['concepto']?></td>
						   	<td><?=$a['fecha_alta']?></td>
						   	<td>

						   		<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
						   		<!-- MODAL QUE DEPLIEGA EL DETALLADO DEL MONTO -->  
						   		<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
						   		<a href="<?=base_url()?>index.php/PagoColegiaturas/desgloceMonto/<?=$a['monto']?>/<?=$a['monto_cargo']>0?$a['monto_cargo']:0?>" data-toggle="modal" data-target="#modal_monto">$ <?=$a['monto']+$a['monto_cargo']?></a>
						   		<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
						   	
						   	
						   	<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
						   	<!-- Cambiar clave CIE de acuerdo al grupo -->  
							<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
						   	<?php 
						   		$cie = 73431;
						   		$cie = $a['grupo'] == 14?1476130:$cie;
						   	 ?>
							<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  

						   	</td>
						   	<td class="text-center"><span class="text-success">
						   		<a href="<?=base_url()?>index.php/PagoColegiaturas/imprimirFicha/1/<?=$a['cve_cxc']?>/<?=$matricula?>/0/0/<?=$cie?>" target="_top"><i class="fa fa-print" aria-hidden="true"></i></span></td>
						</tr>
						<?php }  ?>
					</table>
	    			</div>
	    		</div>
	    		<div class="col-lg-2"></div>
	    		

	    		<div class="col-lg-5">
	    			<h3><strong>Otros pagos</strong></h3>
	    			<form id="otros_pagos" name="otros_pagos" method="post" action="#" role="form">
	    				<h4>Grupo de conceptos:</h4>
	    				<select id="elegir_grupo_concepto" name="elegir_grupo_concepto" class="form-control">
							<option value="0">--- Selecciona un grupo de conceptos ---</option>
							<?php foreach ($grupos as $g){ ?>
							<option value="<?=$g['cve_grupo_concepto']?>"><?=$g['nombre']?></option>
							<?php } ?>
						</select>
						<br>

						<div id="data_conceptos">	
							<!-- datos dinamicos de acuerdo al grupo de conceptos elegido -->  
							<!-- SELECT "elegir_concepto" -->  
						</div>
						<br>
						<div id="resultado_otros">

						</div>
	    			</form>
	    		</div>	
	    	</div>	
	    </div>
	</div>
</div>	

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modal_ficha">
    <div class="modal-dialog modal-exlg">
      <div class="modal-content">
      </div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modal_monto">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
      </div>
  </div>
</div>

<script type="text/javascript">

$(document).on("hidden.bs.modal", function(e){
	$(e.target).removeData("bs.modal").find(".modal-content").empty();
});

/*
--Quiero aprender de este codigo, por eso lo deje...

$('#modal_monto').on('show.bs.modal', function(e) {
  var trigger = $(e.relatedTarget); 
  var monto = trigger.data('monto');
  var monto_cargo = trigger.data('monto_cargo');
  var modal = $(this);
  modal.find('.monto').text(monto);
  modal.find('.monto_cargo').text(monto_cargo);
});
*/

$(document).ready(function(){
    $("#panelito").lobiPanel({
        reload: false,
        close: false,
        editTitle: false,
        unpin: false,
        toggleIcon: 'fa fa-bars',
        minimize: { icon: 'fa fa-minus', icon2: 'fa fa-plus', tooltip: 'Minimizar/Maximizar' },
        expand: { icon: 'fa fa-expand', icon2: 'fa fa-compress', tooltip: 'Activar/Desactivar pantalla completa' }
    });
});

$("body").on('click','[data-toggle=modal]', function (e) {
  $.post($(this).attr("href"),function(resp){
    $(".modal-content").html(resp);
  });
});


$('#elegir_grupo_concepto').change(function(){
	
	var dire_get = "<?=base_url()?>"+"index.php/PagoColegiaturas/getConceptosReferenciados/";
	var grupo = $('#elegir_grupo_concepto').val();

	$.post(dire_get,{grupo:grupo},function(resp){
		var html_code = resp;
		// RESPUESTA con informacion de el GRUPO elegido
		$('#data_conceptos').html(html_code);

	});
});

$('#data_conceptos').on('click',function(){

	$('#elegir_concepto').change(function(e){
		e.stopImmediatePropagation();
		var concepto = $('#elegir_concepto').val();
		var nombre_concepto = $('#elegir_concepto').text;

		// MONTO
		var valor_monto = $(this).find(':selected').data("monto");
		var monto = valor_monto?valor_monto:'';
		
		// CIE
		var valor_cie = $(this).find(':selected').data("cie")
		var cie =  valor_cie?valor_cie:'';
		
		var resultado_html = '';

		resultado_html += '<h2 class="text-success">Monto: ';
		resultado_html += valor_monto?'<strong class="text-danger"> $'+monto+'.00</strong></h2>':'';
		resultado_html += '<h5 class="text-warning">Clave CIE:<span class="text-info"> '+cie+'</span></h5>';
		resultado_html += '<a href="<?=base_url()?>index.php/PagoColegiaturas/imprimirFicha/0/'+concepto+'/<?=$matricula?>/1/0/'+cie+'" class="btn btn-primary btn-md" target="_top">';
		resultado_html += 'Imprimir Ficha <span class="espaciado"> <i class="fa fa-print" aria-hidden="true"></i></span></a>';

		$('#resultado_otros').html(resultado_html);
	});

});


</script>