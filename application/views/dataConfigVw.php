<!-- ################################## -->
<!-- Menu -->  
<!-- ################################## -->
<h1 class="conf">
<span id="boton_crear" class="menu_superior">Crear </span> 
<span id="boton_activar" class="menu_superior">Activar</span> 
<span id="boton_multas" class="menu_superior">Multas</span> 
<span id="boton_modificar" class="menu_superior">Modificar</span>
</h1>

<!-- ################################## -->
<!-- Crear -->  
<!-- ################################## -->
<div id = "crear" class="caja">
	
	<h2 class="conf">Periodo a crear</h2>
	<span class="mensaje">(El periodo m&aacute;s reciente ser&aacute; el que se activar&aacute;)</span>		
	<select id="periodos_crear" class="select-css">
		<option value="0">Elige un periodo</option>
		<?php 
		foreach($periodos as $i=>$p){
		?>
			<option value="<?php echo $i;?>"><?php echo $p;?></option>	
		<?php
		}
		?>
	</select>

	<div id="div--btn__crear">
		<button id = "btn_crear" type="button" class="btn btn__verde">Crear</button>
	</div>
	<div id="respuesta_crear"></div>
</div><!--caja crear-->	

<!-- ################################## -->
<!-- Activacion Manual -->  
<!-- ################################## -->
<div id = "activar" class="caja">
	<h2 class="conf">Activacion MANUAL de Reinscripciones
		<label class="switch">
			<input type="checkbox" id="switch_reinscripcion" name="switch_reinscripcion">
			<span class="slider round"></span>
		</label>
	</h2>
	<span class="mensaje">(Se recomienda solo para situaciones emergentes, ya que no tiene fecha de finalizaci&oacute;n)</span>
</div><!--caja activacion-->	
	
<!-- ################################## -->	
<!-- Multas -->  
<!-- ################################## -->
<div id = "multas" class="caja">
<h2 class="conf">Activar multas</h2>
	<label class="switch">
		<input type="checkbox" id="switch_multa" name="switch_multa">
  		<span class="slider round"></span>
	</label>
<h2 class="conf">Multa a aplicar
	<select id="listado_multas" class="select-css">
		<option value="0">Elige una Multa</option>
		<?php 
		foreach($listado_multas as $i){
		?>
			<option value="<?php echo $i['id'];?>"><?php echo '['.$i['id'].'] '.substr($i['nombre'],0,30).'... $'.$i['monto'].'.00';?></option>	
		<?php
		}
		?>
	</select>	
</h2>
	<div id="div--btn__multa">
		<button id = "btn_multa" type="button" class="btn btn__verde">Aplicar</button>
	</div>
	<div id="respuesta_multa"></div>
</div><!--caja multas-->	
	
<!-- ################################## -->	
<!-- Modificar -->  
<!-- ################################## -->
<div id = "modificar" class="caja">
<h3 class="conf">Periodo activo:
	 <u id="ultimo_periodo" class="underline"><?php echo $ultimo_periodo;?></u>
	 <button id = "btn_eliminar" type="button" class="btn btn__rojo">Eliminar</button>
</h3>
	<label for="fecha_inicio"><h2 class="conf"><i class="fa fa-calendar"></i> Fecha inicio de Reinscripci&oacute;n</h2></label>     
	<input type="text" id="fecha_inicio" name="fecha_inicio" calendario value="<?php echo $fecha_inicio;?>"  class="form__input" />      
	<div class="espacio"></div>
	<label for="fecha_fin"><h2 class="conf"><i class="fa fa-calendar"></i> Fecha Fin de Reinscripci&oacute;n</h2></label>     
	<input type="text" id="fecha_fin" name="fecha_fin" calendario value="<?php echo $fecha_fin;?>"  class="form__input" />      

	<div id="div--btn__modificar">
		<button id = "btn_modificar" type="button" class="btn btn__verde">Modificar</button>
	</div>
	<div id="respuesta_modificar"></div>
	<div id="respuesta_eliminar"></div>
</div><!--caja modificar-->	




<script type="text/javascript">
// ----------------------------
// Crear periodo
// ----------------------------
var dire_crear = "<?=base_url()?>"+"index.php/DataConfiguration/crearPeriodo/";

// ----------------------------
// Activar Reinscripcion
// ----------------------------
var dire_activa = "<?=base_url()?>"+"index.php/DataConfiguration/reinscripcionActiva/";
var dire_activar = "<?=base_url()?>"+"index.php/DataConfiguration/activarReinscripciones/";
var dire_desactivar = "<?=base_url()?>"+"index.php/DataConfiguration/desactivarReinscripciones/";

// ----------------------------
// Activar Multa
// ----------------------------
var dire_multa_activa = "<?=base_url()?>"+"index.php/DataConfiguration/multaActiva/";
var dire_multa_activar = "<?=base_url()?>"+"index.php/DataConfiguration/activarMultas/";
var dire_multa_desactivar = "<?=base_url()?>"+"index.php/DataConfiguration/desactivarMultas/";
var dire_cambiar_multa = "<?=base_url()?>"+"index.php/DataConfiguration/setMulta/";

// ----------------------------
//Modificar periodo (Fechas)
// ----------------------------
var dire_modificar_periodo = "<?=base_url()?>"+"index.php/DataConfiguration/actualizarFecha/";
var dire_eliminar_periodo = "<?=base_url()?>"+"index.php/DataConfiguration/eliminarPeriodo/";
var dire_get_ultimo_periodo = "<?=base_url()?>"+"index.php/DataConfiguration/getUltimoPeriodo/";

// ----------------------------
// Crear
// ----------------------------
let periodo = $('#periodos_crear').val();
$('#periodos_crear').change(function(){
	periodo = $('#periodos_crear').val();
});

$('#btn_crear').click(function(){	
	if (parseInt(periodo)){
		$.post(dire_crear,{id:periodo},function(resp){
			$('#respuesta_crear').html(resp);
			setTimeout(() => {
				actualizarPeriodo();
				$('#respuesta_crear').html('');
			}, 2000);
		});	
	}else{
		let tipo_crear = $('#respuesta_crear');
		noCero(tipo_crear);
	}
});


function noCero(tipo){
	var mensaje_error_cero = '<div class="mensaje_dinamico alerta">Debes elegir una opcion</div>';
	tipo.html(mensaje_error_cero);
		setTimeout(() => {		
			tipo.html('');
		}, 2000);
}

// ----------------------------
// Activar
// ----------------------------
$.post(dire_activa,function(resp){	
	if (resp == '0'){		
		$('#switch_reinscripcion').prop('checked',false);
	}else{
		$('#switch_reinscripcion').prop('checked',true);
	}
	
});

$("#switch_reinscripcion").click(function(){
	if( $('#switch_reinscripcion').prop('checked') ) {
	    $.post(dire_activar,function(resp){
			$('#respuesta').html(resp);
		});	 
		$('#dire').html(dire_activar);   
	}else{
		$.post(dire_desactivar,function(resp){
			$('#respuesta').html(resp);
		});
	}
});

// ----------------------------
// Multas
// ----------------------------
let multa = $('#listado_multas').val();
$('#listado_multas').change(function(){
	multa = $('#listado_multas').val();
});

$('#btn_multa').click(function(){
	if (parseInt(multa)){
		$.post(dire_cambiar_multa,{multa:multa},function(resp){
			$('#respuesta_multa').html(resp);
			setTimeout(() => {
				$('#respuesta_multa').html('');
			}, 2000);
		});	
	}else{
		let tipo_multa = $('#respuesta_multa');
		noCero(tipo_multa);
	}
});

$.post(dire_multa_activa,function(resp){	
	if (resp == '0'){		
		$('#switch_multa').prop('checked',false);
	}else{
		$('#switch_multa').prop('checked',true);
	}
	
});

$("#switch_multa").click(function(){
	if( $('#switch_multa').prop('checked') ) {
	    $.post(dire_multa_activar,function(resp){
			$('#respuesta').html(resp);
		});	 
		$('#dire').html(dire_multa_activar);   
	}else{
		$.post(dire_multa_desactivar,function(resp){
			$('#respuesta').html(resp);
		});
	}
});


// ----------------------------
// Modificar
// ----------------------------
var fecha_inicio, fecha_fin
$('#btn_modificar').click(function(){
	fecha_inicio = $('#fecha_inicio').val();
	fecha_fin = $('#fecha_fin').val();
	$.post(dire_modificar_periodo,{fecha_inicio:fecha_inicio,fecha_fin:fecha_fin},function(resp){
		$('#respuesta_modificar').html(resp);
		setTimeout(() => {
			$('#respuesta_modificar').html('');
		}, 2000);
	});
	
});

$('#btn_eliminar').click(function(){
	$.post(dire_eliminar_periodo,function(resp){
		$('#respuesta_eliminar').html(resp);
		setTimeout(() =>{			
			actualizarPeriodo();
			$('#respuesta_eliminar').html('');
		},2000);
	
	});
});

function actualizarPeriodo(){
	$.post(dire_get_ultimo_periodo,function(resp){
		$('#ultimo_periodo').html(resp);
	});

}

/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
/* @@@@@@@@@@@@@@@ M E N U @@@@@@@@@@@@@@@@@ */
/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */

//Desactivar CAJAS inactivas (Estado Default)
$('#activar').hide();
$('#multas').hide();
$('#modificar').hide();
$('.menu_superior').removeClass('seleccion');
$('#boton_crear').addClass('seleccion');


//Menu
$('#boton_crear').click(function(){
	$('.caja').hide();
	$('#crear').show();
	$('.menu_superior').removeClass('seleccion');
	$('#boton_crear').addClass('seleccion');

});

$('#boton_activar').click(function(){
	$('.caja').hide();
	$('#activar').show();
	$('.menu_superior').removeClass('seleccion');
	$('#boton_activar').addClass('seleccion');
});

$('#boton_multas').click(function(){
	$('.caja').hide();
	$('#multas').show();
	$('.menu_superior').removeClass('seleccion');
	$('#boton_multas').addClass('seleccion');
});

$('#boton_modificar').click(function(){
	$('.caja').hide();
	$('#modificar').show();
	$('.menu_superior').removeClass('seleccion');
	$('#boton_modificar').addClass('seleccion');
});



/* CALENDARIO DATEPICKER */
$("[calendario]").datepicker({
		startView: 0,
		format: "yyyy-mm-dd",
		language: "es",
		weekStart: 1,
		autoclose: true,
		orientation: "bottom auto",
		todayHighlight: true,
		keyboardNavigation: false,
		daysOfWeekDisabled: [0,6]
});



</script>
