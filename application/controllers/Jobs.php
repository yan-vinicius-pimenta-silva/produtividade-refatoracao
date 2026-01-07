<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Jobs extends SO_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('model_pontos', 'Pontos');
	}

	/**
	 * Função que calcula a pontuação do fiscal.
	 *
	 * @return void
	 */
	public function calcularPontos()
	{
		if (!is_cli()) exit();
		$this->Pontos->calcularPontosPorFiscal();
	}
}
