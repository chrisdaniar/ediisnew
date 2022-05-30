<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Finance/Finance_master_model','master_model');
		$this->load->model('Recruitment/Leads_model', 'leads_model');
		$this->load->model('Admin/Admin_master_model','admin_model');
		$this->link_terakhir = $this->config->item('link_terakhir');
	}

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function tuition_fee($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Finance - Master';
			 $data['academic_year'] = $this->master_model->get_academic_year();
			 $data['program_type'] = $this->master_model->get_program_type();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_fee($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_fee($this->input->get(), 'num_rows');
		     $this->load->view('Finance/Master/fee_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {

			 $data['title'] = 'Finance - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['fee_desc'] = $this->master_model->get_fee_desc();
		     $data['program_type'] = $this->master_model->get_program_type();
		     $data['academic_year'] = $this->master_model->get_academic_year();
		     $data['additional_fee'] = $this->master_model->get_additional_fee();
		     $this->load->view('Finance/Master/add_fee_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);

		} elseif ($param1 == 'edit') {

			 $id_fee = $this->uri->segment(5);

			 $data['title'] = 'Finance - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['fee_desc'] = $this->master_model->get_fee_desc();
		     $data['academic_year'] = $this->master_model->get_academic_year();
		     $data['additional_fee'] = $this->master_model->get_additional_fee();
		     $data['program_type'] = $this->master_model->get_program_type();
		     $data['fee'] = $this->master_model->get_fee_by_id_fee($id_fee);
		     $data['first_payment'] = $this->master_model->get_first_payment_by_id_fee($id_fee);
		     $data['second_payment'] = $this->master_model->get_second_payment_by_id_fee($id_fee);
		     $data['third_payment'] = $this->master_model->get_third_payment_by_id_fee($id_fee);

		     $this->load->view('Finance/Master/add_fee_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);

		} elseif ($param1 == 'save_tuition_fee') {
			$this->master_model->save_tuition_fee(); 
		} elseif ($param1 == 'save_additional_fee') {
			$post = json_decode($this->input->post('data'));
			foreach ($post as $key) {
					$cek = $this->db->where('id_program_type', $key->id_program_type)
									->where('id_academic_year', $key->id_academic_year)
									->get('tb_fee')
									->row();
					if ($cek->id_fee != null) {

					$data['additional_fee'] = preg_replace('/[^0-9]/', '', $key->additional_fee);
					$data['id_additional_part'] = $key->id_additional_part;
					$data['id_fee_desc'] = $key->id_fee_desc;
					$data['id_fee'] = $cek->id_fee;

					$this->db->insert('tb_additional_fee', $data);
					
				}	
			}

		} elseif ($param1 == 'update_fee') {
			$this->master_model->update_fee(); 
		} elseif ($param1 == 'update_tuition_fee') {
			$this->master_model->update_tuition_fee(); 
		} elseif ($param1 == 'update_additional_fee') {
			$post = json_decode($this->input->post('data'));
			foreach ($post as $key) {

					$cek = $this->db->where('id_fee', $key->id_fee)
									->where('id_fee_desc', $key->id_fee_desc)
									->get('tb_additional_fee')
									->row();

					 if ($cek->additional_fee == null) {

					 	
							$data['additional_fee'] = preg_replace('/[^0-9]/', '', $key->additional_fee);
							$data['id_additional_part'] = $key->id_additional_part;
							$data['id_fee_desc'] = $key->id_fee_desc;
							$data['id_fee'] = $key->id_fee;

							$this->db->insert('tb_additional_fee', $data);

					} else {
						
							$data['additional_fee'] = preg_replace('/[^0-9]/', '', $key->additional_fee);
							$data['id_additional_part'] = $key->id_additional_part;

							$this->db->where('id_additional_fee', $key->id_additional_fee)
									 ->update('tb_additional_fee', $data);
					}
			}

		} elseif ($param1 == 'copy') {
			$this->master_model->copy_fee(); 

		} elseif ($param1 == 'delete') {
			$url = 'http://'.$this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_fee($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_fee($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_fee';
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
            $query = $this->db->or_like('program_type', $get['search'])
            				  ->or_like('academic_year', $get['search']);
        }

        $query = $this->db->join('tb_course','tb_course.id_course=tb_fee.id_course','left')
        				  ->join('tb_intended_program','tb_intended_program.id_intended_program=tb_program_type.id_intended_program','left')
        				  ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_fee.id_academic_year','left')
        				   
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_fee')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function discount($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Discount - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_discount($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_discount($this->input->get(), 'num_rows');
		     $this->load->view('Finance/Master/discount_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_discount();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_discount();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_discount($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_discount($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_discount';
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
            $query = $this->db->like('discount_name', $get['search'])
            				  ->or_like('discount_total', $get['search'])
            				  ->or_like('discount_type', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_discount')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function fee_type($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Fee Type- Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_fee_type($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_fee_type($this->input->get(), 'num_rows');
		     $this->load->view('Finance/Master/fee_type_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_fee_type();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_fee_type();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_fee_type($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_fee_type($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_fee_type';
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
            $query = $this->db->like('fee_type', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_fee_type')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function fee_desc($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Fee Type- Master';
			 $data['fee_type'] = $this->master_model->get_fee_type();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_fee_desc($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_fee_desc($this->input->get(), 'num_rows');
		     $this->load->view('Finance/Master/fee_desc_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_fee_desc();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_fee_desc();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_fee_desc($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_fee_desc($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_fee_desc';
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
            $query = $this->db->like('fee_type', $get['search'])
            				  ->or_like('fee_desc', $get['search']);
        }

        $query = $this->db->join('tb_fee_type','tb_fee_type.id_fee_type=tb_fee_desc.id_fee_type','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_fee_desc.fee_desc_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_fee_desc')->$param();

        return $query;
    }

    public function get_fee_desc_by_id(){
        $id_fee_desc = $this->input->post('id_fee_desc');
        $data = $this->master_model->get_fee_desc_by_id($id_fee_desc);
        echo json_encode($data);
    }
    
	
}
