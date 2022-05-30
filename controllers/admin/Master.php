<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Admin/Admin_master_model','master_model');

		ini_set('display_errors', 0);

		if ($this->session->userdata('logged_in') == FALSE OR $this->session->userdata('id_level') != 1) {
			redirect('login');
		}
	}

	public function check_navbar(){
		if ($this->session->userdata('id_level') == 2 OR $this->session->userdata('id_level') == 3 OR $this->session->userdata('id_level') == 4) {
			$navbar = 'Marketing/left_navbar_template';
		} elseif ($this->session->userdata('id_level') == 8 OR $this->session->userdata('id_level') == 9 OR $this->session->userdata('id_level') == 10) {
			$navbar = 'Finance/left_navbar_template';
		} else {
			$navbar = 'Admin/left_navbar_template';
		}

		return $navbar;
	}

	 
	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function level($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Level - Master';
		     $data['left_bar'] = $this->master_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_level($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_level($this->input->get(), 'num_rows');
		     $this->load->view('Admin/Master/level_view', $data);
		     
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_level();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_level();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_level($id);
			}
			redirect($url);
		} else {

		}
	}

	 public function pagination_level($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_level';
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

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_level.level_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_level')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function level_system($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Level - Master';
		     $data['left_bar'] = $this->master_model->check_navbar();
		     $data['level'] = $this->master_model->get_level();
		     $data['employee'] = $this->master_model->get_employee();
		     $data['pagination_data'] = $this->pagination_level_system($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_level_system($this->input->get(), 'num_rows');
		     $this->load->view('Admin/Master/level_system_view', $data);
		     
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
        if(!isset($get['sortby'])) $get['sortby'] = 'employee_name';
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
            $query = $this->db->like('employee_name', $get['search'])
            				  ->or_like('employee_office_email', $get['search'])
            				  ->or_like('employee_username', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee_type','tb_employee_type.id_employee_type = tb_employee.id_employee_type','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->where('tb_employee.id_employee_status','1')
                          ->get('db_hr.tb_employee')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function log($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Log';
		     $data['left_bar'] = $this->master_model->check_navbar();
		     $data['log_menu'] = $this->master_model->get_log_menu();
		     $data['log_action'] = $this->master_model->get_log_action();
		     $data['employee'] = $this->master_model->get_employee();
		     $data['pagination_data'] = $this->pagination_log($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_log($this->input->get(), 'count_all_results');
		     $this->load->view('Admin/Master/log_view', $data);

		} else {

		}
	}

    public function pagination_log($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_log';
        if(!isset($get['sortby2'])) $get['sortby2'] = 'desc';

        if($get['page'] == 1){
            $mulai = 0;
        } else {
            $get['page']--;
            $mulai = $get['page'] * $get['limit'];
        }

        if(isset($get['search'])){

        	$query = $this->db->like('log_menu', $get['search'])
            				  ->or_like('log_action', $get['search'])
            				  ->or_like('log_notes', $get['search'])
            				  ->or_like('log_link', $get['search'])
            				  ->or_like('log_ip_address', $get['search'])
            				  ->or_like('log_device_name', $get['search'])
            				  ->or_like('tb_leads.name', $get['search'])
            				  ->or_like('tb_leads.family_name', $get['search'])
            				  ->or_like('CONCAT(name, family_name)',$get['search'])
            				  ->or_like('employee_name', $get['search']);
        }

        if (isset($get['log_menu_filter'])) {
            $query = $this->db->where('log_menu', $get['log_menu_filter']);
        }

        if (isset($get['log_action_filter'])) {
            $query = $this->db->where('log_action', $get['log_action_filter']);
        }

        if (isset($get['id_employee_filter'])) {
            $query = $this->db->where('tb_employee.id_employee', $get['id_employee_filter']);
        }

        if (isset($get['log_start_date_filter'])) {
            $query = $this->db->where('log_last_updated >=', $get['log_start_date_filter'])
            				  ->where('log_last_updated <=', $get['log_end_date_filter']);
        }

         if($param == 'result'){

            $query = $this->db->limit($get['limit'], $mulai);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_log.id_employee','left')
        				   ->join('tb_leads','tb_leads.id_leads=tb_log.id_leads','left');

        $query = $this->db->order_by($get['sortby'], $get['sortby2']);

        if($param == 'result'){

            $query = $this->db->get('tb_log')
            				  ->result();
        } else {
        	$query = $this->db->count_all('tb_log');
        }

        return $query;
    }
  
	
}
