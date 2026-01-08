<?php
class Model_Pontos extends CI_Model
{
	private function getTotais($idFiscal, $dataVigencia = null)
	{
		$this->db->select('COALESCE(sum(total_pontos), 0) as total_pontos, 
		COALESCE(SUM(afc.valor_total), 0) as total_arrecadado, 
		ta.nome as tipo_atividade, 
		u.nome as nome_fiscal, 
		u.id as id_fiscal, 
		afc.data_vigencia');
		$this->db->from('atividade_fiscal_contabilizada afc');
		$this->db->join('atividade a', 'afc.id_atividade = a.id');
		$this->db->join('tipo_atividade ta', 'ta.id = a.id_tipo_atividade');
		$this->db->join('usuarios u', 'u.id = afc.id_fiscal');
		$this->db->where('afc.id_fiscal', $idFiscal);
		$this->db->where('to_char(afc.data_vigencia, \'YYYY-MM\') =', $dataVigencia);

		$this->db->group_by('u.nome, u.id, afc.data_vigencia, ta.nome');
		$query = $this->db->get();
		return $query->result();
	}

	function getPontos($id = null)
	{
		$this->db->select('a.pontos');
		$this->db->from('atividade_lancamento h');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');
		$this->db->where('id_usuario', $id);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}

	function calculaAtividades($id_usuario = null, $id_atividade = null)
	{
		$this->db->select('COUNT(h.id_atividade),
                           h.id_atividade,
                           SUM(a.pontos)');
		$this->db->from('atividade_lancamento h');
		$this->db->join('atividade a', 'a.id = h.id_atividade', 'left');

		if ($id_usuario != null)
			$this->db->where('h.id_usuario', $id_usuario);

		if ($id_atividade != null)
			$this->db->where('h.id_atividade', $id_atividade);

		$this->db->group_by("h.id");

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}

	/**
	 * Período de calculo automático será mes anterior e atual ?
	 * TODO: Período
	 *
	 * @return void
	 */
	public function calcularPontosPorFiscal()
	{
		$this->load->model('model_usuario', 'fiscal');
		$this->load->model('model_atividade_fiscal_contabilizada', 'AtividadeFiscalContabilizada');
		$this->load->model('model_atividade_fiscal', 'AtividadeFiscal');
		$this->load->model('model_parametros', 'Parametros');
		$this->load->model('model_unidade_fiscal', 'UnidadeFiscal');
		$this->load->model('model_atividade_fiscal_contabilizada', 'AtividadeFiscalContabilizada');
		$this->load->model('model_banco_ponto', 'BancoPonto');
		try {
			$fiscais = $this->fiscal->buscaFiscais();
			//limpa tabela
			$this->AtividadeFiscalContabilizada->clear();
			$periodos = [
				// date('Y-m',  strtotime('+2 month')),
				// date('Y-m',  strtotime('+1 month')),
				date('Y-m',  strtotime('this month')),
				date('Y-m',  strtotime('-1 month')),
				date('Y-m',  strtotime('-2 month')),
			];
			foreach ($periodos as $periodo) {
				foreach ($fiscais as $fiscal) {
					$this->calcularPontosAtividades($fiscal->id, $periodo);
					$this->calcularTotalPontos($fiscal->id, $periodo);
				}
			}
		} catch (\Exception $th) {
			log_message('error', $th->getMessage());
		}
	}


	/**
	 * O calculo depende da atividade executada e do lançamento feito, 
	 * então precisa do id_atividade, valor ufesp (se utilizar), id_fiscal, período_vigencia
	 * 
	 * A fórmula para atividades UFESP é (Valor total atividade / valor ufesp) / 10 * ponto atividade
	 */

	public function calcularPontosAtividades($idFiscal, $periodo = null)
	{
		$atividadesCalculadas = array();
		//busca atividade
		$atividadesConfirmadas = $this->AtividadeFiscal->buscaAtividadesConfirmadas($idFiscal, $periodo);
		$atividadesUfesp = $this->Parametros->getAtividades(null, $this->Parametros::TIPO_ATIVIDADE_UFESP, null);
		$atividadesPontuacao = $this->Parametros->getAtividades(null, $this->Parametros::TIPO_ATIVIDADE_PONTUACAO, null);
		$atividadesDeducao = $this->Parametros->getAtividades(null, $this->Parametros::TIPO_ATIVIDADE_DEDUCAO, null);

		if (!$atividadesConfirmadas ||  !$atividadesUfesp || !$atividadesPontuacao) {
			// log_message('error', "Nenhuma atividade encontrada. IDFISCAL: $idFiscal");
			return FALSE;
		}

		//Calcula o valor total por atividade ufesp
		foreach ($atividadesUfesp as $atividadeUfesp) {
			$totalValorAtividade = 0;
			$flag = '';
			foreach ($atividadesConfirmadas as $atividade) {
				if ($atividade->tipo_pontuacao == 'UFESP' && $atividade->id_atividade == $atividadeUfesp->id) {
					$dataVigencia = date('Y-m-01', strtotime($atividade->data_conclusao));
					// Busca a ufesp por ano, caso o lançamento seja feito em período de transição da taxa ufesp
					// (atividade lançada em janeiro com data de dezembro, utilizará o valor ufesp do ano em que a atividade possui data_conclusao)
					$valorUfespAtivo = $this->UnidadeFiscal->get(date('Y', strtotime($atividade->data_conclusao)), null);
					if (!$valorUfespAtivo) {
						log_message('error', 'Nenhum valor UFESP ativo.');
						return FALSE;
					}
					$valorUfesp = (int)$valorUfespAtivo[0]->valor; // valor ufesp usado no calculo
					// Se a data vigencia da atual atividade for diferente da anterior, zera o total
					if ($flag != $dataVigencia) {
						$totalValorAtividade = 0;
					}
					$totalValorAtividade += (int)$atividade->valor; // valor arrecadado com essa atividade
					$pontoAtividade  = (int) str_replace('.', '', $atividadeUfesp->pontos);
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['id_atividade'] = $atividadeUfesp->id;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['id_fiscal'] = $idFiscal;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['data_vigencia'] = $dataVigencia;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['valor_total'] = $totalValorAtividade;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['valor_ufesp'] = $valorUfesp;
					$quantidade_ufesp = (int) intdiv($totalValorAtividade, $valorUfesp);
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['total_pontos'] = intdiv($quantidade_ufesp * $pontoAtividade, 10);
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['data_cadastro'] = date('Y-m-d H:i:s');
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['tipo_pontuacao'] = 'UFESP';
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['quantidade'] = $quantidade_ufesp;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['ponto_base_calculo'] = $pontoAtividade;
					$flag = $dataVigencia;
				}
			}
		}
		foreach ($atividadesPontuacao as $atividadePonto) {
			$totalPonto = 0;
			$quantidade = 0;
			$dataVigenciaTemp = '';
			foreach ($atividadesConfirmadas as $atividade) {
				// Calcular total de pontos por tipo atividade
				if ($atividade->tipo_pontuacao == 'PONTUACAO' && $atividade->id_atividade == $atividadePonto->id) {
					$dataVigencia = date('Y-m-01', strtotime($atividade->data_conclusao));
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['id_atividade'] = $atividadePonto->id;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['id_fiscal'] = $idFiscal;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['data_vigencia'] = $dataVigencia;
					if ($dataVigenciaTemp != $dataVigencia) {
						$totalPonto = 0;
					}
					$totalPonto += (int) $atividade->pontuacao_total;
					$quantidade += (int) $atividade->quantidade;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['total_pontos'] = $totalPonto;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['data_cadastro'] = date('Y-m-d H:i:s');
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['tipo_pontuacao'] = 'PONTUAÇÃO';
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['quantidade'] = $quantidade;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['ponto_base_calculo'] = str_replace('.', '', $atividadePonto->pontos);
					$dataVigenciaTemp = $dataVigencia;
				}
			}
		}

		foreach ($atividadesDeducao as $atividadeDeducao) {
			$totalPonto = 0;
			$quantidade = 0;
			$dataVigenciaTemp = '';
			foreach ($atividadesConfirmadas as $atividade) {
				// Calcular total de pontos por tipo atividade
				if ($atividade->tipo_pontuacao == 'DEDUCAO' && $atividade->id_atividade == $atividadeDeducao->id) {
					$dataVigencia = date('Y-m-01', strtotime($atividade->data_conclusao));
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['id_atividade'] = $atividadeDeducao->id;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['id_fiscal'] = $idFiscal;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['data_vigencia'] = $dataVigencia;
					if ($dataVigenciaTemp != $dataVigencia) {
						$totalPonto = 0;
					}
					$totalPonto += (int) $atividade->pontuacao_total;
					$quantidade += (int) $atividade->quantidade;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['total_pontos'] = $totalPonto;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['data_cadastro'] = date('Y-m-d H:i:s');
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['tipo_pontuacao'] = 'DEDUÇÃO';
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['quantidade'] = $quantidade;
					$atividadesCalculadas[$atividade->id_atividade . $dataVigencia]['ponto_base_calculo'] = str_replace('.', '', $atividadeDeducao->pontos);
					$dataVigenciaTemp = $dataVigencia;
				}
			}
		}
		$result = $this->AtividadeFiscalContabilizada->cadastrar($atividadesCalculadas);

		if (!$result) {
			log_message('error', 'Os valores calculados não foram inseridos no banco.');
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * calcula e cadastra pontos remanescentes 
	 * (> 100 ou < 0) 
	 *
	 * @param [int] $id_fiscal
	 * @return void
	 */
	private function calcularTotalPontos($idFiscal, $periodo = null)
	{
		// Busca pontos remanescentes do mês anterior
		$periodoAnterior = DateTime::createFromFormat('Y-m', $periodo);
		$periodoAnterior->modify('-1 month');
		$ultimoMes = $periodoAnterior->format('Y-m-01');
		$remanescenteAnterior = 0;
		$pontosRemanescentes = $this->BancoPonto->get($idFiscal, $ultimoMes);
		$remanescenteAnterior = ($pontosRemanescentes !== false) ? (int)$pontosRemanescentes->saldo_remanescente : 0;
		// Busca pontos do mês atual
		$totaisAtividades = $this->getTotais($idFiscal, $periodo);
		if (!$totaisAtividades) {
			return;
		}
		$totalPontos = 0;
		$totalRemanescente = 0;
		$pontosAtividadesDeducao = 0;
		$pontosAtividadesUfesp = 0;
		$pontosAtividadesPontuacao = 0;
		$totalArrecadado = 0;
		$dataVigencia = date('Y-m-d');
		$data = array();

		foreach ($totaisAtividades as $key => $total) {
			switch ($total->tipo_atividade) {
				case 'UFESP':
					$pontosAtividadesUfesp += (int)$total->total_pontos;
					break;
				case 'PONTUACAO':
					$pontosAtividadesPontuacao += (int)$total->total_pontos;
					break;
				case 'DEDUCAO':
					$pontosAtividadesDeducao += (int)$total->total_pontos;
					break;
			}
			$totalArrecadado += (int)$total->total_arrecadado;
			$totalPontos += (int) $total->total_pontos;
			$dataVigencia = $total->data_vigencia;
		}

		/**
		 * Desconta pontos remanescentes
		 * Deixar apenas para saldo negativo, porém, caso mude a lei, 
		 * o positivo já está aqui.
		 */
		// if ($remanescenteAnterior !== 0) {
		// 	$totalPontos += $remanescenteAnterior;
		// }

		if ($remanescenteAnterior < 0) {
			$totalPontos += $remanescenteAnterior;
		} else {
			$remanescenteAnterior = 0;
		}

		if ($totalPontos < 0) {
			$totalRemanescente = $totalPontos;
		}
		// 1000 == 100,0 (100,0 em inteiro)
		// if ($totalPontos > 1000) {
		// 	$totalRemanescente = (int) bcsub($totalPontos, 1000);
		// }
		$data = [
			'id_fiscal' => $idFiscal,
			'data_vigencia' => $dataVigencia,
			'pontos_totais' => $totalPontos,
			'pontos_atividades_deducao' => $pontosAtividadesDeducao,
			'pontos_atividades_ufesp' => $pontosAtividadesUfesp,
			'pontos_atividades_pontuacao' => $pontosAtividadesPontuacao,
			'total_arrecadado' => $totalArrecadado,
			'saldo_remanescente' => $totalRemanescente,
			'saldo_remanescente_utilizado' => $remanescenteAnterior,

		];
		$this->load->model('Model_total_pontos_fiscal', 'TotalPontos');
		$this->TotalPontos->cadastro($data);

		$pontosRemanescentes = [
			'id_fiscal' => $idFiscal,
			'saldo_remanescente' => $totalRemanescente,
			'data_vigencia' => $dataVigencia,
		];
		if (!$this->BancoPonto->cadastro($pontosRemanescentes)) {
			log_message('error', 'Erro ao cadastrar banco de pontos');
		}
	}
}
