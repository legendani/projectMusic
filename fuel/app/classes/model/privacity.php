<?php
class Model_Privacity extends Orm\Model
{
   	protected static $_table_name = 'privacity';
	protected static $_properties = array('id', 'profile','friends','lists','notifications','localization');
	protected static $_primary_key = array('id');
}