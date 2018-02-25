<?php
class Model_Users extends Orm\Model
{
   	protected static $_table_name = 'users';
	protected static $_properties = array('id','username','password','email','id_device'
        ,'photo','x','y','birthday','city','description','id_rol','id_privacity');
	protected static $_has_many = array(
        'lists' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Lists',
            'key_to' => 'id_user',
            'cascade_save' => true,
            'cascade_delete' => true,
        ),
        'follow' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Follow',
            'key_to' => 'id_followed',
            'cascade_save' => true,
            'cascade_delete' => true,
        ),
        'follow' => array(
            'key_from' => 'id',
            'model_to' => 'Model_Follow',
            'key_to' => 'id_follower',
            'cascade_save' => true,
            'cascade_delete' => true,
        )
    );
}