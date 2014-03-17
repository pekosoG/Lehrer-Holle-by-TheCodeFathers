<?php
/*
*	boolean	validar()	Validates the data needned to make the insertion in the DB
	void 	alta()		Make the insertion in the DB
*/
	class mdl_altaCurso{
		public $pattern;
		public $error_seccion;


		public function validar(){
				foreach ($_POST as $key => $value) {
					switch ($key) {
						case 'curso':
							$this->pattern = '/[\\w]{5,40}/';

							if(preg_match($this->pattern, $_POST['curso'])==0){
								$this->error_seccion = "Curso";
								return false;
							}

							break;
						
						case 'seccion':
							$this->pattern = '/^[a-zA-Z]?[0-9]{2,2}/';

							if(preg_match($this->pattern, $_POST['seccion'])==0){
								$this->error_seccion = "Sección";
								return false;
							}
							
							break;
						
						case 'nrc':
							$this->pattern = '/[0-9]{5,5}/';

							if(preg_match($this->pattern, $_POST['nrc'])==0){
								$this->error_seccion = "NRC";
								return false;
							}
							
							break;

						case 'academia':
							$this->pattern = '/^[a-zA-Z][a-zA-Z\w]{2,30}/';

							if(preg_match($this->pattern, $_POST['academia'])==0){
								$this->error_seccion = "Academia";
								return false;
							}
							
							break;

						default:
							# code...
							break;
					}
				}
			return true;
		}

		public function alta(){
			//Insercion en la BD

		}

	}
?>