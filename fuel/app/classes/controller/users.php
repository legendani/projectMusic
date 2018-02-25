<?php
//require_once '../../../vendor/autoload.php';
use Firebase\JWT\JWT;
class Controller_Users extends Controller_Rest
{
	private $key = 'my_secret_key';
	protected $format = 'json';

	function post_create(){
		try {
			if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['email']) || $_POST['username'] == "" || $_POST['password'] == "" || $_POST['email'] == "") {
				return $this->createResponse(400, 'Faltan parámetros (username y/o password y/o email)');
			}
			$username = $_POST['username'];
			$password = $_POST['password'];
			$email = $_POST['email'];
			if(!$this->userExists($username, $email)){ //Si el usuario todavía no existe
				//Creamos privacidad para el nuevo usuario
				$props = array('username' => $username, 'password' => $password, 'email' => $email);
				$newUser = new Model_Users($props);
				$newUser->save();
				return $this->createResponse(200, 'Usuario creado', ['user' => $newUser]);
			}else{ //Si el usuario introducido ya existe
				return $this->createResponse(400, 'El usuario ya existe, username o email repetido');
			} 
		}catch (Exception $e) {
			
			return $this->createResponse(500, $e->getMessage());
		}      
				
	}

	function get_login(){
		try{
			if (!isset($_GET['username']) || !isset($_GET['password']) || $_GET['username'] == "" || $_GET['password'] == "") {
				return $this->createResponse(400, 'Faltan parámetros (username y/o password)');
			}
			$username = $_GET['username'];
			$password = $_GET['password'];
			$userDB = Model_Users::find('first', array(
					'where' => array(
							array('username', $username),
							array('password', $password)
					)
			));
			if($userDB != null){ //Si el usuario se ha logueado (existe en la BD)
				//Creación de token
				$time = time();
				$token = array(
						'iat' => $time, 
						'data' => [ 
							'id' => $userDB['id'],
							'username' => $username,
							'password' => $password
						]
				);
				$jwt = JWT::encode($token, $this->key);
				return $this->createResponse(200, 'Login correcto', ['token' => $jwt, 'username' => $username]);
			}else{
				return $this->createResponse(400, 'El usuario no existe');
			}
		}catch (Exception $e){
			
			return $this->createResponse(500, $e->getMessage());
		}  
	}

	function post_login(){
		try{
			if (!isset($_POST['username']) || 
				!isset($_POST['password']) ||
				!isset($_POST['id_device']) || 
				!isset($_POST['x']) || 
				!isset($_POST['y']) ||  
				$_POST['username'] == "" || 
				$_POST['password'] == "" ||
				$_POST['id_device'] == "" ||
				$_POST['x'] == "" ||
				$_POST['y'] == ""
				) {
				return $this->createResponse(400, 'Faltan parámetros (username y/o password y/o id_device y/o x y/o y)');
			}
			$username = $_POST['username'];
			$password = $_POST['password'];
			$id_device = $_POST['id_device'];
			$x = $_POST['x'];
			$y = $_POST['y'];
			$userDB = Model_Users::find('first', array(
					'where' => array(
							array('username', $username),
							array('password', $password)
					)
			));
			if($userDB != null){ //Si el usuario se ha logueado (existe en la BD)
				$userDB->id_device = $id_device;
				$userDB->x = $x;
				$userDB->y = $y;
				$userDB->save();
				//Creación de token
				$time = time();
				$token = array(
						'iat' => $time, 
						'data' => [ 
							'id' => $userDB['id'],
							'username' => $username,
							'password' => $password
						]
				);
				$jwt = JWT::encode($token, $this->key);
				return $this->createResponse(200, 'Usuario logueado', ['token' => $jwt, 'username' => $username]);
			}else{
				return $this->createResponse(400, 'El usuario no existe');
			}
		}catch (Exception $e){
			
			return $this->createResponse(500, $e->getMessage());
		}  
	}
	function get_comprobateemail(){
		try{
			if(!isset($_GET["email"]) || $_GET["email"] == ""){
				
				return $this->createResponse(400, 'Falta parámetro email');
			}
			$email = $_GET["email"];
			$userDB = Model_Users::find('first', array(
					'where' => array(
							array('email', $email)
					)
			));
			if($userDB != null){
				return $this->createResponse(200, 'El email existe', ['id_user' => $userDB->id]);
			}else{
				return $this->createResponse(400, 'El email no existe');
			}
		}catch(Exception $e){
			return $this->createResponse(500, $e->getMessage());
		}
	}
	function post_borrar(){
		try{
			$jwt = apache_request_headers()['Authorization'];
			if($this->validateToken($jwt)){
				$token = JWT::decode($jwt, $this->key, array('HS256'));
				$id = $token->data->id;
	 
				$usuario = Model_Users::find($id);
				if($usuario != null){
					$usuario->delete();
					return $this->createResponse(200, 'Usuario borrado', ['usuario' => $usuario]);
				}else{
					return $this->createResponse(400, 'El usuario introducido no existe');
				}
				
			}else{
				
				return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
			}
		}catch (Exception $e){
			return $this->createResponse(500, $e->getMessage());
		}  
		
	}
	function post_edit(){
		try{
			if(!isset(apache_request_headers()['Authorization']) || apache_request_headers()['Authorization'] == ""){
				return $this->createResponse(400, 'Falta el token en el header');
			}
			$jwt = apache_request_headers()['Authorization'];
			if($this->validateToken($jwt)){
				if((!isset($_POST["password"]) || $_POST["password"] == "") &&
					(!isset($_POST["description"]) || $_POST["description"] == "") &&
					(!isset($_POST["birthday"]) || $_POST["bithday"] == "") &&
					(!isset($_POST["city"]) || $_POST["city"] == "") &&
						empty($_FILES['photo'])){
					return $this->createResponse(400, 'Faltan parámetros, es necesario al menos uno (password o description o birthday o city)');
				}
				$token = JWT::decode($jwt, $this->key, array('HS256'));
				$id = $token->data->id;
	 
				$usuario = Model_Users::find($id);
				if($usuario != null){
					if(isset($_POST["password"]) && $_POST["password"] != ""){
						$password = $_POST["password"];
						$usuario->password = $password;
					}
					if (!empty($_FILES['photo'])) {
		                
		                $config = array(
		                    'path' => DOCROOT . 'assets/img',
		                    'randomize' => true,
		                    'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png'),
		                );
		                
		                Upload::process($config);
		                
		                if (Upload::is_valid())
		                {
		                    
		                    Upload::save();
		                    foreach(Upload::get_files() as $file)
		                    {
		                        
		                        $usuario->photo = $this->urlDev.$file['saved_as'];
		                    }
                		}else{
                			$this->createResponse(400, 'El archivo subido no es válido');
                		}
		                // and process any errors
		                foreach (Upload::get_errors() as $file)
		                {
		                    return $this->createResponse(500, 'Error al subir la imagen', $file);
		                }
            		}
					if(isset($_POST["description"]) && $_POST["description"] != ""){
						$description = $_POST["description"];
						$usuario->description = $description;
					}
					if(isset($_POST["birthday"]) && $_POST["birthday"] != ""){
						$birthday = $_POST["birthday"];
						$usuario->birthday = $birthday;
					}
					if(isset($_POST["city"]) && $_POST["city"] != ""){
						$city = $_POST["city"];
						$usuario->city = $city;
					}
					
					$usuario->save();
					return $this->createResponse(200, 'Usuario editado', ['user' => $usuario]);
				}else{
					return $this->createResponse(400, 'El usuario no existe');
				}
					
			}else{
				return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
			}
		}catch (Exception $e){
			return $this->createResponse(500, $e->getMessage());
		} 
			
	}
	function post_editpassword(){
		try{
			if(!isset($_POST["password"]) || $_POST["password"] == "" ||
				!isset($_POST["id_user"]) || $_POST["id_user"] == ""){
				return $this->createResponse(400, 'Faltan parámetros (password y/o id_user)');
			}
			$id_user = $_POST['id_user'];
 
			$user = Model_Users::find($id_user);
			if($user != null){
				$password = $_POST["password"];
				if (strlen($password) < 5 || strlen($password) > 12){
					return $this->createResponse(400, 'La contraseña debe tener entre 5 y 12 caracteres');
				}
				$user->password = $password;
				$user->save();
				return $this->createResponse(200, 'Password modificada', ['user' => $user]);
			}else{
				return $this->createResponse(400, 'El usuario no existe');
			}
					
		}catch (Exception $e){
			return $this->createResponse(500, $e->getMessage());
		} 
			
	}

	function get_allusers(){
		try{
			if(!isset(apache_request_headers()['Authorization']) || apache_request_headers()['Authorization'] == ""){
				return $this->createResponse(400, 'Falta el token en el header');
			}
			$jwt = apache_request_headers()['Authorization'];
			if($this->validateToken($jwt)){
				$users = Model_Users::find('all');
				return $this->createResponse(200, 'Todos los usuarios devueltos', ['users' => $users]);
			}else{
				return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
			}
		}catch(Exception $e){
			return $this->createResponse(500, $e->getMessage());
		}
	}
	
	function get_user(){
		try{
			if(!isset(apache_request_headers()['Authorization']) || apache_request_headers()['Authorization'] == ""){
				return $this->createResponse(400, 'Falta el token en el header');
			}
			if(!isset($_GET["id_user"]) || $_GET["id_user"] == ""){
				return $this->createResponse(400, 'Falta el parámetro id_user');
			}
			$id_user = $_GET['id_user'];
			$jwt = apache_request_headers()['Authorization'];
			if($this->validateToken($jwt)){
				$user = Model_Users::find($id_user);
				return $this->createResponse(200, 'Usuario devuelto', ['user' => $user]);
			}else{
				return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
			}
		}catch(Exception $e){
			return $this->createResponse(500, $e->getMessage());
		}
	}
	
	private function userExists($username, $email){
		$userDB = Model_Users::find('all', array(
					'where' => array(
							array('username', $username),
							'or' => array(
								array('email', $email),
							),
					)
				)); 
		if($userDB != null){
			return true;
		}else{
			return false;
		}
	}

	function validateToken($jwt){
		try{
			$token = JWT::decode($jwt, $this->key, array('HS256'));
			$username = $token->data->username;
			$password = $token->data->password;
			$userDB = Model_Users::find('all', array(
			'where' => array(
					array('username', $username),
					array('password', $password)
					)
			));
			if($userDB != null){
				return true;
			}else{
				return false;
			}
		}catch(Exception $e){
			return false;
		}
		
	}
	function createResponse($code, $message, $data = []){
		$json = $this->response(array(
					'code' => $code,
					'message' => $message,
					'data' => $data
				));
		return $json;
	}
}