<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_users extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'username' => array(
				'type' => 'VARCHAR',
				'constraint' => 255
			),
			'password' => array(
				'type' => 'VARCHAR',
				'constraint' => 255
			)
		));

		$this->dbforge->create_table('users');
	}

	public function down()
	{
		$this->dbforge->drop_table('users');
	}
}
