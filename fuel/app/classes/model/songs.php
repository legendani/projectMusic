<?php
class Model_Songs extends Orm\Model
{
   	protected static $_table_name = 'songs'; 
	protected static $_properties = array(
	      'id',
	      'title',
	      'artist',
	      'url_youtube',
	      'reproductions'
	   );
	protected static $_many_many = array(
    'lists' => array(
	        'key_from' => 'id',
	        'key_through_from' => 'id_song',
	        'table_through' => 'contain',
	        'key_through_to' => 'id_list',
	        'model_to' => 'Model_Lists',
	        'key_to' => 'id',
	        'cascade_save' => true,
	        'cascade_delete' => false,
    	)
	);
	
}