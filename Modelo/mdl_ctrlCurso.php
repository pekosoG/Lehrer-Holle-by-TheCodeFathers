<?php
	class mdl_ctrlCurso{
		public $db_driver;

		function __construct(){
			require_once("DataBase.php");
			$this->db_driver = DataBase::singleton()->db_driver;
		}		

		public function alta($array_validacion, $array_clasesdias, $array_rubros){
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

			$query = 'SELECT id FROM rubro ORDER BY id ASC';
			$valores = ''; 
			$resultado = $this->db_driver->query($query);
			$array_temp = array();
			$cont = 0;
			$array_querys = array();

			if(!$array_temp = $resultado->fetch_array(MYSQL_ASSOC)){
				
				for($i=0;$i<sizeof($array_rubros);$i++){
					$query = 'INSERT INTO rubro (id, ';
					$valores = $i . ', ';;
					foreach ($array_rubros[$i] as $key_aux => $value_aux) {
						$cont = 0;

						foreach ($value_aux as $key => $value) {
							$query .= $key;
							$valores .= $value[0];

							if($cont+1<sizeof($value_aux)){
								$query .= ', ';
								$valores .= ', ';
							}

							$cont++;
						}

						$query .= ', curso_id) VALUES (' . $valores . ', ' . $array_validacion['id'][0] . ');';
						
						$this->db_driver->query($query);
					}
				}
			}
			else{
				while($array_temp = $resultado->fetch_array(MYSQL_ASSOC)){ $cont++; }

				for($i=0;$i<sizeof($array_rubros);$i++){
					$query = 'INSERT INTO rubro (id, ';
					$valores = $cont+1 . ', ';;
					foreach ($array_rubros[$i] as $key_aux => $value_aux) {
						$cont_aux = 0;

						foreach ($value_aux as $key => $value) {
							$query .= $key;
							$valores .= $value[0];

							if($cont_aux+1<sizeof($value_aux)){
								$query .= ', ';
								$valores .= ', ';
							}

							$cont_aux++;
						}

						$query .= ', curso_id) VALUES (' . $valores . ', ' . $array_validacion['id'][0] . ');';
						$this->db_driver->query($query);
					}
					$cont++;
				}
			}

			return true;
		}

		public function consulta_CursoAlumno($id_nrc,$usuario_id){
			return $this->db_driver->query('SELECT * FROM curso_alumnos WHERE 
				usuario_id=' . $usuario_id . ' AND curso_id= ' . $id_nrc);
		}

		public function consulta_Curso($nrc){
			return $this->db_driver->query("SELECT * FROM curso AS c JOIN usuario " .
				'AS u JOIN rubro AS r ON c.usuario_id=u.id AND r.curso_id=c.id '
				. 'WHERE c.nrc=' . $nrc);
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

		public function lista_Curso($curso_id){
			$query = 'SELECT u.codigo, u.nombre,c.abreviacion FROM '
				. 'curso_alumnos AS ca JOIN usuario AS u JOIN carrera AS c ON ca.curso_id=' . $curso_id
				. ' AND ca.usuario_id=u.id AND u.carrera_id=c.id';

			//var_dump($query);

			$resultado = $this->db_driver->query($query);
			$array_temp = array();
			while($array_temp = $resultado->fetch_array(MYSQL_ASSOC)){
				var_dump($array_temp);
			}
		}

		public function modificarCurso($querys){
			for($i=0;$i<sizeof($querys);$i++){
				if(!$this->db_driver->query($querys[$i])){
					echo 'No se pudo realizar el query. ' . $this->db_driver->errno;
				}
			}
		}
	}
?>