<?php
include 'Controlador/ctrlEstandar.php';
	class ctrlLogin extends ctrlEstandar{
		public function ejecutar(){
			if(isset($_GET['act'])){
				switch ($_GET['act']) {
					case 'login':
						if(!$this->isLogged()){
							if(isset($_POST['username']) && isset($_POST['password'])){
								if($this->login()){
									//header ('Location: http://localhost/index.php');
									echo 'Login exitosp';
								}
							}
							else
								echo 'No hay valores en POST user/pass';
							
						}
						else
							echo 'Ya inciiaste sesion.';

						break;

					case 'logout':
						if($this->isLogged()){
							$this->logout();
							echo 'Te has desloggeado';
						}
						else
							echo 'No puedes ahcer logout, no estas loggeado.';
						break;

					case 'password':
						if($this->isLogged()){
							if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['nuevapass'])){
								require("Modelo/mdl_consultaLogin.php");
								$mdl = new mdl_consultaLogin();
								if($mdl->consulta($_POST['username'], $_POST['password'])){
									if($mdl->cambiarPassword($_POST['nuevapass'])){
										echo 'Se cambio la pass';
									}
									else
										echo 'no se pudo cambiar.';
								}
								else
									echo 'No coinciden los datos.';
							}
							else
								echo 'Faltan valores en post';

						}
						else
							echo 'No estas loggeado';
						break;
					
					default:
						# code...
						break;
				}
			}
			else{
				echo 'No hay parámetros en GET.';
			}
		}
	}
?>