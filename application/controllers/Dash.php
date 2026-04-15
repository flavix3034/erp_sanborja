<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dash extends MY_Controller {

    function __construct(){
        parent::__construct();
    }

    public function index(){
    	$this->data["page_title"] = "Dashboard";
    	$this->template->load('view_layout', 'dash/index', $this->data);
    }

}