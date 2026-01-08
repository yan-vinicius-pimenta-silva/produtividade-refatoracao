<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_table_atividade_fiscal_anexo extends CI_Migration
{

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'bigserial'
			),
			'id_atividade_fiscal' => array(
				'type' => 'INT',
			),
			'anexo' => array(
				'type' => 'VARCHAR',
				'constraint' => '100'
			),
			'data_cadastro' => array(
				'type' => 'timestamp',
				'default' => 'NOW()'
			),
			'data_update' => array(
				'type' => 'timestamp',
				'null' => TRUE,
			),
			'data_exclusao' => array(
				'type' => 'timestamp',
				'null' => TRUE,
			),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('atividade_fiscal_anexo');
	}

	public function down()
	{
		$this->dbforge->drop_table('atividade_fiscal_anexo');
	}
}
