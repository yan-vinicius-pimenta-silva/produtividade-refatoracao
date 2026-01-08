<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Lixeira extends SO_Controller
{

	private $id_user;
	private $id_empresa;
	private $nome_usuario;

	function __construct()
	{
		parent::__construct();
		$this->logado();
		$this->load->model('model_atividade_fiscal', 'AtividadeFiscal');
		$this->load->model('model_usuario');
		$this->load->model('model_os');

		$this->id_user = $this->session->userdata['logged_in']['id'];
		$this->nome_usuario = $this->session->userdata['logged_in']['nome'];
		$this->id_nivel = $this->session->userdata['logged_in']['nivel'];
		$this->id_empresa = $this->session->userdata['logged_in']['id_empresa'];
		$this->parametros_empresa = $this->session->userdata['logged_in']['parametros_empresa'];
		$this->data = array();
	}

	function index()
	{
		$this->load->view('excluidas');
	}

	function getAtividadeExcluidoJSON()
	{
		$postData = $this->input->post();
		$draw = $postData['draw'];
		$start = $postData['start'];
		$rowperpage = $postData['length']; // Rows display per page
		$columnIndex = $postData['order'][0]['column']; // Column index
		$columnName = $postData['columns'][$columnIndex]['data']; // Column name
		$columnSortOrder = $postData['order'][0]['dir']; // asc or desc
		$searchValue = $postData['search']['value']; // Search value
		$data = array();
		$data_ini = (isset($postData['data_ini'])) ? date('Y-m-d 23:59:59', strtotime($postData['data_ini'])) : '';
		$data_fim = (isset($postData['data_fim'])) ? date('Y-m-d 23:59:59', strtotime($postData['data_fim'])) : '';
		if ($this->id_nivel != 1) {
			$query = $this->AtividadeFiscal->excluidas($draw, $start, $rowperpage, $columnIndex, $columnName, $columnSortOrder, $searchValue, $data_ini, $data_fim, $this->id_user, $this->id_empresa);
		} else {
			$query = $this->AtividadeFiscal->excluidas($draw, $start, $rowperpage, $columnIndex, $columnName, $columnSortOrder, $searchValue, $data_ini, $data_fim, null, $this->id_empresa);
		}

		foreach ($query["aaData"] as $key => $value) {
			$data[] = array(
				'id' => $value->id,
				'tipo' => $value->tipo,
				'data_exclusao' => "<span class='hidden'>" . $value->data_exclusao . "</span>" . date('d/m/Y', strtotime($value->data_exclusao)),
				'protocolo' => ($value->protocolo) ?? '--',
				'documento' => ($value->documento) ?? '--',
				'rc' => ($value->rc) ? $value->rc : '--',
				'cpf_cnpj' => ($value->cpf_cnpj) ?? '--',
				'pontos' => ($value->pontuacao_total) ? $value->pontuacao_total / 10 : '--',
				'quantidade' => ($value->quantidade) ?? '--',
				'valor' => number_format((float) $value->valor / 100, 2, ',', '.'),
				'arquivos' => "<a style=\"color:#555555;\" href=\"#\" id=\"buscar_arquivos\" data-id-lancamento=\"$value->id\" title=\"Documento\" ><i class=\"material-icons\">insert_drive_file</i></a>",
				'rc' => ($value->rc) ? $value->rc : '--',
				'pontos' => ($value->pontos) ? $value->pontos : '--',
				'nome_fiscal' => $value->nome_fiscal,
				'usuario_exclusao' => ($value->usuario_exclusao) ?? '--',
				'motivo_exclusao' => ($value->motivo_exclusao) ?? '--',
			);
		}

		## Response
		$response = array(
			"draw" => intval($query["draw"]),
			"iTotalRecords" => $query["iTotalRecords"],
			"iTotalDisplayRecords" => $query["iTotalDisplayRecords"],
			"aaData" => $data,
			"data_ini" => $data_ini,
			"data_fim" => $data_fim
		);

		echo json_encode($response);
	}
}
