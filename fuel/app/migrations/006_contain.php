<?php
namespace Fuel\Migrations;

class Contain
{

    function up()
    {
        \DBUtil::create_table('contain', array(
            'id_lists' => array('type' => 'int', 'constraint' => 5),
            'id_songs' => array('type' => 'int', 'constraint' => 5)
        ),

        array('id_lists', 'id_songs'), false, 'InnoDB', 'utf8_unicode_ci',
            array(
                array(
                    'constraint' => 'claveAjenaContainALists',
                    'key' => 'id_lists',
                    'reference' => array(
                        'table' => 'lists',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'RESTRICT'
                ),
                array(
                    'constraint' => 'claveAjenaContainASongs',
                    'key' => 'id_songs',
                    'reference' => array(
                        'table' => 'songs',
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
       \DBUtil::drop_table('contain');
    }
}