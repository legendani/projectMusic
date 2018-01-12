<?php
namespace Fuel\Migrations;

class Contener
{

    function up()
    {
        \DBUtil::create_table('contener', array(
            'id_listas' => array('type' => 'int', 'constraint' => 5),
            'id_canciones' => array('type' => 'int', 'constraint' => 5)
        ),

        array('id_listas', 'id_canciones'), false, 'InnoDB', 'utf8_unicode_ci',
            array(
                array(
                    'constraint' => 'claveAjenaContenerAListas',
                    'key' => 'id_listas',
                    'reference' => array(
                        'table' => 'listas',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'RESTRICT'
                ),
                array(
                    'constraint' => 'claveAjenaContenerACanciones',
                    'key' => 'id_canciones',
                    'reference' => array(
                        'table' => 'canciones',
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
       \DBUtil::drop_table('contener');
    }
}