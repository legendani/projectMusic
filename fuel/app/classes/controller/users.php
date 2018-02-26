<?php
use Firebase\JWT\JWT;
class Controller_Users extends Controller_Rest
{
    private $key = 'my_secret_key';
    protected $format = 'json';

    public function post_create()
    {
        $input = $_POST;

        if (array_key_exists('username', $input)&& array_key_exists('email', $input) && array_key_exists('passwordRepeat', $input) && array_key_exists('password', $input) && array_key_exists('rol', $input) && array_key_exists('x', $input) && array_key_exists('y', $input) && array_key_exists('id_device', $input)){

            $userDB = Model_Users::find('first', array(
                'where' => array(
                    array('username', $input['username'])
                ),
            ));

            $emailDB = Model_Users::find('first', array(
                'where' => array(
                    array('email', $input['email'])
                ),
            ));

            if ($input['password'] == $input['passwordRepeat']){
                if(count($emailDB) < 1){
                    if(count($userDB) < 1){
                        $new = new Model_Users();
                        $new->username = $input['username'];
                        $new->email = $input['email'];
                        $new->password = $input['password'];
                        $new->id_roles = $input['rol'];
                        $new->x = $input['x'];
                        $new->y = $input['y'];
                        $new->id_device = $input['id_device'];
                        $new->profile_photo = $input['profile_photo'];
                        $new->birthday = $input['birthday'];
                        $new->city = $input['city'];
                        $new->description = $input['description'];
                        $new->id_privacity = $input['id_privacity'];
                        $new->save();
                        $this->Mensaje('200', 'user created', $input);
                    } else {
                        $this->Mensaje('400', 'User already exist', $input['username']);
                    }
                } else {
                    $this->Mensaje('400', 'Email in use', $input['email']);
                }
            }else {
                $this->Mensaje('400', 'Passwords dont match', $input['password']);
            }
        } else{
            $this->Mensaje('400', 'Invalid arguments', $input);
        }    
    }

    public function get_login()
    {
        $username = $_GET['username'];
        $password = $_GET['password'];

        if(!empty($username) && !empty($password)){
            $userDB = Model_Users::find('first', array(
             'where' => array(
                 array('username', $username),
                 array('password', $password)
            ),
        ));
            if(count($userDB) == 1){
             $time = time();
             $token = array(
                'iat' => $time,
                'data' => [ // información del usuario
                'id' => $userDB->id,
                'username' => $username,
                'password'=> $password
            ]
        );
             $jwt = JWT::encode($token, $this->key);

             $this->Mensaje('200', 'User logged', $jwt);
         	} else {
            $this->Mensaje('400', 'User not valid', $username);
        	}
    	}else {
        	$this->Mensaje('400', 'Empty arguments', $username);
    	}	
	}

	public function get_users(){
	    $jwt = apache_request_headers()['Authorization'];
	    $token = JWT::decode($jwt, $this->key , array('HS256'));
	    $username = $token->data->username;
	    $password = $token->data->password;

	    $userDB = Model_Users::find('all', array(
	        'where' => array(
	            array('username', $username),
	            array('password', $password)
	            ),
	        ));

	    if(count($userDB) == 1){
	        $allUsers = Model_Users::find('all');
	        $this->Mensaje('200', 'Users list', $allusers);
	    }else {
	        $this->Mensaje('400', 'User not valid', $username);
	    }
	}

	public function post_modify(){
	    $jwt = apache_request_headers()['Authorization'];
	    try{
	        $token = JWT::decode($jwt, $this->key , array('HS256'));
	        
	        $username = $token->data->username;
	        $password = $token->data->password;
	        $input = $_POST;

	        $userDB = Model_Users::find('first', array(
	            'where' => array(
	                array('username', $username),
	                array('password', $password)
	                ),
	            ));

	        if($userDB != null){
	            $userDB->password = $input['password'];
	            $userDB->save();

	            $this->Mensaje('200', 'New password saved', $input['password']);
	        } else {
	            $this->Mensaje('400', 'User not valid', $input['username']);
	        }
	    } catch(Exception $e) {
	        $this->Mensaje('400', 'Verification error', "Error");
	    } 
	}

	public function post_deleteUser(){
	    $jwt = apache_request_headers()['Authorization'];

	    if(!empty($jwt)){
	        $token = JWT::decode($jwt, $this->key , array('HS256'));
	        $id = $token->data->id;

	        $userDB = Model_Users::find('first', array(
	            'where' => array(
	                array('id', $id)
	                ),
	            ));

	        if($userDB != null){
	            $userDB->delete();
	            $this->Mensaje('200', 'User deleted', $userDB);
	        } else {
	            $this->Mensaje('400', 'User not valid', $input['username']);
	        }
	    } else {
	        $this->Mensaje('400', 'Empty token', $jwt);
	    }
	}

	public function get_recoverPassword(){
	    $email = $_GET['email'];

	    try{
	        $userDB = Model_Users::find('first', array(
	            'where' => array(
	                array('email', $email)
	                ),
	            ));

	        if($userDB != null){
	            $this->Mensaje('200', 'email correcto', $userDB);
	        } else {
	            $this->Mensaje('400', 'email invalido', $email);
	        }
	    }catch(Exception $e) {
	        $this->Mensaje('500', 'Error de servidor', "Error");
	    }
	}

	function post_configAdmin(){
	    $userDB = Model_Users::find('first', array(
	        'where' => array(
	            array('username', 'admin')
	            ),
	        ));

	    $emailDB = Model_Users::find('first', array(
	        'where' => array(
	            array('email', 'admin@admin.com')
	            ),
	        ));

	    if(count($emailDB) < 1){
	        if(count($userDB) < 1){
	            $new = new Model_Users();
	            $new->username = 'admin';
	            $new->email = 'admin@admin.com';
	            $new->password = 'password';
	            $new->id_rol = '1';
	            $new->x = '0';
	            $new->y = '0';
	            $new->id_device = '0';
	            $new->profile_photo = 'photo';
	            $new->birthday = '01/01/2018';
	            $new->city = 'madrid';
	            $new->description = 'admin';
	            $new->id_privacity = '1';
	            $new->save();
	            $this->Mensaje('200', 'Administrator created', 'admin');
	        } else {
	            $this->Mensaje('400', 'User already exist', $input['username']);
	        }
	    } else {
	        $this->Mensaje('400', 'Email in use', $input['email']);
	    }     
	}

	function post_followUser(){
	    $jwt = apache_request_headers()['Authorization'];
	    $token = JWT::decode($jwt, $this->key , array('HS256'));
	    $idFollower = $token->data->id;
	    $idFollowed = $_POST['id_followed'];

	    $userDB = Model_Users::find('all', array(
	        'where' => array(
	            array('id', $idFollower)
	            ),
	        ));

	    $userFollowDB = Model_Follow::find('all', array(
	        'where' => array(
	            array('id_follower', $idFollower),
	            array('id_followed', $idFollowed)
	            ),
	        ));

	    if(empty($userFollowDB)){
	        if(!empty($userDB)){
	            $new = new Model_Follow();
	            $new->id_follower = $idFollower;
	            $new->id_followed = $idFollowed;
	            $new->save();
	            $this->Mensaje('200', 'User followed', $userDB);
	        } else {
	            $this->Mensaje('400', 'User not found', $idFollowed);
	        }
	    }else {
	        $this->Mensaje('400', 'usuario ya seguido', $idFollowed);
	    }
	}

	function post_unFollowUser(){
	    $jwt = apache_request_headers()['Authorization'];
	    $token = JWT::decode($jwt, $this->key , array('HS256'));
	    $idFollower = $token->data->id;
	    $idUnfollowed = $_POST['id_unfollowed'];

	    $userDB = Model_Users::find('all', array(
	        'where' => array(
	            array('id', $idFollower)
	            ),
	        ));

	    $userUnfollowDB = Model_Follow::find('first', array(
	        'where' => array(
	            array('id_follower', $idFollower),
	            array('id_followed', $idUnfollowed)
	            ),
	        ));
	    
	    if(!empty($userUnfollowDB)){
	        if(!empty($userDB)){
	            $userUnfollowDB->delete();
	            $this->Mensaje('200', 'User unfollowed', $userDB);
	        } else {
	            $this->Mensaje('400', 'User not found', $idUnfollowed);
	        }
	    }else {
	        $this->Mensaje('400', 'User not followed', $idUnfollowed);
	    }
	}

	function get_getFollowedUsers(){
	    $jwt = apache_request_headers()['Authorization'];
	    $token = JWT::decode($jwt, $this->key , array('HS256'));
	    $id = $token->data->id;

	    $userDB = Model_Users::find('first', array(
	        'where' => array(
	            array('id', $id)
	            ),
	        ));

	    if(count($userDB) == 1){
	        $followedUsers = Model_Follow::find('all', array(
	            'where' => array(
	                array('id_follower', $id)
	                ),
	            ));
	        $this->Mensaje('200', 'Followed users list', $users);
	    }else {
	        $this->Mensaje('400', 'User not valid', $username);
	    }
	}

	function Mensaje($code, $message, $data){
	    $json = $this->response(array(
	        'code' => $code,
	        'message' => $message,
	        'data' => $data
	    ));
	    return $json;
	}
}