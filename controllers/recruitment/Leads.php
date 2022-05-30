<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Leads extends CI_Controller
{

    public function __construct()
    {

        parent::__construct();
        /*header('Cache-Control: no-cache, must-revalidate, max-age:0');
        header('Cache-Control: post-check=0, pre-check=0, false');*/
        //header('Pragma: no-cache');

        $this->load->model('Recruitment/Leads_model', 'leads_model');
        $this->load->model('Admin/Admin_master_model', 'admin_model');
        $this->load->model('Recruitment/Recruitment_master_model', 'recruitment_master_model');
        $this->load->model('Academic/Academic_master_model', 'academic_master_model');
        $this->load->model('Student/Student_model', 'student_model');

        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('login');
        }

        ini_set('display_errors', 0);
        
    }

    public function index()
    {
        if ($this->session->id_level != 17) {

            if ($this->session->id_level == 1 OR $this->session->id_level == 5 OR $this->session->id_level == 6) {
                $access = 1;
            } else {
                if ($this->session->id_level == 7) {
                    $access = 0;
                } else {
                    $access = 2;
                }
            }   
           
            $m = $this->db->get('tb_gender')->result();
            $data['title'] = 'Recruitment';
            $data['acreditation'] = $this->admin_model->check_acreditation();
            $data['country'] = $this->leads_model->get_country();
            $data['left_bar'] = $this->admin_model->check_navbar();
            $data['pagination_data'] = $this->leads_model->pagination_leads($this->input->get(), 'result_array', $access, $data['acreditation']);
            $data['pagination_total_page'] = $this->leads_model->pagination_leads($this->input->get(), 'num_rows', $access, $data['acreditation']);
            $data['recruitment'] = $this->leads_model->get_recruitment();
            $data['campus'] = $this->academic_master_model->get_campus($data['acreditation']);
            $data['intended_program'] = $this->academic_master_model->get_intended_program($data['acreditation']);
            $data['course'] = $this->academic_master_model->get_course($data['acreditation']);
            $data['intake_year'] = $this->recruitment_master_model->get_intake_year();
            $data['month'] = $this->recruitment_master_model->get_month();
            $data['status_active'] = $this->leads_model->get_status(1);
            $data['access'] = $access;
            $this->load->view('Recruitment/Leads/leads_view', $data);

        } else {
            redirect('login');
        }

    }
    public function task()
    {
        if ($this->session->id_level != 17) {

            if ($this->session->id_level == 1 OR $this->session->id_level == 5 OR $this->session->id_level == 6) {
                $access = 1;
            } else {
                $access = 0;
            }
           
            $data['title'] = 'Task';
            $data['access'] = $access;
            $data['left_bar'] = $this->admin_model->check_navbar();
            $data['recruitment'] = $this->leads_model->get_recruitment();
            $data['pagination_data'] = $this->leads_model->pagination_task($this->input->get(), 'result');
            $data['pagination_total_page'] = $this->leads_model->pagination_task($this->input->get(), 'num_rows');
            $this->load->view('Recruitment/Leads/leads_task_view', $data);

        } else {
            redirect('login');
        }

    }
    public function expired_leads()
    {
        if ($this->session->id_level != 17) {

            if ($this->session->id_level == 1 OR $this->session->id_level == 5 OR $this->session->id_level == 6) {
                $access = 1;
            } else {
                $access = 0;
            }
           
            $data['title'] = 'Expired Leads';
            $data['access'] = $access;
            $data['left_bar'] = $this->admin_model->check_navbar();
            $data['recruitment'] = $this->leads_model->get_recruitment();
            $data['pagination_data'] = $this->leads_model->pagination_expired_leads($this->input->get(), 'result');
            $data['pagination_total_page'] = $this->leads_model->pagination_expired_leads($this->input->get(), 'num_rows');
            $data['intended_program'] = $this->leads_model->list_intended_program();
            $data['campus'] = $this->leads_model->get_campus();
            $this->load->view('Recruitment/Leads/expired_leads_view', $data);

        } else {
            redirect('login');
        }

    }
    public function family($param1 = '')
    {
        if ($this->session->id_level != 17) {

            if ($param1 == 'index') {
               
                if ($this->session->id_level == 1 OR $this->session->id_level == 5 OR $this->session->id_level == 6) {
                    $access = 1;
                } else {
                    $access = 0;
                }
               
                $data['title'] = 'Family';
                $data['access'] = $access;
                $data['left_bar'] = $this->admin_model->check_navbar();
                $data['recruitment'] = $this->leads_model->get_recruitment();
                $data['phone_code'] = $this->leads_model->get_phone_code();
                $data['pagination_data'] = $this->leads_model->pagination_family($this->input->get(), 'result');
                $data['pagination_total_page'] = $this->leads_model->pagination_family($this->input->get(), 'num_rows');
                $this->load->view('Recruitment/Leads/leads_family_view', $data);

            } elseif($param1 == 'family_approval') {
                $id_family = $this->input->post('id_family');
                $family_status = $this->input->post('family_status');

                $data = $this->leads_model->family_approval($id_family, $family_status);
                echo json_encode($data);

                echo $id_family;
            } else {

            }


        } else {
            redirect('login');
        }

    }
    public function document($param1 = '')
    {
        if ($this->session->id_level != 17) {

            if ($param1 == 'index') {
               
                if ($this->session->id_level == 1 OR $this->session->id_level == 5 OR $this->session->id_level == 6) {
                    $access = 1;
                } else {
                    $access = 0;
                }
               
                $data['title'] = 'Document';
                $data['access'] = $access;
                $data['left_bar'] = $this->admin_model->check_navbar();
                $data['recruitment'] = $this->leads_model->get_recruitment();
                $data['document_type'] = $this->recruitment_master_model->get_document_type();
                $data['pagination_data'] = $this->leads_model->pagination_document($this->input->get(), 'result');
                $data['pagination_total_page'] = $this->leads_model->pagination_document($this->input->get(), 'num_rows');
                $this->load->view('Recruitment/Leads/leads_document_view', $data);

            } elseif($param1 == 'document_approval') {
                $id_document = $this->input->post('id_document');
                $document_status = $this->input->post('document_status');

                $data = $this->leads_model->document_approval($id_document, $document_status);
                echo json_encode($data);
            } else {

            }

           

        } else {
            redirect('login');
        }

    }
    public function add()
    {
        header('Cache-Control: no-cache, must-revalidate, max-age:0');
        header('Cache-Control: post-check=0, pre-check=0, false');
        header('Pragma: no-cache');
        
        $data['title'] = 'Leads';
        $data['left_bar'] = 'Recruitment/left_navbar_template';
        $data['event_type'] = $this->leads_model->get_event_type();
        $data['gender'] = $this->leads_model->get_gender();
        $data['country'] = $this->leads_model->get_country();
        $data['campus'] = $this->leads_model->get_campus();
        $data['recruitment'] = $this->leads_model->get_recruitment();
        $data['province'] = $this->leads_model->get_province();
        $data['marketing_source'] = $this->leads_model->get_marketing_source();
        $data['initial_activity'] = $this->leads_model->get_initial_activity();
        $this->load->view('Recruitment/Leads/detail_leads_view', $data);
    }

    public function details($id_leads = ''){
        $this->get_detail_leadss($id_leads);
    }

    public function detail($id_leads = '')
    {
        header('Cache-Control: no-cache, must-revalidate, max-age:0');
        header('Cache-Control: post-check=0, pre-check=0, false');
        header('Pragma: no-cache');
        $this->get_detail_leadss($id_leads);
    }
    public function get_detail_leadss($id_leads = '')
    {
        $data['title'] = 'Leads';
        $data['acreditation'] = $this->admin_model->check_acreditation();
        $data['left_bar'] = $this->admin_model->check_navbar();
        $data['id_leads'] = $this->uri->segment(4);
        $data['event_type'] = $this->leads_model->get_event_type();
        $data['leads'] = $this->leads_model->get_detail_leads($id_leads);
        $data['gender'] = $this->leads_model->get_gender();
        $data['average_type'] = $this->leads_model->get_average_type();
        $data['initial_contact'] = $this->leads_model->get_initial_contact($id_leads);
        $data['initial_contact_employee'] = $this->leads_model->get_initial_contact_for_employee($id_leads);
        $data['country'] = $this->leads_model->get_country();
        $data['phone_code'] = $this->leads_model->get_phone_code();
        $data['recruitment'] = $this->leads_model->get_recruitment();
        $data['province'] = $this->leads_model->get_province();
        $data['program'] = $this->academic_master_model->get_program($data['acreditation']);
        $data['class'] = $this->leads_model->get_class();
        $data['session'] = $this->academic_master_model->get_session();
        $data['campus'] = $this->academic_master_model->get_campus($data['acreditation']);
        $data['trimester'] = $this->leads_model->get_trimester();
        $data['qualification'] = $this->leads_model->get_qualification();
        $data['student_status'] = $this->academic_master_model->get_student_status();
        $data['marketing_source'] = $this->leads_model->get_marketing_source();
        $data['initial_activity'] = $this->leads_model->get_initial_activity();
        $data['status_nonactive'] = $this->leads_model->get_status(3);
        $data['status_active'] = $this->leads_model->get_status_active();
        $data['form'] = $this->leads_model->get_form();
        $this->load->view('Recruitment/Leads/detail_leads_view', $data);
    }
    public function get_regency()
    {
        $id_province = $this->input->post('id_province');
        $data = $this->leads_model->get_regency($id_province);
        echo json_encode($data);
    }
    public function get_detail_leads()
    {
        $id_leads = $this->input->post('id_leads');
        $detail = $this->input->post('detail');
        
        if($detail == 'profile'){
            $data = $this->leads_model->get_detail_leads($id_leads);
        } else if($detail == 'family'){
            $data = $this->leads_model->get_list_family($id_leads);
        } else if($detail == 'future_plan'){
            $data = $this->leads_model->get_list_future_plan($id_leads);
        } else if($detail == 'english' || $detail == 'document' ){
            $doc_type = $this->input->post('doc_type');
            $data = $this->leads_model->get_list_document($id_leads,$doc_type);
        }  else if($detail == 'timeline'){
            $data = $this->leads_model->get_list_timeline($id_leads);
        } else if($detail == 'timeline_load'){
            $data = $this->leads_model->scroll_activity($id_leads);
        } else if($detail == 'get_score'){
            $data = $this->leads_model->get_score($id_leads);
        } else if($detail == 'fo' || $detail == 'co'){
            $data = $this->leads_model->get_list_ol($id_leads, $detail);
        } else if($detail == 'payment'){
            $data = $this->leads_model->get_list_payment($id_leads);
        } else if($detail == 'student'){
            $data = $this->leads_model->get_list_student($id_leads);
        } else if($detail == 'initial_contact'){
            $data = $this->leads_model->get_list_initial_contact($id_leads);
        } else if($detail == 'additional_form'){
            $data = $this->leads_model->get_list_additional_form($id_leads);
        } else if($detail == 'leads_progress_status'){
            $status['leads'] = $this->leads_model->get_detail_leads($id_leads);
            $status['status_active'] = $this->leads_model->get_status_active();
            $data = $this->load->view('Recruitment/Leads/Sub_page/detail_leads_status_view', $status);
        }
        echo json_encode($data);
    }

    public function get_leads_enrollment(){
         $id_leads = $this->input->post('id_leads');
         $data['enrollment'] = $this->leads_model->get_list_enrollment($id_leads);
         $this->load->view('Recruitment/Leads/display_leads_enrollment_view', $data);
    }

    public function get_leads_payment_document(){
         $id_leads_payment = $this->input->post('id_leads_payment');
         $data['document_type'] = $this->input->post('document_type');
         $data['leads_payment_document'] = $this->leads_model->get_list_payment_document($id_leads_payment, $data['document_type']);
         $data['payment'] = $this->leads_model->get_detail_leads_payment($id_leads_payment);
         $this->load->view('Recruitment/Leads/display_leads_payment_document_view', $data);
    }

    public function get_main_class_for_leads(){
        $id_student = $this->input->post('id_student');
        $data = $this->leads_model->get_main_class_for_leads($id_student);
        echo json_encode($data);
    }

    public function get_detail_timeline(){
        $type = $this->input->post('type');
        $id = $this->input->post('id');
        $data = $this->leads_model->get_detail_timeline($type, $id);
        echo json_encode($data);
    }

    public function get_leads_schedule(){
         $id_leads = $this->input->post('id_leads');
         $data['schedule'] = $this->leads_model->get_list_schedule($id_leads);
         $data['leads'] = $this->leads_model->get_detail_leads($id_leads);
         $this->load->view('Recruitment/Leads/display_leads_schedule_view', $data);
    }

    public function df_test()
    {
        $query = $this->db->order_by('create_date', 'asc')->get('tb_leads_timeline')->result_array();
        foreach ($query as $key) {
            $key['detail'] = '';
            if($key['type_timeline'] == 'Task'){
                $key['detail'] = $this->db->where('id_leads_timeline', $key['id_leads_timeline'])->get('tb_lt_task')->row_array();
            }
            $rows[] = $key;
        }
        $no = 0;
    }
    public function get_intended_program()
    {
        $id_program = $this->input->post('id_program');
        $data = $this->leads_model->get_intended_program($id_program);
        echo json_encode($data);
    }
    public function get_course()
    {
        $id_intended_program = $this->input->post('id_intended_program');
        $id_class = $this->input->post('id_class');
        $data = $this->leads_model->get_course($id_intended_program, $id_class);
        echo json_encode($data);
    }
    public function get_program_type()
    {
        $id_intended_program = $this->input->post('id_intended_program');
        $data = $this->leads_model->get_program_type($id_intended_program);
        echo json_encode($data);
    }
    public function get_specialist()
    {
        $id_course = $this->input->post('id_course');
        $data = $this->leads_model->get_specialist($id_course);
        echo json_encode($data);
    }
    public function get_class_leads()
    {
        $id_qualification = $this->input->post('id_qualification');
        $data = $this->leads_model->get_class_leads($id_qualification);
        echo json_encode($data);
    }
    public function get_detail_family(){
        $id_family = $this->input->post('id_family');
        $data = $this->leads_model->get_detail_family($id_family);
        echo json_encode($data);
    }
    public function get_detail_marketing_source(){
        $id_marketing_source = $this->input->post('id_marketing_source');
        $data = $this->leads_model->get_detail_marketing_source($id_marketing_source);
        echo json_encode($data);
    }
    public function get_detail_agent(){
        $id_agent = $this->input->post('id_agent');
        $data = $this->leads_model->get_detail_agent($id_agent);
        echo json_encode($data);
    }
    public function get_detail_leads_payment(){
        $id_leads_payment = $this->input->post('id_leads_payment');
        $data = $this->leads_model->get_detail_leads_payment($id_leads_payment);
        echo json_encode($data);
    }
    public function get_student_by_leads(){
        $id_leads = $this->input->post('id_leads');
        $data = $this->leads_model->get_list_student($id_leads);
        echo json_encode($data);
    }
    public function get_detail_task(){
        $id_leads_timeline = $this->input->post('id_leads_timeline');
        $data = $this->leads_model->get_detail_task($id_leads_timeline);
        echo json_encode($data);
    }
    public function get_detail_fo(){
        $id_fo = $this->input->post('id_fo');
        $data = $this->leads_model->get_detail_fo($id_fo);
        echo json_encode($data);
    }
    public function get_detail_future(){
        $id_future_plan = $this->input->post('id_future_plan');
        $data = $this->leads_model->get_detail_future_plan($id_future_plan);
        echo json_encode($data);
    }

    public function get_detail_student(){
        $id_student = $this->input->post('id_student');
        $data = $this->leads_model->get_detail_student($id_student);
        echo json_encode($data);
    }

    public function get_detail_enrolment(){
        $id_class_student = $this->input->post('id_class_student');
        $data = $this->leads_model->get_detail_enrolment($id_class_student);
        echo json_encode($data);
    }
    public function get_detail_initial_contact(){
        $id_initial_contact = $this->input->post('id_initial_contact');
        $data = $this->leads_model->get_detail_initial_contact($id_initial_contact);
        echo json_encode($data);
    }
    public function get_english_requirement(){
        $data = $this->leads_model->get_english_requirement();
        echo json_encode($data);
    }
    public function get_employee(){
        $data = $this->leads_model->get_recruitment();
        echo json_encode($data);
    }
    public function get_discount(){
        $data = $this->leads_model->get_discount();
        echo json_encode($data);
    }
    public function get_document_requirement(){
        $id_category = $this->input->post('id_category');
        $data = $this->leads_model->get_document_requirement($id_category);
        echo json_encode($data);
    }
    public function get_academic_requirement(){
        $data = $this->leads_model->get_academic_requirement();
        echo json_encode($data);
    }
    public function get_intake(){
        $id_intended_program = $this->input->post('id_intended_program');
        $data = $this->leads_model->get_intake($id_intended_program);
        echo json_encode($data);
    }
    public function get_intake_month(){
        $id_intended_program = $this->input->post('id_intended_program');
        $data = $this->leads_model->get_intake_month($id_intended_program);
        echo json_encode($data);
    }
    
    public function get_tuition_fee(){
        $id_program_type = $this->input->post('id_program_type');
        $data = $this->leads_model->get_tuition_fee($id_program_type);
        echo json_encode($data);
    }
    public function cek_intake(){
        $intake_year = $this->input->post('intake_year');
        $id_intake_month = $this->input->post('id_intake_month');
        $data = $this->leads_model->cek_intake($intake_year, $id_intake_month);
        echo json_encode($data);
    }
    public function get_dms()
    {
        $ms = $this->input->post('ms');
        if($ms == 'event'){
            $data = $this->leads_model->get_event();
        } else if($ms == 'agent'){
            $data = $this->leads_model->get_agent();
        } else if($ms == 'marketing_source_detail'){
            $id_marketing_source = $this->input->post('id_marketing_source');
            $id_marketing_source_detail = $this->input->post('id_marketing_source_detail');
            $data = $this->leads_model->get_marketing_source_detail_for_leads($id_marketing_source, $id_marketing_source_detail);
        } 
        echo json_encode($data);
    }
    public function get_event()
    {
        $id_event_type = $this->input->post('id_event_type');
        $id_event = $this->input->post('id_event');
        $data = $this->leads_model->get_event_for_leads($id_event_type, $id_event);
        echo json_encode($data);
    }
    public function action_form()
    {
        $aksi = $this->input->post('aksi');
        if($aksi == 'add' || $aksi == 'detail' || $aksi == 'personal_detail'){
            $hasil = $this->leads_model->action_form($aksi);
            echo $hasil;
        } else if($aksi == 'family'){
            $hasil = $this->leads_model->action_form_family();
            echo $hasil;
        } else if($aksi == 'initial_contact'){

            $id_initial_contact = $this->input->post('id_initial_contact');
            $id_leads = $this->input->post('id_leads');
            $handled_by = $this->input->post('handled_by');
            $locked = $this->input->post('locked');
            $initial_date = $this->admin_model->format_tanggal_waktu($this->input->post('initial_date'));
            $hasil = $this->leads_model->action_form_initial_contact($id_initial_contact, $id_leads, $handled_by, $initial_date, $locked, 'initial');
            echo $hasil;
        } else if($aksi == 'student'){
            $hasil = $this->leads_model->action_form_student();
            echo $hasil;
        } else if($aksi == 'future_plan'){
            $hasil = $this->leads_model->action_form_future();
            echo $hasil;
        } else if($aksi == 'document'){
            $hasil = $this->leads_model->action_form_document();
            echo $hasil;
        } else if($aksi == 'payment'){
            $hasil = $this->leads_model->action_form_payment();
            echo $hasil;
        } else if($aksi == 'timeline'){
            $hasil = $this->leads_model->action_form_timeline();
            echo $hasil;
        } else if($aksi == 'academic_requirement'){
            $hasil = $this->leads_model->action_academic_requirement();
            echo $hasil;
        } else if($aksi == 'ol'){
            $hasil = $this->leads_model->action_form_ol();
            echo $hasil;
        } else if($aksi == 'manual_enrollment'){
            $hasil = $this->leads_model->action_manual_enrollment();
            echo $hasil;
        } else if($aksi == 'check_1a'){
            $hasil = $this->leads_model->action_check_1a();
            echo $hasil;
        } else if($aksi == 'enrollment'){
            $post = json_decode($this->input->post('data'));
            $id_student = $this->input->post('id_student');
            $student = $this->leads_model->get_detail_student($id_student);
            foreach ($post as $key) {
                 $hasil = $this->academic_master_model->add_class_student($id_student,$key->id_main_class);
            }

            $status = $this->leads_model->cek_status($this->input->post('id_leads'),0);
            $status = json_decode($status);

            $hasil['status_name'] = $status->status_name;
            $hasil['id_status'] = $status->id_status;
            $hasil['status_order'] = $status->status_order;
            
            echo $hasil;
        } else if($aksi == 'lt_task'){
            $hasil = $this->leads_model->action_check_task();
            echo $hasil;
        }
    }
    public function action_delete()
    {
        $hapus = $this->input->post('hapus_data');
        $id_hapus = $this->input->post('id_hapus');
        $hasil = $this->leads_model->action_delete($hapus, $id_hapus);
        echo $hasil;
    }
    public function delete_initial_contact(){
        $hasil = $this->leads_model->delete_initial_contact();
        echo $hasil;
    }
    public function cek_duplicate()
    {   
        $id_leads = $this->input->post('id_leads');
        $filter = $this->input->post('filter');
        $data = $this->leads_model->cek_duplicate($id_leads, $filter);
        echo json_encode($data);
    }
    public function okok()
    {
        $this->generate_ol('17','3','5','co');
    }
    public function generate_ol($id_ol = '', $id_intake = '', $id_course = '', $ol = '')
    {
        $course = $this->db->where('id_course', $id_course)
                           ->get('tb_course')
                           ->row();
        $intake = $this->db->where('id_intake', $id_intake)->get('tb_intake')->row();
        $semester = '';
        if($intake->id_trimester != ''){
            $semester = $this->db->where('id_trimester', $intake->id_trimester)->get('tb_trimester')->row()->trimester;
        } else {
            $semester = $this->db->where('id_semester', $intake->id_semester)->get('tb_semester')->row()->semester;
        }
        if($id_ol != ''){
            $query = $this->db->where('tb_offer_letter.id_ol !=', $id_ol);
        }
        $ol_number = 0;
        $query = $this->db->join('tb_leads','tb_leads.id_leads=tb_offer_letter.id_leads')
                         ->join('tb_course', 'tb_course.id_course = tb_leads.id_course')
                         ->where('tb_offer_letter.id_intake', $id_intake);
        if($course->id_intended_program != '3'){
            $query = $this->db->where('tb_offer_letter.id_course', $id_course);
        }
        
        $query = $this->db->where('ol_type', $ol)
                         ->order_by('ol_number', 'desc')
                         ->get('tb_offer_letter')
                         ->row();
        if($query != ''){
            $ol_number = $query->ol_number;
        }
        $last_number = (int)$ol_number + 1;
        $number = str_pad($last_number, 3, '0', STR_PAD_LEFT);

        if($course->id_intended_program != '3'){
            $ol_reference = $course->course_abstract.'/'.strtoupper($ol).'/'.$semester.'-'.date('y').'/'.date('my').'-'.$number;
        } else {
            $ol_reference = "Offer-$semester-".date('y')."/PDAP/".date('my')."-$number";
        }

        
        $hasil['ol_number'] = $last_number;
        $hasil['ol_reference'] = $ol_reference;

        return $hasil;
    }
    public function update_leads_status()
    {
        $data = $this->leads_model->update_leads_status();
        echo json_encode($data);        
    }
    public function update_custom_column()
    {
        $data = $this->leads_model->update_custom_column();
        echo json_encode($data);        
    }
    public function check_student_enrollment(){
        $id_student = $this->input->get('id_student');

        $query = $this->db->where('id_student', $id_student)
                          ->where('class_student_softdel',0)
                          ->get('tb_class_student')
                          ->num_rows();

        echo json_encode($query);

    }

    public function check_student_offer(){
        $id_student = $this->input->get('id_student');

        $offer = $this->leads_model->check_student_offer($id_student);

        if ($offer == null) {
            $display = 'No Offer';
        } else {
            if ($offer->document_type_status == '11') {
                $display = '<b>FO</b> : '.date('d-m-y',strtotime($offer->upload_date));
            } else {
                $display = '<b>CO</b> : '.date('d-m-y',strtotime($offer->upload_date));
            }
        }

        echo json_encode($display);

    }

    public function get_student_active($param = NULL) {
        $option = "";
        $option .= '<option value=""> -- Select Program -- </option>';
        $id_leads = $this->input->post('id_leads');
        $offer = $this->input->post('offer');
        if ($offer == '') {
            $result = $this->db->join('tb_course','tb_course.id_course = tb_student.id_course')
                               ->where('id_leads', $id_leads)
                               ->where('id_student_status','1')
                               ->where('student_active','1')
                               ->get('tb_student')
                               ->result();
            foreach ($result as $data) {
                $option .= "<option value='".$data->id_student."' >".$data->course."</option>";
            }
        } else {
            $result = $this->db->join('tb_student','tb_student.id_student = tb_document.id_student')
                           ->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                           ->join('tb_course','tb_course.id_course = tb_student.id_course')
                           ->join('tb_document_type','tb_document_type.id_document_type = tb_document.id_doc_type')
                           ->join('tb_status','tb_status.id_status = tb_leads.id_status')
                           ->where('tb_document.id_leads', $id_leads)
                           ->where('id_student_status','1')
                           ->where('document_type_status','3')
                           ->where('document_status','1')
                           ->where('status_order >',2)
                           ->where('student_active','1')
                           ->or_where('tb_document.id_leads', $id_leads)
                           ->where('id_student_status','1')
                           ->where('document_type_status','11')
                           ->where('document_status','1')
                           ->where('status_order >',2)
                           ->where('student_active','1')
                           ->group_by('tb_student.id_student')
                           ->get('tb_document')
                           ->result();
            foreach ($result as $data) {
                $option .= "<option value='".$data->id_document."' >".$data->course." - ".$data->document_type."</option>";
            }
        }
        
        echo $option;

    }
    public function get_fee_desc() {
        $id_fee_type = $this->input->post('id_fee_type');

        $result = $this->db->where('id_fee_type', $id_fee_type)
                           ->get('tb_fee_desc')
                           ->result();
        
        $option = "";
        $option .= '<option value=""> -- Select Fee -- </option>';
        foreach ($result as $data) {
            $option .= "<option value='".$data->id_fee_desc."' >".$data->fee_desc."</option>";
        }
        echo $option;

    }
    public function get_total_payment_document(){
        $id_leads_payment = $this->input->post('id_leads_payment');
        $document_type = $this->input->post('document_type');

        $data['total'] = $this->db->where('id_leads_payment', $id_leads_payment)
                         ->where('document_type', $document_type)
                         ->get('tb_leads_payment_document')
                         ->num_rows();

        echo json_encode($data);
    }
    public function assign_admission(){
        $aksi = $this->leads_model->assign_admission();
        echo $aksi;

        /*$post = json_decode($this->input->post('data'));

        echo 'a';


        foreach ($post as $key) {
            echo $key->id_leads;
            echo $key->leads_check;
        }*/
    }
    public function event($param1 = ''){
        if ($param1 == '') {
            $data['title'] = 'Recruitment';
            $data['left_bar'] = $this->admin_model->check_navbar();
            $data['campus'] = $this->master_model->get_campus();
            $data['employee'] = $this->leads_model->get_recruitment();
            $data['major'] = $this->marketing_master_model->get_major_by_leads_event();
            $data['destination'] = $this->marketing_master_model->get_destination_by_leads_event();
            $data['event_type'] = $this->marketing_master_model->get_event_type();
            $data['pagination_data'] = $this->leads_model->pagination_leads_event($this->input->get(), 'result');
            $data['pagination_total_page'] = $this->leads_model->pagination_leads_event($this->input->get(), 'num_rows');
            $this->load->view('Recruitment/Leads/unassigned_leads_view', $data);
        } elseif($param1 == 'export_leads_event_to_leads'){
            $aksi = $this->leads_model->export_leads_event_to_leads();
            echo $aksi;
        }
    }

   
}