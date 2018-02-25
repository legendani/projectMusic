<?php
class Model_Follow extends Orm\Model
{
   	protected static $_table_name = 'follow'; 
	protected static $_properties = array('id_followed','id_follower');
	protected static $_primary_key = array('id_followed','id_follower');
	protected static $_belongs_to = array(
    'users' => array(
        'key_from' => 'id_follower',
        'model_to' => 'Model_Users',
        'key_to' => 'id',
        'cascade_save' => true,
        'cascade_delete' => false,
    ),
	'users' => array(
        'key_from' => 'id_follower',
        'model_to' => 'Model_Users',
        'key_to' => 'id',
        'cascade_save' => true,
        'cascade_delete' => false,
    ));
}