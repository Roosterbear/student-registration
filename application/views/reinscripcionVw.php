<div class="espaciado"></div>
<div class="mi-contenedor">
  <table class="tabla-datos">
    <tr>
      <th>Alumno</th>
      <th>Matr&iacute;cula</th>
      <th>Carrera</th>
      <th>Grupo</th>
    </tr> 
    <tr>
      <td><span><?=$this->system_controller->alumno->getNombre()?></span></td>
      <td><span><?=$this->system_controller->alumno->getMatricula()?></span></td>
      <td><span><?=$this->system_controller->alumno_datos_escolares->getCarrera()?></span></td>
      <td><span><?=$this->system_controller->alumno_datos_escolares->getGrupo()?></span></td>
    </tr>
  </table>
  <div class="text-center"><small>
    <a href="http://admision.utags.edu.mx/index.php/home/preregistroIng/<?=$periodo_actual_admision?>">Si terminaste TSU y quieres continuar tus estudios, entra aqui.</a>
  </small></div>
</div><!-- mi-contenedor -->  
<div class="clearfix"></div>

<div class="board">
  <!-- tabs -->  
  <div class="board-inner">
    <ul class="nav nav-tabs" id="myTab">

    <div class="liner"></div>
    <?php $documentos = $this->system_controller->alumno_documentos_controller->alumno_documentos->allChecked()?true:false?>
    <?php $documentos_active = $documentos?'':'active'?>
    <?php $one = $documentos?'completed':'incompleted'?>
    <li class="<?=$documentos_active?>">
      <a href="#documentos" data-toggle="tab" title="documentos">
        <span class="round-tabs <?=$one?>">
          <i class="fa fa-folder-o"></i>
        </span> 
      </a>
    </li>
    <?php $calificaciones = $this->system_controller->alumno_calificaciones_controller->alumno_calificaciones->allChecked()?true:false?>
    <?php $calificaciones_active = $documentos?($calificaciones?'':'active'):''?>
    <?php $two = $calificaciones?'completed':'incompleted'?>
    <li class="<?=$calificaciones_active?>">
      <a href="#calificaciones" data-toggle="tab" title="calificaciones">
        <span class="round-tabs two <?=$two?>">
          <i class="fa fa-pencil-square-o"></i>
        </span> 
      </a>
    </li>
    <?php $adeudos = $this->system_controller->alumno_adeudos_controller->alumno_adeudos->allChecked()?true:false?>
    <?php $adeudos_active = $documentos?($calificaciones?($adeudos?'':'active'):''):''?>
    <?php $three = $adeudos?'completed':'incompleted'?>
    <li class="<?=$adeudos_active?>">
      <a href="#adeudos" data-toggle="tab" title="adeudos">
        <span class="round-tabs three <?=$three?>">
         <i class="fa fa-usd"></i>
        </span> 
      </a>
    </li>
    <?php $completed = $this->system_controller->allChecked()?true:false?>
    <?php $completed_active = $documentos?($calificaciones?($adeudos?'active':''):''):''?>
    <?php $four = $completed?'completed':'incompleted'?>
    <li class="<?=$completed_active?>">
      <a href="#completed" data-toggle="tab" title="completed">
        <span class="round-tabs four <?=$four?>">
          <i class="fa fa-check"></i>
        </span>
      </a>
    </li>
     
    </ul>
  </div>

  <!-- contenido -->  
  <div class="tab-content">
    
    <!-- documentos -->  
    <div class="tab-pane fade in <?=$documentos_active?>" id="documentos">
      <h3 class="head text-center">Documentos</h3>
      <p class="narrow text-center">
        <div class="mi-contenedor">
            <?=@$this->system_controller->alumno_documentos_controller->alumno_documentos->printDocumentosToTable('tabla-datos') ?>
            <?php if(!$documentos){ ?>
            <span class="text-center">Para cualquier aclaraci&oacute;n respecto a adeudo de documentos, favor de enviar un correo a <strong>mcastaneda@utags.edu.mx</strong></<span>
            <?php } ?>
        </div>  
      </p>
    </div>

    <!-- calificaciones -->  
    <div class="tab-pane fade in <?=$calificaciones_active?>" id="calificaciones">
      <h3 class="head text-center">Calificaciones</h3>
      <p class="narrow text-center">
        <div class="mi-contenedor">
          <?php 
          $matricula = $this->system_controller->alumno->getMatricula();
          echo @$this->system_controller->alumno_calificaciones_controller->alumno_calificaciones->printCalificacionesToTable('tabla-datos',$matricula);
          
          if(count($this->system_controller->alumno_calificaciones_controller->alumno_calificaciones->materiasPlanFaltantes) >0) { ?>
          <h3 class="text-center">Materias sin Cursar del Plan</h3>
          <?php 
              arrayToTable2($this->system_controller->alumno_calificaciones_controller->alumno_calificaciones->materiasPlanFaltantes,"tabla-datos"); 
          }?>
        </div>
      </p>
    </div>

    <!-- adeudos -->  
    <div class="tab-pane fade in <?=$adeudos_active?>" id="adeudos">
      <h3 class="head text-center">Adeudos</h3>
      <?=$tiene_beca?'<h5 class="centrado"><small>Actualmente cuentas con beca externa</small></h5>':''?>
      <p class="narrow text-center">
        <div class="mi-contenedor">
         <?=@$this->system_controller->alumno_adeudos_controller->alumno_adeudos->printAdeudosToTable('tabla-datos') ?>
         <?= $this->system_controller->alumno_adeudos_controller->alumno_adeudos->tieneConvenio();?> 
        </div>
      </p>
    </div>

    <!-- completed -->  
    <div class="tab-pane fade in <?=$completed_active?>" id="completed">
      <h3 class="head text-center">Formato de Reinscripci&oacute;n</h3>
      <p class="narrow text-center">
        <h1 class="centrado"><?=@$this->system_controller->getMensajeCompleted()?> </h1>

<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
<!-- @@@@@@@@@@@@@@@@@@@@ DESHABILITAR BOTON @@@@@@@@@@@@@@@@@@@ -->  
<!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  

<?php 
if ($completed){
  if ($fin){
    echo "<div class=\"centrado\"><h2>Carrera terminada</h2></div>";
    echo $tsu?"<div class=\"centrado\"><small>Para Ingenieria necesitas realizar el proceso de admisi&oacute;n</small></div>":"";
  }//fin (Ya terminaron TSU)
  else
  {
 ?>
      <div class="centrado">
        <!-- MENSAJE DE MULTA -->  
        <?php if (($activar_multa) && (!$ya_pago_reinscripcion)){ ?>
        <!-- Se quita el mensaje si desde el controlador (linea 51) le ponemos a la bandera false a $activar_multa-->  
        <h3 class="rojo">Debido a que el periodo de Reinscripci&oacute;n venci&oacute;</h3> 
        <h3 class="rojo">se te generar&aacute; una <strong>MULTA</strong> al generar tu ficha</h3>
        <?php 
        }// Activar multa 
         ?>

      <?php if ($ya_pago_reinscripcion){ ?>
        <div class="centrado">
          <p class="blackened">
            <button class="btn btn-success"><h4>YA INSCRITO</h4><i class="fa fa-sticky-note-o fa-4x"></i></button>
          </p>
          <h4><small>Pago registrado: <strong><?php echo $fecha_pago_reinscripcion ?></strong></small></h4>
      <?php 
      } // INSCRITO
      else
      { // PAGAR
       ?>
        <a href="<?=base_url()?>index.php/Reinscripcion/fichaReinscripcion" target="_top">
          <button class="btn btn-primary"><h4>Generar ficha </h4><i class="fa fa-sticky-note-o fa-4x"></i></button>
        </a>                
      </div>

      <?php 
      } // COMPLETED
      ?>
    </div>

<?php  
  } // FIN (else)
} // Completed (Si no debe Documentos, Materias o $$$)
else
{ // Si no tiene todo completo
?>
     <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
     <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
     <!-- @@@ Si ya esta inscrito no importa que deba @@@ -->  
     <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  
     <!-- @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ -->  

     <?php if ($ya_pago_reinscripcion){ ?>
        <div class="centrado">
          <p class="blackened">
            <button class="btn btn-success"><h4>YA INSCRITO</h4><i class="fa fa-sticky-note-o fa-4x"></i></button>
          </p>
          <h4><small>Pago registrado: <strong><?php echo $fecha_pago_reinscripcion ?></strong></small></h4>
      <?php 
      } // INSCRITO
      else
      { // NO COMPLETO
       ?>
    <div class="centrado">
          <p class="blackened">
            <button class="btn btn-inverse"><h4>Generar ficha </h4><i class="fa fa-sticky-note-o fa-4x"></i></button>
          </p>
          <h4><small>Aun no completas los requisitos de reinscripci&oacute;n</small></h4>
    </div>
<?php
  }// Ya pago
} // Completed (else)
 ?>        
      </p>
    </div>

  <div class="clearfix"></div>
  </div><!-- tab-content -->  
</div><!-- board -->  
<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modal_alumnos">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" id="modal_detalle">
      </div>
  </div>
</div>
<script>
$(function(){
    $('a[title]').tooltip();
});

$("body").on('click','[data-toggle=modal]', function (e) {
  
  $.post($(this).attr("href"),function(resp){
    $("#modal_detalle").html(resp);
  });
});
</script>
