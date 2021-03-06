<?php
//file_put_contents("../../log.txt","nuevo usuario iniciado"." linea 2"."\n",FILE_APPEND);
include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
// checking for minimum PHP version
if (version_compare(PHP_VERSION, '5.3.7', '<')) {
    exit("Sorry, Simple PHP Login does not run on a PHP version smaller than 5.3.7 !");
} else if (version_compare(PHP_VERSION, '5.5.0', '<')) {
    // if you are using PHP 5.3 or PHP 5.4 you have to include the password_api_compatibility_library.php
    // (this library adds the PHP 5.5 password hashing functions to older versions of PHP)
    require_once("../../libraries/password_compatibility_library.php");
}		
		if (empty($_POST['nombre'])){
			$errors[] = "Nombres vacíos";
		} elseif (empty($_POST['apellido'])){
			$errors[] = "Apellidos vacíos";
		}  elseif (empty($_POST['nombre_usuarios'])) {
            $errors[] = "Nombre de usuario vacío";
        } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            $errors[] = "Contraseña vacía";
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $errors[] = "la contraseña y la repetición de la contraseña no son lo mismo";
        } elseif (strlen($_POST['user_password_new']) < 6) {
            $errors[] = "La contraseña debe tener como mínimo 6 caracteres";
        } elseif (strlen($_POST['nombre_usuarios']) > 64 || strlen($_POST['nombre_usuarios']) < 2) {
            $errors[] = "Nombre de usuario no puede ser inferior a 2 o más de 64 caracteres";
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['nombre_usuarios'])) {
            $errors[] = "Nombre de usuario no encaja en el esquema de nombre: Sólo aZ y los números están permitidos , de 2 a 64 caracteres";
        } elseif (empty($_POST['correo'])) {
            $errors[] = "El correo electrónico no puede estar vacío";
        } elseif (strlen($_POST['correo']) > 64) {
            $errors[] = "El correo electrónico no puede ser superior a 64 caracteres";
        } elseif (!filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Su dirección de correo electrónico no está en un formato de correo electrónico válida";
        } elseif (
			!empty($_POST['nombre_usuarios'])
			&& !empty($_POST['nombre'])
			&& !empty($_POST['apellido'])
            && strlen($_POST['nombre_usuarios']) <= 64
            && strlen($_POST['nombre_usuarios']) >= 2
            && preg_match('/^[a-z\d]{2,64}$/i', $_POST['nombre_usuarios'])
            && !empty($_POST['correo'])
            && strlen($_POST['correo']) <= 64
            && filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL)
            && !empty($_POST['user_password_new'])
            && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
        ) {
            require_once ("../../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
			require_once ("../../config/conexion.php");//Contiene funcion que conecta a la base de datos
			
				// escaping, additionally removing everything that could be (html/javascript-) code
                $nombre = mysqli_real_escape_string($con,(strip_tags($_POST["nombre"],ENT_QUOTES)));
				$apellido = mysqli_real_escape_string($con,(strip_tags($_POST["apellido"],ENT_QUOTES)));
				$nombre_usuarios = mysqli_real_escape_string($con,(strip_tags($_POST["nombre_usuarios"],ENT_QUOTES)));
                $correo = mysqli_real_escape_string($con,(strip_tags($_POST["correo"],ENT_QUOTES)));
				$user_password = $_POST['user_password_new'];
				$fecha=date("Y-m-d H:i:s");
                // crypt the user's password with PHP 5.5's password_hash() function, results in a 60 character
                // hash string. the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using
                // PHP 5.3/5.4, by the password hashing compatibility library
				$user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);
					
                // check if user or email address already exists
                $sql = "SELECT * FROM usuarios   WHERE nombre_usuarios = '" . $nombre_usuarios . "' OR correo = '" . $correo . "';";
                //file_put_contents("../../log.txt",$sql." linea 65"."\n",FILE_APPEND);
                $query_check_nombre_usuarios = mysqli_query($con,$sql);
				$query_check_user=mysqli_num_rows($query_check_nombre_usuarios);
                if ($query_check_user == 1) {
                    $errors[] = "Lo sentimos , el nombre de usuario ó la dirección de correo electrónico ya está en uso.";
                } else {
					// write new user's data into database
                    $sql = "INSERT INTO usuarios   (nombre, apellido, nombre_usuarios, user_password_hash, correo, fecha)
                            VALUES('".$nombre."','".$apellido."','" . $nombre_usuarios . "', '" . $user_password_hash . "', '" . $correo . "','".$fecha."');";
                    $query_new_user_insert = mysqli_query($con,$sql);

                    // if user has been added successfully
                    if ($query_new_user_insert) {
                        $messages[] = "La cuenta ha sido creada con éxito.";
                    } else {
                        $errors[] = "Lo sentimos , el registro falló. Por favor, regrese y vuelva a intentarlo.";
                    }
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