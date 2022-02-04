
<form id="formBanBajio" action="<?php echo site_url("PagoBanBajio/Consolidar")?>" >
	<input name="cl_folio" id="folio" class="form-control input-sm" type="text" required="required" value="">
	<input name="cl_referencia" id="referencia"  type="text" required="required" class="form-control input-sm" value=""/>
	<input name="dl_monto" id="monto" type="text" class="form-control input-sm" required="required" value=""/>
	<input name="dt_fechaPago" id="fechaPago" type="text" required="required" class="form-control input-sm" value="" />
	<input name="nl_tipoPago"id="tipoPago"  type="text" required="required" class="form-control input-sm" value="" />
	<input name="nl_status"id="status"  type="text" required="required" class="form-control input-sm" value="" />
	<input name="hash" id="hash" type="text" class="form-control input-sm" required="required" value=""/><br>
	<div class="col-md-2 pull-right">
	<button class="btn btn-md btn-primary btn-block" name="Submit" type="submit"><i class="fa fa-shopping-cart"></i> Enviar datos</button>
	</div><br>
</form>

<div id="respuesta"></div>

<script type="text/javascript">

	$("formBanBajio").on("submit",function(e){
		e.stopImmediatePropagation();
		e.preventDefault();
	
		$.ajax({
	      url: $(this).attr("action"),
	      type: 'POST',
	      data: new FormData(this),
	      processData: false,
	      contentType: false
	    }).done(function(resp) {
	    	$("#respuesta").html(resp);
	    });
	});
	
	$(document).ajaxStart(function(){
	    $("#cargando").show();
	});
	
	$(document).ajaxComplete(function(){
	    $("#cargando").hide();
	});
</script>