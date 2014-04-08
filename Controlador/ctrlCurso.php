<?php
include 'Controlador/ctrlEstandar.php';
	class ctrlCurso extends ctrlEstandar{
		private $mdl;
		private $array_validacion;
		private $array_clasesdias;
		private $array_rubros;

		public function ejecutar(){
			require("Modelo/mdl_Curso.php");
			$this->mdl = new mdl_Curso();
			if(isset($_GET['act'])){
				switch ($_GET['act']) {
					case 'alta':
						if($this->isLogged()){
							if($this->isProfessor()){

								if(!empty($_POST)){

									if(self::validar_alta()){
										//var_dump($this->array_validacion);
										if($this->mdl->alta($this->array_validacion, $this->array_clasesdias)){

										}	
									}
								}
								else{
									//formulario
									echo 'formulario';
								}

							}
							else
								echo 'No tienes permiso. Sólo profesores';
						}
						else
							echo 'No has iniciado sesion.';
						break;
					case 'matricular':
						if($this->isLogged()){

							if($this->isStudent()){
								if(self::validar_matricular()){
									//Cargar vista
								}
							}
							else{
								echo 'No tienes permiso de amtricula. Estudiantes';
							}
						}
						else
							echo 'No estás loggeado.';

						
						break;

					case 'desmatricular':
						if($this->isLogged()){
							if($this->isStudent()){
								if(self::validar_desmatricular()){
									//Cargar vista
									echo 'Se desmatriculo.';
								}
							}
						}
						else
							echo 'No estas loggeado.';
						break;

					case 'consulta':
						if(self::consultar_datos_curso()){

						}
						break;
					
					default:

						break;
				}
			}
		}

		public function validar_alta(){
			$pattern;
			$query;

			$this->array_rubros = array();
	
			$this->array_validacion = [
				"id" => ["",false],
				"nrc" => ["",false],	
				"nombre" => ["",false],
				"materia_clave" => ["",false],
				"ciclo_id" => ["",false],
				"usuario_id" => ["",false],
			];

			$this->array_clasesdias = [
				"lunes" => ["",false],
				"martes" => ["",false],
				"miercoles" => ["",false],
				"jueves" => ["",false],
				"viernes" => ["",false],
				"sabado" => ["",false],
			];

			$ban_dias=0;

			foreach ($_POST as $key => $value) {
				switch ($key) {
					case 'nombre':
						$pattern = '/^[a-zA-Z].{0,49}$/';

						if(preg_match($pattern, $_POST['nombre'])==1){
							$this->array_validacion['nombre'][0]="'" . $_POST['nombre'] . "'";
							$this->array_validacion['nombre'][1]=true;
						}
						else{
							echo 'Nombre inválido.';
							return false;
						}

						break;

					case 'nrc':
						$pattern = '/^[0-9]{5,5}$/';

						if(preg_match($pattern, $_POST['nrc'])==1){
							$query = 'SELECT * FROM curso WHERE nrc='. $_POST['nrc'];
							$resultado = $this->mdl->db_driver->query($query);
							$array_usuario = array();
							if(!$array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
								$this->array_validacion['nrc'][0] = '\'' . $_POST['nrc'] . '\'';
								$this->array_validacion['nrc'][1] =true;

								$query = 'SELECT * FROM curso ORDER BY id ASC';
								$resultado = $this->mdl->db_driver->query($query);
								$array_temp = array();
								$cont=0;
								if(!$array_temp = $resultado->fetch_array(MYSQL_ASSOC)){
									$this->array_validacion['id'][0] = $cont;
									$this->array_validacion['id'][1] = true;
								}
								else{
									while($array_temp = $resultado->fetch_array(MYSQL_ASSOC)){ $cont++; }
									$this->array_validacion['id'][0] = $cont+1;
									$this->array_validacion['id'][1] = true;
								}
							}
							else{
								echo 'NRC ocupado.';
								return false;
							}
						}
						else{
							echo 'NRC inválido.';
							return false;
						}

						break;

					case 'materia_clave':
						$pattern = '/^[a-zA-Z0-9]+$/';

						if(preg_match($pattern, $_POST['materia_clave'])==1){
							$query = "SELECT * FROM materia WHERE clave='". $_POST['materia_clave'] . '\'';
							$resultado = $this->mdl->db_driver->query($query);
							$array_usuario = array();
							if(!$array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
								echo 'La materia no existe.';
								return false;
							}
							else{
								$this->array_validacion['materia_clave'][0] = '\'' . $_POST['materia_clave'] . '\'';
								$this->array_validacion['materia_clave'][1] = true;
							}
						}
						else{
							echo 'Clave de materia inválido.';
							return false;
						}

						break;
						
					case 'ciclo_id':
						$pattern = '/^[a-zA-Z0-9]{1,6}$/';

						if(preg_match($pattern, $_POST['ciclo_id'])==1){
							$query = 'SELECT * FROM ciclo WHERE id=\''. $_POST['ciclo_id'] . '\'';
							$resultado = $this->mdl->db_driver->query($query);
							$array_usuario = array();
							if(!$array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
								echo 'EL ciclo no existe.';
								return false;
							}
							else{
								$this->array_validacion['ciclo_id'][0] = '\'' . $_POST['ciclo_id'] . '\'';
								$this->array_validacion['ciclo_id'][1] = true;
							}
						}
						else{
							echo 'Ciclo id inválido.';
							return false;
						}

						break;

					case 'usuario_id':
						$pattern = '/^[0-9]+$/';

						if(preg_match($pattern, $_POST['usuario_id'])==1){
							$query = "SELECT * FROM cuentas WHERE id=". $_POST['usuario_id'];
							$resultado = $this->mdl->db_driver->query($query);
							$array_usuario = array();
							if(!$array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
								echo 'ID no existe.';
								//vista
								return false;
							}
							else{
								if($array_usuario['tipo']==1){
									$this->array_validacion['usuario_id'][0]=$_POST['usuario_id'];
									$this->array_validacion['usuario_id'][1]=true;
								}
								else{
									echo 'Este usuario no es profesor';
									return false;
								}
							}
						}
						else{
							echo 'Usuario id inválido.';
							return false;
						}

						break;

					case 'lunes':
					case 'martes':
					case 'miercoles':
					case 'jueves':
					case 'viernes':
					case 'sabado':
						$pattern = '/^[0-9]{4,4}-[0-9]{4,4}$/';

						if(preg_match($pattern, $_POST[$key])==1){
							$this->array_clasesdias[$key][0]=$_POST[$key];
							$this->array_clasesdias[$key][1]=true;
							$ban_dias=1;
						}
						else{
							echo 'Horario inválidos. '.$key;
							//vista
							return false;
						}

						break;
					default:

						$array = [
						"nombre" => ["",false],
						"porcentaje" => ["",false],
						"extra" => ["",false],
					];
				array_push($this->array_rubros, $array);
				
						$pattern = '/^(rubro)[0-9]+_(nombre)$/';
						

						if(preg_match($pattern, $_POST[$key])==1){

							
							
						}
						else{
							$pattern = '/^(rubro)[0-9]+_(porcentaje)$/';

							if(preg_match($pattern, $_POST[$key])==1){

								
							}
							else{
								$pattern = '/^(rubro)[0-9]+_(extra)$/';

							}
						}
						break;
				}
			}

			if($this->array_validacion['nrc'][1] && $this->array_validacion['nombre'][1] && $this->array_validacion['materia_clave'][1] &&
				$this->array_validacion['ciclo_id'][1] && $this->array_validacion['usuario_id'][1]){
				if($ban_dias==1)
					return true;
				else{
					echo 'Necesitan indicar dias.';
					return false;
				}
			}	
			else{
				echo 'Faltan campos necesarios.';
				return false;
			}
		}

		public function validar_matricular(){

			if(!empty($_POST)){
				
				if(isset($_POST['id_nrc']) && isset($_POST['usuario_id'])){

					$query = 'SELECT * FROM curso WHERE id=' . $_POST['id_nrc'];
					$resultado = $this->mdl->db_driver->query($query);
					$array_curso = array();
					if($array_curso = $resultado->fetch_array(MYSQL_ASSOC)){
						$query = "SELECT * FROM cuentas WHERE id=" . $_POST['usuario_id'];
						$resultado = $this->mdl->db_driver->query($query);
						$array_usuario = array();
						if($array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
							if($array_usuario['tipo']==2){
								$resultado = $this->mdl->consulta_CursoAlumno($array_curso['id'], $array_usuario['id']);
								$array_temp = array();

								if(!$array_temp = $resultado->fetch_array(MYSQL_ASSOC)){
									
									if($this->mdl->matricular($array_curso['id'], $array_usuario['id'])){
										echo 'Se amtriculo el alumno';
										return true;
									}
									else
										echo 'No se pudo matricular.';
								}
								else
									echo 'El alumno ya esta registrado.';	
							}
							else
								echo 'El usuario no es un alumno';

						}
						else
							echo 'El usuario no existe.';
					}
					else
						echo 'El nrc no existe';

				}
				else
					echo 'Faltan valores.';
			}
			else
				echo 'No hay vaores psot';


			return false;
		}

		public function validar_desmatricular(){

			if(!empty($_POST)){
				
				if(isset($_POST['id_nrc']) && isset($_POST['usuario_id'])){

					$query = 'SELECT * FROM curso WHERE id=' . $_POST['id_nrc'];
					$resultado = $this->mdl->db_driver->query($query);
					$array_curso = array();
					if($array_curso = $resultado->fetch_array(MYSQL_ASSOC)){
						$query = 'SELECT * FROM cuentas WHERE id=' . $_POST['usuario_id'];
						$resultado = $this->mdl->db_driver->query($query);
						$array_usuario = array();
						if($array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
							
							$resultado = $this->mdl->consulta_CursoAlumno($array_curso['id'], $array_usuario['id']);
							$array_temp = array();

							if($array_temp = $resultado->fetch_array(MYSQL_ASSOC)){
								if($this->mdl->desmatricular($array_curso['id'], $array_usuario['id'])){
									return true;
								}
								else
									echo 'No se pudo desmatricular';
							}
							else
								echo 'El usuario no está matriculado en este curso.';
						}
						else
							echo 'El usuario no existe.';
					}
					else
						echo 'El nrc no existe';

				}
				else
					echo 'Faltan valores.';
			}
			else
				echo 'No hay vaores psot';


			return false;
		}

		public function consultar_datos_curso(){

			if(!empty($_POST)){
				if(isset($_POST['nrc'])){
					$resultado = $this->mdl->consulta_Curso($_POST['nrc']);
					$array_curso = array();
					if($array_curso = $resultado->fetch_array(MYSQL_ASSOC)){
						var_dump($array_curso);
					}
					else
						echo 'El NRC no existe.';
				}
				else
					echo 'Falta valores';
			}
			else
				echo 'Post vacio.';

			return false;
		}
	}
?>