<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_Atividade_anexo extends CI_Model
{

	public function cadastrar($idAtividadeFiscal, $arquivos)
	{
		for ($i = 0; $i < count($arquivos); $i++) {
			$arquivos[$i]['id_atividade_fiscal'] = $idAtividadeFiscal;
			$arquivos[$i]['data_cadastro'] = date('Y-m-d H:i:s');
			$arquivos[$i]['data_update'] = date('Y-m-d H:i:s');
		}
		return $this->db->insert_batch('atividade_fiscal_anexo', $arquivos);
	}

	public function get($idAtividadeFiscal = null)
	{
		$query = $this->db->select('*')
			->from('atividade_fiscal_anexo')
			->where('id_atividade_fiscal', $idAtividadeFiscal)
			->where('data_exclusao', null)
			->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}

	public function update($ano, $data)
	{
	}
}
