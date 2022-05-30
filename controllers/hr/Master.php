<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('HR/Employee_model','employee_model');
        $this->load->model('HR/HR_master_model','master_model');
		$this->load->model('Admin/Admin_master_model','admin_model');
		$this->link_terakhir = $this->config->item('link_terakhir');
	}

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function division($param1 = ''){
        if ($param1 == '') {
             $data['title'] = 'Division';
             $data['left_bar'] = $this->admin_model->check_navbar();
             $data['pagination_data'] = $this->pagination_division($this->input->get(), 'result');
             $data['pagination_total_page'] = $this->pagination_division($this->input->get(), 'num_rows');
             $this->load->view('HR/Master/division_view', $data);
             $this->session->set_userdata('previous_url', $this->link_terakhir);
        } elseif ($param1 == 'add') {
            $aksi = $this->master_model->add_division();
            echo $aksi;
        } elseif ($param1 == 'edit') {
            $aksi = $this->master_model->edit_division();
            echo $aksi;
        } elseif ($param1 == 'delete') {
            $url = $this->input->post('url');
            foreach ($_POST['id'] as $id) {
                $this->master_model->delete_division($id);
            }
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
            redirect($url);
        } else {

        }
    }

	public function pagination_division($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 20;
        if(!isset($get['sortby'])) $get['sortby'] = 'division_name';
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
            $query = $this->db->like('division_name', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
                          ->join('db_hr.tb_employee','tb_employee.id_employee=tb_division.division_updated_by','left')
                          ->get('db_hr.tb_division')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

    public function position($param1 = ''){
        if ($param1 == '') {
             $data['title'] = 'Position';
             $data['left_bar'] = $this->admin_model->check_navbar();
             $data['position'] = $this->master_model->get_position();
             $data['pagination_data'] = $this->pagination_position($this->input->get(), 'result');
             $data['pagination_total_page'] = $this->pagination_position($this->input->get(), 'num_rows');
             $this->load->view('HR/Master/position_view', $data);
             $this->session->set_userdata('previous_url', $this->link_terakhir);
        } elseif ($param1 == 'add') {
            $aksi = $this->master_model->add_position();
            echo $aksi;
        } elseif ($param1 == 'edit') {
            $aksi = $this->master_model->edit_position();
            echo $aksi;
        } elseif ($param1 == 'delete') {
            $url = $this->input->post('url');
            foreach ($_POST['id'] as $id) {
                $this->master_model->delete_position($id);
            }
            $this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
            redirect($url);
        } else {

        }
    }

    public function pagination_position($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'position_name';
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
            $query = $this->db->like('position_name', $get['search']);
        }

        $query = $this->db->select('*, tb_position.id_position')
                          ->order_by($get['sortby'], $get['sortby2'])
                          ->join('db_hr.tb_employee','tb_employee.id_employee=tb_position.position_updated_by','left')
                          ->get('db_hr.tb_position')->$param();

        return $query;
    }
	
}
