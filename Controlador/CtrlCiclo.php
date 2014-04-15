<?php
/**
 * Clase encargada de administrar los datos correspondientes
 * al ciclo escolar
 * 
 * @author PekosoG
 * @version 0.5
 */
class CtrlCiclo extends ctrlEstandar{
	private $modelo;
	private $cont;
	
	function __construct(){
		parent::__construct();
		require('Modelo\ModCiclo.php');
		$this->modelo= new ModCiclo();
		$this->cont=func_get_arg(0);
	}
	
	function ejecutar(){
		if(preg_match('/^[a-z]+$/',$this->cont['act'])===0)
				die("Act Rechazado!");
		switch ($this->cont['act']) {
			case 'alta':
				$this->nuevoCiclo();
				break;
			case 'consulta':
				break;
			default:
				
				break;
		}
	}
	/**
	 * Este metodo se encarga de dar de alta un nuevo ciclo
	 * 
	 */
	function nuevoCiclo(){
		/*Aqui deberá de mostrar la vista donde seleccina la fecha de
		 * inicio y la fecha final... junto con los datos de los dias 
		 * festivos...  supongo.
		 */
		 if(parent::isProfessor()){
		 	if(isset($this->cont['nomCiclo']))
				if(isset($this->cont['fIni']))
					if(isset($this->cont['fFin'])){
						$inicio=new DateTime($this->cont['fIni']);
						$fin=new DateTime($this->cont['fFin']);
						
						if($fin>$inicio){
							$res=$this->modelo->insertaCiclo($array);
							
							var_dump($res);
						}
					}						
		 }
	}
	
	function consultaCiclo(){
		if(parent::isLogged()){
			
		}
	}
}
?>