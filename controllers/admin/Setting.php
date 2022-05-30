<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Admin/Admin_master_model','master_model');
		$this->load->model('Admin/Setting_model','setting_model');
		$this->link_terakhir = $this->config->item('link_terakhir');

		if ($this->session->userdata('logged_in') == FALSE) {
			redirect('login');
		}

		ini_set('display_errors', 0);
	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function change_password(){
		$this->load->view('Admin/Setting/change_password_view');
	}

	 
	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function email_notification($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Email Notification - Master';
		     $data['left_bar'] = $this->master_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_email_notification($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_email_notification($this->input->get(), 'num_rows');
		     $this->load->view('Admin/Setting/email_notification_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		}  elseif ($param1 == 'edit') {
			$url = $this->input->post('url');
			$this->setting_model->edit_email_notification();
            redirect('admin/setting/email_notification');
		} elseif ($param1 == 'detail') {
			 $id_email_notification = $param2;
			 $data['title'] = 'Email Notification - Master';
		     $data['left_bar'] = $this->master_model->check_navbar();
			 $data['email_notification'] = $this->setting_model->get_email_notification_by_id($id_email_notification);
		     $this->load->view('Admin/Setting/form_email_notification_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		}  else {

		}
	}

	 public function pagination_email_notification($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_email_notification';
        if(!isset($get['sortby2'])) $get['sortby2'] = 'asc';

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
            $query = $this->db->like('level', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_email_notification.email_notification_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_email_notification')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function level_system($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Level - Master';
		     $data['left_bar'] = $this->check_navbar();
		     $data['level'] = $this->master_model->get_level();
		     $data['employee'] = $this->master_model->get_employee();
		     $data['pagination_data'] = $this->pagination_level_system($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_level_system($this->input->get(), 'num_rows');
		     $this->load->view('Admin/Master/level_system_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_level_system();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_level_system();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_level_system($id);
			}
			redirect($url);
		} else {

		}
	}

	 public function pagination_level_system($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_level_system';
        if(!isset($get['sortby2'])) $get['sortby2'] = 'asc';

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
            $query = $this->db->like('level', $get['search']);
        }

        $query = $this->db->select('*, user.employee_name as user_name, updated_by.employee_name as updated_name, user.id_employee as id_user')
        				  ->join('db_hr.tb_employee as user','user.id_employee=tb_level_system.id_employee','left')
        				  ->join('db_hr.tb_employee as updated_by','updated_by.id_employee=tb_level_system.level_system_updated_by','left')
        				  ->join('tb_level','tb_level.id_level=tb_level_system.id_level','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_level_system')->$param();

        return $query;
    }

	
}
