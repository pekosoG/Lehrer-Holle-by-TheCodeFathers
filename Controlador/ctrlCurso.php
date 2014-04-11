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
										if($this->mdl->alta($this->array_validacion, $this->array_clasesdias, $this->array_rubros)){
											echo 'se dio de alta';
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
			require("Controlador/validaciones.php");
			$aux = new validaciones();

			foreach ($_POST as $key => $value) {
				switch ($key) {
					case 'nombre':

						if(!$aux->validar('nombre', $_POST['nombre'], $this->array_validacion)){
							echo 'Nombre inválido.';
							return false;
						}

						break;

					case 'nrc':

						if($aux->validar('nrc', $_POST['nrc'])){

							if(!$this->mdl->existe('nrc', $_POST['nrc'])){
								$this->array_validacion['nrc'][0] = '\'' . $_POST['nrc'] . '\'';
								$this->array_validacion['nrc'][1] =true;

								$this->array_validacion['id'][0] = $this->mdl->nextID('curso');
								$this->array_validacion['id'][1] = true;
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

						if($aux->validar('materia_clave', $_POST['materia_clave'])){
							
							if(!$this->mdl->existe('materia_clave', $_POST['materia_clave'])){
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
						if($aux->validar('ciclo_id', $_POST['ciclo_id'])){

							if(!$this->mdl->existe('ciclo_id', $_POST['ciclo_id'])){
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
						if($aux->validar('usuario_id', $_POST['usuario_id'])){
							if(!$this->mdl->existe('usuario_id', $_POST['usuario_id'])){
								echo 'ID usuario no existe.';
								//vista
								return false;
							}
							else{
								if(strcmp($_SESSION['type'], 'professor')==0){
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
							$this->array_clasesdias[$key][0] = $_POST[$key];
							$this->array_clasesdias[$key][1] = true;
							$ban_dias=1;
						}
						else{
							echo 'Horario inválidos. '.$key;
							//vista
							return false;
						}

						break;
					default:
						$pattern = '/^(rubro)[0-9]+_(nombre)$/';

						if(preg_match($pattern, $key)==1){

							$llave = explode('_', $key);

							$ban = 0;
							
							for($i=0;$i<sizeof($this->array_rubros);$i++){
								foreach ($this->array_rubros[$i] as $llave_array => $value_array) {
									if(strcmp($llave_array, $llave[0])==0){
										$this->array_rubros[$i][$llave_array]['nombre'][0] = $_POST[$key];
										$this->array_rubros[$i][$llave_array]['nombre'][1] = true;
										$ban=1;
										break;
									}
								}
							}

							if($ban==0){
								$array = [
									"$llave[0]" => [ 
											"nombre" => [$_POST[$key],true],
											"porcentaje" => ["",false],
											"extra" => ["",false],
										],
								];

								array_push($this->array_rubros, $array);
							}
						}
						else{
							$pattern = '/^(rubro)[0-9]+_(porcentaje)$/';

							if(preg_match($pattern, $key)==1){
								$llave = explode('_', $key);

								$ban = 0;
								
								for($i=0;$i<sizeof($this->array_rubros);$i++){
									foreach ($this->array_rubros[$i] as $llave_array => $value_array) {
										if(strcmp($llave_array, $llave[0])==0){
											$this->array_rubros[$i][$llave_array]['porcentaje'][0] = $_POST[$key];
											$this->array_rubros[$i][$llave_array]['porcentaje'][1] = true;
											$ban=1;
											break;
										}
									}
								}

								if($ban==0){
									$array = [
										"$llave[0]" => [ 
												"nombre" => ["",false],
												"porcentaje" => [$_POST[$key],true],
												"extra" => ["",false],
											],
									];

									array_push($this->array_rubros, $array);
								}
							}
							else{
								$pattern = '/^(rubro)[0-9]+_(extra)$/';

								if(preg_match($pattern, $key)==1){
									$llave = explode('_', $key);

									$ban = 0;
									
									for($i=0;$i<sizeof($this->array_rubros);$i++){
										foreach ($this->array_rubros[$i] as $llave_array => $value_array) {
											if(strcmp($llave_array, $llave[0])==0){
												$this->array_rubros[$i][$llave_array]['extra'][0] = $_POST[$key];
												$this->array_rubros[$i][$llave_array]['extra'][1] = true;
												$ban=1;
												break;
											}
										}
									}

									if($ban==0){
										$array = [
											"$llave[0]" => [ 
													"nombre" => ["",false],
													"porcentaje" => ["",false],
													"extra" => [$_POST[$key],true],
												],
										];

										array_push($this->array_rubros, $array);
									}
								}
							}
						}

						break;
				}
			}

			if(!empty($_POST['materia_clave']) && !empty($_POST['seccion'])){
				if(!$this->mdl->existe('seccion', $_POST['materia_clave'], $_POST['seccion'])){
					
					$this->array_validacion['seccion'][0] = $_POST['seccion'];
					$this->array_validacion['seccion'][1] = true;
				}
				else{
					echo 'seccion ocupada';
					return false;
				}

			}

			if(!empty($this->array_rubros)){
				for($i=0;$i<sizeof($this->array_rubros);$i++){
					foreach ($this->array_rubros[$i] as $llave_array => $value_array) {	
						foreach ($value_array as $key => $value) {
							if(!$value[1]){
								echo 'faltan parametros en ' . $llave_array;
								return false;
							}
							else{
								//echo $key . $value[0];
								if(!$aux->validar($key,$value[0])){
									echo 'El formato de ' . $key . ' en ' . $llave_array;
									return false;
								}
							}
						}
					}
				}
			}
			else{
				echo 'Necesita rubros de evaluacion';
				return false;
			}

			if($this->array_validacion['nrc'][1] && $this->array_validacion['nombre'][1] && $this->array_validacion['materia_clave'][1] &&
				$this->array_validacion['ciclo_id'][1] && $this->array_validacion['usuario_id'][1] && $this->array_validacion['seccion'][1]){
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
				
				if(!empty($_POST['nrc']) && !empty($_POST['usuario_id'])){

					if($this->mdl->existe('nrc', $_POST['nrc'])){
						
						if($this->mdl->existe('usuario_id', $_POST['usuario_id'])){

							$array_temp = $this->mdl->consulta_Alumno($_POST['usuario_id']);

							if($array_temp['tipo']==2){

								$array_curso = $this->mdl->consulta_cursoID($_POST['nrc']);

								if($this->mdl->consulta_CursoAlumno($_POST['usuario_id'], $array_curso['id'])){
									
									if($this->mdl->matricular($array_curso['id'], $_POST['usuario_id'])){
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

			if(!empty($_POST['nrc']) && !empty($_POST['usuario_id'])){

				if($this->mdl->existe('nrc', $_POST['nrc'])){
						
					if($this->mdl->existe('usuario_id', $_POST['usuario_id'])){
							
						$array_curso = $this->mdl->consulta_cursoID($_POST['nrc']);

						if(!$this->mdl->consulta_CursoAlumno($_POST['usuario_id'], $array_curso['id'])){

							if($this->mdl->desmatricular($array_curso['id'], $_POST['usuario_id'])){
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