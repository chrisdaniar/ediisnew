<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Academic/Academic_master_model','master_model');
		$this->load->model('Admin/Admin_master_model','admin_model');
		$this->load->model('Academic/Academic_report_model','report_model');
		$this->load->model('Recruitment/Recruitment_master_model','recruitment_model');
		$this->link_terakhir = $this->config->item('link_terakhir');

		if ($this->session->userdata('logged_in') == FALSE) {
			redirect('login');
		}
		
		ini_set('display_errors', 0);
	}

	public function enrollment_report(){
		$data['title'] = 'Enrollment Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['course'] = $this->master_model->get_course();
		$data['intended_program'] = $this->master_model->get_intended_program();
		$data['campus'] = $this->master_model->get_campus();
		$data['trimester'] = $this->report_model->get_trimester_group();
		$data['month'] = $this->recruitment_model->get_month();
		$this->load->view('Academic/Report/enrollment_report_view', $data);

	}

	public function display_enrollment_report(){
		 ini_set('display_errors', 0);
		 $id_intended_program = $this->input->post('id_intended_program');
		 $id_course = $this->input->post('id_course');
		 $id_trimester = $this->input->post('id_trimester');
		 $intake_start = $this->input->post('intake_start');
		 $intake_end = $this->input->post('intake_end');
		 $id_month_start = $this->input->post('id_month_start');
		 $id_month_end = $this->input->post('id_month_end');
		 $id_campus = $this->input->post('id_campus');
		 $start_time = $this->input->post('start_time');
		 $end_time = $this->input->post('end_time');
		 $fc_start = $this->input->post('fc_start');
		 $fc_end = $this->input->post('fc_end');
		 $id_leads = $this->input->post('id_leads');
		 $id_student_status = $this->input->post('id_student_status');
			 
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_trimester'] = $id_trimester;
			 $data['intake_start'] = $intake_start;
			 $data['intake_end'] = $intake_end;
			 $data['id_month_start'] = $id_month_start;
			 $data['id_month_end'] = $id_month_end;
			 $data['id_campus'] = $id_campus;
			 $data['start_time'] = $start_time;
			 $data['end_time'] = $end_time;
			 $data['fc_start'] = $fc_start;
			 $data['fc_end'] = $fc_end;
			 $data['export'] = 0;
			 $data['campus_check'] = $this->input->post('campus_check');
			 $data['program_check'] = $this->input->post('program_check');
			 $data['course_check'] = $this->input->post('course_check');
			 $data['specialist_check'] = $this->input->post('specialist_check');
			 $data['address_check'] = $this->input->post('address_check');
			 $data['dob_check'] = $this->input->post('dob_check');
			 $data['email_check'] = $this->input->post('email_check');
			 $data['phone_check'] = $this->input->post('phone_check');
			 $data['intake_check'] = $this->input->post('intake_check');
			 $data['nationality_check'] = $this->input->post('nationality_check');
			 $data['gender_check'] = $this->input->post('gender_check');
			 $data['subject_check'] = $this->input->post('subject_check');
			 $data['subject_list_check'] = $this->input->post('subject_list_check');
			 $data['form_check'] = $this->input->post('form_check');
			 $data['document_check'] = $this->input->post('document_check');
			 $data['family_check'] = $this->input->post('family_check');
			 $data['id_student_status_check'] = $this->input->post('id_student_status_check');
			 $data['finance_clearance_check'] = $this->input->post('finance_clearance_check');
			 $data['invitation_check'] = $this->input->post('invitation_check');
			 $data['username_check'] = $this->input->post('username_check');
			 $data['handled_by_check'] = $this->input->post('handled_by_check');

			 $data['form'] = $this->recruitment_model->get_form();
			 $data['document'] = $this->recruitment_model->get_document();
			 $data['month_start'] = $this->recruitment_model->get_month_by_id($id_month_start);
			 $data['month_end'] = $this->recruitment_model->get_month_by_id($id_month_end);
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['campus'] = $this->master_model->get_campus_by_id($id_campus);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['subject'] = $this->report_model->get_subject_for_enrollment($id_intended_program, $id_course, $id_trimester, $id_campus, $id_leads);
			 $data['student'] = $this->report_model->get_student_for_enrollment($id_intended_program, $id_course, $intake_start, $intake_end, $id_month_start, $id_month_end, $id_campus, $id_leads, $id_student_status);
	         $display = $this->load->view('Academic/Report/display_enrollment_report_view', $data);
	         
	         echo $display;
	}

	public function export_enrollment_report(){

		 ini_set('display_errors', 0);

		 $id_intended_program = $this->input->get('id_intended_program');
		 $id_course = $this->input->get('id_course');
		 $id_trimester = $this->input->get('id_trimester');
		 $intake_start = $this->input->get('intake_start');
		 $intake_end = $this->input->get('intake_end');
		 $id_month_start = $this->input->get('id_month_start');
		 $id_month_end = $this->input->get('id_month_end');
		 $id_campus = $this->input->get('id_campus');
		 $start_time = $this->input->get('start_time');
		 $end_time = $this->input->get('end_time');
		 $fc_start = $this->input->get('fc_start');
		 $fc_end = $this->input->get('fc_end');
		 $id_leads = $this->input->get('id_leads');
		 $id_student_status = $this->input->get('id_student_status');


		 $course = $this->master_model->get_course_by_id($id_course);
		 $trimester = $this->master_model->get_trimester_by_id($id_trimester);

		 header('Content-Type: application/vnd.ms-excel');  
         header('Content-disposition: attachment; filename='.$course->course_name.' Enrollment for '.$trimester->academic_year.' - '.$trimester->trimester.' ('.date('d-m-Y').').xls');

	         $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_trimester'] = $id_trimester;
			 $data['intake_start'] = $intake_start;
			 $data['intake_end'] = $intake_end;
			 $data['id_month_start'] = $id_month_start;
			 $data['id_month_end'] = $id_month_end;
			 $data['id_campus'] = $id_campus;
			 $data['start_time'] = $start_time;
			 $data['end_time'] = $end_time;
			 $data['fc_start'] = $fc_start;
			 $data['fc_end'] = $fc_end;
			 $data['export'] = 1;
			 $data['campus_check'] = $this->input->get('campus_check');
			 $data['program_check'] = $this->input->get('program_check');
			 $data['course_check'] = $this->input->get('course_check');
			 $data['specialist_check'] = $this->input->get('specialist_check');
			 $data['address_check'] = $this->input->get('address_check');
			 $data['dob_check'] = $this->input->get('dob_check');
			 $data['email_check'] = $this->input->get('email_check');
			 $data['phone_check'] = $this->input->get('phone_check');
			 $data['intake_check'] = $this->input->get('intake_check');
			 $data['nationality_check'] = $this->input->get('nationality_check');
			 $data['gender_check'] = $this->input->get('gender_check');
			 $data['subject_check'] = $this->input->get('subject_check');
			 $data['subject_list_check'] = $this->input->get('subject_list_check');
			 $data['form_check'] = $this->input->get('form_check');
			 $data['document_check'] = $this->input->get('document_check');
			 $data['family_check'] = $this->input->get('family_check');
			 $data['id_student_status_check'] = $this->input->get('id_student_status_check');
			 $data['finance_clearance_check'] = $this->input->get('finance_clearance_check');
			 $data['invitation_check'] = $this->input->get('invitation_check');
			 $data['username_check'] = $this->input->get('username_check');
			 $data['handled_by_check'] = $this->input->get('handled_by_check');

			 $data['form'] = $this->recruitment_model->get_form();
			 $data['document'] = $this->recruitment_model->get_document();
			 $data['month_start'] = $this->recruitment_model->get_month_by_id($id_month_start);
			 $data['month_end'] = $this->recruitment_model->get_month_by_id($id_month_end);
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['campus'] = $this->master_model->get_campus_by_id($id_campus);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['subject'] = $this->report_model->get_subject_for_enrollment($id_intended_program, $id_course, $id_trimester, $id_campus, $id_leads);
			 $data['student'] = $this->report_model->get_student_for_enrollment($id_intended_program, $id_course, $intake_start, $intake_end, $id_month_start, $id_month_end, $id_campus, $id_leads, $id_student_status);
			 $display = $this->load->view('Academic/Report/display_enrollment_report_view', $data);
	}

	public function attendance_report(){
		$data['title'] = 'Attendance Report';
		$data['acreditation'] = $this->admin_model->check_acreditation();
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$data['course'] = $this->master_model->get_course($data['acreditation']);
		$data['intended_program'] = $this->master_model->get_intended_program($data['acreditation']);
		$data['campus'] = $this->master_model->get_campus($data['acreditation']);
		$data['trimester'] = $this->report_model->get_trimester_group();
		$this->load->view('Academic/Report/attendance_report_view', $data);

	}

	public function get_main_class_by_filter() {
        $id_academic_year = $this->input->post('id_academic_year');
        $id_trimester = $this->input->post('id_trimester');
        $id_semester = $this->input->post('id_semester');
        $id_course = $this->input->post('id_course');
        $id_intended_program = $this->input->post('id_intended_program');
        $id_campus = $this->input->post('id_campus');

        $trimester = $this->master_model->get_trimester_by_id($id_trimester);
        $semester = $this->master_model->get_trimester_by_id($id_semester);

        $result = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                           ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                           ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure')
                           ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester')
                           ->join('tb_semester','tb_semester.id_semester=tb_main_class.id_semester','left')
                           ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year')
                           ->join('tb_course','tb_course.id_course=tb_course_structure.id_course');

                if ($id_academic_year != '') {
                	$result = $this->db->where('tb_academic_year.id_academic_year', $id_academic_year);
                }

                if ($id_trimester != '') {
                	$result = $this->db->where('tb_trimester.id_trimester', $id_trimester);
                }

                if ($id_semester != '') {
                	$result = $this->db->where('tb_semester.id_semester', $id_semester);
                }                    

                if ($id_intended_program != '') {
                    $result = $this->db->where('tb_course.id_intended_program', $id_intended_program);      
                }

                if ($id_course != '') {
                    $result = $this->db->where('tb_course.id_course', $id_course);      
                }

                if ($id_campus != '') {
                    $result = $this->db->where('tb_main_class.id_campus', $id_campus);      
                }

                    $result = $this->db->group_by('tb_subject.id_subject')
                    				   ->get('tb_main_class')
                                       ->result();

        $option = "";
        $option .= '<option value=""> -- Select Class --- </option>';
        foreach ($result as $data) {
            $option .= "<option value='".$data->id_main_class."' >".$data->subject_code." - ".$data->subject_name."</option>";
        }
        echo $option;
    }


	public function display_attendance_report(){

		 $id_campus = $this->input->post('id_campus');
		 $id_intended_program = $this->input->post('id_intended_program');
		 $id_course = $this->input->post('id_course');
		 $id_trimester = $this->input->post('id_trimester');
		 $id_main_class = $this->input->post('id_main_class');
		 $id_session = $this->input->post('id_session');
		 $attendance_status = $this->input->post('attendance_status');
		 $start_date = $this->input->post('start_date');
		 $end_date = $this->input->post('end_date');

			if ($id_intended_program != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_main_class'] = $id_main_class;
			 $data['id_session'] = $id_session;
			 $data['attendance_status'] = $attendance_status;
			 $data['start_date'] = $this->admin_model->format_tanggal($start_date);
			 $data['end_date'] = $this->admin_model->format_tanggal($end_date);
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['main_class'] = $this->report_model->get_class_for_attendance($id_campus,$id_intended_program, $id_course, $id_trimester, $id_main_class, $id_session);
	         $display = $this->load->view('Academic/Report/display_attendance_report_view', $data);
	         
	         echo $display;
     	}
	}

	public function export_attendance_report(){

		 $id_campus = $this->input->get('id_campus');
		 $id_intended_program = $this->input->get('id_intended_program');
		 $id_course = $this->input->get('id_course');
		 $id_trimester = $this->input->get('id_trimester');
		 $id_main_class = $this->input->get('id_main_class');
		 $id_session = $this->input->get('id_session');
		 $attendance_status = $this->input->get('attendance_status');
		 $start_date = $this->input->get('start_date');
		 $end_date = $this->input->get('end_date');
		 $id_campus_class = $this->input->get('id_campus_class');
		 $subject_code_class = $this->input->get('subject_code_class');

		 $intended_program = $this->master_model->get_intended_program_by_id($id_intended_program);
		 $course = $this->master_model->get_course_by_id($id_course);
		 $trimester = $this->master_model->get_trimester_by_id($id_trimester);


		 header('Content-Type: application/vnd.ms-excel');  
         header('Content-disposition: attachment; filename= '.$intended_program->intended_program_abstract.' '.$course->course_abstract.' Attendance for '.$trimester->academic_year.' - '.$trimester->trimester.' ('.date('d-m-Y').').xls');


			if ($id_intended_program != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_main_class'] = $id_main_class;
			 $data['id_session'] = $id_session;
			 $data['attendance_status'] = $attendance_status;
			 $data['id_campus_class'] = $id_campus_class;
			 $data['subject_code_class'] = $subject_code_class;
			 $data['start_date'] = $this->admin_model->format_tanggal($start_date);
			 $data['end_date'] = $this->admin_model->format_tanggal($end_date);
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['main_class'] = $this->report_model->get_class_for_attendance($id_campus,$id_intended_program, $id_course, $id_trimester, $id_main_class, $id_session);
	         $display = $this->load->view('Academic/Report/display_attendance_report_view', $data);
	        
     	}
	}


	public function tpr_report(){
		$data['title'] = 'TPR Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['acreditation'] = $this->admin_model->check_acreditation();
		$data['course'] = $this->master_model->get_course($data['acreditation']);
		$data['intended_program'] = $this->master_model->get_intended_program($data['acreditation']);
		$data['campus'] = $this->master_model->get_campus($data['acreditation']);
		$data['trimester'] = $this->report_model->get_trimester_group();
		$this->load->view('Academic/Report/tpr_report_view', $data);

	}

	public function display_tpr_report(){

		 $id_campus = $this->input->post('id_campus');
		 $id_intended_program = $this->input->post('id_intended_program');
		 $id_course = $this->input->post('id_course');
		 $id_trimester = $this->input->post('id_trimester');
		 $id_main_class = $this->input->post('id_main_class');
		 $id_session = $this->input->post('id_session');

			if ($id_intended_program != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_main_class'] = $id_main_class;
			 $data['id_session'] = $id_session;
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['main_class'] = $this->report_model->get_class_for_tpr($id_campus,$id_intended_program, $id_course, $id_trimester, $id_main_class, $id_session);
	         $display = $this->load->view('Academic/Report/display_tpr_report_view', $data);
	         
	         echo $display;
     	}
	}

	public function export_tpr_report(){

		 $id_campus = $this->input->get('id_campus');
		 $id_intended_program = $this->input->get('id_intended_program');
		 $id_course = $this->input->get('id_course');
		 $id_trimester = $this->input->get('id_trimester');
		 $id_main_class = $this->input->get('id_main_class');
		 $id_session = $this->input->get('id_session');


		 $intended_program = $this->master_model->get_intended_program_by_id($id_intended_program);
		 $course = $this->master_model->get_course_by_id($id_course);
		 $trimester = $this->master_model->get_trimester_by_id($id_trimester);

		 header('Content-Type: application/vnd.ms-excel');  
         header('Content-disposition: attachment; filename= '.$intended_program->intended_program_abstract.' '.$course->course_abstract.' TPR for '.$trimester->academic_year.' - '.$trimester->trimester.' ('.date('d-m-Y').').xls');

			if ($id_intended_program != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_main_class'] = $id_main_class;
			 $data['id_session'] = $id_session;
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['main_class'] = $this->report_model->get_class_for_tpr($id_campus,$id_intended_program, $id_course, $id_trimester, $id_main_class, $id_session);
	         $display = $this->load->view('Academic/Report/export_tpr_report_view', $data);
     	}
	}



	public function score_result_report(){
		$data['title'] = 'Score Result Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$data['course'] = $this->master_model->get_course();
		$data['intended_program'] = $this->master_model->get_intended_program();
		$data['campus'] = $this->master_model->get_campus();
		$data['trimester'] = $this->report_model->get_trimester_group();
		$this->load->view('Academic/Report/score_result_report_view', $data);

	}

	public function display_score_result_report(){

		 if ($this->input->post('view') == '') {
		 	$action = 'get';
		 } else {
		 	$action = 'post';
		 }

		 $id_campus = $this->input->$action('id_campus');
		 $id_intended_program = $this->input->$action('id_intended_program');
		 $id_course = $this->input->$action('id_course');
		 $id_academic_year = $this->input->$action('id_academic_year');
		 $id_trimester = $this->input->$action('id_trimester');
		 $id_semester = $this->input->$action('id_semester');
		 $id_main_class = $this->input->$action('id_main_class');
		 $id_session = $this->input->$action('id_session');
		 $view = $this->input->$action('view');

			if ($id_intended_program != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_academic_year'] = $id_academic_year;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_semester'] = $id_semester;
			 $data['id_main_class'] = $id_main_class;
			 $data['id_session'] = $id_session;
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['main_class'] = $this->report_model->get_class_for_score_result($id_campus, $id_intended_program,$id_course, $id_academic_year, $id_trimester, $id_semester, $id_main_class, $id_session);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Report/display_score_result_report_view', $data);
			 	echo $display;
			 } else {
			 	 header('Content-Type: application/vnd.ms-excel');  
        		 header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' Score for '.$data['trimester']->academic_year.' - '.$data['trimester']->trimester.' ('.date('d-m-Y').').xls');

			 	$display = $this->load->view('Academic/Report/display_score_result_report_view', $data);
			 }
     	}
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function historical_report(){
		$data['title'] = 'Historical Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['session'] = $this->master_model->get_session();
		$data['course'] = $this->master_model->get_course();
		$data['intended_program'] = $this->master_model->get_intended_program();
		$data['campus'] = $this->master_model->get_campus();
		$data['month'] = $this->recruitment_model->get_month();
		$data['trimester'] = $this->report_model->get_trimester_group();
		$this->load->view('Academic/Report/historical_report_view', $data);

	}

	public function display_historical_report(){

		 if ($this->input->post('view') == '') {
		 	$action = 'get';
		 } else {
		 	$action = 'post';
		 }

		 $id_campus = $this->input->$action('id_campus');
		 $id_intended_program = $this->input->$action('id_intended_program');
		 $id_course = $this->input->$action('id_course');
		 $id_trimester = $this->input->$action('id_trimester');
		 $id_main_class = $this->input->$action('id_main_class');
		 $id_session = $this->input->$action('id_session');

		 $id_student_status = $this->input->$action('id_student_status');

		 $id_leads = $this->input->$action('id_leads');
		 $intake_start = $this->input->$action('intake_start');
		 $intake_end = $this->input->$action('intake_end');
		 $id_month_start = $this->input->$action('id_month_start');
		 $id_month_end = $this->input->$action('id_month_end');

		 $view = $this->input->$action('view');

			if ($id_intended_program != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_main_class'] = $id_main_class;
			 $data['id_session'] = $id_session;
			 $data['view'] = $view;
			 $data['final_score_check'] = $this->input->$action('final_score_check');
			 $data['alphabet_score_check'] = $this->input->$action('alphabet_score_check');
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['course_structure'] = $this->report_model->get_subject_for_historical($id_campus, $id_intended_program, $id_course, $id_trimester, $id_main_class, $id_session, $id_leads);
			 $data['student'] = $this->report_model->get_student_for_enrollment($id_intended_program, $id_course, $intake_start, $intake_end, $id_month_start, $id_month_end, $id_campus, $id_leads, $id_student_status);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Report/display_historical_report_view', $data);
			 	echo $display;
			 } else {
			 	 header('Content-Type: application/vnd.ms-excel');  
        		 header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' Score for '.$data['trimester']->academic_year.' - '.$data['trimester']->trimester.' ('.date('d-m-Y').').xls');

			 	$display = $this->load->view('Academic/Report/display_historical_report_view', $data);
			 }
     	}
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function grade_report(){
		$data['title'] = 'Grade Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['course'] = $this->master_model->get_course();
		$data['intended_program'] = $this->master_model->get_intended_program();
		$data['campus'] = $this->master_model->get_campus();
		$data['trimester'] = $this->report_model->get_trimester_group();
		$this->load->view('Academic/Report/grade_report_view', $data);

	}

	public function display_grade_report(){

		 if ($this->input->post('view') == '') {
		 	$action = 'get';
		 } else {
		 	$action = 'post';
		 }

		 $id_campus = $this->input->$action('id_campus');
		 $id_intended_program = $this->input->$action('id_intended_program');
		 $id_course = $this->input->$action('id_course');
		 $id_trimester = $this->input->$action('id_trimester');
		 $id_leads = $this->input->$action('id_leads');
		 $view = $this->input->$action('view');
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_leads'] = $id_leads;
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['student'] = $this->report_model->get_student_for_score($id_campus,$id_intended_program, $id_course, $id_trimester, $id_leads);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Report/display_grade_report_view', $data);
			 	echo $display;
			 } else {
			 	 header('Content-Type: application/vnd.ms-excel');  
        		 header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' Score for '.$data['trimester']->academic_year.' - '.$data['trimester']->trimester.' ('.date('d-m-Y').').xls');

			 	$display = $this->load->view('Academic/Report/display_grade_report_view', $data);
			 }
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function transcript_report2(){
		$data['title'] = 'Transcript Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['course'] = $this->master_model->get_course();
		$data['intended_program'] = $this->master_model->get_intended_program();
		$data['campus'] = $this->master_model->get_campus();
		$data['trimester'] = $this->report_model->get_trimester_group();
		$this->load->view('Academic/Report/transcript_view_report', $data);

	}

	public function display_transcript_report2(){

		 if ($this->input->post('view') == '') {
		 	$action = 'get';
		 } else {
		 	$action = 'post';
		 }

		 $id_campus = $this->input->$action('id_campus');
		 $id_intended_program = $this->input->$action('id_intended_program');
		 $id_course = $this->input->$action('id_course');
		 $id_leads = $this->input->$action('id_leads');
		 $view = $this->input->$action('view');
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_leads'] = $id_leads;
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['student'] = $this->report_model->get_student_for_transcript($id_campus,$id_intended_program, $id_course, $id_leads);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Report/display_transcript_report_view', $data);
			 	echo $display;
			 } else {
			 	 header('Content-Type: application/vnd.ms-excel');  
        		 header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' Score for '.$data['trimester']->academic_year.' - '.$data['trimester']->trimester.' ('.date('d-m-Y').').xls');

			 	$display = $this->load->view('Academic/Report/display_transcript_report_view', $data);
			 }
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function class_report(){
		$data['title'] = 'Class Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['course'] = $this->master_model->get_course();
		$data['subject'] = $this->master_model->get_subject();
		$data['intended_program'] = $this->master_model->get_intended_program();
		$data['campus'] = $this->master_model->get_campus();
		$data['trimester'] = $this->report_model->get_trimester_group();
		$data['month'] = $this->recruitment_model->get_month();
		$this->load->view('Academic/Report/class_report_view', $data);

	}

	public function display_class_report(){
		 
		 $id_intended_program = $this->input->post('id_intended_program');
		 $id_course = $this->input->post('id_course');
		 $id_trimester = $this->input->post('id_trimester');
		 $id_campus = $this->input->post('id_campus');
		 $start_time = $this->input->post('start_time');
		 $end_time = $this->input->post('end_time');
		 $id_subject = $this->input->post('id_subject');
			 
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_subject'] = $id_subject;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_campus'] = $id_campus;
			 $data['start_time'] = $start_time;
			 $data['end_time'] = $end_time;

			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['campus'] = $this->master_model->get_campus_by_id($id_campus);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['main_class'] = $this->report_model->get_main_class_for_class($id_intended_program, $id_course, $id_trimester, $id_campus, $id_subject);
	         $display = $this->load->view('Academic/Report/display_class_report_view', $data);
	         
	         echo $display;
	}

	public function export_class_report(){

		 $id_intended_program = $this->input->get('id_intended_program');
		 $id_course = $this->input->get('id_course');
		 $id_trimester = $this->input->get('id_trimester');
		 $id_campus = $this->input->get('id_campus');
		 $start_time = $this->input->get('start_time');
		 $end_time = $this->input->get('end_time');
		 $id_subject = $this->input->get('id_subject');

		 $intended_program = $this->master_model->get_intended_program_by_id($id_intended_program);
		 $course = $this->master_model->get_course_by_id($id_course);
		 $trimester = $this->master_model->get_trimester_by_id($id_trimester);

		 header('Content-Type: application/vnd.ms-excel');  
         header('Content-disposition: attachment; filename= '.$intended_program->intended_program_abstract.' '.$course->course_abstract.' Class for '.$trimester->academic_year.' - '.$trimester->trimester.' ('.date('d-m-Y').').xls');

			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_subject'] = $id_subject;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_campus'] = $id_campus;
			 $data['start_time'] = $start_time;
			 $data['end_time'] = $end_time;

			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['campus'] = $this->master_model->get_campus_by_id($id_campus);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['main_class'] = $this->report_model->get_main_class_for_class($id_intended_program, $id_course, $id_trimester, $id_campus, $id_subject);
	         $display = $this->load->view('Academic/Report/export_class_report_view', $data);
	}

	public function claim_form_report(){
		$data['title'] = 'Claim Form Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['employee'] = $this->master_model->get_employee_by_id($this->session->userdata('id_employee'));
		$data['teacher'] = $this->master_model->get_teacher();
		$data['intended_program'] = $this->master_model->get_intended_program();
		$data['employee_type'] = $this->master_model->get_employee_type();
		$data['campus'] = $this->master_model->get_campus();
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$this->load->view('Academic/Report/claim_form_report_view', $data);

	}

	public function check_claim_form(){
		$aksi = $this->report_model->check_claim_form();
		echo $aksi;
	}

	public function display_claim_form_report(){
		 
		 $id_campus = $this->input->post('id_campus');
		 $id_employee = $this->input->post('id_employee');
		 $id_teaching_period = $this->input->post('id_teaching_period');
		 $claim_form_view = $this->input->post('claim_form_view');
		 $id_employee_type = $this->input->post('id_employee_type');
		 $id_intended_program = $this->input->post('id_intended_program');
		 $data = json_decode($this->input->post('data'));

		 if ($id_teaching_period != null) {

		 	 $teaching_period = $this->master_model->get_teaching_period_by_id($id_teaching_period);
			 
			 $data['intended_program_filter'] = $this->master_model->get_intended_program_by_filter($data);
			 $data['id_employee'] = $id_employee;
			 $data['id_campus'] = $id_campus;
			 $data['id_teaching_period'] = $id_teaching_period;
			 $data['id_intended_program'] = $id_intended_program;

			 $data['teaching_period'] = $this->master_model->get_teaching_period_by_id($id_teaching_period);

			 if ($teaching_period == null) {
			 	$data['start_date'] = '';
			 	$data['end_date'] = '';
			 } else {
				$data['start_date'] = $teaching_period->teaching_period_start;
			 	$data['end_date'] = $teaching_period->teaching_period_end;
			 }

			 $data['campus'] = $this->master_model->get_campus_by_id($id_campus);
			 $data['teacher'] = $this->master_model->get_employee_by_id($id_employee);
			 $data['claim_form'] = $this->report_model->get_teacher_for_claim_form($id_campus, $id_employee, $teaching_period->teaching_period_start, $teaching_period->teaching_period_end, $claim_form_view, $id_employee_type, $data['intended_program_filter']);

			 if ($claim_form_view == 'Details') {
			 	$display = $this->load->view('Academic/Report/display_claim_form_report_view', $data);
			 } else {
			 	$display = $this->load->view('Academic/Report/display_claim_form_program_report_view', $data);
			 }
	         
	         echo $display;
	    }
	}

	public function export_claim_form_report(){

		 $id_campus = $this->input->get('id_campus');
		 $id_employee = $this->input->get('id_employee');
		 $id_teaching_period = $this->input->get('id_teaching_period');
		 $claim_form_view = $this->input->get('claim_form_view');
		 $id_employee_type = $this->input->get('id_employee_type');
		 $id_intended_program = $this->input->get('id_intended_program');
		 $data = json_decode($this->input->get('data'));

		 if ($id_teaching_period != null) {

		     header('Content-Type: application/vnd.ms-excel');  
             header('Content-disposition: attachment; filename= claim_form.xls');
			 
			 $teaching_period = $this->master_model->get_teaching_period_by_id($id_teaching_period);
			 
			 $data['intended_program_filter'] = $this->master_model->get_intended_program_by_filter($data);
			 $data['id_employee'] = $id_employee;
			 $data['id_campus'] = $id_campus;
			 $data['id_teaching_period'] = $id_teaching_period;
			 $data['id_intended_program'] = $id_intended_program;

			 $data['teaching_period'] = $this->master_model->get_teaching_period_by_id($id_teaching_period);

			 if ($teaching_period == null) {
			 	$data['start_date'] = '';
			 	$data['end_date'] = '';
			 } else {
				$data['start_date'] = $teaching_period->teaching_period_start;
			 	$data['end_date'] = $teaching_period->teaching_period_end;
			 }

			 $data['campus'] = $this->master_model->get_campus_by_id($id_campus);
			 $data['teacher'] = $this->master_model->get_employee_by_id($id_employee);
			 $data['claim_form'] = $this->report_model->get_teacher_for_claim_form($id_campus, $id_employee, $teaching_period->teaching_period_start, $teaching_period->teaching_period_end, $claim_form_view, $id_employee_type, $data['intended_program_filter']);

			 if ($claim_form_view == 'Details') {
			 	$display = $this->load->view('Academic/Report/export_claim_form_report_view', $data);
			 } else {
			 	$display = $this->load->view('Academic/Report/display_claim_form_program_report_view', $data);
			 }
	         
	    }
	}

	//-----------------------------------------------LOCAAAALLL-------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function krs_report(){
		$data['title'] = 'KRS Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['acreditation'] = $this->admin_model->check_acreditation();
		$data['course'] = $this->master_model->get_course($data['acreditation']);
		$data['session'] = $this->master_model->get_session();
		$data['intended_program'] = $this->master_model->get_intended_program($data['acreditation']);
		$data['campus'] = $this->master_model->get_campus($data['acreditation']);
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$data['trimester_number'] = $this->report_model->get_trimester_number();
		$data['semester_number'] = $this->report_model->get_semester_number();
		$this->load->view('Academic/Local_report/krs_report_view', $data);
	}

	//-----------------------------------------------LOCAAAALLL-------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	
	public function grade_local_report(){
		$data['title'] = 'Grade Report';
		$data['acreditation'] = $this->admin_model->check_acreditation();
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['course'] = $this->master_model->get_course($data['acreditation']);
		$data['session'] = $this->master_model->get_session();
		$data['intended_program'] = $this->master_model->get_intended_program($data['acreditation']);
		$data['campus'] = $this->master_model->get_campus($data['acreditation']);
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$data['trimester_number'] = $this->report_model->get_trimester_number();
		$data['semester_number'] = $this->report_model->get_semester_number();
		$this->load->view('Academic/Local_report/grade_local_report_view', $data);
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function khs_report(){
		$data['title'] = 'KHS Report';
		$data['acreditation'] = $this->admin_model->check_acreditation();
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['intake_year'] = $this->report_model->get_intake_year();
		$data['course'] = $this->master_model->get_course($data['acreditation']);
		$data['session'] = $this->master_model->get_session();
		$data['intended_program'] = $this->master_model->get_intended_program($data['acreditation']);
		$data['campus'] = $this->master_model->get_campus($data['acreditation']);
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$data['trimester_number'] = $this->report_model->get_trimester_number();
		$data['semester_number'] = $this->report_model->get_semester_number();
		$this->load->view('Academic/Local_report/khs_report_view', $data);

	}

	//-----------------------------------------------LOCAAAALLL-------------------------------------------------------------------------------------------------------------------------------------------------------------------//



	public function display_krs_report(){

		 if ($this->input->post('view') == '') {
		 	$action = 'get';
		 } else {
		 	$action = 'post';
		 }


		 $id_campus = $this->input->$action('id_campus');
		 $id_intended_program = $this->input->$action('id_intended_program');
		 $id_course = $this->input->$action('id_course');
		 $academic_year = $this->input->$action('academic_year');
		 $trimester = $this->input->$action('trimester');
		 $semester = $this->input->$action('semester');
		 $id_leads = $this->input->$action('id_leads');
		 $id_session = $this->input->$action('id_session');
		 $intake_year = $this->input->$action('intake_year');
		 $view = $this->input->$action('view');
		 $view_type = $this->input->$action('view_type');
		 $export_type = $this->input->$action('export_type');
		 $report_type = $this->input->$action('report_type');
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['trimester'] = $trimester;
			 $data['semester'] = $semester;
			 $data['id_leads'] = $id_leads;
			 $data['view'] = $view;
			 $data['view_type'] = $view_type;
			 $data['export_type'] = $export_type;
			 $data['report_type'] = $report_type;
			 $data['intake_year'] = $intake_year;
			 $data['academic_year'] = $this->report_model->get_academic_year_by_academic_year($academic_year);
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['student'] = $this->report_model->get_student_for_krs($id_campus,$id_intended_program, $id_course, $id_session, $intake_year, $id_leads, $academic_year, $trimester, $semester);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Local_report/display_krs_report_view', $data);
			 	echo $display;
			 } else {

			 	if ($export_type == 'excel') {
			 		header('Content-Type: application/vnd.ms-excel');  
        			header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' KRS for '.$data['trimester']->academic_year.' - '.$data['trimester']->trimester.' ('.date('d-m-Y').').xls');
			 	}

			 	$display = $this->load->view('Academic/Local_report/display_krs_report_view', $data);
			 }
	}

	//-----------------------------------------------LOCAAAALLL-------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function transcript_local_report(){
		$data['title'] = 'Transcript Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['course'] = $this->master_model->get_course();
		$data['session'] = $this->master_model->get_session();
		$data['intended_program'] = $this->master_model->get_intended_program();
		$data['campus'] = $this->master_model->get_campus();
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$data['trimester_number'] = $this->report_model->get_trimester_number();
		$data['semester_number'] = $this->report_model->get_semester_number();
		$this->load->view('Academic/Local_report/transcript_local_report_view', $data);
	}

	public function display_transcript_local_report(){

		 if ($this->input->post('view') == '') {
		 	$action = 'get';
		 } else {
		 	$action = 'post';
		 }

		 $id_campus = $this->input->$action('id_campus');
		 $id_intended_program = $this->input->$action('id_intended_program');
		 $id_course = $this->input->$action('id_course');
		 $id_leads = $this->input->$action('id_leads');
		 $id_session = $this->input->$action('id_session');
		 $intake_year = $this->input->$action('intake_year');
		 $view = $this->input->$action('view');
		 $export_type = $this->input->$action('export_type');
		 $report_type = $this->input->$action('report_type');
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_leads'] = $id_leads;
			 $data['view'] = $view;
			 $data['view_type'] = $view_type;
			 $data['export_type'] = $export_type;
			 $data['report_type'] = $report_type;
			 $data['intake_year'] = $intake_year;
			 $data['academic_year'] = $this->report_model->get_academic_year_by_academic_year($academic_year);
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['student'] = $this->report_model->get_student_for_transcript($id_campus,$id_intended_program, $id_course, $id_session, $intake_year, $id_leads);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Local_report/display_transcript_local_report_view', $data);
			 	echo $display;
			 } else {

			 	if ($export_type == 'excel') {
			 		header('Content-Type: application/vnd.ms-excel');  
        			header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' KRS for '.$data['trimester']->academic_year.' - '.$data['trimester']->trimester.' ('.date('d-m-Y').').xls');
			 	}

			 	$display = $this->load->view('Academic/Local_report/display_transcript_local_report_view', $data);
			 }
	}


	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function score_report(){
		$data['title'] = 'Score Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['acreditation'] = $this->admin_model->check_acreditation();
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$data['intended_program'] = $this->master_model->get_intended_program($data['acreditation'] );
		$data['campus'] = $this->master_model->get_campus($data['acreditation'] );
		$this->load->view('Academic/Local_report/score_report_view', $data);

	}

	public function display_score_report(){

		 if ($this->input->post('view') == '') {
		 	$action = 'get';
		 } else {
		 	$action = 'post';
		 }

		 $id_campus = $this->input->$action('id_campus');
		 $id_intended_program = $this->input->$action('id_intended_program');
		 $id_course = $this->input->$action('id_course');
		 $id_academic_year = $this->input->$action('id_academic_year');
		 $id_trimester = $this->input->$action('id_trimester');
		 $id_semester = $this->input->$action('id_semester');
		 $id_main_class = $this->input->$action('id_main_class');
		 $id_session = $this->input->$action('id_session');
		 $view = $this->input->$action('view');

			if ($id_intended_program != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_academic_year'] = $id_academic_year;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_semester'] = $id_semester;
			 $data['id_main_class'] = $id_main_class;
			 $data['id_session'] = $id_session;
			 $data['view'] = $view;
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['main_class'] = $this->report_model->get_class_for_score_result($id_campus, $id_intended_program,$id_course, $id_academic_year, $id_trimester, $id_semester, $id_main_class, $id_session);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Local_report/display_score_report_view', $data);
			 	echo $display;
			 } else {
			 	 header('Content-Type: application/vnd.ms-excel');  
        		 header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' Score for '.$data['trimester']->academic_year.' - '.$data['trimester']->trimester.' ('.date('d-m-Y').').xls');

			 	$display = $this->load->view('Academic/Local_report/display_score_report_view', $data);
			 }
     	}
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//


	public function tpr_local_report(){
		$data['title'] = 'TPR Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['acreditation'] = $this->admin_model->check_acreditation();
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$data['intended_program'] = $this->master_model->get_intended_program($data['acreditation']);
		$data['campus'] = $this->master_model->get_campus($data['acreditation']);
		$this->load->view('Academic/Local_report/tpr_local_report_view', $data);

	}

	public function display_tpr_local_report(){

			 if ($this->input->post('view') == '') {
			 	$action = 'get';
			 } else {
			 	$action = 'post';
			 }

			 $id_campus = $this->input->$action('id_campus');
			 $id_intended_program = $this->input->$action('id_intended_program');
			 $id_course = $this->input->$action('id_course');
			 $id_academic_year = $this->input->$action('id_academic_year');
			 $id_trimester = $this->input->$action('id_trimester');
			 $id_semester = $this->input->$action('id_semester');
			 $id_main_class = $this->input->$action('id_main_class');
			 $id_session = $this->input->$action('id_session');
			 $view = $this->input->$action('view');

			if ($id_intended_program != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_main_class'] = $id_main_class;
			 $data['id_session'] = $id_session;
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['main_class'] = $this->report_model->get_class_for_tpr_local($id_campus,$id_intended_program, $id_course, $id_academic_year, $id_semester, $id_trimester, $id_main_class, $id_session);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Local_report/display_tpr_local_report_view', $data);
			 	echo $display;
			 } else {
			 	 header('Content-Type: application/vnd.ms-excel');  
        		 header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' TPR Local Report for '.$data['trimester']->academic_year.' - '.$data['trimester']->trimester.' ('.date('d-m-Y').').xls');

			 	$display = $this->load->view('Academic/Local_report/display_tpr_local_report_view', $data);
			 }
     	}
	}

	public function export_tpr_local_report(){

		 $id_campus = $this->input->get('id_campus');
		 $id_intended_program = $this->input->get('id_intended_program');
		 $id_course = $this->input->get('id_course');
		 $id_trimester = $this->input->get('id_trimester');
		 $id_main_class = $this->input->get('id_main_class');
		 $id_session = $this->input->get('id_session');


		 $intended_program = $this->master_model->get_intended_program_by_id($id_intended_program);
		 $course = $this->master_model->get_course_by_id($id_course);
		 $trimester = $this->master_model->get_trimester_by_id($id_trimester);

		 header('Content-Type: application/vnd.ms-excel');  
         header('Content-disposition: attachment; filename= '.$intended_program->intended_program_abstract.' '.$course->course_abstract.' TPR for '.$trimester->academic_year.' - '.$trimester->trimester.' ('.date('d-m-Y').').xls');

			if ($id_intended_program != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_main_class'] = $id_main_class;
			 $data['id_session'] = $id_session;
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['trimester'] = $this->master_model->get_trimester_by_id($id_trimester);
			 $data['main_class'] = $this->report_model->get_class_for_tpr($id_campus,$id_intended_program, $id_course, $id_trimester, $id_main_class, $id_session);
	         $display = $this->load->view('Academic/Local_report/export_tpr_report_view', $data);
     	}
	}


	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//


	public function teacher_recap_report(){
		$data['title'] = 'Teacher Recap Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['acreditation'] = $this->admin_model->check_acreditation();
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$data['intended_program'] = $this->master_model->get_intended_program($data['acreditation']);
		$data['campus'] = $this->master_model->get_campus($data['acreditation']);
		$data['teacher'] = $this->master_model->get_teacher($data['acreditation']);
		$this->load->view('Academic/Local_report/teacher_recap_report_view', $data);

	}

	public function display_teacher_recap_report(){

			 if ($this->input->post('view') == '') {
			 	$action = 'get';
			 } else {
			 	$action = 'post';
			 }

			 $id_campus = $this->input->$action('id_campus');
			 $id_intended_program = $this->input->$action('id_intended_program');
			 $id_course = $this->input->$action('id_course');
			 $id_academic_year = $this->input->$action('id_academic_year');
			 $id_trimester = $this->input->$action('id_trimester');
			 $id_semester = $this->input->$action('id_semester');
			 $id_employee = $this->input->$action('id_employee');

			 $view = $this->input->$action('view');

			if ($id_intended_program != '' && $id_semester != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_semester'] = $id_semester;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_employee'] = $id_employee;
			 $data['id_academic_year'] = $id_academic_year;
			 $data['semester'] = $this->master_model->get_semester_by_id($id_semester);
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['teacher'] = $this->report_model->get_teacher_recap($id_campus,$id_intended_program, $id_course, $id_academic_year, $id_semester, $id_trimester, $id_employee);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Local_report/display_teacher_recap_report_view', $data);
			 	echo $display;
			 } else {
			 	 header('Content-Type: application/vnd.ms-excel');  
        		 header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' Teacher Recap for '.$data['semester']->academic_year.' - '.$data['semester']->semester_name.'.xls');

			 	$display = $this->load->view('Academic/Local_report/display_teacher_recap_report_view', $data);
			 }
     	}
	}


	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//


	public function attendance_official_report(){
		$data['title'] = 'Attendance Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['acreditation'] = $this->admin_model->check_acreditation();
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$data['intended_program'] = $this->master_model->get_intended_program($data['acreditation']);
		$data['campus'] = $this->master_model->get_campus($data['acreditation']);
		$data['teacher'] = $this->master_model->get_teacher($data['acreditation']);
		$this->load->view('Academic/Local_report/attendance_official_report_view', $data);

	}

	public function display_attendance_official_report(){

			 if ($this->input->post('view') == '') {
			 	$action = 'get';
			 } else {
			 	$action = 'post';
			 }

			 $id_campus = $this->input->$action('id_campus');
			 $id_intended_program = $this->input->$action('id_intended_program');
			 $id_course = $this->input->$action('id_course');
			 $id_academic_year = $this->input->$action('id_academic_year');
			 $id_trimester = $this->input->$action('id_trimester');
			 $id_semester = $this->input->$action('id_semester');
			 $id_employee = $this->input->$action('id_employee');
			 $id_main_class = $this->input->$action('id_main_class');

			 $view = $this->input->$action('view');

			if ($id_intended_program != '' && $id_semester != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_semester'] = $id_semester;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_employee'] = $id_employee;
			 $data['id_academic_year'] = $id_academic_year;
			 $data['id_main_class'] = $id_main_class;

			 $data['semester'] = $this->master_model->get_semester_by_id($id_semester);
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['main_class'] = $this->report_model->get_official_class_for_attendance($id_campus, $id_intended_program,$id_course, $id_academic_year, $id_trimester, $id_semester, $id_main_class, $id_employee);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Local_report/display_attendance_official_report_view', $data);
			 	echo $display;
			 } else {
			 	 header('Content-Type: application/vnd.ms-excel');  
        		 header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' Teacher Recap for '.$data['semester']->academic_year.' - '.$data['semester']->semester_name.'.xls');

			 	$display = $this->load->view('Academic/Local_report/display_attendance_official_report_view', $data);
			 }
     	}
	}

	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//


	public function score_official_report(){
		$data['title'] = 'Score Official Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['acreditation'] = $this->admin_model->check_acreditation();
		$data['academic_year'] = $this->recruitment_model->get_academic_year();
		$data['intended_program'] = $this->master_model->get_intended_program($data['acreditation']);
		$data['campus'] = $this->master_model->get_campus($data['acreditation']);
		$data['teacher'] = $this->master_model->get_teacher($data['acreditation']);
		$this->load->view('Academic/Local_report/score_official_report_view', $data);

	}

	public function display_score_official_report(){

			 if ($this->input->post('view') == '') {
			 	$action = 'get';
			 } else {
			 	$action = 'post';
			 }

			 $id_campus = $this->input->$action('id_campus');
			 $id_intended_program = $this->input->$action('id_intended_program');
			 $id_course = $this->input->$action('id_course');
			 $id_academic_year = $this->input->$action('id_academic_year');
			 $id_trimester = $this->input->$action('id_trimester');
			 $id_semester = $this->input->$action('id_semester');
			 $id_employee = $this->input->$action('id_employee');
			 $id_main_class = $this->input->$action('id_main_class');

			 $view = $this->input->$action('view');

			if ($id_intended_program != '' && $id_semester != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['id_semester'] = $id_semester;
			 $data['id_trimester'] = $id_trimester;
			 $data['id_employee'] = $id_employee;
			 $data['id_academic_year'] = $id_academic_year;
			 $data['id_main_class'] = $id_main_class;
			 $data['view'] = $view;

			 $data['semester'] = $this->master_model->get_semester_by_id($id_semester);
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['main_class'] = $this->report_model->get_official_class_for_attendance($id_campus, $id_intended_program,$id_course, $id_academic_year, $id_trimester, $id_semester, $id_main_class, $id_employee);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Local_report/display_score_official_report_view', $data);
			 	echo $display;
			 } else {
			 	 header('Content-Type: application/vnd.ms-excel');  
        		 header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' Teacher Recap for '.$data['semester']->academic_year.' - '.$data['semester']->semester_name.'.xls');

			 	$display = $this->load->view('Academic/Local_report/display_score_official_report_view', $data);
			 }
     	}
	}

	

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function student_status_report(){
		$data['title'] = 'Score Result Report';
		$data['left_bar'] = $this->admin_model->check_navbar();
		$data['course'] = $this->master_model->get_course();
		$data['intended_program'] = $this->master_model->get_intended_program();
		$data['session'] = $this->master_model->get_session();
		$data['campus'] = $this->master_model->get_campus();
		$data['student_status'] = $this->master_model->get_student_status();
		$this->load->view('Academic/Report/student_status_report_view', $data);

	}

	public function display_student_status_report(){

		 if ($this->input->post('view') == '') {
		 	$action = 'get';
		 } else {
		 	$action = 'post';
		 }

		 $id_campus = $this->input->$action('id_campus');
		 $id_intended_program = $this->input->$action('id_intended_program');
		 $id_course = $this->input->$action('id_course');
		 $start_date = $this->input->$action('start_date');
		 $end_date = $this->input->$action('end_date');
		 $id_session = $this->input->$action('id_session');
		 $id_student_status = $this->input->$action('id_student_status');
		 $view = $this->input->$action('view');

			if ($id_intended_program != '') {
			 
			 $data['id_campus'] = $id_campus;
			 $data['id_intended_program'] = $id_intended_program;
			 $data['id_course'] = $id_course;
			 $data['start_date'] = $start_date;
			 $data['end_date'] = $end_date;
			 $data['id_session'] = $id_session;
			 $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
			 $data['course'] = $this->master_model->get_course_by_id($id_course);
			 $data['student_status'] = $this->master_model->get_student_status_by_id($id_student_status);
			 $data['student'] = $this->report_model->get_student_status_result($id_campus, $id_intended_program,$id_course, $start_date, $end_date, $id_session, $id_student_status);

			 if ($view == 'Display') {
			 	$display = $this->load->view('Academic/Report/display_student_status_report_view', $data);
			 	echo $display;
			 } else {
			 	 header('Content-Type: application/vnd.ms-excel');  
        		 header('Content-disposition: attachment; filename= '.$data['intended_program']->intended_program_abstract.' '.$data['course']->course_abstract.' Score for '.$data['trimester']->academic_year.' - '.$data['trimester']->trimester.' ('.date('d-m-Y').').xls');

			 	$display = $this->load->view('Academic/Report/display_student_status_report_view', $data);
			 }
     	}
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//


	public function preview($param1 = '', $param2 = ''){

		if ($param1 == 'stars') {
			$data['title'] = 'Stars';
			$id_emergency_form = $this->uri->segment(5);
			$emergency_form = $this->student_model->get_emergency_form_by_id($id_emergency_form);
			$data['intended_program'] = $this->academic_master_model->get_intended_program();
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "Siblings Agreement - ".$emergency_form->student_name." (".$emergency_form->intended_program.").pdf";
			$this->pdf->preview_view('Student/Form/preview_siblings_agreement_view', $data);
		} 
	}



	
}
