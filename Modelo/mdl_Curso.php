<?php
	class mdl_Curso{
		public $db_driver;

		function __construct(){
			require_once("DataBase.php");
			$this->db_driver = DataBase::singleton()->db_driver;
		}		

		public function alta($array_validacion, $array_clasesdias){
			$query = 'INSERT INTO curso(';
			$llaves = '';
			$valores = '';
			
			$cont=0;	
			foreach ($array_validacion as $key => $value) {
				if($value[1]){
					$llaves .= $key;
					$valores .= $value[0];

					if($cont+1<sizeof($array_validacion)){
						$llaves .= ", ";
						$valores .= ", ";
					}	
				}

				$cont++;
			}

			$query .= $llaves . ') VALUES (' . $valores . ');';

			$this->db_driver->query($query);

			foreach ($array_clasesdias as $key => $value) {
				$query = 'INSERT INTO clasesdias(dia, horainicio, horafinal, curso_id) VALUES(';
				if($value[1]){
					switch ($key) {
						case 'lunes':
							$query .= 1;
							break;
						case 'martes':
							$query .= 2;
							break;
						case 'miercoles':
							$query .= 3;
							break;
						case 'jueves':
							$query .= 4;
							break;
						case 'viernes':
							$query .= 5;
							break;
						case 'sabado':
							$query .= 6;
							break;
					}
					
					$split = explode("-", $value[0]);
					$query .= ", '" . $split[0] . "', '" . $split[1] . "', " . $array_validacion['id'][0] . ");";
					$this->db_driver->query($query);
				}	
			}
		}

		public function consulta_CursoAlumno($id_nrc,$usuario_id){
			return $this->db_driver->query('SELECT * FROM curso_alumnos WHERE 
				usuario_id=' . $usuario_id . ' AND curso_id= ' . $id_nrc);
		}

		public function consulta_Curso($nrc){
			return $this->db_driver->query("SELECT * FROM curso AS c JOIN usuario " .
				"AS u ON c.usuario_id=u.id WHERE c.nrc=" . $nrc);
		}
		public function matricular($nrc, $usuario_id){
			$query = 'INSERT INTO curso_alumnos VALUES(' . $usuario_id . ', ' . $nrc . ');';
			if($this->db_driver->query($query)){
				return true;
			}

			return false;
		}

		public function desmatricular($id_nrc, $usuario_id){
			$query = 'DELETE FROM curso_alumnos WHERE usuario_id=' . $usuario_id .
				' AND curso_id=' . $id_nrc;

			if($this->db_driver->query($query)){
				return true;
			}

			return false;
		}
	}
?>