<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GerarOrdem extends SO_Controller
{

    public $id_user;
    public $id_empresa;

    function __construct()
    {
        parent::__construct();
        $this->logado();
        $this->load->model('model_parametros');
        $this->load->model('model_os');
        $this->load->model('model_atividade');
        $this->load->model('model_usuario');

        $this->id_user = $this->session->userdata['logged_in']['id'];
        $this->user_name = $this->session->userdata['logged_in']['nome'];
        $this->id_nivel = $this->session->userdata['logged_in']['nivel'];
        $this->id_empresa = $this->session->userdata['logged_in']['id_empresa'];
        $this->parametros_empresa = $this->session->userdata['logged_in']['parametros_empresa'];
        $this->data = array();
    }

    function index()
    {
        if ($this->parametros_empresa->os != 1 || $this->id_nivel != 1) {
            $message = array('message_heading' => 'Você não tem permissão para acessar essa página!', 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/home'));
        }
        $this->data["id_fiscal"] = $this->model_usuario->getUsers($this->id_empresa, 2);
        $this->load->view('ordem_servico/chefe/gerar_ordem', $this->data);
    }

    function cadastrar()
    {
        // Validação para os campos Tipo e Nome do fiscal
        $this->form_validation->set_rules('descricao', 'Descrição da Atividade', 'trim|required');
        $this->form_validation->set_rules('id_fiscal', 'Nome do Fiscal', 'trim|required');
        $this->db->trans_begin();
        $this->data["id_fiscal"] = $this->model_usuario->getUsers($this->id_empresa, 2);
        if ($this->form_validation->run() == FALSE) {
            $this->data['form_error'] = array('message_heading' => validation_errors(), 'class_result'  => 'red');
            return $this->load->view('ordem_servico/chefe/gerar_ordem', $this->data);
        }
        $uploadedData = array();
        $this->data['descricao'] = $this->input->post('descricao');
        $this->data['id_fiscal'] = $this->input->post('id_fiscal');
        $this->data['data_cadastro'] = date('Y-m-d H:i:s');
        $this->data['rc'] = $this->input->post('rc');


        $this->data['data_prazo'] = $this->_setDefaultPrazo($this->input->post('data_prazo'), "+15 days");
        $this->data['observacao'] = $this->input->post('observacao');
        $this->data['id_chefe'] = $this->id_user;
        $this->data['data_update'] = date('Y-m-d H:i:s');
        $this->data['id_empresa'] = $this->id_empresa;

        $id_os = $this->model_os->cadastrarOs($this->data);

        if ($id_os && file_exists($_FILES["arquivo"]['tmp_name'])) {
            $uploadedData = $this->uploadAnexo($id_os);
        }
        if (!is_array($uploadedData)) {
            $this->data['form_error'] = array('message_heading' => $uploadedData, 'class_result'  => 'red');
            $this->data["id_fiscal"] = $this->model_usuario->getUsers($this->id_empresa, 2);
            return $this->load->view('ordem_servico/chefe/gerar_ordem', $this->data);
        }

        if ($id_os && is_array($uploadedData)) {
            $this->model_os->updateAnexoHistoricoOrdemServico($id_os, $uploadedData);
        }
        $this->db->trans_commit();
        $this->data["id_fiscal"] = $this->model_usuario->getUsers($this->id_empresa, 2);
        $this->data['form_error'] = array('message_heading' => 'Atividade cadastrada com sucesso!', 'class_result' => 'green');
        return $this->load->view('ordem_servico/chefe/gerar_ordem', $this->data);
    }

    public function cancelarOS()
    {
        $id_ordem_servico = $this->input->post('id_ordem_servico');
        $data['motivo_exclusao'] = $this->input->post('motivo');
        $this->form_validation->set_rules('motivo', 'Motivo', 'trim|required');
        $this->form_validation->set_rules('id_ordem_servico', 'Ordem de Serviço', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $message = array('message_heading' => validation_errors(), 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/consultarordem/ver?id=' . $id_ordem_servico . ''));
        }
        $this->db->trans_begin();
        $cancelar = $this->model_os->deletarOs($id_ordem_servico, $data);
        if (!$cancelar) {
            $message = array('message_heading' => 'Não foi possível cancelar a Ordem de Serviço!', 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/consultarordem/ver?id=' . $id_ordem_servico . ''));
        }
        $this->db->trans_commit();
        $message = array('message_heading' => 'Ordem de serviço finalizada com sucesso!', 'class_result' => 'green');
        $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
        redirect(base_url('/consultarordem'));
    }
    public function finalizarOS()
    {
        $id_ordem_servico = $this->input->post('id_ordem_servico');
        $data['id_atividade'] = $this->input->post('atividade');
        $data['data_conclusao'] = $this->input->post('data_conclusao');
        $this->form_validation->set_rules('atividade', 'Atividade', 'trim|required');
        $this->form_validation->set_rules('id_ordem_servico', 'Ordem de Serviço', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $message = array('message_heading' => validation_errors(), 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/consultarordem/ver?id=' . $id_ordem_servico . ''));
        }
        $this->db->trans_begin();
        $data['validado'] = 1;
        $data['data_update'] = date('Y-m-d H:i:s');
        if (!$this->validar($id_ordem_servico, $data, $this->id_empresa)) {
            $message = array('message_heading' => 'Não foi possível finalizar a Ordem de Serviço!', 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/consultarordem/ver?id=' . $id_ordem_servico . ''));
        }
        $this->db->trans_commit();
        $message = array('message_heading' => 'Ordem de serviço finalizada com sucesso!', 'class_result' => 'green');
        $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
        redirect(base_url('/consultarordem'));
    }
    public function editar()
    {
        $id_ordem_servico = $this->input->post('id_os');
        $dataUpdated = array(
            'descricao' => $this->input->post('descricao'),
            'rc' => $this->input->post('rc'),
            'data_prazo' => $this->_setDefaultPrazo($this->input->post('data_prazo'), "+15 days"),
            'data_update' => date('Y-m-d'),
            'observacao' => $this->input->post('observacao')
        );
        $this->db->trans_begin();
        $isUpdated = $this->model_os->updateOrdemServico($id_ordem_servico, $dataUpdated);
        if (!$isUpdated) {
            $message = array('message_heading' => 'Não foi possível editar a Ordem de Serviço!', 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/consultarordem/ver?id=' . $id_ordem_servico . ''));
        }
        $this->db->trans_commit();
        $message = array('message_heading' => 'Ordem de serviço finalizada com sucesso!', 'class_result' => 'green');
        $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
        redirect(base_url('/consultarordem/ver?id=' . $id_ordem_servico . ''));
    }
    /*
    * Recebe dados para validar a ordem de serviço na finalização
    * @int id_ordem_servico, @array data, @int id_empresa
    * 
    *  return boolean
    */
    private function validar($id_ordem_servico, $data, $id_empresa)
    {
        $validar = $this->model_os->validarOs($id_ordem_servico, $data, $id_empresa);
        if (!$validar) {
            return  false;
        }
        return true;
    }

    /*
    * Recebe chamadas da view em ajax e retorna o html do modal
    * @input null
    * 
    *  return HTMl
    */
    public function callModalCancelar()
    {
        return $this->load->view('ordem_servico/modal/modal_cancelar');
    }

    /*
    * Recebe chamadas da view em ajax e retorna o html do modal
    * @input null
    * 
    *  return HTMl
    */
    public function callModalFinalizar()
    {
        $this->data['atividades'] = $this->model_atividade->getAtividadesOs($this->id_empresa);
        return $this->load->view('ordem_servico/modal/modal_finalizar', $this->data);
    }

    /*
    * Recebe chamadas da view em ajax e retorna o html do modal
    * @input null
    * 
    *  return HTMl
    */
    public function callModalResponder()
    {
        if ($this->id_nivel == 1) {
            return $this->load->view('ordem_servico/modal/modal_responder', $this->data);
        }
        if ($this->id_nivel == 2) {
            return $this->load->view('ordem_servico/modal/modal_responder_fiscal', $this->data);
        }
    }

    /*
    * Define data_prazo padrão caso o usuário não defina.
    *  @input $data_prazo, $default data
    *  return date
    */
    private function _setDefaultPrazo($data_prazo, $default_data)
    {
        if ($data_prazo == '') {
            return date('Y-m-d', strtotime(str_replace('/', '-', $data_prazo) . $default_data));
        } else {
            return date('Y-m-d', strtotime(str_replace('/', '-', $data_prazo)));
        }
    }

    /*
    * Faz o upload do anexo pelo fiscal chefe
    *  @input $result
    *  return result
    */

    private function uploadAnexo($result)
    {
        $config['upload_path'] = './uploads/empresa_' . $this->id_empresa . '/ordem_servico/os_' . $result . '/anexo/';
        $config['allowed_types'] = 'pdf|png|jpg|jpeg';
        $config['file_name'] = date('YmdHis') . '_' . preg_replace("/[^A-Za-z0-9\-_\.]/", "", $_FILES["arquivo"]['name']);
        $config['max_size'] = 10000000;
        $config['encrypt_name'] = TRUE;

        $this->load->library('upload');
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
}
