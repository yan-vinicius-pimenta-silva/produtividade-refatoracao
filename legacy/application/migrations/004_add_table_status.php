<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_table_status extends CI_Migration
{

        public function up()
        {
                $this->dbforge->add_field(array(
                        'id' => array(
                                'type' => 'INT',
                                'constraint' => 5,
                                'unsigned' => TRUE,
                                'auto_increment' => TRUE
                        ),
                        'nome' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '100',
                                'null' => TRUE,
                        ),
                        'cor' => array(
                                'type' => 'VARCHAR',
                                'constraint' => '100',
                                'null' => TRUE,
                        )
                ));
                $this->dbforge->add_key('id', TRUE);
                $this->dbforge->create_table('status');
                $sql = "INSERT INTO status VALUES (1, 'Aguardando Fiscal', '#ffc107'), (2, 'Aguardando Chefe', '#17a2b8'), (3, 'Finalizado', '#3ca616'), (4, 'Cancelado', '#dc3545') ";
                $this->db->query($sql);
        }

        public function down()
        {
                $this->dbforge->drop_table('status');
        }
}
