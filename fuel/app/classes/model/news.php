<?php
class Model_News extends Orm\Model
{
   	protected static $_table_name = 'news'; 
	protected static $_properties = array('id','title','description', 'id_user');

}