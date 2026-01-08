<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_table_historico_ordem_servico extends CI_Migration
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
         'id_ordem_servico' => array(
            'type' => 'INT',
            'constraint' => '5',
         ),
         'id_usuario' => array(
            'type' => 'INT',
            'constraint' => '5',
         ),
         'id_status' => array(
            'type' => 'INT',
            'constraint' => '5',
         ),
         'descricao' => array(
            'type' => 'VARCHAR',
            'constraint' => '500',
            'null' => TRUE,
         ),
         'observacao' => array(
            'type' => 'VARCHAR',
            'constraint' => '500',
            'null' => TRUE,
         ),
         'anexo' => array(
            'type' => 'VARCHAR',
            'constraint' => '500',
            'null' => TRUE,
         ),
         'data_cadastro' => array(
            'type' => 'timestamp',
            'default' => 'NOW()'
         ),
         'data_update' => array(
            'type' => 'timestamp',
            'default' => 'NOW()'
         ),
      ));
      $this->dbforge->add_key('id', TRUE);
      $this->dbforge->create_table('historico_ordem_servico');
   }

   public function down()
   {
      $this->dbforge->drop_table('historico_ordem_servico');
   }
}
