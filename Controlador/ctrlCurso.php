<?php
include 'Controlador/ctrlEstandar.php';
	class ctrlCurso extends ctrlEstandar{
		private $mdl;
		private $array_validacion;
		private $array_clasesdias;
		private $array_rubros;

		public function ejecutar(){
			require("Modelo/mdl_ctrlCurso.php");
			$this->mdl = new mdl_ctrlCurso();
			if(isset($_GET['act'])){
				switch ($_GET['act']) {
					case 'alta':
						if($this->isLogged()){
							if($this->isProfessor()){
								if(self::validar_alta()){
									//var_dump($this->array_validacion);
									if($this->mdl->alta($this->array_validacion, $this->array_clasesdias, $this->array_rubros)){
										echo 'Se agrego el curso';
									}
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

					case 'lista':
						if($this->isLogged()){
							if($this->isProfessor()){
								if(isset($_GET['curso_id']) && !empty($_GET['curso_id'])){
									$this->mdl->lista_Curso($_GET['curso_id']);
								}
							}
							else{
								if($this->isStudent()){
									if(isset($_GET['curso_id'])){
										$this->mdl->lista_Curso($_GET['curso_id']);
									}
								}
							}
						}
						else{
							echo 'No estas loggeado.';
						}

						break;

					case 'clonar_curso':
						if($this->isLogged()){
							if($this->isProfessor()){
								if(isset($_POST)){
									if(self::validar_clonarCurso()){
										if($this->mdl->alta($this->array_validacion, $this->array_clasesdias, $this->array_rubros)){
											echo 'Se agrego el curso';
										}	
										else{
											echo 'no se clono';
										}
									}
								}
								else{

								}
							}
							else{
								echo 'solo prof';
							}
						}
						else{
							echo 'No estas loggeado.';
						}
						break;

					case 'modificar':
						if($this->isLogged()){
							if($this->isProfessor()){
								if(self::validar_modificarCurso()){
									$this->mdl->modificarCurso($this->array_validacion);
								}
								else{
									echo 'no se modifico';
								}
							}
							else{
								echo 'solo prof';
							}

						}
						break;
					
					default:

						break;
				}
			}
		}

		public function validar_alta(){
			if(!empty($_POST)){
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
					"seccion" => ["",false],
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

							if(!isset($_POST['seccion'])){
								echo 'Falta la seccion';
								return false;
							}
							else{
								$pattern = '/^[0-9]{1,2}$/';
								if(preg_match($pattern, $_POST['seccion'])==0){
									echo 'La seccion no es valida';
									return false;
								}
							}


							$pattern = '/^[0-9]{5,5}$/';

							if(preg_match($pattern, $_POST['nrc'])==1){
								$query = 'SELECT * FROM curso WHERE nrc='. $_POST['nrc'];
								$resultado = $this->mdl->db_driver->query($query);
								$array_usuario = array();
								if(!$array_usuario = $resultado->fetch_array(MYSQL_ASSOC)){
									$this->array_validacion['nrc'][0] = '\'' . $_POST['nrc'] . '\'';
									$this->array_validacion['nrc'][1] =true;

									$query = 'SELECT id,seccion FROM curso ORDER BY id ASC';
									$resultado = $this->mdl->db_driver->query($query);
									$array_temp = array();
									$cont=0;
									if(!$array_temp = $resultado->fetch_array(MYSQL_ASSOC)){
										$this->array_validacion['id'][0] = $cont;
										$this->array_validacion['id'][1] = true;

										$this->array_validacion['seccion'][0] = $_POST['seccion'];
										$this->array_validacion['seccion'][1] = true;
									}
									else{
										$ban_cont=0;
										while($array_temp = $resultado->fetch_array(MYSQL_ASSOC)){ 
											if($array_temp['seccion']==$_POST['seccion']){
												echo 'La seccion ya esta en uso.';
												return false;
											}

											if($array_temp['id']!=$cont){
												$ban_cont=1;
												break;
											}
												

											$cont++; 
										}

										if($ban_cont==1)
											$cont--;

										$this->array_validacion['id'][0] = $cont+1;
										$this->array_validacion['id'][1] = true;

										$this->array_validacion['seccion'][0] = $_POST['seccion'];
										$this->array_validacion['seccion'][1] = true;
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
								$query = 'SELECT * FROM materia WHERE clave=\''. $_POST['materia_clave'] . '\'';
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
							//echo $this->array_rubros[0]['rubro1_nombre']['nombre'][0];
							$pattern = '/^(rubro)[0-9]+_(nombre)$/';
						
							$ban = 0;
							if(preg_match($pattern, $key)==1){
								$split = explode('_', $key);
								//echo $split[0];
								for($i=0;$i<sizeof($this->array_rubros);$i++){
									if(isset($this->array_rubros[$i][$split[0]])){
										$pattern = '/^[a-zA-Z].{0,50}$/';

										if(preg_match($pattern, $_POST[$key])){
											$this->array_rubros[$i][$split[0]]['nombre'][0] = '\'' . $_POST[$key] . '\'';
											$this->array_rubros[$i][$split[0]]['nombre'][1] = true;;
											$ban=1;
											break;
										}
										else{
											echo 'No es un nombre valido RUBRO';
											return false;
										}
									}
								}

								if($ban==0){
									$pattern = '/^[a-zA-Z].{0,50}$/';

									if(preg_match($pattern, $_POST[$key])){
										$array = [
											$split[0] => [
												"nombre" => ['\'' . $_POST[$key] . '\'',true],
												"porcentaje" => ["",false],
												"extra" => ["",false],
											],
										];
									}
									else{
										echo 'No es un nombre valido RUBRO';
										return false;
									}
									
									array_push($this->array_rubros, $array);
								}
							}
							else{
								$pattern = '/^(rubro)[0-9]+_(porcentaje)$/';

								if(preg_match($pattern, $key)==1){
									$split = explode('_', $key);
									//echo $split[0];
									for($i=0;$i<sizeof($this->array_rubros);$i++){
										if(isset($this->array_rubros[$i][$split[0]])){
											$pattern = '/^[0-9]?\.[0-9]+$/';

											if(preg_match($pattern, $_POST[$key])){
												$this->array_rubros[$i][$split[0]]['porcentaje'][0] = $_POST[$key];
												$this->array_rubros[$i][$split[0]]['porcentaje'][1] = true;;
												$ban=1;
												break;
											}
											else{
												echo 'No es un porcentaje valido RUBRO';
												return false;
											}
										}
									}

									if($ban==0){
										$pattern = '/^[0-9]?\.[0-9]+$/';

										if(preg_match($pattern, $_POST[$key])){
											$array = [
												$split[0] => [
													"nombre" => ["",false],
													"porcentaje" => [$_POST[$key],true],
													"extra" => ["",false],
												],
											];
										}
										else{
											echo 'No es un porcentaje valido RUBRO';
											return false;
										}

										array_push($this->array_rubros, $array);
									}
								}
								else{
									$pattern = '/^(rubro)[0-9]+_(extra)$/';

									if(preg_match($pattern, $key)==1){
										$split = explode('_', $key);
										//echo $split[0];
										for($i=0;$i<sizeof($this->array_rubros);$i++){
											if(isset($this->array_rubros[$i][$split[0]])){
												$pattern = '/^[0-9]$/';

												if(preg_match($pattern, $_POST[$key])){
													$this->array_rubros[$i][$split[0]]['extra'][0] = $_POST[$key];
													$this->array_rubros[$i][$split[0]]['extra'][1] = true;;
													$ban=1;
													break;
												}
												else{
													echo 'No es un extra valido RUBRO';
													return false;
												}
											}
										}

										if($ban==0){
											$pattern = '/^[0-9]$/';

											if(preg_match($pattern, $_POST[$key])){
												$array = [
													$split[0] => [
														"nombre" => ["",false],
														"porcentaje" => ["",false],
														"extra" => [$_POST[$key],true],
													],
												];												}
											else{
												echo 'No es un extra valido RUBRO';
												return false;
											}

											array_push($this->array_rubros, $array);
										}
									}
								}
							}

							break;
					}
				}
				//var_dump($this->array_rubros);
			/*	var_dump($this->array_rubros[0]);
				var_dump($this->array_rubros[1]);
				var_dump($this->array_rubros[2]);*/
				$ban = 0;
				$ban_rubro = 0;

				for($i=0;$i<sizeof($this->array_rubros);$i++){
					$ban = 0;
					foreach ($this->array_rubros[$i] as $key_aux => $value_aux) {
						foreach ($value_aux as $key => $value) {
							if(!$value[0]){
								echo 'Faltan campos a ' .  $key_aux . '<br/>';
								$ban = 1;
								$ban_rubro = 1;
								break;
							}
						}

						if($ban==1)
							break;
					}
				}

				if($ban_rubro==1)
					return false;

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
			else{
				echo 'no hay post';
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

		public function validar_clonarCurso(){
			if(isset($_POST['curso_id']) && isset($_POST['ciclo_nuevo'])){
				$query = 'SELECT * FROM curso WHERE id=' . $_POST['curso_id'];
				$resultado = $this->mdl->db_driver->query($query);
				$array_curso = array();

				if($array_curso = $resultado->fetch_array(MYSQL_ASSOC)){
					
					$query = 'SELECT * FROM ciclo WHERE id=\'' . $_POST['ciclo_nuevo'] . '\'';
					$resultado = $this->mdl->db_driver->query($query);
					$arrayx = array();
					if(!$arrayx = $resultado->fetch_array(MYSQL_ASSOC)){
						echo 'no existe el ciclo';
						return false;
					}
					else{
						$query = 'SELECT * FROM curso WHERE nrc=' . $array_curso['nrc'] .
							' AND ciclo_id=\'' . $arrayx['id'] . '\'';
						$resultado = $this->mdl->db_driver->query($query);
						$array_y = array();

						if($array_y = $resultado->fetch_array(MYSQL_ASSOC)){
							echo 'Ya hay un curso existente,';
							return false;
						}


						$query = 'SELECT id FROM curso ORDER BY id ASC';
						$resultado = $this->mdl->db_driver->query($query);
						$array_cont = array();
						$cont = 0;
						while($array_cont = $resultado->fetch_array(MYSQL_ASSOC) ){
							if($array_cont['id']!=$cont)
								break;

							$cont++;
						}

						$this->array_validacion = [
							"id" => [$cont,true],
							"nrc" => [$array_curso['nrc'],true],
							"nombre" => ['\'' . $array_curso['nombre'] . '\'',true],
							"materia_clave" => ['\'' . $array_curso['materia_clave'] . '\'',true],
							"ciclo_id" => ['\'' . $arrayx['id'] . '\'',true],
							"usuario_id" => [$array_curso['usuario_id'],true],
							"seccion" => [$array_curso['seccion'],true],
						];
					}

					

					$this->array_clasesdias = [
						"lunes" => ["",false],
						"martes" => ["",false],
						"miercoles" => ["",false],
						"jueves" => ["",false],
						"viernes" => ["",false],
						"sabado" => ["",false],
					];

					//var_dump($this->array_validacion);

					$query = 'SELECT * FROM clasesdias WHERE curso_id=' . $array_curso['id'];
					unset($array_cont);
					$array_clases = array();
					$resultado = $this->mdl->db_driver->query($query);
					$this->array_clasesdias = array();
					while($array_clases = $resultado->fetch_array(MYSQL_ASSOC) ){
						switch ($array_clases['dia']) {
							case 1:
								$this->array_clasesdias["lunes"][0] = $array_clases['horainicio'].'-'.$array_clases['horafinal'];
								$this->array_clasesdias["lunes"][1] = true;
								break;
							case 2:
								$this->array_clasesdias["martes"][0] = $array_clases['horainicio'].'-'.$array_clases['horafinal'];
								$this->array_clasesdias["martes"][1] = true;
								break;
							case 3:
								$this->array_clasesdias["miercoles"][0] = $array_clases['horainicio'].'-'.$array_clases['horafinal'];
								$this->array_clasesdias["miercoles"][1] = true;
								break;
							case 4:
								$this->array_clasesdias["jueves"][0] = $array_clases['horainicio'].'-'.$array_clases['horafinal'];
								$this->array_clasesdias["jueves"][1] = true;
								break;
							case 5:
								$this->array_clasesdias["vieres"][0] = $array_clases['horainicio'].'-'.$array_clases['horafinal'];
								$this->array_clasesdias["vieres"][1] = true;
								break;
							case 6:
								$this->array_clasesdias["sabado"][0] = $array_clases['horainicio'].'-'.$array_clases['horafinal'];
								$this->array_clasesdias["sabado"][1] = true;
								break;
						}
					}

					//var_dump($this->array_clasesdias);
				
					$query = 'SELECT * FROM RUBRO where curso_id=' . $array_curso['id'];
					$this->array_rubros = array();
					$rubros = array();
					$resultado = $this->mdl->db_driver->query($query);
					$i = 1;
					while($rubros = $resultado->fetch_array(MYSQL_ASSOC)){
						$array = [
							"rubro$i" => [
								"nombre" => ['\'' . $rubros['nombre'] . '\'',true],
								"porcentaje" => [$rubros['porcentaje'],true],
								"extra" => [$rubros['extra'],true],
								],
						];	

						array_push($this->array_rubros, $array);
						$i++;
					}
					//var_dump($this->array_rubros);
					return true;

				}
				else{
					echo 'no existe el curso';
				}

			}
			else{
				echo 'faltan valores';
				
			}
			return false;
		}

		public function validar_modificarCurso(){
			if(!isset($_POST['id'])){
				echo 'no hay id';
				return false;
			}


			$this->array_validacion = array();
			foreach ($_POST as $key => $value) {
				switch ($key) {
					case 'nrc':
						if(strcmp($value, '')!=0){
							$pattern = '/^[0-9]{5,5}$/';

							if(preg_match($pattern, $value)){
								$query = 'UPDATE curso SET nrc=' . $value . ' WHERE id=' . $_POST['id'];
								array_push($this->array_validacion, $query);
							}
							else{
								echo 'nrc error ';
								return false;
							}
						}

						break;

					case 'nombre_curso':
						
						if(strcmp($value, '')!=0){
							$pattern = '/^[a-zA-Z].{0,49}$/';

							if(preg_match($pattern, $value)){
							
								$query = 'UPDATE curso SET nombre=\'' . $value . '\' WHERE id=' . $_POST['id'];
								array_push($this->array_validacion, $query);
							}
							else{
								echo 'nombre error ';
								return false;
							}
						}
						
						break;

					case 'seccion':
						if(strcmp($value, '')!=0){
							$pattern = '/^[0-9]{1,2}$/';

							if(preg_match($pattern, $value)){
								$query = 'UPDATE curso SET seccion=' . $value . ' WHERE id=' . $_POST['id'];
								array_push($this->array_validacion, $query);
							}
							else{
								echo 'seccion error ';
								return false;
							}
						}
						
						break;

					case 'lunes':
					case 'martes':
					case 'miercoles':
					case 'jueves':
					case 'viernes':
					case 'sabado':
						if(strcmp($value, '')!=0){
							$pattern = '/^[0-9]\^[0-9]{4,4}-[0-9]{4,4}\*[0-9]\^[0-9]{4,4}-[0-9]{4,4}$/';
							
							if(preg_match($pattern, $value)){
								$dia = explode('*', $value);
								$viejo = explode('^', $dia[0]);
								$nuevo = explode('^', $dia[1]);
								$horario_viejo = explode('-', $viejo[1]);
								$horario_nuevo = explode('-', $nuevo[1]);

								$query = 'UPDATE clasesdias SET dia=' . $nuevo[0]  . ', '
									. 'horainicio=\'' . $horario_nuevo[0] . '\', horafinal=\'' . $horario_nuevo[1]
									.'\' WHERE curso_id=' . $_POST['id']
									. ' AND dia=' . $viejo[0];

								array_push($this->array_validacion, $query);
							}
						}
						
					break;
					
					default:
						# code...
						break;
				}
			}
			var_dump($this->array_validacion);
			return true;
		}
	}
?>