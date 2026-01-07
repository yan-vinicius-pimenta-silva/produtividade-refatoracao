<?php
class Migrate extends SO_Controller {
        public function index()
        {
                $this->load->library('migration');
                if ($this->migration->current() === FALSE)
                {
                        show_error($this->migration->error_string());
                }
                $this->migration->error_string();
        }
}
?>