<?php
include 'Controlador/ctrlEstandar.php';
	class ctrlAlumno extends ctrlEstandar{
		private $mdl;
		private $array_validacion;

		public function ejecutar(){
			require("Modelo/mdl_Alumno.php");
			$this->mdl = new mdl_Alumno();
			if(!empty($_GET['act'])){
				switch ($_GET['act']){
					case 'alta':

						if(self::validacion_alta()){
							if($this->mdl->alta($this->array_validacion)){
								echo 'Usuario agregado.';
								//cargar vista
							}
							else{
								echo 'No se pudo agregar usuario.';
								//cargar vista
							}
						}
						break;

					case 'baja':
						if($this->isLogged()){
							if($this->isAdmin()){
								if(self::validacion_baja()){
									echo 'Se dio de baja el usuario.';
									//cargar vista
								}
							}
							else{
								echo 'Permiso denegado.';
							}
						}
						else{
							echo 'No estas loggeado.';
						}
						break;

					case 'consulta':
						if($this->isLogged()){
							if(self::consulta()){
								echo 'Consulta exitosa';
								if($this->isProfessor()){
									//vista professor
								}
								elseif ($this->isStudent()) {
									//vista alumno
								}
							}
						}
						else{
							echo 'No estas loggeado.';
						}
						break;
					
					case 'modificar':
						if($this->isLogged()){
								if(self::validar_modificacion()){
								if($this->mdl->modificar($this->array_validacion)){
									echo 'Se actualizo.';
									//vista
								}
								else{
									echo 'No se pudo modificar';
								}
								//cargar vista
							}
						}
						else{
							echo 'No te has loggeado.';
						}
						break;
					default:
						//Cargar 404
						echo 'Acción no válida. <EMPTY $_GET';
						break;
				}
			}
			else
			{
				//Cargar vista
				echo "No hay nada en el parámetro GET['act']";
			}
		}

		public function validacion_alta(){
			if(!empty($_POST)){
				require("Controlador/validaciones.php");
				$validacion = new validaciones();
				$query;

				$this->array_validacion = [
					"id" => ["",false],	
					"codigo" => ["",false],
					"nombre" => ["",false],
					"mail" => ["",false],
					"status" => ["",false],
					"celular" => ["",false],
					"github" => ["",false],
					"sitio" => ["",false],
					"equipo" => ["",false],
					"carrera_id" => ["",false],
				];

				foreach ($_POST as $key => $value) {
					switch ($key) {
						case 'nombre':
							if(!$validacion->validar('nombre',$_POST['nombre'],$this->array_validacion)){
								//vista
								echo 'Nombre invalido.';
								return false;
							}

						break;

						case 'mail': 
							if(!$validacion->validar('mail',$_POST['mail'],$this->array_validacion)){
								//cargar vista
								echo 'Correo inválido.';
								return false;
							}

						break;

						case 'codigo':
							if($validacion->validar('codigo',$_POST['codigo'])){
								$resultado = $this->mdl->consulta_usuario($_POST['codigo']);
								$array_usuario = array();
								if(!$array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
									$this->array_validacion['codigo'][0]="'". $_POST['codigo'] ."'";
									$this->array_validacion['codigo'][1]=true;
								}
								else{
									echo 'Código en uso.';
									//Cargar vista
									return false;
								}
							}	
							else{
								//cargar vista
								echo 'Código inválido.';
								return false;
							}
							

						break;

						case 'carrera_id':

							if($validacion->validar('carrera_id',$_POST['carrera_id'])){
								$query = "SELECT * FROM carrera WHERE id=" . $_POST['carrera_id'];
								$resultado = $this->mdl->db_driver->query($query);
								$array_carrera = array();
								if($array_carrera = $resultado->fetch_array(MYSQL_ASSOC)){
									$this->array_validacion['carrera_id'][0]=$_POST['carrera_id'];
									$this->array_validacion['carrera_id'][1]=true;	
								}
								else{
									echo 'No existe la carrera';
									return false;
								}
							}
							else{
								//cargar vista
								echo 'Carrera inválido.';
								return false;
							}
							
						break;

						case 'sitio':
							if(!$validacion->validar('sitio',$_POST['sitio'],$this->array_validacion)){
								echo 'URL inválida.';
								return false;
							}

							break;

						case 'github':
							if(!$validacion->validar('github',$_POST['github'],$this->array_validacion)){
								echo 'Github inválida.';
								return false;
							}

						break;

						case 'celular':
							if(!$validacion->validar('celular',$_POST['celular'],$this->array_validacion)){
								echo 'Celular inválido.';
								return false;
							}

						break;

						case 'equipo':
							if(!$validacion->validar('equipo',$_POST['equipo'],$this->array_validacion)){
								echo 'Equipo inválida.';
								return false;
							}

						break;

						case 'password':
							if(!$validacion->validar('password',$_POST['password'])){
								echo 'Contraseña inválida.';
								return false;
							}

						break;
					}
				}

				if(!isset($_POST['password'])){
					echo 'falta la password';
					return false;
				}

				$this->array_validacion['status'][0]=1;
				$this->array_validacion['status'][1]=true;

				$query = "SELECT * FROM usuario ORDER BY 'id' ASC";
				$resultado = $this->mdl->db_driver->query($query);
				$array_usuario = array();
				if(!$array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
					$this->array_validacion['id'][0]=0;
					$this->array_validacion['id'][1]=true;
				}
				else{
					$cont =0;
					while($array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){ $cont++; }

					$this->array_validacion['id'][0]=$cont+1;
					$this->array_validacion['id'][1]=true;
				}
				
				if($this->array_validacion['codigo'][1] && $this->array_validacion['nombre'][1] &&
					$this->array_validacion['mail'][1] && $this->array_validacion['carrera_id'][1]) {
					return true;
				}
				else{
					echo 'Faltan datos necesarios.';
				}
			}
			else{
				//Cargar vista
				echo 'No hay valores en POST.';
			}

			return false;
		}

		public function validacion_baja(){
				if(!empty($_POST)){
					if(isset($_POST['codigo'])){
						$resultado = $this->mdl->consulta_usuario($_POST['codigo']);
						$array_usuario = array();

						if($array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
							if($this->mdl->baja($array_usuario['id'])){
								return true;
							}
							else{
								echo 'No se pudo eliminar.';
								//vista
							}
						}
						else{
							echo 'No existe el código.';
							//cargar vista
						}
					}
					else{
						echo 'No hay nada en post-codigo';
					}
				}
				else{
					echo 'No hay valores en POST.';
					//cargar vista
				}

				return false;
		}

		public function consulta(){
			if(!empty($_POST)){
				if(isset($_POST['codigo'])){
					$resultado = $this->mdl->consulta_usuario($_POST['codigo']);
					$array_usuario = array();
					if(!$array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
						echo 'No se encontro el usuario.';						
					}
					else{
						var_dump($array_usuario);
						return true;
					}
				}
			}
			else{
				echo 'No hay valores en POST.';
			}

			return false;
		}

		public function validar_modificacion(){
			if(!empty($_POST)){
				require("Controlador/validaciones.php");
				$validacion = new validaciones();
				
				$this->array_validacion = [
					"nombre" => ["",false],
					"mail" => ["",false],
					"celular" => ["",false],
					"github" => ["",false],
					"sitio" => ["",false],
					"equipo" => ["",false],
					"carrera" => ["",false],
				];
				
				if(isset($_POST['codigo']))
					$resultado = $this->mdl->consulta_usuario($_POST['codigo']);
				else{
					echo 'Faltan valores';
					return false;
				}

				$array_usuario = array();
				if($array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
					if(isset($_POST['id'])){
						if($_POST['id']!=$array_usuario['id']){
							echo 'No concuerdan los valores enviados con los de la BD.';
							//vista
							return false;
						}
					}
					else{
						echo 'No se envio ID.';
						//vista
						return false;
					}
				}
				else{
					echo 'No se encontraron resultados.';
					//vista
					return false;
				}
				
				foreach ($_POST as $key => $value){
					switch ($key) {
						case 'nombre':
							if(!$validacion->validar('nombre',$_POST['nombre'],$this->array_validacion)){
								echo 'nombre invalido';
								return false;
							}
						break;

						case 'mail': 
							if(!$validacion->validar('mail',$_POST['mail'],$this->array_validacion)){
								//cargar vista
								echo 'Correo inválido.';
								return false;
							}

						break;

						case 'carrera_id':
							$pattern = '/^[A-Z]{3,5}$/';

							if(!$validacion->validar('carrera_id',$_POST['carrera_id'])){
								//cargar vista
								echo 'Carrera inválido.';
								return false;
							}
							
						break;

						case 'sitio':
							if(!$validacion->validar('sitio',$_POST['sitio'],$this->array_validacion)){
								echo 'URL inválida.';
								return false;
								//vista
							}

							break;

						case 'github':
							if(!$validacion->validar('github',$_POST['github'],$this->array_validacion)){
								echo 'Github invalido';
								return false;
							}

						break;

						case 'celular':
							if(!$validacion->validar('celular',$_POST['celular'],$this->array_validacion)){
								echo 'celular invalido';
								return false;
							}

						break;

						case 'equipo':
							if(!$validacion->validar('equipo',$_POST['equipo'],$this->array_validacion)){
								echo 'equipo invalido';
								return false;
							}

						break;
					}
				}
				
				return true;
			}
			else{
				echo 'No hay valores en POST.';
			}

			return false;
		}
	}
?>