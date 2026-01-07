<?php
class Model_Home extends CI_Model
{

	function getTotais($id = null, $data_ini = null, $data_fim = null, $id_empresa = null)
	{
		$this->db->select('COUNT("A"."id")');
		$this->db->from('atividade_lancamento A');
		$this->db->join('usuario_empresa usuemp', 'A.id_usuario = usuemp.id_usuario', 'left');
		$this->db->where('A.id_usuario', $id);
		$this->db->where('A.id_empresa', $id_empresa);
		$this->db->where('usuemp.ativo', 1);
		$this->db->where('A.data_conclusao >=', $data_ini);
		$this->db->where('A.data_conclusao <=', $data_fim);
		$this->db->where('A.excluido', 0);
		$this->db->where('A.validacao', 1);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result()[0]->count;
		}
		return false;
	}

	function getPontos($id = null, $data_ini = null, $data_fim = null, $id_empresa = null)
	{
		$this->db->select('a.pontos');
		$this->db->from('atividade_lancamento h');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');
		$this->db->join('usuario_empresa usuemp', 'h.id_usuario = usuemp.id_usuario', 'left');
		$this->db->where('h.id_usuario', $id);
		$this->db->where('h.id_empresa', $id_empresa);
		$this->db->where('usuemp.ativo', 1);
		$this->db->where('h.data_conclusao >=', $data_ini);
		$this->db->where('h.data_conclusao <=', $data_fim);
		$this->db->where('h.excluido', 0);
		$this->db->where('h.validacao', 1);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}

	function getPontosJSON(
		$id_usuario = null,
		$id_empresa = null,
		$data_ini = null,
		$data_fim = null

	) {
		$this->db->select('a.pontos');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');
		$this->db->join('usuario_empresa usuemp', 'h.id_usuario = usuemp.id_usuario', 'left');
		$this->db->where('h.id_usuario', $id_usuario);
		$this->db->where('h.id_empresa', $id_empresa);
		$this->db->where('usuemp.ativo', 1);
		$this->db->where('h.data_conclusao >=', $data_ini);
		$this->db->where('h.data_conclusao <=', $data_fim);
		$this->db->where('h.excluido', 0);
		$this->db->where('h.validacao', 1);

		$records = $this->db->get('atividade_lancamento h')->result();

		$data = array(
			"aaData" => $records
		);

		return $data;
	}

	function getTotaisJSON(
		$id = null,
		$data_ini = null,
		$data_fim = null,
		$id_empresa = null

	) {
		$this->db->select('count(*) as allcount');
		$this->db->join('usuario_empresa usuemp', 'A.id_usuario = usuemp.id_usuario', 'left');
		$this->db->where('A.id_usuario', $id);
		$this->db->where('A.id_empresa', $id_empresa);
		$this->db->where('usuemp.ativo', 1);
		$this->db->where('A.data_conclusao >=', $data_ini);
		$this->db->where('A.data_conclusao <=', $data_fim);
		$this->db->where('A.excluido', 0);
		$this->db->where('A.validacao', 1);


		$records = $this->db->get('atividade_lancamento A')->result();

		$data = array('total' => $records[0]->allcount);

		return $data;
	}
}
