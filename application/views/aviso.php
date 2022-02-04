
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h4 class="modal-title" id="plan_de_estudios">Imprimir Ficha</h4>
      </div>
      <div class="modal-body">
        
        <div class="col-12-lg centrado">
          <h3><span class="amarillo"><i class="fa fa-exclamation-circle fa-lg"></i></span> Al aceptar la materia reprobatoria, se te generar&aacute; un cargo en cajas.</h3>
          <h4><small>( Tu ficha tambi&eacute;n la podr&aacute;s volver a imprimir en la secci&oacute;n de adeudos )          
          </small></h4>
            <a href="<?=base_url()?>index.php/Reinscripcion/generarAdeudo/<?=$n?>">
              <button data-aceptar class="btn btn-warning">Aceptar</button>
            </a>
          <span class="espaciado"></span> 
          <a href="#">
            <button type="button" class="btn btn-success" data-dismiss="modal">Cancelar</button>
          </a>
          <p><cite class="very-small">
            
          </p></cite>
        </div>

      </div>
      <div class="modal-footer">
      </div>
      <script type="text/javascript">
            // Para evitar que se ejecute mas de una vez
            $(document).on("click","[data-aceptar]", function(e){
                e.stopImmediatePropagation();
                $(this).attr("disabled",true);
            });
      </script>
