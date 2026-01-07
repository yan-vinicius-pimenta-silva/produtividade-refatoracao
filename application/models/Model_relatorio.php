<?php
class Model_Relatorio extends CI_Model
{
	function produtividade($idFiscal, $periodo, $idEmpresa = null)
	{
		$data = array();
		$data['receita_gerada'] = $this->buscaReceitaGerada($idFiscal, $periodo, $idEmpresa);
		$data['contabilizadas_ufesp'] = $this->buscaContabilizadasUfesp($idFiscal, $periodo, $idEmpresa);
		$data['contabilizadas_pontuacao'] = $this->buscaContabilizadasPontuacao($idFiscal, $periodo, $idEmpresa);
		$data['contabilizadas_deducao'] = $this->buscaContabilizadasDeducao($idFiscal, $periodo, $idEmpresa);
		$data['totais_pontos'] = $this->buscaTotais($idFiscal, $periodo);

		return $data;
	}

	function fiscalPontuacao($periodo, $idEmpresa = null)
	{
		$periodo = DateTime::createFromFormat("d-m-Y", $periodo);
		$sql = "SELECT u.nome, 
						u.matricula,
						tpf.pontos_totais,
						tpf.pontos_atividades_deducao,
						tpf.pontos_atividades_ufesp,
						tpf.pontos_atividades_pontuacao,
						tpf.total_arrecadado,
						tpf.saldo_remanescente,
						tpf.saldo_remanescente_utilizado,
						tpf.data_vigencia
			FROM usuarios u
			LEFT JOIN total_ponto_fiscal tpf ON tpf.id_fiscal = u.id
			AND to_char(tpf.data_vigencia, 'YYYY-MM') = ?
			WHERE u.id_empresa = ?
			AND u.nivel = 2 AND u.excluido = 0
			ORDER BY u.nome, u.id ASC";

		$query = $this->db->query($sql, array($periodo->format('Y-m'), $idEmpresa));
		return $query->result();
	}

	function totalPontuacao($periodo, $idEmpresa = null)
	{
		$periodo = DateTime::createFromFormat("d-m-Y", $periodo);
		$sql = "SELECT SUM(tpf.pontos_totais) AS soma, COUNT(u.id) AS qntd_fiscais
			FROM usuarios u
			LEFT JOIN total_ponto_fiscal tpf ON (tpf.id_fiscal = u.id
			AND to_char(tpf.data_vigencia, 'YYYY-MM') = ?)
			WHERE u.id_empresa = ?
			AND u.nivel = 2 AND u.excluido = 0";
			
		$query = $this->db->query($sql, array($periodo->format('Y-m'), $idEmpresa));
		return $query->result();
	}

	function buscaChefeDpto($idEmpresa = null)
	{
		$sql = "SELECT u.nome, u.matricula
			FROM usuarios u
			WHERE u.id_empresa = $idEmpresa
			AND u.nivel = 1 AND u.excluido = 0";
			
		$query = $this->db->query($sql);
		return $query->result();
	}

	function descritivo($idFiscal, $dataInicio, $dataFim, $idEmpresa)
	{
		$this->db->select('af.documento, af.protocolo, af.data_conclusao, af.valor, a.tipo')
			->from('atividade_fiscal af')
			->join('atividade a', 'a.id = af.id_atividade')
			->where('af.data_validado <> ', null)
			->where('af.id_fiscal = ', $idFiscal)
			->where('af.data_conclusao >= ', $dataInicio)
			->where('af.data_conclusao <= ', $dataFim)
			->where('af.id_empresa', $idEmpresa)
			->order_by('af.data_conclusao ASC, a.tipo ASC, a.id_tipo_atividade ASC');
		$query = $this->db->get();
		return $query->result();
	}

	private function buscaReceitaGerada($idFiscal, $periodo, $idEmpresa = null)
	{
		$periodo = DateTime::createFromFormat("d-m-Y", $periodo);
		$sql = "SELECT a.tipo, coalesce(SUM(afc.valor_total), 0) as receita_gerada 
		FROM atividade a
		join tipo_atividade ta on ta.id = a.id_tipo_atividade 
		LEFT JOIN atividade_fiscal_contabilizada afc ON afc.id_atividade = a.id AND to_char(afc.data_vigencia, 'YYYY-MM') = ? AND  afc.id_fiscal = ? 
		WHERE a.ativo = 1 
		and a.excluido = 0
		and ta.nome = 'UFESP'
		and a.id_empresa = ?
		GROUP BY a.tipo";
		$query = $this->db->query($sql, array($periodo->format('Y-m'), $idFiscal, $idEmpresa));
		return $query->result();
	}

	private function buscaContabilizadasUfesp($idFiscal, $periodo, $idEmpresa = null)
	{
		$periodo = DateTime::createFromFormat("d-m-Y", $periodo);
		$sql = "SELECT a.tipo, a.pontos, coalesce(afc.valor_total, 0) as valor_total, coalesce(afc.total_pontos , 0) as total_pontos
		FROM atividade a
		join tipo_atividade ta on ta.id = a.id_tipo_atividade 
		LEFT JOIN atividade_fiscal_contabilizada afc ON afc.id_atividade = a.id AND to_char(afc.data_vigencia, 'YYYY-MM') = ? AND  afc.id_fiscal = ? 
		WHERE a.ativo = 1 
		and a.excluido = 0
		and a.id_empresa = ?
		and ta.nome = 'UFESP'";
		$query = $this->db->query($sql, array($periodo->format('Y-m'), $idFiscal, $idEmpresa));
		return $query->result();
	}
	//TODO: Buscar quantidade
	private function buscaContabilizadasPontuacao($idFiscal, $periodo, $idEmpresa = null)
	{
		$periodo = DateTime::createFromFormat("d-m-Y", $periodo);
		$sql = "SELECT a.tipo, a.pontos, coalesce(afc.total_pontos , 0) as total_pontos, quantidade
		FROM atividade a
		join tipo_atividade ta on ta.id = a.id_tipo_atividade 
		LEFT JOIN atividade_fiscal_contabilizada afc ON afc.id_atividade = a.id AND to_char(afc.data_vigencia, 'YYYY-MM') = ? AND  afc.id_fiscal = ? 
		WHERE a.ativo = 1 
		and a.excluido = 0
		and a.id_empresa = ?
		and ta.nome = 'PONTUACAO'";
		$query = $this->db->query($sql, array($periodo->format('Y-m'), $idFiscal, $idEmpresa));
		return $query->result();
	}
	//TODO: Buscar quantidade
	private function buscaContabilizadasDeducao($idFiscal, $periodo, $idEmpresa = null)
	{
		$periodo = DateTime::createFromFormat("d-m-Y", $periodo);
		$sql = "SELECT a.tipo,a.pontos, coalesce(afc.total_pontos , 0) as total_pontos, quantidade
		FROM atividade a
		join tipo_atividade ta on ta.id = a.id_tipo_atividade 
		LEFT JOIN atividade_fiscal_contabilizada afc ON afc.id_atividade = a.id AND to_char(afc.data_vigencia, 'YYYY-MM') = ? AND  afc.id_fiscal = ? 
		WHERE a.ativo = 1 
		and a.excluido = 0
		and a.id_empresa = ?
		and ta.nome = 'DEDUCAO'";
		$query = $this->db->query($sql, array($periodo->format('Y-m'), $idFiscal, $idEmpresa));
		return $query->result();
	}

	private function buscaTotais($idFiscal, $periodo)
	{
		$periodo = DateTime::createFromFormat("d-m-Y", $periodo);
		$sql = "SELECT * FROM total_ponto_fiscal tpf where to_char(tpf.data_vigencia, 'YYYY-MM') = ? AND  tpf.id_fiscal = ? ";
		$query = $this->db->query($sql, array($periodo->format('Y-m'), $idFiscal));
		return $query->row();
	}

	private function buscaAtividades()
	{
		$this->db->select('a.id, a.tipo, a.pontos, ta.nome as tipo_atividae ');
		$this->db->from('atividade a');
		$this->db->join('tipo_atividade ta', 'ta.id = a.id_tipo_atividade');

		$this->db->where('a.ativo', 1);
		$this->db->order_by('ta.nome, a.tipo');
		$query = $this->db->get();
		return $query->result();
	}
}
