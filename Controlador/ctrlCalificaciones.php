<?php
include 'Controlador/ctrlEstandar.php';
	class ctrlCalificaciones extends ctrlEstandar{
		private $mdl;
		private $calificaciones;

		public function ejecutar(){
			require("Modelo/mdl_Calificaciones.php");
			$this->mdl = new mdl_Calificaciones();
			if(isset($_GET['act'])){
				switch ($_GET['act']) {
					case 'consulta':
						if($this->isLogged()){
							if($this->isProfessor()){
								self::datos_Consulta();
								if(!empty($this->calificaciones)){
									//vista profe
									var_dump($this->calificaciones);
								}
							}
							else{
								if($this->isStudent()){
									self::datos_Consulta();
									if(!empty($this->calificaciones)){
										//vista alumno
										var_dump($this->calificaciones);
									}
								}
							}
						}
						else{
							echo 'no estas loggeado';
						}
						break;

					case 'alta':
						if($this->isLogged()){
							if($this->isProfessor()){
								if(isset($_GET['tipo'])){
									switch ($_GET['tipo']) {
										case 'alumno':
											if(isset($_POST)){
												if(self::validar_datos()){
													if($this->mdl->alta_Alumno($this->calificaciones)){

													}
												}
											}
											break;

										case 'curso':
											if(isset($_POST)){
												if(self::validar_Curso()){
													if($this->mdl->alta_Curso($this->calificaciones)){

													}
												}
											}
											break;

										case 'rubro':
											if(isset($_POST)){
												if(self::validar_datos()){
													if($this->mdl->alta_Alumno($this->calificaciones)){

													}
												}
											}
											break;
										
										default:
											# code...
										break;
									}
								}
								else{
									echo 'faltan parametros';
								}

							}
							else{
								echo 'solo prof';
							}
						}
						else{
							echo ' no estas loggeado';
						}
						break;

					case 'modificar':
						if($this->isLogged()){
							if($this->isProfessor() && isset($_GET['tipo'])){
								switch ($_GET['tipo']) {
										case 'alumno':
											if(isset($_POST)){
												if(self::validar_datos()){
													if($this->mdl->modificar_Alumno($this->calificaciones)){

													}
												}
											}
											break;

										case 'curso':
											if(isset($_POST)){
												if(self::validar_Curso()){
													if($this->mdl->modificar_Curso($this->calificaciones)){

													}
												}
											}
											break;

										case 'rubro':
											if(isset($_POST)){
												if(self::validar_datos()){
													if($this->mdl->modificar_Alumno($this->calificaciones)){

													}
												}
											}
											break;
										
										default:
											# code...
										break;
									}

							}
							else{
								echo 'solo prof';
							}
						}
						else{
							echo ' no estas loggeado';
						}
						break;

					
					default:
						# code...
						break;
				}
			}
		}

		public function datos_Consulta(){
			if(isset($_POST['alumno_id'])){
				$query = 'SELECT * FROM calificacion_curso WHERE id=' . $_POST['alumno_id'];

				$resultado = $this->mdl->db_driver->query($query);
				$this->calificaciones = array();
				$temp= array();
				while($temp = $resultado->fetch_array(MYSQL_ASSOC)){
					array_push($this->calificaciones, $temp);
				}
			}
		}

		public function validar_datos(){
			if(isset($_POST['calificacion']) && isset($_POST['alumno_id'])
				&& isset($_POST['curso_id'])){
				$pattern;
				$ban_rubro = 0;
				if(strcmp($_GET['tipo'], 'rubro')==0){
					if(isset($_POST['rubro_id'])){
						$this->calificaciones = [
							"calificacion" => "",
							"usuario_id" => "",
							"curso_id" => "",
							"rubro_id" => "",
							"rubro_extra_id" => "NULL",
						];
						$ban_rubro = 1;
					}
					else{
						echo 'faltan aprametros';
						return false;
					}
					
				}
				else{
					$this->calificaciones = [
						"calificacion" => "",
						"usuario_id" => "",
						"curso_id" => "",
					];
				}

				foreach ($_POST as $key => $value) {
					switch ($key) {
						case 'calificacion':
							$pattern = '/^((100)|([0-9]{1,2}))$/';

							if(preg_match($pattern, $value)==1){
								$this->calificaciones['calificacion'] = $value;
							}
							else{
								echo 'calificacion invalida';
								return false;
							}

							break;

						case 'curso_id':
							$pattern = '/^[0-9]+$/';

							if(preg_match($pattern, $value)==1){
								$query = 'SELECT nrc FROM curso WHERE id=' . $value;

								if($this->mdl->db_driver->query($query)){
									$this->calificaciones['curso_id'] = $value;
								}
								else{
									echo 'no existe el curso';
									return false;
								}
							}
							else{
								echo 'curso invalido';
								return false;
							}

							break;

						case 'alumno_id':
							$pattern = '/^[0-9]+$/';

							if(preg_match($pattern, $value)==1){
								$query = 'SELECT * FROM cuentas WHERE id=' . $value;

								if($this->mdl->db_driver->query($query)){
									$this->calificaciones['usuario_id'] = $value;
								}
								else{
									echo 'no existe el alumno';
									return false;
								}
							}
							else{
								echo 'alumno_id invalido';
								return false;
							}

							break;
					}
				}

				if($ban_rubro==1){
					$pattern = '/^[0-9]+$/';

					if(preg_match($pattern, $_POST['rubro_id'])==1){
						$query = 'SELECT curso_id FROM rubro WHERE id=' . $_POST['rubro_id'];

						$resultado = $this->mdl->db_driver->query($query);
						$temp = array();

						if($temp = $resultado->fetch_array(MYSQL_ASSOC)){
							if($temp['curso_id']==$this->calificaciones['curso_id']){
								$this->calificaciones['rubro_id'] = $_POST['rubro_id'];
							}
							else{
								echo 'rubro y curso no coinciden';
								return false;
							}
						}
						else{
							echo 'el rubro noe xiste';
							return false;
						}
					}
					else{
						echo 'alumno_id invalido';
						return false;
					}
				}

				return true;
			}
		}

		public function validar_Curso(){
			if(isset($_POST['calif_curso']) && isset($_POST['curso_id'])){
				$pattern = '/^([0-9]+-((100)|([0-9]{1,2})),)+$/';
				$this->calificaciones = array();

				if(preg_match($pattern, $_POST['calif_curso'])){
					$this->calificaciones = explode(',', $_POST['calif_curso']);
					array_push($this->calificaciones, $_POST['curso_id']);
				}
				else{
					echo 'formato invalido';
					return false;
				}

				return true;
			}
		}
	}
?>