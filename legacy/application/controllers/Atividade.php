<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Atividade extends SO_Controller
{
	private $id_user;
	private $id_empresa;
	private $nome_usuario;

	function __construct()
	{
		parent::__construct();
		$this->logado();
		$this->load->model('model_atividade');
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
		$result = $this->model_atividade
			->getAtividades(
				$this->id_empresa
			);
	}


	function cadastrar()
	{
		$this->form_validation->set_rules(
			'atividade',
			'Atividade',
			'trim|required'
		);
		$this->form_validation->set_rules(
			'n_doc',
			'Número de documento',
			'trim|required'
		);
		$this->form_validation->set_rules(
			'data_conclusao',
			'Data de conclusão',
			'trim|required'
		);
		// $this->form_validation->set_rules('arquivo', 'PDF', 'trim|required');


		if ($this->form_validation->run() == FALSE) {
			$this->data['ufesps'] = $this->model_ufesp->get();
			return $this->load->view(
				'ufesp/index',
				$this->data
			);
		}

		if ($this->form_validation->run() == FALSE) {
			$this->data['data_ini'] = null;
			$this->data['data_fim'] = null;
			$this->data['nome_usuario'] = $this->nome_usuario;
			$this->data['total_pontos'] = $this->calcularTotais();
			$this->data["historico_atividade"] = $this->model_atividade
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
					$this->id_empresa
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
			return $this->load->view(
				'fiscal/home_fiscal',
				$this->data
			);
		}

		$this->data['validacao'] = 0;
		$config['upload_path'] = './uploads/empresa_' . $this->id_empresa . '/';
		$config['allowed_types'] = 'pdf|jpg|png|jpeg';
		$config['file_name'] = date('YmdHis') . '_' . preg_replace(
			"/[^A-Za-z0-9\-_\.]/",
			"",
			$_FILES["arquivo"]['name']
		);
		$config['max_size'] = 100000000;

		$this->load->library('upload');

		$this->upload->initialize($config);

		if ($this->upload->do_upload('arquivo')) {
			$this->data['nome_arquivo'] =   $config['upload_path'] . $this->upload->data('file_name');
		} else {
			$message = array(
				'message_heading' => $this->upload->display_errors(),
				'class_result' => 'red'
			);
			$this->session->set_flashdata(
				'result',
				$this->parser->parse(
					'template/result_message.php',
					$message
				)
			);
			redirect(base_url('/home'));
		}

		$this->data['data_cadastro'] = date('d-m-Y H:i:s');
		$this->data['id_atividade'] = $this->input->post('atividade');
		$this->data['n_documento'] = strtoupper(
			$this->input->post('n_doc')
		);
		$this->data['data_conclusao'] = str_replace(
			'/',
			'-',
			$this->input->post('data_conclusao')
		);
		$this->data['rc'] = $this->input->post('rc');
		$this->data['n_protocolo'] = strtoupper(
			$this->input->post('n_protocolo')
		);
		$this->data['observacao'] = strtoupper(
			$this->input->post('observacao')
		);
		$this->data['id_usuario'] = $this->session->userdata['logged_in']['id'];
		$this->data['id_empresa'] = $this->id_empresa;
		$multiplicador = $this->input->post('digitar_multiplicador');

		if ($multiplicador != null) {
			for ($i = 1; $i <= $multiplicador; $i++) {
				$result = $this->model_atividade
					->cadastrarAtividadeUsuario($this->data);
			}
		} else {
			$result = $this->model_atividade
				->cadastrarAtividadeUsuario($this->data);
		}
		if ($result) {
			$message = array(
				'message_heading' => 'Atividade cadastrada com sucesso!',
				'class_result' => 'green'
			);
			$this->session->set_flashdata(
				'result',
				$this->parser->parse(
					'template/result_message.php',
					$message
				)
			);
			redirect(
				base_url('/home')
			);
		}

		$message = array(
			'message_heading' => 'Erro ao cadastrar Atividade!',
			'class_result' => 'red'
		);
		$this->session->set_flashdata(
			'result',
			$this->parser->parse(
				'template/result_message.php',
				$message
			)
		);
		redirect(
			base_url('/home')
		);
	}


	function editar()
	{
		$this->form_validation->set_rules(
			'doc',
			'Número de documento',
			'trim|required'
		);

		if ($this->form_validation->run() == FALSE) {
			$message = array(
				'message_heading' => validation_errors(),
				'class_result' => 'red'
			);
			$this->session->set_flashdata(
				'result',
				$this->parser->parse(
					'template/result_message.php',
					$message
				)
			);
			redirect(
				base_url('/home')
			);
		}

		$this->data['id'] = $this->input->post('id');
		$this->data['n_protocolo']  =  $this->input->post('protocolo');
		$this->data['data_conclusao']  =  $this->input->post('data');
		$this->data['n_documento'] = $this->input->post('doc');
		$this->data['rc'] = $this->input->post('rc_m');
		$this->data['observacao'] = $this->input->post('obs');

		$result = $this->model_atividade
			->alterarAtividadeUsuario(
				$this->data['id'],
				$this->data
			);

		if ($result) {
			$message = array(
				'message_heading' => 'Atividade editada com sucesso!',
				'class_result' => 'green'
			);
			$this->session->set_flashdata('result', $this->parser->parse(
				'template/result_message.php',
				$message
			));
			redirect(
				base_url('/home')
			);
		}

		$message = array(
			'message_heading' => 'Erro ao editar Atividade!',
			'class_result' => 'red'
		);
		$this->session->set_flashdata(
			'result',
			$this->parser->parse(
				'template/result_message.php',
				$message
			)
		);
		redirect(
			base_url('/home')
		);
	}


	function deletar()
	{
		$this->form_validation->set_rules(
			'id_atividade',
			'ID Atividade',
			'trim|required|numeric'
		);
		if ($this->form_validation->run() == FALSE) {
			$message = array(
				'message_heading' => validation_errors(),
				'class_result' => 'red'
			);
			$this->session->set_flashdata(
				'result',
				$this->parser->parse(
					'template/result_message.php',
					$message
				)
			);
			redirect(
				base_url('/home')
			);
		}

		$this->data['id_atividade'] = $this->input->post('id_atividade');
		$this->data['motivo_exclusao'] = $this->input->post('motivo');
		$this->data['data_update'] = date('Y-m-d H:i:s');
		$this->data['nome_usuario_update'] = $this->nome_usuario;

		if ($this->model_atividade->deletarAtividadeUsuario($this->data['id_atividade'], $this->data)) {
			$message = array(
				'message_heading' => 'Atividade deletada com sucesso!',
				'class_result' => 'green'
			);
			$this->session->set_flashdata(
				'result',
				$this->parser->parse(
					'template/result_message.php',
					$message
				)
			);
			redirect(
				base_url('/home')
			);
		}

		$message = array(
			'message_heading' => 'Erro ao deletar Atividade!',
			'class_result' => 'red'
		);
		$this->session->set_flashdata(
			'result',
			$this->parser->parse(
				'template/result_message.php',
				$message
			)
		);
		redirect(
			base_url('/home')
		);
	}


	function getAtividadeJson()
	{
		$result = $this->model_atividade
			->getAtividades($this->id_empresa);
		if ($result) {
			$data = array();
			foreach ($result as $value) {
				$data[] = array(
					"id" => $value->id,
					"name" => $value->tipo
				);
			}
			echo json_encode(
				$data
			);
		}
		return array();
	}


	function excluidas()
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
		$data_ini = (isset($postData['data_ini']))
			? date(
				'Y-m-d 23:59:59',
				strtotime(
					$postData['data_ini']
				)
			)
			: '';
		$data_fim = (isset($postData['data_fim']))
			? date(
				'Y-m-d 23:59:59',
				strtotime(
					$postData['data_fim']
				)
			)
			: '';
		if ($this->id_nivel != 1) {
			$query = $this->model_atividade
				->getAtividadeExcluidoJSON(
					$draw,
					$start,
					$rowperpage,
					$columnIndex,
					$columnName,
					$columnSortOrder,
					$searchValue,
					$data_ini,
					$data_fim,
					$this->id_user,
					$this->id_empresa
				);
		} else {
			$query = $this->model_atividade
				->getAtividadeExcluidoJSON(
					$draw,
					$start,
					$rowperpage,
					$columnIndex,
					$columnName,
					$columnSortOrder,
					$searchValue,
					$data_ini,
					$data_fim,
					null,
					$this->id_empresa
				);
		}

		foreach ($query["aaData"] as $key => $value) {
			$data[] = array(
				'id' => $value->id,
				'tipo' => $value->tipo,
				'data_update' => date(
					'd/m/Y',
					strtotime($value->data_update)
				),
				'n_protocolo' => ($value->n_protocolo)
					? $value->n_protocolo
					: '--',
				'n_documento' => ($value->n_documento)
					? $value->n_documento
					: '--',
				'nome_arquivo' => ($value->nome_arquivo)
					? '<a style="color:#555555;" href="' . base_url() . $value->nome_arquivo . '" target=_blank title="Documento" ><i class="material-icons">insert_drive_file</i></a>'
					: '--',
				'rc' => ($value->rc)
					? $value->rc
					: '--',
				'pontos' => ($value->pontos)
					? $value->pontos
					: '--',
				'nome_fiscal' => $value->nome_fiscal,
				'nome_usuario_update' => $value->nome_usuario_update,
				'motivo_exclusao' => ($value->motivo_exclusao)
					? $value->motivo_exclusao
					: '--'
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


	function getOsExcluidoJSON()
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
		$data_ini = (isset($postData['data_ini']))
			? date('Y-m-d 23:59:59', strtotime($postData['data_ini']))
			: '';
		$data_fim = (isset($postData['data_fim']))
			? date('Y-m-d 23:59:59', strtotime($postData['data_fim']))
			: '';
		if ($this->id_nivel != 1) {
			$query = $this->model_os
				->getOsExcluidoJson(
					$draw,
					$start,
					$rowperpage,
					$columnIndex,
					$columnName,
					$columnSortOrder,
					$searchValue,
					$data_ini,
					$data_fim,
					$this->id_user,
					$this->id_empresa
				);
		} else {
			$query = $this->model_os
				->getOsExcluidoJson(
					$draw,
					$start,
					$rowperpage,
					$columnIndex,
					$columnName,
					$columnSortOrder,
					$searchValue,
					$data_ini,
					$data_fim,
					null,
					$this->id_empresa
				);
		}

		foreach ($query["aaData"] as $key => $value) {
			$data[] = array(
				'id' => $value->id,
				'tipo' => ($value->tipo)
					? $value->tipo
					: $value->tipo_atividade_os,
				'data_update' => date(
					'd/m/Y',
					strtotime($value->data_update)
				),
				'n_protocolo' => ($value->n_protocolo)
					? $value->n_protocolo
					: '--',
				'n_documento' => ($value->n_documento)
					? $value->n_documento
					: '--',
				'rc' => ($value->rc)
					? $value->rc
					: '--',
				'nome_fiscal' => $value->nome_fiscal,
				'nome_usuario_update' => $value->nome_usuario_update,
				'motivo_exclusao' => ($value->motivo_exclusao)
					? $value->motivo_exclusao
					: '--'
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


	function confirmar()
	{
		$id = $this->input->post();

		if ($id == false) {
			echo 'false';
			return false;
		}

		foreach ($id['id'] as $ids) {

			$valor['validacao'] = 1;
			$validar = $this->model_atividade
				->validar(
					$ids,
					$valor
				);
			if (!$validar) {
				echo 'false';
				return false;
			}
		}

		echo 'true';
		return true;
	}


	function getMultiplicadorJson()
	{
		$id_atividade = $this->uri->segment(3);
		$multiplicadores = $this->model_atividade
			->getMultiplicadorJSON($id_atividade);

		if ($multiplicadores) {

			$resultado = json_encode(array(
				$multiplicadores[0]->multiplicador

			), JSON_UNESCAPED_UNICODE);
			echo ($resultado);
		}
	}


	private function calcularTotais(
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
				$this->model_home
					->getTotais(
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
				$this->data['total_pontos'] += floatval(
					$this->data['consulta_pontos'][$i]->pontos
				);
			}
		}
		return $this->data['total_pontos'];
	}
	

	public function buscaForm()
	{
		$tipoAtividade = $this->input->post('tipo_atividade');
		$formData['formData'] = $this->input->post('form_data');
		$view = '';
		if ($tipoAtividade == 'UFESP') {
			$view = $this->load
				->view(
					'fiscal/forms/atividades_ufesp',
					$formData,
					TRUE
				);
		} else {
			$view = $this->load
				->view(
					'fiscal/forms/atividades_ponto',
					$formData,
					TRUE
				);
		}

		return $this->output
			->set_content_type('text/html; charset=UTF-8')
			->set_status_header(200)
			->set_output($view);
	}
}
