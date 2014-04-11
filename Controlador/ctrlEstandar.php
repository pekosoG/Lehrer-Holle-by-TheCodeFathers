<?php
header( 'Content-Type: text/html;charset=utf-8' ); 
	class ctrlEstandar{
		public $db_driver;

		function __construct(){
			require_once("DataBase.php");
			$this->db_driver = DataBase::singleton()->db_driver;
		}

		public function isLogged(){
			if(isset($_SESSION['username'])){
				return true;
			}
			else{
				return false;
			}
		}

		public function isAdmin(){
			if(isset($_SESSION['type']) && $_SESSION['type']=='admin'){
				return true;
			}

			return false;
		}

		public function isProfessor(){
			if(isset($_SESSION['type']) && $_SESSION['type']=='professor'){
				return true;
			}

			return false;	
		}

		public function isStudent(){
			if(isset($_SESSION['type']) && $_SESSION['type']=='student'){
				return true;
			}

			return false;	
		}

		public function logout(){
			session_unset();
			session_destroy();
			setcookie(session_name(), '',time()-3600);
		}

		public function login(){

			require("Modelo/mdl_consultaLogin.php");
			$mdl = new mdl_consultaLogin();
			if($mdl->consulta_sesion()){
				$_SESSION['username'] = $mdl->username;
				$_SESSION['type'] = $mdl->type;
				$_SESSION['name'] = $mdl->name;
			}
			else{
				echo 'No coinciden los datos.';
				//vista
				$mdl->db_driver->close();
				return false;
			}
			$mdl->db_driver->close();
			return true;
		}

	}
?>