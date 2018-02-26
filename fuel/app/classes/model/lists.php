<?php
class Model_Lists extends Orm\Model
{
   	protected static $_table_name = 'lists'; 
	protected static $_properties = array('id','title','editable','id_user');
	protected static $_primary_key = array('id');
}