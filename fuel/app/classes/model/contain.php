<?php
class Model_Contain extends Orm\Model
{
   	protected static $_table_name = 'contain'; 
	protected static $_properties = array('id_song','id_list');
	protected static $_primary_key = array('id_song','id_list');
