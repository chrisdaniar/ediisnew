<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Pdf','pdf');
		$this->load->helper('path');
		$this->load->model('Recruitment/Recruitment_master_model','recruitment_master_model');
		$this->load->model('Recruitment/Leads_model','leads_model');
		$this->load->model('Academic/Academic_master_model','academic_master_model');
		$this->load->model('Admin/Admin_master_model','admin_master_model');
		$this->load->model('Student/Student_model','student_model');

		ini_set('display_errors', 0);

		if ($this->session->userdata('logged_in') == FALSE) {
			redirect('login');
		}
	}

	public function personal_detail($param1 = '', $param2 = '')
    {
    	if ($param1 == '') {
    		$data['title'] = 'Leads';
    		$id_leads = $this->session->userdata('id_leads');
    		$data['acreditation'] = $this->admin_model->check_acreditation();
	        $data['left_bar'] = $this->admin_master_model->check_navbar();
	        $data['id_leads'] = $id_leads;
	        $data['session'] = $this->academic_master_model->get_session();
	        $data['leads'] = $this->leads_model->get_detail_leads($id_leads);
	        $data['gender'] = $this->leads_model->get_gender();
	        $data['country'] = $this->leads_model->get_country();
	        $data['recruitment'] = $this->leads_model->get_recruitment();
	        $data['province'] = $this->leads_model->get_province();
	        $data['program'] = $this->leads_model->get_program();
	        $data['school'] = $this->leads_model->get_school();
	        $data['class'] = $this->leads_model->get_class();
	        $data['campus'] = $this->leads_model->get_campus();
	        $data['trimester'] = $this->leads_model->get_trimester();
	        $data['qualification'] = $this->leads_model->get_qualification();
	        $data['marketing_source'] = $this->leads_model->get_marketing_source();
	        $data['initial_activity'] = $this->leads_model->get_initial_activity();
	        $data['status_nonactive'] = $this->leads_model->get_status(2);
	        $data['form'] = $this->leads_model->get_form();
	        $data['phone_code'] = $this->leads_model->get_phone_code();
	        $this->load->view('Recruitment/Leads/detail_leads_view', $data);
    	}
    }

	public function form($param1 = '', $param2 = ''){

		if ($param1 == '') {
			$data['title'] = 'JIC Online Form';
			$data['left_bar'] = $this->admin_master_model->check_navbar();
		    $this->load->view('Student/Form/online_form_view', $data);	
		} elseif ($param1 == 'siblings_agreement') {
			$data['title'] = 'Sibling Agreement';
			$data['intended_program'] = $this->academic_master_model->get_intended_program();
			$data['left_bar'] = $this->admin_master_model->check_navbar();
	        $this->load->view('Student/Form/siblings_agreement_form_view', $data);
		} elseif ($param1 == 'save_siblings_agreement') {
			$aksi = $this->student_model->save_siblings_agreement();
            echo $aksi;
		} elseif ($param1 == 'preview_siblings_agreement') {
			$data['title'] = 'Sibling Agreement';
			$id_emergency_form = $this->uri->segment(5);
			$emergency_form = $this->student_model->get_emergency_form_by_id($id_emergency_form);
			$data['intended_program'] = $this->academic_master_model->get_intended_program();
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "Siblings Agreement - ".$emergency_form->student_name." (".$emergency_form->intended_program.").pdf";
			$this->pdf->preview_view('Student/Form/preview_siblings_agreement_view', $data);
		} 

		//----------------------------------------//

		elseif ($param1 == 'student_gets_student') {
			$data['title'] = 'Student Get Student';
			$data['intended_program'] = $this->academic_master_model->get_intended_program();
			$data['month'] = $this->recruitment_master_model->get_month();
			$data['left_bar'] = $this->admin_master_model->check_navbar();
	        $this->load->view('Student/Form/student_get_student_form_view', $data);	
		} elseif ($param1 == 'save_student_gets_student') {
			$aksi = $this->student_model->save_student_gets_student();
            echo $aksi;
		} elseif ($param1 == 'edit_student_gets_student') {
			$aksi = $this->student_model->edit_student_gets_student();
            echo $aksi;
		} elseif ($param1 == 'preview_student_gets_student') {
			$data['title'] = 'Student Gets Student';
			$id_emergency_form = $this->uri->segment(5);
			$emergency_form = $this->student_model->get_emergency_form_by_id($id_emergency_form);
			$data['title'] = 'Student Gets Student';
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "Student Gets Student - ".$emergency_form->student_name." (".$emergency_form->intended_program.").pdf";
			$this->pdf->preview_view('Student/Form/preview_student_gets_student_view', $data);
		}

		//----------------------------------------//

		elseif ($param1 == 'visa_application') {
			$data['title'] = 'Visa Application';
			$data['left_bar'] = $this->admin_master_model->check_navbar();
	        $this->load->view('Student/Form/visa_application_form_view', $data);	
		} elseif ($param1 == 'save_visa_application') {
			$aksi = $this->student_model->save_student_gets_student();
            echo $aksi;
		} elseif ($param1 == 'preview_student_gets_student') {
			$data['title'] = 'Visa Application';
			$id_emergency_form = $this->uri->segment(5);
			$emergency_form = $this->student_model->get_emergency_form_by_id($id_emergency_form);
			$data['title'] = 'Visa Application';
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "Visa Application - ".$emergency_form->student_name." (".$emergency_form->intended_program.").pdf";
			$this->pdf->preview_view('Student/Form/preview_student_gets_student_view', $data);
		}

		//----------------------------------------//

		elseif ($param1 == 'process_form') {
			$aksi = $this->student_model->process_form();
            echo $aksi;
		} elseif ($param1 == 'save_form') {
			$aksi = $this->student_model->save_form();
            echo $aksi;
		} elseif ($param1 == 'cancel_form') {
			$aksi = $this->student_model->cancel_form();
            echo $aksi;
		}

		//----------------------------------------//

		elseif ($param1 == 'new_protocol') {
			$data['title'] = 'JIC New Protocol';
			if ($this->session->userdata('id_level') == '17') {
				$id_leads = $this->session->userdata('id_leads');
			} else {
				$id_leads = $this->uri->segment(5);
			}
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$data['leads'] = $this->leads_model->get_student_active_by_leads($id_leads);
			$data['family'] = $this->leads_model->get_family_by_leads($id_leads);
	        $this->load->view('Student/Form/new_protocol_form_view', $data);	
		} elseif ($param1 == 'preview_new_protocol') {
			$data['title'] = 'JIC New Protocol';
			$id_leads_form = $this->uri->segment(5);
			$data['leads_form'] = $this->student_model->get_leads_form_by_id($id_leads_form);
			$form = $this->student_model->get_leads_form_by_id($id_leads_form);
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "JIC New Protocol - ".$form->leads_name." ".$form->family_name.".pdf";
			$this->pdf->preview_view('Student/Form/preview_new_protocol_form_view', $data);
		}

		//----------------------------------------//

		elseif ($param1 == 'statement_of_agreement') {
			$data['title'] = 'State of Agreement';
			if ($this->session->userdata('id_level') == '17') {
				$id_leads = $this->session->userdata('id_leads');
			} else {
				$id_leads = $this->uri->segment(5);
			}
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$data['leads'] = $this->leads_model->get_student_active_by_leads($id_leads);
			$data['family'] = $this->leads_model->get_family_by_leads($id_leads);
	        $this->load->view('Student/Form/statement_of_agreement_form_view', $data);	
		} elseif ($param1 == 'preview_statement_of_agreement') {
			$data['title'] = 'Statement of Agreement';
			$id_leads_form = $this->uri->segment(5);
			$data['leads_form'] = $this->student_model->get_leads_form_by_id($id_leads_form);
			$form = $this->student_model->get_leads_form_by_id($id_leads_form);
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "Statement of Agreement - ".$form->leads_name." ".$form->family_name.".pdf";
			$this->pdf->preview_view('Student/Form/preview_statement_of_agreement_form_view', $data);
		}

		//----------------------------------------//

		elseif ($param1 == 'books_regulation') {
			$data['title'] = 'Books Regulation';
			if ($this->session->userdata('id_level') == '17') {
				$id_leads = $this->session->userdata('id_leads');
			} else {
				$id_leads = $this->uri->segment(5);
			}
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$data['leads'] = $this->leads_model->get_student_active_by_leads($id_leads);
			$data['family'] = $this->leads_model->get_family_by_leads($id_leads);
	        $this->load->view('Student/Form/books_regulation_form_view', $data);	
		} elseif ($param1 == 'preview_books_regulation') {
			$data['title'] = 'Books Regulation';
			$id_leads_form = $this->uri->segment(5);
			$data['leads_form'] = $this->student_model->get_leads_form_by_id($id_leads_form);
			$form = $this->student_model->get_leads_form_by_id($id_leads_form);
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "Books Regulation - ".$form->leads_name." ".$form->family_name.".pdf";
			$this->pdf->preview_view('Student/Form/preview_books_regulation_form_view', $data);
		}

		//----------------------------------------//

		elseif ($param1 == 'dresscode') {
			$data['title'] = 'Dresscode';
			if ($this->session->userdata('id_level') == '17') {
				$id_leads = $this->session->userdata('id_leads');
			} else {
				$id_leads = $this->uri->segment(5);
			}
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$data['leads'] = $this->leads_model->get_student_active_by_leads($id_leads);
			$data['family'] = $this->leads_model->get_family_by_leads($id_leads);
	        $this->load->view('Student/Form/dresscode_form_view', $data);	
		} elseif ($param1 == 'preview_dresscode') {
			$data['title'] = 'Dresscode';
			$id_leads_form = $this->uri->segment(5);
			$data['leads_form'] = $this->student_model->get_leads_form_by_id($id_leads_form);
			$form = $this->student_model->get_leads_form_by_id($id_leads_form);
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "Dresscode - ".$form->leads_name." ".$form->family_name.".pdf";
			$this->pdf->preview_view('Student/Form/preview_dresscode_form_view', $data);
		}

		//----------------------------------------//

		elseif ($param1 == 'health_concent') {
			$data['title'] = 'Health Concent';
			if ($this->session->userdata('id_level') == '17') {
				$id_leads = $this->session->userdata('id_leads');
			} else {
				$id_leads = $this->uri->segment(5);
			}
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$data['leads'] = $this->leads_model->get_student_active_by_leads($id_leads);
			$data['family'] = $this->leads_model->get_family_by_leads($id_leads);
	        $this->load->view('Student/Form/health_concent_form_view', $data);	
		} elseif ($param1 == 'preview_health_concent') {
			$data['title'] = 'Health Concent';
			$id_leads_form = $this->uri->segment(5);
			$data['leads_form'] = $this->student_model->get_leads_form_by_id($id_leads_form);
			$form = $this->student_model->get_leads_form_by_id($id_leads_form);
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "Health Concent - ".$form->leads_name." ".$form->family_name.".pdf";
			$this->pdf->preview_view('Student/Form/preview_health_concent_form_view', $data);
		}

		//----------------------------------------//

		elseif ($param1 == 'image_video_concent') {
			$data['title'] = 'Image & Video Consent';
			if ($this->session->userdata('id_level') == '17') {
				$id_leads = $this->session->userdata('id_leads');
			} else {
				$id_leads = $this->uri->segment(5);
			}
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$data['leads'] = $this->leads_model->get_student_active_by_leads($id_leads);
			$data['family'] = $this->leads_model->get_family_by_leads($id_leads);
	        $this->load->view('Student/Form/image_video_concent_form_view', $data);	
		} elseif ($param1 == 'preview_image_video_concent') {
			$data['title'] = 'Image & Video Consent';
			$id_leads_form = $this->uri->segment(5);
			$data['leads_form'] = $this->student_model->get_leads_form_by_id($id_leads_form);
			$form = $this->student_model->get_leads_form_by_id($id_leads_form);
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "Image & Video Concent - ".$form->leads_name." ".$form->family_name.".pdf";
			$this->pdf->preview_view('Student/Form/preview_image_video_concent_form_view', $data);
		}

		//----------------------------------------//

		elseif ($param1 == 'acceptance_form') {
			$data['title'] = 'Acceptance Form';
			if ($this->session->userdata('id_level') == '17') {
				$id_leads = $this->session->userdata('id_leads');
			} else {
				$id_leads = $this->uri->segment(5);
			}
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$data['leads'] = $this->leads_model->get_student_active_by_leads($id_leads);
			$data['family'] = $this->leads_model->get_family_by_leads($id_leads);
	        $this->load->view('Student/Form/acceptance_form_view', $data);	
		} elseif ($param1 == 'preview_acceptance_form') {
			$data['title'] = 'Acceptance Form';
			$id_leads_form = $this->uri->segment(5);
			$data['leads_form'] = $this->student_model->get_leads_form_by_id($id_leads_form);
			$form = $this->student_model->get_leads_form_by_id($id_leads_form);
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "Image & Video Concent - ".$form->leads_name." ".$form->family_name.".pdf";
			$this->pdf->preview_view('Student/Form/preview_image_video_concent_form_view', $data);
		}
	}	


	public function course($param1 = ''){
		
		$id_student = $param1;
		$data['title'] = 'Course Detail';
		$data['acreditation'] = $this->admin_model->check_acreditation();
		$data['left_bar'] = $this->admin_master_model->check_navbar();
		$data['student'] = $this->student_model->get_student_by_id($id_student);
		$this->load->view('Student/Activity/course_detail_view', $data);	
	}

	public function get_detail_student(){
		$aksi = $this->input->post('aksi');
		$data['acreditation'] = $this->admin_model->check_acreditation();

		if ($aksi == 'class_student') {
			$id_student = $this->input->post('id_student');
			$data['class_student'] = $this->student_model->get_class_student_by_id_student($id_student);
			$this->load->view('Student/Activity/display_class_student_view', $data);
		} elseif ($aksi == 'attendance') {
			$id_class_student = $this->input->post('id_class_student');
			$data['schedule'] = $this->student_model->get_schedule_by_id_class_student($id_class_student);
			$data['schedule_total'] = $this->student_model->get_schedule_total_by_id_class_student($id_class_student);
			$data['class_student'] = $this->student_model->get_class_student_by_id($id_class_student);
			$this->load->view('Student/Activity/display_attendance_view', $data);
		} elseif ($aksi == 'period_student') {
			$id_student = $this->input->post('id_student');
			$data['student'] = $this->student_model->get_student_by_id($id_student);
			if ($data['student']->id_program == 1) {
				$data['semester'] = $this->student_model->get_semester_by_student($id_student);
			$this->load->view('Student/Activity/Local/display_local_scores_view', $data);
			} else {
				$data['trimester'] = $this->student_model->get_trimester_by_student($id_student);
				$this->load->view('Student/Activity/display_scores_view', $data);
			}
		} elseif ($aksi == 'transcript') {
			$id_student = $this->input->post('id_student');
			$data['student'] = $this->student_model->get_student_by_id($id_student);
			$data['class_student'] = $this->student_model->get_class_student_for_transcript_by_id_student($id_student);
			$this->load->view('Student/Activity/Local/display_transcript_view', $data);
		} 
	}

	public function education_plan($param1 = '', $param2 = ''){

		if ($param1 == 'attendance' && $param2 != '') {
			$id_student = $param2;
			$data['class_student'] = $this->student_model->get_class_student_by_id_student($id_student);
			$this->load->view('Student/Activity/display_class_student_view', $data);
		} 
	}
  		
}
