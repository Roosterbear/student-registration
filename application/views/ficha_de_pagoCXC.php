<?php
require_once __DIR__.'/../libraries/Cajas/Cajas.php';
require_once __DIR__.'/../libraries/php-barcode/autoload.php';//Clase para el codigo de barras 

$estudiante = Cajas::getEstudiante($matricula);
$info = Cajas::getCXC($cve_cxc, $matricula);
$total = ceil($info->monto);

$id = $info->id;
$concepto = $info->nombre." ".$info->descripcion;
$fecha_limite = new DateTime();
/**
 * La ficha caducara el mismo dia que se imprima
 * solicitud: msolano a travez de olga
 *  @since 2018-10-24
 *  @author jguerrero 
 */
//$fecha_limite->add(new DateInterval('P1D'));//original 1
$matricula = '00000';
$cadena = Cajas::generarReferencia('Bancomer10', $id, $matricula, $total, $fecha_limite->format('d'), $fecha_limite->format('m'),'N',"0");



?>

<div>
	<div class="container">
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
					<th >Matr&iacute;cula</th>
					<td><?=$estudiante->getMatricula()?></td>
				</tr>
				<tr>
					<th>Carrera</th>
					<td><?=$estudiante->getCarrera()?></td>
				</tr>
				<tr>
					<th>Ultimo perido registrado</th>
					<td><?=$estudiante->getCuatrimestre()?></td>
				</tr>
				<tr>
					<th>Ultimo grupo registrado</th>
					<td><?=$estudiante->getGrupo()?></td>
				</tr>
			</table>
		</div>
		
	    <?php if(Cajas::esVigente($matricula, $id)){?>	
	    <div class="row">
			<p class="lead">
				Referencias de Pago
			</p>
			<table class="table table-striped table-bordered lead table-condensed table-responsive" style="font-size:100%">
				<tr>
					<th >Concepto</th>
					<td colspan="3"><?=$concepto?></td>
				</tr>
				<tr>
					<th>Monto a pagar</th>
					<td colspan="3" class="text-center"><?=money_format("$ %i", $total)?></td>
				</tr>
				<tr>
					<th>Fecha l&iacute;mite de pago</th>
					<td colspan="3" class="text-center"><?=$fecha_limite->format("d/m/Y")?></td>
				</tr>	
				<tr>
					<th>Instituci&oacute;n</th>
					<td class="text-center" ><img src="<?=base_url()?>application/assets/images/Bancomer.ico" alt="Bancomer"> BANCOMER</td>
					<?php if($cie==73431){?>
					<td class="text-center" ><img src="<?=base_url()?>application/assets/images/Santander.ico" alt="BancoAzteca"> SANTANDER</td>
					<?php }?>
				</tr>
				<tr>
					<th>Convenio</th>
					<td class="text-center">CIE <?=$cie?></td>
					<?php if($cie==73431){?>
					<td class="text-center">4932</td>
					<?php } ?>
				</tr>

				<tr>
					<th>L&iacute;nea de captura</th>
					<td colspan="2" class="text-center"><b><?=$cadena?></b></td>
				</tr>
			</table>
		</div>
		        
        <form action="https://www.adquiramexico.com.mx:443/mExpress/pago/avanzado" method="post"/>
        <div class="row hidden-print">
    		<div class="col-sm-3 "><span class="label label-success"><i class="fa fa-check-circle " aria-hidden="true"></i> Nuevo</span><p class="lead">Pagar en Linea 
    		</p>
    		</div>
    		<div class="col-sm-3 ">
    		<input type="image" style="height: 1.5cm;" src="data:image/jpeg;base64,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"  >
    		
    		</div>
    		<div class="col-sm-6 ">
    		
				<?php
				
				$meBBVA=new MultipagosExpressBBVA();
				$meBBVA->setImporte($total);//FIXME
				$meBBVA->setReferencia(str_ireplace(" ", "", $cadena));

				?><p>Con tarjeta de Crédito o débito.
                
                <input type="hidden" name="importe" value="<?=money_format("%i", $meBBVA->getImporte())?>"/>
                <input type="hidden" name="referencia" value="<?=$meBBVA->getReferencia()?>"/>
                <input type="hidden" name="urlretorno" value="<?=$meBBVA->URL_RETORNO?>"/>
                <input type="hidden" name="idexpress" value="<?=$meBBVA->getIdExpress()?>"/>
                <input type="hidden" name="financiamiento" value="0"/>
                <input type="hidden" name="plazos" value="" />
                <input type="hidden" name="mediospago" value="100000"/>
                <input type="hidden" name="signature" value="<?php echo $meBBVA->Firma()?>"/>
				<input type="image" src="https://s3.amazonaws.com/prod.adquira.mp2.repo.files/verticales/bexpress/resources/img/icon/paybutton_3.png" style="vertical-align: middle;"/>
                <button class="btn btn-primary" type="submit">Ir </button>
                
        
            </div>
        </div>
        </form>
		
		
		
		<?php if($estudiante->getMatricula()==130062){ 
		    $oxxo= Cajas::generarReferencia('Oxxo', $id, $matricula, $total, $fecha_limite->format('d'), $fecha_limite->format('m'),'N',"0"); /* @var $oxxo Oxxo */
		    $oxxo->setIdentificador(23);//Valor asignado por Oxxo
		    //$oxxo->getLineaCompleta("Base10");//Algoritmo para calculo
		    //$oxxo->getLineaCompleta("Algoritmo123");//Algoritmo para calculo
		    $oxxo->getLineaCompleta("Algoritmo19");//Algoritmo para calculo
		    ?>
		<div class="row">
	    	<div class="col-sm-6 ">
		    	<p class="lead">Pago en  <img alt="" src="/reinscripcion/oxxo.png" style="height: 1cm;"></p>
	    	 </div>
			<div class="col-sm-6 text-center">
				<div style="height: 1cm;width: 6cm;"><?php 
					$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
		            ?>
		            <img src="data:image/png;base64,<?php echo base64_encode($generator->getBarcode($oxxo->__toString(), $generator::TYPE_CODE_128 ))?>"  style="height:100%;width: 100% "/>
		           <?php echo $oxxo->__toString();?>
		        </div>
	        </div>
	        <?php } ?>
			<?php }else{?>
			<div class="row">
				<div class="alert alert-warning">El Concepto <b><?=$concepto?></b> no est&aacute; vigente para su Pago</div>
			</div>
		<?php }?>
		</div>
		<br>
		<div class="row">
			<div class="container">
				<p class="lead" style="font-size:70%">
				<?=$texto_pie_ficha?>
				</p>
			</div>
		</div>
		
	</div>
</div>
<script type="text/javascript">
$('#btn-imprimir').click(function(){
	$('#btn-imprimir').hide();
	window.print();
});
</script>
</body>
</html>

