<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_usuario_empresa extends CI_Migration {

        public function up()
        {
                $this->dbforge->add_field(array(
                        'id' => array(
                                'type' => 'INT',
                                'constraint' => 5,
                                'unsigned' => TRUE,
                                'auto_increment' => TRUE
                        ),
                        'id_usuario' => array(
                            'type' => 'INT',
                            'constraint' => '5',
                        ),
                        'id_empresa' => array(
                            'type' => 'INT',
                            'constraint' => '5',
                        ),
                        'ativo' => array(
                                'type' => 'INT',
                                'constraint' => '5',
                        ),
                ));
                $this->dbforge->add_key('id', TRUE);
                $this->dbforge->create_table('usuario_empresa');
        }

        public function down()
        {
                $this->dbforge->drop_table('usuario_empresa');
        }
}
?>