<?php
use Firebase\JWT\JWT;
class Controller_News extends Controller_Rest
{
	private $key = 'my_secret_key';
    protected $format = 'json';

    public function post_createNews(){
		$input = $_POST;
		$jwt = apache_request_headers()['Authorization'];
		$token = JWT::decode($jwt, $this->key , array('HS256'));
		$id = $token->data->id;

		$userDB = Model_Users::find('first', array(
			'where' => array(
				array('id', $id)
			),
		));

		if($userDB != null){
			$new = new Model_News();
			$new->title = $input['title'];
			$new->description = $input['description'];
			$new->id_user = $id;
			$new->save();
			$this->Mensaje('200', 'News created', $input);
		} else {
			$this->Mensaje('400', 'user not found', $input);
		}
	}

	public function post_modifyNews(){
		$jwt = apache_request_headers()['Authorization'];
		$token = JWT::decode($jwt, $this->key , array('HS256'));
		$id = $token->data->id;
		$input = $_POST;
		$id_news = $input['id_news'];
		$description = $input['description'];
		$title = $input['title'];
		$id_user = $input['id_user'];

		$userDB = Model_Users::find('first', array(
			'where' => array(
				array('id', $id)
			),
		));

		if($userDB != null){
			$searchNews = Model_News::find('first', array(
				'where' => array(
					array('id', $id_news)
				),
			));
			if($searchNews != null){
				if (empty($description) && empty($title) && !empty($id_user)) {
					$searchNews->id_user = $input['id_user'];
					$searchNews->save();
				}
				if (empty($description) && !empty($title) && empty($id_user)) {
					$searchNews->title = $input['title'];
					$searchNews->save();
				}
				if (!empty($description) && empty($title) && empty($id_user)) {
					$searchNews->description = $input['description'];
					$searchNews->save();
				}
				if (!empty($description) && !empty($title) && empty($id_user)) {
					$searchNews->description = $input['description'];
					$searchNews->title = $input['title'];
					$searchNews->save();
				}
				if (!empty($description) && empty($title) && !empty($id_user)) {
					$searchNews->description = $input['description'];
					$searchNews->id_user = $input['id_user'];
					$searchNews->save();
				}
				if (empty($description) && !empty($title) && !empty($id_user)) {
					$searchNews->id_user = $input['id_user'];
					$searchNews->title = $input['title'];
					$searchNews->save();
				}
				if (!empty($description) && !empty($title) && !empty($id_user)) {
					$searchNews->description = $input['description'];
					$searchNews->id_user = $input['id_user'];
					$searchNews->title = $input['title'];
					$searchNews->save();
				}
				$this->Mensaje('200', 'News saved', $searchNews);
			}
		} else {
			$this->Mensaje('400', 'User not valid', $id);
		}
	}

	public function post_deleteNew(){
		$jwt = apache_request_headers()['Authorization'];
		$input = $_POST;
		$id_news = $input['id_news'];
		$token = JWT::decode($jwt, $this->key , array('HS256'));
		$id = $token->data->id;

		$userDB = Model_Users::find('first', array(
			'where' => array(
				array('id', $id)
			),
		));

		if(count($userDB) == 1){
			$news = Model_News::find('first', array(
				'where' => array(
					array('id_user', $id ),
					array('id', $id_news)
				),
			));
			if($news != null){
				$news->delete();
				$this->Mensaje('200', 'News deleted', $news);
			}else{
				$this->Mensaje('400', 'News not found', $news);
			}
		} else {
			$this->Mensaje('400', 'User not valid', $id);
		}
	}

	public function get_getOwnNews(){
		$jwt = apache_request_headers()['Authorization'];
		$token = JWT::decode($jwt, $this->key , array('HS256'));
		$username = $token->data->username;
		$password = $token->data->password;
		$id = $token->data->id;

		$userDB = Model_Users::find('all', array(
			'where' => array(
				array('username', $username),
				array('password', $password)
			),
		));
		
		if(count($userDB) == 1){
			$newsList = Model_News::find('all', array(
				'where' => array(
					array('id_user', $id)
				),
			));
			$this->Mensaje('200', 'News list', $newsList);
		}else {
			$this->Mensaje('400', 'usuario no valido', $id);
		}
	}

	public function get_getNew(){
		$jwt = apache_request_headers()['Authorization'];
		$token = JWT::decode($jwt, $this->key , array('HS256'));
		$username = $token->data->username;
		$password = $token->data->password;
		$id = $token->data->id;
		$title = $_GET['title'];

		$userDB = Model_Usuarios::find('all', array(
			'where' => array(
				array('username', $username),
				array('password', $password)
			),
		));
		
		if(count($userDB) == 1){
			$new = Model_News::find('all', array(
				'where' => array(
					array('title', $title)
				),
			));
			$this->Mensaje('200', 'News', $new);
		}else {
			$this->Mensaje('400', 'User not valid', $id);
		}
	}

	public function get_getNews(){
		$jwt = apache_request_headers()['Authorization'];
		$token = JWT::decode($jwt, $this->key , array('HS256'));
		$username = $token->data->username;
		$password = $token->data->password;
		$id = $token->data->id;

		$userDB = Model_Users::find('all', array(
			'where' => array(
				array('username', $username),
				array('password', $password)
			),
		));
		
		if(count($userDB) == 1){
			$newsList = Model_News::find('all');
			$this->Mensaje('200', 'News List', $newsList);
		}else {
			$this->Mensaje('400', 'User not valid', $id);
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