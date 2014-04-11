<?php
	class mdl_Curso{
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
			var_dump($query);
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

			for($i=0;$i<sizeof($array_rubros);$i++){
				$query = 'INSERT INTO rubro(id, curso_id, ';
				$valores = self::nextID('rubro') . ', ' . $array_validacion['id'][0] . ', ';
				$cont = 0;
				foreach ($array_rubros[$i] as $llave_array => $value_array) {	
					foreach ($value_array as $key => $value) {

						if(strcmp($key, 'nombre')==0)
							$valores .= '\'' . $value[0] . '\'';
						else
							$valores .= $value[0];

						$query .= $key;

						if($cont+1<sizeof($value_array)){
							$query .= ', ';
							$valores .= ', ';
						}

						$cont++;
					}
						
				}
				
				$query .= ') VALUES(' . $valores . ');';
				$this->db_driver->query($query);
			}

			return true;
		}

		public function consulta_CursoAlumno($usuario_id, $id_nrc){
			$array = $this->db_driver->query('SELECT * FROM curso_alumnos WHERE 
				usuario_id=' . $usuario_id . ' AND curso_id=' 
				. $id_nrc)->fetch_array(MYSQL_ASSOC);

			if(empty($array))
				return true;
			else
				return false;
		}

		public function consulta_Curso($nrc){
			return $this->db_driver->query("SELECT * FROM curso AS c JOIN usuario " .
				"AS u ON c.usuario_id=u.id WHERE c.nrc=" . $nrc);
		}

		public function consulta_Alumno($id){

			return $this->db_driver->query('SELECT * FROM cuentas WHERE id=' . $id)->fetch_array(MYSQL_ASSOC);
		}

		public function consulta_cursoID($nrc){

			return $this->db_driver->query('SELECT * FROM curso WHERE nrc=' . $nrc)->fetch_array(MYSQL_ASSOC);
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

		public function existe($campo, $cadena, $seccion = NULL){
			$query = '';
			switch ($campo) {
				case 'nrc':
					$query = 'SELECT * FROM curso WHERE nrc=' . $cadena;
					break;

				case 'materia_clave':
					$query = "SELECT * FROM materia WHERE clave='" . $cadena . '\'';
					break;

				case 'ciclo_id':
					$query = 'SELECT * FROM ciclo WHERE id=\'' . $cadena . '\'';
					break;

				case 'usuario_id':
					$query = 'SELECT * FROM cuentas WHERE id=' . $cadena . ' AND status=1';
					break;

				case 'curso':
					$query = 'SELECT * FROM curso WHERE id=' . $cadena;
					break;

				case 'seccion':
					if(func_num_args()==3){
						$query = 'SELECT * FROM curso WHERE materia_clave=\'' . $cadena
					 		. '\' AND seccion=' . $seccion;
					}
					else
						return false;

					break;
			}

			$resultado = $this->db_driver->query($query);
			$array = array();
			if($array = $resultado->fetch_array(MYSQL_ASSOC)){

				return true;
			}

			return false;
		}

		public function nextID($tabla){
			$query = 'SELECT id FROM ' . $tabla . ' ORDER BY id ASC';
			
			$resultado = $this->db_driver->query($query);
			$array = array();
			$cont=0;

			while($array = $resultado->fetch_array(MYSQL_ASSOC)){ 

				if($array['id']!= $cont)
					return $cont;


				$cont++;
			}

			return $cont;
		}
	}
?>