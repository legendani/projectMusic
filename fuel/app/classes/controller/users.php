<?php
use Firebase\JWT\JWT;
class Controller_Users extends Controller_Rest
{
	private $key = 'lihkj3n2oirfhne982h3brf7sd89fyhgb738uibHJGu892734bjkb';
 
   function post_create()
   {
        try {
            if (!isset($_POST['username']) || !isset($_POST['password']) || !isset($_POST['email']) || $_POST['username'] == "" || $_POST['password'] == "" || $_POST['email'] == "") 
            {
              $this->response(400, 'Required username and password');
            }
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];

            if(!$this->userInDB($username, $email)){
                $userData = array('username' => $username, 'password' => $password, 'email' => $email);
                $new = new Model_Users($userData);
                $new->save();
                $this->response(200, 'Usuario creado', ['user' => $new]);
            }else{
                $this->response(400, 'User already created');
            } 
       }
        catch (Exception $e) 
        {
            $this->response(500, $e->getMessage());
        }      
        
   }
   function get_login()
   {
    try{
        if (!isset($_POST['username']) || !isset($_POST['password']) || $_POST['username'] == "" || $_POST['password'] == "") {
            $this->response(400, 'Incorrect username or password');
        }
     	  $username = $_GET['username'];
  	    $password = $_GET['password'];

      	$findUser = Model_Users::find('first', array(
          	'where' => array(
              	array('username', $username),
              	array('password', $password)
          	),
      	));
      	if($findUser != null){
      		$time = time();
      		$token = array(
      		    'iat' => $time, 
      		    'data' => [ 
                  'id' => $findUser['id'],
      		        'username' => $username,
      		        'password' => $password
      		    ]
      		);

      		$jwt = JWT::encode($token, $this->key);

            $this->response(200, 'Correct Data', ['token' => $jwt]);
      	}else{
            $this->response(400, 'User doesnt exist');
      	}
    }catch (Exception $e){
        $this->response(500, $e->getMessage());
    }  
  }

  function post_edit()
  {
    try{
        $jwt = apache_request_headers()['Authorization'];
        if($this->tokenValidated($jwt)){

            $editPass = $_POST['password'];
            $token = JWT::decode($jwt, $this->key, array('HS256'));
            $id = $token->data->id;
           
            $user = Model_Users::find($id);
            if($user != null){
                $user->password = $editPass;
                $user->save();
                $this->response(200, 'Data saved', ['user' => $user]);
            }else{
                $this->response(400, 'User doesnt exist');
            }
                
        }else{
            $this->response(400, 'Permission denied');
        }
    }catch (Exception $e){
        $this->response(500, $e->getMessage());
    } 
  }

  function post_delete()
  {
    try{
        $jwt = apache_request_headers()['Authorization'];

        if($this->tokenValidated($jwt)){
            $token = JWT::decode($jwt, $this->key, array('HS256'));
            $id = $token->data->id;
           
            $user = Model_Users::find($id);
            if($user != null){
                $user->delete();
                $this->response(200, 'User deleted', ['user' => $user]);
            }else{
                $this->response(400, 'User doesnt exist');
            }
              
        }else{
            $this->response(400, 'Permission denied');
        }
    }catch (Exception $e){
        $this->response(500, $e->getMessage());
    }  
  }

  function userInDB($username, $email)
  {
    $userData = Model_Users::find('all', array(
                    'where' => array(
                      array('username', $username),
                        'or' => array(
                      array('email', $email),
                      ),
                    )
                )); 
    if($userData != null){
        return true;
    }else{
        return false;
    }
  }

  function tokenValidated($jwt)
  {
    $token = JWT::decode($jwt, $this->key, array('HS256'));
    $username = $token->data->username;
    $password = $token->data->password;
    $userData = Model_Users::find('all', array(
    'where' => array(
        array('username', $username),
        array('password', $password)
        )
    ));
    if($userData != null){
        return true;
    }else{
        return false;
    }
  }

  function response($code, $message, $data = [])
  {
    $json = $this->response(array(
              'code' => $code,
              'message' => $message,
              'data' => $data
        ));
    return $json;
  }
}