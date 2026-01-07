<?php
class Model_Nivel extends CI_Model {

    function getNiveis(){

        $this->db->select('*');
        $this->db->from('nivel');
        $this->db->order_by("nome", "ASC");
        
        $query = $this->db->get();
    
        if($query->num_rows() >= 1) {
            return $query->result();
        }
        return false;
    }

}

?>