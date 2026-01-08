<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_Total_Pontos_Fiscal extends CI_Model
{
	public function cadastro($pontosRemanescentes)
	{
		$this->db->trans_start();
		$sql = "INSERT INTO total_ponto_fiscal (id_fiscal, data_vigencia, pontos_totais, pontos_atividades_deducao, pontos_atividades_ufesp, pontos_atividades_pontuacao, total_arrecadado, saldo_remanescente, saldo_remanescente_utilizado) 
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
				ON CONFLICT (id_fiscal, data_vigencia) 
				DO UPDATE SET 
				pontos_totais=EXCLUDED.pontos_totais, 
				pontos_atividades_deducao=EXCLUDED.pontos_atividades_deducao,
				pontos_atividades_ufesp=EXCLUDED.pontos_atividades_ufesp,
				pontos_atividades_pontuacao=EXCLUDED.pontos_atividades_pontuacao,
				total_arrecadado=EXCLUDED.total_arrecadado,
				saldo_remanescente=EXCLUDED.saldo_remanescente, 
				saldo_remanescente_utilizado=EXCLUDED.saldo_remanescente_utilizado,
				data_update = now() ";
		$this->db->query($sql, $pontosRemanescentes);

		$this->logger->logAction('total_ponto_fiscal create', (array) $pontosRemanescentes);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			return false;
		}
		return true;
	}

	public function get($idFiscal, $periodo)
	{
		$periodo = DateTime::createFromFormat("Y-m", $periodo);
		$sql = "SELECT * FROM total_ponto_fiscal tpf where to_char(tpf.data_vigencia, 'YYYY-MM') = ? AND tpf.id_fiscal = ? ";
		$query = $this->db->query($sql, array($periodo->format('Y-m'), $idFiscal));
		return $query->row();
	}
}
