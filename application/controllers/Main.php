<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	function __construct(){
        parent::__construct();
		$this->load->model('mainmodel');

        $this->load->helper('html');
        $this->load->helper('url');
        $this->load->helper('date');
        $this->load->helper('security');
        $this->load->library('session');  
        $this->load->library('form_validation');
        $this->load->library("pagination"); 
        $this->load->library('zip');
        $this->load->helper('file');
    }

	public function index()
	{

		$source_table = 'kpi_pay';

        $temp_array = $this->mainmodel->get_all_regions($source_table);
        $data['regions'] = $temp_array;
        $temp_array = array();
        
        $temp_array = $this->mainmodel->get_all_provinces($source_table);
        $data['provinces'] = $temp_array;
        $temp_array = array();

        $temp_array = $this->mainmodel->get_all_regions_and_provinces($source_table);
        $data['locations'] = $temp_array;
        $temp_array = array();

		$this->load->view('index_page', $data);
	}
}
