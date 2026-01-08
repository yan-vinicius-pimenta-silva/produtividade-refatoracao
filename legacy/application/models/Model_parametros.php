<?php
class Model_Parametros extends CI_Model
{
	const TIPO_ATIVIDADE_UFESP = 1;
	const TIPO_ATIVIDADE_PONTUACAO = 2;
	const TIPO_ATIVIDADE_DEDUCAO = 3;

	function getAtividades($id_atividade = null, $id_tipo_atividade = null, $id_empresa = null)
	{

		$this->db->select('a.*, ta.nome as tipo_calculo');
		$this->db->from('atividade a');
		$this->db->join('tipo_atividade ta', 'ta.id = a.id_tipo_atividade', 'left');
		$this->db->where('a.excluido', 0);
		$this->db->where('a.ativo', 1);

		if ($id_empresa != null)
			$this->db->where('a.id_empresa', $id_empresa);

		if ($id_tipo_atividade != null)
			$this->db->where('a.id_tipo_atividade', $id_tipo_atividade);

		if ($id_atividade != null)
			$this->db->where('a.id', $id_atividade);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}

	function cadastrarAtividade($data)
	{
		if ($this->db->insert('atividade', $data)) {
			$last_id = $this->db->insert_id();
			$this->logger->logAction('ativdade create', (array) $data);
			return $last_id;
		}
		return false;
	}

	function alterarAtividade($id_atividade, $data)
	{
		$this->db->where('id', $id_atividade);
		if ($this->db->update('atividade', $data)) {
			$this->logger->logAction('ativdade update(ID: ' . $id_atividade . ')', (array) $data);
			return true;
		}
		return false;
	}

	function deletarAtividade($id_atividade)
	{
		$data = array(
			'excluido' => 1
		);

		$this->db->where('id', $id_atividade);
		if ($this->db->update('atividade', $data)) {
			$this->logger->logAction('ativdade delete(ID: ' . $id_atividade . ')', (array) $data);
			return true;
		}

		return false;
	}
	//alterar nome função
	function verificaAtividades($id_atividade, $id_empresa)
	{
		$this->db->select('*');
		$this->db->from('atividade_fiscal h');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');
		$this->db->where('h.id_atividade', $id_atividade);
		$this->db->where('h.id_empresa', $id_empresa);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}

	function getTipoAtividade()
	{
		$this->db->select('*');
		$this->db->from('tipo_atividade');

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}
}
