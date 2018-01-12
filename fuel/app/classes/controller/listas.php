<?php
use Firebase\JWT\JWT;
class Controller_Lists extends Controller_Rest{
	private $key = 'eih12ohgsfh2o389fsag2891JK8912h3sa';
	function post_create()
  {
   	try{
      if (!isset($_POST['title']) || $_POST['title'] == "") 
      {
        $this->createResponse(400, 'Incorrect data');
      }
        $jwt = apache_request_headers()['Authorization'];
        if($this->tokenValidated($jwt))
        {
            
    }catch (Exception $e){
        $json = $this->response(array(
              'code' => 500,
              'message' => 'Internal error',
        ));
        return $json;
    } 	   
  }
}