<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends SO_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model('model_admin', '', TRUE);
        $this->data = array();
    }
    public function index() {
        $this->data['niveis'] = $this->model_admin->getNiveis();
        $this->data['usuarios'] = $this->model_admin->getUsers();
        $this->data['empresas'] = $this->model_admin->getEmpresas();
       
        $this->load->view('admin/admin', $this->data);
    }
    public function editar(){
        if(!is_numeric($this->uri->segment(3))){
            $message = array('message_heading' => 'Parâmetro inválido!','class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/admin'));
        }
        
        $id_usuario = $this->uri->segment(3);
        $result = $this->model_admin->getUser($id_usuario, null);
        if($result) {
            $this->data['alterar']['id'] = $result[0]->id;
            $this->data['alterar']['nome'] = $result[0]->nome;
            $this->data['alterar']['usuario'] = $result[0]->usuario;
            $this->data['alterar']['nivel'] = $result[0]->nivel;
            $this->data['alterar']['ativo'] = $result[0]->ativo;
            $this->data['alterar']['matricula'] = $result[0]->matricula;
            $this->data['alterar']['id_empresa'] = $result[0]->id_empresa;
        }

        $this->data['niveis'] = $this->model_admin->getNiveis();
        $this->data['usuarios'] = $this->model_admin->getUsers();
        $this->data['empresas'] = $this->model_admin->getEmpresas();

        $this->load->view('admin/admin', $this->data);
      
    }
    public function cadastrar(){
        if(!is_numeric($this->uri->segment(3))){
            $message = array('message_heading' => 'Parâmetro inválido!','class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/admin'));
        }
        
        $id_usuario = $this->uri->segment(3);
        $result = $this->model_admin->getUser($id_usuario, null);
        if($result) {
            $this->data['alterar']['id'] = $result[0]->id;
            $this->data['alterar']['nome'] = $result[0]->nome;
            $this->data['alterar']['usuario'] = $result[0]->usuario;
            $this->data['alterar']['nivel'] = $result[0]->nivel;
            $this->data['alterar']['ativo'] = $result[0]->ativo;
            $this->data['alterar']['matricula'] = $result[0]->matricula;
            $this->data['alterar']['id_empresa'] = $result[0]->id_empresa;
        }
       
        $this->data['niveis'] = $this->model_admin->getNiveis();
        $this->data['usuarios'] = $this->model_admin->getUsers();
        $this->data['empresas'] = $this->model_admin->getEmpresas();

        $this->load->view('admin/admin', $this->data);
      
    }

    function alterar()
    {
        $this->form_validation->set_rules('id_usuario', 'ID Usuário', 'trim|required|numeric');
        $this->form_validation->set_rules('nivel', 'Nível', 'trim|required|numeric');
        
        if($this->form_validation->run() == FALSE) {
            $message = array('message_heading' => validation_errors(),'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/admin'));
        }

        $id_usuario = $this->input->post('id_usuario');
        $this->data['nivel'] =  $this->input->post('nivel');
        $this->data['nome']=  $this->input->post('nome');
        $this->data['matricula']=  $this->input->post('matricula');
        $this->data['ativo'] = ($this->input->post('ativo') != null) ? 1 : 0;
        $this->data['data_update'] = date('Y/m/d H:i:s');
        $this->empresa['id_empresa'] = $this->input->post('id_empresa');
        $this->empresa['ativo'] = 1;

        $result = $this->model_admin->alterarUsuario($id_usuario, null, $this->data);
        if($result){
            $this->_checkEmpresa( $id_usuario,  $this->empresa['id_empresa']);
            $message = array('message_heading' => 'Usuário editado com sucesso!','class_result' => 'green');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/admin/editar/'.$id_usuario));
        }

        $message = array('message_heading' => 'Erro ao editar Usuário!','class_result' => 'red');
        $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
        redirect(base_url('/admin/editar/'.$id_usuario)); 
    }
    
    function deletar()
    {
        $this->form_validation->set_rules('id_usuario', 'ID Usuário', 'trim|required|numeric');
        if($this->form_validation->run() == FALSE) {
            $message = array('message_heading' => validation_errors(),'class_result' => 'red');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/admin'));
        }

        $id_usuario = $this->input->post('id_usuario');
        
        if($this->model_admin->deletarUsuario($id_usuario)){
            $message = array('message_heading' => 'Usuário deletado com sucesso!','class_result' => 'green');
            $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
            redirect(base_url('/admin'));
        }

        $message = array('message_heading' => 'Erro ao deletar Usuário!','class_result' => 'red');
        $this->session->set_flashdata('result', $this->parser->parse('template/result_message.php', $message));
        redirect(base_url('/admin'));
    }

    private function _checkEmpresa($id_usuario, $id_empresa) {
        $empresa_usuario = $this->model_admin->getEmpresasUsuario($id_usuario, $id_empresa);
        $arrayOfIdEmpresa = array();
     
        foreach($empresa_usuario as $index=>$userEmp) {
            $arrayOfIdEmpresa[] = $userEmp->id_empresa;
        }
        if(in_array($id_empresa,$arrayOfIdEmpresa)) {
            foreach($empresa_usuario as $index=>$userEmp) {
                if ($userEmp->id_empresa != $id_empresa && $userEmp->ativo == 1) {
                    $this->model_admin->alterarUsuarioEmpresa($id_usuario, $userEmp->id_empresa, $data = array("ativo" => 0));
                }
                if($userEmp->id_empresa == $id_empresa && $userEmp->ativo == 0) {
                    $this->model_admin->alterarUsuarioEmpresa($id_usuario, $id_empresa, $data = array("ativo" => 1));
                }
            }
        }
        else {
            foreach($empresa_usuario as $index=>$userEmp) {
                if ($userEmp->id_empresa != $id_empresa && $userEmp->ativo == 1) {
                    $this->model_admin->alterarUsuarioEmpresa($id_usuario, $userEmp->id_empresa, $data = array("ativo" => 0));
                }
            }
            $this->model_admin->cadastrarUsuarioEmpresa(array("id_usuario" => $id_usuario, 
            "id_empresa"=>$id_empresa,
            "ativo" => 1));
        }
    }
}
?>