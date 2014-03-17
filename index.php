<?php
	/**
	 * Prueba de index para las peticiones
	 * @author PekosoG (Israel Garcia)
	 * @version 0.5 
	 */
	
	//$cont=$_GET;
	//$_GET=null;
	//$contPost=$_POST;
	$cont=$_POST;
	 if(isset($cont['ctrl'])){
		
		if(preg_match('/^[a-z]+$/',$cont['ctrl'])===0)
			die("Ctrl Rechazado!");
		
	 	$controlador=null; 
		switch($cont['ctrl']){
			case 'alumno':
				require('controlador\CtrlAlumno.php');
				$controlador = new CtrlAlumno($cont);
				break;
			/*¿El curso será creado directamente desde el Index o 
			 * se tiene que entrar como Admin/profesor para poder checarlo?*/
			case 'curso':
				require('controlador\CtrlCurso.php');
				$controlador = new CtrlCurso($cont);
				break;
			case 'profesor':
				require('controlador\CtrlProfesor.php');
				$controlador = new CtrlProfesor();
			case 'ciclo':
				require('controlador\CtrlCiclo.php');
				$controlador= new CtrlCiclo();
			default:
				//Controlador Default, un 404 quiza?
				die("404...  parametro mal!");
				break;
		}
		$controlador->ejecutar();	 	
	 }else
	 	die("Nuu-uuuhh...!");
?>