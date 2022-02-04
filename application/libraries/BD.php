<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

global $DBSito,$DBEdist,$DBCE;
include('adodb/adodb.inc.php');

@require "/var/www/config/db-sito.php";
$DBSito=NewADOConnection($CONFIGURACION_PARAM_SITO_BD_driver);
$DBSito->SetFetchMode(ADODB_FETCH_ASSOC);
$DBSito->PConnect($CONFIGURACION_PARAM_SITO_BD_host,$CONFIGURACION_PARAM_SITO_BD_usuario,$CONFIGURACION_PARAM_SITO_BD_password,$CONFIGURACION_PARAM_SITO_BD_base);
