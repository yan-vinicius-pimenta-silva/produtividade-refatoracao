<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Model_Atividade_Fiscal_Contabilizada extends CI_Model
{
	/**
	 * Cadastra as atividades contabilizadas
	 * Se o valor existir no banco, realiza o update.
	 *
	 * @param [type] $atividadesCalculadas
	 * @return void
	 */
	public function cadastrar($atividadesCalculadas)
	{
		$this->db->trans_start();
		foreach ($atividadesCalculadas as $atividade) {
			if ($atividade['tipo_pontuacao'] == 'UFESP') {
				unset($atividade['tipo_pontuacao']);
				$sql = "INSERT INTO atividade_fiscal_contabilizada (id_atividade, id_fiscal, data_vigencia, valor_total, valor_ufesp, total_pontos, data_cadastro, quantidade, ponto_base_calculo) 
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
				ON CONFLICT (id_atividade, id_fiscal, data_vigencia) DO UPDATE SET 
				valor_ufesp=EXCLUDED.valor_ufesp, 
				valor_total=EXCLUDED.valor_total, 
				total_pontos=EXCLUDED.total_pontos, 
				data_update = now(),
				quantidade=EXCLUDED.quantidade,
				ponto_base_calculo=EXCLUDED.ponto_base_calculo";
				$this->db->query($sql, $atividade);
			} else {
				unset($atividade['tipo_pontuacao']);
				$sql = "INSERT INTO atividade_fiscal_contabilizada (id_atividade, id_fiscal, data_vigencia, total_pontos, data_cadastro, quantidade, ponto_base_calculo) VALUES (?, ?, ?, ?, ?, ?, ?)
				ON CONFLICT (id_atividade, id_fiscal, data_vigencia) DO UPDATE SET 
				total_pontos=EXCLUDED.total_pontos,  
				quantidade=EXCLUDED.quantidade,
				ponto_base_calculo=EXCLUDED.ponto_base_calculo,
				data_update = now()";
				$this->db->query($sql, $atividade);
			}
			$this->logger->logAction('atividade_fiscal_contabilizada create', (array) $atividade);
		}
		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			return false;
		}
		return true;
	}


	public function update($id_atividade, $data)
	{
		$this->db->where('id', $id_atividade);
		if ($this->db->update('atividade_fiscal', $data)) {
			$this->logger->logAction('atividade_fiscal update(ID: ' . $id_atividade . ')', (array) $data);
			return true;
		}
		return false;
	}

	public function get(
		$id_usuario = null,
		$data_ini = null,
		$data_fim = null,
		$nome_fiscal = null,
		$n_documento = null,
		$n_protocolo = null,
		$id_empresa = null
	) {
		$this->db->select('af.*,
                           a.tipo,
                           a.id as id_atividade,
                           a.pontos,
                           u.nome, 
                           u.id as id_usuario');
		$this->db->from('atividade_fiscal af');
		$this->db->join('atividade a', 'a.id = af.id_atividade', 'left');
		$this->db->join('usuarios u', 'u.id = af.id_fiscal', 'left');

		$this->db->where('af.data_exclusao', null);
		if ($id_usuario != null)
			$this->db->where('af.id_fiscal', $id_usuario);
		if ($data_ini != null)
			$this->db->where('af.data_conclusao >=', $data_ini);
		if ($data_fim != null)
			$this->db->where('af.data_conclusao <=', $data_fim);
		if ($nome_fiscal != null)
			$this->db->like('u.nome', $nome_fiscal);
		if ($n_documento != null)
			$this->db->where('af.documento', $n_documento);
		if ($n_protocolo != null)
			$this->db->where('af.protocolo', $n_protocolo);
		if ($id_empresa != null)
			$this->db->where('af.id_empresa', $id_empresa);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
	}

	public function totalPontosFiscal($id_fiscal)
	{
		$this->db->select_sum('total_pontos');
		$this->db->select('u.nome, u.id, afc.data_vigencia');
		$this->db->from('atividade_fiscal_contabilizada afc');
		$this->db->join('atividade a', 'afc.id_atividade = a.id');
		$this->db->join('usuarios u', 'u.id = afc.id_fiscal');
		if ($id_fiscal != null)
			$this->db->where('afc.id_fiscal', $id_fiscal);
		$this->db->group_by('u.nome, u.id, afc.data_vigencia');
		$query = $this->db->get();
		return $query->row();
	}

	public function clear()
	{
		$sql = 'TRUNCATE table atividade_fiscal_contabilizada';
		$this->db->query($sql);
		$sql = 'TRUNCATE table total_ponto_fiscal';
		$this->db->query($sql);
	}
}
