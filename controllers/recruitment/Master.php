<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Recruitment/Recruitment_master_model','master_model');
		$this->load->model('Marketing/Marketing_master_model','marketing_model');
		$this->load->model('Recruitment/Leads_model','leads_model');
		$this->load->model('Admin/Admin_master_model','admin_model');
		$this->link_terakhir = $this->config->item('link_terakhir');

		ini_set('display_errors', 0);

		if ($this->session->userdata('logged_in') == FALSE) {
			redirect('login');
		}
	}

	public function autocomplete($param1 = ''){
		if ($param1 == 'get_school') {

			if(isset($_GET['term'])){

	          $school_name = $_GET['term'];

	          $result = $this->master_model->get_autocomplete_school($_GET['term'], $school_name);

	          if(count($result) > 0){

	          	foreach ($result as $row) {

					$result_array[] = array(
						'label' => $row->school_name,
	                	'id' => $row->id_school,
	            		'education' => $row->education);
				}

				echo json_encode($result_array);

		      }
		    }
		} else if($param1 == 'get_student'){

			if(isset($_GET['term'])){

	          $leads_name = $_GET['term'];

	          $result = $this->master_model->get_autocomplete_paid_leads($_GET['term'], $leads_name);

	          if(count($result) > 0){

	          	foreach ($result as $row) {

					$result_array[] = array(
						'label' => $row->name.' '.$row->family_name.' - '.$row->student_id.'('.$row->course_abstract.')',
	                	'id' => $row->id_leads);
				}

				echo json_encode($result_array);

		      }
		    }
		} else if($param1 == 'get_student_all'){

			if(isset($_GET['term'])){

	          $leads_name = $_GET['term'];

	          $result = $this->master_model->get_autocomplete_student($_GET['term'], $leads_name);

	          if(count($result) > 0){

	          	foreach ($result as $row) {

					$result_array[] = array(
						'label' => $row->name.' '.$row->family_name,
						'family_name' => $row->family_name,
						'id_student' => $row->id_student,
						'id_leads' => $row->id_leads,
						'student_id' => $row->student_id,
						'dob' => $row->dob,
						'pob' => $row->pob,
						'leads_phone_code' => $row->leads_phone_code,
						'phone' => $row->phone,
						'id_intended_program' => $row->id_intended_program,
						'id_gender' => $row->id_gender,
	                	'name' => $row->name);
				}

				echo json_encode($result_array);

		      }
		    }
		}

	}

	public function get_intended_program_by_program($param = NULL) {
		$id_program = $param;
		$result = $this->db->where('id_program', $id_program)->get('tb_intended_program')->result();
		$option = "";
		$option .= '<option value=""> -- Select Intended Program --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intended_program."' >".$data->intended_program."</option>";
		}
		echo $option;

	}

	public function get_intake_month_by_intended_program($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_intended_program = $param;
		$result = $this->db->join('tb_month','tb_month.id_month=tb_intake_month.id_month')
							->where('id_intended_program', $id_intended_program)
							->get('tb_intake_month')->result();
		$option = "";
		$option .= '<option value=""> -- Select Month --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intake_month."' >".$data->month."</option>";
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
		$option .= '<option value=""> -- Select Month --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intake_month."' >".$data->month."</option>";
		}
		echo $option;

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

	public function get_specialist_by_course($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_course = $param;
		$result = $this->db->where('id_course', $id_course)->get('tb_specialist')->result();
		$option = "";
		$option .= '<option value=""> -- Select Specialist --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_specialist."' >".$data->specialist_name."</option>";
		}
		echo $option;

	}

	public function get_intended_program_by_intended_program() {
		$id_program = $this->input->post('id_program');
		$id_intended_program = $this->input->post('id_intended_program');
		
		$result = $this->db->where('id_program', $id_program)->where('id_intended_program !=', $id_intended_program)->get('tb_intended_program')->result();
		$row = $this->db->where('id_intended_program', $id_intended_program)->get('tb_intended_program')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Intended Program -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_intended_program.'" selected="selected"> '.$row->intended_program.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intended_program."' >".$data->intended_program."</option>";
		}
		echo $option;

	}

	public function get_course_by_course() {
		$id_course = $this->input->post('id_course');
		$id_intended_program = $this->input->post('id_intended_program');
		
		$result = $this->db->where('id_intended_program', $id_intended_program)->where('id_course !=', $id_course)->get('tb_course')->result();
		$row = $this->db->where('id_course', $id_course)->get('tb_course')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Course -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_course.'" selected="selected"> '.$row->course.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_course."' >".$data->course."</option>";
		}
		echo $option;

	}

	public function get_specialist_by_specialist() {
		$id_course = $this->input->post('id_course');
		$id_specialist = $this->input->post('id_specialist');
		
		$result = $this->db->where('id_course', $id_course)->where('id_specialist !=', $id_course)->get('tb_specialist')->result();
		$row = $this->db->where('id_specialist', $id_specialist)->get('tb_specialist')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Specialist -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_specialist.'" selected="selected"> '.$row->specialist_name.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_specialist."' >".$data->specialist_name."</option>";
		}
		echo $option;

	}


	public function get_course() {
		$id_course = $this->input->post('id_course');
		
		$result = $this->db->where('id_course !=', $id_course)->get('tb_course')->result();
		$row = $this->db->where('id_course', $id_course)->get('tb_course')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Course -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_course.'" selected="selected"> '.$row->course.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_course."' >".$data->course."</option>";
		}
		echo $option;

	}

	public function get_intake_month_by_intake_month() {
		$id_intake_month = $this->input->post('id_intake_month');
		$id_intended_program = $this->input->post('id_intended_program');
		
		$result = $this->db->join('tb_month','tb_month.id_month=tb_intake.id_month')
						   ->where('id_intended_program', $id_intended_program)->where('id_intake_month !=', $id_intake_month)->get('tb_intake_month')->result();
		$row = $this->db->where('id_intake_month', $id_intake_month)->get('tb_intake_month')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Month -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_intake_month.'" selected="selected"> '.$row->month.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intake_month."' >".$data->month."</option>";
		}
		echo $option;

	}

	public function get_intake_month_by_intake_month_course() {
		$id_intake_month = $this->input->post('id_intake_month');
		$id_course = $this->input->post('id_course');

		$cek_intended_program = $this->db->join('tb_intended_program','tb_intended_program.id_intended_program=tb_course.id_intended_program')
										->where('id_course', $id_course)
										->get('tb_course')->row();

		$id_intended_program = $cek_intended_program->id_intended_program;
		
		$result = $this->db->join('tb_month','tb_month.id_month=tb_intake_month.id_month')
						   ->where('id_intended_program', $id_intended_program)
						   ->where('id_intake_month !=', $id_intake_month)
						   ->get('tb_intake_month')->result();

		$row = $this->db->join('tb_month','tb_month.id_month=tb_intake_month.id_month')->where('id_intake_month', $id_intake_month)->get('tb_intake_month')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Month -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_intake_month.'" selected="selected"> '.$row->month.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intake_month."' >".$data->month."</option>";
		}
		echo $option;

	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function school($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'School';
		     $data['country'] = $this->master_model->get_country();
		     $data['qualification'] = $this->master_model->get_qualification();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_school($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_school($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Master/school_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} if ($param1 == 'leads') {
			 $id_school = $param2;
			 $data['title'] = 'School';
			 $data['school'] = $this->master_model->get_school_by_id($id_school);
		     $data['status'] = $this->leads_model->get_status();
		     $data['marketing_source'] = $this->leads_model->get_marketing_source();
		     $data['pagination_data'] = $this->pagination_school_leads($this->input->get(), 'result', $id_school);
		     $data['pagination_total_page'] = $this->pagination_school_leads($this->input->get(), 'num_rows', $id_school);
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $this->load->view('Recruitment/Master/school_leads_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'save_school') {
			$aksi = $this->marketing_model->save_school();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_school($id);
			}
			redirect($url);
		} else {

		}
	}

	 public function pagination_school($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_school.id_school';
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
            $query = $this->db->like('school_name', $get['search'])
            				  ->or_like('school_address', $get['search'])
            				  ->or_like('country_name', $get['search']);
        }

        $query = $this->db->select('*, count(id_leads) as total,tb_school.id_school, tb_school.id_regency, tb_school.id_province, tb_school.id_country')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->join('tb_leads','tb_leads.id_school=tb_school.id_school','left')
        				  ->join('tb_regency','tb_regency.id_regency=tb_school.id_regency','left')
        				  ->join('tb_province','tb_province.id_province=tb_school.id_province','left')
        				  ->join('tb_country','tb_country.id_country=tb_school.id_country','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_school.school_updated_by','left')
        				  ->group_by('tb_school.id_school')
                          ->get('tb_school')->$param();

        return $query;
    }

    public function pagination_school_leads($get = [], $param = 'result', $id_school = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_leads';
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
            $query = $this->db->like('CONCAT(name," ",family_name)', $get['search']);
        }

        if (isset($get['id_marketing_source_filter'])) {
            $query = $this->db->where('tb_leads.id_marketing_source', $get['id_marketing_source_filter']);
        }

        if (isset($get['id_status_filter'])) {
            $query = $this->db->where('tb_leads.id_status', $get['id_status_filter']);
        }

        $query = $this->db->select('*, tb_leads.id_regency, tb_regency.id_province, create_date')
        				  ->where('tb_leads.id_school', $id_school)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->join('tb_regency','tb_regency.id_regency=tb_leads.id_regency','left')
        				  ->join('tb_province','tb_province.id_province=tb_regency.id_province','left')
        				  ->join('tb_marketing_source','tb_marketing_source.id_marketing_source=tb_leads.id_marketing_source','left')
        				  ->join('tb_status','tb_status.id_status=tb_leads.id_status','left')
                          ->get('tb_leads')->$param();

        return $query;
    }

     public function get_province_by_country($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_country = $param;
		$result = $this->master_model->get_province_by_country($id_country);
		$option = "";
		$option .= '<option value=""> -- Select Province --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_province."' >".$data->province_name."</option>";
		}
		echo $option;

	}

    public function get_regency_by_province($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_province = $param;
		$result = $this->master_model->get_regency_by_province($id_province);
		$option = "";
		$option .= '<option value=""> -- Select Regency --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_regency."' >".$data->regency_name."</option>";
		}
		echo $option;

	}

	public function get_country_by_country() {
		// $layanan =$this->input->post('layanan');
		$id_country = $this->input->post('id_country');
		
		$result = $this->db->where('id_country !=', $id_country)->get('tb_country')->result();
		$row = $this->db->where('id_country', $id_country)->get('tb_country')->row();

		$option = "";
		$option .= '<option value="'.$row->id_country.'" selected="selected"> '.$row->country_name.' </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_country."' >".$data->country_name."</option>";
		}
		echo $option;

	}

	public function get_province_by_province() {
		// $layanan =$this->input->post('layanan');
		$id_country = $this->input->post('id_country');
		$id_province = $this->input->post('id_province');
		
		$result = $this->db->where('id_country', $id_country)->where('id_province !=', $id_province)->get('tb_province')->result();
		$row = $this->db->where('id_province', $id_province)->get('tb_province')->row();

		$option = "";
		$option .= '<option value="'.$row->id_province.'" selected="selected"> '.$row->province_name.' </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_province."' >".$data->province_name."</option>";
		}
		echo $option;

	}

	public function get_regency_by_regency() {
		// $layanan =$this->input->post('layanan');
		$id_province = $this->input->post('id_province');
		$id_regency = $this->input->post('id_regency');
		
		$result = $this->db->where('id_province', $id_province)->where('id_regency !=', $id_regency)->get('tb_regency')->result();
		$row = $this->db->where('id_regency', $id_regency)->get('tb_regency')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select City -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_regency.'" selected="selected"> '.$row->regency_name.' </option>';
		}

		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_regency."' >".$data->regency_name."</option>";
		}
		echo $option;

	}

	public function get_district_by_district() {
		// $layanan =$this->input->post('layanan');
		$id_regency = $this->input->post('id_regency');
		$id_district = $this->input->post('id_district');
		
		$result = $this->db->where('id_regency', $id_regency)->where('id_district !=', $id_district)->get('tb_district')->result();
		$row = $this->db->where('id_district', $id_district)->get('tb_district')->row();

		$option = "";
		$option .= '<option value="'.$row->id_district.'" selected="selected"> '.$row->district_name.' </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_district."' >".$data->district_name."</option>";
		}
		echo $option;

	}

	public function get_village_by_village() {
		// $layanan =$this->input->post('layanan');
		$id_district = $this->input->post('id_district');
		$id_village = $this->input->post('id_village');
		
		$result = $this->db->where('id_district', $id_district)->where('id_village !=', $id_village)->get('tb_village')->result();
		$row = $this->db->where('id_village', $id_village)->get('tb_village')->row();

		$option = "";
		$option .= '<option value="'.$row->id_village.'" selected="selected"> '.$row->village_name.' </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_village."' >".$data->village_name."</option>";
		}
		echo $option;

	}

	public function get_district_by_regency($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_regency = $param;
		$result = $this->master_model->get_district_by_regency($id_regency);
		$option = "";
		$option .= '<option value=""> -- Select District --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_district."' >".$data->district_name."</option>";
		}
		echo $option;

	}

	public function get_village_by_district($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_district = $param;
		$result = $this->master_model->get_village_by_district($id_district);
		$option = "";
		$option .= '<option value=""> -- Select Village --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_village."' >".$data->village_name."</option>";
		}
		echo $option;

	}

	public function get_event_by_event_type($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_event_type = $param;
		$result = $this->marketing_model->get_event_by_event_type($id_event_type);
		$option = "";
		$option .= '<option value=""> -- Select Event --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_event."' >".$data->event_name."</option>";
		}
		echo $option;

	}

	
	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function class_($param1 = '', $param2 = ''){
		if ($param1 == 'view') {
			 $data['title'] = 'Class';
			 $id_qualification = $param2;
			 $data['qualification'] = $this->master_model->get_qualification_by_id($id_qualification);
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_class($this->input->get(), 'result', $id_qualification);
		     $data['pagination_total_page'] = $this->pagination_class($this->input->get(), 'num_rows', $id_qualification);
		     $this->load->view('Recruitment/Requirement/class_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_class();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_class();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_class($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_class($get = [], $param = 'result', $id_qualification = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_class';
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
            $query = $this->db->like('class_name', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_class.class_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->where('id_qualification', $id_qualification)
        				  //->group_by('tb_school.id_school')
                          ->get('tb_class')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function english_type($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'English Type';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_english_type($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_english_type($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Requirement/english_type_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_english_type();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_english_type();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_english_type($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_english_type($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_document_type';
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
            $query = $this->db->like('document_type', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
        				  ->where('id_category', 2)
        				  //->group_by('tb_school.id_school')
                          ->get('tb_document_type')->$param();

        return $query;
    }
	
    public function cek_semester_trimester() {
		// $layanan =$this->input->post('layanan');
		$id_intended_program = $this->input->post('id_intended_program');

		$result = $this->db->where('id_intended_program', $id_intended_program)
						   ->get('tb_intended_program')
						   ->row();

		echo $result->period;

	}

    public function get_semester_by_academic_year() {
		// $layanan =$this->input->post('layanan');
		$id_intended_program = $this->input->post('id_intended_program');
		$id_academic_year = $this->input->post('id_academic_year');

		$result = $this->db->where('id_intended_program', $id_intended_program)
						   ->where('id_academic_year', $id_academic_year)
						   ->where('semester_start_date <=', date('Y-m-d'))
						   ->where('semester_end_date >=', date('Y-m-d'))
						   ->get('tb_semester')
						   ->result();
		$option = "";
		$option .= '<option value=""> -- Select Semester --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_semester."' >".$data->semester."</option>";
		}
		echo $option;

	}

	public function get_trimester_by_academic_year() {
		// $layanan =$this->input->post('layanan');
		$id_intended_program = $this->input->post('id_intended_program');
		$id_academic_year = $this->input->post('id_academic_year');
		
		$result = $this->db->where('id_intended_program', $id_intended_program)
						   ->where('id_academic_year', $id_academic_year)
						   //->where('trimester_start_date <=', date('Y-m-d'))
						   //->where('trimester_end_date >=', date('Y-m-d'))
						   ->get('tb_trimester')
						   ->result();
		$option = "";
		$option .= '<option value=""> -- Select Trimester --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_trimester."' >".$data->trimester."</option>";
		}
		echo $option;

	}

	public function get_class_by_qualification($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_qualification = $param;
		$result = $this->db->where('id_qualification', $id_qualification)->order_by('class_name','asc')->get('tb_class')->result();
		$option = "";
		$option .= '<option value=""> -- Select Class --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_class."' >".$data->class_name."</option>";
		}
		echo $option;

	}

	public function get_class_by_class() {
		$id_qualification = $this->input->post('id_qualification');
		$id_class = $this->input->post('id_class');
		
		$result = $this->db->where('id_qualification', $id_qualification)->where('id_class !=', $id_class)->get('tb_class')->result();
		$row = $this->db->where('id_class', $id_class)->get('tb_class')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Grade -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_class.'" selected="selected"> '.$row->class_name.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_class."' >".$data->class_name."</option>";
		}
		echo $option;

	}

	public function get_qualification_by_qualification() {
		// $layanan =$this->input->post('layanan');
		$id_qualification = $this->input->post('id_qualification');
		
		$result = $this->db->where('id_qualification !=', $id_qualification)->get('tb_qualification')->result();
		$row = $this->db->where('id_qualification', $id_qualification)->get('tb_qualification')->row();

		$option = "";
		$option .= '<option value="'.$row->id_qualification.'" selected="selected"> '.$row->qualification.' </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_qualification."' >".$data->qualification."</option>";
		}
		echo $option;

	}

	 //-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function special_subject($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Special Subject - Master';
			 $data['course'] = $this->master_model->get_course();
			 $data['class'] = $this->master_model->get_class();
			 $data['detail_requirement'] = $this->master_model->get_detail_requirement();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_special_subject($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_special_subject($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Requirement/special_subject_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_special_subject();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_special_subject();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_special_subject($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	public function pagination_special_subject($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_special_subject.id_special_subject';
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
            $query = $this->db->like('special_subject', $get['search'])
            				  ->or_like('intended_program', $get['search'])
            				  ->or_like('course', $get['search']);
        }

        $query = $this->db->select('*, count(id_detail_special_subject) as total, tb_special_subject.id_special_subject')
        				  ->join('tb_detail_special_subject','tb_detail_special_subject.id_special_subject=tb_special_subject.id_special_subject','left')
        				  ->join('tb_course','tb_course.id_course=tb_special_subject.id_course','left')
        				  ->join('tb_intended_program','tb_intended_program.id_intended_program=tb_course.id_intended_program','left')
        				  ->join('tb_program','tb_program.id_program=tb_intended_program.id_program','left')
        				  ->join('tb_class','tb_class.id_class=tb_special_subject.id_class','left')
        				  ->join('tb_detail_requirement','tb_detail_requirement.id_detail_requirement=tb_special_subject.id_detail_requirement','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_special_subject.special_subject_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->group_by('tb_special_subject.id_special_subject')
        				  //->group_by('tb_school.id_school')
                          ->get('tb_special_subject')->$param();

        return $query;
    }
   
    
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function document_type($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Document Type - Master';
			 $data['category'] = $this->master_model->get_category();
			 $data['status_leads'] = $this->leads_model->get_status('1');
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_document_type($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_document_type($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Master/document_type_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_document_type();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_document_type();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_document_type($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	public function pagination_document_type($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_document_type';
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
            $query = $this->db->like('document_type', $get['search']);
        }

        $query = $this->db->join('tb_category','tb_category.id_category=tb_document_type.id_category','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_document_type.document_type_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_document_type')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function average_type($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Average Type - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_average_type($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_average_type($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Requirement/average_type_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_average_type();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_average_type();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_average_type($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	public function pagination_average_type($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_average_type';
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
            $query = $this->db->like('average', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_average_type')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function average($param1 = '', $param2 = ''){
		if ($param1 == 'view') {
			 $data['title'] = 'Average - Master';
			 $id_qualification = $param2;
			 $data['average_type'] = $this->master_model->get_average_type();
			 $data['qualification'] = $this->master_model->get_qualification_by_id($id_qualification);
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_average($this->input->get(), 'result', $id_qualification);
		     $data['pagination_total_page'] = $this->pagination_average($this->input->get(), 'num_rows', $id_qualification);
		     $this->load->view('Recruitment/Requirement/average_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_average();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_average();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_average($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	public function pagination_average($get = [], $param = 'result', $id_qualification = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_average';
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
            $query = $this->db->like('average', $get['search']);
        }

        $query = $this->db->join('tb_average_type','tb_average_type.id_average_type=tb_average.id_average_type','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_average.average_updated_by','left')
        				  ->where('tb_average.id_qualification', $id_qualification)
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_average')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function english_score($param1 = '', $param2 = ''){
		if ($param1 == 'view') {
			 $id_document_type = $param2;
			 $data['title'] = 'English Score - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['qualification'] = $this->master_model->get_qualification();
		     $data['class'] = $this->master_model->get_class();
		     $data['intended_program'] = $this->master_model->get_intended_program();
		     $data['document_type'] = $this->db->where('id_document_type', $id_document_type)->get('tb_document_type')->row();
		     $data['pagination_data'] = $this->pagination_english_score($this->input->get(), 'result', $id_document_type);
		     $data['pagination_total_page'] = $this->pagination_english_score($this->input->get(), 'num_rows', $id_document_type);
		     $this->load->view('Recruitment/Requirement/english_score_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_english_score();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_english_score();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_english_score($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_english_score($get = [], $param = 'result', $id_document_type = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_english_score';
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
            $query = $this->db->like('english_score', $get['search'])
            				  ->or_like('qualification', $get['search'])
            				  ->or_like('class_name', $get['search'])
            				  ->or_like('intended_program', $get['search'])
            				  ->or_like('course', $get['search']);
        }

        $query = $this->db->join('tb_course','tb_course.id_course=tb_english_score.id_course','left')
        				  ->join('tb_intended_program','tb_intended_program.id_intended_program=tb_course.id_intended_program','left')
        				  ->join('tb_class','tb_class.id_class=tb_english_score.id_class','left')
        				  ->join('tb_qualification','tb_qualification.id_qualification=tb_english_score.id_qualification','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_english_score.english_score_updated_by','left')
        				  ->where('id_document_type', $id_document_type)
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_english_score')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function detail_special_subject($param1 = '', $param2 = ''){
		if ($param1 == 'view') {
			 $id_special_subject = $param2;
			 $data['title'] = 'Special Subject - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $special_subject = $this->master_model->get_special_subject_by_id($id_special_subject);
		     $data['subject'] = $this->master_model->get_subject_by_course($special_subject->id_course);
		     $data['special_subject'] = $this->master_model->get_special_subject_by_id($id_special_subject);
		     $data['pagination_data'] = $this->pagination_detail_special_subject($this->input->get(), 'result', $id_special_subject);
		     $data['pagination_total_page'] = $this->pagination_detail_special_subject($this->input->get(), 'num_rows', $id_special_subject);
		     $this->load->view('Recruitment/Requirement/detail_special_subject_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_detail_special_subject();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_detail_special_subject();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_detail_special_subject($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_detail_special_subject($get = [], $param = 'result', $id_special_subject = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_detail_special_subject';
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
            $query = $this->db->like('subject_name', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_detail_special_subject.detail_special_subject_updated_by','left')
        				  ->join('tb_subject','tb_subject.id_subject=tb_detail_special_subject.id_subject','left')
        				  ->where('id_special_subject', $id_special_subject)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_detail_special_subject')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function qualification($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Qualification - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_qualification($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_qualification($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Requirement/qualification_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_qualification();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_qualification();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_qualification($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_qualification($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_qualification.id_qualification';
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
            $query = $this->db->like('qualification', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_qualification.qualification_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_qualification')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function detail_requirement($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Detail Requirement - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_detail_requirement($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_detail_requirement($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Requirement/detail_requirement_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_detail_requirement();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_detail_requirement();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_detail_requirement($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_detail_requirement($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_detail_requirement';
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
            $query = $this->db->like('detail_requirement', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_detail_requirement.detail_requirement_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_detail_requirement')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function alphabet($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Alphabet - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_alphabet($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_alphabet($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Master/alphabet_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_alphabet();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_alphabet();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_alphabet($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_alphabet($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_alphabet';
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
            $query = $this->db->like('alphabet', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_alphabet.alphabet_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_alphabet')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function score_scale($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Score Scale - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['qualification'] = $this->master_model->get_qualification();
		     $data['alphabet'] = $this->master_model->get_alphabet();
		     $data['pagination_data'] = $this->pagination_score_scale($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_score_scale($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Requirement/score_scale_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_score_scale();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_score_scale();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_score_scale($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_score_scale($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_score_scale';
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
            $query = $this->db->like('alphabet', $get['search'])
            				  ->or_like('qualification', $get['search'])
            				  ->or_like('min_score', $get['search'])
            				  ->or_like('max_score', $get['search'])
            				  ->or_like('scale_start_date', $get['search'])
            				  ->or_like('scale_end_date', $get['search']);
        }

        $query = $this->db->join('tb_qualification','tb_qualification.id_qualification=tb_score_scale.id_qualification','left')
        				  ->join('tb_alphabet','tb_alphabet.id_alphabet=tb_score_scale.id_alphabet','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_score_scale.scale_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_score_scale')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function requirement($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Requirement - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['qualification'] = $this->master_model->get_qualification();
		     $data['alphabet'] = $this->master_model->get_alphabet();
		     $data['class'] = $this->master_model->get_class();
		     $data['intended_program'] = $this->master_model->get_intended_program();
		     $data['detail_requirement'] = $this->master_model->get_detail_requirement();
		     $data['pagination_data'] = $this->pagination_requirement($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_requirement($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Requirement/requirement_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_requirement();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_requirement();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_requirement($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_requirement($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_requirement';
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
            $query = $this->db->like('qualification', $get['search'])
            				  ->or_like('detail_requirement', $get['search'])
            				  ->or_like('requirement_start_date', $get['search'])
            				  ->or_like('requirement_end_date', $get['search'])
            				  ->or_like('class_name', $get['search']);
        }

        $query = $this->db->join('tb_detail_requirement','tb_detail_requirement.id_detail_requirement=tb_requirement.id_detail_requirement','left')
        				  ->join('tb_qualification','tb_qualification.id_qualification=tb_requirement.id_qualification','left')
        				  ->join('tb_class','tb_class.id_class=tb_requirement.id_class','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_requirement.requirement_updated_by','left')
        				  ->join('tb_specialist','tb_specialist.id_specialist=tb_requirement.id_specialist')
        				  ->join('tb_course','tb_course.id_course=tb_specialist.id_course','left')
        				  ->join('tb_intended_program','tb_intended_program.id_intended_program=tb_course.id_intended_program','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_requirement')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function requirement_score($param1 = '', $param2 = ''){
		if ($param1 == 'view') {
			 $data['title'] = 'Requirement Score - Master';
			 $id_requirement = $param2;
			 $detail = $this->master_model->get_requirement_by_id($id_requirement);
			 $data['requirement'] = $this->master_model->get_requirement_by_id($id_requirement);
			 $data['average'] = $this->master_model->get_average_by_qualification($detail->id_qualification);
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_requirement_score($this->input->get(), 'result', $id_requirement);
		     $data['pagination_total_page'] = $this->pagination_requirement_score($this->input->get(), 'num_rows', $id_requirement);
		     $this->load->view('Recruitment/Requirement/requirement_score_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_requirement_score();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_requirement_score();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_requirement_score($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	public function pagination_requirement_score($get = [], $param = 'result', $id_requirement = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_requirement_score';
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
            $query = $this->db->like('average', $get['search']);
        }

        $query = $this->db->join('tb_average','tb_average.id_average=tb_requirement_score.id_average','left')
        				  ->join('tb_average_type','tb_average_type.id_average_type=tb_average.id_average_type')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_requirement_score.requirement_score_updated_by','left')
        				  ->where('tb_requirement_score.id_requirement', $id_requirement)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_requirement_score')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function document_terms($param1 = '', $param2 = ''){
		if ($param1 == 'view') {
			 $id_requirement = $param2;
			 $data['title'] = 'Document Terms - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['document_type'] = $this->master_model->get_document_type();
		     $data['month'] = $this->master_model->get_month();
		     $data['terms'] = $this->master_model->get_requirement_by_id($id_requirement);
		     $data['pagination_data'] = $this->pagination_document_terms($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_document_terms($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Requirement/document_terms_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_document_terms();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_document_terms();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_document_terms($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_document_terms($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_document_terms';
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
            $query = $this->db->like('document_type', $get['search'])
            				  ->or_like('month', $get['search']);
        }

        $query = $this->db->join('tb_month','tb_month.id_month=tb_document_terms.id_month','left')
        				  ->join('tb_document_type','tb_document_type.id_document_type=tb_document_terms.id_document_type','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_document_terms')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function intake($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Intake';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_intake($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_intake($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Master/intake_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			 $data['title'] = 'Intake';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['academic_year'] = $this->master_model->get_academic_year();
		     $data['intended_program'] = $this->master_model->get_intended_program();
		     $data['pagination_data'] = $this->pagination_intake($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_intake($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Master/intake_form_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'edit') {
			 $id_intake = $this->uri->segment(5);

			 $data['title'] = 'Intake';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['academic_year'] = $this->master_model->get_academic_year();
		     $data['intended_program'] = $this->master_model->get_intended_program();
		     $data['intake'] = $this->master_model->get_intake_by_id($id_intake);
		     $data['pagination_data'] = $this->pagination_intake($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_intake($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Master/intake_form_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'edits') {
			 $id_intake = $this->uri->segment(5);

			 $data['title'] = 'Intake';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['academic_year'] = $this->master_model->get_academic_year();
		     $data['intended_program'] = $this->master_model->get_intended_program();
		     $data['intake'] = $this->master_model->get_intake_by_id($id_intake);
		     $data['pagination_data'] = $this->pagination_intake($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_intake($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Master/intake_form_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'invitation') {
			 $id_intake = $this->uri->segment(5);

			 $data['title'] = 'Intake';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['intake'] = $this->master_model->get_intake_by_id($id_intake);
		     $this->load->view('Recruitment/Master/invitation_page_view', $data);
		} elseif ($param1 == 'save_intake') {
			$aksi = $this->master_model->save_intake();
            echo $aksi;
		} elseif ($param1 == 'update_intake') {
			$aksi = $this->master_model->update_intake();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_intake($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		}  elseif ($param1 == 'add_intake_document') {
			$aksi = $this->master_model->add_intake_document();
			echo $aksi;

		} elseif ($param1 == 'get_intake_document_by_id_intake') {
			$id_intake = $this->input->post('id_intake');
			$data = $this->master_model->get_intake_document_by_id_intake($id_intake);
			echo json_encode($data);
		} elseif ($param1 == 'delete_intake_document') {
			$hapus = $this->input->post('hapus_data');
	        $id_hapus = $this->input->post('id_hapus');
	        $hasil = $this->master_model->delete_intake_document($hapus, $id_hapus);
	        echo $hasil;
		} else {
				
		}
	}

	public function pagination_intake($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_intake';
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
            $query = $this->db->like('year', $get['search'])
            				  ->or_like('month', $get['search']);
        }

        $query = $this->db->join('tb_intake_month','tb_intake_month.id_intake_month=tb_intake.id_intake_month','left')
        				  ->join('tb_month','tb_month.id_month=tb_intake_month.id_month','left')
        				  ->join('tb_intended_program','tb_intended_program.id_intended_program=tb_intake.id_intended_program','left')
        				  ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_intake.id_academic_year','left')
        				   ->join('db_hr.tb_employee','tb_employee.id_employee=tb_intake.intake_updated_by','left')	 
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_intake')->$param();

        return $query;
    }

    
    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function campus($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Campus - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['acreditation'] = $this->admin_model->check_acreditation();
		     $data['province'] = $this->master_model->get_province();
		     $data['pagination_data'] = $this->pagination_campus($this->input->get(), 'result', $data['acreditation']);
		     $data['pagination_total_page'] = $this->pagination_campus($this->input->get(), 'num_rows', $data['acreditation']);
		     $this->load->view('Recruitment/Master/campus_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_campus();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_campus();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_campus($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_campus($get = [], $param = 'result', $acreditation)
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_campus.id_campus';
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
            $query = $this->db->like('semester', $get['search'])
            				  ->or_like('academic_year', $get['search']);
        }

        $query = $this->db->select('*, tb_campus.id_campus')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_campus.campus_updated_by','left')
        				  ->join('tb_province','tb_province.id_province = tb_campus.id_province','left');
        if ($acreditation == '1') {
        	$query = $this->db->where('campus_acreditation','1');
        }
        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_campus')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function intake_month($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Intake Month - Master';
			 $data['intended_program'] = $this->master_model->get_intended_program();
			 $data['month'] = $this->master_model->get_month();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_intake_month($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_intake_month($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Master/intake_month_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_intake_month();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_intake_month();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_intake_month($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_intake_month($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_intake_month';
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
            $query = $this->db->like('intended_program', $get['search'])
            				  ->or_like('month', $get['search']);
        }

        $query = $this->db->join('tb_intended_program','tb_intended_program.id_intended_program=tb_intake_month.id_intended_program','left')
        				  ->join('tb_month','tb_month.id_month=tb_intake_month.id_month','left')
        				   ->join('db_hr.tb_employee','tb_employee.id_employee=tb_intake_month.intake_month_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_intake_month')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function category($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Category - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_category($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_category($this->input->get(), 'num_rows');
		     $this->load->view('Recruitment/Master/category_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_category();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_category();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_category($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_category($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_category';
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
            $query = $this->db->like('category', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_category')->$param();

        return $query;
    }
	
}
