<?php
class Model_Usuario extends CI_Model
{

	function getUsers($id_empresa, $nivel = null)
	{

		$this->db->select('usu.*, n.nome as nivel');
		$this->db->from('usuarios usu');
		$this->db->join('nivel n', 'n.id = usu.nivel');
		$this->db->join('usuario_empresa usuemp', 'usu.id = usuemp.id_usuario', 'left');
		$this->db->where('usuemp.id_empresa', $id_empresa);
		$this->db->where('usu.excluido', 0);

		if ($nivel != null)
			$this->db->where_in('usu.nivel', $nivel);

		$this->db->order_by("usu.nome", "ASC");

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return array();
	}

	function getPermissoesByUser($id_usuario, $id_empresa)
	{

		$this->db->select('auth.id_metodo');
		$this->db->from('auth_permissoes auth');
		$this->db->join('usuarios usu', 'usu.id_grupo = auth.id_grupo');
		$this->db->where('usu.id', $id_usuario);
		$this->db->where('auth.id_empresa', $id_empresa);
		$this->db->where('auth.excluido', 0);

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			foreach ($query->result() as $row) {
				$permissoes[] = $row->id_metodo;
			}

			return $permissoes;
		}
		return array();
	}


	function getUser($id_usuario, $nome_usuario, $id_empresa)
	{
		$this->db->select("usu.*");
		$this->db->from("usuarios usu");
		$this->db->join('usuario_empresa usuemp', 'usu.id = usuemp.id_usuario', 'left');
		if ($id_usuario)
			$this->db->where('usu.id', $id_usuario);
		if ($nome_usuario)
			$this->db->like('usu.nome', $nome_usuario);
		$this->db->where('usuemp.id_empresa', $id_empresa);
		$this->db->where('usu.excluido', 0);

		$query = $this->db->get();
		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}

	function cadastrarUsuario($data)
	{
		if ($this->db->insert('usuarios', $data)) {
			$last_id = $this->db->insert_id();
			$this->logger->logAction('user create', (array) $data);
			return $last_id;
		}

		return false;
	}

	function cadastrarPermissaoUsuario($data)
	{
		if ($this->db->insert('auth_permissoes', $data)) {
			$this->logger->logAction('permissaoUser create', (array) $data);
			return true;
		}

		return false;
	}

	function deletarPermissaoUsuario($id_usuario, $id_empresa)
	{
		$data = array(
			'excluido' => 1
		);

		$this->db->where('id_usuario', $id_usuario);
		$this->db->where('id_empresa', $id_empresa);
		if ($this->db->update('auth_permissoes', $data)) {
			$this->logger->logAction('permissaoUser delete', (array) $data);
			return true;
		}

		return false;
	}

	function alterarUsuario($id_usuario, $id_empresa, $data)
	{
		$this->db->where('id', $id_usuario);
		$this->db->where('id_empresa', $id_empresa);
		if ($this->db->update('usuarios', $data)) {
			$this->logger->logAction('user update(ID: ' . $id_usuario . ')', (array) $data);
			return true;
		}

		return false;
	}


	function deletarUsuario($id_usuario)
	{
		$data = array(
			'excluido' => 1
		);

		$this->db->where('id', $id_usuario);
		if ($this->db->update('usuarios', $data)) {
			$this->logger->logAction('user delete(ID: ' . $id_usuario . ')', (array) $data);
			return true;
		}

		return false;
	}

	function getClasses()
	{

		$this->db->select('classe');
		$this->db->from('auth_metodos');
		$this->db->group_by("classe");
		$this->db->order_by("classe", "ASC");

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}

	function getMetodos()
	{

		$this->db->select('*');
		$this->db->from('auth_metodos');
		$this->db->order_by("metodo", "ASC");

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}

	function getNiveis()
	{
		$this->db->select('*');
		$this->db->from('nivel N');

		$query = $this->db->get();

		return $query->result();
	}

	function cadastrarUsuarioEmpresa($data)
	{
		if ($this->db->insert('usuario_empresa', $data)) {
			$this->logger->logAction('usuario_empresa create', (array) $data);
			return true;
		}
		return false;
	}

	function buscaFiscais()
	{
		$this->db->select('u.id');
		$this->db->from('usuarios u');
		$this->db->join('nivel n', 'n.id = u.nivel');
		$this->db->join('usuario_empresa emp', 'u.id = emp.id_usuario', 'left');
		$this->db->where('u.excluido', 0);
		$this->db->where('u.nivel', FISCAL);
		$this->db->order_by("u.nome", "ASC");

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return array();
	}

	function buscaEmpresas()
	{
		$this->db->select('id');
		$this->db->from('empresa');
		$this->db->where('excluido', 0);
		$this->db->order_by('id');

		$query = $this->db->get();

		if ($query->num_rows() >= 1) {
			return $query->result();
		}
		return false;
	}
}
