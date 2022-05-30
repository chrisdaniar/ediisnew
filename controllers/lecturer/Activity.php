<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Academic/Academic_master_model','academic_master_model');
		$this->load->model('Recruitment/Recruitment_master_model','recruitment_model');

		$this->load->model('Admin/Admin_master_model','admin_model');
		$this->link_terakhir = $this->config->item('link_terakhir');

		if ($this->session->userdata('logged_in') == FALSE OR $this->session->userdata('id_level') != 1 AND $this->session->userdata('id_level') != 11 AND $this->session->userdata('id_level') != 12 AND $this->session->userdata('id_level') != 13) {
			redirect('login');
		}
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function class_lecture($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Class Lecture';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['teacher'] = $this->academic_master_model->get_teacher();
		     $data['course'] = $this->recruitment_model->get_course();
		     $data['session'] = $this->academic_master_model->get_session();
		     $data['subject'] = $this->academic_master_model->get_subject();
		     $data['academic_year'] = $this->recruitment_model->get_academic_year();
		     $data['teacher_hours'] = $this->academic_master_model->get_teacher_hours();
		     $data['course_structure'] = $this->academic_master_model->get_course_structure();
		     $data['pagination_data'] = $this->pagination_main_class($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_main_class($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Activity/class_lecture_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} else {

		}
	}

	public function pagination_main_class($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_main_class.id_main_class';
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
            $query = $this->db->like('subject_name', $get['search'])
            				  ->or_like('subject_code', $get['search'])
            				  ->or_like('course', $get['search'])
            				  ->or_like('course_abstract', $get['search'])
            				  ->or_like('course_code', $get['search'])
            				  ->or_like('employee_name', $get['search'])
            				  ->or_like('tb_main_class.id_main_class', $get['search']);
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

        $query = $this->db->select('*, tb_main_class.id_main_class')
        				  ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
        				  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
        				  ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester','left')
        				  ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
        				  ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
        				  ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year','left')
        				  ->join('tb_class_lecture','tb_class_lecture.id_main_class=tb_main_class.id_main_class','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_class_lecture.id_employee','left')
        				  ->join('tb_session','tb_session.id_session=tb_main_class.id_session','left')
        				  ->where('tb_class_lecture.id_employee', $this->session->userdata('id_employee'))
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->group_by('tb_main_class.id_main_class')
        				  //->group_by('tb_school.id_school')
                          ->get('tb_main_class')->$param();
        return $query;
    }



	
}




