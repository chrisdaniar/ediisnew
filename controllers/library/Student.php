<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Student extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Admin_master_model', 'admin_master_model');
        $this->load->model('IT/It_student_model', 'it_student_model');
        $this->load->model('Library/Library_student_model', 'library_student_model');
        $this->load->model('Academic/Academic_master_model', 'academic_master_model');
        $this->load->model('Recruitment/Recruitment_master_model', 'recruitment_master_model');
        $this->link_terakhir = $this->config->item('link_terakhir');
    }

    public function finance_clearance($param = '')
    {
        if ($param == 'new_student') {
            $data['title'] = 'Student';
            $data['intended_program'] = $this->academic_master_model->get_intended_program();
            $data['course'] = $this->academic_master_model->get_course();
            $data['intake_year'] = $this->recruitment_master_model->get_intake_year();
            $data['month'] = $this->recruitment_master_model->get_month();
            $data['course'] = $this->academic_master_model->get_course();
            $data['campus'] = $this->academic_master_model->get_campus();
            $data['left_bar'] = $this->admin_master_model->check_navbar();
            $data['pagination_data'] = $this->it_student_model->pagination_student($this->input->get(), 'result_array');
            $data['pagination_total_page'] = $this->it_student_model->pagination_student($this->input->get(), 'num_rows');
            $this->load->view('IT/Student/finance_clearance_view', $data);
            $this->session->set_userdata('previous_url', $this->link_terakhir);
        } elseif ($param == 'existing_student') {
            $data['title'] = 'Student';
            $data['intended_program'] = $this->academic_master_model->get_intended_program();
            $data['course'] = $this->academic_master_model->get_course();
            $data['intake_year'] = $this->recruitment_master_model->get_intake_year();
            $data['month'] = $this->recruitment_master_model->get_month();
            $data['course'] = $this->academic_master_model->get_course();
            $data['campus'] = $this->academic_master_model->get_campus();
            $data['left_bar'] = $this->admin_master_model->check_navbar();
            $data['pagination_data'] = $this->it_student_model->pagination_existing_student($this->input->get(), 'result_array');
            $data['pagination_total_page'] = $this->it_student_model->pagination_existing_student($this->input->get(), 'num_rows');
            $this->load->view('IT/Student/finance_clearance_view', $data);
            $this->session->set_userdata('previous_url', $this->link_terakhir);
        } elseif ($param == 'activate_book_borrowing') {
            $hasil = $this->library_student_model->activate_book_borrowing();
            echo $hasil;
        }
    }
}