<?php
use Firebase\JWT\JWT;
class Controller_Lists extends Controller_Rest{
	private $key = 'my_secret_key';
	protected $format = 'json';
	function post_create()
   	{
   		
        try {
            $jwt = apache_request_headers()['Authorization'];
            if (empty($_POST['title'])) 
            {
                return $this->createResponse(400, 'Parámetros incorrectos, falta parámetro title');
            }else{
                if($this->validateToken($jwt)){
                    $token = JWT::decode($jwt, $this->key, array('HS256'));
                    $id_user = $token->data->id;
                    $title = $_POST['title'];
                    if(!$this->listExists($id_user, $title)){
                        $props = array('id_user' => $id_user, 'title' => $title, 'editable' => true);
                        $new = new Model_Lists($props);
                        $new->save();
                        return $this->createResponse(200, 'Lista creada', ['list' => $new]);
                    }else{
                        return $this->createResponse(400, 'Lista ya creada por este usuario');
                    }
                }else{
                    return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
                }
            }   
	  
        }
        catch (Exception $e) 
        {
            return $this->createResponse(500, $e->getMessage());
        }      
   	}
   	function get_lists(){
        try{
            $jwt = apache_request_headers()['Authorization'];
            if($this->validateToken($jwt)){
                if(!isset($_GET['id_user']) || $_GET['id_user'] == ""){
                    $token = JWT::decode($jwt, $this->key, array('HS256'));
                    $id_user = $token->data->id;
                }else{
                    $id_user = $_GET['id_user'];
                    $user = Model_Users::find($id_user);
                    $privacity = Model_Privacity::find($user->id_privacity);
                    if($privacity->lists == 0){
                        return $this->createResponse(400, 'El usuario no permite que se vean sus listas');
                    }
                }
                $user = Model_Users::find($id_user);
                if($user == null){
                    return $this->createResponse(400, 'El usuario no existe');
                }
              
                $lists = Model_Lists::find('all', array(
                    'where' => array(
                        array('id_user', $id_user),
                  )));
                if($lists != null){
                    return $this->createResponse(200, 'Listas devueltas', ['lists' => $lists]);
                }else{
                    return $this->createResponse(200, 'El usuario no tiene listas creadas');
                }
            }else{
              return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
            }
        }catch (Exception $e) {
            return $this->createResponse(500, $e->getMessage());
        }  
    }
    function get_list(){
        try{
            $jwt = apache_request_headers()['Authorization'];
            if($this->validateToken($jwt)){
                if(!isset($_GET['id_list']) || $_GET['id_list'] == ""){
                    return $this->createResponse(400, 'Parámetros incorrectos');
                }
                $id_list = $_GET['id_list'];
              
                $list = Model_Lists::find('first', array(
                    'where' => array(
                        array('id', $id_list),
                  )));
                if($list != null){
                    return $this->createResponse(200, 'Lista devuelta', ['list' => $list]);
                }else{
                    return $this->createResponse(400, 'La lista no existe');
                }
            }else{
              return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
            }
        }catch (Exception $e) {
            return $this->createResponse(500, $e->getMessage());
        } 
    }
    function post_delete(){
        try{
            $jwt = apache_request_headers()['Authorization'];
            if($this->validateToken($jwt)){
                $token = JWT::decode($jwt, $this->key, array('HS256'));
                if(!isset($_POST['id_list']) || $_POST['id_list'] == ""){
                    return $this->createResponse(400, 'Falta parámetro obligatorio id_list');
                }
                $id_list = $_POST['id_list'];
                $list = Model_Lists::find($id_list);
                if($list == null){
                    return $this->createResponse(400, 'La lista no existe');
                }
           
                $list = Model_Lists::find('first', array(
                    'where' => array(
                        array('id', $id_list),
                        array('id_user', $token->data->id),
                        array('editable', true)
                    )
                ));
                if($list != null){
                    $list->delete();
                    return $this->createResponse(200, 'Lista borrada correctamente', ['list' => $list]);
                }else{
                    return $this->createResponse(400, 'No puedes borrar esta lista');
                }
              
            }else{
                return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
            }
        }catch (Exception $e) {
            return $this->createResponse(500, $e->getMessage());
        } 
    }
    function post_edit(){
        try{
            $jwt = apache_request_headers()['Authorization'];
            if($this->validateToken($jwt)){
                $token = JWT::decode($jwt, $this->key, array('HS256'));
                $id_user = $token->data->id;
                if(!isset($_POST['id_list']) || $_POST['id_list'] == "" || !isset($_POST['title']) || $_POST['title'] == ""){
                    
                    return $this->createResponse(400, 'Parámetros incorrectos, faltan parámetros obligatorios (id_list y title)');
                }
                $id_list = $_POST['id_list'];
                $title = $_POST['title'];
                $list = Model_Lists::find($id_list);
                if($list == null){
                    return $this->createResponse(400, 'La lista no existe');
                }
                $list = Model_Lists::find('first', array(
                    'where' => array(
                        array('id', $id_list),
                        array('id_user', $id_user),
                        array('editable', true)
                    )
                ));
                if($list != null){
                    $list->title = $title;
                    $list->save();
                    return $this->createResponse(200, 'Lista editada', ['list' => $list]);
                }else{
                    return $this->createResponse(400, 'No puedes editar esta lista');
                }
            }else{
                return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
            }
        }catch (Exception $e) {
            return $this->createResponse(500, $e->getMessage());
        }
        
    }
   function post_addSong(){
        try{
            $jwt = apache_request_headers()['Authorization'];
            if($this->validateToken($jwt)){
                if(!isset($_POST['id_song']) || $_POST['id_song'] == "" || !isset($_POST['id_list']) || $_POST['id_list'] == ""){
                    
                    return $this->createResponse(400, 'Parámetros incorrectos, faltan parámetros obligatorios (id_song y/o id_list)');
                }
                $id_song = $_POST['id_song'];
                $id_list = $_POST['id_list'];
                $song = Model_Songs::find($id_song);
                if($song == null){
                    return $this->createResponse(400, 'La canción no existe');
                }
                $contain = Model_Contain::find('first', array(
                    'where' => array(
                        array('id_list', $id_list),
                        array('id_song', $id_song)
                    )
                ));
                if($contain == null){
                    $token = JWT::decode($jwt, $this->key, array('HS256'));
                    $id_user = $token->data->id;
                    $list = Model_Lists::find('first', array(
                        'where' => array(
                            array('id', $id_list),
                            array('id_user', $id_user)
                        )
                    ));
                    if($list != null){
                        $props = array('id_list' => $id_list, 'id_song' => $id_song);
                        $new = new Model_Contain($props);
                        $new->save();
                        return $this->createResponse(200, 'Canción añadida a la lista', ['list' => $list]);
                        
                    }else{
                        return $this->createResponse(400, 'No tienes permiso para añadir canciones a esa lista');
                    }
                }else{
                     return $this->createResponse(400, 'La canción ya pertenece a la lista');
                }
            }else{
                return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
            }
        }catch(Exception $e){
            return $this->createResponse(500, $e->getMessage());
        }
   }
   function listExists($id_user, $title){
      $lists = Model_Lists::find('all', array(
                  'where' => array(
                      array('id_user', $id_user),
                      array('title', $title)
                )));
      if($lists != null){
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
    function validateToken($jwt){
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
    }
}