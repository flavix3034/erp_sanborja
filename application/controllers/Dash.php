<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dash extends CI_Controller {

    function __construct(){
        parent::__construct();
        session_start();
    }

    public function index(){
    	$this->data["page_title"] = "Dashboard";
    	$this->template->load('view_layout', 'dash/index', $this->data);
    }

}