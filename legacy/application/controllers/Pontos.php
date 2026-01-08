<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pontos extends SO_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->logado();
		$this->load->model('model_pontos', 'Pontos');

		$this->id_user = $this->session->userdata['logged_in']['id'];
		$this->nome_usuario = $this->session->userdata['logged_in']['nome'];
		$this->id_nivel = $this->session->userdata['logged_in']['nivel'];
		$this->id_empresa = $this->session->userdata['logged_in']['id_empresa'];
		$this->parametros_empresa = $this->session->userdata['logged_in']['parametros_empresa'];
		$this->data = array();
	}

	public function buscarPontosJSON()
	{
		$periodo = $this->input->get('periodo');
		if ($periodo == false) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(422)
				->set_output(json_encode(array(
					'status' => 'ERROR',
					'data' => FALSE,
					'message' => 'periodo não encontrado.'
				)));
		}

		$this->load->model('model_total_pontos_fiscal', 'TotalPontos');
		$pontosFiscal = $this->TotalPontos->get($this->id_user, $periodo);
		if (!$pontosFiscal) {
			return $this->output
				->set_content_type('application/json')
				->set_status_header(422)
				->set_output(json_encode(array(
					'status' => 'ERROR',
					'data' => [
						'pontos_atividades_pontuacao' => 0.0,
						'pontos_atividades_deducao' => 0.0,
						'pontos_atividades_ufesp' => 0.0,
						'pontos_totais' => 0.0,
						'total_arrecadado' => 0.0,
						'saldo_remanescente' => 0.0,
					],
					'message' => 'Nenhum ponto encontrado!'
				)));
		}

		$pontosFiscal->pontos_atividades_pontuacao =  $pontosFiscal->pontos_atividades_pontuacao / 10;
		$pontosFiscal->pontos_atividades_deducao = $pontosFiscal->pontos_atividades_deducao / 10;
		$pontosFiscal->pontos_atividades_ufesp = $pontosFiscal->pontos_atividades_ufesp / 10;
		$pontosFiscal->pontos_totais = $pontosFiscal->pontos_totais / 10;
		$pontosFiscal->total_arrecadado = $pontosFiscal->total_arrecadado / 100;
		$pontosFiscal->saldo_remanescente = $pontosFiscal->saldo_remanescente / 10;

		return $this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode(array(
				'status' => 'SUCCESS',
				'data' => $pontosFiscal,
			)));
	}
}
