<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario extends SO_Controller {

    private $id_user;
    private $id_empresa;

 	function __construct() {
        parent::__construct();
        $this->logado();
        $this->load->model('model_usuario');
        $this->load->model('model_login');
        $this->load->model('model_nivel');

        $this->id_user = $this->session->userdata['logged_in']['id'];
        $this->id_empresa = $this->session->userdata['logged_in']['id_empresa'];
        $this->id_nivel = $this->session->userdata['logged_in']['nivel'];
    }
    
	function index()
	{
        if($this->id_nivel != 1)
        {
            $message = array('message_heading' => 'Você não tem permissão para acessar essa página!', 'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/home'));
        }
        $this->data['niveis'] = $this->getNiveis();
        $this->data['usuarios'] = $this->consultar();


		$this->load->view('/usuario', $this->data);
	}

    function consultar(){
        return $this->model_usuario->getUsers($this->id_empresa, null);
    }

    function getNiveis(){
        return $this->model_usuario->getNiveis();
    }

    function cadastrar(){
      
        $this->form_validation->set_rules('nome', 'Nome', 'trim|required');
        $this->form_validation->set_rules('usuario', 'Usuário', 'trim|required');
        $this->form_validation->set_rules('matricula', 'Matrícula', 'trim|required');

        if($this->form_validation->run() == FALSE) {
            $message = array('message_heading' => validation_errors(),'class_result'  => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/usuario'));
        }

        $this->data['nome'] = mb_strtoupper($this->input->post('nome'));
        $this->data['usuario'] = mb_strtolower($this->input->post('usuario'));
        $this->data['nivel'] = $this->input->post('nivel');
        $this->data['matricula'] = $this->input->post('matricula');
        $this->data['ativo'] = 1;
        $this->data['id_empresa'] = $this->id_empresa;
        $this->data['excluido'] = 0;
        $this->data['data_cadastro'] = date('Y/m/d H:i:s');

        $result = $this->model_usuario->cadastrarUsuario($this->data);
        if($result){
            $this->model_usuario->cadastrarUsuarioEmpresa(array("id_usuario"=>$result,"id_empresa"=> $this->id_empresa, "ativo" => 1));
            $message = array('message_heading' => 'Usuário cadastrado com sucesso!','class_result'  => 'green');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/usuario'));
        }

        $message = array('message_heading' => 'Erro ao cadastrar Usuário!','class_result'  => 'red');
        $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
        redirect(base_url('/usuario'));
}

    function editar(){
        if(!is_numeric($this->uri->segment(3))){
            $message = array('message_heading' => 'Parâmetro inválido!','class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/usuario'));
        }
        
        $id_usuario = $this->uri->segment(3);
        $result = $this->model_usuario->getUser($id_usuario, null, $this->id_empresa);
        if($result){
            $this->data['alterar']['id'] = $result[0]->id;
            $this->data['alterar']['nome'] = $result[0]->nome;
            $this->data['alterar']['usuario'] = $result[0]->usuario;
            $this->data['alterar']['nivel'] = $result[0]->nivel;
            $this->data['alterar']['ativo'] = $result[0]->ativo;
            $this->data['alterar']['matricula'] = $result[0]->matricula;
        }

        $this->data['niveis'] = $this->getNiveis();
        $this->data['usuarios'] = $this->consultar();
        $this->load->view('/usuario', $this->data);
      
    }

    function alterar()
    {
        $this->form_validation->set_rules('id_usuario', 'ID Usuário', 'trim|required|numeric');
        $this->form_validation->set_rules('nivel', 'Nível', 'trim|required|numeric');
        
        if($this->form_validation->run() == FALSE) {
            $message = array('message_heading' => validation_errors(),'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/usuario'));
        }

        $id_usuario = $this->input->post('id_usuario');
        $this->data['nivel'] =  $this->input->post('nivel');
        $this->data['nome']=  $this->input->post('nome');
        $this->data['matricula']=  $this->input->post('matricula');
        $this->data['ativo'] = ($this->input->post('ativo') != null) ? 1 : 0;
        $this->data['data_update'] = date('Y/m/d H:i:s');

        $result = $this->model_usuario->alterarUsuario($id_usuario, $this->id_empresa, $this->data);
        if($result){

            $message = array('message_heading' => 'Usuário editado com sucesso!','class_result' => 'green');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/usuario'));
        }

        $message = array('message_heading' => 'Erro ao editar Usuário!','class_result' => 'red');
        $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
        redirect(base_url('/usuario/editar/'.$id_usuario)); 
    }
    
    function deletar()
    {
        $this->form_validation->set_rules('id_usuario', 'ID Usuário', 'trim|required|numeric');
        if($this->form_validation->run() == FALSE) {
            $message = array('message_heading' => validation_errors(),'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/usuario'));
        }

        $id_usuario = $this->input->post('id_usuario');
        
        if($this->model_usuario->deletarUsuario($id_usuario)){
            $message = array('message_heading' => 'Usuário deletado com sucesso!','class_result' => 'green');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/usuario'));
        }

        $message = array('message_heading' => 'Erro ao deletar Usuário!','class_result' => 'red');
        $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
        redirect(base_url('/usuario'));
    }


    function getUserByParam()
    {
        
    }

    function getUserJson()
    {
        $result = $this->model_usuario->getUser(null, null, $this->id_empresa);
        if($result){
            $data = array();
            foreach ($result as $value) {
                $data[] = array("id" => $value->id, "name" => $value->nome);
            }
            echo json_encode($data);
        }
        return array();
    }
}
