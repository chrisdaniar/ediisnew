<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Form extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Recruitment/Recruitment_master_model','recruitment_master_model');
		$this->load->model('Recruitment/Leads_model','leads_model');
		$this->load->model('Marketing/Marketing_master_model','marketing_master_model');
		$this->load->model('Academic/Academic_master_model','academic_master_model');
		$this->load->model('Admin/Admin_master_model','admin_master_model');
		$this->load->model('Student/Student_model','student_model');
		$this->load->model('Student/Form_model','form_model');

		//ini_set('display_errors', 0);
	}

	public function get_regency()
    {
        $id_province = $this->input->post('id_province');
        $data = $this->form_model->get_regency($id_province);
        echo json_encode($data);
    }

	public function get_course_by_intended_program($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_intended_program = $param;
		$result = $this->db->where('id_intended_program', $id_intended_program)->get('tb_course')->result();
		$option = "";
		$option .= '<option value=""> -- Select Course --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_course."' >".$data->course."</option>";
		}
		echo $option;

	}

	public function get_intake_month_by_course($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_course= $param;
		$id_intended_program = $this->db->join('tb_intended_program','tb_intended_program.id_intended_program=tb_course.id_intended_program')
										->where('id_course', $id_course)
										->get('tb_course')->row();

		$result = $this->db->join('tb_month','tb_month.id_month=tb_intake_month.id_month')
							->where('id_intended_program', $id_intended_program->id_intended_program)
							->get('tb_intake_month')->result();
		$option = "";
		$option .= '<option value=""> -- Month --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intake_month."' >".$data->month."</option>";
		}
		echo $option;

	}

	public function index($param1 = '', $param2 = '', $param3 = ''){

		if ($param1 == '') {
			$data['title'] = 'JIC Online Form';
			$data['left_bar'] = $this->admin_master_model->check_navbar();
		    $this->load->view('Student/Form/online_form_view', $data);	
		} elseif ($param1 == 'siblings_agreement') {
			$id_leads = $param2;
			$data['title'] = 'Sibling Agreement';
			$data['leads'] = $this->leads_model->get_detail_leads($id_leads);
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
			$id_leads = $param2;
			$data['title'] = 'Student Get Student';
			$data['leads'] = $this->leads_model->get_detail_leads($id_leads);
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
			$id_emergency_form = $this->uri->segment(5);
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$data['emergency_form'] = $this->student_model->get_emergency_form_by_id($id_emergency_form);
	        $this->load->view('Student/Form/visa_application_form_view', $data);	
		} elseif ($param1 == 'save_visa_application') {
			$aksi = $this->student_model->save_visa_application();
            $this->index('thank_you');
		} elseif ($param1 == 'preview_visa_application') {
			$data['title'] = 'Visa Application';
			$id_emergency_form = $this->uri->segment(5);
			$emergency_form = $this->student_model->get_emergency_form_by_id($id_emergency_form);
			$data['emergency_form'] = $this->student_model->get_emergency_form_by_id($id_emergency_form);
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->pdf->filename = "Visa Application - ".$emergency_form->student_name." (".$emergency_form->intended_program.").pdf";
			$this->pdf->preview_view('Student/Form/preview_visa_application_form_view', $data);
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

		elseif ($param1 == 'thank_you') {
			$data['title'] = 'Thank You';
			$data['left_bar'] = $this->admin_master_model->check_navbar();
			$this->load->view('Student/Form/thank_you_view', $data);
		}

	}	


	public function electronic_signature($status = '')
    {
    	$id_leads_form = $this->uri->segment(4);

    	if ($status == 1) {
    		$data['left_bar'] = $this->admin_master_model->check_navbar();
	    	$data['status'] = 1;
	    	$data['leads_form'] = $this->form_model->get_leads_form_by_id($id_leads_form);
	        $this->load->view('Student/Form/electronic_signature_view', $data);
    	} else {
	    	$data['left_bar'] = $this->admin_master_model->check_navbar();
	    	$data['status'] = 0;
	        $this->load->view('Student/Form/electronic_signature_view', $data);
    	}
    }


	public function postForm()
    {

        $recaptchaResponse = trim($this->input->post('g-recaptcha-response'));
 
        $userIp=$this->input->ip_address();
     
        $secret = $this->config->item('google_secret');
   
        $url="https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$recaptchaResponse."&remoteip=".$userIp;
 
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        $output = curl_exec($ch); 
        curl_close($ch);      
         
        $status= json_decode($output, true);
 
        if ($status['success']) {
            //print_r('Google Recaptcha Successful');
            $this->electronic_signature('1');
            //exit;
        }else{
            $this->session->set_flashdata('flashError', 'Google Recaptcha is Expired!');
            $this->electronic_signature('0');
        }
 
        //redirect('form', 'refresh');
    }  

    public function event($param1 = '', $param2 = ''){
		if ($param1 != '' && $param1 != 'add') {
             $id_event = $param1;
			 $data['title'] = 'Form';
             $data['event'] = $this->marketing_master_model->get_event_by_id($id_event);
             $data['province'] = $this->marketing_master_model->get_province();
             $data['country'] = $this->marketing_master_model->get_country();
             $data['class_'] = $this->marketing_master_model->get_class();
             $data['event_handled_by'] = $this->marketing_master_model->get_event_handled_by_by_id_event($id_event);
		     $data['left_bar'] = 'Admin/no_level_left_navbar_template';
		     if ($data['event']->id_campus == 1) {
		     	$this->load->view('Marketing/Event/Leads/form_local_view', $data);
		     } else {
		     	$this->load->view('Marketing/Event/Leads/form_view', $data);
		     }
		     
		} elseif ($param1 == 'add') {
			$aksi = $this->form_model->add_leads();
            echo $aksi;
		} else {

		}
	}
  		
}
