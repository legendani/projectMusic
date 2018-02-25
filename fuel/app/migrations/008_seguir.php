<?php
namespace Fuel\Migrations;

class Seguir
{

    function up()
    {
        \DBUtil::create_table('seguir', array(
            'id_seguido' => array('type' => 'int', 'constraint' => 5),
            'id_seguidor' => array('type' => 'int', 'constraint' => 5)
        ),

        array('id_seguido', 'id_seguidor'), false, 'InnoDB', 'utf8_unicode_ci',
            array(
                array(
                    'constraint' => 'claveAjenaSeguidoAUsuarios',
                    'key' => 'id_seguido',
                    'reference' => array(
                        'table' => 'usuarios',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'RESTRICT'
                ),
                array(
                    'constraint' => 'claveAjenaSeguidorAUsuarios',
                    'key' => 'id_seguidor',
                    'reference' => array(
                        'table' => 'usuarios',
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