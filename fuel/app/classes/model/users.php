<?php
class Model_Users extends Orm\Model
{
   	protected static $_table_name = 'users';
	protected static $_properties = array('id','username','password','email','id_device','profile_photo','x','y','birthday','city','description','id_roles','id_privacity');
    protected static $_primary_key = array('id');
}