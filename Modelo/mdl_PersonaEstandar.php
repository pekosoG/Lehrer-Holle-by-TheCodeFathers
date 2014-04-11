<?php
	class mdl_PersonaEstandar{
		protected $db_driver;

		function __construct(){
			require_once("DataBase.php");
			$this->db_driver = DataBase::singleton()->db_driver;
		}

		public function alta($array_validacion, $usuario_tipo){
			
			$query_cuenta = 'INSERT INTO cuentas VALUES(' . $array_validacion['id'][0] . ', '
				 . $array_validacion['codigo'][0] . ', \'' . $_POST['password'] . '\', ';

			switch ($usuario_tipo) {
				case 'admin':
					$query_cuenta .= '0';
					break;
				
				case 'profesor':
					$query_cuenta .= '1';
					break;

				case 'alumno':
					$query_cuenta .= '2';
					break;
			}

			$query_cuenta .= ', 1);';
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

		public function baja($codigo){
			$query = 'UPDATE cuentas SET status=0 WHERE username=\'' . $codigo . '\'';
			
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

		public function consulta($codigo){

			return $this->db_driver->query('SELECT * FROM usuario WHERE codigo=\''
			 . $codigo . '\'')->fetch_array(MYSQL_ASSOC);
		}

		public function nextID($tabla){
			$query = 'SELECT id FROM ' . $tabla . ' ORDER BY id ASC';
			
			$resultado = $this->db_driver->query($query);
			$array = array();
			$cont=0;

			while($array = $resultado->fetch_array(MYSQL_ASSOC)){ 

				if($array['id']!= $cont)
					return $cont;


				$cont++;
			}

			return $cont;
		}

		public function existe($campo, $cadena){
			$query = '';
			switch ($campo) {
				case 'codigo':
					$query = 'SELECT * FROM cuentas WHERE username=\'' . $cadena . '\'';
					break;

				case 'carrera':
					$query = 'SELECT * FROM carrera WHERE id=' . $cadena;
					break;

				case 'codigo_activo':
					$query = 'SELECT * FROM cuentas WHERE username=\'' . $cadena . '\' AND status=1';
					break;
			}
			
			$resultado = $this->db_driver->query($query);
			$array = array();
			if($array = $resultado->fetch_array(MYSQL_ASSOC)){

				return true;
			}

			return false;
		}
	}
?>