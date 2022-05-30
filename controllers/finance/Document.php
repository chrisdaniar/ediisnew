<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Document extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('Pdf','pdf');
		$this->load->model('Finance/Finance_master_model','master_model');
		ini_set('display_errors', 0);
	}

		function payment_receipt(){
			$id_leads_payment = $this->uri->segment(4);
			$receipt = $this->master_model->get_payment_by_id($id_leads_payment);
			$ol = $this->db->where('id_ol', $receipt->id_ol)->get('tb_offer_letter')->row();
			$tuition = $this->master_model->get_tuition_fee_by_id($ol->id_fee);
			$data['receipt'] = $this->master_model->get_payment_by_id($id_leads_payment);
			$data['discount'] = $this->master_model->get_total_discount_by_id_fee($tuition, $receipt->id_ol);

			$data['tuition_fee'] = $this->master_model->get_tuition_fee_by_id($ol->id_fee);
			$data['additional_fee'] = $this->master_model->get_additional_fee_by_id($ol->id_fee);

			$data['first_payment'] = $this->master_model->get_first_payment_by_id_fee($ol->id_fee);
			$data['second_payment'] = $this->master_model->get_second_payment_by_id_fee($ol->id_fee);
			$data['third_payment'] = $this->master_model->get_third_payment_by_id_fee($ol->id_fee);

			$this->pdf->preview_view('Finance/Document/receipt_view', $data);
		}


		function offer_letter(){
			$id_fo = $this->uri->segment(4);
			$fo = $this->master_model->get_fo_by_id($id_fo);
			$data['fo'] = $this->master_model->get_fo_by_id($id_fo);
			$data['first_payment'] = $this->master_model->get_first_payment_by_id_fee($fo->id_fee);
			$data['second_payment'] = $this->master_model->get_second_payment_by_id_fee($fo->id_fee);
			$data['third_payment'] = $this->master_model->get_third_payment_by_id_fee($fo->id_fee);
			$data['additional_fee'] = $this->master_model->get_additional_fee_by_id_fee($fo->id_fee);
			$data['requirement']= $this->master_model->get_requirement($fo->id_qualification, $fo->id_class, $fo->id_average, $fo->id_course);
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
					$this->pdf->preview_view('Marketing/Document/fo_mufy_view', $data);
				} elseif ($fo->id_intended_program == 4 OR $fo->id_intended_program == 5 OR $fo->id_intended_program == 6) {
					$this->pdf->preview_view('Marketing/Document/fo_diploma_view', $data);
				} elseif ($fo->id_intended_program == 2) {
					$this->pdf->preview_view('Marketing/Document/fo_meb_view', $data);
				} elseif ($fo->id_intended_program == 3) {
					$this->pdf->preview_view('Marketing/Document/fo_wmu_view', $data);
				} else {

				}
			} else {
				if ($fo->id_intended_program == 1) {
					$this->pdf->preview_view('Marketing/Document/co_mufy_view', $data);
				} elseif ($fo->id_intended_program == 4 OR $fo->id_intended_program == 5 OR $fo->id_intended_program == 6) {
					$this->pdf->preview_view('Marketing/Document/co_diploma_view', $data);
				} elseif ($fo->id_intended_program == 3) {
					$this->pdf->preview_view('Marketing/Document/co_wmu_view', $data);
				} else {

				}
			}
		}
		
		function fo_generate_to_pdf(){
			$id_fo = $this->uri->segment(4);
			$fo = $this->master_model->get_fo_by_id($id_fo);

			if ($fo->id_intended_program == 2) {
				$data['meb_course'] = $this->master_model->get_meb_course($fo->id_meb_course);
				$data['meb_intake'] = $this->master_model->get_intake_by_id($fo->id_intake_meb);
			}

			$data['fo'] = $this->master_model->get_fo_by_id($id_fo);
			$data['first_payment'] = $this->master_model->get_first_payment_by_id_fee($fo->id_fee);
			$data['second_payment'] = $this->master_model->get_second_payment_by_id_fee($fo->id_fee);
			$data['third_payment'] = $this->master_model->get_third_payment_by_id_fee($fo->id_fee);
			$data['additional_fee'] = $this->master_model->get_additional_fee_by_id_fee($fo->id_fee);
			$data['requirement']= $this->master_model->get_requirement($fo->id_qualification, $fo->id_class, $fo->id_average, $fo->id_course);
			$data['english'] = $this->master_model->get_english_requirement($fo->id_qualification, $fo->id_class, $fo->id_course);
			$data['discount'] = $this->master_model->get_discount_ol($id_fo);

			if ($fo->ol_type == 'fo') {

				if ($fo->id_intended_program == 1) {
					$this->pdf->load_view('Marketing/Document/fo_mufy_view', $data);
				} elseif ($fo->id_intended_program == 4 OR $fo->id_intended_program == 5 OR $fo->id_intended_program == 6) {
					$this->pdf->load_view('Marketing/Document/fo_diploma_view', $data);
				} elseif ($fo->id_intended_program == 2) {
					$this->pdf->load_view('Marketing/Document/fo_meb_view', $data);
				} elseif ($fo->id_intended_program == 3) {
					$this->pdf->load_view('Marketing/Document/fo_wmu_view', $data);
				} else {
					
				}
			} else {
				if ($fo->id_intended_program == 1) {
					$this->pdf->load_view('Marketing/Document/co_mufy_view', $data);
				} elseif ($fo->id_intended_program == 4 OR $fo->id_intended_program == 5 OR $fo->id_intended_program == 6) {
					$this->pdf->load_view('Marketing/Document/co_diploma_view', $data);
				} elseif ($fo->id_intended_program == 2) {
					$this->pdf->load_view('Marketing/Document/co_meb_view', $data);
				} elseif ($fo->id_intended_program == 3) {
					$this->pdf->load_view('Marketing/Document/co_wmu_view', $data);
				} else {
					
				}
			}

			$str = $fo->ol_reference;
			$ganti = str_replace("/", "-", $str);

			$this->pdf->render();
			$this->pdf->stream($ganti.".pdf");
		}

	
}
