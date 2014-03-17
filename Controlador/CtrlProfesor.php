<?php
require_once("controladores/CtrlInterfaz.php");
/**
 * Clase controladora de las acciones que 
 * el profesor puede hacer
 * 
 * @author PekosoG
 * @version 0.5
 */
class CtrlProfesor implements CtrlInterfaz{ //funcionará asi como en Java o es diferente el bussines?
	private $modelo;
	private $cont;
	/**
	 * constructor del controlador de Profesor
	 * @version 1.0
	 */
	function __construct(){
		require("modelos\ModProfesor.php");
		$this->modelo= new ModProfesor();
		$this->cont=func_get_arg(0);
	}
		
	function ejecutar(){
		if(preg_match('/^[a-z]+$/',$this->cont['act'])===0)
				die("Act Rechazado!");
		switch ($this->cont['act']) {
			case 'cursonuevo':
				$this->nuevoCurso();			
				break;
			case 'clonarcurso':
				$this->clonarCurso();
				break;
			case 'reglasev':
				$this->generaReglasEV();
				break;
			case 'altaalumnos':
				$this->altaAlumnos();
				break;
			case 'capturaevaluacion':
				$this->capturaEvaluacion();
				break;
			default:
				
				break;
		}
	}
	
	function nuevoCurso(){
		//Supongo que aqui tiene que mostrarse la vista para que se pueda generar el curso		
	}
	
	function clonarCurso(){
		//Aqui tambien se muestra la vista... no?
	}
	
	function generaReglasEV(){
		//Aqui tambien....
	}
	
	function altaAlumnos(){
		//lo mismo aqui...
	}
	function capturaEvaluacion(){
		//lo unico que cambia seria saber que curso es el que quiere, no? porqe tambien muestra la vista
		//aqui mismo, la vista te deberia dejar capturar las asistencias...
	}
}
?>