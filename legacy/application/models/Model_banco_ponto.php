<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_Banco_Ponto extends CI_Model
{
	public function get($idFiscal, $dataVigencia)
	{
		$this->db->select('*');
		$this->db->from('banco_ponto_fiscal bpf');
		$this->db->where('bpf.id_fiscal', $idFiscal);
		$this->db->where('bpf.data_exclusao', null);
		$this->db->where('bpf.data_vigencia', $dataVigencia);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->row();
		}
		return false;
	}

	public function cadastro($pontosRemanescentes)
	{
		$this->db->trans_start();
		$sql = "INSERT INTO banco_ponto_fiscal (id_fiscal, saldo_remanescente, data_vigencia) 
				VALUES (?, ?, ?)
				ON CONFLICT (id_fiscal, data_vigencia) DO UPDATE SET saldo_remanescente=EXCLUDED.saldo_remanescente, data_update = now() ";
		$this->db->query($sql, $pontosRemanescentes);

		$this->logger->logAction('banco_ponto_fiscal create', (array) $pontosRemanescentes);
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			log_message('error', $this->db->error());
			return false;
		}
		return true;
	}
}
