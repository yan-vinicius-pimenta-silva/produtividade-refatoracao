<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Deducao extends SO_Controller
{
	private $id_user;
	private $id_empresa;
	private $nome_usuario;
	private $data = array();

	function __construct()
	{
		parent::__construct();
		$this->logado();
		$this->load->model('model_atividade');
		$this->load
			->model(
				'model_atividade_anexo',
				'atividadeAnexo'
			);
		$this->load
			->model(
				'model_deducao',
				'deducao'
			);

		$this->id_user = $this->session->userdata['logged_in']['id'];
		$this->nome_usuario = $this->session->userdata['logged_in']['nome'];
		$this->id_nivel = $this->session->userdata['logged_in']['nivel'];
		$this->id_empresa = $this->session->userdata['logged_in']['id_empresa'];
		$this->parametros_empresa = $this->session->userdata['logged_in']['parametros_empresa'];
	}


	public function index()
	{
		return $this->load
			->view('deducao/consulta');
	}


	public function cadastro()
	{
		$this->load->model(
			'model_usuario',
			'usuario'
		);
		$data = array();
		$data["fiscais"] = $this->usuario
			->getUsers(
				$this->id_empresa,
				2
			);
		$data["deducoes"] = $this->model_atividade
			->getAtividades(
				$this->id_empresa,
				null,
				CHEFE
			);
		return $this->load->view(
			'deducao/cadastro',
			$data
		);
	}


	public function cadastrar()
	{
		$this->load->model('model_usuario', 'usuario');
		// Validação para os campos Tipo e Nome do fiscal
		$this->form_validation->set_rules(
			'id_deducao',
			'Dedução',
			'trim|required'
		);
		$this->form_validation->set_rules(
			'id_fiscal',
			'Fiscal',
			'trim|required'
		);

		if ($this->form_validation->run() == FALSE) {
			$data["fiscais"] = $this->usuario
				->getUsers(
					$this->id_empresa,
					2
				);
			$data["deducoes"] = $this->model_atividade
				->getAtividades(
					$this->id_empresa,
					null,
					CHEFE
				);
			$data['form_error'] = array(
				'message_heading' => validation_errors(),
				'class_result'  => 'red'
			);
			return $this->load->view(
				'deducao/cadastro',
				$data
			);
		}

		$this->data['id_atividade'] = $this->input->post('id_deducao');
		$this->data['id_fiscal'] = $this->input->post('id_fiscal');
		$pontuacaoAtividade = $this->model_atividade
			->getAtividades(
				$this->id_empresa,
				$this->data['id_atividade'],
				CHEFE
			);
		if ($this->input->post('quantidade')) {
			$this->data['quantidade'] = (int)str_replace(
				[',', '.'],
				'',
				$this->input->post('quantidade')
			);
			$this->data['pontuacao_total'] = intdiv(
				(int)($this->data['quantidade'] * (int) $pontuacaoAtividade[0]->pontos),
				10
			);
		} else {
			$this->data['quantidade'] = (int)10;
			$this->data['pontuacao_total'] = intdiv(
				(int)($this->data['quantidade'] * (int) $pontuacaoAtividade[0]->pontos),
				10
			);
		}

		$this->data['data_conclusao'] = str_replace(
			'/',
			'-',
			$this->input->post('data_conclusao')
		);
		$this->data['motivo_deducao'] = $this->input->post('motivo_deducao');
		$this->data['usuario_deducao'] = $this->nome_usuario;
		$this->data['data_update'] = date('Y-m-d H:i:s');
		$this->data['id_empresa'] = $this->id_empresa;

		$idAtividade = $this->deducao->cadastrar(
			$this->data
		);
		$this->data["fiscais"] = $this->usuario
			->getUsers(
				$this->id_empresa,
				2
			);
		$this->data["deducoes"] = $this->model_atividade
			->getAtividades(
				$this->id_empresa,
				null,
				CHEFE
			);
		if ($idAtividade === FALSE) {
			$this->data['form_error'] = array(
				'message_heading' => 'Erro ao cadastrar atividade!',
				'class_result'  => 'red'
			);
			return $this->load
				->view(
					'deducao/cadastro',
					$this->data
				);
		}
		$this->load->model(
			'model_usuario',
			'usuario'
		);
		$this->data["id_fiscal"] = $this->usuario
			->getUsers(
				$this->id_empresa,
				2
			);
		$this->data['form_error'] = array(
			'message_heading' => 'Atividade cadastrada com sucesso!',
			'class_result' => 'green'
		);
		return $this->load->view(
			'deducao/cadastro',
			$this->data
		);
	}


	function get()
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
		$query = $this->deducao
			->get(
				$draw,
				$start,
				$rowperpage,
				$columnIndex,
				implode(',', $columnName),
				implode(',', $columnSortOrder),
				$searchValue,
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
				'data_conclusao' => "<span class='hidden'>" . $value->data_conclusao . "</span>" . date('d/m/Y', strtotime($value->data_conclusao)),
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

		echo json_encode($response);
	}
}
