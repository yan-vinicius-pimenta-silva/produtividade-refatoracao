<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_Unidade_Fiscal extends CI_Model
{

	function cadastrar($data)
	{
		$this->db->trans_start();
		if ($data['ativo'] == 1)
			$this->disableOld();
		$result = $this->db->insert('unidade_fiscal', $data);
		if ($result)
			$this->logger->logAction('unidade_fiscal create', (array) $data);
		$this->db->trans_complete();
		return $result;
	}

	function get($ano = null, $ativo = null)
	{
		$this->db->select("*");
		$this->db->from("unidade_fiscal");
		if ($ano !== null) {
			$this->db->where("ano", $ano);
		}
		if ($ativo !== null) {
			$this->db->where("ativo", $ativo);
		}
		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return FALSE;
	}

	public function update($ano, $data)
	{
		if ($data['ativo'] === 1) {
			$this->disableOld();
		}
		$this->db->where('ano', $ano);
		$this->db->update('unidade_fiscal', $data);
		if ($this->db->affected_rows() > 0) {
			$this->logger->logAction('unidade_fiscal update', (array) $data);
			return true;
		}
		return false;
	}

	public function excluir($ano)
	{
		$this->db->where('ano', $ano);
		$this->db->delete('unidade_fiscal');
		if ($this->db->affected_rows() > 0) {
			$this->logger->logAction('unidade_fiscal delete', (array) $ano);
			return true;
		}
		return false;
	}

	public function isInUse($ano)
	{
		$this->db->select("count(*) as count");
		$this->db->from("atividade_fiscal");
		$this->db->where("ufesp_ano", $ano);
		$query = $this->db->get();
		$result = $query->row();
		return $result->count > 0;
	}

	private function disableOld()
	{
		$this->db->update('unidade_fiscal', ["ativo" => 0], "ativo = 1");
	}
}
