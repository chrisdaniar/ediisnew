<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->load->model('Recruitment/Leads_model','leads_model');
        $this->load->model('Admin/Notification_model','notification_model');
		$this->link_terakhir = $this->config->item('link_terakhir');

		if ($this->session->userdata('logged_in') == FALSE) {
			redirect('login');
		}

		ini_set('display_errors', 0);
	}

	public function proof_of_payment(){
        $id_leads_payment = $this->input->post('id_leads_payment');
        $view = $this->input->post('view');
        $datas['view'] = $view;
        $datas['leads_payment'] = $this->leads_model->get_detail_leads_payment($id_leads_payment);
        if ($view == 0) {
            echo $message = $this->load->view('Email/Leads/proof_of_payment_notification_view', $datas, TRUE);
        } else {

            $payment = $this->leads_model->get_detail_leads_payment($id_leads_payment);

            $receiver = 'nita.natalia@jic.ac.id';
            $cc = $payment->employee_office_email;
            $title = 'Proof of Payment Notification';
            $subject = 'Proof of Payment Notification';

            $message = $this->load->view('Email/Leads/proof_of_payment_notification_view', $datas, TRUE);
            $this->notification_model->send_email_system($receiver, $cc, $title, $subject,  $message,  $attach, $id_leads);
            echo true;
        }
  }
}