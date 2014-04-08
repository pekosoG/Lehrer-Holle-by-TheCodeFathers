<?php
	class mdl_consultaLogin{
		public $username;
		public $type;
		public $name;
		public $db_driver;

		protected $id;
		protected $tipo;

		function __construct(){
			require_once("DataBase.php");
			$this->db_driver = DataBase::singleton()->db_driver;
		}

		function consulta($username, $password){
			$query = "SELECT * FROM cuentas WHERE username='" . $username . "'";

			$resultado = $this->db_driver->query($query);

			$array_cuenta = array();
			if($array_cuenta = $resultado->fetch_array(MYSQL_ASSOC)){

				if(strcmp($array_cuenta['pass'], $password)==0){
					$this->id = $array_cuenta['id'];
					$this->tipo = $array_cuenta['tipo'];
					return true;
				}
					
			}

			return false;
		}

		public function consulta_sesion(){
			if(!self::consulta($_POST['username'], $_POST['password'])){
				return false;
			}
			else{
				$query = "SELECT * FROM usuario where id=" . $this->id;
				$resultado_usuario = $this->db_driver->query($query);
				$array_usuario = array();

				if($array_usuario = $resultado_usuario->fetch_array(MYSQL_ASSOC)){
					$this->name = $array_usuario['nombre'];
				}
				else
					return false;

				$this->username = $_POST['username'];
				switch ($this->tipo) {
					case 0:
						$this->type = 'admin';
						break;
					case 1:
						$this->type = 'professor';
						break;
					case 2: 
						$this->type = 'student';
						break;
				}

				return true;
			}
		}

		public function cambiarPassword($nueva_pass){
			$query = "UPDATE cuentas SET pass='" . $nueva_pass . "' WHERE id=" . $this->id;

			if($this->db_driver->query($query))
				return true;

			return false;
		}
	}
?>