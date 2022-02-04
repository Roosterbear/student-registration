<?php
if (! defined ( 'BASEPATH' ))
exit ( 'No direct script access allowed' );

require_once __DIR__ . "/../models/AlumnoCalificaciones.php";

class AlumnoCalificacionesController
{
	public $alumno_calificaciones;	
	
	public function __construct(){		
		$this->alumno_calificaciones = new AlumnoCalificaciones();		
	}
}