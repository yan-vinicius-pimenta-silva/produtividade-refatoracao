<?php
class Model_Migration extends CI_Model
{

	function create_tipo_atividade()
	{
		$data = array(
			array(
				'nome' => 'UFESP',
				'ativo' => 1
			),
			array(
				'nome' => 'PONTUACAO',
				'ativo' => 1
			),
			array(
				'nome' => 'DEDUCAO',
				'ativo' => 1
			),
		);

		$this->db->insert_batch('tipo_atividade', $data);
	}
}
