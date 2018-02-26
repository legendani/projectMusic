<?php
namespace Fuel\Migrations;

class Lists
{

    function up()
    {
        \DBUtil::create_table('lists', array(
            'id' => array('type' => 'int', 'constraint' => 5, 'auto_increment' => true),
            'title' => array('type' => 'varchar', 'constraint' => 100),
            'editable' => array('type' => 'bool'),
            'id_user' => array('type' => 'int', 'constraint' => 5),
        ),

        array('id'), false, 'InnoDB', 'utf8',
            array(
                array(
                    'constraint' => 'claveAjenaListsAUsers',
                    'key' => 'id_user',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'RESTRICT'
                )
            )
        );    
    }

    function down()
    {
       \DBUtil::drop_table('listas');
    }
}