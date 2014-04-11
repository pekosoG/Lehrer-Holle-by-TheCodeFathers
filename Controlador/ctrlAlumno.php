<?php
include 'Controlador/ctrlPersonaEstandar.php';
	class ctrlAlumno extends ctrlPersonaEstandar{
		
		public function ejecutar(){

			if(!empty($_GET['act'])){
				switch ($_GET['act']){
					case 'alta':
					
						if($this->validacion_alta()){

							if($this->mdl->alta($this->array_validacion, 'alumno')){
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
	}
?>