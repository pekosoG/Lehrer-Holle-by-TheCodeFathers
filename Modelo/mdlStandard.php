<?php

/**
 * Modelo estadard para la conexion con la base de datos
 * 
 * Creo que es mejor tener el modelo estandar y que los demas modelos hereden de el
 * en lugar de que la referencia a la BD esté en el controlador.
 * 
 * @author PekosoG
 */
class mdlStandard{
		
	public $db_driver;

	function __construct(){
		require_once("DataBase.php"); //este no debería estar en la carpeta de modelos?
		$this->db_driver = DataBase::singleton()->db_driver;
	}
	
}
?>