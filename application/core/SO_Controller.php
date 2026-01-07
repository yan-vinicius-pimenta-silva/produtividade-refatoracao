<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class SO_Controller extends CI_Controller
{

	function __construct()
	{
		parent::__construct();
	}

	function logado()
	{
		if (!$this->user_id()) redirect('login', 'refresh');
	}

	function user_id()
	{
		if (!$this->session->userdata('logged_in')) {
			return false;
		}
		$user = $this->session->userdata('logged_in');
		return $user['id'];
	}

	function check_menu($id_user, $id_empresa)
	{

		$this->load->model('model_usuario');
		$permissoes = $this->model_usuario->getPermissoesByUser($id_user, $id_empresa);

		if (!$permissoes)
			return false;

		$this->db->select('*');
		$this->db->from('auth_metodos');
		$this->db->where_in('id', $permissoes);

		$query = $this->db->get();

		if ($query->num_rows() >= 1)
			return $query->result();

		return false;
	}

	function last_ocorrencias($id_user)
	{
		// $this->load->model('model_ocorrencia');
		// $result = $this->model_ocorrencia->getOcorrencias($id_user, null, null, null, null, null, null, null, 5);
		// if($result){
		//     foreach ($result as $row) {
		//         $row->tempo_atras = $this->tempo_atras($row->data_ocorrencia, $row->hora_ocorrencia);
		//     }

		//     return $result;
		// }

		return false;
	}

	function tempo_atras($date, $time)
	{
		$tempo_atras = '';
		$timestamp = strtotime($date . " " . $time);
		$segundo = time() - $timestamp;
		$minuto = (int)($segundo / 60);
		$hora = (int)($minuto / 60);
		$dia = (int)($hora / 24);
		$meses = (int)($dia / 30);
		$ano = (int)($dia / 365);

		if ($ano >= 1)
			$tempo_atras = 'há ' . $ano . ' ano' . ($ano != 1 ? 's' : '');
		else if ($meses >= 1)
			$tempo_atras = 'há ' . $meses .  ' mes' . ($meses != 1 ? 'es' : '');
		else if ($dia >= 1)
			$tempo_atras = 'há ' . $dia . ' dia' . ($dia != 1 ? 's' : '');
		else if ($hora >= 1)
			$tempo_atras = 'há ' . $hora . ' hora' . ($hora != 1 ? 's' : '');
		else if ($minuto >= 1)
			$tempo_atras = 'há ' . $minuto . ' minuto' . ($minuto != 1 ? 's' : '');
		else
			$tempo_atras = 'há ' . $segundo . ' ha segundo' . ($segundo != 1 ? 's' : '');


		return $tempo_atras;
	}

	/**
	 *
	 * decimar_br
	 *
	 * Verifica se é decimal, mas com virgula no lugar de .
	 * @access  public
	 * @param   string
	 * @return  bool
	 */
	public function decimal_br($str)
	{
		$CI = &get_instance();
		$CI->form_validation->set_message('decimal_br', 'O campo %s não contem um valor decimal válido.');
		return (bool) preg_match('/^[\-+]?[0-9]+\,[0-9]+$/', $str);
	}

	/**
	 *
	 * valida_cpf
	 *
	 * Verifica CPF é válido
	 * @access  public
	 * @param   string
	 * @return  bool
	 */
	function valida_cpf($cpf)
	{
		$CI = &get_instance();

		$CI->form_validation->set_message('valida_cpf', 'O %s informado não é válido.');
		$cpf = preg_replace('/[^0-9]/', '', $cpf);
		if (strlen($cpf) != 11 || preg_match('/^([0-9])\1+$/', $cpf)) {
			return false;
		}
		// 9 primeiros digitos do cpf
		$digit = substr($cpf, 0, 9);
		// calculo dos 2 digitos verificadores
		for ($j = 10; $j <= 11; $j++) {
			$sum = 0;
			for ($i = 0; $i < $j - 1; $i++) {
				$sum += ($j - $i) * ((int) $digit[$i]);
			}
			$summod11 = $sum % 11;
			$digit[$j - 1] = $summod11 < 2 ? 0 : 11 - $summod11;
		}

		return $digit[9] == ((int)$cpf[9]) && $digit[10] == ((int)$cpf[10]);
	}

	/**
	 * valida_date
	 *
	 * valida data no pradrao brasileiro
	 * 
	 * @access  public
	 * @param   string
	 * @return  bool
	 */
	function valida_date($data)
	{
		$CI = &get_instance();
		$CI->form_validation->set_message('valida_date', 'O campo %s não contém uma data válida.');
		$padrao = explode('/', $data);
		return checkdate($padrao[1], $padrao[0], $padrao[2]);
	}
	/**
	 * valida_cep
	 *
	 * Verifica se CEP é válido
	 * 
	 * @access  public
	 * @param   string
	 * @return  bool
	 */
	function valida_cep($cep)
	{
		$CI = &get_instance();
		$CI->form_validation->set_message('valida_cep', 'O campo %s não contém um CEP válido.');
		$cep = str_replace('.', '', $cep);
		$cep = str_replace('-', '', $cep);
		$url = 'http://republicavirtual.com.br/web_cep.php?cep=' . urlencode($cep) . '&formato=query_string';
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 0);
		$resultado = curl_exec($ch);
		curl_close($ch);
		if (!$resultado)
			$resultado = "&resultado=0&resultado_txt=erro+ao+buscar+cep";
		$resultado = urldecode($resultado);
		$resultado = utf8_encode($resultado);
		parse_str($resultado, $retorno);
		if ($retorno['resultado'] == 1 || $retorno['resultado'] == 2)
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * valida_fone
	 *
	 * validação simples de telefone
	 *
	 * @access  public
	 * @param   string
	 * @return  bool
	 */
	function valida_fone($fone)
	{
		$CI = &get_instance();
		$CI->form_validation->set_message('valida_fone', 'O campo %s não contém um Telefone válido.');
		$fone = preg_replace('/[^0-9]/', '', $fone);
		$fone = (string) $fone;
		if (strlen($fone) >= 10)
			return TRUE;
		else
			return FALSE;
	}


	function envia_email($to, $name_to, $subject, $template, $data)
	{
		$this->load->library('email');
		$config['smtp_host'] = "smtp.araras.sp.gov.br";
		$config['smtp_user'] = "no-replay@araras.sp.gov.br";
		$config['smtp_pass'] = "rqa6sngs";
		$config['smtp_port'] = "587";
		$config['protocol'] = "smtp";
		$config['wordwrap'] = TRUE; // define se haverá quebra de palavra no texto
		$config['validate'] = TRUE; // define se haverá validação dos endereços de email
		$config['mailtype'] = 'html';
		$this->email->initialize($config);

		// Define remetente e destinatário
		$this->email->from('no-replay@araras.sp.gov.br', 'Aplicativo - Prefeitura de Araras'); // Remetente
		$this->email->to($to, $name_to); // Destinatário
		//$this->email->to('andre.fantinato@gmail.com', $name_to); // Destinatário
		// Define o assunto do email
		$this->email->subject($subject);
		//Passa o conteúdo do template para a mensagem
		$this->email->message($this->load->view('template/email/' . $template, $data, TRUE));

		//send e-mail
		return $this->email->send();
	}


	// function sendPush($heading, $content, $email)
	// {
	//     if($email == '' || $email == null) return;

	//     $heading = array("en" => $heading);
	//     $content = array("en" => $content);

	//     $fields = array(
	//         'app_id'     => "8c5bf430-4581-46b8-9328-400a0c7b0340",
	//         'filters'    => array(array("field" => "tag", "key" => "email", "relation" => "=", "value" => $email)),
	//         'small_icon' => 'icon',
	//         'data'       => array("foo" => "bar"),
	//         'headings'   => $heading,
	//         'contents'   => $content
	//     );

	//     $fields = json_encode($fields);

	//     $ch = curl_init();
	//     curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
	//     // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
	//     //                                         'Authorization: Basic OGMwYTE4NTMtNzA0ZC00NjQyLWFmYzItYWJhMzhmZTkwOTg3'));
	//     curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
	//                                                'Authorization: Basic ZDQ5ZTAxZWQtYTdiYi00ZTVkLWIyNmUtNzBhZWYyZGQ5MWVm'));
	//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	//     curl_setopt($ch, CURLOPT_HEADER, FALSE);
	//     curl_setopt($ch, CURLOPT_POST, TRUE);
	//     curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
	//     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	//     $response = curl_exec($ch);
	//     curl_close($ch);

	//     return $response;
	// }

	function convertMes($mes)
	{
		$mes_para_texto = array(
			1 => 'JAN',
			2 => 'FEV',
			3 => 'MAR',
			4 => 'ABR',
			5 => 'MAI',
			6 => 'JUN',
			7 => 'JUL',
			8 => 'AGO',
			9 => 'SET',
			10 => 'OUT',
			11 => 'NOV',
			12 => 'DEZ'
		);

		return $mes_para_texto[$mes];
	}

	function convertSemana($dia)
	{
		$semana_para_texto = array(
			0 => 'DOM',
			1 => 'SEG',
			2 => 'TER',
			3 => 'QUA',
			4 => 'QUI',
			5 => 'SEX',
			6 => 'SÁB'
		);

		return $semana_para_texto[$dia];
	}

	function removeCaractere($string)
	{

		$table = array(
			'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z',
			'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
			'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
			'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
			'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
			'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
			'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
			'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
			'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
			'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
			'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
			'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
			'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
			'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
			'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r'
		);

		// Traduz os caracteres em $string, baseado no vetor $table
		$string = strtr($string, $table);
		// converte para minúsculo
		$string = strtolower($string);
		// remove caracteres indesejáveis (que não estão no padrão)
		$string = preg_replace("/[^a-z0-9_.\s-]/", "", $string);
		// Remove múltiplas ocorrências de hífens ou espaços
		$string = preg_replace("/[\s-]+/", " ", $string);
		// Transforma espaços e underscores em hífens
		$string = preg_replace("/[\s_]/", "-", $string);

		// retorna a string
		return $string;
	}


	function getAddress($lat, $lng)
	{
		$rua = "";
		$bairro = "";
		$cidade = "";
		$uf = "";
		$cep = "";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $lat . "," . $lng . "&key=AIzaSyDNc3Xy5T_QmFqHuNHXlWSjm0zFu_Sk6PM");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-type: application/binary"
		));

		//curl_setopt($ch, CURLOPT_POST, 0);
		$response = curl_exec($ch);
		if (curl_errno($ch))
			return -1;

		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$str = substr($response, $header_size);
		curl_close($ch);

		$data = json_decode($str, true);
		if (isset($data["results"]) && is_array($data["results"])) {

			$ac = $data["results"][0]["address_components"];
			$qtd = count($ac);

			if ($qtd == 8) {

				$rua = ($ac[1]["long_name"]) ? $ac[1]["long_name"] : "";
				$bairro = ($ac[2]["long_name"]) ? $ac[2]["long_name"] : "";
				$cidade = ($ac[3]["long_name"]) ? $ac[3]["long_name"] : "";
				$uf = ($ac[5]["short_name"]) ? $ac[5]["short_name"] : "";
				$cep = ($ac[7]["long_name"]) ? $ac[7]["long_name"] : "";
				$cep = (strlen($cep) < 9) ? "" : $cep;
			} else if ($qtd == 7) {

				$rua = ($ac[1]["long_name"]) ? $ac[1]["long_name"] : "";
				$bairro = ($ac[2]["long_name"]) ? $ac[2]["long_name"] : "";
				$cidade = ($ac[3]["long_name"]) ? $ac[3]["long_name"] : "";
				$uf = ($ac[4]["short_name"]) ? $ac[4]["short_name"] : "";
			} else if ($qtd == 6) {

				$rua = ($ac[0]["long_name"]) ? $ac[0]["long_name"] : "";
				$bairro = ($ac[1]["long_name"]) ? $ac[1]["long_name"] : "";
				$cidade = ($ac[2]["long_name"]) ? $ac[2]["long_name"] : "";
				$uf = ($ac[3]["short_name"]) ? $ac[3]["short_name"] : "";
			} else if ($qtd == 5) {

				$rua = ($ac[0]["long_name"]) ? $ac[0]["long_name"] : "";
				$bairro = ($ac[1]["long_name"]) ? $ac[1]["long_name"] : "";
				$cidade = ($ac[1]["long_name"]) ? $ac[1]["long_name"] : "";
				$uf = ($ac[3]["short_name"]) ? $ac[3]["short_name"] : "";
			} else if ($qtd == 4) {

				$rua = ($ac[0]["long_name"]) ? $ac[0]["long_name"] : "";
				$bairro = ($ac[1]["long_name"]) ? $ac[1]["long_name"] : "";
				$cidade = ($ac[1]["long_name"]) ? $ac[1]["long_name"] : "";
				$uf = ($ac[2]["short_name"]) ? $ac[2]["short_name"] : "";
			}
		}

		$result = array(
			"rua"    => $rua,
			"bairro" => $bairro,
			"cidade" => $cidade,
			"uf"     => $uf,
			"cep"    => $cep
		);

		return $result;
	}

	function is_timestamp($timestamp)
	{
		$check = (is_int($timestamp) or is_float($timestamp))
			? $timestamp
			: (string) (int) $timestamp;
		return ($check === $timestamp)
			and ((int) $timestamp <=  PHP_INT_MAX)
			and ((int) $timestamp >= ~PHP_INT_MAX);
	}

	function uploadFile()
	{
		$token = $this->post('token');
		if ($token == '8nfr=g9u@@433') {

			$data = $this->post('fileupload');
			$foto_name = $this->post('foto_name');
			$pasta_dir = $this->post('pasta_dir');


			if (!is_dir($pasta_dir))
				mkdir($pasta_dir, 0777, TRUE);

			file_put_contents($pasta_dir . $foto_name, $data);
		}
	}
}
