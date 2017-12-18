<?php
use Firebase\JWT\JWT;
class Controller_Users extends Controller_Rest
{
	private $key = 'eih12ohgsfh2o389fsag2891JK8912h3sa';
    protected $format = 'json';
 
   function post_create()
   {
        try {
            if (!isset($_POST['username']) || !isset($_POST['password']) || $_POST['username'] == "" || $_POST['password'] == "") 
            {
              $this->createResponse(400, 'Parámetros incorrectos');
            }
            $nombre = $_POST['username'];
            $password = $_POST['password'];
            if(!$this->userExists($nombre)){ //Si el usuario todavía no existe
                $props = array('nombre' => $nombre, 'contraseña' => $password, 'id_rol' => 1);
                $new = new Model_Usuarios($props);
                $new->save();
                $this->createResponse(200, 'Usuario creado', ['nombre' => $nombre]);
            }else{ //Si el usuario introducido ya existe
                $this->createResponse(400, 'El usuario ya existe');
            } 
       }
        catch (Exception $e) 
        {
            $this->createResponse(500, $e->getMessage());
        }      
        
   }
   function get_login()
   {
     	$username = $_GET['username'];
  	    $password = $_GET['password'];
      	$userDB = Model_Usuarios::find('first', array(
          	'where' => array(
              	array('username', $username),
              	array('password', $password)
          	),
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
            $this->createResponse(200, 'login correcto', ['token' => $jwt, 'username' => $username]);
      	}else{
            $this->createResponse(400, 'El usuario no existe');
      	}
   }
   function get_userToken(){
        $jwt = apache_request_headers()['Authorization'];
        if($jwt != ""){
            if($this->validateToken($jwt)){
                $token = JWT::decode($jwt, $this->key , array('HS256'));
                $this->createResponse(200, 'Usuario devuelto', $token->data);
            }else{
                $this->createResponse(400, 'No tienes permiso para realizar esta acción');
            }
        }else{
          $this->createResponse(400, 'No tienes permiso para realizar esta acción');
        }
   }
    function post_borrar(){
        $jwt = apache_request_headers()['Authorization'];
        if($this->validateToken($jwt)){
            $id = $_POST['id'];
       
            $usuario = Model_Usuarios::find($id);
            if($usuario != null){
                $usuario->delete();
                $this->createResponse(200, 'Usuario borrado', ['usuario' => $usuario]);
            }else{
                $this->createResponse(400, 'El usuario introducido no existe');
            }
          
        }else{
            $this->createResponse(400, 'No tienes permiso para realizar esta acción');
        }
      
    }
    function post_edit(){
        $jwt = apache_request_headers()['Authorization'];
        if($this->validateToken($jwt)){
            $id = $_POST['id'];
            $username = $_POST['username'];
            $password = $_POST['password'];
       
            $usuario = Model_Usuarios::find($id);
            if($usuario != null){
                $usuario->nombre = $username;
                $usuario->contraseña = $password;
                $usuario->save();
                $this->createResponse(200, 'Usuario editado', ['usuario' => $usuario]);
            }else{
                $this->createResponse(400, 'El usuario introducido no existe');
            }
            
        }else{
            $this->createResponse(400, 'No tienes permiso para realizar esta acción');
        }
    }
    function userExists($username){
        $userDB = Model_Usuarios::find('first', array(
                    'where' => array(
                        array('nombre', $username)
                    )
                )); 
        if($userDB != null){
            return true;
        }else{
            return false;
        }
   }
    function validateToken($jwt){
        $token = JWT::decode($jwt, $this->key, array('HS256'));
        $username = $token->data->username;
        $password = $token->data->password;
        $userDB = Model_Usuarios::find('all', array(
        'where' => array(
            array('nombre', $username),
            array('contraseña', $password)
            )
        ));
        if($userDB != null){
            return true;
        }else{
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