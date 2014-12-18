<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_ap_list extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'ssid' => array(
				'type' => 'TEXT'
			),
			'type' => array(
				'type' => 'TEXT'
			),
			'psk' => array(
				'type' => 'TEXT'
			),
			'priority' => array(
				'type' => 'INT'
			)
		));

		$this->dbforge->create_table('ap_list');
	}

	public function down()
	{
		$this->dbforge->drop_table('ap_list');
	}
}
