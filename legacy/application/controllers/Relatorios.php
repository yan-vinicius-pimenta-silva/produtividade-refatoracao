<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Relatorios extends SO_Controller
{

	public $id_user;
	public $id_empresa;
	private $nome_empresa;
	private $email_empresa;
	private $secretaria;
	private $divisao;
	private $telefone;

	function __construct()
	{
		parent::__construct();
		$this->logado();
		$this->load->model('model_atividade');
		$this->load->model('model_pontos');
		$this->load->model('model_usuario');
		$this->load->model('model_os');

		$this->id_user = $this->session->userdata['logged_in']['id'];
		$this->id_nivel = $this->session->userdata['logged_in']['nivel'];
		$this->id_empresa = $this->session->userdata['logged_in']['id_empresa'];
		$this->nome_empresa = $this->session->userdata['logged_in']['nome_empresa'];
		$this->email_empresa = $this->session->userdata['logged_in']['email_empresa'];
		$this->secretaria = $this->session->userdata['logged_in']['secretaria'];
		$this->divisao = $this->session->userdata['logged_in']['divisao'];
		$this->telefone = $this->session->userdata['logged_in']['telefone'];
		$this->parametros_empresa = $this->session->userdata['logged_in']['parametros_empresa'];
		$this->data = array();
		$this->pontos = array();
	}


	function index()
	{
		$result = $this->model_atividade->getAtividades($this->id_empresa);
	}

	function gerarDescritivo()
	{
		$this->load->model('model_relatorio');
		if ($this->input->post('id_usuario')) {
			$this->data['id_usuario'] = $this->input->post('id_usuario');
			$this->data['data_ini'] = str_replace('/', '-', '01/' . $this->input->post('data_init'));
			$result = $this->model_usuario->getUser($this->data['id_usuario'], null, $this->id_empresa);
			$this->data['nome_usuario'] = ($result) ? $result[0]->nome : '';
			$this->data['matricula'] = ($result) ? $result[0]->matricula : '';
		} elseif ($this->input->post('nome_fiscal')) {
			$this->data['nome_fiscal'] = $this->input->post('nome_fiscal');
			$this->data['data_ini'] = str_replace('/', '-', '01/' . $this->input->post('data_init'));
			$result = $this->model_usuario->getUser(null, $this->data['nome_fiscal'], $this->id_empresa);
			$this->data['id_usuario'] = ($result) ? $result[0]->id : '';
			$this->data['nome_usuario'] = ($result) ? $result[0]->nome : '';
			$this->data['matricula'] = ($result) ? $result[0]->matricula : '';
		}
		$dateTime = DateTime::createFromFormat('d-m-Y', $this->data['data_ini']);

		$descritivo = $this->model_relatorio->descritivo($this->data['id_usuario'], $dateTime->format('Y-m-01'), $dateTime->format('Y-m-t'), $this->id_empresa);
		if (!$descritivo) {
			$message = array('message_heading' => 'Nenhuma atividade encontrada!', 'class_result' => 'red');
			$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
			redirect(base_url('/home'));
		}
		$this->data['atividades'] = $descritivo;
		$html = $this->load->view('template/pdf/descritivo', $this->data, TRUE);
		$header = $this->load->view('template/pdf/header', $this->session->userdata['logged_in'], TRUE);
		$footer = $this->load->view('template/pdf/footer', null, TRUE);

		// $html = $this->load->view('template/pdf/header', $this->session->userdata['logged_in'], TRUE);
		// $html .= $this->load->view('template/pdf/descritivo', $this->data, TRUE);

		// $html .= $this->load->view('template/pdf/footer', null, TRUE);
		// echo $html;

		$this->load->library('Pdf');
		$mpdf = new Pdf();
		$pdf = $mpdf->load();
		$pdf->debug = true;

		$pdf->SetHTMLHeader($header);
		$pdf->SetFooter($footer);

		//TIRAR ERRO DE EXIBIÇÃO DO PDF NO CHROME
		if (ob_get_contents()) ob_end_clean();

		$pdf->writeHTML($html);
		$filename = "Relatório_descritivo_" . $this->data['nome_usuario'];
		$pdf->Output($filename, 'I');
	}

	function calcularAtividades()
	{
		$this->pontos['total_atividade'] = intval($this->model_pontos->getTotais($this->id_user));
		$this->data['consulta_pontos'] = $this->model_pontos->getPontos($this->id_user);
		$this->pontos['total_pontos'] = 0.0;

		if ($this->data['consulta_pontos']) {
			for ($i = 0; $i < count($this->data['consulta_pontos']); $i++) {
				$this->pontos['total_pontos'] += floatval($this->data['consulta_pontos'][$i]->pontos);
			}
		}
		return $this->pontos;
	}
	function gerarProdutividade()
	{
		$this->load->model('model_relatorio');
		if ($this->input->post('id_usuario')) {
			$this->data['id_usuario'] = $this->input->post('id_usuario');
			$this->data['data_ini'] = str_replace('/', '-', '01/' . $this->input->post('data_start'));
			$result = $this->model_usuario->getUser($this->data['id_usuario'], null, $this->id_empresa);
			$this->data['nome_usuario'] = ($result) ? $result[0]->nome : '';
			$this->data['matricula'] = ($result) ? $result[0]->matricula : '';
		} elseif ($this->input->post('nome_fiscal')) {
			$this->data['nome_fiscal'] = $this->input->post('nome_fiscal');
			$this->data['data_ini'] = str_replace('/', '-', '01/' . $this->input->post('data_start'));
			$result = $this->model_usuario->getUser(null, $this->data['nome_fiscal'], $this->id_empresa);
			$this->data['id_usuario'] = ($result) ? $result[0]->id : '';
			$this->data['nome_usuario'] = ($result) ? $result[0]->nome : '';
			$this->data['matricula'] = ($result) ? $result[0]->matricula : '';
		}

		$this->data['dados_relatorio'] = $this->model_relatorio->produtividade($this->data['id_usuario'], $this->data['data_ini'], $this->id_empresa);

		$html = $this->load->view('template/pdf/produtividade', $this->data, TRUE);
		$header = $this->load->view('template/pdf/header', $this->session->userdata['logged_in'], TRUE);
		$footer = $this->load->view('template/pdf/footer', null, TRUE);
		$this->load->library('Pdf');
		$mpdf = new Pdf();
		$pdf = $mpdf->load();
		$pdf->SetHTMLHeader($header);
		$pdf->SetFooter($footer);

		//TIRAR ERRO DE EXIBIÇÃO DO PDF NO CHROME
		if (ob_get_contents()) ob_end_clean();

		$pdf->writeHTML($html);
		$filename = "Relatório_produtividade_" . $this->data['nome_usuario'];
		$pdf->Output($filename, 'I');
	}

	function gerarPontuacao()
	{
		$this->load->model('model_relatorio');
		$this->data['data_ini'] = str_replace('/', '-', '01/' . $this->input->post('data_ini_pontuacao'));
		$this->data['dados_relatorio'] = $this->model_relatorio->fiscalPontuacao($this->data['data_ini'], $this->id_empresa);
		// TOTAL DE PONTOS (CHEFE) = Total da soma de pontos dos fiscais / Quantidade de fiscais
		$this->data['total'] = $this->model_relatorio->totalPontuacao($this->data['data_ini'], $this->id_empresa);
		$this->data['chefe'] = $this->model_relatorio->buscaChefeDpto($this->id_empresa);

		$html = $this->load->view('template/pdf/pontuacao', $this->data, TRUE);
		$header = $this->load->view('template/pdf/header', $this->session->userdata['logged_in'], TRUE);
		$footer = $this->load->view('template/pdf/footer', null, TRUE);
		$this->load->library('Pdf');
		$mpdf = new Pdf();
		$pdf = $mpdf->load();
		$pdf->AddPage('L');
		$pdf->SetHTMLHeader($header);
		$pdf->SetFooter($footer);

		//TIRAR ERRO DE EXIBIÇÃO DO PDF NO CHROME
		if (ob_get_contents()) ob_end_clean();

		$pdf->writeHTML($html);
		$pdf->Output();
	}
	function gerarDescritivoLegado()
	{
		if ($this->input->post('id_usuario')) {
			$this->data['id_usuario'] = $this->input->post('id_usuario');
			$this->data['data_ini'] = str_replace('/', '-', $this->input->post('data_init'));
			$this->data['data_fim'] = str_replace('/', '-', $this->input->post('data_end'));
			$this->data['atividade'] = $this->model_atividade->getHistoricoAtividadesFiscal($this->data['id_usuario'], $this->data['data_ini'],  $this->data['data_fim'], null, null, null, $this->id_empresa, 'id');
			$result = $this->model_usuario->getUser($this->data['id_usuario'], null, $this->id_empresa);
			$this->data['matricula'] = ($result) ? $result[0]->matricula : '';
			$this->data['nome_usuario'] = ($result) ? $result[0]->nome : '';
		} elseif ($this->input->post('nome_fiscal')) {
			$this->data['nome_fiscal'] = $this->input->post('nome_fiscal');
			$this->data['data_ini'] = str_replace('/', '-', $this->input->post('data_init'));
			$this->data['data_fim'] = str_replace('/', '-', $this->input->post('data_end'));
			$this->data['atividade'] = $this->model_atividade->getHistoricoAtividadesFiscal(null, $this->data['data_ini'],  $this->data['data_fim'], $this->data['nome_fiscal'], null, null, $this->id_empresa, 'id');
			$result = $this->model_usuario->getUser(null, $this->data['nome_fiscal'], $this->id_empresa);
			$this->data['matricula'] = ($result) ? $result[0]->matricula : '';
			$this->data['nome_usuario'] = ($result) ? $result[0]->nome : '';
		}

		$html = $this->load->view('template/pdf/descritivo_legado', $this->data, TRUE);
		$header = $this->load->view('template/pdf/header', $this->session->userdata['logged_in'], TRUE);
		$footer = $this->load->view('template/pdf/footer', null, TRUE);

		$this->load->library('pdf');
		$mpdf = new Pdf();
		$pdf = $mpdf->load();

		$pdf->SetHTMLHeader($header);
		$pdf->SetFooter($footer);

		//TIRAR ERRO DE EXIBIÇÃO DO PDF NO CHROME
		if (ob_get_contents()) ob_end_clean();

		$pdf->writeHTML($html);
		$pdf->Output();
	}


	function gerarProdutividadeLegado()
	{
		if ($this->input->post('id_usuario')) {
			$this->data['id_usuario'] = $this->input->post('id_usuario');
			$this->data['data_ini'] = str_replace('/', '-', $this->input->post('data_start'));
			$this->data['data_fim'] = str_replace('/', '-', $this->input->post('data_over'));
			$this->data['atividade'] = $this->model_atividade->getHistoricoAtividadesFiscal($this->data['id_usuario'], $this->data['data_ini'],  $this->data['data_fim'], null, null, null, $this->id_empresa, null);
			$result = $this->model_usuario->getUser($this->data['id_usuario'], null, $this->id_empresa);
			$this->data['nome_usuario'] = ($result) ? $result[0]->nome : '';
			$this->data['matricula'] = ($result) ? $result[0]->matricula : '';
		} elseif ($this->input->post('nome_fiscal')) {
			$this->data['nome_fiscal'] = $this->input->post('nome_fiscal');
			$this->data['data_ini'] = str_replace('/', '-', $this->input->post('data_start'));
			$this->data['data_fim'] = str_replace('/', '-', $this->input->post('data_over'));

			$this->data['atividade'] = $this->model_atividade->getHistoricoAtividadesFiscal(null, $this->data['data_ini'],  $this->data['data_fim'], $this->data['nome_fiscal'], null, null, $this->id_empresa, null);

			$result = $this->model_usuario->getUser(null, $this->data['nome_fiscal'], $this->id_empresa);
			$this->data['id_usuario'] = ($result) ? $result[0]->id : '';
			$this->data['nome_usuario'] = ($result) ? $result[0]->nome : '';
			$this->data['matricula'] = ($result) ? $result[0]->matricula : '';
		}
		$this->data['atividade_fiscal'] = $this->model_atividade->getTotaisAtividades(
			$this->data['id_usuario'],
			$this->data['data_ini'],
			$this->data['data_fim'],
			$this->id_empresa
		);

		$html = $this->load->view('template/pdf/produtividade_legado', $this->data, true);
		$header = $this->load->view('template/pdf/header', $this->session->userdata['logged_in'], TRUE);
		$footer = $this->load->view('template/pdf/footer', null, TRUE);

		$this->load->library('Pdf');
		$mpdf = new Pdf();
		$pdf = $mpdf->load();
		$pdf->SetHTMLHeader($header);
		$pdf->SetFooter($footer);

		//TIRAR ERRO DE EXIBIÇÃO DO PDF NO CHROME
		if (ob_get_contents()) ob_end_clean();

		$pdf->writeHTML($html);
		$pdf->Output();
	}

	function gerarPdfOs()
	{
		if ($this->parametros_empresa->os != 1) {
			$message = array('message_heading' => 'Você não tem permissão para acessar essa página!', 'class_result' => 'red');
			$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
			redirect(base_url('/home'));
		}

		$verificar = $this->model_os->verificaOsExcluido($this->input->get('id'), $this->id_empresa);
		if ($verificar) {
			$message = array('message_heading' => 'Esta ordem de servço está cancelada!', 'class_result' => 'red');
			$this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
			redirect(base_url('/consultarordem'));
		}

		if ($this->input->get('id')) {
			$this->data['id_os'] = $this->input->get('id');

			$result = $this->model_os->getOsPdf($this->data['id_os'], $this->id_empresa);
			$historicoResult = $this->model_os->getHistoricoOrdemServico($this->data['id_os']);

			// var_dump($result); die;

			$this->data['id_fiscal'] = ($result) ? $result[0]->id_fiscal : '';
			$this->data['tipo'] = ($result) ? $result[0]->tipo : '';
			$this->data['nome_usuario'] = ($result) ? $result[0]->nome_usuario : '';
			$this->data['matricula'] = ($result) ? $result[0]->matricula : '';
			$this->data['descricao'] = ($result) ? $result[0]->descricao : '';
			$this->data['n_documento'] = ($result) ? $result[0]->n_documento : '';
			$this->data['n_protocolo'] = ($result) ? $result[0]->n_protocolo : '';
			$this->data['rc'] = ($result) ? $result[0]->rc : '';

			$this->data['data_prazo'] = ($result[0]->data_prazo) ? date('d/m/Y', strtotime($result[0]->data_prazo)) : '';

			$this->data['data_cadastro'] = ($result[0]->data_cadastro) ? date('d/m/Y', strtotime($result[0]->data_cadastro)) : '';

			$this->data['data_conclusao'] = ($result[0]->data_conclusao) ? date('d/m/Y', strtotime($result[0]->data_conclusao)) : '';

			$this->data['historico_os'] = $historicoResult;
			$this->data['nome_chefe'] = ($result) ? $result[0]->nome_chefe : '';
			$this->data['pontos'] = ($result) ? $result[0]->pontos : '';
			$this->data['nome_empresa'] = ($result) ? $result[0]->nome_empresa : '';
		}

		// var_dump($this->data['historico_os']); die;

		$html = $this->load->view('template/pdf/ordem_servico', $this->data, TRUE);
		$header = $this->load->view('template/pdf/os_header', $this->session->userdata['logged_in'], TRUE);
		$footer = $this->load->view('template/pdf/os_footer', null, TRUE, $this->data);

		// ----------------------------------------------------------------------------------------------
		$this->load->library('Pdf');
		$mpdf = new Pdf();
		$pdf = $mpdf->load();

		$pdf->SetHTMLHeader($header);
		$pdf->SetFooter($footer);

		//TIRAR ERRO DE EXIBIÇÃO DO PDF NO CHROME
		if (ob_get_contents()) ob_end_clean();

		$pdf->writeHTML($html);
		$pdf->Output();
	}
}
