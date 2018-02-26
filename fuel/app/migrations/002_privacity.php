<?php
namespace Fuel\Migrations;

class Privacity
{

    function up()
    {
        \DBUtil::create_table('privacity', array(
            'id' => array('type' => 'int', 'constraint' => 5, 'auto_increment' => true),
            'profile' => array('type' => 'bool', 'null' => false),
            'friends' => array('type' => 'bool', 'null' => false),
            'lists' => array('type' => 'bool', 'null' => false),
            'notifications' => array('type' => 'bool', 'null' => false),
            'localization' => array('type' => 'bool', 'null' => false),
        ), array('id'));  
    }

    function down()
    {
       \DBUtil::drop_table('privacity');
    }
}