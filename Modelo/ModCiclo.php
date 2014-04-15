<?php
/**
 * Modelo encargado de los ciclos escolares.
 */
class ModCiclo extends mdlStandard{
	//Magia... MAGIA!
	
	/**
	 * Funcion encargada de agregar los ciclos nuevos
	 * @param $nombre del ciclo (2014A)
	 * @param $inicio, fecha inicio del ciclo
	 * @param $final, fecha final del ciclo
	 * @return TRUE o FALSE, dependiendo si se ejecuta bien el Query
	 */
	function insertaCiclo($nombre,$inicio,$final){
		if(!$this->db_driver->query("insert into ciclo values('$nombre',$inicio,$final)"))
			return false;
		else 
			return true;
	}
	
	/**
	 * Funcion encargada de regresar los datos de un ciclo especifico
	 * @param $nombre, nombre del ciclo (2014A)
	 * @return mixto, array con el resultado o FALSE si no se pudo
	 */
	function consultaCiclo($nombre){
		$result = $this->db_driver->query("select inicio, final from ciclo where id='$nombre'");
		if($result===false)
			return false;
		else
			return $result->fetch_array(MYSQL_ASSOC);
	}
}
?>