<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ConsultarOrdem extends SO_Controller
{

    public $id_user;
    private $id_empresa;

    function __construct()
    {
        parent::__construct();
        $this->logado();
        $this->load->model('model_os');
        $this->load->model('model_usuario');
        $this->load->model('model_atividade');
        $this->load->model('model_home');

        $this->id_user = $this->session->userdata['logged_in']['id'];
        $this->nome_usuario = $this->session->userdata['logged_in']['nome'];
        $this->id_nivel = $this->session->userdata['logged_in']['nivel'];
        $this->id_empresa = $this->session->userdata['logged_in']['id_empresa'];
        $this->parametros_empresa = $this->session->userdata['logged_in']['parametros_empresa'];

        $this->data = array();
    }

    function index()
    {
        if ($this->parametros_empresa->os != 1) {
            $message = array('message_heading' => 'Você não tem permissão para acessar essa página!', 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/home'));
        }
        if ($this->id_nivel == 1) {
            $this->load->view('ordem_servico/chefe/consultar_ordem');
        }
        if ($this->id_nivel == 2) {
            $this->load->view('ordem_servico/fiscal/consultar_ordem_fiscal');
        }
    }

    /* parâmetros: $id_atividade, $value = [$data_update, $nome_usuario_update, $motivo_exclusao] */
    function deletarOs()
    {

        $this->form_validation->set_rules('id_os', 'ID Usuário', 'trim|required|numeric');
        if ($this->form_validation->run() == FALSE) {
            $message = array('message_heading' => validation_errors(), 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/consultarordem'));
        }

        $this->id_os = $this->input->post('id_os');

        $this->data['nome_usuario_update'] = $this->nome_usuario;
        $this->data['motivo_exclusao'] = $this->input->post('motivo');
        $this->data['data_update'] = date('Y-M-d H:i:s');

        if ($this->model_os->deletarOs($this->id_os, $this->data)) {
            $message = array('message_heading' => 'Ordem de serviço excluída com sucesso!', 'class_result' => 'green');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/consultarordem'));
        }

        $message = array('message_heading' => 'Erro ao excluir ordem de serviço!', 'class_result' => 'red');
        $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
        redirect(base_url('/consultarordem'));
    }



    /*
    * Responde OS baseado no nível do usuário (A estrutura do array muda dependendo do nível)
    * @input id_ordem_servico, id_empresa, id_user
    *  
    */

    function responderOs()
    {
        $this->db->trans_begin();
        $this->form_validation->set_rules('id_ordem_servico', 'ID Ordem Serviço!', 'trim|required|numeric');
        if ($this->form_validation->run() == FALSE) {
            $message = array('message_heading' => validation_errors(), 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/consultarordem'));
        }

        $id_ordem_servico = $this->input->post('id_ordem_servico');
        $anexoData = array();

        if (file_exists($_FILES["arquivo"]['tmp_name'])) {
            $anexoData = $this->uploadAnexo($id_ordem_servico);
        }

        if (!is_array($anexoData)) {
            $message = array('message_heading' => $anexoData, 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/consultarordem/ver?id=' . $id_ordem_servico . ''));
        }
        if ($this->id_nivel == 1) {
            $dataHistorico = array(
                'id_usuario' => $this->id_user,
                'id_ordem_servico' => $id_ordem_servico,
                'observacao' => $this->input->post('observacao'),
                'anexo' => (isset($anexoData['full_path'])) ? './uploads/empresa_' . $this->id_empresa . '/ordem_servico/os_' . $id_ordem_servico . '/' . $anexoData['file_name'] : null,
                'data_cadastro' => date('Y-m-d H:i:s'),
                'data_update' => date('Y-m-d H:i:s'),
                'id_status' => 1
            );

            $dataOrdemServico = array(
                'n_documento' => $this->input->post('n_documento'),
                'n_protocolo' => $this->input->post('n_protocolo'),
                'rc' => $this->input->post('rc'),
                'is_respondido' => 0,
                'data_update' => date('Y-m-d H:i:s'),
            );
        }
        if ($this->id_nivel == 2) {
            $dataHistorico = array(
                'id_usuario' => $this->id_user,
                'id_ordem_servico' => $id_ordem_servico,
                'observacao' => $this->input->post('obs_fiscal'),
                'anexo' => (isset($anexoData['full_path'])) ? './uploads/empresa_' . $this->id_empresa . '/ordem_servico/os_' . $id_ordem_servico . '/' . $anexoData['file_name'] : null,
                'data_cadastro' => date('Y-m-d H:i:s'),
                'data_update' => date('Y-m-d H:i:s'),
                'id_status' => 2
            );

            $dataOrdemServico = array(
                'n_documento' => $this->input->post('n_documento'),
                'n_protocolo' => $this->input->post('n_protocolo'),
                'rc' => $this->input->post('rc'),
                'is_respondido' => 1,
                'data_update' => date('Y-m-d H:i:s'),
            );
        }

        $result = $this->model_os->responderOs($id_ordem_servico, $dataHistorico, $dataOrdemServico);

        if (!$result) {
            $message = array('message_heading' => 'Erro ao responder ordem de serviço!', 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/consultarordem/ver?id=' . $id_ordem_servico . ''));
        }
        $message = array('message_heading' => 'Ordem de serviço respondida com sucesso!', 'class_result' => 'green');
        $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
        $this->db->trans_commit();
        redirect(base_url('/consultarordem'));
    }

    private function uploadAnexo($id_os)
    {
        $config['upload_path'] = './uploads/empresa_' . $this->id_empresa . '/ordem_servico/os_' . $id_os . '/';
        $config['allowed_types'] = 'pdf|png|jpg|jpeg';
        $config['file_name'] = date('YmdHis') . '_' . $_FILES["arquivo"]['name'];
        $config['max_size'] = 10000000;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0777, TRUE);
        }

        if (!$this->upload->do_upload('arquivo')) {
            rmdir($config['upload_path']);
            return  $this->upload->display_errors();
        }
        return $this->upload->data();
    }

    function getOsJson()
    {
        $postData = $this->input->post();
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        $is_respondido = $postData['is_respondido']; // Filtro que define se é a tabela de respondidos ou não respondidos
        $data = array();
        $data_ini = (($postData['data_ini'])) ? date('Y-m-d H:i:s', strtotime($postData['data_ini'])) : null;
        $data_fim = (($postData['data_fim'])) ? date('Y-m-d 23:59:59', strtotime($postData['data_fim'])) : null;
        $query = $this->model_os->getOsJson($draw, $start, $rowperpage, $columnIndex, $columnName, $columnSortOrder, $searchValue, $is_respondido, $data_ini, $data_fim, $this->id_empresa, $this->id_user);

        foreach ($query["aaData"] as $key => $value) {

            $data[] = array(
                'id' => $value->id,
                'descricao' => $value->descricao,
                'data_cadastro' => date('d/m/Y', strtotime($value->data_cadastro)),
                'data_prazo' => date('d/m/Y', strtotime($value->data_prazo)),
                'nome_fiscal' => $value->nome_fiscal,
                'observacao' => ($value->observacao) ? $value->observacao : '--',
                'rc' => ($value->rc) ? $value->rc : '--',
                'status' => ($value->status) ? '<span style="color:' . $value->status_cor . '">' . $value->status . '</span>' : '--',
                'opcoes' => '
                    <a class="btn btn-primary waves-effect"
                    href="' . base_url() . 'consultarordem/ver?id=' . $value->id . '" target="_blank">
                    <i class="material-icons">visibility</i>
                    </a>
                    <a class="btn btn-warning waves-effect"
                    href="' . base_url() . 'relatorios/gerarPdfOs?id=' . $value->id . '" target="_blank">
                    <i class="material-icons">print</i>
                    </a>
                    <button type="button" class="btn btn-danger waves-effect open-exclusao"
                    data-toggle="modal" data-target="#modal_exclusao" data-id="' . $value->id . '">
                    <i class="material-icons">delete</i>
                    </button>',
            );
        }

        ## Response
        $response = array(
            "draw" => intval($query["draw"]),
            "iTotalRecords" => $query["iTotalRecords"],
            "iTotalDisplayRecords" => $query["iTotalDisplayRecords"],
            "aaData" => $data,
            "data_ini" => $data_ini,
            "data_fim" => $data_fim
        );

        echo json_encode($response);
    }
    function getOsInativaJson()
    {
        $postData = $this->input->post();
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        $is_respondido = $postData['is_respondido']; // Filtro que define se é a tabela de respondidos ou não respondidos
        $data = array();
        $data_ini = (($postData['data_ini'])) ? date('Y-m-d H:i:s', strtotime($postData['data_ini'])) : null;
        $data_fim = (($postData['data_fim'])) ? date('Y-m-d 23:59:59', strtotime($postData['data_fim'])) : null;
        $query = $this->model_os->getOsInativaJson($draw, $start, $rowperpage, $columnIndex, $columnName, $columnSortOrder, $searchValue, $is_respondido, $data_ini, $data_fim, $this->id_empresa, $this->id_user);
        foreach ($query["aaData"] as $key => $value) {
            $data[] = array(
                'id' => $value->id,
                'descricao' => $value->descricao,
                'data_cadastro' => date('d/m/Y', strtotime($value->data_cadastro)),
                'data_prazo' => date('d/m/Y', strtotime($value->data_prazo)),
                'nome_fiscal' => $value->nome_fiscal,
                'observacao' => ($value->observacao) ? $value->observacao : '--',
                'rc' => ($value->rc) ? $value->rc : '--',
                'status' => ($value->status) ? '<span style="color:' . $value->status_cor . '">' . $value->status . '</span>' : '--',
                'opcoes' => '
                    <a class="btn btn-primary waves-effect"
                    href="' . base_url() . 'consultarordem/ver?id=' . $value->id . '" target="_blank">
                    <i class="material-icons">visibility</i>
                    </a>
                    <a class="btn btn-warning waves-effect"
                    href="' . base_url() . 'relatorios/gerarPdfOs?id=' . $value->id . '" target="_blank">
                    <i class="material-icons">print</i>
                    </button>',

            );
        }

        ## Response
        $response = array(
            "draw" => intval($query["draw"]),
            "iTotalRecords" => $query["iTotalRecords"],
            "iTotalDisplayRecords" => $query["iTotalDisplayRecords"],
            "aaData" => $data,
            "data_ini" => $data_ini,
            "data_fim" => $data_fim
        );

        echo json_encode($response);
    }





    function getOsJsonFiscal()
    {
        $postData = $this->input->post();
        $draw = $postData['draw'];
        $start = $postData['start'];
        $rowperpage = $postData['length']; // Rows display per page
        $columnIndex = $postData['order'][0]['column']; // Column index
        $columnName = $postData['columns'][$columnIndex]['data']; // Column name
        $columnSortOrder = $postData['order'][0]['dir']; // asc or desc
        $searchValue = $postData['search']['value']; // Search value
        $is_respondido = $postData['is_respondido']; // Filtro que define se é a tabela de respondidos ou não respondidos
        $id_user = $this->id_user;
        $data = array();
        $today = date('Y-m-d H:i:s');
        $data_ini = (($postData['data_ini'])) ? date('Y-m-d H:i:s', strtotime($postData['data_ini'])) : null;
        $data_fim = (($postData['data_fim'])) ? date('Y-m-d 23:59:59', strtotime($postData['data_fim'])) : null;
        $query = $this->model_os->getOsJsonFiscal($draw, $start, $rowperpage, $columnIndex, $columnName, $columnSortOrder, $searchValue, $is_respondido, $id_user, $data_ini, $data_fim, $this->id_empresa);
        $colorTimer = 'black';
        foreach ($query["aaData"] as $key => $value) {
            $var = (new DateTime($today))->diff(new DateTime($value->data_prazo))->format("%r%a");
            if ($var > 5) {
                $colorTimer = 'olive';
            }
            if ($var  >= 2 && $var <= 5) {
                $colorTimer = 'darkorange';
            }
            if ($var <  2) {
                $colorTimer = 'darkred';
            }
            $data[] = array(
                'id' => '<span style=" color:' . $colorTimer . '">' . $value->id . '</span>',
                'descricao' => ($value->descricao) ? '<span style=" color:' . $colorTimer . '">' . $value->descricao . '</span>' : $value->descricao,
                'data_cadastro' => '<span style=" color:' . $colorTimer . '">' . date('d/m/Y', strtotime($value->data_cadastro)) . '</span>',
                'data_prazo' => '<span style=" color:' . $colorTimer . '">' . date('d/m/Y', strtotime($value->data_prazo)) . '</span>',
                'obs_chefe' => $value->observacao,
                'rc' => ($value->rc) ? $value->rc : '--',
                'validado' => ($value->validado == 1) ? '<td style="text-align:center">
                            <span class="badge bg-green">
                                <i class="material-icons">done</i>
                            </span>' : '<td style="text-align:center">
                            <span class="badge bg-gray">
                                <i class="material-icons">close</i>
                            </span>',
                'status' => ($value->status) ? '<span style="color:' . $value->status_cor . '">' . $value->status . '</span>' : '--',
                'opcoes' => '
                    <a class="btn btn-primary waves-effect"
                    href="' . base_url() . 'consultarordem/ver?id=' . $value->id . '" target="_blank">
                    <i class="material-icons">visibility</i>
                    </a>
                    <a class="btn btn-warning waves-effect"
                    href="' . base_url() . 'relatorios/gerarPdfOs?id=' . $value->id . '" target="_blank">
                    <i class="material-icons">print</i>
                    </a>'
            );
        }

        ## Response
        $response = array(
            "draw" => intval($query["draw"]),
            "iTotalRecords" => $query["iTotalRecords"],
            "iTotalDisplayRecords" => $query["iTotalDisplayRecords"],
            "aaData" => $data,
            "data_ini" => $data_ini,
            "data_fim" => $data_fim
        );

        echo json_encode($response);
    }


    public function ver()
    {
        $id_os = $this->input->get('id');
        $this->data['ordem_servico'] = $this->model_os->getOrdemServico($id_os);
        $this->data['ordem_servico']['historico'] = $this->model_os->getHistoricoOrdemServico($id_os);
        $this->data["id_fiscal"] = $this->model_usuario->getUsers($this->id_empresa, 2);

        if ($this->id_nivel == 1) {
            $this->load->view('ordem_servico/chefe/ver_ordem', $this->data);
        }
        if ($this->id_nivel == 2) {
            $this->load->view('ordem_servico/fiscal/ver_ordem', $this->data);
        }
    }
}
