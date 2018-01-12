<?php
namespace Fuel\Migrations;

class Usuarios
{

    function up()
    {
        \DBUtil::create_table('usuarios', array(
            'id' => array('type' => 'int', 'constraint' => 5, 'auto_increment' => true),
            'nombre' => array('type' => 'varchar', 'constraint' => 100),
            'email' => array('type' => 'varchar', 'constraint' => 100),
            'password' => array('type' => 'varchar', 'constraint' => 100),
            'id_dispositivo' => array('type' => 'varchar', 'constraint' => 100),
            'fotoPerfil' => array('type' => 'varchar', 'constraint' => 100),
            'x' => array('type' => 'varchar', 'constraint' => 100),
            'y' => array('type' => 'varchar', 'constraint' => 100),
            'cumpleaños' => array('type' => 'varchar', 'constraint' => 100),
            'ciudad' => array('type' => 'varchar', 'constraint' => 100),
            'descripcion' => array('type' => 'varchar', 'constraint' => 100),
            'id_rol' => array('type' => 'int', 'constraint' => 5),
            'id_privacidad' => array('type' => 'int', 'constraint' => 5),
        ), 

        array('id'), false, 'InnoDB', 'utf8',
            array(
                array(
                    'constraint' => 'claveAjenaUsuariosARol',
                    'key' => 'id_rol',
                    'reference' => array(
                        'table' => 'roles',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'RESTRICT'
                ),
                array(
                    'constraint' => 'claveAjenaUsuariosAPrivacidad',
                    'key' => 'id_privacidad',
                    'reference' => array(
                        'table' => 'privacidad',
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
       \DBUtil::drop_table('usuarios');
    }
}