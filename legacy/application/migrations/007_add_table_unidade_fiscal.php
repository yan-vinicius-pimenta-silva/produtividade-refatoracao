<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_table_unidade_fiscal extends CI_Migration
{

	public function up()
	{
		$this->dbforge->add_field(array(
			'ano' => array(
				'type' => 'varchar',
				'constraint' => '100'
			),
			'nome' => array(
				'type' => 'VARCHAR',
				'constraint' => '100'
			),
			'valor' => array(
				'type' => 'INT',
			),
			'ativo' => array(
				'type' => 'INT',
			)
		));
		$this->dbforge->add_key('ano', TRUE);
		$this->dbforge->create_table('unidade_fiscal');
	}

	public function down()
	{
		$this->dbforge->drop_table('unidade_fiscal');
	}
}
