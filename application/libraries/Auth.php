<?php
class Auth{

  private $ci;   

  public function __construct(){
    $this->ci = &get_instance();       
  }

    function check_logged($classe, $metodo){  

    $classe = strtolower($classe);
    $metodo = strtolower($metodo);

    $array = array('classe' => $classe, 'metodo' => $metodo);

    $this->ci->db->where($array);
    $query = $this->ci->db->get('auth_metodos');  
    $result = $query->result();

    // Se este metodo ainda não existir na tabela será cadastrado
    if(count($result) == 0){

      $data = array(
        'classe' => $classe,
        'metodo' => $metodo,
        'apelido' => $classe .  '/' . $metodo,
        'privado' => 1

      );

      $this->ci->db->insert('auth_metodos', $data);
      redirect(base_url(). $classe . '/' . $metodo, 'refresh');
    }


    // Se for privado, verifica o login
    if($result[0]->privado != 0){

      $user = $this->ci->session->userdata('logged_in');

      $id_auth_metodos = $result[0]->id;

      // Se o usuario estiver logado vai verificar se tem permissao na tabela.
      if($user['id'] && $user['id_grupo'] && $user['id_empresa']){

        $array = array(
                  'id_metodo' => $id_auth_metodos, 
                  'id_grupo' => $user['id_grupo'], 
                  'excluido' => 0
                );
        
        $this->ci->db->where($array);
        $query2 = $this->ci->db->get('auth_permissoes');
        $result2 = $query2->result();

        // Se não vier nenhum resultado da consulta, manda para página de
        // usuario sem permissão.
        if(count($result2) == 0){
          $message = array('message_heading' => 'Acesso negado! Você não tem permissão para acessar a página requisitada.','class_result'  => 'red');
          $this->ci->session->set_flashdata('result', $this->ci->parser->parse('template/result_message.php', $message));
          redirect(base_url().'home', 'refresh');
        }
        else
          return true;
  
      }
       
      // caso não esteja logado, redireciona
      redirect(base_url().'login/logout', 'refresh');

    }

    // Escapa da validacão e mostra o método.
    return false;

  }



  /**
  * Método auxiliar para autenticar entradas em menu.
  * Não faz parte do plugin como um todo.
  */


  // function check_menu($classe, $metodo){

  //   $sql = "SELECT SQL_CACHE
  //             count(auth_permissoes.id) as found
  //           FROM
  //             auth_permissoes
  //           INNER JOIN auth_metodos
  //           ON auth_metodos.id = auth_permissoes.id_metodo
  //           WHERE id_usuario = '" . $this->ci->session->userdata('id_usuario') . "'
  //           AND classe = '" . $classe . "'
  //           AND metodo = '" . $metodo . "'";

  //   $query = $this->ci->db->query($sql);  
  //   $result = $query->result();
  //   return $result[0]->found;

  // }

}

?>