<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_table_tipo_atividade extends CI_Migration
{

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'BIGSERIAL'
			),
			'nome' => array(
				'type' => 'VARCHAR',
				'constraint' => '100'
			),
			'ativo' => array(
				'type' => 'INT',
			)
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('tipo_atividade');
		$this->load->model('model_migration');
		$this->model_migration->create_tipo_atividade();
	}

	public function down()
	{
		$this->dbforge->drop_table('tipo_atividade');
	}
}
