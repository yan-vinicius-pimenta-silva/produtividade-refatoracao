<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_Atividade_Fiscal extends CI_Model
{

	public function cadastrar($data, $arquivos = null)
	{
		$this->load->model('model_atividade_anexo', 'atividadeAnexo');
		$this->db->trans_start();
		$this->db->insert('atividade_fiscal', $data);
		$last_id = $this->db->insert_id();
		$this->atividadeAnexo->cadastrar($last_id, $arquivos);
		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			return false;
		}
		$this->logger->logAction('atividade_fiscal create', (array) $data);
		return true;
	}

	public function get(
		$id_usuario = null,
		$data_ini = null,
		$data_fim = null,
		$nome_fiscal = null,
		$n_documento = null,
		$n_protocolo = null,
		$id_empresa = null
	) {
		$this->db->select('af.*,
                           a.tipo,
                           a.id as id_atividade,
                           a.pontos,
                           u.nome, 
                           u.id as id_usuario');
		$this->db->from('atividade_fiscal af');
		$this->db->join('atividade a', 'a.id = af.id_atividade', 'left');
		$this->db->join('usuarios u', 'u.id = af.id_fiscal', 'left');

		$this->db->where('af.data_exclusao', null);
		if ($id_usuario != null)
			$this->db->where('af.id_fiscal', $id_usuario);
		if ($data_ini != null)
			$this->db->where('af.data_conclusao >=', $data_ini);
		if ($data_fim != null)
			$this->db->where('af.data_conclusao <=', $data_fim);
		if ($nome_fiscal != null)
			$this->db->like('u.nome', $nome_fiscal);
		if ($n_documento != null)
			$this->db->where('af.documento', $n_documento);
		if ($n_protocolo != null)
			$this->db->where('af.protocolo', $n_protocolo);
		if ($id_empresa != null)
			$this->db->where('af.id_empresa', $id_empresa);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
	}

	public function update($id_atividade, $data)
	{
		$this->db->where('id', $id_atividade);
		if ($this->db->update('atividade_fiscal', $data)) {
			$this->logger->logAction('atividade_fiscal update(ID: ' . $id_atividade . ')', (array) $data);
			return true;
		}
		return false;
	}

	function getAtividadesFiscalJSON(
		$draw = null,
		$start = null,
		$rowperpage = null,
		$columnIndex = null,
		$columnName = null,
		$columnSortOrder = null,
		$searchValue = null,
		$periodo = null,
		$id_usuario = null,
		$id_empresa = null
	) {

		## Search 
		$searchQuery = "";
		if ($searchValue != '') {
			$searchQuery = " (u.nome ilike '%" . $searchValue . "%' or a.tipo ilike '%" . $searchValue . "%' or af.documento ilike '%" . $searchValue . "%' or af.protocolo ilike '%" . $searchValue . "%' or af.rc ilike '%" . $searchValue . "%') ";
		}

		## Total number of records without filtering
		$this->db->select('count(*) as allcount');
		$this->db->where('atividade_fiscal.id_fiscal', $id_usuario);
		$this->db->where('atividade_fiscal.data_exclusao', null);
		// $this->db->where('atividade_fiscal.id_empresa', $id_empresa);
		$records = $this->db->get('atividade_fiscal')->result();
		$totalRecords = $records[0]->allcount;

		## Total number of record with filtering
		$this->db->select('count(*) as allcount');
		$this->db->where('atividade_fiscal.id_fiscal', $id_usuario);
		$this->db->where('atividade_fiscal.data_exclusao', null);
		// $this->db->where('atividade_fiscal.id_empresa', $id_empresa);
		$this->db->where('to_char(atividade_fiscal.data_conclusao, \'YYYY-MM\') =', $periodo);
		$records = $this->db->get('atividade_fiscal')->result();
		$totalRecordwithFilter = $records[0]->allcount;

		## Fetch records
		$this->db->select('af.*,        
        a.tipo,
        a.id as id_atividade,
        a.pontos,
        u.nome, 
        u.id as id_usuario');
		$this->db->join('atividade a', 'a.id = af.id_atividade', 'left');
		$this->db->join('usuarios u', 'u.id = af.id_fiscal', 'left');

		$this->db->where('af.data_exclusao', null);
		if ($searchQuery != '')
			$this->db->where($searchQuery);
		if ($periodo != null);
		$this->db->where('to_char(af.data_conclusao, \'YYYY-MM\') =', $periodo);
		if ($id_usuario != null)
			$this->db->where('af.id_fiscal', $id_usuario);
		if ($id_empresa != null)
			$this->db->where('af.id_empresa', $id_empresa);
		$this->db->order_by($columnName, $columnSortOrder);
		$this->db->limit($rowperpage, $start);
		$records = $this->db->get('atividade_fiscal af')->result();


		$data = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"aaData" => $records
		);

		return $data;
	}

	function alterar($id_atividade, $data)
	{
		$this->db->where('id', $id_atividade);
		if ($this->db->update('atividade_fiscal', $data)) {
			$this->logger->logAction('atividade_fiscal update(ID: ' . $id_atividade . ')', (array) $data);
			return true;
		}
		return false;
	}
	function buscaAtividadesConfirmadas($id_fiscal, $periodo = null)
	{
		$this->db->select('af.*, af.id_atividade, a.tipo as tipo_atividade, ta.nome as tipo_pontuacao, a.pontos as ponto, TO_CHAR(af.data_conclusao, \'YYYY-MM\') as periodo');
		$this->db->from('atividade_fiscal af');
		$this->db->join('atividade a', 'a.id = af.id_atividade');
		$this->db->join('tipo_atividade ta', 'ta.id = a.id_tipo_atividade');

		$this->db->where('af.data_exclusao', null);
		$this->db->where('af.data_validado <>', null);

		if ($periodo != null)
			$this->db->where("TO_CHAR(af.data_conclusao, 'YYYY-MM') =", $periodo);
		// if ($data_fim != null);
		// $this->db->where('af.data_conclusao <=', $data_fim);
		if ($id_fiscal != null)
			$this->db->where('af.id_fiscal', $id_fiscal);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
	}


	/**
	 * Validar atividade
	 *
	 * Quando executar esse método, deve executar o método para calcular os pontos do fiscal e salvar no banco
	 * @param [type] $id_atividade
	 * @param [type] $data
	 * @return void
	 */
	function validar($ids)
	{

		$this->db->trans_start();
		foreach ($ids['id'] as $id) {
			$this->db->where('id', $id);
			$this->db->update('atividade_fiscal', ['usuario_validado' => $this->session->userdata['logged_in']['nome'], 'data_validado' => date('Y-m-d')]);
			$this->logger->logAction('atividade_fiscal confirma(ID: ' . $id . ')', (array) ['validado' => 'S']);
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			return false;
		}
		return true;
	}


	function excluir($id_atividade, $value)
	{
		$data = array(
			'data_exclusao' => $value['data_exclusao'],
			'usuario_exclusao' => $value['usuario_exclusao'],
			'motivo_exclusao' => $value['motivo_exclusao']
		);

		$this->db->where('id', $id_atividade);
		if ($this->db->update('atividade_fiscal', $data)) {
			$this->logger->logAction('atividade_fiscal delete(ID: ' . $id_atividade . ')', (array) $data);
			return true;
		}

		return false;
	}

	function excluidas(
		$draw = null,
		$start = null,
		$rowperpage = null,
		$columnIndex = null,
		$columnName = null,
		$columnSortOrder = null,
		$searchValue = null,
		$data_ini = null,
		$data_fim = null,
		$id_user = null,
		$id_empresa = null
	) {
		## Search 
		$searchQuery = "";
		if ($searchValue != '') {
			$searchQuery = " (u.nome ilike '%" . $searchValue . "%' or a.tipo ilike '%" . $searchValue . "%' or af.rc ilike '%" . $searchValue . "%') ";
		}
		## Total number of records without filtering
		$this->db->select('count(*) as allcount');
		($id_user == null) ?: $this->db->where('af.id_fiscal', $id_user);
		$this->db->where('af.data_exclusao <>', null);
		$this->db->where('af.id_empresa', $id_empresa);
		$records = $this->db->get('atividade_fiscal af')->result();
		$totalRecords = $records[0]->allcount;

		## Total number of record with filtering
		$this->db->select('count(*) as allcount');
		($id_user == null) ?: $this->db->where('af.id_fiscal', $id_user);
		$this->db->where('af.data_exclusao <>', null);
		$this->db->where('af.id_empresa', $id_empresa);
		$this->db->where('af.data_exclusao >=', $data_ini);
		$this->db->where('af.data_exclusao <=', $data_fim);
		$records = $this->db->get('atividade_fiscal af')->result();
		$totalRecordwithFilter = $records[0]->allcount;

		## Fetch records
		$this->db->select('af.*,
                           a.tipo,
                           a.id as id_atividade,
                           a.pontos,
                           u.nome as nome_fiscal,
                           u.id as id_fiscal');
		$this->db->join('atividade a', 'a.id = af.id_atividade', 'left');
		$this->db->join('usuarios u', 'u.id = af.id_fiscal', 'left');
		$this->db->where('af.id_empresa', $id_empresa);
		$this->db->where('af.data_exclusao <>', null);
		if ($searchQuery != '')
			$this->db->where($searchQuery);
		if ($id_user != null)
			$this->db->where('af.id_fiscal', $id_user);
		if ($data_ini != null)
			$this->db->where('af.data_exclusao >=', $data_ini);
		if ($data_fim != null)
			$this->db->where('af.data_exclusao <=', $data_fim);
		$this->db->order_by($columnName, $columnSortOrder);
		$this->db->limit($rowperpage, $start);
		$records = $this->db->get('atividade_fiscal af')->result();

		$data = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"aaData" => $records
		);

		return $data;
	}
}
