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
              $json = $this->response(array(
              'code' => 400,
              'message' => 'Required username and password',
              ));
              return $json;
            }
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];

            if(!$this->userInDB($username, $email)){
                $userData = array('username' => $username, 'password' => $password, 'email' => $email);
                $new = new Model_Users($userData);
                $new->save();
                $json = $this->response(array(
                'code' => 200,
                'message' => 'Correct data',
                'data' => $new
                ));
                return $json;
            }else{
                $json = $this->response(array(
              'code' => 400,
              'message' => 'Incorrect data',
              ));
              return $json;
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
        if (!isset($_POST['username']) || !isset($_POST['password']) || $_POST['username'] == "" || $_POST['password'] == "") 
        {
            $json = $this->response(array(
              'code' => 400,
              'message' => 'Incorrect data',
              ));
            return $json;
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
      		    'data' => [ 
                  'id' => $findUser['id'],
      		        'username' => $username,
      		        'password' => $password
      		    ]
      		);

      		$jwt = JWT::encode($token, $this->key);

          $json = $this->response(array(
              'code' => 200,
              'message' => 'Correct data',
              'data' => $token
              ));
                return $json;
      	}else{
            $json = $this->response(array(
              'code' => 400,
              'message' => 'User doesnt exist',
              ));
              return $json;
      	}
    }catch (Exception $e){
        $json = $this->response(array(
              'code' => 500,
              'message' => 'Internal error',
        ));
        return $json;
    }  
  }

  function post_edit()
  {
    try{
        $jwt = apache_request_headers()['Authorization'];
        if($this->tokenValidated($jwt))
        {

            $editPass = $_POST['password'];
            $token = JWT::decode($jwt, $this->key, array('HS256'));
            $id = $token->data->id;
           
            $user = Model_Users::find($id);
            if($user != null){
                $user->password = $editPass;
                $user->save();
                $json = $this->response(array(
                'code' => 200,
                'message' => 'User edited',
                'data' => $user
                ));
                return $json;
            }else{
                $json = $this->response(array(
              'code' => 400,
              'message' => 'User doesnt exist',
              ));
              return $json;
            }
                
        }else{
            $json = $this->response(array(
              'code' => 400,
              'message' => 'Permission denied',
              ));
              return $json;
        }
    }catch (Exception $e){
        $json = $this->response(array(
              'code' => 500,
              'message' => 'Internal error',
        ));
        return $json;
    } 
  }

  function post_delete()
  {
    try{
        $jwt = apache_request_headers()['Authorization'];

        if($this->tokenValidated($jwt))
        {
            $token = JWT::decode($jwt, $this->key, array('HS256'));
            $id = $token->data->id;
           
            $user = Model_Users::find($id);
            if($user != null){
                $user->delete();
                $json = $this->response(array(
                'code' => 200,
                'message' => 'User deleted',
                'data' => $user
                ));
                return $json;
            }else{
              $json = $this->response(array(
              'code' => 400,
              'message' => 'User doesnt exist',
              ));
              return $json;
            }
              
        }else{
            $json = $this->response(array(
              'code' => 400,
              'message' => 'Permission Denied',
        ));
        return $json;
        }
    }catch (Exception $e){
        $json = $this->response(array(
              'code' => 500,
              'message' => 'Internal error',
        ));
        return $json;
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
}