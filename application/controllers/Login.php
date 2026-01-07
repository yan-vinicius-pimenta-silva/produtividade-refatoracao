<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Login extends SO_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model(
			'model_login',
			'',
			TRUE
		);
	}
	

	function index()
	{
		$login = LOGIN_DEV;
		$jwt = null;

		$userPicture = (object) array('foto' => base_url() . 'images/brasao.png');
		if (!$login) {
			$url = parse_url($_SERVER['REQUEST_URI']);
			if (!isset($url['query'])) {
				$this->logout();
			}

			parse_str($url['query'], $params);
			$jwt = $params['token'];
			if ($this->loadUserPicture($jwt)->foto != null) {
				$userPicture = $this->loadUserPicture($jwt);
			}

			$key = "c7b34762-1284-4ea3-90a2-01f78e9761bf";

			try {
				$decoded = JWT::decode($jwt, new Key($key, 'HS256'));
				$login = $decoded->login;
			} catch (\Firebase\JWT\SignatureInvalidException $e) {
				print_r('Signature error');
				header("location: http://pma.sp.gov.br/intranet/?error=403");
				die;
			} catch (\Firebase\JWT\BeforeValidException $e) {
				print_r("Before valid Exception");
				header("location: http://pma.sp.gov.br/intranet/?error=403");
				die;
			} catch (\Firebase\JWT\ExpiredException $e) {
				print_r("Expired Token");
				header("location: http://pma.sp.gov.br/intranet/?error=403");
				die;
			}
		}

		$result = $this->model_login
			->login(
				$login,
				null
			);
		if ($result) {
			$sess_array = array();
			$usuario = $result[0];

			$sess_array = array(
				'id'                 => $usuario->id,
				'usuario'            => $usuario->usuario,
				'nome'               => $usuario->nome,
				'nivel'              => $usuario->nivel,
				'id_empresa'         => $usuario->id_empresa,
				'nome_empresa'       => $usuario->nome_empresa,
				'email_empresa'      => $usuario->email_empresa,
				'secretaria'         => $usuario->secretaria,
				'divisao'            => $usuario->divisao,
				'telefone'           => $usuario->telefone,
				'logo_img'           => $usuario->logo_img,
				'parametros_empresa' => json_decode($usuario->parametros),
				'foto'               => $userPicture->foto
			);
			$this->model_login->update_access($usuario->id);
			$this->session->set_userdata(
				'logged_in',
				$sess_array
			);

			redirect('home', 'refresh');
		} else {
			print_r("Expired Token");
			header("location: http://pma.sp.gov.br/intranet/?error=403");
			die;
		}
	}


	function logout()
	{
		$this->session->unset_userdata('logged_in');
		redirect('https://www.pma.sp.gov.br/intranet/');
	}


	function loadUserPicture($token)
	{
		/* API URL */
		$url = USERPIC_URL;

		/* Init cURL resource */
		$ch = curl_init($url);

		/* Array Parameter Data */
		$data = json_encode(
			array('pmaUser' => $token)
		);

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		/* pass encoded JSON string to the POST fields */
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		/* set the content type json */
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type:application/json',
			'x-access-token:a7hj2s4df476-7654-82ed-877h-nb23h9f3c'
		));

		/* set return type json */
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		/* execute request */
		$result = curl_exec($ch);

		/* close cURL resource */
		curl_close($ch);

		return json_decode($result);
	}
}
