<?php
class Model_os extends CI_Model
{

    function getOsPdf($id_os, $id_empresa)
    {

        $this->db->select('Os.*,
        a.tipo,
        a.pontos,
        e.nome as nome_empresa,
        u.nome as nome_usuario,
        u.matricula,
        uc.nome as nome_chefe');
        $this->db->from('ordem_servico Os');
        $this->db->join('empresa e', 'e.id = Os.id_empresa', 'left');
        $this->db->join('usuarios u', 'u.id = Os.id_fiscal', 'left');
        $this->db->join('usuarios uc', 'uc.id = Os.id_chefe', 'left');
        $this->db->join('atividade a', 'a.id = Os.id_atividade', 'left');
        $this->db->where('Os.id', $id_os);
        $this->db->where('Os.id_empresa', $id_empresa);
        $this->db->where('Os.excluido', 0);

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        }
        return false;
    }

    function verificaOsExcluido($id_os)
    {
        $this->db->select('Os.excluido');
        $this->db->from('ordem_servico Os');
        $this->db->where('Os.id', $id_os);
        $this->db->where('Os.excluido', 1);

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        }
        return false;
    }

    function getOsFiscal($id_user, $id_empresa)
    {
        $this->db->select('Os.*,
            os.tipo,
            a.id as id_atividade');
        $this->db->from('ordem_servico Os');
        $this->db->join('atividade a', 'a.id = Os.id_atividade', 'left');
        $this->db->where('Os.is_respondido', 0);
        $this->db->where('Os.id_fiscal', $id_user);
        $this->db->where('Os.id_empresa', $id_empresa);
        $this->db->where('Os.excluido', 0);
        $this->db->order_by('Os.data_prazo', 'ASC');

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        }
        return false;
    }

    function getOrdemServico($id_os)
    {
        $this->db->select('os.id, 
        os.id_atividade, 
        os.id_chefe,  
        uc.nome as nome_chefe, 
        os.id_fiscal,  
        uf.nome as nome_fiscal,
        uf.id_empresa,
        os.n_documento, 
        os.n_protocolo, 
        os.rc,
        os.descricao, 
        os.observacao, 
        os.motivo_exclusao, 
        os.excluido,
        os.validado, 
        os.is_respondido,
        os.data_cadastro,
        os.data_update,
        os.data_prazo,
        os.data_conclusao
        ');
        $this->db->from('ordem_servico os');
        $this->db->join('atividade a', 'a.id = os.id_atividade', 'left');
        $this->db->join('usuarios uc', 'uc.id = os.id_chefe', 'left');
        $this->db->join('usuarios uf', 'uf.id = os.id_fiscal', 'left');
        $this->db->join('empresa e', 'e.id = os.id_empresa', 'left');

        $this->db->where('os.id', $id_os);

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        }
        return false;
    }

    function getHistoricoOrdemServico($id_os)
    {
        $this->db->select('hos.id, 
        hos.descricao, 
        hos.observacao, 
        hos.anexo, 
        hos.data_cadastro, 
        hos.data_update,
        u.nome as usuario_interacao,
        s.nome as status_nome,
        s.cor as status_cor,
        s.id as status_id');
        $this->db->from('historico_ordem_servico hos');
        // $this->db->join('atividade a', 'a.id = os.id_atividade', 'left');
        // $this->db->join('ordem_servico os', 'uc.id = hos.id_ordem_servico', 'left');
        $this->db->join('usuarios u', 'u.id = hos.id_usuario', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        // $this->db->join('empresa e', 'e.id = os.id_empresa', 'left');

        $this->db->where('hos.id_ordem_servico', $id_os);
        $this->db->order_by('hos.id', 'ASC');

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        }
        return false;
    }

    function getOsNotificacao($id_user, $id_empresa, $data)
    {
        $this->db->select('os.*');
        $this->db->from('ordem_servico os');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico GROUP BY id_ordem_servico) hso', 'os.id = hso.id_ordem_servico');
        $this->db->join('historico_ordem_servico hos', 'hos.id = hso.id', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        $this->db->where('s.id',  1);
        $this->db->where('os.is_respondido', 0);
        $this->db->where('os.id_fiscal', $id_user);
        $this->db->where('os.id_empresa', $id_empresa);
        $this->db->where('os.excluido', 0);
        $this->db->where('os.data_prazo <=', $data);
        $this->db->order_by('os.data_prazo', 'ASC');

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        }
        return false;
    }

    function cadastrarOs($data)
    {
        $this->db->insert('ordem_servico', $data);
        $id_os = $this->db->insert_id();
        if (($this->db->trans_status() === TRUE) && $id_os) {
            $historicoData = array(
                'id_ordem_servico' => $id_os,
                'id_usuario' => $data['id_chefe'],
                'id_status' => 1,
                'observacao' => $data['observacao'],
                'data_cadastro' => $data['data_cadastro'],
                'data_update' => $data['data_update']
            );
            $this->db->insert('historico_ordem_servico', $historicoData);
        }
        if (($this->db->trans_status() === FALSE) || !$id_os) {
            $this->db->trans_rollback();
            return false;
        }
        $this->logger->logAction('ordem_servico create', (array) $data);
        return $id_os;
    }

    function updateAnexoHistoricoOrdemServico($id_os, $data)
    {
        $teste = array('anexo' => (isset($data['full_path'])) ? './uploads/empresa_' . $this->id_empresa . '/ordem_servico/os_' . $id_os . '/anexo/' . $data['file_name'] : null);
        $this->db->where('id_ordem_servico', $id_os);
        $this->db->update('historico_ordem_servico', $teste);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }
        return true;
    }

    function validarOs($id_os, $data, $id_empresa) //não há como por observação na validação.
    {
        $this->db->where('id', $id_os);            //só vira atividade quando tem resposta do fiscal
        if ($this->db->update('ordem_servico', $data)) {
            $historicoData = array(
                'id_ordem_servico' => $id_os,
                'id_usuario' => $this->id_user,
                'id_status' => 3,
                'data_cadastro' => $data['data_update'], //recebe a mesma data da alteração
                'data_update' => $data['data_update']
            );
            $this->db->insert('historico_ordem_servico', $historicoData);
            $this->generateAtividadeLancamentoFromOS($id_os, $this->id_empresa);
            if (($this->db->trans_status() === FALSE) || !$id_os) {
                $this->db->trans_rollback();
                return false;
            }
            $this->logger->logAction('ordem_servico update(ID: ' . $id_os . ')', (array) $data);
            return true;
        }
    }

    function updateOrdemServico($id_os, $data)
    {
        $this->db->where('id', $id_os);
        $this->db->update('ordem_servico', $data);
        if (($this->db->trans_status() === FALSE) || !$id_os) {
            $this->db->trans_rollback();
            return false;
        }
        $this->logger->logAction('ordem_servico update(ID: ' . $id_os . ')', (array) $data);
        return true;
    }

    function generateAtividadeLancamentoFromOS($id_os, $id_empresa)
    {
        $this->db->select('
        os.id as id_os,
        os.id_atividade,
        os.n_documento,
        os.data_conclusao,
        os.n_protocolo,
        os.id_fiscal as id_usuario,
        os.data_cadastro,
        os.observacao,
        os.id_empresa,
        os.data_update,
        hos.anexo as nome_arquivo,
        os.rc,
        os.validado as validacao');
        $this->db->from('ordem_servico os ');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico  WHERE id_status = 2 GROUP BY id_ordem_servico) hso', 'os.id = hso.id_ordem_servico');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico  WHERE id_status = 3 GROUP BY id_ordem_servico) hsof', 'hso.id_ordem_servico = hso.id_ordem_servico');
        $this->db->join('historico_ordem_servico hos', 'hos.id = hso.id', 'left');
        $this->db->join('usuarios uc', 'uc.id = os.id_chefe', 'left');
        $this->db->join('usuarios uf', 'uf.id = os.id_fiscal', 'left');
        $this->db->join('usuarios uit', 'uit.id = hos.id_usuario', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        $this->db->where('os.excluido', 0);
        $this->db->where('os.id', $id_os);

        $query = $this->db->get();
        if ($query->num_rows() >= 1) {
            $data =  $query->result();
            $this->db->insert('atividade_lancamento', $data[0]);
            return true;
        }
        return false;
    }

    /* parâmetros: $id_atividade */
    function deletarOs($id_os, $value)
    {
        $historicoData = array(
            'id_ordem_servico' => $id_os,
            'id_status' => 4,
            'data_cadastro' => date('Y-m-d H:i:s'), // mesma data update
            'data_update' => date('Y-m-d H:i:s'),
            'observacao' => $value['motivo_exclusao'], //recebe observação com motivo da exclusao
            'id_usuario' => $this->id_user
        );
        $this->db->where('id', $id_os);
        $this->db->update('ordem_servico', array('motivo_exclusao' => $value['motivo_exclusao']));
        if (($this->db->trans_status() === FALSE) || !$id_os) {
            $this->db->trans_rollback();
            return false;
        }
        $this->logger->logAction('ordem_servico delete(ID: ' . $id_os . ')', (array) array('motivo_exclusao' => $value['motivo_exclusao']));
        $this->db->insert('historico_ordem_servico', $historicoData);

        return true;
    }

    function responderOs($id_ordem_servico, $dataHistorico, $dataOrdemServico)
    {
        $this->db->where('id', $id_ordem_servico);
        $this->db->update('ordem_servico', $dataOrdemServico);
        if (($this->db->trans_status() === FALSE)) {
            $this->db->trans_rollback();
            return false;
        }
        $this->db->insert('historico_ordem_servico', $dataHistorico);
        $this->logger->logAction('ordem_servico / historico_ordem_servico answer(ID: ' . $id_ordem_servico . ')', (array) array('dataHistorico' => $dataHistorico, 'dataOrdemServico' => $dataOrdemServico));
        return true;
    }
    
    function getOsJson(
        $draw = null,
        $start = null,
        $rowperpage = null,
        $columnIndex = null,
        $columnName = null,
        $columnSortOrder = null,
        $searchValue = null,
        $is_respondido = null,
        $data_ini = null,
        $data_fim = null,
        $id_empresa = null,
        $id_user = null,
        $id_status = null
    ) {

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (uf.nome ilike '%" . $searchValue . "%' or os.descricao ilike '%" . $searchValue . "%' or os.rc ilike '%" . $searchValue . "%' or s.nome ilike '%" . $searchValue . "%') ";
        }

        // if ($columnName == 'tipo_atividade') {
        //     $columnName = '(CASE WHEN a.tipo IS NULL THEN os.tipo_atividade_os ELSE a.tipo END)';
        // };


        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ordem_servico os');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico GROUP BY id_ordem_servico) hso', 'os.id = hso.id_ordem_servico');
        $this->db->join('historico_ordem_servico hos', 'hos.id = hso.id', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        $this->db->where('os.id_empresa', $id_empresa);
        $this->db->where("(s.id = '1' OR s.id = '2')");
        $this->db->where('os.id_empresa', $id_empresa);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;



        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ordem_servico os');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico GROUP BY id_ordem_servico) hso', 'os.id = hso.id_ordem_servico');
        $this->db->join('historico_ordem_servico hos', 'hos.id = hso.id', 'left');
        $this->db->join('usuarios uc', 'uc.id = os.id_chefe', 'left');
        $this->db->join('usuarios uf', 'uf.id = os.id_fiscal', 'left');
        $this->db->join('usuarios uit', 'uit.id = hos.id_usuario', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        $this->db->where('os.id_empresa', $id_empresa);
        $this->db->where("(s.id = '1' OR s.id = '2')");
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        if ($data_ini != null)
            $this->db->where('os.data_cadastro >=', $data_ini);
        if ($data_fim != null)
            $this->db->where('os.data_cadastro <=', $data_fim);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select(
            'os.id, os.descricao, os.data_prazo, os.data_cadastro, os.rc, os.observacao, os.n_documento, os.n_protocolo, 
        uit.id as id_usuario_interacao, uit.nome as nome_usuario_interacao,
        uc.id as id_chefe, uc.nome as nome_chefe, uf.nome as nome_fiscal, s.nome as status, s.cor as status_cor'
        );
        $this->db->from('ordem_servico os ');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico GROUP BY id_ordem_servico) hso', 'os.id = hso.id_ordem_servico');
        $this->db->join('historico_ordem_servico hos', 'hos.id = hso.id', 'left');
        $this->db->join('usuarios uc', 'uc.id = os.id_chefe', 'left');
        $this->db->join('usuarios uf', 'uf.id = os.id_fiscal', 'left');
        $this->db->join('usuarios uit', 'uit.id = hos.id_usuario', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        $this->db->where("(s.id = '1' OR s.id = '2')");
        $this->db->where('os.excluido', 0);

        if ($searchQuery != '')
            $this->db->where($searchQuery);
        if ($data_ini != null)
            $this->db->where('os.data_cadastro >=', $data_ini);
        if ($data_fim != null)
            $this->db->where('os.data_cadastro <=', $data_fim);
        if ($id_empresa != null)
            $this->db->where('os.id_empresa', $id_empresa);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $this->db->group_by('os.id, uit.id, uit.nome, uc.nome, uf.nome, uc.id, s.nome, s.cor');
        $records = $this->db->get()->result();


        $data = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $records
        );

        return $data;
    }


    function getOsInativaJson(
        $draw = null,
        $start = null,
        $rowperpage = null,
        $columnIndex = null,
        $columnName = null,
        $columnSortOrder = null,
        $searchValue = null,
        $is_respondido = null,
        $data_ini = null,
        $data_fim = null,
        $id_empresa = null,
        $id_user = null
    ) {

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (uf.nome ilike '%" . $searchValue . "%' or os.descricao ilike '%" . $searchValue . "%' or os.rc ilike '%" . $searchValue . "%' or s.nome ilike '%" . $searchValue . "%') ";
        }

        // if ($columnName == 'tipo_atividade') {
        //     $columnName = '(CASE WHEN a.tipo IS NULL THEN os.tipo_atividade_os ELSE a.tipo END)';
        // };


        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ordem_servico os');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico GROUP BY id_ordem_servico) hso', 'os.id = hso.id_ordem_servico');
        $this->db->join('historico_ordem_servico hos', 'hos.id = hso.id', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        $this->db->where("(s.id = '3' OR s.id = '4')");
        $this->db->where('os.id_empresa', $id_empresa);
        $records = $this->db->get()->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ordem_servico os');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico GROUP BY id_ordem_servico) hso', 'os.id = hso.id_ordem_servico');
        $this->db->join('historico_ordem_servico hos', 'hos.id = hso.id', 'left');
        $this->db->join('usuarios uf', 'uf.id = os.id_fiscal', 'left');
        $this->db->join('usuarios uit', 'uit.id = hos.id_usuario', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        $this->db->where("(s.id = '3' OR s.id = '4')");
        $this->db->where('os.id_empresa', $id_empresa);
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        if ($data_ini != null)
            $this->db->where('os.data_cadastro >=', $data_ini);
        if ($data_fim != null)
            $this->db->where('os.data_cadastro <=', $data_fim);
        $records = $this->db->get()->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select(
            'os.id, os.descricao, os.data_prazo, os.data_cadastro, os.rc, os.observacao, os.n_documento, os.n_protocolo, 
        uit.id as id_usuario_interacao, uit.nome as nome_usuario_interacao,
        uc.id as id_chefe, uc.nome as nome_chefe, uf.nome as nome_fiscal, s.nome as status, s.cor as status_cor'
        );
        $this->db->from('ordem_servico os ');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico GROUP BY id_ordem_servico) hso', 'os.id = hso.id_ordem_servico');
        $this->db->join('historico_ordem_servico hos', 'hos.id = hso.id', 'left');
        $this->db->join('usuarios uc', 'uc.id = os.id_chefe', 'left');
        $this->db->join('usuarios uf', 'uf.id = os.id_fiscal', 'left');
        $this->db->join('usuarios uit', 'uit.id = hos.id_usuario', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        $this->db->where("(s.id = '3' OR s.id = '4')");

        if ($searchQuery != '')
            $this->db->where($searchQuery);
        if ($data_ini != null)
            $this->db->where('os.data_cadastro >=', $data_ini);
        if ($data_fim != null)
            $this->db->where('os.data_cadastro <=', $data_fim);
        if ($id_empresa != null)
            $this->db->where('os.id_empresa', $id_empresa);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $this->db->group_by('os.id, uit.id, uit.nome, uc.nome, uf.nome, uc.id, s.nome, s.cor');
        $records = $this->db->get()->result();


        $data = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $records
        );

        return $data;
    }

    function getExcluidas(
        $id_usuario = null,
        $data_ini = null,
        $data_fim = null,
        $id_empresa = null
    ) {
        $this->db->select('os.*,
                           a.tipo,
                           a.id as id_atividade,
                           u.nome ,
                           u.id as id_usuario');
        $this->db->from('ordem_servico os');
        $this->db->join('atividade a', 'a.id = os.id_atividade', 'left');
        $this->db->join('usuarios u', 'u.id = os.id_fiscal', 'left');

        if ($id_usuario != null)
            $this->db->where('os.id_fiscal', $id_usuario);
        if ($data_ini != null)
            $this->db->where('os.data_update >=', $data_ini);
        if ($data_fim != null)
            $this->db->where('os.data_update <=', $data_fim);

        $this->db->where('os.id_empresa', $id_empresa);
        $this->db->where('os.excluido', 1);

        $query = $this->db->get();

        if ($query->num_rows() >= 1) {
            return $query->result();
        }
    }

    function getOsJsonFiscal(
        $draw = null,
        $start = null,
        $rowperpage = null,
        $columnIndex = null,
        $columnName = null,
        $columnSortOrder = null,
        $searchValue = null,
        $is_respondido = null,
        $id_user = null,
        $data_ini = null,
        $data_fim = null,
        $id_empresa = null
    ) {

        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (u.nome ilike '%" . $searchValue . "%' or a.tipo ilike '%" . $searchValue . "%' or os.rc ilike '%" . $searchValue . "%' or os.tipo_atividade_os ilike '%" . $searchValue . "%' ) ";
        }

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        $this->db->from('ordem_servico os ');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico GROUP BY id_ordem_servico) hso', 'os.id = hso.id_ordem_servico');
        $this->db->join('historico_ordem_servico hos', 'hos.id = hso.id', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        $this->db->where("(s.id = '1' OR s.id = '2')");
        $this->db->where('os.id_fiscal', $id_user);
        $this->db->where('os.excluido', 0);
        $this->db->where('os.id_empresa', $id_empresa);
        $records = $this->db->get('')->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        $this->db->select('count(*) as allcount');
        $this->db->from('ordem_servico os ');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico GROUP BY id_ordem_servico) hso', 'os.id = hso.id_ordem_servico');
        $this->db->join('historico_ordem_servico hos', 'hos.id = hso.id', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        $this->db->where("(s.id = '1' OR s.id = '2')");
        $this->db->where('os.id_fiscal', $id_user);
        $this->db->where('os.excluido', 0);
        $this->db->where('os.id_empresa', $id_empresa);
        if ($data_ini != null)
            $this->db->where('os.data_prazo >=', $data_ini);
        if ($data_ini != null)
            $this->db->where('os.data_prazo <=', $data_fim);
        $records = $this->db->get('')->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select(
            'os.id, os.descricao, os.data_prazo, os.data_cadastro, os.rc, os.observacao, os.n_documento, os.n_protocolo, 
            uit.id as id_usuario_interacao, uit.nome as nome_usuario_interacao,
            uc.id as id_chefe, uc.nome as nome_chefe, uf.nome as nome_fiscal, s.nome as status, s.cor as status_cor, os.validado'
        );
        $this->db->from('ordem_servico os ');
        $this->db->join('(SELECT MAX(id) as id, id_ordem_servico FROM historico_ordem_servico GROUP BY id_ordem_servico) hso', 'os.id = hso.id_ordem_servico');
        $this->db->join('historico_ordem_servico hos', 'hos.id = hso.id', 'left');
        $this->db->join('usuarios uc', 'uc.id = os.id_chefe', 'left');
        $this->db->join('usuarios uf', 'uf.id = os.id_fiscal', 'left');
        $this->db->join('usuarios uit', 'uit.id = hos.id_usuario', 'left');
        $this->db->join('status s', 's.id = hos.id_status', 'left');
        $this->db->where('os.id_fiscal', $id_user);
        $this->db->where('os.excluido', 0);
        $this->db->where("(s.id = '1' OR s.id = '2')");

        if ($searchQuery != '')
            $this->db->where($searchQuery);
        if ($data_ini != null)
            $this->db->where('os.data_prazo >=', $data_ini);
        if ($data_fim != null)
            $this->db->where('os.data_prazo <=', $data_fim);
        if ($id_empresa != null)
            $this->db->where('os.id_empresa', $id_empresa);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get()->result();


        $data = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $records
        );


        return $data;
    }

    function getOsExcluidoJson(
        $draw = null,
        $start = null,
        $rowperpage = null,
        $columnIndex = null,
        $columnName = null,
        $columnSortOrder = null,
        $searchValue = null,
        $data_ini = null,
        $data_fim = null,
        $id_user = null,
        $id_empresa = null
    ) {
        ## Search 
        $searchQuery = "";
        if ($searchValue != '') {
            $searchQuery = " (u.nome ilike '%" . $searchValue . "%' or a.tipo ilike '%" . $searchValue . "%' or os.rc ilike '%" . $searchValue . "%' or os.tipo_atividade_os ilike '%" . $searchValue . "%') ";
        }

        if ($columnName == 'tipo_atividade') {
            $columnName = '(CASE WHEN a.tipo IS NULL THEN os.tipo_atividade_os ELSE a.tipo END)';
        };

        ## Total number of records without filtering
        $this->db->select('count(*) as allcount');
        ($id_user == null) ?: $this->db->where('ordem_servico.id_fiscal', $id_user);
        $this->db->where('ordem_servico.excluido', 1);
        $this->db->where('ordem_servico.id_empresa', $id_empresa);
        $records = $this->db->get('ordem_servico')->result();
        $totalRecords = $records[0]->allcount;

        ## Total number of record with filtering
        $this->db->select('count(*) as allcount');
        ($id_user == null) ?: $this->db->where('ordem_servico.id_fiscal', $id_user);
        $this->db->where('ordem_servico.excluido', 1);
        $this->db->where('ordem_servico.id_empresa', $id_empresa);
        $this->db->where('ordem_servico.data_update >=', $data_ini);
        $this->db->where('ordem_servico.data_update <=', $data_fim);
        $records = $this->db->get('ordem_servico')->result();
        $totalRecordwithFilter = $records[0]->allcount;

        ## Fetch records
        $this->db->select('os.*,
                           a.tipo,
                           a.id as id_atividade,
                           u.nome as nome_fiscal,
                           u.id as id_usuario');
        $this->db->join('atividade a', 'a.id = os.id_atividade', 'left');
        $this->db->join('usuarios u', 'u.id = os.id_fiscal', 'left');
        $this->db->where('os.id_empresa', $id_empresa);
        $this->db->where('os.excluido', 1);
        if ($searchQuery != '')
            $this->db->where($searchQuery);
        if ($id_user != null)
            $this->db->where('os.id_fiscal', $id_user);
        if ($data_ini != null)
            $this->db->where('os.data_update >=', $data_ini);
        if ($data_fim != null)
            $this->db->where('os.data_update <=', $data_fim);
        $this->db->order_by($columnName, $columnSortOrder);
        $this->db->limit($rowperpage, $start);
        $records = $this->db->get('ordem_servico os')->result();

        $data = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordwithFilter,
            "aaData" => $records
        );

        return $data;
    }
}
