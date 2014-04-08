<?php
	class mdl_Calificaciones{
		public $db_driver;

		function __construct(){
			require_once("DataBase.php");
			$this->db_driver = DataBase::singleton()->db_driver;
		}

		public function alta_ALumno($datos){
			if(isset($datos['rubro_id'])){
				$query = 'INSERT INTO calificacion_rubro(';
			}
			else{
				$query = 'INSERT INTO calificacion_curso(';
			}
			$valores = '';

			$cont = 0;
			foreach ($datos as $key => $value) {
				$query .= $key;
				$valores .= $value;

				if($cont+1<sizeof($datos)){
					$query .= ', '; 
					$valores .= ', '; 
				}
				$cont++;
			}

			$query .= ') VALUES(' . $valores . ');';
			var_dump($query);

			return true;
		}

		public function alta_Curso($datos){
			$array_querys = array();
			$query = '';
			for($i=0;$i<sizeof($datos)-1;$i++){
				if(!empty($datos[$i])){
					$query = 'INSERT INTO calificacion_curso(curso_id, usuario_id, calificacion)';
					$temp = explode('-', $datos[$i]);
					$query .= ' VALUES(' . $datos[sizeof($datos)-1] . ', ' . $temp[0] . ', ' . $temp[1] . ');';
					array_push($array_querys, $query);
				}
			}

			var_dump($array_querys);
			return true;
		}

		public function modificar_Alumno($datos){
			$query = '';
			if(isset($datos['rubro_id'])){
				$query = 'UPDATE calificacion_rubro SET calificacion=' . $datos['calificacion']
					. ' WHERE curso_id=' . $datos['curso_id'] . ' AND usuario_id=' . $datos['usuario_id']
					. ' AND rubro_id=' . $datos['rubro_id'];
			}
			else{
				$query = 'UPDATE calificacion_curso SET calificacion=' . $datos['calificacion']
					. ' WHERE curso_id=' . $datos['curso_id'] . ' AND usuario_id=' . $datos['usuario_id'];
			}
			var_dump($query);
			return true;
		}

		public function modificar_Curso($datos){
			$array_querys = array();
			$query = '';
			for($i=0;$i<sizeof($datos)-1;$i++){
				if(!empty($datos[$i])){
					$query = 'UPDATE calificacion_curso SET calificacion=';
					$temp = explode('-', $datos[$i]);
					$query .= $temp[1];
					$query .= ' WHERE usuario_id=' . $temp[0] . ' AND curso_id=' . $datos[sizeof($datos)-1];
					array_push($array_querys, $query);
				}
			}

			var_dump($array_querys);
			return true;
		}
	}
?>