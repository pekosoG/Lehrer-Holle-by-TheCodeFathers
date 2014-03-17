<?php
/**
 * Interfaz de Controlador para seguir con un Standard
 * en el metodo de ejecutar (evitar problemas con MaYUsCULas)
 * @author PekosoG
 * @version 0.5
 */
interface Controlador{
	/**
	 * Aunque no estoy muy seguro si funcionan tal cual como
	 * en Java, donde el lenguaje te obliga a implementarlo y 
	 * a mostrar con el "@Override" que ese metodo está sobre escrito
	 */
	function ejecutar();
}
?>