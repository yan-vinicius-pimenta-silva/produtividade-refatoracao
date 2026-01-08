<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Model_Atividade extends CI_Model
{
	function getAtividades(
		$id_empresa,
		$id_atividade = null,
		$nivel = null
	) {
		$this->db->select('
				a.id,
				a.tipo, 
				ta.nome, 
				a.pontos, 
				a.multiplicador
				');
		$this->db->from('atividade a');
		$this->db->join(
			'tipo_atividade ta',
			'ta.id = a.id_tipo_atividade'
		);
		$this->db->where('a.ativo', 1);
		$this->db->where('a.id_empresa', $id_empresa);
		$this->db->where('a.atividade_os', 0);
		$this->db->where('a.excluido', 0);

		if ($id_atividade != null) {
			$this->db->where('a.id', $id_atividade);
		}

		if ($nivel != null && $nivel == FISCAL) {
			$this->db->where_in(
				'a.id_tipo_atividade',
				[1, 2]
			);
		}

		if ($nivel != null && $nivel == CHEFE) {
			$this->db->where('a.id_tipo_atividade', 3);
		}

		$this->db->order_by('a.tipo', 'asc');

		$query = $this->db->get();
		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}


	function getAtividadesOs($id_empresa)
	{
		$this->db->select('*');
		$this->db->from('atividade A');
		$this->db->where('A.ativo', 1);
		$this->db->where('A.id_empresa', $id_empresa);
		$this->db->where('A.excluido', 0);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}

	function cadastrarAtividadeUsuario($data)
	{
		if ($this->db->insert('atividade_lancamento', $data)) {
			$last_id = $this->db->insert_id();
			$this->logger->logAction(
				'atividade_lancamento create',
				(array) $data
			);
			return $last_id;
		}
		return false;
	}


	function alterarAtividadeUsuario(
		$id_atividade,
		$data
	) {
		$this->db->where('id', $id_atividade);
		if ($this->db->update('atividade_lancamento', $data)) {
			$this->logger->logAction(
				'atividade_lancamento update(ID: ' . $id_atividade . ')',
				(array) $data
			);
			return true;
		}
		return false;
	}


	/**
	 * Validar atividade
	 *
	 * Quando executar esse método, deve executar o método para calcular os pontos do fiscal e salvar no banco
	 * @param [type] $id_atividade
	 * @param [type] $data
	 * @return void
	 */
	function validar(
		$id_atividade,
		$data
	) {
		$this->db->where('id', $id_atividade);
		if ($this->db->update('atividade_lancamento', $data)) {
			$this->logger->logAction(
				'atividade_lancamento update(ID: ' . $id_atividade . ')',
				(array) $data
			);
			return true;
		}
		return false;
	}


	function deletarAtividadeUsuario(
		$id_atividade,
		$value
	) {
		$data = array(
			'excluido' => 1,
			'data_update' => $value['data_update'],
			'nome_usuario_update' => $value['nome_usuario_update'],
			'motivo_exclusao' => $value['motivo_exclusao']
		);

		$this->db->where('id', $id_atividade);
		if ($this->db->update('atividade_lancamento', $data)) {
			$this->logger->logAction(
				'atividade_lancamento delete(ID: ' . $id_atividade . ')',
				(array) $data
			);
			return true;
		}

		return false;
	}


	function getHistoricoAtividadesFiscal(
		$id_usuario = null,
		$data_ini = null,
		$data_fim = null,
		$nome_fiscal = null,
		$n_documento = null,
		$n_protocolo = null,
		$id_empresa = null,
		$orderBy = null
	) {
		$this->db->select('h.*,
                           a.tipo,
                           a.id as id_atividade,
                           a.pontos,
                           u.nome, 
                           u.id as id_usuario');
		$this->db->from('atividade_lancamento h');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');
		$this->db->join('usuarios u', 'u.id = h.id_usuario', 'left');
		$this->db->join('usuario_empresa usuemp', 'u.id = usuemp.id_usuario', 'left');

		$this->db->where('h.excluido', 0);

		if ($id_usuario != null)
			$this->db->where('h.id_usuario', $id_usuario);
		if ($data_ini != null)
			$this->db->where('h.data_conclusao >=', $data_ini);
		if ($data_fim != null)
			$this->db->where('h.data_conclusao <=', $data_fim);
		if ($nome_fiscal != null)
			$this->db->like('u.nome', $nome_fiscal);
		if ($n_documento != null)
			$this->db->where('h.n_documento', $n_documento);
		if ($n_protocolo != null)
			$this->db->where('h.n_protocolo', $n_protocolo);
		if ($id_empresa != null)
			$this->db->where('h.id_empresa', $id_empresa);
		if ($orderBy != null)
			$this->db->order_by($orderBy);

		// $this->db->order_by('h.data_conclusao, h.id', 'asc');
		$this->db->where('h.validacao', 1);
		$this->db->distinct();

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
	}


	function getAtividadesFiscal(
		$id_usuario = null,
		$data_ini = null,
		$data_fim = null,
		$nome_fiscal = null,
		$n_documento = null,
		$n_protocolo = null,
		$id_empresa = null
	) {
		$this->db->select('h.*,
                           a.tipo,
                           a.id as id_atividade,
                           a.pontos,
                           u.nome, 
                           u.id as id_usuario');
		$this->db->from('atividade_lancamento h');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');
		$this->db->join('usuarios u', 'u.id = h.id_usuario', 'left');

		$this->db->where('h.excluido', 0);
		if ($id_usuario != null)
			$this->db->where('h.id_usuario', $id_usuario);
		if ($data_ini != null)
			$this->db->where('h.data_conclusao >=', $data_ini);
		if ($data_fim != null)
			$this->db->where('h.data_conclusao <=', $data_fim);
		if ($nome_fiscal != null)
			$this->db->like('u.nome', $nome_fiscal);
		if ($n_documento != null)
			$this->db->where('h.n_documento', $n_documento);
		if ($n_protocolo != null)
			$this->db->where('h.n_protocolo', $n_protocolo);
		if ($id_empresa != null)
			$this->db->where('h.id_empresa', $id_empresa);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
	}


	function getExcluidas(
		$id_usuario = null,
		$data_ini = null,
		$data_fim = null,
		$nome_fiscal = null,
		$n_documento = null,
		$n_protocolo = null,
		$id_empresa = null
	) {
		$this->db->select('h.*,
                           a.tipo,
                           a.id as id_atividade,
                           a.pontos,
                           u.nome, 
                           u.id as id_usuario');
		$this->db->from('atividade_lancamento h');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');
		$this->db->join('usuarios u', 'u.id = h.id_usuario', 'left');

		if ($id_usuario != null)
			$this->db->where('h.id_usuario', $id_usuario);
		if ($data_ini != null)
			$this->db->where('h.data_conclusao >=', $data_ini);
		if ($data_fim != null)
			$this->db->where('h.data_conclusao <=', $data_fim);
		if ($nome_fiscal != null)
			$this->db->like('u.nome', $nome_fiscal);
		if ($n_documento != null)
			$this->db->where('h.n_documento', $n_documento);
		if ($n_protocolo != null)
			$this->db->where('h.n_protocolo', $n_protocolo);
		if ($id_empresa != null)
			$this->db->where('h.id_empresa', $id_empresa);

		$this->db->where('h.excluido', 1);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
	}


	function getTotaisAtividades(
		$id_usuario,
		$data_ini,
		$data_fim,
		$id_empresa
	) {
		$this->db->select('
            a.id,
            a.descricao,
            a.pontos,
            COUNT(at.id) quantidade');
		$this->db->from('atividade a');
		$this->db->join('atividade_lancamento at', 'a.id = at.id_atividade 
            AND at.id_usuario = ' . $id_usuario . '
            AND at.data_conclusao >= \'' . $data_ini . '\'
            AND at.data_conclusao <= \'' . $data_fim . '\'
            AND at.excluido = 0
            AND at.validacao = 1', 'LEFT');
		$this->db->where('a.excluido', 0);
		$this->db->where('a.id_empresa', $id_empresa);

		$this->db->group_by('a.id');
		$this->db->order_by('a.descricao');

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
	}


	function getAtividadesJSON(
		$draw = null,
		$start = null,
		$rowperpage = null,
		$columnIndex = null,
		$columnName = null,
		$columnSortOrder = null,
		$searchValue = null,
		$validacao = null,
		$data_ini = null,
		$data_fim = null,
		$id_empresa = null
	) {
		## Search 
		$searchQuery = "";
		if ($searchValue != '') {
			$searchQuery = " (u.nome ilike '%" . $searchValue . "%' or a.tipo ilike '%" . $searchValue . "%' or h.n_documento ilike '%" . $searchValue . "%' or h.n_protocolo ilike '%" . $searchValue . "%' or h.rc ilike '%" . $searchValue . "%') ";
		}

		## Total number of records without filtering
		$this->db->select('count(*) as allcount');
		$this->db->where('atividade_lancamento.validacao', $validacao);
		$this->db->where('atividade_lancamento.excluido', 0);
		$this->db->where('atividade_lancamento.id_empresa', $id_empresa);
		$records = $this->db->get('atividade_lancamento')->result();
		$totalRecords = $records[0]->allcount;

		## Total number of record with filtering
		$this->db->select('count(*) as allcount');
		$this->db->where('atividade_lancamento.validacao', $validacao);
		$this->db->where('atividade_lancamento.excluido', 0);
		$this->db->where('atividade_lancamento.id_empresa', $id_empresa);
		$this->db->where('atividade_lancamento.data_conclusao >=', $data_ini);
		$this->db->where('atividade_lancamento.data_conclusao <=', $data_fim);
		$records = $this->db->get('atividade_lancamento')->result();
		$totalRecordwithFilter = $records[0]->allcount;

		## Fetch records
		$this->db->select('h.*,        
        a.tipo,
        a.id as id_atividade,
        a.pontos,
        u.nome, 
        u.id as id_usuario');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');
		$this->db->join('usuarios u', 'u.id = h.id_usuario', 'left');
		$this->db->where('h.validacao', $validacao);
		$this->db->where('h.excluido', 0);
		if ($searchQuery != '')
			$this->db->where($searchQuery);
		if ($data_ini != null);
		$this->db->where('h.data_conclusao >=', $data_ini);
		if ($data_fim != null);
		$this->db->where('h.data_conclusao <=', $data_fim);
		if ($id_empresa != null)
			$this->db->where('h.id_empresa', $id_empresa);
		$this->db->order_by($columnName, $columnSortOrder);
		$this->db->limit($rowperpage, $start);
		$records = $this->db->get('atividade_lancamento h')->result();


		$data = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"aaData" => $records
		);

		return $data;
	}


	function getAtividadeExcluidoJSON(
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
			$searchQuery = " (u.nome ilike '%" . $searchValue . "%' or a.tipo ilike '%" . $searchValue . "%' or h.rc ilike '%" . $searchValue . "%') ";
		}
		## Total number of records without filtering
		$this->db->select('count(*) as allcount');
		($id_user == null) ?: $this->db->where('atividade_lancamento.id_usuario', $id_user);
		$this->db->where('atividade_lancamento.excluido', 1);
		$this->db->where('atividade_lancamento.id_empresa', $id_empresa);
		$records = $this->db->get('atividade_lancamento')->result();
		$totalRecords = $records[0]->allcount;

		## Total number of record with filtering
		$this->db->select('count(*) as allcount');
		($id_user == null) ?: $this->db->where('atividade_lancamento.id_usuario', $id_user);
		$this->db->where('atividade_lancamento.excluido', 1);
		$this->db->where('atividade_lancamento.id_empresa', $id_empresa);
		$this->db->where('atividade_lancamento.data_update >=', $data_ini);
		$this->db->where('atividade_lancamento.data_update <=', $data_fim);
		$records = $this->db->get('atividade_lancamento')->result();
		$totalRecordwithFilter = $records[0]->allcount;

		## Fetch records
		$this->db->select('h.*,
                           a.tipo,
                           a.id as id_atividade,
                           a.pontos,
                           u.nome as nome_fiscal,
                           u.id as id_usuario');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');
		$this->db->join('usuarios u', 'u.id = h.id_usuario', 'left');
		$this->db->where('h.id_empresa', $id_empresa);
		$this->db->where('h.excluido', 1);
		if ($searchQuery != '')
			$this->db->where($searchQuery);
		if ($id_user != null)
			$this->db->where('h.id_usuario', $id_user);
		if ($data_ini != null)
			$this->db->where('h.data_update >=', $data_ini);
		if ($data_fim != null)
			$this->db->where('h.data_update <=', $data_fim);
		$this->db->order_by($columnName, $columnSortOrder);
		$this->db->limit($rowperpage, $start);
		$records = $this->db->get('atividade_lancamento h')->result();

		$data = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"aaData" => $records
		);

		return $data;
	}


	function getAtividadesFiscalJSON(
		$draw = null,
		$start = null,
		$rowperpage = null,
		$columnIndex = null,
		$columnName = null,
		$columnSortOrder = null,
		$searchValue = null,
		$data_ini = null,
		$data_fim = null,
		$id_usuario = null,
		$id_empresa = null
	) {
		## Search 
		$searchQuery = "";
		if ($searchValue != '') {
			$searchQuery = " (u.nome ilike '%" . $searchValue . "%' or a.tipo ilike '%" . $searchValue . "%' or h.n_documento ilike '%" . $searchValue . "%' or h.n_protocolo ilike '%" . $searchValue . "%' or h.rc ilike '%" . $searchValue . "%') ";
		}

		## Total number of records without filtering
		$this->db->select('count(*) as allcount');
		$this->db->where('atividade_lancamento.id_usuario', $id_usuario);
		$this->db->where('atividade_lancamento.excluido', 0);
		$this->db->where('atividade_lancamento.id_empresa', $id_empresa);
		$records = $this->db->get('atividade_lancamento')->result();
		$totalRecords = $records[0]->allcount;

		## Total number of record with filtering
		$this->db->select('count(*) as allcount');
		$this->db->where('atividade_lancamento.id_usuario', $id_usuario);
		$this->db->where('atividade_lancamento.excluido', 0);
		$this->db->where('atividade_lancamento.id_empresa', $id_empresa);
		$this->db->where('atividade_lancamento.data_conclusao >=', $data_ini);
		$this->db->where('atividade_lancamento.data_conclusao <=', $data_fim);
		$records = $this->db->get('atividade_lancamento')->result();
		$totalRecordwithFilter = $records[0]->allcount;

		## Fetch records
		$this->db->select('h.*,        
        a.tipo,
        a.id as id_atividade,
        a.pontos,
        u.nome, 
        u.id as id_usuario');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');
		$this->db->join('usuarios u', 'u.id = h.id_usuario', 'left');

		$this->db->where('h.excluido', 0);
		if ($searchQuery != '')
			$this->db->where($searchQuery);
		if ($data_ini != null);
		$this->db->where('h.data_conclusao >=', $data_ini);
		if ($data_fim != null);
		$this->db->where('h.data_conclusao <=', $data_fim);
		if ($id_usuario != null)
			$this->db->where('h.id_usuario', $id_usuario);
		if ($id_empresa != null)
			$this->db->where('h.id_empresa', $id_empresa);
		$this->db->order_by($columnName, $columnSortOrder);
		$this->db->limit($rowperpage, $start);
		$records = $this->db->get('atividade_lancamento h')->result();


		$data = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"aaData" => $records
		);

		return $data;
	}


	function getMultiplicadorJson($id_atividade = null)
	{
		$this->db->select('a.multiplicador');
		$this->db->from('atividade a');
		$this->db->where('a.id', $id_atividade);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
	}
}
