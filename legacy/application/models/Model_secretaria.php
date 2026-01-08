<?php
class Model_Secretaria extends CI_Model {

    function getSecretarias($id_empresa){

        $this->db->select('A.id, A.nome, A.telefone, B.nome as secretario');
        $this->db->from('secretaria A');
        $this->db->join('usuarios B', 'A.secretario = B.id', 'left');
        $this->db->where('A.id_empresa', $id_empresa);
        $this->db->where('A.excluido', 0);
        $this->db->order_by("A.nome", "ASC");
        
        $query = $this->db->get();
    
        if($query->num_rows() >= 1) {
            return $query->result();
        }
        return false;
    }


    function getSecretaria($id_secretaria, $id_empresa){
        $this->db->select("*");
        $this->db->from("secretaria");
        $this->db->where('id', $id_secretaria);
        $this->db->where('id_empresa', $id_empresa);
        $this->db->where('excluido', 0);

        $query = $this->db->get();
        if($query->num_rows() >= 1) {
            return $query->result();
        }
        return false;
    }
    

    function cadastrarSecretaria($data){
        if($this->db->insert('secretaria', $data)){
            $last_id = $this->db->insert_id();
            $this->logger->logAction('secretaria create', (array) $data);
            return $last_id;
        }

        return false;
    }


    function alterarSecretaria($id_secretaria, $id_empresa, $data)
    {   
        $this->db->where('id', $id_secretaria);
        $this->db->where('id_empresa', $id_empresa);
        if($this->db->update('secretaria', $data)){
            $this->logger->logAction('secretaria update(ID: '.$id_secretaria.')', (array) $data);
            return true;
        }

        return false;
    }


    function validarExclusao($id_secretaria, $id_empresa){
        $this->db->select('sec.nome');
        $this->db->from('secretaria sec');

        $where = "sec.id NOT IN(select id_secretaria from departamento WHERE excluido = 0 AND id_empresa = ".$id_empresa.")";
        $this->db->where($where);
        $this->db->where('sec.excluido', 0);
        $this->db->where('sec.id', $id_secretaria);
        $this->db->where('sec.id_empresa', $id_empresa);
        $this->db->order_by("sec.nome", "ASC");
        
        $query = $this->db->get();
    
        if($query->num_rows() >= 1) {
            return $query->result();
        }
        return false;

        return true;
    }


    function deletarSecretaria($id_secretaria){
        $data = array(
            'excluido' => 1
        );

        $this->db->where('id', $id_secretaria);
        if($this->db->update('secretaria', $data)){
            $this->logger->logAction('secretaria delete(ID: '.$id_secretaria.')', (array) $data);
            return true;
        }

        return false;
    }

}

?>