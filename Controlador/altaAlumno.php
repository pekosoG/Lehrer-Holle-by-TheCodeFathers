<?php
/*
*	void ejecutar() Checks the data needed to work in the Model, once it's checked process to mdl_nuevoAlumno.validar()
*/
	class altaAlumno{

		public function ejecutar(){
			require("Modelo/mdl_nuevoAlumno.php");
			$mdl = mdl_nuevoAlumno();
			$mdl->validar();
			
		}
	}
?>