<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_table_atividade_fiscal_contabilizada extends CI_Migration
{

	public function up()
	{
		$this->dbforge->add_field(array(
			'id_atividade' => array(
				'type' => 'INT',
			),
			'id_fiscal' => array(
				'type' => 'INT',
			),
			'data_vigencia' => array(
				'type' => 'timestamp',
				'default' => 'NOW()'
			),
			'valor_ufesp' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
			'valor_total' => array(
				'type' => 'INT',
				'null' => TRUE,
			),
			'ponto_base_calculo' => array(
				'type' => 'INT',
			),
			'total_pontos' => array(
				'type' => 'INT',
			),
			'quantidade' => array(
				'type' => 'INT',
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
		));
		$this->dbforge->add_key('id_atividade');
		$this->dbforge->add_key('id_fiscal');
		$this->dbforge->add_key('data_vigencia');
		$this->dbforge->create_table('atividade_fiscal_contabilizada');
		$sql = 'ALTER TABLE atividade_fiscal_contabilizada
		  ADD CONSTRAINT unique_atividade_fiscal_contabilizada UNIQUE (id_atividade, id_fiscal, data_vigencia)';

		$this->db->query($sql);
	}

	public function down()
	{
		$sql = 'ALTER TABLE atividade_fiscal_contabilizada
		DROP CONSTRAINT unique_atividade_fiscal_contabilizada';
		$this->db->query($sql);
		$this->dbforge->drop_table('atividade_fiscal_contabilizada');
	}
}
