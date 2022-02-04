<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );

require_once __DIR__ . "/../libraries/SystemController.php";

class Login extends CI_Controller
{
	public function index(){
		$email = $_post['the_code'];
		
		if (empty($email)){
			header("location: /login/error");
		}else{
			header("location: /login/configuracion");
		}
	}
	
	public function configuration(){
		echo 'configuration';
	}
	
	public function error(){
		echo 'error';
	}
}