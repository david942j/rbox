<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_network extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'device' => array(
				'type' => 'TEXT'
			),
			'dynamic_flag' => array(
				'type' => 'INT'
			),
			'ip_addr' => array(
				'type' => 'TEXT'
			),
			'subnet_mask' => array(
				'type' => 'TEXT'
			),
			'gateway' => array(
				'type' => 'TEXT'
			),
			'dns' => array(
				'type' => 'TEXT'
			)
		));

		$this->dbforge->create_table('network');
	}

	public function down()
	{
		$this->dbforge->drop_table('network');
	}
}
