<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AtividadeFiscal extends SO_Controller
{

	private $id_user;
	private $id_empresa;
	private $nome_usuario;
	const MAX_FILE_SIZE =  1024 * 50;

	function __construct()
	{
		parent::__construct();
		$this->logado();
		$this->load->model('model_atividade');
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


	function cadastrar()
	{
		$this->form_validation->set_rules(
			'atividade',
			'Atividade',
			'trim|required'
		);
		$this->form_validation->set_rules(
			'data_conclusao',
			'Data de conclusão',
			'trim|required'
		);
		$this->form_validation->set_rules(
			'arquivo[]',
			'PDF',
			'trim'
		);
		if ($this->input->post('tipo_form') === 'LANCAMENTO_UFESP') {
			$this->form_validation->set_rules(
				'n_doc',
				'Número de documento',
				'trim|required'
			);
			$this->form_validation->set_rules(
				'cpf_cnpj',
				'CPF/CNPJ',
				'trim|required'
			);
			$this->form_validation->set_rules(
				'valor_lancamento',
				'Valor do Lançamento',
				'trim|required'
			);
		}

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
			$this->session->set_flashdata(
				'form_data',
				$this->input->post()
			);
			redirect(
				'home#tab_atividades'
			);
		}
		$dados = [];

		$filesData = $this->do_upload();

		if (array_key_exists('error', $filesData)) {
			$this->session->set_flashdata(
				'form_data',
				$this->input->post()
			);
			redirect(
				'home#tab_atividades'
			);
		}

		$pontuacaoAtividade = $this->model_atividade
			->getAtividades(
				$this->id_empresa,
				$this->input->post('atividade')
			);

		$dados['data_cadastro'] = date('d-m-Y H:i:s');
		$dados['id_atividade'] = $this->input->post('atividade');
		$dados['documento'] = ($this->input->post('n_doc'))
			? mb_strtoupper($this->input->post('n_doc'))
			: null;
		$dados['protocolo'] = ($this->input->post('n_protocolo'))
			? mb_strtoupper(
				$this->input->post('n_protocolo')
			)
			: null;
		$dados['data_conclusao'] = str_replace(
			'/',
			'-',
			$this->input->post('data_conclusao')
		);

		if ($this->input->post('tipo_form') === 'LANCAMENTO_UFESP') {
			$this->load->model(
				'model_unidade_fiscal',
				'UnidadeFiscal'
			);
			$valorUfespAtivo = $this->UnidadeFiscal
				->get(
					date(
						'Y',
						strtotime($dados['data_conclusao'])
					),
					null
				);
			if (!$valorUfespAtivo) {
				log_message('error', 'Nenhum valor UFESP ativo.');
				return FALSE;
			}

			$valorUfesp = (int)$valorUfespAtivo[0]->valor; // valor ufesp usado no calculo
			$dados['ufesp_ano'] = $valorUfespAtivo[0]->ano;
			$dados['rc'] = $this->input->post('rc');

			if ($this->input->post('cpf_cnpj'))
				$dados['cpf_cnpj'] = mb_strtoupper(
					$this->input->post('cpf_cnpj')
				);

			$valorLancamento = (int) str_replace(
				[',', '.'],
				'',
				$this->input->post('valor_lancamento')
			);

			$quantidade_ufesp = (int) intdiv(
				$valorLancamento,
				$valorUfesp
			);

			$pontoAtividade  = (int) str_replace(
				'.',
				'',
				$pontuacaoAtividade[0]->pontos
			);

			$pontuacao  = intdiv(
				$quantidade_ufesp * $pontoAtividade,
				10
			);

			$dados['valor'] = str_replace(
				[',', '.'],
				'',
				$this->input->post('valor_lancamento')
			);

			$dados['pontuacao_total'] = $pontuacao;
			$dados['quantidade'] = $quantidade_ufesp;
		} else {
			$dados['quantidade'] = (int)str_replace(
				[',', '.'],
				'',
				$this->input->post('quantidade')
			);
			$dados['pontuacao_total'] = intdiv(
				(int)($dados['quantidade'] * (int) $pontuacaoAtividade[0]->pontos),
				10
			);
		}

		$dados['observacao'] = strtoupper(
			$this->input->post('observacao')
		);
		$dados['id_fiscal'] = $this->session
			->userdata['logged_in']['id'];
		$dados['id_empresa'] = $this->id_empresa;

		$result = $this->AtividadeFiscal
			->cadastrar(
				$dados,
				$filesData
			);

		// $multiplicador = $this->input->post('digitar_multiplicador');

		// if ($multiplicador != null) {
		// 	for ($i = 1; $i <= $multiplicador; $i++) {
		// 		$result = $this->model_atividade->cadastrarAtividadeUsuario($this->data);
		// 	}
		// } else {
		// 	$result = $this->model_atividade->cadastrarAtividadeUsuario($this->data);
		// }
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
				base_url(
					'/home#tab_atividades'
				)
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
			base_url(
				'/home#tab_atividades'
			)
		);
	}


	private function do_upload()
	{
		$config['upload_path'] = './uploads/empresa_' . $this->id_empresa . '/';
		$config['allowed_types'] = 'gif|jpg|png|pdf';
		$config['max_size'] = self::MAX_FILE_SIZE;
		$config['encrypt_name'] = FALSE;

		$this->load->library(
			'upload',
			$config
		);

		$files = $_FILES['arquivo'];
		$result = array();

		if (!is_dir($config['upload_path'])) {
			mkdir(
				$config['upload_path'],
				755
			);
		}

		foreach ($files['name'] as $key => $filename) {
			$_FILES['arquivo'] = array(
				'name' => $files['name'][$key],
				'type' => $files['type'][$key],
				'tmp_name' => $files['tmp_name'][$key],
				'error' => $files['error'][$key],
				'size' => $files['size'][$key]
			);

			if (!$this->upload->validate_file('arquivo')) {
				$message = array(
					'message_heading' => "Erro no upload do arquivo $filename" . $this->upload->display_errors(),
					'class_result' => 'red'
				);
				$this->session->set_flashdata(
					'result',
					$this->parser->parse(
						'template/result_message.php',
						$message
					)
				);
				return [
					'error' => $this->upload->display_errors()
				];
			}
		}

		foreach ($files['name'] as $key => $filename) {
			$_FILES['arquivo'] = array(
				'name' => $files['name'][$key],
				'type' => $files['type'][$key],
				'tmp_name' => $files['tmp_name'][$key],
				'error' => $files['error'][$key],
				'size' => $files['size'][$key]
			);
			if ($this->upload->do_upload('arquivo')) {
				$upload_data = $this->upload->data();
				$result[] = array(
					'anexo' => strstr(
						$upload_data['full_path'],
						'/uploads'
					)
				);
			} else {
				$message = array(
					'message_heading' => "Erro no upload do arquivo $filename" . $this->upload->display_errors(),
					'class_result' => 'red'
				);
				$this->session->set_flashdata(
					'result',
					$this->parser->parse(
						'template/result_message.php',
						$message
					)
				);
				$result['error'] = $this->upload->display_errors();
				break;
			}
		}
		return $result;
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

		$id = $this->input->post('id');
		$this->data['protocolo']  =  $this->input->post('protocolo');
		$this->data['data_conclusao']  =  $this->input->post('data');
		$this->data['documento'] = $this->input->post('doc');
		$this->data['rc'] = $this->input->post('rc_m');
		$this->data['observacao'] = $this->input->post('obs');
		$this->data['cpf_cnpj'] = strtoupper(
			$this->input->post('cpf_cnpj')
		);

		$result = $this->AtividadeFiscal->alterar(
			$id,
			$this->data
		);

		if ($result) {
			$message = array(
				'message_heading' => 'Atividade editada com sucesso!',
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


	function confirmar()
	{
		$id = $this->input->post();

		if ($id == false) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(422)
				->set_output(json_encode(array(
					'status' => 'ERROR',
					'data' => FALSE,
					'message' => 'id_atividade_fiscal não enviado.'
				)));
		}
		$validar = $this->AtividadeFiscal->validar($id);

		if (!$validar) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(422)
				->set_output(json_encode(array(
					'status' => 'ERROR',
					'data' => FALSE,
					'message' => 'Não foi possível validar a atividade.'
				)));
		}

		return $this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(
				json_encode(
					array(
						'status' => 'SUCCESS',
						'data' => TRUE,
						'message' => 'Atividade confirmada com sucesso.'
					)
				)
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
		$this->data['data_exclusao'] = date('Y-m-d H:i:s');
		$this->data['usuario_exclusao'] = $this->nome_usuario;

		if ($this->AtividadeFiscal->excluir(
			$this->data['id_atividade'],
			$this->data
		)) {
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
}
