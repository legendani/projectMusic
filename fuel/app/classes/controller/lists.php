<?php
use Firebase\JWT\JWT;
class Controller_Lists extends Controller_Rest
{
    private $key = 'my_secret_key';
    protected $format = 'json';

    public function post_createList()
    {
        $input = $_POST;
        $jwt = apache_request_headers()['Authorization'];
        $title = $input['title'];
        $editable = $input['editable'];
        $token = JWT::decode($jwt, $this->key , array('HS256'));
        $id = $token->data->id;

        $userDB = Model_Users::find('first', array(
            'where' => array(
                array('id', $id)
            ),
        ));

        if($userDB != null){
            $new = new Model_Lists();
            $new->title = $input['title'];
            $new->id_user = $id;
            $new->editable = $input['editable'];
            $new->save();
            $this->Mensaje('200', 'List created', $input['title']);
        }else {
            $this->Mensaje('400', 'User not valid', $jwt);
        }
    }

    public function get_lists(){
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
            $allLists = Model_Lists::find('all', array(
                'where' => array(
                    array('id_user', $id)
                ),
            ));
            $this->Mensaje('200', 'Lists', $allLists);
        }else {
            $this->Mensaje('400', 'User not valid', $id);
        }
    }

    public function post_deleteList(){
        $jwt = apache_request_headers()['Authorization'];
        $input = $_POST;
        $id_list = $input['id_list'];
        $token = JWT::decode($jwt, $this->key , array('HS256'));
        $id = $token->data->id;

        $userDB = Model_Users::find('first', array(
            'where' => array(
                array('id', $id)
            ),
        ));

        if(count($userDB) == 1){
            $list = Model_Lists::find('first', array(
                'where' => array(
                    array('id_user', $id ),
                    array('id', $id_list)
                ),
            ));
            if($list != null){
                $list->delete();
                $this->Mensaje('200', 'List deleted', $list);
            }else{
                $this->Mensaje('400', 'List not found', $list);
            }
        } else {
            $this->Mensaje('400', 'User not valid', $id);
        }
    }

    public function post_modifyList(){
        $input = $_POST;
        $jwt = apache_request_headers()['Authorization'];

        if (array_key_exists('title', $input)&& array_key_exists('id_list', $input) && array_key_exists('editable', $input)) {
            $title = $input['title'];
            $id_list = $input['id_list'];
            $editable = $input['editable'];

            $token = JWT::decode($jwt, $this->key , array('HS256'));
            $id = $token->data->id;

            $userDB = Model_Users::find('first', array(
                'where' => array(
                    array('id', $id)
                ),
            ));

            if($userDB != null){
                $list = Model_Lists::find('first', array(
                    'where' => array(
                        array('id_user', $id),
                        array('id', $id_list)
                    ),
                ));
                if($list != null){
                    $list->title = $title;
                    $list->editable = $editable;
                    $list->save();
                    $this->Mensaje('200', 'List edited', $list);
                }else{
                    $this->Mensaje('400', 'Permission denied', $list);
                }
            } else {
                $this->Mensaje('400', 'User not valid', $list);
            }
        }else {
            $this->Mensaje('400', 'Invalid arguments', $input);
        }
    }

    public function post_addSongToList(){
        $jwt = apache_request_headers()['Authorization'];
        try{
            $token = JWT::decode($jwt, $this->key , array('HS256'));
            $input = $_POST;
            $id = $token->data->id;
            $id_list = $input['id_list'];
            $id_song = $input['id_song'];

            $containDB = Model_Contain::find('first', array(
                'where' => array(
                    array('id_lists', $id_list),
                    array('id_songs', $id_song)
                    ),
                ));

            $songDB = Model_Songs::find('first', array(
                'where' => array(
                    array('id', $id_song)
                    ),
                ));

            $listDB = Model_Lists::find('first', array(
                'where' => array(
                    array('id', $id_list)
                    ),
                ));

            $userDB = Model_Users::find('first', array(
                'where' => array(
                    array('id', $id)
                    ),
                ));

            if($userDB != null){
                if($songDB != null){
                    if($listDB != null){
                        if($containDB == null){
                            $new = new Model_Contain();
                            $new->id_lists = $id_list;
                            $new->id_songs = $id_song;
                            $new->save();
                            $this->Mensaje('200', 'Song added to the list', $id_song);
                        } else {
                            $this->Mensaje('400', 'Song already in list', $id_song);
                        }
                    } else {
                        $this->Mensaje('400', 'List doesnt exist', $id_list);
                    }
                } else {
                    $this->Mensaje('400', 'Song doesnt exist', $id_song);
                }
            } else {
                $this->Mensaje('400', 'User not valid', $id);
            }
        } catch(Exception $e) {
            $this->Mensaje('500', 'Verification error', "error");
        } 
    }

    public function post_deleteSongFromList(){
            $jwt = apache_request_headers()['Authorization'];
            try{
                $input = $_POST;
                $id_song = $input['id_song'];
                $id_list = $input['id_list'];
                $token = JWT::decode($jwt, $this->key , array('HS256'));
                $id = $token->data->id;

                $containDB = Model_Contain::find('first', array(
                        'where' => array(
                            array('id_lists', $id_list),
                            array('id_songs', $id_song)
                            ),
                        ));

                $userDB = Model_Users::find('first', array(
                'where' => array(
                    array('id', $id)
                    ),
                ));

                if($userDB != null){
                    if($containDB != null){
                        $containDB->delete();
                        $this->Mensaje('200', 'Song deleted from list', $input);
                    } else {
                        $this->Mensaje('400', 'Song not in the list', $input);
                    }
                } else {
                    $this->Mensaje('400', 'User not valid', $id);
                }
            } catch(Exception $e) {
                $this->Mensaje('500', 'Verification error', "error");
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