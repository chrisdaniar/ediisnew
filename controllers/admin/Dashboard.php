<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{	

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Admin/Admin_master_model','admin_model');
		//ini_set('display_errors', 0);
		
	}

	public function v2()
	{
		$data['title'] = 'Dashboard';	
		$data['left_bar'] = $this->admin_model->check_navbar();

		$data['semester_dropdown'] = $this->db->order_by('semester_start_date','desc')
									 		  ->get('tb_semester')
									 		  ->result();	
		
		$this->load->view('Admin/Dashboard/dashboard_acreditation_view', $data);
	}

	public function student_teacher_ratio()
	{
		$data['title'] = 'Dashboard';	
		$data['left_bar'] = $this->admin_model->check_navbar();
		$id_semester = $this->input->post('id_semester');
		$data['semester'] = $this->db->where('id_semester', $id_semester)
									 ->get('tb_semester')
									 ->row();	
		
		$this->load->view('Admin/Dashboard/student_teacher_ratio_view', $data);
	}
}