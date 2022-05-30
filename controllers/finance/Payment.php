<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Finance/Finance_master_model','finance_master_model');
        $this->load->model('Academic/Academic_master_model','academic_master_model');
        $this->load->model('Recruitment/Recruitment_master_model','recruitment_master_model');
        $this->load->model('Marketing/Marketing_master_model','marketing_master_model');
		$this->load->model('Finance/Payment_model','payment_model');
        $this->load->model('IT/it_student_model','it_student_model');
        $this->load->model('Admin/Admin_master_model','admin_master_model');
		$this->link_terakhir = $this->config->item('link_terakhir');

        //ini_set('display_errors', 0);
	}

	public function tes_code(){

           $this->db->SELECT('RIGHT(tb_leads_payment.no_receipt,5) as kode', FALSE)
                    ->order_by('no_receipt','DESC')  
                    ->limit(1); 

          $query = $this->db->get('tb_leads_payment');      //cek dulu apakah ada sudah ada kode di tabel.    
          if($query->num_rows() <> 0){      
           //jika kode ternyata sudah ada.      
           $data = $query->row();      
           $kode = intval($data->kode) + 1;    
          }
          else {      
           //jika kode belum ada      
           $kode = 1;    
          }
          $kodemax = str_pad($kode, 5, "0", STR_PAD_LEFT); // angka 4 menunjukkan jumlah digit angka 0
          $kodejadi = "RCP".$kodemax;    // hasilnya ODJ-991-0001 dst.
          echo $kodejadi; 
	}

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function index($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Payment';
             $data['intended_program'] = $this->academic_master_model->get_intended_program();
             $data['campus'] = $this->academic_master_model->get_campus();
             $data['intake_year'] = $this->recruitment_master_model->get_intake_year();
             $data['month'] = $this->recruitment_master_model->get_month();
             $data['course'] = $this->academic_master_model->get_course();
		     $data['left_bar'] = $this->admin_master_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_payment($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_payment($this->input->get(), 'num_rows');
		     $this->load->view('Finance/Payment/first_payment_view', $data);
		} elseif ($param1 == 'payment_verification') {
			$aksi = $this->payment_model->payment_verification();
            echo $aksi;
		} else {
			
		}
	}

    public function payment_verification(){
        $id_leads_payment = $this->input->post('id_leads_payment');
        $payment = $this->input->post('payment');

        $leads_payment = $this->db->where('id_leads_payment', $id_leads_payment)
                                  ->get('tb_leads_payment')
                                  ->row();

        if ($payment == 1) {
            $data['payment_status'] = 'Paid';
            $data['verified_by'] = $this->session->id_employee;
            $data['verification_date'] = date('Y-m-d H:i:s');
            $leads['id_status'] = '4';
        } else {
            $data['payment_status'] = 'Pending';
            $data['verified_by'] = '';
            $data['verification_date'] = '';
            $leads['id_status'] = '2';
        }

        $this->db->where('id_leads_payment', $id_leads_payment)
                 ->update('tb_leads_payment', $data);



        $this->db->where('id_leads', $leads_payment->id_leads)
                 ->update('tb_leads', $leads);
    }

	public function pagination_payment($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 20;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_leads_payment';
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
            $query = $this->db->like('CONCAT(name," ",family_name)',$get['search']);
        }
        if (isset($get['id_intended_program'])) {
            $query = $this->db->where('tb_course.id_intended_program', $get['id_intended_program']);
        }

        if (isset($get['id_course'])) {
            $query = $this->db->where('tb_course.id_course', $get['id_course']);
        }
        if (isset($get['id_campus'])) {
            $query = $this->db->where('tb_campus.id_campus', $get['id_campus']);
        }

        if (isset($get['intake_year'])) {
            $query = $this->db->where('year(intake_leads)', $get['intake_year']);
        }

        if (isset($get['id_month'])) {
            $query = $this->db->where('month(intake_leads)', $get['id_month']);
        }

        if (isset($get['id_fee_desc'])) {
            $query = $this->db->where('id_fee_desc', $get['id_fee_desc']);
        }

        if (isset($get['payment_status'])) {
            $query = $this->db->where('payment_status', $get['payment_status']);
        }

        $query = $this->db->select('*, tb_leads.id_leads')
                          ->join('tb_student','tb_student.id_student = tb_leads_payment.id_student')
                          ->join('tb_leads','tb_leads.id_leads=tb_student.id_leads')
        				  ->join('tb_user','tb_user.id_user=tb_leads_payment.uploaded_by')
                          ->join('tb_campus', 'tb_campus.id_campus = tb_student.id_campus', 'left')
                          ->join('tb_course', 'tb_course.id_course = tb_student.id_course', 'left')
                          ->join('tb_fee_desc','tb_fee_desc.id_fee_desc = tb_leads_payment.id_fee_desc')
        				  ->order_by($get['sortby'], $get['sortby2']);

        if($param == 'result'){

            $query = $this->db->get('tb_leads_payment')
                              ->result();
        } else {
            $query = $this->db->count_all_results('tb_leads_payment');
        }

        return $query;
    }

     public function finance_clearance($param = '')
     {
      if ($param == 'active_student') {
            $data['title'] = 'Student';
            $data['intended_program'] = $this->academic_master_model->get_intended_program();
            $data['course'] = $this->academic_master_model->get_course();
            $data['campus'] = $this->academic_master_model->get_campus();
            $data['intake_year'] = $this->recruitment_master_model->get_intake_year();
            $data['month'] = $this->recruitment_master_model->get_month();
            $data['left_bar'] = $this->admin_master_model->check_navbar();
            $data['pagination_data'] = $this->it_student_model->pagination_active_student($this->input->get(), 'result_array');
            $data['pagination_total_page'] = $this->it_student_model->pagination_active_student($this->input->get(), 'num_rows');
            $this->load->view('IT/Student/finance_clearance_view', $data);
            $this->session->set_userdata('previous_url', $this->link_terakhir);
        } elseif ($param == 'activate_finance_clearance') {
         $hasil = $this->payment_model->activate_finance_clearance();
         echo $hasil;
      }
    }
}
