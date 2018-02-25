<?php
use Firebase\JWT\JWT;
class Controller_News extends Controller_Rest{
	private $key = 'my_secret_key';
	protected $format = 'json';
	function post_create(){
		try {
            $jwt = apache_request_headers()['Authorization'];
            if (!isset($_POST['title']) || $_POST['title'] == "" || $_POST['description'] == "" || !isset($_POST['description'])) 
            {
                return $this->createResponse(400, 'Faltan parámetros obligatorios (title y/o description)');
            }
            if($this->validateToken($jwt)){
            	$token = JWT::decode($jwt, $this->key, array('HS256'));
                $id_user = $token->data->id;
            	$title = $_POST['title'];
            	$description= $_POST['description'];
            	if(strlen($title) > 100){
            		return $this->createResponse(400, 'El título debe contener como máximo 100 caracteres');
            	}
            	if(strlen($description) > 1000){
            		return $this->createResponse(400, 'La descripción debe contener como máximo 1000 caracteres');
            	}
	            if(!$this->noticeExists($title)){
	                $props = array('title' => $title, 'description' => $description, 'id_user' => $id_user);
	                $newNotice = new Model_News($props);
	                $newNotice->save();
	                return $this->createResponse(200, 'Noticia creada', ['notice' => $newNotice]);
	            }else{
	                return $this->createResponse(400, 'Noticia ya existente');
	            }
	        }else{
	        	return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
	        }
        }
        catch (Exception $e) 
        {
            return $this->createResponse(500, $e->getMessage());
        }    
	}
	function get_news(){
        try{
            $jwt = apache_request_headers()['Authorization'];
            if($this->validateToken($jwt)){
              
                $news = Model_News::find('all');
                if($news != null){
                    $this->createResponse(200, 'Noticias devueltas', ['news' => $news]);
                }else{
                    $this->createResponse(200, 'No hay noticias');
                }
            }else{
              $this->createResponse(400, 'No tienes permiso para realizar esta acción');
            }
        }catch (Exception $e) {
            $this->createResponse(500, $e->getMessage());
        }
    }
    function get_notice(){
        try{
            $jwt = apache_request_headers()['Authorization'];
            if($this->validateToken($jwt)){
				if (!isset($_GET['id']) || $_GET['id'] == "") 
		        {
	                return $this->createResponse(400, 'Falta parámetro obligatorio (id)');
	            }
	            $id = $_GET['id'];
                $notice = Model_News::find($id);
                if($notice != null){
                    $this->createResponse(200, 'Noticia devuelta', ['notice' => $notice]);
                }else{
                    $this->createResponse(400, 'No existe la noticia con ese id');
                }
            }else{
              $this->createResponse(400, 'No tienes permiso para realizar esta acción');
            }
        }catch (Exception $e) {
            $this->createResponse(500, $e->getMessage());
        }
    }
    function get_ownnews(){
        try{
            $jwt = apache_request_headers()['Authorization'];
            if($this->validateToken($jwt)){
            	$token = JWT::decode($jwt, $this->key, array('HS256'));
                $id_user = $token->data->id;
              
                $news = Model_News::find('all', array(
						'where' => array(
							array('id_user' => $id_user)
					)));
                if($news != null){
                    $this->createResponse(200, 'Noticias propias devueltas', ['news' => $news]);
                }else{
                    $this->createResponse(200, 'No has publicado ninguna noticia');
                }
            }else{
              $this->createResponse(400, 'No tienes permiso para realizar esta acción');
            }
        }catch (Exception $e) {
            $this->createResponse(500, $e->getMessage());
        }
    }
    function get_nearnews(){
        try{
            $jwt = apache_request_headers()['Authorization'];
            if($this->validateToken($jwt)){
            	$token = JWT::decode($jwt, $this->key, array('HS256'));
                $id_user = $token->data->id;
              
                $news = Model_News::find('all', array(
						'where' => array(
							array('id_user' => $id_user)
					)));
                if($news != null){
                    $this->createResponse(200, 'Noticias propias devueltas', ['news' => $news]);
                }else{
                    $this->createResponse(200, 'No hay noticias propias');
                }
            }else{
              $this->createResponse(400, 'No tienes permiso para realizar esta acción');
            }
        }catch (Exception $e) {
            $this->createResponse(500, $e->getMessage());
        }
    }
	function post_edit(){
		try {
            $jwt = apache_request_headers()['Authorization'];
            if($this->validateToken($jwt)){
            	$token = JWT::decode($jwt, $this->key, array('HS256'));
                $id_user = $token->data->id;
	            if (!isset($_POST['id_notice']) || $_POST['id_notice'] == "") 
	            {
	                return $this->createResponse(400, 'Falta parámetro (id_notice)');
	            }
	            $id_notice = $_POST['id_notice'];
	            $notice = Model_News::find('first', array(
						'where' => array(
							array('id' => $id_notice),
							array('id_user' => $id_user)
					)));
	            if($notice == null){
	            	return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
	            }
	            if (empty($_POST['title']) && empty($_POST['description'])) 
	            {
	                return $this->createResponse(400, 'Falta alguno de los parámetros (title o description)');
	            }
	            $notice = Model_News::find('first', array(
						'where' => array(
							array('id' => $id_notice)
					)));
	            if($notice == null){
	            	return $this->createResponse(400, 'La noticia no existe');
	            }
	            if(!empty($_POST['title'])){
	            	if(strlen($_POST['title']) > 100){
	            		return $this->createResponse(400, 'El título debe contener como máximo 100 caracteres');
	            	}
	            	if(!$this->noticeExists($_POST['title'])){
	            		$notice->title = $_POST['title'];
	            	}else{
	            		return $this->createResponse(400, 'Noticia ya existente');
	            	}
	            }
	            if(!empty($_POST['description'])){
	            	if(strlen($_POST['description']) > 1000){
	            		return $this->createResponse(400, 'La descripción debe contener como máximo 1000 caracteres');
	            	}
	            	$notice->description = $_POST['description'];
	            }
	            $notice->save();
	            return $this->createResponse(200, 'Noticia editada', ['notice' => $notice]);
	        }else{
	        	return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
	        }
        }catch (Exception $e) 
        {
            return $this->createResponse(500, $e->getMessage());
        }
	}
	function post_delete(){
		try {
            $jwt = apache_request_headers()['Authorization'];
            if($this->validateToken($jwt)){
            	$token = JWT::decode($jwt, $this->key, array('HS256'));
                $id_user = $token->data->id;
	            if (!isset($_POST['id_notice']) || $_POST['id_notice'] == "") 
	            {
	                return $this->createResponse(400, 'Falta parámetro (id_notice)');
	            }
	            $id_notice = $_POST['id_notice'];
	            $notice = Model_News::find($id_notice);
	            if($notice == null){
	            	return $this->createResponse(400, 'La noticia no existe');
	            }
	            $notice = Model_News::find('first', array(
						'where' => array(
							array('id' => $id_notice),
							array('id_user' => $id_user)
					)));
	            if($notice == null){
	            	return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
	            }
	            $notice->delete();
	            return $this->createResponse(200, 'Noticia eliminada', ['notice' => $notice]);
	        }else{
	        	return $this->createResponse(400, 'No tienes permiso para realizar esta acción');
	        }
        }catch (Exception $e) 
        {
            return $this->createResponse(500, $e->getMessage());
        }
	}
	private function noticeExists($title){
		$notice = Model_News::find('first', array(
					'where' => array(
						array('title' => $title)
				)));
		if ($notice != null){
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