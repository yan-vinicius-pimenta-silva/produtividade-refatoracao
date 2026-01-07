<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_Deducao extends CI_Model
{
	public function cadastrar($data)
	{
		if ($this->db->insert('atividade_fiscal', $data)) {
			$id_os = $this->db->insert_id();
			$this->logger->logAction(
				'atividade_fiscal create',
				(array) $data
			);
			return $id_os;
		}
		return false;
	}


	function get(
		$draw = null,
		$start = null,
		$rowperpage = null,
		$columnIndex = null,
		$columnName = null,
		$columnSortOrder = null,
		$searchValue = null,
		$data_ini = null,
		$data_fim = null,
		$id_empresa = null
	) {
		## Search 
		$searchQuery = "";
		if ($searchValue != '') {
			$searchQuery = " (u.nome ilike '%" . $searchValue
				. "%' or a.tipo ilike '%" . $searchValue
				. "%' or h.documento ilike '%" . $searchValue
				. "%' or h.protocolo ilike '%" . $searchValue
				. "%' or h.rc ilike '%" . $searchValue . "%') ";
		}

		## Total number of records without filtering
		$this->db->select('count(*) as allcount');
		$this->db->where('af.data_exclusao', null);
		$this->db->where('af.id_empresa', $id_empresa);
		$records = $this->db->get('atividade_fiscal af')->result();
		$totalRecords = $records[0]->allcount;

		## Total number of record with filtering
		$this->db->select('count(*) as allcount');
		$this->db->join('atividade a', 'a.id = af.id_atividade');
		$this->db->join('tipo_atividade ta', 'a.id_tipo_atividade = ta.id');
		$this->db->where('af.data_exclusao', null);
		$this->db->where('af.id_empresa', $id_empresa);
		$this->db->where('af.data_conclusao >=', $data_ini);
		$this->db->where('af.data_conclusao <=', $data_fim);
		$this->db->where('ta.nome', 'DEDUCAO');
		$records = $this->db->get('atividade_fiscal af')->result();
		$totalRecordwithFilter = $records[0]->allcount;

		## Fetch records
		$this->db->select('af.*,        
        a.tipo,
        a.id as id_atividade,
        a.pontos,
        u.nome, 
        u.id as id_fiscal');
		$this->db->join('atividade a', 'a.id = af.id_atividade');
		$this->db->join('usuarios u', 'u.id = af.id_fiscal');
		$this->db->join('tipo_atividade ta', 'a.id_tipo_atividade = ta.id');
		$this->db->where('af.data_exclusao', null);
		$this->db->where('ta.nome', 'DEDUCAO');
		if ($searchQuery != '')
			$this->db->where($searchQuery);
		if ($data_ini != null);
		$this->db->where('af.data_conclusao >=', $data_ini);
		if ($data_fim != null);
		$this->db->where('af.data_conclusao <=', $data_fim);
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
}
