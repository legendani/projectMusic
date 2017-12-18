<?php
use Firebase\JWT\JWT;
class Controller_Lists extends Controller_Rest{
	private $key = 'eih12ohgsfh2o389fsag2891JK8912h3sa';
	protected $format = 'json';
	function post_create()
   	{
   		$jwt = apache_request_headers()['Authorization'];
        try {
            if (!isset($_POST['title']) || $_POST['title'] == "") 
            {
                $this->createResponse(400, 'Parámetros incorrectos');
            }
            if($this->validateToken($jwt)){
                $token = JWT::decode($jwt, $this->key, array('HS256'));
                $id_user = $token->data->id;
                $title = $_POST['title'];
              if(!$this->listExists($id_user, $title)){
                  $props = array('id_usuario' => $id_user, 'titulo' => $title);
                  $new = new Model_Listas($props);
                  $new->save();
                  $this->createResponse(200, 'Lista creada', ['list' => $new]);
              }else{
                  $this->createResponse(400, 'Lista ya creada por este usuario');
              }
            }else{
                  $this->createResponse(400, 'El token no es válido');
            }
	  
        }
        catch (Exception $e) 
        {
            $this->createResponse(500, $e->getMessage());
        }      
   	}
   	function get_lists(){
        $jwt = apache_request_headers()['Authorization'];
        if($this->validateToken($jwt)){
          $token = JWT::decode($jwt, $this->key, array('HS256'));
          $id_user = $token->data->id;
          
          $lists = Model_Listas::find('all', array(
    		    'where' => array(
        		    array('id_usuario', $id_user),
    		  )));
          if($lists != null){
            $this->createResponse(200, 'Listas devueltas', ['lists' => $lists]);
          }else{
            $this->createResponse(200, 'No hay listas', ['lists' => null]);
          }
        }else{
          $this->createResponse(400, 'No tienes permiso para realizar esta acción');
        }
    }
    function post_borrar(){
    	$jwt = apache_request_headers()['Authorization'];
        if($this->validateToken($jwt)){
            $token = JWT::decode($jwt, $this->key, array('HS256'));
            $id = $_POST['id'];
       
            $list = Model_Listas::find('first', array(
                'where' => array(
                    array('id', $id),
                    array('id_usuario', $token->data->id)
                )
            ));
        if ($list != null){
            $list->delete();
            $this->createResponse(200, 'Lista borrada correctamente', ['list' => $list]);
        }else{
            $this->createResponse(400, 'No puedes realizar esta acción');
        }
          
        }else{
            $this->createResponse(400, 'No tienes permiso para realizar esta acción');
        }
    }
    function post_edit(){
        $jwt = apache_request_headers()['Authorization'];
        if($this->validateToken($jwt)){
            $token = JWT::decode($jwt, $this->key, array('HS256'));
            $id_user = $token->data->id;
            $id = $_POST['id'];
            $title = $_POST['title'];
            $list = Model_Listas::find('first', array(
                'where' => array(
                    array('id', $id),
                    array('id_usuario', $id_user)
                )
            ));
            if($list != null){
                $list->titulo = $title;
                $list->save();
                $this->createResponse(200, 'Lista editada', ['list' => $list]);
            }else{
                $this->createResponse(400, 'No puedes realizar esta acción');
            }
        }else{
            $this->createResponse(400, 'No tienes permiso para realizar esta acción');
        }
    }
   function post_addSong(){
    try{
        $jwt = apache_request_headers()['Authorization'];
        if($this->validateToken($jwt)){
            $id_cancion = $_POST['id_cancion'];
            $id_lista = $_POST['id_lista'];
            $contener = Model_Contener::find('first', array(
                'where' => array(
                    array('id_lista', $id_lista),
                    array('id_cancion', $id_cancion)
                )
            ));
            if($contener == null){
                $token = JWT::decode($jwt, $this->key, array('HS256'));
                $id_user = $token->data->id;
                $list = Model_Listas::find('first', array(
                    'where' => array(
                        array('id', $id_lista),
                        array('id_usuario', $id_user)
                    )
                ));
                if($list != null){
                    $props = array('id_cancion' => $id_cancion, 'id_lista' => $id_lista);
                    $new = new Model_Contener($props);
                    $new->save();
                    $this->createResponse(200, 'Canción añadida a la lista', ['list' => $list]);
                    
                }else{
                    $this->createResponse(400, 'No tienes permiso para añadir canciones a esa lista');
                }
           }else{
                 $this->createResponse(400, 'La canción ya pertenece a la lista');
            }
        }else{
            $this->createResponse(400, 'No tienes permiso para realizar esta acción');
        }
    }catch(Exception $e){
        $this->createResponse(500, $e->getMessage());
    }
   }
   function listExists($id_user, $title){
      $lists = Model_Listas::find('all', array(
                  'where' => array(
                      array('id_usuario', $id_user),
                      array('titulo', $title)
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
}