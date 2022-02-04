<?php
function alerta_r($mensaje,$alerta_class,$titulo=""){
	$response ="\t<div class=\"alert alert-$alerta_class alert-dismissible fade in\" role=\"alert\">";
	$response.='<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	$response.="\n\t\t<strong>$titulo</strong> $mensaje \n\t</div>";
	
	return $response;
}

function arrayToTable2($array,$class="",$recursive = false, $return = false, $wrapper=false , $null = '&nbsp;'){
    // Sanity check
    if(empty($array) || !is_array($array)){ return false; }
    if(!isset($array[0]) || !is_array($array[0])){ $array = array($array); }
    
    // Start the table
    $table = "<table class=\"$class\">\n";
    // The header
    $table .= "\t<thead><tr>";
    // Take the keys from the first row as the headings
    
    foreach (array_keys($array[0]) as $heading) {
        if (strpos($heading, 'attr-') !== false) { continue;}
        $table .= '<th>' . $heading . '</th>';
    }unset($heading);
    $table .= "</tr></thead>\n";
    
    // The body
    $table .= "<tbody>\n";
    foreach ($array as $row) {
        $attr_tr="";
        array_walk($row,function($v,$k)use(&$attr_tr){
            if(substr( $k, 0, 8 ) == "attr-tr-"){
                $attr_tr.=" ".substr($k, 8)."=\"$v\"";
            }
        } );
        
        $table .= "\t<tr$attr_tr>" ;
        foreach ($row as $heading=>$cell) {
            if (strpos($heading, 'attr-') !== false) { continue;}
            $table .= '<td>';
            
            // Cast objects
            if (is_object($cell)) { $cell = (array) $cell; }
            if ($recursive === true && is_array($cell) && !empty($cell)) {
                // Recursive mode
                $table .= "\n" . $this->arrayTotable($cell, true, true) . "\n";
            } else {
                @$table .= (strlen($cell) > 0) ?
                $cell :
                $null;
            }
            $table .= '</td>';
        }
        $table .= "</tr>\n";
    }
    $table .= "</tbody>\n";
    // End the table
    $table .= '</table>';
    // Method of output
    if($wrapper!==false){
        $clase_wrapper=is_string($wrapper)?$wrapper:"";
        $table="<div class=\"$clase_wrapper\">\n$table\n</div>";
    }
    if ($return === false) {
        echo $table;
    } else {
        return $table;
    }
}
function arrayToParrafo($array,$return=false){
    $parrafo="<p>";
    foreach ($array as $key=>$valor){
        $parrafo.="<b>$key: </b> $valor<br/>";
    }
    $parrafo.="</p>";
    if ($return === false) { echo $parrafo; }else{ return $parrafo; }
}

?>