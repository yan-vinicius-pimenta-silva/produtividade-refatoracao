<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_table_ordem_servico extends CI_Migration
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
         'id_atividade' => array(
            'type' => 'INT',
            'constraint' => '5',
            'null' => TRUE,
         ),
         'id_chefe' => array(
            'type' => 'INT',
            'constraint' => '5',
            'null' => TRUE,
         ),
         'id_fiscal' => array(
            'type' => 'INT',
            'constraint' => '5',
            'null' => TRUE,
         ),
         'id_empresa' => array(
            'type' => 'INT',
            'constraint' => '5',
            'null' => TRUE,
         ),
         'n_documento' => array(
            'type' => 'VARCHAR',
            'constraint' => '100',
            'null' => TRUE,
         ),
         'n_protocolo' => array(
            'type' => 'VARCHAR',
            'constraint' => '100',
            'null' => TRUE,
         ),
         'rc' => array(
            'type' => 'VARCHAR',
            'constraint' => '100',
            'null' => TRUE,
         ),

         'descricao' => array(
            'type' => 'TEXT',
            'null' => TRUE,
         ),
         'observacao' => array(
            'type' => 'TEXT',
            'null' => TRUE,
         ),
         'motivo_exclusao' => array(
            'type' => 'VARCHAR',
            'constraint' => '500',
            'null' => TRUE,
         ),
         'excluido' => array(
            'type' => 'INT',
            'constraint' => '1',
            'null' => FALSE,
            'default' => 0,
         ),
         'validado' => array(
            'type' => 'INT',
            'constraint' => '1',
            'null' => TRUE,
            'default' => 0,
         ),
         'is_respondido' => array(
            'type' => 'INT',
            'constraint' => '1',
            'null' => TRUE,
            'default' => 0,
         ),
         'data_cadastro' => array(
            'type' => 'timestamp',
            'default' => 'NOW()'
         ),
         'data_update' => array(
            'type' => 'timestamp',
            'default' => 'NOW()'
         ),
         'data_prazo' => array(
            'type' => 'timestamp',
            'null' => TRUE,
         ),
         'data_conclusao' => array(
            'type' => 'timestamp',
            'null' => TRUE,
         ),

      ));
      $this->dbforge->add_key('id', TRUE);
      $this->dbforge->create_table('ordem_servico');
   }

   public function down()
   {
      $this->dbforge->drop_table('ordem_servico');
   }
}
