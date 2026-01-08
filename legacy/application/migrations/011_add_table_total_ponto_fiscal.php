<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_table_total_ponto_fiscal extends CI_Migration
{

	public function up()
	{
		$this->dbforge->add_field(array(
			'id_fiscal' => array(
				'type' => 'INT',
			),
			'data_vigencia' => array(
				'type' => 'timestamp',
				'default' => 'NOW()'
			),
			'pontos_totais' => array(
				'type' => 'INT',
				'default' => 0,
			),
			'pontos_atividades_deducao' => array(
				'type' => 'INT',
				'default' => 0,
			),
			'pontos_atividades_ufesp' => array(
				'type' => 'INT',
				'default' => 0,
			),
			'pontos_atividades_pontuacao' => array(
				'type' => 'INT',
				'default' => 0,
			),
			'total_arrecadado' => array(
				'type' => 'INT',
				'default' => 0,
			),
			'saldo_remanescente' => array(
				'type' => 'INT',
				'default' => 0,
			),
			'saldo_remanescente_utilizado' => array(
				'type' => 'INT',
				'default' => 0,
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
		$this->dbforge->add_key('id_fiscal');
		$this->dbforge->add_key('data_vigencia');
		$this->dbforge->create_table('total_ponto_fiscal');
		$sql = 'ALTER TABLE total_ponto_fiscal
		  ADD CONSTRAINT unique_total_ponto_fiscal UNIQUE (id_fiscal, data_vigencia)';

		$this->db->query($sql);
	}

	public function down()
	{
		$sql = 'ALTER TABLE total_ponto_fiscal
		DROP CONSTRAINT unique_total_ponto_fiscal';
		$this->db->query($sql);
		$this->dbforge->drop_table('total_ponto_fiscal');
	}
}
