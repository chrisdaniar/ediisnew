<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Employee extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('HR/Employee_model','employee_model');
		$this->load->model('Admin/Admin_master_model','admin_model');
		$this->link_terakhir = $this->config->item('link_terakhir');

        ini_set('display_errors', 0);
	}

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//


    public function get_position_by_division() {
    
        $id_division = $this->input->post('id_division');
        
        $result = $this->db->where('id_division', $id_division)->get('db_hr.tb_position')->result();
        $option = "";
        $option .= '<option value=""> -- Select Position --- </option>';
        foreach ($result as $data) {
            $option .= "<option value='".$data->id_position."' >".$data->position_name."</option>";
        }

        echo $option;

    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function index($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Employee';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_employee($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_employee($this->input->get(), 'num_rows');
		     $this->load->view('HR/Employee/employee_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'detail') {
            $id_employee = $param2;
			$data['title'] = 'Employee';

            $data['campus'] = $this->employee_model->get_campus();
            $data['gender'] = $this->employee_model->get_gender();
            $data['country'] = $this->employee_model->get_country();
            $data['position'] = $this->employee_model->get_position();
            $data['division'] = $this->employee_model->get_division();
            $data['religion'] = $this->employee_model->get_religion();
            $data['level'] = $this->employee_model->get_level();
            $data['employee_type'] = $this->employee_model->get_employee_type();
            $data['employee_status'] = $this->employee_model->get_employee_status();

            $data['left_bar'] = $this->admin_model->check_navbar();
            $data['employee'] = $this->employee_model->get_employee_by_id($id_employee);
            $this->load->view('HR/Employee/employee_administration_view', $data);
 		} elseif ($param1 == 'teacher') {
            $id_employee = $param2;
            $data['title'] = 'Employee';
            $data['employee_type'] = $this->employee_model->get_employee_type();
            $data['teacher'] = $this->employee_model->get_teacher_by_employee($id_employee);
            $data['left_bar'] = $this->admin_model->check_navbar();
            $data['employee'] = $this->employee_model->get_employee_by_id($id_employee);
            $this->load->view('HR/Employee/employee_teacher_view', $data);
        } elseif ($param1 == 'education') {
            $id_employee = $param2;
            $data['title'] = 'Employee';
            $data['left_bar'] = $this->admin_model->check_navbar();
            $data['employee'] = $this->employee_model->get_employee_by_id($id_employee);
            $data['pagination_data'] = $this->pagination_employee_education($this->input->get(), 'result', $id_employee);
            $data['pagination_total_page'] = $this->pagination_employee_education($this->input->get(), 'num_rows', $id_employee);
            $this->load->view('HR/Employee/employee_education_view', $data);
        } elseif ($param1 == 'sertification') {
            $id_employee = $param2;
            $data['title'] = 'Employee';
            $data['left_bar'] = $this->admin_model->check_navbar();
            $data['employee'] = $this->employee_model->get_employee_by_id($id_employee);
            $data['pagination_data'] = $this->pagination_employee_sertification($this->input->get(), 'result', $id_employee);
            $data['pagination_total_page'] = $this->pagination_employee_sertification($this->input->get(), 'num_rows', $id_employee);
            $this->load->view('HR/Employee/employee_sertification_view', $data);
        } elseif ($param1 == 'functional_position') {
            $id_employee = $param2;
            $data['title'] = 'Employee';
            $data['left_bar'] = $this->admin_model->check_navbar();
            $data['employee'] = $this->employee_model->get_employee_by_id($id_employee);
            $data['pagination_data'] = $this->pagination_employee_functional_position($this->input->get(), 'result', $id_employee);
            $data['pagination_total_page'] = $this->pagination_employee_functional_position($this->input->get(), 'num_rows', $id_employee);
            $this->load->view('HR/Employee/employee_functional_position_view', $data);
        } elseif ($param1 == 'rank') {
            $id_employee = $param2;
            $data['title'] = 'Employee';
            $data['left_bar'] = $this->admin_model->check_navbar();
            $data['employee'] = $this->employee_model->get_employee_by_id($id_employee);
            $data['pagination_data'] = $this->pagination_employee_rank($this->input->get(), 'result', $id_employee);
            $data['pagination_total_page'] = $this->pagination_employee_rank($this->input->get(), 'num_rows', $id_employee);
            $this->load->view('HR/Employee/employee_rank_view', $data);
        } elseif ($param1 == 'research') {
            $id_employee = $param2;
            $data['title'] = 'Employee';
            $data['left_bar'] = $this->admin_model->check_navbar();
            $data['employee'] = $this->employee_model->get_employee_by_id($id_employee);
            $data['pagination_data'] = $this->pagination_employee_research($this->input->get(), 'result', $id_employee);
            $data['pagination_total_page'] = $this->pagination_employee_research($this->input->get(), 'num_rows', $id_employee);
            $this->load->view('HR/Employee/employee_research_view', $data);
        } elseif ($param1 == 'save_employee') {
            $hasil = $this->employee_model->save_employee();
            echo $hasil;
        } elseif ($param1 == 'save_teacher') {
            $hasil = $this->employee_model->save_teacher();
            echo $hasil;
        } elseif ($param1 == 'save_employee_education') {
            $hasil = $this->employee_model->save_employee_education();
            echo $hasil;
        } elseif ($param1 == 'save_employee_sertification') {
            $hasil = $this->employee_model->save_employee_sertification();
            echo $hasil;
        } elseif ($param1 == 'save_employee_functional_position') {
            $hasil = $this->employee_model->save_employee_functional_position();
            echo $hasil;
        } elseif ($param1 == 'save_employee_rank') {
            $hasil = $this->employee_model->save_employee_rank();
            echo $hasil;
        } elseif ($param1 == 'save_employee_research') {
            $hasil = $this->employee_model->save_employee_research();
            echo $hasil;
        } elseif ($param1 == 'delete_employee_education') {
            $url = $this->input->post('url');
            foreach ($_POST['id'] as $id) {
                $this->employee_model->delete_employee_education($id);
            }
           redirect($url);
        } elseif ($param1 == 'delete_employee_sertification') {
            $url = $this->input->post('url');
            foreach ($_POST['id'] as $id) {
                $this->employee_model->delete_employee_sertification($id);
            }
           redirect($url);
        } elseif ($param1 == 'delete_employee_functional_position') {
            $url = $this->input->post('url');
            foreach ($_POST['id'] as $id) {
                $this->employee_model->delete_employee_functional_position($id);
            }
           redirect($url);
        } elseif ($param1 == 'delete_employee_rank') {
            $url = $this->input->post('url');
            foreach ($_POST['id'] as $id) {
                $this->employee_model->delete_employee_rank($id);
            }
           redirect($url);
        } elseif ($param1 == 'delete_employee_research') {
            $url = $this->input->post('url');
            foreach ($_POST['id'] as $id) {
                $this->employee_model->delete_employee_research($id);
            }
           redirect($url);
        } 
	}

	public function pagination_employee($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 20;
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
            $query = $this->db->or_like('employee_name', $get['search'])
            				  ->or_like('employee_initial', $get['search'])
            				  ->or_like('division_name', $get['search'])
            				  ->or_like('position_name', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_division','tb_division.id_division=tb_employee.id_division','left')
        				  ->join('db_hr.tb_position','tb_position.id_position=tb_employee.id_position','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('db_hr.tb_employee')->$param();

        return $query;
    }

    public function pagination_employee_education($get = [], $param = 'result', $id_employee)
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 20;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_education';
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
            $query = $this->db->like('education_college_name', $get['search']);
        }

        $query = $this->db->where('id_employee', $id_employee)
                          ->order_by($get['sortby'], $get['sortby2'])
                          ->get('db_hr.tb_employee_education')->$param();

        return $query;
    }

    public function pagination_employee_sertification($get = [], $param = 'result', $id_employee)
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 20;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_sertification';
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
            $query = $this->db->like('sertification_program_study', $get['search']);
        }

        $query = $this->db->where('id_employee', $id_employee)
                          ->order_by($get['sortby'], $get['sortby2'])
                          ->get('db_hr.tb_employee_sertification')->$param();

        return $query;
    }

    public function pagination_employee_functional_position($get = [], $param = 'result', $id_employee)
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 20;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_functional';
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
            $query = $this->db->like('functional_position', $get['search']);
        }

        $query = $this->db->where('id_employee', $id_employee)
                          ->order_by($get['sortby'], $get['sortby2'])
                          ->get('db_hr.tb_employee_functional_position')->$param();

        return $query;
    }

    public function pagination_employee_rank($get = [], $param = 'result', $id_employee)
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 20;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_rank';
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
            $query = $this->db->like('rank_name', $get['search']);
        }

        $query = $this->db->where('id_employee', $id_employee)
                          ->order_by($get['sortby'], $get['sortby2'])
                          ->get('db_hr.tb_employee_rank')->$param();

        return $query;
    }

    public function pagination_employee_research($get = [], $param = 'result', $id_employee)
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 20;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_research';
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
            $query = $this->db->like('research_title', $get['search']);
        }

        $query = $this->db->where('id_employee', $id_employee)
                          ->order_by($get['sortby'], $get['sortby2'])
                          ->get('db_hr.tb_employee_research')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//
	
}
