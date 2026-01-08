<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Add_column_tipo_atividade_atividade extends CI_Migration
{

	public function up()
	{
		$fields = array(
			'id_tipo_atividade' => array('type' => 'INT')
		);
		$this->dbforge->add_column('atividade', $fields);
	}

	public function down()
	{
		$this->dbforge->drop_column('atividade', 'id_tipo_atividade');
	}
}
