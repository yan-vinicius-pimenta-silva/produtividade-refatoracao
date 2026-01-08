<?php
class Logger extends Baselibrary {
 
    private $oUser;
 
    public function __construct() {
        parent::__construct();
    }
 
    /**
     * Logs action to log file as 'info'
     * Requires $config['log_threshold'] to be >= 3 (application/config/config.php)
     * @param string $strAction name of the action user takes
     * @param array $arrData optional details of taken action
     */
    public function logAction($strAction, array $arrData = null, $usuario = null) {
        
        $this->oUser = $usuario;
        if(@$this->CI->session->userdata['logged_in']['id'])
            $this->oUser = $this->CI->session->userdata['logged_in']['id'];

        $this->CI->load->library('user_agent');
        $db = $this->CI->load->database();

        $data['user'] = $this->oUser;
        $data['action'] = $strAction;
        $data['ip'] = $this->CI->input->ip_address();
        $data['request_uri'] = $this->CI->input->server('REQUEST_URI');
        $data['datetime'] = date('Y-m-d H:i:s');
        $data['referer_page'] = $this->CI->agent->referrer();
        if ($arrData)
            $data['data'] = str_replace(array("\n", "\r", "    "), '', print_r($arrData, true));


       $this->CI->db->insert('logs', $data);
    }
 
    /**
     * Logs exception to log file as 'error'
     * Requires $config['log_threshold'] to be >= 1 (application/config/config.php)
     * @param Exception $oException
     */
    public function logException(Exception $oException) {
        $strMessage = '';
        $strMessage .= $oException->getMessage() . ' ';
        $strMessage .= $oException->getCode() . ' ';
        $strMessage .= $oException->getFile() . ' ';
        $strMessage .= $oException->getLine();
        $strMessage .= "\n" .  $oException->getTraceAsString();
         
        log_message('error', $strMessage);
    }
 
}

?>