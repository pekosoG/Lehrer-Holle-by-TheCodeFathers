<?php
/*
*	void ejecutar() Validates the data sent from $_POST, if proceed, the insertion in DB it's performed
*/
	class mdl_nuevoALumno{
		public function validar(){
			$ban = -1;
			$parte = "";
			$pattern = "";

			$array_validacion = [
				"codigo" => array("Codigo", false),
				"nombre" => array("Nombre", false),
				"carrera" => array("Carrera", false),
				"correo" => array("Correo electronico", false),
			];


			if(!empty($_POST))
				$ban = 0;
			 	

			foreach ($_POST as $key => $value) {
				switch ($key) {
					case 'nombre': 
						$pattern = '/^^[a-zA-z][a-zA-Z]{,20}/';
						$array_validacion['nombre'][1] = true;
						break;
					case 'correo': 
						$pattern ='/^[\\w-]+(\\.[\\w-]+)*@[A-Za-z0-9]+(\\.[A-Za-z0-9]+)*(\\.[A-Za-z]{2,})$/';

						if(preg_match($pattern, $_POST['correo'])==0)
						{
							$ban = 1;
							$parte ="Correo electronico";
						}
					
						$array_validacion['correo'][1] = true;
					break;

					case 'codigo':
						$pattern = '/^[a-zA-Z]?[0-9]{,10}/';

						if(preg_match($pattern, $_POST['codigo'])==0)
						{
							$ban = 1;
							$parte ="Codigo";
						}	

						$array_validacion['codigo'][1] = true;
					break;

					case 'carrera':
						$pattern = '/[A-Z]{3,5}/';

						if(preg_match($pattern, $_POST['carrera'])==0)
						{
							$ban = 1;
							$parte ="Carrera";
						}
						
						$array_validacion['carrera'][1] = true;

					break;

					case 'url':
						$pattern = '/(^((http:\/\/)|(www\.)|(http:\/\/www\.))(a-zA-Z){,20}\.(a-zA-Z){,6})?/';

						if(preg_match($pattern, $_POST['url'])==0)
						{
							$ban = 1;
							$parte ="URL";
						}

						break;

					case 'github':
						$pattern = '/(^((http:\/\/)|(www\.)|(http:\/\/www\.))github\.com\/(a-zA-Z){,20})?/';

						if(preg_match($pattern, $_POST['github'])==0)
						{
							$ban = 1;
							$parte ="Github";
						}

					break;

					case 'celular':
						$pattern = '/^[0-9][0-9]{10,12}/';

						if(preg_match($pattern, $_POST['celular'])==0)
						{
							$ban = 1;
							$parte ="Celular";
						}

					break;

					case 'equipo':
						$pattern = '/([\w\s]{,30})?/';

						if(preg_match($pattern, $_POST['equipo'])==0)
						{
							$ban = 1;
							$parte ="Equipo";
						}

					break;
				}

				if($ban!=0)
					break;
			}

			foreach ($array_validacion as $key => $value) {
				if($value[1]==false){
					echo 'Falta el campo ',$value[0],'<br/>';
					$ban = 2;
				}
			}

			if($ban==1){
				echo 'Error en la seccion de ',$parte;
			}
			elseif ($ban==0) {
				require("Modelo/mdl_nuevoAlumno.php");
				$mdl = new mdl_nuevoAlumno();
				$mdl->ingresar();
			}
			
			
		}
	}

?>