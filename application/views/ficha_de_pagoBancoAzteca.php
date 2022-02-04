<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
<!-- @@@@@@@@@@@@@@@@@@@@@ OTROS PAGOS @@@@@@@@@@@@@@@@@@@@@@@@ -->  
<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
<?php
require_once __DIR__.'/../libraries/Cajas/Cajas.php';
require_once __DIR__.'/../libraries/php-barcode/autoload.php';//Clase para el codigo de barras 
$estudiante= Cajas::getEstudiante($matricula);

$info=Cajas::getConcepto($cve, $matricula);

$total = $monto;
$id = $cve_concepto;
$concepto = $nombre;
$bypass = false;

$total_multa = 0;
if($id==2010){$bypass=true;$total+=60;}
if($id==2009){$bypass=true;$total+=60;}
if($id==197){$bypass=true;$total+=60;}
if($id==198){$bypass=true;$total+=60;}

$fecha_limite = new DateTime();

/**
 * La ficha caducara el mismo dia que se imprima
 * solicitud: msolano a travez de olga
 *  @since 2018-10-24
 *  @author jguerrero
 */
$fecha_limite->add(new DateInterval('P5D'));//original 1

$cadena = Cajas::generarReferencia('Bancomer10',$matricula, $id, $total, $fecha_limite->format('d'),$fecha_limite->format('m'),'N');
$cadenaBA = Cajas::generarReferencia('BancoAzteca97',$matricula, $id, $total, $fecha_limite->format('d'),$fecha_limite->format('m'),'N');
$cadenaSantander = Cajas::generarReferencia('Santander22',$matricula, $id, $total, $fecha_limite->format('d'),$fecha_limite->format('m'),'N');
$adeudos = Cajas::findAdeudosGeneral($matricula);

///TODO pablo favor de trabajar aqui tu codigo para bb
if($matricula==141603){
    @$bbLib=new BanBajioLib();
    $bbcliente=new BBEnvioCliente();
    $bbcliente->setReferencia(substr($cadena->lineaCompleta,0,12));
    $bbcliente->setMonto($monto);
    $bbcliente->setConcepto("01");
    $bbcliente->setServicio("01");
    $bbcliente->setCve_persona($bbLib->MatriculaToCve_persona($matricula));
        
    $bbLib->FirmarEnvio($bbcliente);
    
    $bbLib->AgregarEnvioCliente($bbcliente);
?>

<form id="formBanBajio" action="<?= $bbLib->FORM_ACTION?>" > 		
    <input name="cl_folio" id="folio" class="form-control input-sm" type="hidden" required="required" value="<?=$bbcliente->getFolio()?>">
	<input name="cl_referencia" id="referencia"  type="hidden" required="required" class="form-control input-sm" value="<?=$bbcliente->getReferencia()?>"/>
	<input name="dl_monto" id="monto" type="hidden" class="form-control input-sm" required="required" value="<?=$bbcliente->getMonto()?>"/>
	<input name="cl_concepto" id="concepto" type="hidden" required="required" class="form-control input-sm" value="<?=$bbcliente->getConcepto()?>" />
	<input name="servicio"id="servicio"  type="hidden" required="required" class="form-control input-sm" value="<?=$bbcliente->getServicio()?>" />
	<input name="hash" id="hash" type="hidden" class="form-control input-sm" required="required" value="<?=$bbcliente->getHash()?>"/><br>		
	<div class="col-md-2 pull-right">
		<button class="btn btn-md btn-primary btn-block" name="Submit" type="submit"><i class="fa fa-shopping-cart"></i> Pagar En Linea</button>
	</div><br>
</form>

<?php }?>

<div>	
	<div class="container" id="temp_contenido_datos">
		<div class="row text-center page-header">
			<center><img src="<?=base_url()?>application/assets/images/head.png" alt="UTAGS" style ="max-height:110px;" class="img-responsive"></center>
		</div>
		<div id="noPrinter" class="row">
			<div class="col-lg-12">
				<div class="col-lg-8"><p class="lead">Datos del Estudiante</p></div>
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
					<th>Concepto</th>
					<td colspan="3" class="text-center"><?=$concepto?></td>
				</tr>
				<tr>
					<th>Monto a pagar</th>
					<td colspan="3" class="text-center"><?=money_format("$ %i", $total)?></td>
				</tr>
				<tr>
					<th>Fecha límite de pago</th>
					<td colspan="3" class="text-center"><?=$fecha_limite->format("d/m/Y")?></td>
				</tr>				
				 <tr>
					<th>Institución</th>
					<td class="text-center" ><img src="<?=base_url()?>application/assets/images/Bancomer.ico" alt="Bancomer"> BANCOMER</td>
					<td class="text-center" ><img src="<?=base_url()?>application/assets/images/Santander.ico" alt="BancoAzteca"> SANTANDER</td>
					
					
				</tr>
				<tr>
					<th>Convenio</th>
					<td class="text-center">CIE <?=$cie?></td>
					
					<td class="text-center">4932</td>
					
					
				</tr>								
				<tr>
					<th>Línea de captura</th>
					<td colspan="2" class="text-center"><b><?=$cadena?></b></td>
					
					<!-- <td><?=$cadenaBA?></td> -->
					
				</tr>				
			</table>
		</div>
		<?php if($estudiante->getMatricula()==141603 && false){ 
		$oxxo= Cajas::generarReferencia('Oxxo',$matricula, $id, $total, $fecha_limite->format('d'), $fecha_limite->format('m'),'N'); /* @var $oxxo Oxxo */
	    $oxxo->setIdentificador(23);//Valor asignado por Oxxo
	    //$oxxo->getLineaCompleta("Base10");//Algoritmo para calculo
	    //$oxxo->getLineaCompleta("Algoritmo123");//Algoritmo para calculo
	    $oxxo->getLineaCompleta("Algoritmo19");//Algoritmo para calculo
	    ?>
		<div class="row">
			<div class="col-sm-6 "><p class="lead">Pago en  <img alt="" src="/reinscripcion/oxxo.png" style="height: 1cm;"></p></div>
			<div class="col-sm-6 text-center">
			
	    		<div style="height: 1cm;width: 6cm;"><?php 
	    			$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
	                ?>
	                <img src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($oxxo->__toString(), $generator::TYPE_CODE_128 ))?>"  style="height:100%;width: 100% "/>
	               <?php echo $oxxo->__toString();?>
	            </div>
	    
	        </div>
        </div>
        <?php } ?>
        <br>
		<div class="row">
			<div class="col-sm-12 ">
				<p class="lead" style="font-size:70%">
					<?=$texto_pie_ficha?>
				</p>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$('#temp_contenido_datos').click(function(){
		$('#btn-imprimir').hide();
		window.print();
	});
</script>
</body>
</html>

