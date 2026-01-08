<?php
class Baselibrary {
 
    protected $CI;
 
    public function __construct() {
        $this->CI = & get_instance();
    }
 
}
?>