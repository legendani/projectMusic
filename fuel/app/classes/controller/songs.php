<?php
use Firebase\JWT\JWT;
class Controller_Songs extends Controller_Rest
{
	private $key = 'my_secret_key';
    protected $format = 'json';
    
    public function post_createSong(){
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
            $new = new Model_Songs();
            $new->title = $input['title'];
            $new->url = $input['url'];
            $new->artist = $input['artist'];
            $new->reproductions = '0';
            $new->save();
            $this->Mensaje('200', 'Song added', $input);
        } else {
            $this->Mensaje('400', 'user not found', $input);
        }
    }

    public function get_Songs(){
        $jwt = apache_request_headers()['Authorization'];
        $token = JWT::decode($jwt, $this->key , array('HS256'));
        $id = $token->data->id;

        $userDB = Model_Users::find('first', array(
            'where' => array(
                array('id', $id)
            ),
        ));

        if($userDB != null){
            $songs = Model_Songs::find('all');
            $this->Mensaje('200', 'List of songs', $songs);
            
        }else {
            $this->Mensaje('400', 'Songs not found', $songs);
        }
    }

    public function post_modifySong(){
        $jwt = apache_request_headers()['Authorization'];
        $token = JWT::decode($jwt, $this->key , array('HS256'));
        $id = $token->data->id;
        $input = $_POST;
        $id_item = $input['id_item'];

        $BDuser = Model_Users::find('first', array(
            'where' => array(
                array('id', $id)
            ),
        ));

        if($userDB != null){
            $searchSong = Model_Songs::find('first', array(
                'where' => array(
                    array('id', $id_item)
                ),
            ));
            if($searchSong != null){
                if (empty($url) && empty($title) && !empty($artist)) {
                    $searchSong->artist = $input['artist'];
                    $searchSong->save();
                }
                if (empty($url) && !empty($title) && empty($artist)) {
                    $searchSong->title = $input['title'];
                    $searchSong->save();
                }
                if (!empty($url) && empty($title) && empty($artist)) {
                    $searchSong->url = $input['url'];
                    $searchSong->save();
                }
                if (!empty($url) && !empty($title) && empty($artist)) {
                    $searchSong->url = $input['url'];
                    $searchSong->title = $input['title'];
                    $searchSong->save();
                }
                if (!empty($url) && empty($title) && !empty($artist)) {
                    $searchSong->url = $input['url'];
                    $searchSong->artist = $input['artist'];
                    $searchSong->save();
                }
                if (empty($url) && !empty($title) && !empty($artist)) {
                    $searchSong->artist = $input['artist'];
                    $searchSong->title = $input['title'];
                    $searchSong->save();
                }
                if (!empty($url) && !empty($title) && !empty($artist)) {
                    $searchSong->url = $input['url'];
                    $searchSong->artist = $input['artist'];
                    $searchSong->title = $input['title'];
                    $searchSong->save();
                }
                $this->Mensaje('200', 'Song edited', $searchSong);
            }
        } else {
            $this->Mensaje('400', 'User not valid', $id);
        }
    }

    public function post_deleteSong(){
        $jwt = apache_request_headers()['Authorization'];
        $token = JWT::decode($jwt, $this->key , array('HS256'));
        $id = $token->data->id;
        $input = $_POST;

        $userDB = Model_Users::find('first', array(
            'where' => array(
                array('id', $id)
            ),
        ));

        if($userDB != null){
            $song = Model_Songs::find('first', array(
                'where' => array(
                    array('id', $input['id'])
                ),
            ));

            if ($song != null) {
                $song->delete();
                $this->Mensaje('200', 'Song deleted', $song);
            }else {
                $this->Mensaje('400', 'Song not found', $song);
            }
        } else {
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