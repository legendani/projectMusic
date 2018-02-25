<?php
class Model_News extends Orm\Model
{
   	protected static $_table_name = 'news'; 
	protected static $_properties = array('id','title','description', 'id_user'
	   );
	// protected static $_belongs_to = array(
 //    'users' => array(
 //        'key_from' => 'id_user',
 //        'model_to' => 'Model_Users',
 //        'key_to' => 'id',
 //        'cascade_save' => true,
 //        'cascade_delete' => true,
 //    ));
}