<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{	

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Admin/Admin_master_model','admin_model');
		ini_set('display_errors', 0);

		if ($this->session->userdata('logged_in') == FALSE) {
			redirect('login');
		}
		
	}

	public function index()
	{
		$data['title'] = 'Dashboard';	
		$data['left_bar'] = $this->admin_model->check_navbar();

		$id_campus = $this->session->id_campus;

		if ($this->session->id_level == 1 OR $this->session->id_level == 5 OR $this->session->id_level == 6) {
			$access = 1;
		} else {
			$access = 0;
		}

		$query = $this->db->select('count(id_leads) as count')
                      ->where('YEAR(create_date) <=', date('Y'))
                      ->where('id_campus', $id_campus)
                      ->where('papua', 0)
                      ->limit(5)
                      ->group_by('YEAR(create_date)')
                      ->order_by('create_date','desc')
                      ->get('tb_leads');

	    $data['leads'] = json_encode(array_column($query->result(), 'count'),JSON_NUMERIC_CHECK);
	   
	    $query = $this->db->select('count(id_student) as count')
	    				  ->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
	                      ->where('intake_leads !=','0000-00-00')
	                      ->where('YEAR(intake_leads) <=', date('Y'))
	                      ->where('id_status2','')
	                      ->where('new_student',1)
	                      ->where('tb_student.id_campus', $id_campus)
	                      ->where('paid', 1)
	                      ->where('papua', 0)
	                      ->limit(5)
	                      ->group_by('YEAR(intake_leads)')
	                      ->order_by('intake_leads','desc')
	                      ->get('tb_student');
	    
	    $data['student'] = json_encode(array_column($query->result(), 'count'),JSON_NUMERIC_CHECK);

	    $query = $this->db->select('intake_leads')
	                      ->where('intake_leads !=','0000-00-00')
	                      ->where('YEAR(intake_leads) <=', date('Y'))
	                      ->where('tb_student.id_campus', $id_campus)
	                      ->limit(5)
	                      ->group_by('YEAR(intake_leads)')
	                      ->order_by('intake_leads','desc')
	                      ->get('tb_student')
	                      ->result();

	    $data['year'] = $query;

	    $query = $this->db->select('count(id_student) as count')
	    				  ->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
	                      ->where('YEAR(intake_leads)', date('Y'))
				    	  ->where('handled_by !=','')
				    	  ->where('handled_by !=',0)
				    	  ->where('paid', 1)
				    	  ->where('new_student',1)
				    	  ->where('papua',0)
				    	  ->where('id_status2','')
				    	  ->where('tb_student.id_campus', $id_campus)
	                      ->group_by('handled_by')
	                      ->order_by('count(id_student)','desc')
	                      ->get('tb_student');
	    
	    $data['line'] = json_encode(array_column($query->result(), 'count'),JSON_NUMERIC_CHECK);


	    $data['admission'] = $this->db->select('employee_initial, count(id_student) as total')
				    				  ->join('db_hr.tb_employee','tb_employee.id_employee = tb_student.handled_by')
				    				  ->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
				    				  ->where('YEAR(intake_leads)', date('Y'))
				    				  ->where('handled_by !=','')
				    				  ->where('handled_by !=', 0)
				    				  ->where('paid', 0)
				    				  ->where('new_student',1)
				    				  ->where('id_status2','')
				    				  ->where('tb_student.id_campus', $id_campus)
				                      ->group_by('tb_student.handled_by')
				                      ->order_by('count(id_student)','desc')
				                      ->get('tb_student')
				                      ->result();

		$data['month'] = $this->db->select('tb_student.id_campus, intake_leads')
								  ->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
								  ->where('papua',0)
								  ->where('paid', 1);
		if ($id_campus == 3) {
			$data['month'] = $this->db->where('MONTH(intake_leads) != ', 2)
									  ->where('MONTH(intake_leads) != ', 7);
		} 
		$data['month'] = $this->db->where('tb_student.id_campus', $id_campus)
								  ->where('YEAR(intake_leads)', date('Y'))
								  ->group_by('intake_leads')
								  ->get('tb_student')
								  ->result();

		$data['task'] = $this->db->join('tb_leads_timeline','tb_leads_timeline.id_leads_timeline = tb_lt_task.id_leads_timeline')
								 ->join('tb_leads','tb_leads.id_leads = tb_leads_timeline.id_leads')
								 ->where('task_checked', 0);

		if ($access == 0) {
			$data['task'] = $this->db->where('id_user_add', $this->session->id_employee);
		}
		
		$data['task'] = $this->db->limit(5)
								 ->order_by('task_date','asc')
								 ->get('tb_lt_task')
								 ->result();

		$data['total_task'] = $this->db->join('tb_leads_timeline','tb_leads_timeline.id_leads_timeline = tb_lt_task.id_leads_timeline')
							   		   ->join('tb_leads','tb_leads.id_leads = tb_leads_timeline.id_leads')
							   		   ->where('task_checked', 0);
		if ($access == 0) {
			$data['total_task'] = $this->db->where('id_user_add', $this->session->id_employee);
		}
			$data['total_task'] = $this->db->count_all_results('tb_lt_task');

		$date_75 = date('Y-m-d', strtotime('-75 day', strtotime(date('Y-m-d'))));


		$data['expired'] = $this->db->select('*, tb_leads.id_leads')
									->join('tb_leads','tb_leads.id_leads = tb_initial_contact.id_leads')
									->join('tb_status','tb_status.id_status = tb_leads.id_status')
									->where('last_follow_up <', $date_75)
									->where('id_status2','')
									->where('id_status_type',1);
		if ($access == 0) {
			$data['expired'] = $this->db->where('tb_initial_contact.handled_by', $this->session->id_employee);
		}
									
		$data['expired'] = $this->db->limit(5)
								 	->order_by('tb_leads.last_follow_up','desc')
								 	->get('tb_initial_contact')
								 	->result();

		$data['total_expired'] = $this->db->join('tb_leads','tb_leads.id_leads = tb_initial_contact.id_leads')
									->join('tb_status','tb_status.id_status = tb_leads.id_status');
		if ($access == 0) {
			$data['total_expired'] = $this->db->where('tb_initial_contact.handled_by', $this->session->id_employee);
		}
		$data['total_expired'] = $this->db->where('last_follow_up <', $date_75)
									->where('id_status2','')
									->where('id_status_type',1)
								 	->count_all_results('tb_initial_contact');

		$data['family'] = $this->db->select('*, tb_family.name as parents_name, tb_family.email as parents_email, tb_family.phone as parents_phone')
						   ->join('tb_leads','tb_leads.id_leads = tb_family.id_leads')
						   ->join('tb_initial_contact','tb_initial_contact.id_leads = tb_leads.id_leads')
						   ->where('tb_initial_contact.locked','0')
						   ->where('family_status','0');

		if ($access == 0) {
			$data['family'] = $this->db->where('tb_initial_contact.handled_by', $this->session->id_employee);
		}

		$data['family'] = $this->db->limit(5)
								   ->order_by('upload_date','desc')
								   ->get('tb_family')
								   ->result();

		$data['document'] = $this->db->join('tb_leads','tb_leads.id_leads = tb_document.id_leads')
								   ->join('tb_initial_contact','tb_initial_contact.id_leads = tb_leads.id_leads')
								   ->join('tb_document_type','tb_document_type.id_document_type = tb_document.id_doc_type')
								   ->where('tb_initial_contact.locked','0')
								   ->where('document_status','0');

		if ($access == 0) {
			$data['document'] = $this->db->where('tb_initial_contact.handled_by', $this->session->id_employee);
		}

		$data['document'] = $this->db->limit(5)
								   ->order_by('upload_date','desc')
								   ->get('tb_document')
								   ->result();

		$data['family_total'] = $this->db->join('tb_leads','tb_leads.id_leads = tb_family.id_leads')
								   ->join('tb_initial_contact','tb_initial_contact.id_leads = tb_leads.id_leads')
								   ->join('db_hr.tb_employee','tb_employee.id_employee = tb_initial_contact.handled_by')
								   ->where('tb_initial_contact.locked','0')
								   ->where('family_status','0');
		if ($access == 0) {
			$data['family_total'] = $this->db->where('tb_initial_contact.handled_by', $this->session->id_employee);
		}

		$data['family_total'] = $this->db->count_all_results('tb_family');

		$data['document_total'] = $this->db->join('tb_leads','tb_leads.id_leads = tb_document.id_leads')
								   ->join('tb_initial_contact','tb_initial_contact.id_leads = tb_leads.id_leads')
								   ->where('tb_initial_contact.locked','0')
								   ->where('document_status','0');
		if ($access == 0) {
			$data['document_total'] = $this->db->where('tb_initial_contact.handled_by', $this->session->id_employee);
		}

		$data['document_total'] = $this->db->count_all_results('tb_document');
		
		$this->load->view('Recruitment/Dashboard/dashboard_recruitment_view', $data);
	}
}