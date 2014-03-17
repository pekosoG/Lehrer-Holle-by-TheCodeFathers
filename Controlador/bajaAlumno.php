<?php
	/* 
	* void ejecutar() Checks the data in the $_POST parameter 'codigo' and calls the function mdl_bajaAlumno.baja() to check if 'codigo' exists
	*/
	class bajaAlumno{
		public function ejecutar(){
			if(!empty($_POST['codigo'])){
				require ("Modelo/mdl_bajaAlumno.php");
				$mdl = new mdl_bajaAlumno();
				$mdl->baja();
			}
		}
	}
?>