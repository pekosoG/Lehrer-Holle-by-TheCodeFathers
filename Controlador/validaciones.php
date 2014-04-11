<?php
	class validaciones{
		public $db_driver;


		function __construct(){
			require_once("DataBase.php");
			$this->db_driver = DataBase::singleton()->db_driver;
		}

		public function validar($tipo, $cadena, &$array = NULL){
			$pattern = '';
			switch ($tipo) {
				case 'nombre':
					$pattern = '/^[a-zA-Z].{0,49}$/';
					break;

				case 'mail':
					$pattern ='/^[\w-]+(\.[\w-]+)*@[A-Za-z0-9]+(\.[A-Za-z0-9]+)*(\.[A-Za-z]{2,})$/';
					break;

				case 'codigo':
					$pattern = '/^[a-zA-Z]?[0-9]{1,9}$/';
					break;

				case 'celular':
					$pattern = '/^(\([0-9]{2,2}\))?[0-9]{10,10}$/';
					break;

				case 'github':
					$pattern = '/^((http:\/\/)|(www\.)|(http:\/\/www\.))github\.com\/[a-zA-Z0-9]{1,20}$/';
					break;

				case 'sitio':
					$pattern = '/^((http:\/\/)|(www\.)|(http:\/\/www\.))[a-zA-Z0-9]{1,30}\.[a-zA-Z0-9]{1,10}$/';
					break;

				case 'equipo':
					$pattern = '/^[a-zA-Z].{0,30}$/';
					break;
				
				case 'carrera_id':
					$pattern = '/^[0-9]+$/';
					break;

				case 'password':
					$pattern = '/^\w{5,15}$/';
					break;

				case 'ciclo_id':
					$pattern = '/^[a-zA-Z0-9]{1,6}$/';
					break;

				case 'usuario_id':
					$pattern = '/^[0-9]+$/';
					break;

				case 'materia_clave':
					$pattern = '/^[a-zA-Z0-9]+$/';
					break;

				case 'nrc':
					$pattern = '/^[0-9]{5,5}$/';
					break;

				case 'porcentaje':
					$pattern = '/^0?\.[0-9]+$/';
					break;

				case 'extra':
					$pattern = '/^(0|1)$/';
					break;
			}

			if(func_num_args()==3){
				if(preg_match($pattern, $cadena)==1){
					switch ($tipo) {

						case 'nombre':
						case 'mail':
						case 'celular':
						case 'github':
						case 'sitio':
						case 'equipo':
							$array[$tipo][0] = '\'' . $cadena . '\'';
							$array[$tipo][1] = true;
							break;

						default:
							# code...
							break;
					}

					return true;
				}
			}
			else{
				if(func_num_args()==2){
					if(preg_match($pattern, $cadena)==1){
						return true;
					}
				}
			}

			return false;
		}
	}
?>