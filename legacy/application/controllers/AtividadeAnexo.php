<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AtividadeAnexo extends SO_Controller
{

	private $id_user;
	private $id_empresa;
	private $nome_usuario;

	function __construct()
	{
		parent::__construct();
		$this->logado();
		$this->load->model('model_atividade');
		$this->load->model('model_atividade_anexo', 'atividadeAnexo');

		$this->id_user = $this->session->userdata['logged_in']['id'];
		$this->nome_usuario = $this->session->userdata['logged_in']['nome'];
		$this->id_nivel = $this->session->userdata['logged_in']['nivel'];
		$this->id_empresa = $this->session->userdata['logged_in']['id_empresa'];
		$this->parametros_empresa = $this->session->userdata['logged_in']['parametros_empresa'];
		$this->data = array();
	}

	function buscar()
	{
		$this->form_validation->set_rules('id_atividade_fiscal', 'ID Atividade', 'trim|required');
		if ($this->input->get('id_atividade_fiscal') == 'null' || $this->input->get('id_atividade_fiscal') == '') {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(422)
				->set_output(json_encode(array(
					'status' => 'ERROR',
					'data' => [],
					'message' => 'id_atividade_fiscal não enviado.'
				)));
		}

		$idAtividadeFiscal = $this->input->get('id_atividade_fiscal');
		$result = $this->atividadeAnexo->get($idAtividadeFiscal);

		if ($result) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode(array(
					'status' => 'SUCCESS',
					'data' => $result,
				)));
		}

		return $this->output
			->set_content_type('application/json')
			->set_status_header(404)
			->set_output(json_encode(array(
				'status' => 'ERROR',
				'data' => [],
				'message' => 'Nenhum anexo encontrado.'
			)));
	}
}
