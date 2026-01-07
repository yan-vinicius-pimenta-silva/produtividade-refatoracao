<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Parametro extends SO_Controller
{
	private $id_user;
	private $id_empresa;

	function __construct()
	{
		parent::__construct();
		$this->logado();
		$this->load->model('model_parametros');

		$this->id_user = $this->session->userdata['logged_in']['id'];
		$this->id_nivel = $this->session->userdata['logged_in']['nivel'];
		$this->id_empresa = $this->session->userdata['logged_in']['id_empresa'];
		$this->data = array();
	}

	function index()
	{
		if ($this->id_nivel != 1) {
			$message = array(
				'message_heading' => 'Você não tem permissão para acessar essa página!',
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
		$this->data['tipos'] = $this->model_parametros
			->getTipoAtividade();
		$this->data['atividade'] = $this->model_parametros
			->getAtividades(
				null,
				null,
				$this->id_empresa
			);
		$this->data['atividade_import'] = $this->model_parametros
			->getAtividades(
				null,
				null,
				1
			);
		$this->load->view(
			'parametros',
			$this->data
		);
	}

	function cadastrar()
	{
		if ($this->input->post('importar_dados')) {
			$id_atividades = $this->input->post('importar_dados');
			foreach ($id_atividades as $id_atividade) {
				$return = $this->model_parametros
					->getAtividades(
						$id_atividade,
						null
					);

				$this->form_validation->set_rules(
					'tipo',
					'Tipo',
					'trim|required'
				);
				$this->form_validation->set_rules(
					'descricao',
					'Descrição',
					'trim|required'
				);
				$this->form_validation->set_rules(
					'pontos',
					'Pontos',
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
						base_url('/parametro')
					);
				}

				$this->data['tipo'] =  mb_strtoupper(
					$return[0]->tipo
				);
				$this->data['descricao'] = mb_strtoupper(
					$return[0]->descricao
				);
				$this->data['pontos'] = (int)($return[0]->pontos * 10);
				$this->data['ativo'] = 1;
				$this->data['atividade_os'] = $return[0]->atividade_os;
				$this->data['id_empresa'] = $this->id_empresa;
				$this->data['multiplicador'] = $return[0]->multiplicador;
				$result = $this->model_parametros
					->cadastrarAtividade($this->data);
			}
			if ($result) {
				$message = array(
					'message_heading' => 'Parâmetro(s) importado(s) com sucesso!',
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
					base_url('/parametro')
				);
			}
		} else {

			$this->form_validation->set_rules(
				'tipo',
				'Tipo',
				'trim|required'
			);
			$this->form_validation->set_rules(
				'descricao',
				'Descrição',
				'trim|required'
			);
			$this->form_validation->set_rules(
				'pontos',
				'Pontos',
				'trim|required'
			);
			$this->form_validation->set_rules(
				'id_tipo_atividade',
				'Pontos',
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
					base_url('/parametro')
				);
			}

			$this->data['tipo'] = mb_strtoupper(
				$this->input->post('tipo')
			);
			$this->data['descricao'] =  mb_strtoupper(
				$this->input->post('descricao')
			);
			$this->data['pontos'] = (int)($this->input->post('pontos') * 10);
			$this->data['id_empresa'] = $this->id_empresa;
			$this->data['id_tipo_atividade'] = $this->input->post('id_tipo_atividade');
			$this->data['atividade_os'] = ($this->input->post('atividade_os') != null)
				? 1
				: 0;
			$this->data['multiplicador'] = ($this->input->post('multiplicador') != null)
				? 1
				: 0;

			$result = $this->model_parametros->cadastrarAtividade($this->data);
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
					base_url('/parametro')
				);
			}

			$message = array(
				'message_heading' => 'Erro ao cadastrar atividade!',
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
				base_url('/parametro')
			);
		}
	}

	function editar()
	{
		if (!is_numeric($this->uri->segment(3))) {
			$message = array(
				'message_heading' => 'Parâmetro inválido!',
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
				base_url('/parametro')
			);
		}

		$id_usuario = $this->uri->segment(3);
		$result = $this->data['atividade'] = $this->model_parametros
			->getAtividades(
				$id_usuario,
				null,
				$this->id_empresa
			);
		if ($result) {
			$this->data['alterar']['id'] = $result[0]->id;
			$this->data['alterar']['tipo'] = $result[0]->tipo;
			$this->data['alterar']['descricao'] = $result[0]->descricao;
			$this->data['alterar']['pontos'] = $result[0]->pontos / 10;
			$this->data['alterar']['ativo'] = $result[0]->ativo;
			$this->data['alterar']['id_tipo_atividade'] = $result[0]->id_tipo_atividade;
			$this->data['alterar']['atividade_os'] = $result[0]->atividade_os;
			$this->data['alterar']['multiplicador'] = $result[0]->multiplicador;
		}
		$this->data['atividade_import'] = $this->model_parametros
			->getAtividades(
				null,
				null,
				1
			);
		$this->data['tipos'] = $this->model_parametros
			->getTipoAtividade();
		$this->load->view(
			'/parametros',
			$this->data
		);
	}

	function alterar()
	{
		$this->form_validation->set_rules(
			'tipo',
			'Tipo',
			'trim|required'
		);
		$this->form_validation->set_rules(
			'descricao',
			'Descrição',
			'trim|required'
		);
		$this->form_validation->set_rules(
			'pontos',
			'Pontos',
			'trim|required'
		);
		$this->form_validation->set_rules(
			'id_tipo_atividade',
			'Pontos',
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
				base_url('/parametro')
			);
		}

		$atividadeEmUso  = $this->model_parametros
			->verificaAtividades(
				$this->input->post('id'),
				$this->id_empresa
			);

		if ($atividadeEmUso) {
			$message = array(
				'message_heading' => 'Ops! Você não pode alterar esse parâmetro! Existem atividades lançadas.',
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
				base_url('/parametro')
			);
		}

		$this->data['id'] = $this->input->post('id');
		$this->data['tipo']  =  $this->input->post('tipo');
		$this->data['descricao'] = $this->input->post('descricao');
		$this->data['pontos'] =  (int)($this->input->post('pontos') * 10);
		$this->data['ativo'] = ($this->input->post('ativo') != null)
			? 1
			: 0;
		$this->data['atividade_os'] = ($this->input->post('atividade_os') != null)
			? 1
			: 0;
		$this->data['multiplicador'] = ($this->input->post('multiplicador') != null)
			? 1
			: 0;
		$this->data['id_tipo_atividade'] = $this->input->post('id_tipo_atividade');

		$result = $this->model_parametros->alterarAtividade(
			$this->data['id'],
			$this->data
		);

		if ($result) {

			$message = array(
				'message_heading' => 'Parâmetro editado com sucesso!',
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
				base_url('/parametro')
			);
		}

		$message = array(
			'message_heading' => 'Erro ao editar parâmetro!',
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
			base_url('/parametro/editar/' . $this->data['id'])
		);
	}

	function deletar()
	{
		$this->form_validation->set_rules(
			'id_atividade',
			'ID Usuário',
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
				base_url('/parametro')
			);
		}

		$this->data['id_atividade'] = $this->input->post('id_atividade');

		//Verify if this activity is being used
		$return  = $this->model_parametros->VerificaAtividades(
			$this->data['id_atividade'],
			$this->id_empresa
		);
		if ($return) {
			$message = array(
				'message_heading' => 'Ops! Você não pode excluir esse parâmetro! Existem atividades lançadas.',
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
				base_url('/parametro')
			);
		} else {

			if ($this->model_parametros->deletarAtividade($this->data['id_atividade'])) {
				$message = array(
					'message_heading' => 'Parâmetro deletado com sucesso!',
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
					base_url('/parametro')
				);
			}
		}

		$message = array(
			'message_heading' => 'Erro ao deletar parâmetro!',
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
			base_url('/parametro')
		);
	}
}
