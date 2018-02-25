<?php
class Model_Lists extends Orm\Model
{
   	protected static $_table_name = 'lists'; 
	protected static $_properties = array(
	      'id',
	      'title',
	      'editable',
	      'id_user'
	   );
	protected static $_many_many = array(
    'songs' => array(
	        'key_from' => 'id',
	        'key_through_from' => 'id_list',
	        'table_through' => 'contain',
	        'key_through_to' => 'id_song',
	        'model_to' => 'Model_Songs',
	        'key_to' => 'id',
	        'cascade_save' => true,
	        'cascade_delete' => false,
    	)
	);
	protected static $_belongs_to = array(
    'users' => array(
        'key_from' => 'id_user',
        'model_to' => 'Model_Users',
        'key_to' => 'id',
        'cascade_save' => true,
        'cascade_delete' => false,
    ));
	
}