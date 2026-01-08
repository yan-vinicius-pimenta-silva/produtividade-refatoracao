<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Model_Atividade_Chefe extends CI_Model
{
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
			$searchQuery = " (u.nome ilike '%" . $searchValue . "%' or a.tipo ilike '%" . $searchValue . "%' or h.documento ilike '%" . $searchValue . "%' or h.protocolo ilike '%" . $searchValue . "%' or h.rc ilike '%" . $searchValue . "%') ";
		}

		## Total number of records without filtering
		$this->db->select('count(*) as allcount');
		if ($validacao === 'S')
			$this->db->where('atividade_fiscal.data_validado <>', null);
		else
			$this->db->where('atividade_fiscal.data_validado', null);
		$this->db->where('atividade_fiscal.data_exclusao', null);
		$this->db->where('atividade_fiscal.id_empresa', $id_empresa);
		$records = $this->db->get('atividade_fiscal')->result();
		$totalRecords = $records[0]->allcount;

		## Total number of record with filtering
		$this->db->select('count(*) as allcount');
		if ($validacao === 'S')
			$this->db->where('atividade_fiscal.data_validado <>', null);
		else
			$this->db->where('atividade_fiscal.data_validado', null);
		$this->db->where('atividade_fiscal.data_exclusao', null);
		$this->db->where('atividade_fiscal.id_empresa', $id_empresa);
		$this->db->where('atividade_fiscal.data_conclusao >=', $data_ini);
		$this->db->where('atividade_fiscal.data_conclusao <=', $data_fim);
		$records = $this->db->get('atividade_fiscal')->result();
		$totalRecordwithFilter = $records[0]->allcount;

		## Fetch records
		$this->db->select('h.*,        
        a.tipo,
        a.id as id_atividade,
        a.pontos,
        u.nome, 
        u.id as id_fiscal');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');
		$this->db->join('usuarios u', 'u.id = h.id_fiscal', 'left');
		if ($validacao === 'S')
			$this->db->where('h.data_validado <>', null);
		else
			$this->db->where('h.data_validado', null);
		$this->db->where('h.data_exclusao', null);
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
		$records = $this->db->get('atividade_fiscal h')->result();


		$data = array(
			"draw" => intval($draw),
			"iTotalRecords" => $totalRecords,
			"iTotalDisplayRecords" => $totalRecordwithFilter,
			"aaData" => $records
		);

		return $data;
	}
}
