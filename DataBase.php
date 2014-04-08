<?php
	class DataBase{
		private static $instancia;
		public $db_driver;


		private function __construct(){
			require("Modelo/datos.inc");
			$this->db_driver = new mysqli($server,$user,$pass,$db);
			if($this->db_driver->connect_errno){
				echo 'Error en la conexion a la DB.<br/>';
				return false;
			}	
		}

		public static function singleton(){
			if(!isset(self::$instancia)){
				self::$instancia = new DataBase();
			}

			return self::$instancia;
		}
	}
?>