<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Admin/Admin_master_model','admin_model');
		$this->load->model('Academic/Academic_master_model','academic_master_model');
		$this->load->model('Recruitment/Recruitment_report_model','report_model');
		$this->load->model('Recruitment/Recruitment_master_model','recruitment_model');
		$this->load->model('Recruitment/Leads_model','leads_model');
		$this->link_terakhir = $this->config->item('link_terakhir');

		ini_set('display_errors', 0);

		if ($this->session->userdata('logged_in') == FALSE) {
			redirect('login');
		}
	}

	public function Form($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Form Report';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_form($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_form($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Form/form_view', $data);
		} else {

		}
	}

	 public function pagination_form($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_emergency_form';
        if(!isset($get['sortby2'])) $get['sortby2'] = 'desc';

        if($get['page'] == 1){
            $mulai = 0;
        } else {
            $get['page']--;
            $mulai = $get['page'] * $get['limit'];
        }
        
        if($param == 'result'){

            $query = $this->db->limit($get['limit'], $mulai);
        }
        if(isset($get['search'])){
            $query = $this->db->like('student_name', $get['search'])
            				  ->or_like('intended_program', $get['search']);
        }

        $query = $this->db->join('tb_intended_program','tb_intended_program.id_intended_program=tb_emergency_form.id_intended_program','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_emergency_form')->$param();

        return $query;
    }

    public function intake_report(){
		$data['title'] = 'Intake Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['campus'] = $this->academic_master_model->get_campus();
		$this->load->view('Recruitment/Report/intake_report_view', $data);

	}

	public function display_intake_report(){

		 $intake_year_start = $this->input->post('intake_year_start');
		 $intake_year_end = $this->input->post('intake_year_end');
		 $id_campus = $this->input->post('id_campus');
			 
			 $data['intake_year_start'] = $intake_year_start;
			 $data['intake_year_end'] = $intake_year_end;
			 $data['id_campus'] = $id_campus;

			 $data['campus'] = $this->master_model->get_campus_by_id($id_campus);
			
	         $display = $this->load->view('Recruitment/Report/display_intake_report_view', $data);
	         
	         echo $display;
	}

	public function export_intake_report(){

		 $intake_year_start = $this->input->get('intake_year_start');
		 $intake_year_end = $this->input->get('intake_year_end');
		 $id_campus = $this->input->get('id_campus');

		 header('Content-Type: application/vnd.ms-excel');  
         header('Content-disposition: attachment; filename=Intake Report '.$intake_year_start.' - '.$intake_year_end.'.xls');
			 
			 $data['intake_year_start'] = $intake_year_start;
			 $data['intake_year_end'] = $intake_year_end;
			 $data['id_campus'] = $id_campus;

			 $data['campus'] = $this->master_model->get_campus_by_id($id_campus);
			
	         $display = $this->load->view('Recruitment/Report/export_intake_report_view', $data);
	     
	}

	public function leads_report(){
		$data['title'] = 'Leads Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['intended_program'] = $this->academic_master_model->get_intended_program();
		$data['campus'] = $this->recruitment_model->get_campus();
		$data['status_active'] = $this->leads_model->get_status_active();
		$data['status_closed'] = $this->leads_model->get_status('3');
		$data['recruitment'] = $this->leads_model->get_recruitment();
		$data['school'] = $this->recruitment_model->get_school();
		$this->load->view('Recruitment/Report/Leads/leads_report_view', $data);

	}

	public function display_leads_report(){

		 if ($this->input->post('view') == '') {
		 	$action = 'get';
		 } else {
		 	$action = 'post';
		 }

		 $id_campus = $this->input->$action('id_campus');
		 $id_intended_program = $this->input->$action('id_intended_program');
		 $id_course = $this->input->$action('id_course');
		 $id_specialist = $this->input->$action('id_specialist');
		 $id_school = $this->input->$action('id_school');
		 $id_employee = $this->input->$action('id_employee');
		 $intake_start = $this->input->$action('intake_start');
		 $intake_end = $this->input->$action('intake_end');
		 $data_status = $this->input->$action('data_status');
		 $data_status2 = $this->input->$action('data_status2');
		 $view = $this->input->$action('view');

			 
			 $data['view'] = $view;
			 $data['intended_program'] = $this->academic_master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->academic_master_model->get_course_by_id($id_course);
			 $data['specialist'] = $this->academic_master_model->get_specialist_by_id($id_specialist);
			 $data['leads'] = $this->report_model->get_leads_for_leads_report($id_campus, $id_intended_program,$id_course, $id_specialist, $id_employee, $id_school, $intake_start, $intake_end, $data_status, $data_status2);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Recruitment/Report/Leads/display_leads_report_view', $data);
			 } else {
			 	 header('Content-Type: application/vnd.ms-excel');  
        		 header('Content-disposition: attachment; filename= Leads Report'.date('d M Y').'.xls');

			 	$display = $this->load->view('Recruitment/Report/Leads/display_leads_report_view', $data);
			 }
     	
	}

	
	
}
