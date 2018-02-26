<?php
class Model_Follow extends Orm\Model
{
   	protected static $_table_name = 'follow'; 
	protected static $_properties = array('id_followed','id_follower');
	protected static $_primary_key = array('id_followed','id_follower');
}