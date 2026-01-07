<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UnidadeFiscal extends SO_Controller
{

	function __construct()
	{
		parent::__construct();
		$this->logado();
		$this->load->model('model_unidade_fiscal', 'model_ufesp');

		$this->id_user = $this->session->userdata['logged_in']['id'];
		$this->id_nivel = $this->session->userdata['logged_in']['nivel'];
		$this->id_empresa = $this->session->userdata['logged_in']['id_empresa'];
		$this->data = array();
	}

	function index()
	{
		if ($this->id_nivel != 1) {
			$message = array('message_heading' => 'Você não tem permissão para acessar essa página!', 'class_result' => 'red');
			$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
			redirect(base_url('/home'));
		}
		$this->data['ufesps'] = $this->model_ufesp->get();
		$this->load->view('ufesp/index', $this->data);
	}

	function cadastrar()
	{
		$this->form_validation->set_rules('ano', 'Ano', 'trim|required|is_unique[unidade_fiscal.ano]');
		$this->form_validation->set_rules('nome', 'Descrição', 'trim|required');
		$this->form_validation->set_rules('valor_ufesp', 'Valor', 'trim|required|callback_valor_check');

		if ($this->form_validation->run() == FALSE) {
			$this->data['ufesps'] = $this->model_ufesp->get();
			return $this->load->view('ufesp/index', $this->data);
		}

		$this->data['ano'] = $this->input->post('ano');
		$this->data['nome'] = $this->input->post('nome');
		$this->data['valor'] = str_replace(',', '', $this->input->post('valor_ufesp'));
		$this->data['ativo'] = ($this->input->post('ativo') != null) ? 1 : 0;

		$result = $this->model_ufesp->cadastrar($this->data);
		if ($result) {
			$message = array('message_heading' => 'UFESP cadastrado com sucesso!', 'class_result' => 'green');
			$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
			redirect(base_url('/unidadefiscal'));
		}

		$message = array('message_heading' => 'Erro ao cadastrar UFESP!', 'class_result' => 'red');
		$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
		redirect(base_url('/unidadefiscal'));
	}

	function editar($ano)
	{
		if (!is_numeric($ano)) {
			$message = array('message_heading' => 'Parâmetro inválido!', 'class_result' => 'red');
			$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
			redirect(base_url('/parametro'));
		}
		$this->data['ufesps'] = $this->model_ufesp->get();
		$result = $this->model_ufesp->get($ano);
		if ($result) {
			$this->data['alterar']['ano'] = $result[0]->ano;
			$this->data['alterar']['nome'] = $result[0]->nome;
			$this->data['alterar']['valor'] = $result[0]->valor;
			$this->data['alterar']['ativo'] = $result[0]->ativo;
		}

		$this->load->view('ufesp/index', $this->data);
	}

	function alterar()
	{
		$this->form_validation->set_rules('nome', 'Descrição', 'trim|required');
		$this->form_validation->set_rules('valor_ufesp', 'Valor', 'trim|required|callback_valor_check');

		if ($this->form_validation->run() == FALSE) {
			$this->data['ufesps'] = $this->model_ufesp->get();
			return $this->load->view('ufesp/index', $this->data);
		}
		$ano = $this->input->post('ano');
		$this->data['nome'] = $this->input->post('nome');
		$this->data['valor'] = str_replace(',', '', $this->input->post('valor_ufesp'));
		$this->data['ativo'] = ($this->input->post('ativo') != null) ? 1 : 0;

		$result = $this->model_ufesp->update($ano, $this->data);
		if ($result) {

			$message = array('message_heading' => 'UFESP editado com sucesso!', 'class_result' => 'green');
			$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
			redirect(base_url('/unidadefiscal'));
		}

		$message = array('message_heading' => 'Erro ao editar UFESP!', 'class_result' => 'red');
		$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
		redirect(base_url("/unidadefiscal/editar/$ano"));
	}

	function deletar()
	{
		$this->form_validation->set_rules('ano', 'Ano', 'trim|required|callback_exclusao_check');
		if ($this->form_validation->run() == FALSE) {
			$message = array('message_heading' => validation_errors(), 'class_result' => 'red');
			$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
			redirect(base_url('/unidadefiscal'));
		}

		$this->data['ano'] = $this->input->post('ano');
		$return  = $this->model_ufesp->excluir($this->data['ano']);
		if ($return) {
			$message = array('message_heading' => 'Unidade Fiscal excluída com sucesso!', 'class_result' => 'green');
			$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
			redirect(base_url('/unidadefiscal'));
		}

		$message = array('message_heading' => 'Erro ao deletar Unidade Fiscal!', 'class_result' => 'red');
		$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
		redirect(base_url('/parametro'));
	}


	public function valor_check($valor)
	{
		if (trim($valor) === '' || $valor == null) {
			$this->form_validation->set_message('valor_check', 'O {field} é obrigatório!');
			return FALSE;
		}
		if (!(bool)preg_match('/^[\-+]?[0-9]+\,[0-9]+$/', $valor)) {
			$this->form_validation->set_message('valor_check', 'O {field} não é um valor válido!');
			return FALSE;
		}

		return TRUE;
	}

	// TODO: Verificar se UFESP está em uso
	public function exclusao_check($ano)
	{
		if ($this->model_ufesp->isInUse($ano)) {
			$this->form_validation->set_message('exclusao_check', 'A ufesp escolhida está em uso e não é possível excluir, apenas desativar!');
			return FALSE;
		}
		return TRUE;
	}
}
