<?php
namespace Fuel\Migrations;

class Listas
{

    function up()
    {
        \DBUtil::create_table('listas', array(
            'id' => array('type' => 'int', 'constraint' => 5, 'auto_increment' => true),
            'titulo' => array('type' => 'varchar', 'constraint' => 100),
            'id_usuarios' => array('type' => 'int', 'constraint' => 5),
        ),

        array('id'), false, 'InnoDB', 'utf8',
            array(
                array(
                    'constraint' => 'claveAjenaListasAUsuarios',
                    'key' => 'id_usuarios',
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
       \DBUtil::drop_table('listas');
    }
}