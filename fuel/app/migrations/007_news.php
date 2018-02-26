<?php
namespace Fuel\Migrations;

class News
{

    function up()
    {
        \DBUtil::create_table('news', array(
            'id' => array('type' => 'int', 'constraint' => 5, 'auto_increment' => true, 'null' => false),
            'title' => array('type' => 'varchar', 'constraint' => 100, 'null' => false),
            'description' => array('type' => 'varchar', 'constraint' => 100, 'null' => false),
            'id_user' => array('type' => 'int', 'constraint' => 5, 'null' => false)
        ),

        array('id'), false, 'InnoDB', 'utf8',
            array(
                array(
                    'constraint' => 'claveAjenaNewsAUsers',
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
       \DBUtil::drop_table('news');
    }
}