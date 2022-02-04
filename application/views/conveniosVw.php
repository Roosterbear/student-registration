<div class="container-fluid no-printable">
	<div id="panelito" class="panel panel-default">
	    <div class="panel-heading">
	        <div class="panel-title"><h4>Creaci&oacute;n de Convenios </h4></div>
	    </div>
	    <div class="panel-body">
	    	<div class="row">
	    		<div class="col-lg-2"></div>
	    		<div class="col-lg-8">

	    			<!-- Formulario -->  
	    			<form id="convenios_form" name="convenios_form" method="post" action="#" role="form">
	    				<h4>Grupo de conceptos:</h4>
	    				<select id="elegir_grupo" name="elegir_grupo" class="form-control">
							<option value="0">--- Selecciona un grupo de conceptos ---</option>
							<?php foreach ($grupos as $g){ ?>
							<option value="<?=$g['cve_grupo_concepto']?>"><?=$g['nombre']?></option>
							<?php } ?>
						</select>
						<br>

						<div class="convenio_institucion">
							<h3 class="text-success">Convenios</h3>
							<h4>Instituci&oacute;n Bancaria <a href="#" id="alta_institucion">
								<small class="espaciado alta details_text">Alta <i class="fa fa-caret-down" aria-hidden="true"></i></small></a>
							</h4>

							<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
							<!-- @@@@@@ ALTA DE INSTITUCION BANCARIA @@@@@@ -->  
							<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
							<div id="panel_alta_institucion">
								<br />
								<label for="data_nombre_institucion">
									<h4 class="text-danger"><strong>Ingresar nombre de Instituci&oacute;n: </strong></h4>
								</label>
		                		<div class="input-group">
				                  <input type="text" id="data_nombre_institucion" name="data_nombre_institucion" value="" class="form-control" />
				                  <span class="input-group-btn">
				                    <a href="#" class="btn btn-primary btn-md" id="btn_agregar_institucion" name="btn_agregar_institucion"> 
				                    Agregar </a>
				                  </span>
				                </div>
								<br />
							</div>	
							<br />
							<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  


							<select id="elegir_institucion_bancaria" name="elegir_institucion_bancaria" class="form-control">
								<option value="0" selected="selected">--- Selecciona una Institucion Financiera ---</option>
								<?php foreach ($instituciones as $ins){ ?>
								<option value="<?=$ins['id']?>"><?=$ins['nombre']?></option>
								<?php } ?>
							</select>
							<br>

			                <div>
			                	<label for="data_convenio"><h4>No. de Convenio CIE</h4></label>   
		                    	<input type="text" id="convenio_data" value="" class="form-control">
			                </div>
							<br>

							<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
							<!-- @@@@@@@@@@@@@@ EXCLUSIONES @@@@@@@@@@@@@@@ -->  
							<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
							<div id="data_conceptos">	
								<!-- datos dinamicos de acuerdo al grupo de conceptos elegido -->  
								<!-- SELECT "elegir_concepto" -->  
							</div>
							<br>
							<div id="resultado_otros">

							</div>
							<div class="text-center">
								<h3>Agregar <span class="text-primary"><u>NUEVA</u></span> <strong>Clave CIE</strong> al sistema</h3>
								<a href="#" class="btn btn-primary btn-md">Aceptar</a>
							</div>
						</div>
	    			</form>
	    		</div>
	    		<div class="col-lg-2"></div>
	    	</div>
		</div>
	</div>
</div>

<script type="text/javascript">

var alta_bool = false;

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

// Al elegir un grupo...
$('#elegir_grupo').change(function(){
	
	var dire_get = "<?=base_url()?>"+"index.php/Convenios/getConceptosGrupos/";
	var grupo = $('#elegir_grupo').val();

	// Mostrar las demas opciones...
	$('.convenio_institucion').show();

	// ...y mostrar los conceptos de ese grupo
	$.post(dire_get,{grupo:grupo},function(resp){
		var html_code = resp;
		// RESPUESTA con los conceptos de el GRUPO elegido
		$('#data_conceptos').html(html_code);

	});
});

$('#alta_institucion').click(function(){
	$('#panel_alta_institucion').slideToggle('fast');
	$('#alta_institucion').find('i').toggleClass('fa-caret-down fa-caret-up');

	if (alta_bool){
		$('.alta').css("color","#999");
		alta_bool = false;
	}else{
		$('.alta').css("color","red");
		alta_bool = true;

	}
	
});

$('#btn_agregar_institucion').click(function(){
	var dire = "<?=base_url()?>"+"index.php/Convenios/altaInstitucion/";
	var institucion = $('#data_nombre_institucion').val();
	
	$.post(dire,{institucion:institucion},function(){});
	$('#panel_alta_institucion').hide();
	mensajeLobibox('success','Institucion agregada con exito');
});

</script>