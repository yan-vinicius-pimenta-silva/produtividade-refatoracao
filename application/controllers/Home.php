<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends SO_Controller
{
	private $id_user;
	private $id_empresa;
	private $niveisPermitidos = [CHEFE, SECRETARIO, ADM];

	function __construct()
	{
		parent::__construct();
		$this->logado();
		$this->load->model('model_atividade');
		$this->load->model('model_atividade_chefe');
		$this->load->model('model_home');
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
		if ($this->id_nivel == CHEFE) {
			$data["atividade"] = $this->model_atividade
				->getAtividadesFiscal(
					null,
					date(
						'Y-m-d',
						mktime(0, 0, 0, date('m'), 1, date('Y'))
					),
					date(
						'Y-m-d',
						mktime(0, 0, 0, date('m') + 1, 0, date('Y'))
					),
					null,
					null,
					null,
					$this->id_empresa
				);

			$this->load
				->view(
					'chefe/home',
					$data
				);
		} elseif ($this->id_nivel == FISCAL) {
			$this->load->model('model_atividade_fiscal');
			$this->data['data_ini'] = null;
			$this->data['data_fim'] = null;
			$this->data['nome_usuario'] = $this->nome_usuario;
			$this->data['total_pontos'] = $this->calcularTotais();
			$this->data["historico_atividade"] = $this->model_atividade_fiscal
				->get(
					$this->id_user,
					null,
					null,
					null,
					null,
					null,
					$this->id_empresa
				);
			$this->data["atividade"] = $this->model_atividade
				->getAtividades(
					$this->id_empresa,
					null,
					FISCAL
				);
			$data = date(
				'Y-m-d H:i:s',
				strtotime('+10 days')
			);
			$this->data["historico_ordem_servico"] = $this->model_os
				->getOsNotificacao(
					$this->id_user,
					$this->id_empresa,
					$data
				);
			$this->data['data_atual'] = date('Y-M-d');
			$this->load->view(
				'fiscal/home_fiscal',
				$this->data
			);
		} else {
			$this->load->view(
				'commom/home',
				$this->data
			);
		}
	}


	function index_antigo()
	{
		if (in_array($this->id_nivel, $this->niveisPermitidos)) {
			$data["atividade"] = $this->model_atividade
				->getAtividadesFiscal(
					null,
					date(
						'Y-m-d',
						mktime(0, 0, 0, date('m'), 1, date('Y'))
					),
					date(
						'Y-m-d',
						mktime(0, 0, 0, date('m') + 1, 0, date('Y'))
					),
					null,
					null,
					null,
					$this->id_empresa
				);
			$this->load->view('antigas/home', $data);
		} elseif ($this->id_nivel == FISCAL) {
			$this->data['data_ini'] = null;
			$this->data['data_fim'] = null;
			$this->data['nome_usuario'] = $this->nome_usuario;
			$this->data['total_pontos'] = $this->calcularTotais();
			$this->data["historico_atividade"] = $this->model_atividade
				->getAtividadesFiscal(
					$this->id_user,
					null,
					null,
					null,
					null,
					null,
					$this->id_empresa
				);
			$this->data["atividade"] = $this->model_atividade
				->getAtividades($this->id_empresa);
			$data = date(
				'Y-m-d H:i:s',
				strtotime('+10 days')
			);
			$this->data["historico_ordem_servico"] = $this->model_os
				->getOsNotificacao(
					$this->id_user,
					$this->id_empresa,
					$data
				);
			$this->data['data_atual'] = date('Y-M-d');
			$this->load->view(
				'antigas/home_fiscal',
				$this->data
			);
		}
	}


	function getOsNotificacao()
	{
		$id_user = $this->id_user;
		$data = date('Y-m-d H:i:s', strtotime('+10 days'));
		$id_empresa = $this->id_empresa;

		$result = $this->model_os
			->getOsNotificacao(
				$id_user,
				$id_empresa,
				$data
			);

		if ($result) {
			$data = array();
			foreach ($result as $value) {
				$data[] = array($value);
			}

			echo json_encode(
				array('status' => 'true')
			);
		} else {
			echo json_encode(
				array('status' => 'false')
			);
		}
	}


	function consultarHistorico()
	{
		$this->data['data_ini'] = str_replace(
			'/',
			'-',
			$this->input->post('data_ini')
		);
		$this->data['data_fim'] = str_replace(
			'/',
			'-',
			$this->input->post('data_fim')
		);

		$this->data["historico_atividade"] = $this->model_atividade
			->getAtividadesFiscal(
				$this->id_user,
				date(
					'Y-m-d',
					strtotime($this->data['data_ini'])
				),
				date(
					'Y-m-d',
					strtotime($this->data['data_fim'])
				),
				null,
				null,
				null,
				$this->id_empresa
			);
		$this->data["atividade"] = $this->model_atividade
			->getAtividades(
				$this->id_empresa
			);
		$this->data['total_pontos'] = $this->calcularTotais(
			$this->data['data_ini'],
			$this->data['data_fim']
		);

		$this->load->view(
			'fiscal/home_fiscal',
			$this->data
		);
	}


	function consultarAtividades()
	{
		$this->data['data_ini'] = str_replace(
			'/',
			'-',
			$this->input->post('data_ini')
		);
		$this->data['data_fim'] = str_replace(
			'/',
			'-',
			$this->input->post('data_fim')
		);
		$this->data['nome'] = strtoupper(
			$this->input->post('nome')
		);
		$this->data['n_documento'] = strtoupper(
			$this->input->post('n_doc')
		);
		$this->data['n_protocolo'] = strtoupper(
			$this->input->post('n_protocolo')
		);

		$this->data["atividade"] = $this->model_atividade
			->getHistoricoAtividadesFiscal(
				null,
				date(
					'Y-m-d',
					strtotime($this->data['data_ini'])
				),
				date(
					'Y-m-d',
					strtotime($this->data['data_fim'])
				),
				$this->data['nome'],
				$this->data['n_documento'],
				$this->data['n_protocolo'],
				$this->id_empresa,
				null
			);

		$this->load->view(
			'home',
			$this->data
		);
	}


	function calcularTotais(
		$data_ini = null,
		$data_fim = null
	) {
		if ($data_ini && $data_fim) {
			$this->data['total_atividade'] = intval(
				$this->model_home
					->getTotais(
						$this->id_user,
						$data_ini,
						$data_fim,
						$this->id_empresa
					)
			);
			$this->data['consulta_pontos'] = $this->model_home
				->getPontos(
					$this->id_user,
					$data_ini,
					$data_fim,
					$this->id_empresa
				);
		} else {
			$this->data['total_atividade'] = intval(
				$this->model_home->getTotais(
					$this->id_user,
					date(
						'Y-m-d',
						mktime(0, 0, 0, date('m'), 1, date('Y'))
					),
					date(
						'Y-m-d',
						mktime(0, 0, 0, date('m') + 1, 0, date('Y'))
					),
					$this->id_empresa
				)
			);
			$this->data['consulta_pontos'] = $this->model_home
				->getPontos(
					$this->id_user,
					date(
						'Y-m-d',
						mktime(0, 0, 0, date('m'), 1, date('Y'))
					),
					date(
						'Y-m-d',
						mktime(0, 0, 0, date('m') + 1, 0, date('Y'))
					),
					$this->id_empresa
				);
		}

		$this->data['total_pontos'] = 0.0;

		if ($this->data['consulta_pontos']) {
			for ($i = 0; $i < count($this->data['consulta_pontos']); $i++) {
				$this->data['total_pontos'] += floatval($this->data['consulta_pontos'][$i]->pontos);
			}
		}
		return $this->data['total_pontos'];
	}


	function getAtividadesJSON()
	{
		$postData = $this->input->post();
		$draw = $postData['draw'];
		$start = $postData['start'];
		$rowperpage = $postData['length']; // Rows display per page

		foreach ($postData['order'] as $key => $index) {
			$columnIndex[] = $index['column'];
			$columnName[] = $postData['columns'][$index['column']]['data'];
			$columnSortOrder[] = $postData['order'][$key]['dir'];
		}
		$searchValue = $postData['search']['value']; // Search value
		$validacao = $postData['validacao']; //filtro de validação
		$data = array();
		$data_ini = (isset($postData['data_ini']))
			? date(
				'Y-m-d',
				strtotime(
					$postData['data_ini']
				)
			)
			: date(
				'Y-m-d',
				mktime(0, 0, 0, date('m'), 1, date('Y'))
			);
		$data_fim = (isset($postData['data_fim']))
			? date(
				'Y-m-d',
				strtotime($postData['data_fim'])
			) : date(
				'Y-m-d',
				mktime(0, 0, 0, date('m') + 1, 0, date('Y'))
			);
		$query = $this->model_atividade_chefe
			->getAtividadesJSON(
				$draw,
				$start,
				$rowperpage,
				$columnIndex,
				implode(',', $columnName),
				implode(',', $columnSortOrder),
				$searchValue,
				$validacao,
				$data_ini,
				$data_fim,
				$this->id_empresa
			);

		foreach ($query["aaData"] as $key => $value) {
			$checked = ($value->data_validado != NULL)
				? "checked"
				: "";

			$nome_campo = ($value->data_validado != NULL)
				? "validadas"
				: "importar_dados[]";

			$data[] = array(
				'id' => $value->id,
				'tipo' => $value->tipo,
				'data_conclusao' => "<span class='hidden'>"
					. $value->data_conclusao
					. "</span>"
					. date(
						'd/m/Y',
						strtotime($value->data_conclusao)
					),
				'protocolo' => ($value->protocolo)
					? $value->protocolo
					: '--',
				'documento' => ($value->documento)
					? $value->documento
					: '--',
				'rc' => ($value->rc)
					? $value->rc
					: '--',
				'cpf_cnpj' => ($value->cpf_cnpj)
					? $value->cpf_cnpj
					: '--',
				'pontos' => ($value->pontuacao_total)
					? $value->pontuacao_total / 10
					: '--',
				'quantidade' => ($value->quantidade)
					? $value->quantidade / 10
					: '--',
				'valor' => number_format(
					(float) $value->valor / 100,
					2,
					',',
					'.'
				),
				'nome' => $value->nome,
				'observacao' => ($value->observacao)
					? $value->observacao
					: '--',
				'arquivos' => "<a style=\"color:#555555;\" href=\"#\" id=\"buscar_arquivos\" data-id-lancamento=\"$value->id\" title=\"Documento\" ><i class=\"material-icons\">insert_drive_file</i></a>",
				'validar' => ' 
                <input title="Selecionar" name="' . $nome_campo . '" type="checkbox" id=' . $value->id . ' class="filled-in chk-col-green" value=' . $value->id . ' ' . $checked . '> <label for=' . $value->id . '>  </label> ',
				'opcoes' => '
                <button type="button" class="btn btn-danger waves-effect open-exclusao"
                data-toggle="modal" data-target="#modal_exclusao" data-id="' . $value->id . '">
                <i class="material-icons">delete</i>
                </button>',
				'usuario_validacao' => ($value->usuario_validado) ?? '--',
				'usuario_deducao' => ($value->usuario_deducao) ?? '--',
				'motivo_deducao' => ($value->motivo_deducao) ?? '--',
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

		echo json_encode(
			$response
		);
	}


	function getAtividadesFiscalJSON()
	{
		$this->load->model(
			'model_atividade_fiscal',
			'atividadeFiscal'
		);
		$this->load->model(
			'model_atividade_anexo',
			'atividadeAnexo'
		);
		$postData = $this->input->get();
		$draw = $postData['draw'];
		$start = $postData['start'];
		$rowperpage = $postData['length']; // Rows display per page

		foreach ($postData['order'] as $key => $index) {
			$columnIndex[] = $index['column'];
			$columnName[] = $postData['columns'][$index['column']]['data'];
			$columnSortOrder[] = $postData['order'][$key]['dir'];
		}
		$searchValue = $postData['search']['value']; // Search value

		$data = array();
		$periodo = (isset($postData['periodo']))
			? date(
				'Y-m',
				strtotime($postData['periodo'])
			)
			: date('Y-m');
		$query = $this->atividadeFiscal
			->getAtividadesFiscalJSON(
				$draw,
				$start,
				$rowperpage,
				$columnIndex,
				implode(',', $columnName),
				implode(',', $columnSortOrder),
				$searchValue,
				$periodo,
				$this->id_user,
				$this->id_empresa
			);

		foreach ($query["aaData"] as $key => $value) {

			if ($value->data_validado !== null) {
				$validacao = '<span style="style="align-content: center;" class="badge bg-green"><i class="material-icons">done</i></span>';
			} else {
				$validacao =  '<span style="align-content: center;" class="badge bg-gray"><i class="material-icons" >close</i></span>';
			}

			$data_conclusao =  strtotime($value->data_conclusao);
			$status = 'disabled';

			if ($this->isAtividadeinMonthDate($data_conclusao) || $this->id_empresa != 4) {
				$status = 'data-target="#modal-editar"';
			}

			$buttons = '
                <button type="button" style="margin-top:3px;margin-left:3px;background-color:#40B2A6;color:white;" class="btn waves-effect open-editar"
                data-id="' . $value->id . '"
                data-tipo="' . $value->tipo . '"
                data-data="' .  date('d/m/Y', $data_conclusao) . '"
                data-n_documento = "' . $value->documento . '"
                data-n_protocolo = "' . $value->protocolo . '"
                data-rc = "' . $value->rc . '"
                data-observacao = "' . $value->observacao . '"
                title="Editar informações" data-toggle="modal"' . $status . '>
                <i class="material-icons">create</i>
                </button>';

			if ($value->data_validado === null) {
				$buttons = $buttons . '<button type="button" style="margin-top:3px;margin-left:3px;" class="btn btn-danger waves-effect open-exclusao"
				data-toggle="modal"
				data-target="#modal_exclusao_fiscal"
				data-id="' . $value->id . '">
				<i class="material-icons">delete</i>
				</button>';
			}

			$data[] = array(
				'id' => $value->id,
				'tipo' => $value->tipo,
				'data_conclusao' => "<span class='hidden'>" . $value->data_conclusao . "</span>" . date('d/m/Y', strtotime($value->data_conclusao)),
				'n_protocolo' => ($value->protocolo) ??  '--',
				'n_documento' => ($value->documento) ?? '--',
				'rc' => ($value->rc)
					? $value->rc
					: '--',
				'cpf_cnpj' => ($value->cpf_cnpj)
					? $value->cpf_cnpj
					: '--',
				'pontos' => ($value->pontuacao_total)
					? $value->pontuacao_total / 10
					: '--',
				'quantidade' => ($value->quantidade)
					? $value->quantidade / 10
					: '--',
				'valor' => number_format(
					(float) $value->valor / 100,
					2,
					',',
					'.'
				),
				'observacao' => ($value->observacao) ?? '--',
				'validar' =>  $validacao,
				'arquivos' => "<a style=\"color:#555555;\" href=\"#\" id=\"buscar_arquivos\" data-id-lancamento=\"$value->id\" title=\"Documento\" ><i class=\"material-icons\">insert_drive_file</i></a>",
				'opcoes' => $buttons,
				'usuario_validacao' => ($value->usuario_validado) ?? '--',
				'usuario_deducao' => ($value->usuario_deducao) ?? '--',
				'motivo_deducao' => ($value->motivo_deducao) ?? '--',
			);
		}
		## Response
		$response = array(
			"draw" => intval($query["draw"]),
			"iTotalRecords" => $query["iTotalRecords"],
			"iTotalDisplayRecords" => $query["iTotalDisplayRecords"],
			"aaData" => $data,
			"periodo" => $periodo,
		);

		echo json_encode($response);
	}


	private function isAtividadeInMonthDate($data_conclusao)
	{
		$today = new DateTime();
		$currentMonth = $today->format('m');
		$dataConclusaoMonth = date(
			'm',
			$data_conclusao
		);
		$lastMonth = date(
			'm',
			strtotime("last month")
		);

		if ($dataConclusaoMonth == $currentMonth) {
			return true;
		}
		if (($dataConclusaoMonth ==  $lastMonth) && $today->format('d') == 1) {
			return true;
		}

		return false;
	}


	function getPontosJSON()
	{
		$postData = $this->input->post();

		$data = array();
		$data_ini = (isset($postData['data_ini']))
			? date(
				'Y-m-d',
				strtotime($postData['data_ini'])
			) : date(
				'Y-m-d',
				mktime(
					0,
					0,
					0,
					date('m'),
					1,
					date('Y')
				)
			);
		$data_fim = (isset($postData['data_fim']))
			? date(
				'Y-m-d',
				strtotime($postData['data_fim'])
			) : date(
				'Y-m-d',
				mktime(0, 0, 0, date('m') + 1, 0, date('Y'))
			);

		$this->data['consulta_pontos'] = $this->model_home
			->getPontos(
				$this->id_user,
				$data_ini,
				$data_fim,
				$this->id_empresa
			);
		$query = $this->model_home
			->getPontosJSON(
				$this->id_user,
				$this->id_empresa,
				$data_ini,
				$data_fim
			);

		$this->data['total_pontos'] = 0.0;

		if ($this->data['consulta_pontos']) {
			for ($i = 0; $i < count($this->data['consulta_pontos']); $i++) {
				$this->data['total_pontos'] += floatval($this->data['consulta_pontos'][$i]->pontos);
			}
		}

		foreach ($query["aaData"] as $key => $value) {
			$data[] = array(
				'pontos' => $value->pontos,
			);
		}

		## Response
		$response = array(
			"aaData" => $data,
			"data_ini" => $data_ini,
			"data_fim" => $data_fim,
			"total" => $this->data['total_pontos']
		);

		echo json_encode($response);
	}

	function getTotalJson()
	{
		$postData = $this->input->post();

		$data = array();
		$data_ini = (isset($postData['data_ini']))
			? date(
				'Y-m-d',
				strtotime($postData['data_ini'])
			) : date(
				'Y-m-d',
				mktime(0, 0, 0, date('m'), 1, date('Y'))
			);
		$data_fim = (isset($postData['data_fim']))
			? date(
				'Y-m-d',
				strtotime($postData['data_fim'])
			) : date(
				'Y-m-d',
				mktime(0, 0, 0, date('m') + 1, 0, date('Y'))
			);

		$query = $this->model_home
			->getTotaisJson(
				$this->id_user,
				$data_ini,
				$data_fim,
				$this->id_empresa
			);

		$data = $query['total'];

		$response = array(
			"aaData" => $data,
			"data_ini" => $data_ini,
			"data_fim" => $data_fim,
		);

		echo json_encode($response);
	}


	function getFormType()
	{
		$this->input->post();
	}


	function getAtividadesAntigas()
	{
		$postData = $this->input->post();
		$draw = $postData['draw'];
		$start = $postData['start'];
		$rowperpage = $postData['length']; // Rows display per page

		foreach ($postData['order'] as $key => $index) {
			$columnIndex[] = $index['column'];
			$columnName[] = $postData['columns'][$index['column']]['data'];
			$columnSortOrder[] = $postData['order'][$key]['dir'];
		}
		$searchValue = $postData['search']['value']; // Search value
		$validacao = $postData['validacao']; //filtro de validação
		$data = array();
		$data_ini = (isset($postData['data_ini']))
			? date(
				'Y-m-d',
				strtotime($postData['data_ini'])
			) : date(
				'Y-m-d',
				mktime(0, 0, 0, date('m'), 1, date('Y'))
			);
		$data_fim = (isset($postData['data_fim']))
			? date(
				'Y-m-d',
				strtotime($postData['data_fim'])
			) : date(
				'Y-m-d',
				mktime(0, 0, 0, date('m') + 1, 0, date('Y'))
			);
		$query = $this->model_atividade
			->getAtividadesJSON(
				$draw,
				$start,
				$rowperpage,
				$columnIndex,
				implode(',', $columnName),
				implode(',', $columnSortOrder),
				$searchValue,
				$validacao,
				$data_ini,
				$data_fim,
				$this->id_empresa
			);

		foreach ($query["aaData"] as $key => $value) {
			$checked = ($value->validacao == 1)
				? "checked"
				: "";

			$nome_campo = ($value->validacao == 1)
				? "validadas"
				: "importar_dados[]";

			$data[] = array(
				'id' => $value->id,
				'tipo' => $value->tipo,
				'data_conclusao' => "<span class='hidden'>" . $value->data_conclusao . "</span>"
					. date('d/m/Y', strtotime($value->data_conclusao)),
				'n_protocolo' => ($value->n_protocolo) ? $value->n_protocolo : '--',
				'n_documento' => ($value->n_documento) ? $value->n_documento : '--',
				'rc' => ($value->rc) ? $value->rc : '--',
				'pontos' => $value->pontos,
				'nome' => $value->nome,
				'observacao' => ($value->observacao) ? $value->observacao : '--',
				'nome_arquivo' => ($value->nome_arquivo) ? '<a style="color:#555555;" href="' . base_url() . $value->nome_arquivo . '" target=_blank title="Documento" ><i class="material-icons">insert_drive_file</i></a>' : '--',
				'validar' => ' 
                <input title="Selecionar" name="' . $nome_campo . '" type="checkbox" id=' . $value->id . ' class="filled-in chk-col-green" value=' . $value->id . ' ' . $checked . '> <label for=' . $value->id . '>  </label> ',
				'opcoes' => '
                <button type="button" class="btn btn-danger waves-effect open-exclusao"
                data-toggle="modal" data-target="#modal_exclusao" data-id="' . $value->id . '">
                <i class="material-icons">delete</i>
                </button>'
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

	function getAtividadesFiscalAntigas()
	{
		$postData = $this->input->post();
		$draw = $postData['draw'];
		$start = $postData['start'];
		$rowperpage = $postData['length']; // Rows display per page

		foreach ($postData['order'] as $key => $index) {
			$columnIndex[] = $index['column'];
			$columnName[] = $postData['columns'][$index['column']]['data'];
			$columnSortOrder[] = $postData['order'][$key]['dir'];
		}
		$searchValue = $postData['search']['value']; // Search value

		$data = array();
		$data_ini = (isset($postData['data_ini']))
			? date(
				'Y-m-d',
				strtotime($postData['data_ini'])
			) : date(
				'Y-m-d',
				mktime(0, 0, 0, date('m'), 1, date('Y'))
			);
		$data_fim = (isset($postData['data_fim']))
			? date(
				'Y-m-d',
				strtotime($postData['data_fim'])
			) : date(
				'Y-m-d',
				mktime(0, 0, 0, date('m') + 1, 0, date('Y'))
			);
		$query = $this->model_atividade
			->getAtividadesFiscalJSON(
				$draw,
				$start,
				$rowperpage,
				$columnIndex,
				implode(',', $columnName),
				implode(',', $columnSortOrder),
				$searchValue,
				$data_ini,
				$data_fim,
				$this->id_user,
				$this->id_empresa
			);


		foreach ($query["aaData"] as $key => $value) {

			if ($value->validacao == 1) {
				$validacao = '<span style="style="align-content: center;" class="badge bg-green"><i class="material-icons">done</i></span>';
			} else {
				$validacao =  '<span style="align-content: center;" class="badge bg-gray"><i class="material-icons" >close</i></span>';
			}

			$data_conclusao =  strtotime($value->data_conclusao);
			$status = 'disabled';

			if ($this->isAtividadeinMonthDate($data_conclusao)) {
				$status = 'data-target="#modal-editar"';
			}
			if ($this->id_empresa != 4) {
				$status = 'data-target="#modal-editar"';
			}
			$data[] = array(
				'id' => $value->id,
				'tipo' => $value->tipo,
				'data_conclusao' => "<span class='hidden'>" . $value->data_conclusao . "</span>"
					. date('d/m/Y', strtotime($value->data_conclusao)),
				'n_protocolo' => ($value->n_protocolo) ? $value->n_protocolo : '--',
				'n_documento' => ($value->n_documento) ? $value->n_documento : '--',
				'rc' => ($value->rc) ? $value->rc : '--',
				'pontos' => $value->pontos,
				'observacao' => ($value->observacao) ? $value->observacao : '--',
				'validar' =>  $validacao,
				'nome_arquivo' => ($value->nome_arquivo) ? '<a style="color:#555555;" href="' . base_url() . $value->nome_arquivo . '" target=_blank title="Documento" ><i class="material-icons">insert_drive_file</i></a>' : '--',
				'opcoes' => '
                <button type="button" style="margin-top:3px;margin-left:3px;background-color:#40B2A6;color:white;" class="btn waves-effect open-editar"
                data-id="' . $value->id . '"
                data-tipo="' . $value->tipo . '"
                data-data="' .  date('d/m/Y', $data_conclusao) . '"
                data-n_documento = "' . $value->n_documento . '"
                data-n_protocolo = "' . $value->n_protocolo . '"
                data-rc = "' . $value->rc . '"
                data-observacao = "' . $value->observacao . '"
                title="Editar informações" data-toggle="modal"' . $status . '>
                <i class="material-icons">create</i>
                </button> '
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
