<?php
include('../src/ajax/is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../libraries/password_compatibility_library.php");
}		
		if (empty($_POST['nombre2'])){
			$errors[] = "Nombres vacíos";
		} elseif (empty($_POST['apellido2'])){
			$errors[] = "Apellidos vacíos";
		}  elseif (empty($_POST['nombre_usuarios2'])) {
            $errors[] = "Nombre de usuario vacío";
        }  elseif (strlen($_POST['nombre_usuarios2']) > 64 || strlen($_POST['nombre_usuarios2']) < 2) {
            $errors[] = "Nombre de usuario no puede ser inferior a 2 o más de 64 caracteres";
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['nombre_usuarios2'])) {
            $errors[] = "Nombre de usuario no encaja en el esquema de nombre: Sólo aZ y los números están permitidos , de 2 a 64 caracteres";
        } elseif (empty($_POST['correo2'])) {
            $errors[] = "El correo electrónico no puede estar vacío";
        } elseif (strlen($_POST['correo2']) > 64) {
            $errors[] = "El correo electrónico no puede ser superior a 64 caracteres";
        } elseif (!filter_var($_POST['correo2'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Su dirección de correo electrónico no está en un formato de correo electrónico válida";
        } elseif (
			!empty($_POST['nombre_usuarios2'])
			&& !empty($_POST['nombre2'])
			&& !empty($_POST['apellido2'])
            && strlen($_POST['nombre_usuarios2']) <= 64
            && strlen($_POST['nombre_usuarios2']) >= 2
            && preg_match('/^[a-z\d]{2,64}$/i', $_POST['nombre_usuarios2'])
            && !empty($_POST['correo2'])
            && strlen($_POST['correo2']) <= 64
            && filter_var($_POST['correo2'], FILTER_VALIDATE_EMAIL)
          )
         {
            require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
			require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
			
				// escaping, additionally removing everything that could be (html/javascript-) code
                $nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre2"],ENT_QUOTES)));
				$apellido = mysqli_real_escape_string($con,(strip_tags($_POST["apellido2"],ENT_QUOTES)));
				$nombre_usuarios = mysqli_real_escape_string($con,(strip_tags($_POST["nombre_usuarios2"],ENT_QUOTES)));
                $correo = mysqli_real_escape_string($con,(strip_tags($_POST["correo2"],ENT_QUOTES)));
				
				$id_usuario=intval($_POST['mod_id']);
					
               
					// write new user's data into database
                    $sql = "UPDATE usuarios   SET nombre='".$nombre."', apellido='".$apellido."', nombre_usuarios='".$nombre_usuarios."', correo='".$correo."'
                            WHERE id_usuario='".$id_usuario."';";
                    $query_update = mysqli_query($con,$sql);

                    // if user has been added successfully
                    if ($query_update) {
                        $messages[] = "La cuenta ha sido modificada con éxito.";
                    } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
                
            
        } else {
            $errors[] = "Un error desconocido ocurrió.";
        }
		
		if (isset($errors)){
			
			?>
			<div class="alert alert-danger" role="alert">
				<button type="button" class="close" data-bs-dismiss="alert">&times;</button>
					<strong>Error!</strong> 
					<?php
						foreach ($errors as $error) {
								echo $error;
							}
						?>
			</div>
			<?php
			}
			if (isset($messages)){
				
				?>
				<div class="alert alert-success" role="alert">
						<button type="button" class="close" data-bs-dismiss="alert">&times;</button>
						<strong>¡Bien hecho!</strong>
						<?php
							foreach ($messages as $message) {
									echo $message;
								}
							?>
				</div>
				<?php
			}

?>