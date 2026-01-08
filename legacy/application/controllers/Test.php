<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Test extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->load->library('unit_test');

        // Adds one plus one
        $test = 1 + 1;

        $expected_result = 2;

        $test_name = 'Adds one plus one';

        $this->unit->run($test, $expected_result, $test_name);

        echo $this->unit->report();
    }
}
