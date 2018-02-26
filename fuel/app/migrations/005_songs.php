<?php
namespace Fuel\Migrations;

class Songs
{

    function up()
    {
        \DBUtil::create_table('songs', array(
            'id' => array('type' => 'int', 'constraint' => 5, 'auto_increment' => true),
            'title' => array('type' => 'varchar', 'constraint' => 100),
            'artist' => array('type' => 'varchar', 'constraint' => 100),
            'url' => array('type' => 'varchar', 'constraint' => 100),
            'reproductions' => array('type' => 'int', 'constraint' => 5),
        ), array('id'));

        \DB::query("ALTER TABLE `songs` ADD UNIQUE (`url`)")->execute();
    }
    

    function down()
    {
       \DBUtil::drop_table('songs');
    }
}