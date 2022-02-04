<?php
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );

require_once __DIR__ . "/../models/AlumnoDocumentos.php";

class AlumnoDocumentosController
{
	public $alumno_documentos;

	public function __construct(){
		$this->alumno_documentos = new AlumnoDocumentos();
	}
}