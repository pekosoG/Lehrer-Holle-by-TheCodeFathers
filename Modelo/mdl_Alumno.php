<?php
	class mdl_Alumno{
		public $db_driver;


		function __construct(){
			require_once("DataBase.php");
			$this->db_driver = DataBase::singleton()->db_driver;
		}

		public function alta($array_validacion){
			$query_cuenta = 'INSERT INTO cuentas VALUES(' . $array_validacion['id'][0] . ', ' . $array_validacion['codigo'][0] . ', \'' . $_POST['password'] . '\', 2);';
			var_dump($query_cuenta);

			$query_usuario = 'INSERT INTO usuario(';
			$cont =0;
			$llaves = '';
			$valores = '';
			foreach ($array_validacion as $key => $value) {
				if($value[1]){
					$llaves .= "$key";
					$valores .= "$value[0]";

					if($cont+1<sizeof($array_validacion)){
						$llaves .= ', ';
						$valores .= ', ';
					}
				}

				$cont++;
			}

			$query_usuario .= $llaves . ') VALUES(' . $valores . ');';

			var_dump($query_usuario);

			if($this->db_driver->query($query_cuenta)){
				if(!$this->db_driver->query($query_usuario)){
					$query_cuenta = "DELETE FROM cuentas WHERE id= " . $array_validacion['id'][0];
					$this->db_driver->query($query_cuenta);
					return false;
				}

				return true;
			}
			else
				return false;
		}

		public function consulta_usuario($codigo){
			return $this->db_driver->query("SELECT * FROM usuario WHERE codigo='". $codigo ."'");
		}

		public function baja($id){
			$query = "UPDATE usuario SET status=0 WHERE id=". $id;

			if(!$this->db_driver->query($query)){
				return false;
			}

			return true;
		}

		public function modificar($array_validacion){
			$query = "UPDATE usuario SET ";
			$cont=0;
			foreach ($array_validacion as $key => $value) {
				if($value[1]){
					if($cont>0)
						$query .= ", ";

					$query .= $key . "=" . $value[0];
					$cont++;
				}
			}
			$query .= " WHERE id=" . $_POST['id'];
	
			if(!$this->db_driver->query($query)){
				return false;
			}

			return true;
		}
	}
?>