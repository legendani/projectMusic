<?php
namespace Fuel\Migrations;

class Follow
{
    function up()
    {
        \DBUtil::create_table('follow', array(
            'id_followed' => array('type' => 'int', 'constraint' => 5),
            'id_follower' => array('type' => 'int', 'constraint' => 5)
        ), 

        array('id_followed', 'id_follower'),
            true,
            'InnoDB',
            'utf8_unicode_ci',
            array(
                array(
                    'constraint' => 'claveAjenaFollowedAUsers',
                    'key' => 'id_followed',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'RESTRICT'
                ),
                array(
                    'constraint' => 'claveAjenaFollowerAUsers',
                    'key' => 'id_follower',
                    'reference' => array(
                        'table' => 'users',
                        'column' => 'id',
                    ),
                    'on_update' => 'CASCADE',
                    'on_delete' => 'RESTRICT'
                )
            ));
    }
    function down()
    {
       \DBUtil::drop_table('follow');
    }
}