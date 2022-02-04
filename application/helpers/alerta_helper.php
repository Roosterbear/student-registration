<?php
header('Content-Type: text/html; charset=UTF-8');
function alerta($mensaje,$alerta_class,$titulo="" ,$return=false){
	$html="<div class=\"alert alert-$alerta_class alert-dismissible fade show\" role=\"alert\">";
	$html.="  <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>";
	$html.="  <strong>$titulo</strong> $mensaje \n\t</div>";
	if($return){
		return $html;
	}else{
		echo $html;
	} 
}
function bs_button($class,$contenido,$id=NULL,$data=array(), $return=false){
    $data_attrib="";
    if(count($data)>0){
        foreach ($data as $name=>$val){
            $data_attrib.=" data-$name=\"$val\" ";
        }
    }
    $html= "<button type=\"button\" class=\"btn $class\" $data_attrib>$contenido</button>";
    if($return){
        return $html;
    }else{
        echo $html;
    } 
}
function alerta_modal($id_modal=".modal",$mensaje,$alerta_class,$titulo=""){?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"><?=$titulo?></h4>
</div>
<div class="modal-body">
<br>
<a class="btn btn-sm close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times"></i></a>	
	<div class="alert alert-<?=$alerta_class?>" role="alert">	
		<?=$mensaje?>
	</div>	
</div>
<script type="text/javascript">
    $("<?=$id?>.modal-dialog").removeClass("modal-sm");
    $("<?=$id?>.modal-dialog").addClass("modal-lg");
    $("<?=$id?>.modal").modal("show");    
</script>
<?php } 

function redirigir($controlador="",$funcion="",$vista="",$objetivo=""){
    $obj =& get_instance();
    $datos['ruta'] = "{$controlador}/{$funcion}";
    $datos['objetivo'] = $objetivo;
    $obj->load->view($vista,$datos);
}

function abrirModal($idModal=".modal",$class="",$esCerrable=true){?>
    <script type="text/javascript">
    	<?php if(!$esCerrable){ ?>
    	$("<?=$idModal?>").modal({
      		 show:false,
      		 backdrop: 'static',
      		 keyboard: false
      	});
    	<?php } ?>
        $("<?=$idModal?> .modal-dialog").removeClass("modal-sm modal-lg");
        $("<?=$idModal?> .modal-dialog").addClass("<?=$class?>");
        $("<?=$idModal?>").modal("show");
	</script>
<?php }

function alertaAccesoDenegado($jumboText,$jumboBtnText="",$jumboBtn=false,$jumboBtnHref="",$jumboBtnAttr=""){
?>
	<div class="jumbotron">
        <h1><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Ooopsss...</h1>
        <p><?=$jumboText?></p>
        <?php if($jumboBtn){ ?>
        <p><a href="<?=$jumboBtnHref?>" class="btn btn-primary btn-lg" <?=$jumboBtnAttr?>><?=$jumboBtnText?></a></p>
        <?php } ?>
	</div>
	<script type="text/javascript">
        $(".modal .modal-dialog").removeClass("modal-sm");
        $(".modal .modal-dialog").addClass("modal-lg");
    </script>
<?php }

function alertaContinuarDenegado($jumboText,$jumboBtnText="",$jumboBtn=false,$jumboBtnHref="",$jumboBtnAttr=""){
	?>
	<div class="jumbotron">
        <h2><i class="fa fa-exclamation-circle" aria-hidden="true"></i> Alto...</h2>
        <p><?=$jumboText?></p>
        <?php if($jumboBtn){ ?>
        <p><a href="<?=$jumboBtnHref?>" class="btn btn-primary btn-lg" <?=$jumboBtnAttr?>><?=$jumboBtnText?></a></p>
        <?php } ?>
	</div>
	<script type="text/javascript">
        $(".modal .modal-dialog").removeClass("modal-sm");
        $(".modal .modal-dialog").addClass("modal-lg");

        $(document).ajaxStart(function(){
      	     $(".btn-primary").attr("disabled",true);
      	 });
    </script>
<?php }

function sesion($texto="",$ruta){ ?>
	<h1><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Ooopsss...</h1>
    <p><?=$texto?></p>
    <p><a href="<?=$ruta?>" class="btn btn-primary btn-sm">Iniciar Sesión</a></p>
<?php }

function alertaAccesoDenegadoHTML($importCSS=false,$jumboText,$jumboBtn=false,$jumboBtnText="",$jumboBtnHref=""){
    ?>
    <?php if($importCSS){ ?>
    <link href="<?=base_url()?>application/assets/bootstrap4/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="<?=base_url()?>application/assets/fontAwesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <script src="<?=base_url()?>application/assets/jQuery/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="<?=base_url()?>application/assets/bootstrap4/js/bootstrap.min.js"></script>
    <div class="container-fluid"><br>
    <?php } ?>
    <div class="jumbotron">
    	<h1 class="display-3"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Ooopsss...</h1>
        <p class="lead"><?=$jumboText?></p>
        <hr class="my-4">
        <?php if($jumboBtn){ ?>
        <p class="lead">
        	<a class="btn btn-primary btn-lg" href="<?=$jumboBtnHref?>" role="button"><?=$jumboBtnText?></a>
        </p>
        <?php } ?>
    </div>
	<?php if($importCSS){ ?>
	</div>
	<?php } ?>
<?php }

function modal($contenido,$idModal="#modalDinamico", $class="",$esCerrable=true){?>
	<div id="modal_contenido_temporal" style="display: none" ><?=$contenido?></div>
    <script type="text/javascript">
    	<?php if(!$esCerrable){ ?>
    	$("<?=$idModal?>").modal({
      		 show:false,
      		 backdrop: 'static',
      		 keyboard: false
      	});
    	<?php } ?>
    	$("<?=$idModal?> .modal-content").html(function(){
    		var html=$("#modal_contenido_temporal").html();
    		$("#modal_contenido_temporal").remove();
    		return html;
        });
    	
        $("<?=$idModal?> .modal-dialog").removeClass("modal-sm modal-lg");
        $("<?=$idModal?> .modal-dialog").addClass("<?=$class?>");
        $("<?=$idModal?>").modal("show");
	</script><?php 
}


function send_exception(Exception $e){
    set_status_header(500);
    echo $e->getMessage();
    //echo $e->getPrevious();
}
function alerta_exception(Exception $e, $alerta_class="danger"){
	echo alerta($e->getMessage(), $alerta_class) ;
	//echo $e->getPrevious();
}
function send_exception_free($msg=""){
    set_status_header(500);
    echo $msg;
    //echo $e->getPrevious();
}
function utf8ize($d){
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = $this->utf8ize($v);
        }
    } else if (is_string ($d)) {
        return utf8_encode($d);
    }
    return $d;
}
?>