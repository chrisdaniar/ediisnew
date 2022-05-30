<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Admin/Admin_master_model','admin_model');
		ini_set('display_errors', 0);

		if ($this->session->userdata('logged_in') == FALSE) {
			redirect('login');
		}
		
	}

	public function hapus(){
		$file = "uploads/0cd401920a30bf28dd59e87c74f71f7d.pdf";

		if (!unlink($file)) {
		  echo ("Error deleting $file");
		} else {
		  echo ("Deleted $file");
		}
	}
	
	public function index()
	{
		$data['title'] = 'Dashboard';	
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['chart_marketing_source'] = $this->chart_marketing_source();
		$data['chart_intended_program'] = $this->chart_intended_program();
		$this->load->view('Marketing/Dashboard/dashboard_marketing_view', $data);
	}

	public function chart_marketing_source(){
		$program = $this->db->get('tb_marketing_source')->result();
		$datasets = array();
		foreach ($program as $row) {
			$jumlah = $this->db->where('id_marketing_source', $row->id_marketing_source)->get('tb_leads')->num_rows();
			$data[] = (int)$jumlah;
			$label[] = $row->marketing_source;
		}
		$datasets['data'] = $data;
		$datasets['backgroundColor'] = array('#fb0091', '#51dacf', '#41aaa8', '#f2a2e4', '#9ea9f0', '#ab72c0', '#af0404', '#000272');
		$kk[] = $datasets;
		$ini['datasets'] = $kk;
		$ini['labels'] = $label;
		$s = json_encode($ini);
		return $s;
	}

	public function chart_intended_program(){
		$program = $this->db->get('tb_intended_program')->result();
		$datasets = array();
		foreach ($program as $row) {
			$jumlah = $this->db->where('id_intended_program', $row->id_intended_program)->get('tb_student')->num_rows();
			$data[] = (int)$jumlah;
			$label[] = $row->intended_program;
		}
		$datasets['data'] = $data;
		$datasets['backgroundColor'] = array('#fb0091', '#51dacf', '#41aaa8', '#f2a2e4', '#9ea9f0', '#ab72c0', '#af0404', '#000272');
		$kk[] = $datasets;
		$ini['datasets'] = $kk;
		$ini['labels'] = $label;
		$s = json_encode($ini);
		return $s;
	}

	

	

}