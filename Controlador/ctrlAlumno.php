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
				$query;

				$this->array_validacion = [
					"id" => ["",false],	
					"codigo" => ["",false],
					"nombre" => ["",false],
					"mail" => ["",false],
					"status" => ["",false],
					"celular" => ["",false],
					"github" => ["",false],
					"url" => ["",false],
					"equipo" => ["",false],
					"carrera_id" => ["",false],
				];

				foreach ($_POST as $key => $value) {
					switch ($key) {
						case 'nombre':
							$pattern = '/^[a-zA-Z].{0,49}$/';
							
							if(preg_match($pattern, $_POST['nombre'])==1){
								$this->array_validacion['nombre'][0]="'". $_POST['nombre'] ."'";
								$this->array_validacion['nombre'][1]=true;
							}
							else{
								//cargar vista
								echo 'Nombre inválido.';
								return false;
							}

						break;

						case 'mail': 
							$pattern ='/^[\w-]+(\.[\w-]+)*@[A-Za-z0-9]+(\.[A-Za-z0-9]+)*(\.[A-Za-z]{2,})$/';

							if(preg_match($pattern, $_POST['mail'])==1){
								$this->array_validacion['mail'][0]="'". $_POST['mail'] ."'";
								$this->array_validacion['mail'][1]=true;
							}
							else{
								//cargar vista
								echo 'Correo inválido.';
								return false;
							}

						break;

						case 'codigo':
							$pattern = '/^[a-zA-Z]?[0-9]{1,9}$/';

							if(preg_match($pattern, $_POST['codigo'])==1){
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
							$pattern = '/^[0-9]+$/';

							if(preg_match($pattern, $_POST['carrera_id'])==1){
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

						case 'url':
							$pattern = '/^((http:\/\/)|(www\.)|(http:\/\/www\.))[a-zA-Z0-9]{1,30}\.[a-zA-Z0-9]{1,10}$/';

							if(preg_match($pattern, $_POST['url'])==1){
								$this->array_validacion['url'][0]="'". $_POST['url'] ."'";
								$this->array_validacion['url'][1]=true;
							}
							else{
								echo 'URL inválida.';
								return false;
							}

							break;

						case 'github':
							$pattern = '/^((http:\/\/)|(www\.)|(http:\/\/www\.))github\.com\/(a-zA-Z){1,20}$/';

							if(preg_match($pattern, $_POST['github'])==1){
								$this->array_validacion['github'][0]="'". $_POST['github'] ."'";
								$this->array_validacion['github'][1]=true;
							}
							else{
								echo 'Github inválida.';
								return false;
							}

						break;

						case 'celular':
							$pattern = '/^[0-9][0-9]{10,12}/';

							if(preg_match($pattern, $_POST['celular'])==1){
								$this->array_validacion['celular'][0]="'". $_POST['celular'] ."'";
								$this->array_validacion['celular'][1]=true;
							}
							else{
								echo 'Celular inválido.';
								return false;
							}

						break;

						case 'equipo':
							$pattern = '/^[a-zA-Z].{0,30}$/';

							if(preg_match($pattern, $_POST['equipo'])==1){
								$this->array_validacion['equipo'][0]="'". $_POST['equipo'] ."'";
								$this->array_validacion['equipo'][1]=true;
							}
							else{
								echo 'Equipo inválida.';
								return false;
							}

						break;

						case 'password':
							$pattern = '/^\w{5,15}$/';

							if(preg_match($pattern, $_POST['password'])==0){
								echo 'Contraseña inválida.';
								return false;
							}

						break;
					}
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
				$this->array_validacion = [
					"nombre" => ["",false],
					"mail" => ["",false],
					"status" => ["",false],
					"celular" => ["",false],
					"github" => ["",false],
					"url" => ["",false],
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
							$pattern = '/^[a-zA-Z].{0,49}$/';
							
							if(preg_match($pattern, $_POST['nombre'])==1){
								$this->array_validacion['nombre'][0]="'". $_POST['nombre'] ."'";
								$this->array_validacion['nombre'][1]=true;
							}
							else{
								//cargar vista
								echo 'Nombre inválido.';
								return false;
							}

						break;

						case 'correo': 
							$pattern ='/^[\w-]+(\.[\w-]+)*@[A-Za-z0-9]+(\.[A-Za-z0-9]+)*(\.[A-Za-z]{2,})$/';

							if(preg_match($pattern, $_POST['correo'])==1){
								$this->array_validacion['mail'][0]="'". $_POST['correo'] ."'";
								$this->array_validacion['mail'][1]=true;
							}
							else{
								//cargar vista
								echo 'Correo inválido.';
								return false;
							}
							;

						break;

						case 'carrera':
							$pattern = '/^[A-Z]{3,5}$/';

							if(preg_match($pattern, $_POST['carrera'])==1){
								$this->array_validacion['carrera'][0]=0;
								$this->array_validacion['carrera'][1]=true;
							}
							else{
								//cargar vista
								echo 'Carrera inválido.';
								return false;
							}
							
						break;

						case 'url':
							$pattern = '/^((http:\/\/)|(www\.)|(http:\/\/www\.))[a-zA-Z0-9]{1,30}\.[a-zA-Z0-9]{1,10}$/';

							if(preg_match($pattern, $_POST['url'])==1){
								$this->array_validacion['url'][0]="'". $_POST['url'] ."'";
								$this->array_validacion['url'][1]=true;
							}
							else{
								echo 'URL inválida.';
								return false;
								//vista
							}

							break;

						case 'github':
							$pattern = '/^((http:\/\/)|(www\.)|(http:\/\/www\.))github\.com\/[a-zA-Z]{1,20}$/';

							if(preg_match($pattern, $_POST['github'])==1){
								$this->array_validacion['github'][0]="'". $_POST['github'] ."'";
								$this->array_validacion['github'][1]=true;
							}
							else{
								echo 'Github invalido';
								return false;
							}

						break;

						case 'celular':
							$pattern = '/^[0-9]{10,12}$/';

							if(preg_match($pattern, $_POST['celular'])==1){
								$this->array_validacion['celular'][0]="'". $_POST['celular'] ."'";
								$this->array_validacion['celular'][1]=true;
							}
							else{
								echo 'celular invalido';
								return false;
							}

						break;

						case 'equipo':
							$pattern = '/^[a-zA-Z].{0,29}$/';

							if(preg_match($pattern, $_POST['equipo'])==1){
								$this->array_validacion['equipo'][0]="'". $_POST['equipo'] ."'";
								$this->array_validacion['equipo'][1]=true;
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