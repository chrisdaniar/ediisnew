<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Academic/Academic_master_model','academic_model');
		$this->load->model('Recruitment/Recruitment_master_model','recruitment_model');
		$this->load->model('Admin/Admin_master_model','admin_model');
		$this->link_terakhir = $this->config->item('link_terakhir');

		if ($this->session->userdata('logged_in') == FALSE) {
			redirect('login');
		}

        ini_set('display_errors', 0);
	}

	public function cek(){
		$leads = $this->db->select('*, CONCAT(name, family_name)')
						  ->like('CONCAT(name, family_name)','kaito')
						  //->group_by('concat(name, family_name)')
						  ->get('tb_leads')->result();

		print_r($leads);
	}

	  //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function enrollment_activity($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Enrollment Activity';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['intended_program'] = $this->recruitment_model->get_intended_program();
		     $data['course'] = $this->recruitment_model->get_course();
		     $data['subject'] = $this->academic_model->get_subject();
		     $data['academic_year'] = $this->recruitment_model->get_academic_year();
		     $data['pagination_data'] = $this->pagination_enrollment_activity($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_enrollment_activity($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Notification/enrollment_notification_view', $data);
		} 
	}

	public function pagination_enrollment_activity($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 20;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_class_student.id_class_student';
        if(!isset($get['sortby2'])) $get['sortby2'] = 'desc';

        if($get['page'] == 1){
            $mulai = 0;
        } else {
            $get['page']--;
            $mulai = $get['page'] * $get['limit'];
        }
        
        if($param == 'result'){

            $query = $this->db->select('*, CONCAT(name, family_name)')
            				  ->limit($get['limit'], $mulai);
        }
        if(isset($get['search'])){
            $query = $this->db->like('subject_name', $get['search'])
            				  ->or_like('subject_code', $get['search'])
            				  ->or_like('course', $get['search'])
            				  ->or_like('intended_program', $get['search'])
            				  ->or_like('intended_program_abstract', $get['search'])
            				  ->or_like('course_abstract', $get['search'])
            				  ->or_like('course_code', $get['search'])
            				  ->or_like('name', $get['search'])
            				  ->or_like('family_name', $get['search'])
            				  ->or_like('CONCAT(name, family_name)',$get['search']);
        }

        if (isset($get['id_intended_program_filter'])) {
            $query = $this->db->where('tb_intended_program.id_intended_program', $get['id_intended_program_filter']);
        }

        if (isset($get['id_course_filter'])) {
            $query = $this->db->where('tb_course.id_course', $get['id_course_filter']);
        }
        if (isset($get['trimester_filter'])) {
            $query = $this->db->where('tb_trimester.trimester', $get['trimester_filter']);
        }
        if (isset($get['id_subject_filter'])) {
            $query = $this->db->where('tb_subject.id_subject', $get['id_subject_filter']);
        }
        if (isset($get['id_academic_year_filter'])) {
            $query = $this->db->where('tb_academic_year.id_academic_year', $get['id_academic_year_filter']);
        }

        $query = $this->db->join('tb_main_class','tb_main_class.id_main_class=tb_class_student.id_main_class')
        				  ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
        				  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
        				  ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester','left')
        				  ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
        				  ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
        				  ->join('tb_intended_program','tb_intended_program.id_intended_program=tb_course.id_intended_program','left')
        				  ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year','left')
        				  ->join('tb_student','tb_student.id_student=tb_class_student.id_student')
        				  ->join('tb_leads','tb_leads.id_leads=tb_student.id_leads')
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_class_student')->$param();
        return $query;
    }
}