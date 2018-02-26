<?php
class Model_Songs extends Orm\Model
{
   	protected static $_table_name = 'songs'; 
	protected static $_properties = array('id','title','artist','url','reproductions');
	protected static $_primary_key = array('id');
}