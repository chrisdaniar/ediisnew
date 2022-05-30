<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document extends CI_Controller {

	public function __construct()
	{
		
		parent::__construct();
		$this->load->library('Pdf','pdf');
		$this->load->helper('path');
		$this->load->model('Recruitment/Recruitment_master_model','master_model');
		$this->load->model('Admin/Admin_master_model','admin_model');

		ini_set('display_errors', 0);
		
	}

		function preview_offer_letter(){

			$id_leads = $this->input->post('id_leads');
			$id_fee = $this->input->post('id_fee');
			$id_campus = $this->input->post('id_campus');
			$id_course = $this->input->post('id_course');
			$id_intake = $this->input->post('id_intake');
			$meb_id_course = $this->input->post('meb_id_course');
			$meb_intake_month = $this->input->post('meb_intake_month');
			$meb_intake_year = $this->input->post('meb_intake_year');
			$id_class = $this->input->post('id_class');
			$id_school = $this->input->post('id_school');
			$ol_type = $this->input->post('ol_type');

			if ($meb_id_course == null OR $meb_id_course == '') {
			} else {

				$intended_program = $this->db->join('tb_intended_program','tb_intended_program.id_intended_program=tb_course.id_intended_program')
											 ->where('id_course', $meb_id_course)
											 ->get('tb_course')
											 ->row();

				$id_intake_meb = $this->db->select('id_intake')
										  ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_intake.id_academic_year')
										  ->where('id_intake_month', $meb_intake_month)
										  ->where('academic_year' , $meb_intake_year)
										  ->where('id_intended_program', $intended_program->id_intended_program)
										  ->get('tb_intake')
										  ->row();

				$data['meb_course'] = $this->master_model->get_meb_course($meb_id_course);
				$data['meb_intake'] = $this->master_model->get_intake_by_id($id_intake_meb);
				$fee_year = $this->db->where('id_fee', $id_fee)->get('tb_fee')->row();
				$fee_meb = $this->db->where('id_course', $meb_id_course)->where('id_tuition_year', $fee_year->id_tuition_year)->get('tb_fee')->row();

				$data['tuition_meb'] = $this->master_model->get_first_payment_by_id_fee($id_fee);
				$data['tuition_diploma'] = $this->master_model->get_tuition_diploma($fee_meb->id_fee);
				$data['amenities_diploma'] = $this->master_model->get_amenities_diploma($fee_meb->id_fee);
				$data['administration_diploma'] = $this->master_model->get_administration_diploma($fee_meb->id_fee);
			}
  
			$detail = $this->master_model->get_leads_by_id($id_leads);
			$course = $this->master_model->get_course_by_id($id_course);
			$data['agent'] = $this->master_model->get_agent_by_id_leads($id_leads);
			$data['fo'] = $this->master_model->get_leads_by_id($id_leads);
			$data['intake'] = $this->master_model->get_intake_by_id($id_intake);
			$data['campus'] = $this->master_model->get_campus_by_id($id_campus);
			$data['course'] = $this->master_model->get_course_by_id($id_course);
			$data['class'] = $this->master_model->get_class_by_id($id_class);
			$data['school'] = $this->master_model->get_school_by_id($id_school);
			$data['discount'] = $this->input->post('discount');
			$data['first_payment'] = $this->master_model->get_first_payment_by_id_fee($id_fee);
			$data['second_payment'] = $this->master_model->get_second_payment_by_id_fee($id_fee);
			$data['third_payment'] = $this->master_model->get_third_payment_by_id_fee($id_fee);
			$data['additional_fee'] = $this->master_model->get_additional_fee_by_id_fee($id_fee);
			$req = $this->master_model->get_requirement($detail->id_qualification, $detail->id_class, $detail->id_course);
			$data['requirement'] = $this->master_model->get_requirement_score($id_course, $detail->id_leads);
			$data['english'] = $this->master_model->get_english_requirement($detail->id_qualification, $detail->id_class, $detail->id_course);


			if ($ol_type == 'fo') {
				
				if ($course->id_intended_program == 1) {
					$this->pdf->preview_view('Recruitment/Document/preview_fo_mufy_view', $data);
				} elseif ($course->id_intended_program == 4 OR $course->id_intended_program == 5 OR $course->id_intended_program == 6) {
					$this->pdf->preview_view('Recruitment/Document/preview_fo_diploma_view', $data);
				} elseif ($course->id_intended_program == 2) {
					$this->pdf->preview_view('Recruitment/Document/preview_fo_meb_view', $data);
				} elseif ($course->id_intended_program == 3) {
					$this->pdf->preview_view('Recruitment/Document/preview_fo_wmu_view', $data);
				} else {

				}
			} else {
				if ($course->id_intended_program == 1) {
					$this->pdf->preview_view('Recruitment/Document/preview_co_mufy_view', $data);
				} elseif ($course->id_intended_program == 4 OR $course->id_intended_program == 5 OR $course->id_intended_program == 6) {
					$this->pdf->preview_view('Recruitment/Document/preview_co_diploma_view', $data);
				} elseif ($course->id_intended_program == 3) {
					$this->pdf->preview_view('Recruitment/Document/preview_co_wmu_view', $data);
				} else {

				}
			}
		}


		function offer_letter(){

			$id_fo = $this->uri->segment(4);
			$fo = $this->master_model->get_fo_by_id($id_fo);
			$data['fo'] = $this->master_model->get_fo_by_id($id_fo);
			$data['first_payment'] = $this->master_model->get_first_payment_by_id_fee($fo->id_fee);
			$data['second_payment'] = $this->master_model->get_second_payment_by_id_fee($fo->id_fee);
			$data['third_payment'] = $this->master_model->get_third_payment_by_id_fee($fo->id_fee);
			$data['additional_fee'] = $this->master_model->get_additional_fee_by_id_fee($fo->id_fee);
			$req = $this->master_model->get_requirement($fo->id_qualification, $fo->id_class, $fo->id_course);

			$data['requirement'] = $this->master_model->get_requirement_score($fo->id_course, $fo->id_leads);
			$data['english'] = $this->master_model->get_english_requirement($fo->id_qualification, $fo->id_class, $fo->id_course);
			$data['discount'] = $this->master_model->get_discount_ol($id_fo);

			if ($fo->meb_id_course == null OR $meb_id_course == '') {
			} else {
				$data['meb_course'] = $this->master_model->get_meb_course($fo->meb_id_course);
				$data['meb_intake'] = $this->master_model->get_intake_by_id($fo->meb_id_intake);
				$fee_year = $this->db->where('id_fee', $fo->id_fee)->get('tb_fee')->row();
				$fee_meb = $this->db->where('id_course', $fo->meb_id_course)->where('id_tuition_year', $fee_year->id_tuition_year)->get('tb_fee')->row();

				$data['tuition_meb'] = $this->master_model->get_first_payment_by_id_fee($fo->id_fee);
				$data['tuition_diploma'] = $this->master_model->get_tuition_diploma($fee_meb->id_fee);
				$data['amenities_diploma'] = $this->master_model->get_amenities_diploma($fee_meb->id_fee);
				$data['administration_diploma'] = $this->master_model->get_administration_diploma($fee_meb->id_fee);
			}

			if ($fo->ol_type == 'fo') {
				
				if ($fo->id_intended_program == 1) {
					$this->pdf->preview_view('Recruitment/Document/fo_mufy_view', $data);
				} elseif ($fo->id_intended_program == 4 OR $fo->id_intended_program == 5 OR $fo->id_intended_program == 6) {
					$this->pdf->preview_view('Recruitment/Document/fo_diploma_view', $data);
				} elseif ($fo->id_intended_program == 2) {
					$this->pdf->preview_view('Recruitment/Document/fo_meb_view', $data);
				} elseif ($fo->id_intended_program == 3) {
					$this->pdf->preview_view('Recruitment/Document/fo_wmu_view', $data);
				} else {

				}
			} else {
				if ($fo->id_intended_program == 1) {
					$this->pdf->preview_view('Recruitment/Document/co_mufy_view', $data);
				} elseif ($fo->id_intended_program == 4 OR $fo->id_intended_program == 5 OR $fo->id_intended_program == 6) {
					$this->pdf->preview_view('Recruitment/Document/co_diploma_view', $data);
				} elseif ($fo->id_intended_program == 3) {
					$this->pdf->preview_view('Recruitment/Document/co_wmu_view', $data);
				} else {

				}
			}
		}
		
		function ol_generate_to_pdf(){
			$id_fo = $this->uri->segment(4);
			$fo = $this->master_model->get_fo_by_id($id_fo);
			$data['fo'] = $this->master_model->get_fo_by_id($id_fo);
			$data['first_payment'] = $this->master_model->get_first_payment_by_id_fee($fo->id_fee);
			$data['second_payment'] = $this->master_model->get_second_payment_by_id_fee($fo->id_fee);
			$data['third_payment'] = $this->master_model->get_third_payment_by_id_fee($fo->id_fee);
			$data['additional_fee'] = $this->master_model->get_additional_fee_by_id_fee($fo->id_fee);
			$req = $this->master_model->get_requirement($fo->id_qualification, $fo->id_class, $fo->id_course);

			$data['requirement'] = $this->master_model->get_requirement_score($fo->id_course, $fo->id_leads);
			$data['english'] = $this->master_model->get_english_requirement($fo->id_qualification, $fo->id_class, $fo->id_course);
			$data['discount'] = $this->master_model->get_discount_ol($id_fo);

			if ($fo->meb_id_course == null OR $meb_id_course == '') {
			} else {
				$data['meb_course'] = $this->master_model->get_meb_course($fo->meb_id_course);
				$data['meb_intake'] = $this->master_model->get_intake_by_id($fo->meb_id_intake);
				$fee_year = $this->db->where('id_fee', $fo->id_fee)->get('tb_fee')->row();
				$fee_meb = $this->db->where('id_course', $fo->meb_id_course)->where('id_tuition_year', $fee_year->id_tuition_year)->get('tb_fee')->row();

				$data['tuition_meb'] = $this->master_model->get_first_payment_by_id_fee($fo->id_fee);
				$data['tuition_diploma'] = $this->master_model->get_tuition_diploma($fee_meb->id_fee);
				$data['amenities_diploma'] = $this->master_model->get_amenities_diploma($fee_meb->id_fee);
				$data['administration_diploma'] = $this->master_model->get_administration_diploma($fee_meb->id_fee);
			}

			if ($fo->ol_type == 'fo') {

				if ($fo->id_intended_program == 1) {
					$this->pdf->load_view('Recruitment/Document/fo_mufy_view', $data);
				} elseif ($fo->id_intended_program == 4 OR $fo->id_intended_program == 5 OR $fo->id_intended_program == 6) {
					$this->pdf->load_view('Recruitment/Document/fo_diploma_view', $data);
				} elseif ($fo->id_intended_program == 2) {
					$this->pdf->load_view('Recruitment/Document/fo_meb_view', $data);
				} elseif ($fo->id_intended_program == 3) {
					$this->pdf->load_view('Recruitment/Document/fo_wmu_view', $data);
				} else {
					
				}
			} else {
				if ($fo->id_intended_program == 1) {
					$this->pdf->load_view('Recruitment/Document/co_mufy_view', $data);
				} elseif ($fo->id_intended_program == 4 OR $fo->id_intended_program == 5 OR $fo->id_intended_program == 6) {
					$this->pdf->load_view('Recruitment/Document/co_diploma_view', $data);
				} elseif ($fo->id_intended_program == 2) {
					$this->pdf->load_view('Recruitment/Document/co_meb_view', $data);
				} elseif ($fo->id_intended_program == 3) {
					$this->pdf->load_view('Recruitment/Document/co_wmu_view', $data);
				} else {
					
				}
			}

			$str = $fo->ol_reference;
			$ganti = str_replace("/", "-", $str);

			$this->pdf->render();

			$file_to_save = 'uploads/'.$ganti.".pdf";
			file_put_contents($file_to_save, $this->pdf->output());


			$receiver = $fo->email;
			$cek_sender = $this->db->where('id_employee', $fo->id_owner)
							   ->get('db_hr.tb_employee')
							   ->row();

			if ($fo->ol_type == 'fo'){
				$cek_email = $this->db->where('id_email_notification', 2)
											   ->get('tb_email_notification')
											   ->row();
			} else {
				$cek_email = $this->db->where('id_email_notification', 1)
											   ->get('tb_email_notification')
											   ->row();
			}

			if ($cek_email->email_notification_active == 1) {
				$sender = $cek_sender->employee_email;
				$title = $cek_email->email_title;
				$subject = $cek_email->email_subject;
				$message = $cek_email->email_message;
				$attach = 'uploads/'.$ganti.".pdf";
				$this->send_email($receiver, $sender, $title, $subject, $message, $attach);
			}

			!unlink('uploads/'.$ganti.'.pdf');

		}

		
		public function send_email($receiver = '', $sender = '', $title = '', $subject = '', $message = '', $attach = '')
  		{
	  	
	        $this->load->library('email');
	            $config = array(
	              'protocol' => 'smtp',
	              'smtp_host'   => 'ssl://smtp.googlemail.com',
	              'smtp_port'   => 465,
	              'smtp_user'   => 'jic.itservices@gmail.com',
	              'smtp_pass'   => 'm0nash01',
	              'mailtype'    => 'html',
	              'wordwrap'  => TRUE
	            ); 
	            $this->email->initialize($config);
	            $this->email->set_newline("\r\n");
	            $this->email->from($sender, $title);

	            $o = explode(",", $receiver);
	            $recipientArr = $o;
	            $this->email->to($recipientArr);
	            $this->email->cc($sender);
	            
	            $this->email->subject($subject);
	            $this->email->message($message);
	            if($attach != ''){
	              $this->email->attach($attach);
	            }
	            $this->email->send();
  		}


  	public function new_protocol()
  		{
  			$data['title'] = 'Enrollment Report';
			$data['left_bar'] = $this->admin_model->check_navbar();
	        $this->load->view('Recruitment/Document/new_normal_protocol_view', $data);
  		}

  		
}
