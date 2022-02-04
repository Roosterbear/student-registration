<?php
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );

require_once __DIR__ . "/../models/AlumnoAdeudos.php";

class AlumnoAdeudosController
{
	public $alumno_adeudos;
	
	public function __construct(){		
		$this->alumno_adeudos = new AlumnoAdeudos ();
	}
	
	
}