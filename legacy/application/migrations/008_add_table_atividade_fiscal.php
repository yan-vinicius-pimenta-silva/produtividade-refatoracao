<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_table_atividade_fiscal extends CI_Migration
{

	public function up()
	{
		$this->dbforge->add_field(array(
			'id' => array(
				'type' => 'bigserial'
			),
			'id_atividade' => array(
				'type' => 'INT',
			),
			'id_fiscal' => array(
				'type' => 'INT',
			),
			'id_empresa' => array(
				'type' => 'INT',
			),
			'ufesp_ano' => array(
				'type' => 'varchar',
				'constraint' => '100',
				'comment' => 'Valor UFESP utilizado',
				'null' => TRUE,
			),
			'documento' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE,
			),
			'protocolo' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE,
			),
			'cpf_cnpj' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE,
			),
			'rc' => array(
				'type' => 'VARCHAR',
				'constraint' => '100',
				'null' => TRUE,
			),
			'valor' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
			'quantidade' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
			'pontuacao_total' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
			'data_validado' => array(
				'type' => 'timestamp',
				'null' => TRUE,
			),
			'usuario_exclusao' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => TRUE,
			),
			'motivo_exclusao' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => TRUE,
			),
			'usuario_deducao' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => TRUE,
			),
			'motivo_deducao' => array(
				'type' => 'VARCHAR',
				'constraint' => '1000',
				'null' => TRUE,
			),
			'usuario_validado' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => TRUE,
			),
			'observacao' => array(
				'type' => 'VARCHAR',
				'constraint' => '500',
				'null' => TRUE,
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
			'data_conclusao' => array(
				'type' => 'timestamp',
				'null' => TRUE,
			),
		));
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('atividade_fiscal');
	}

	public function down()
	{
		$this->dbforge->drop_table('atividade_fiscal');
	}
}
