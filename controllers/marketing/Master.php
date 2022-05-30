<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Marketing/Marketing_master_model','master_model');
		$this->load->model('Admin/Admin_master_model','admin_model');
		$this->load->model('Recruitment/Leads_model','leads_model');
		$this->link_terakhir = $this->config->item('link_terakhir');

		//$this->session->set_userdata('previous_url', current_url());
		if ($this->session->userdata('logged_in') == FALSE) {
			redirect('login');
		}

		ini_set('display_errors', 0);
	}
	 
	
	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function agent($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Agent';
		     $data['company'] = $this->master_model->get_company();
		     $data['agent_registered'] = $this->master_model->get_agent_registered();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_agent($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_agent($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Agent/agent_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'save_agent') {
			$aksi = $this->master_model->save_agent();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_agent($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_agent($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_agent';
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
            $query = $this->db->like('agent_name', $get['search'])
            				  ->or_like('company_name', $get['search']);
        }

        $query = $this->db->select('*, sum(tb_leads.id_leads) as total')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->join('tb_company','tb_company.id_company=tb_agent.id_company')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_agent.agent_updated_by','left')
        				  ->join('tb_leads','tb_leads.id_detail_ms = tb_agent.id_agent','left')
        				  ->group_by('tb_agent.id_agent')
                          ->get('tb_agent')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function initial_activity($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Initial Acivity';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_initial_activity($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_initial_activity($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Initial_activity/initial_activity_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} if ($param1 == 'leads') {
			 $id_initial_activity = $param2;
			 $data['title'] = 'Initial Acivity';
			 $data['status'] = $this->leads_model->get_status();
			 $data['initial_activity'] = $this->master_model->get_initial_activity_by_id($id_initial_activity);
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_initial_activity_leads($this->input->get(), 'result', $id_initial_activity);
		     $data['pagination_total_page'] = $this->pagination_initial_activity_leads($this->input->get(), 'num_rows', $id_initial_activity);
		     $this->load->view('Marketing/Initial_activity/initial_activity_leads_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'save_initial_activity') {
			$aksi = $this->master_model->save_initial_activity();
            echo $aksi;
		} elseif ($param1 == 'delete_initial_activity') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_initial_activity($id);
			}
			redirect($url);
		} else {

		}
	}

	 public function pagination_initial_activity($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_initial_activity';
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
            $query = $this->db->like('initial_activity', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_initial_activity.initial_activity_updated_by','left')
                          ->get('tb_initial_activity')->$param();

        return $query;
    }

    public function pagination_initial_activity_leads($get = [], $param = 'result', $id_initial_activity = '')
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
        	if ($get['id_status_filter'] == 'Closed') {
        		$query = $this->db->where('tb_leads.id_status2 !=','');
        	} else {
        		$query = $this->db->where('tb_leads.id_status', $get['id_status_filter']);
        	}
        }

        $query = $this->db->select('*, tb_leads.id_regency, tb_regency.id_province')
        				  ->where('tb_leads.id_initial_activity', $id_initial_activity)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->join('tb_regency','tb_regency.id_regency=tb_leads.id_regency','left')
        				  ->join('tb_province','tb_province.id_province=tb_regency.id_province','left')
        				  ->join('tb_marketing_source','tb_marketing_source.id_marketing_source=tb_leads.id_marketing_source','left')
        				  ->join('tb_status','tb_status.id_status=tb_leads.id_status','left')
                          ->get('tb_leads')->$param();

        return $query;
    }	

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function event($param1 = '', $param2 = '', $param3 = ''){
		if ($param1 == 'type') {
			 $data['title'] = 'Event Type';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_event_type($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_event_type($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Event/event_type_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif($param1 == 'detail'){
			 $id_event_type = $param2;
			 $data['event_type'] = $this->master_model->get_event_type_by_id($id_event_type);
			 $data['title'] = 'Event';
			 $data['school'] = $this->leads_model->get_school();
		     $data['campus'] = $this->master_model->get_campus();
		     $data['province'] = $this->master_model->get_province();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['admission_marketing'] = $this->master_model->get_admission_marketing();
		     $data['pagination_data'] = $this->pagination_event($this->input->get(), 'result', $id_event_type);
		     $data['pagination_total_page'] = $this->pagination_event($this->input->get(), 'num_rows', $id_event_type);
		     $this->load->view('Marketing/Event/event_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		}  elseif($param1 == 'leads'){
			 $id_event = $param2;
			 $data['title'] = 'Event';
			 $data['event'] = $this->master_model->get_event_by_id($id_event);
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['recruitment'] = $this->leads_model->get_recruitment();
		     $data['status'] = $this->leads_model->get_status();
		     $data['marketing_source'] = $this->leads_model->get_marketing_source();
		     $data['pagination_data'] = $this->pagination_event_leads($this->input->get(), 'result', $id_event);
		     $data['pagination_total_page'] = $this->pagination_event_leads($this->input->get(), 'num_rows', $id_event);
		     $this->load->view('Marketing/Event/event_leads_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif($param1 == 'leads_event' && $param2 != 'detail'){
			 $id_event = $param2;
			 $data['title'] = 'Event';
			 $data['recruitment'] = $this->leads_model->get_recruitment();
			 $data['event'] = $this->master_model->get_event_by_id($id_event);
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['leads_major'] = $this->master_model->get_leads_major_by_id_event($id_event);
		     $data['country'] = $this->master_model->get_all_country_by_id_event($id_event);
             $data['class_'] = $this->master_model->get_class_by_id_event($id_event);
             $data['event_handled_by'] = $this->master_model->get_event_handled_by_by_id_event($id_event);
		     $data['pagination_data'] = $this->pagination_event_leads_trash($this->input->get(), 'result', $id_event);
		     $data['pagination_total_page'] = $this->pagination_event_leads_trash($this->input->get(), 'num_rows', $id_event);
		     $this->load->view('Marketing/Event/event_leads_trash_view', $data);
		} elseif($param1 == 'leads_event' && $param2 == 'detail'){
			 $id_leads_event = $param3;
             $data['title'] = 'Leads Detail';
             $data['left_bar'] = 'Recruitment/left_navbar_template';
             $data['leads_event'] = $this->master_model->get_leads_event_by_id($id_leads_event);
             $this->load->view('Marketing/Event/Leads/leads_detail_view', $data);
		} elseif($param1 == 'winner'){
			 $id_event = $param2;
			 $data['title'] = 'Event';
		     $data['winner'] = $this->master_model->get_winner_by_id_event($id_event);
		     $data['left_bar'] = 'Recruitment/left_navbar_template';
		     $this->load->view('Marketing/Event/event_winner_view', $data);
		} elseif ($param1 == 'winners') {
             $id_event = $param2;
             $data['title'] = 'Event';
             $data['winner'] = $this->master_model->get_winner_by_id_event($id_event);
             $data['left_bar'] = 'Recruitment/left_navbar_template';
             $this->load->view('Marketing/Event/event_winner_view', $data);
        }  elseif ($param1 == 'winner_history') {
             $id_event = $param2;
             $data['title'] = 'Winner History';
             $data['event'] = $this->master_model->get_event_by_id($id_event);
             $data['pagination_data'] = $this->pagination_winner($this->input->get(), 'result', $id_event);
             $data['pagination_total_page'] = $this->pagination_winner($this->input->get(), 'num_rows', $id_event);
             $data['left_bar'] = 'Recruitment/left_navbar_template';
             $this->load->view('Marketing/Event/event_winner_history_view', $data);
        }elseif ($param1 == 'save_event_type') {
			$aksi = $this->master_model->save_event_type();
            echo $aksi;
		} elseif ($param1 == 'delete_event_type') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_event_type($id);
			}
			redirect($url);
		} elseif ($param1 == 'save_event') {
			$aksi = $this->master_model->save_event();
            echo $aksi;
		} elseif ($param1 == 'delete_event') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_event($id);
			}
			redirect($url);
		} elseif ($param1 == 'setting_event') {
			$aksi = $this->master_model->setting_event();
            echo $aksi;
		} elseif ($param1 == 'barcode') {
			 $id_event = $this->input->post('id_event');
			 $data['title'] = 'Event';
		     $data['event'] = $this->master_model->get_event_by_id($id_event);
		     $display =  $this->load->view('Marketing/Event/barcode_view', $data);
		     echo $display;
		} elseif ($param1 == 'spin_winner') {
			$aksi = $this->master_model->spin_winner();
            echo $aksi;
		} elseif ($param1 == 'save_event_handled_by') {
			$aksi = $this->master_model->save_event_handled_by();
            echo $aksi;
		} elseif ($param1 == 'delete_event_handled_by') {
			$url = $this->input->post('url');
			foreach ($_POST['id_event_handled_by'] as $id) {
				$this->master_model->delete_event_handled_by($id);
			}
			redirect($url);
		}else {

		}
	}

	 public function pagination_event_type($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_event_type';
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
            $query = $this->db->like('event_type_name', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_event_type.event_type_updated_by','left')
                          ->get('tb_event_type')->$param();

        return $query;
    }

    public function pagination_event($get = [], $param = 'result', $id_event_type = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_event';
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
            $query = $this->db->like('event_description', $get['search']);
        }

        $query = $this->db->select('*, tb_event.id_regency, tb_regency.id_province')
        				  ->where('tb_event.id_event_type', $id_event_type)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->join('tb_event_type','tb_event_type.id_event_type=tb_event.id_event_type','left')
        				  ->join('tb_campus','tb_campus.id_campus=tb_event.id_campus','left')
        				  ->join('tb_regency','tb_regency.id_regency=tb_event.id_regency','left')
        				  ->join('tb_province','tb_province.id_province=tb_regency.id_province','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_event.person_in_charge','left')
        				  ->join('tb_school','tb_school.id_school=tb_event.id_school','left')
                          ->get('tb_event')->$param();

        return $query;
    }

    public function pagination_event_leads($get = [], $param = 'result', $id_event = '')
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

        $query = $this->db->select('*, tb_leads.id_regency, tb_regency.id_province')
        				  ->where('tb_leads.id_event', $id_event)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->join('tb_regency','tb_regency.id_regency=tb_leads.id_regency','left')
        				  ->join('tb_province','tb_province.id_province=tb_regency.id_province','left')
        				  ->join('tb_marketing_source','tb_marketing_source.id_marketing_source=tb_leads.id_marketing_source','left')
        				  ->join('tb_status','tb_status.id_status=tb_leads.id_status','left')
                          ->get('tb_leads')->$param();

        return $query;
    }

    public function pagination_event_leads_trash($get = [], $param = 'result', $id_event = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_leads_event.id_leads_event';
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

        if (isset($get['major_filter'])) {
            $query = $this->db->where('tb_leads_event.major', $get['major_filter']);
        }

        if (isset($get['id_country_filter'])) {
            $query = $this->db->where('tb_leads_event.destination_country', $get['id_country_filter']);
        }

         if (isset($get['id_class_filter'])) {
            $query = $this->db->where('tb_leads_event.id_class', $get['id_class_filter']);
        }

        if(isset($get['search'])){
            $query = $this->db->like('CONCAT(name," ",last_name)', $get['search']);
        }

        $query = $this->db->where('tb_leads_event.id_event', $id_event)
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->join('tb_event','tb_event.id_event=tb_leads_event.id_event','left')
                          ->join('tb_campus','tb_campus.id_campus=tb_event.id_campus','left')
        				  ->join('tb_country','tb_country.id_country=tb_leads_event.destination_country','left')
        				  ->join('tb_class','tb_class.id_class=tb_leads_event.id_class','left')
                          ->get('tb_leads_event')->$param();

        return $query;
    }

    public function pagination_winner($get = [], $param = 'result', $id_event = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_winner.id_winner';
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

        $query = $this->db->select('*')
                          ->where('tb_winner.id_event', $id_event)
                          ->order_by($get['sortby'], $get['sortby2'])
                          ->join('tb_leads_event','tb_leads_event.id_leads_event=tb_winner.id_leads_event','left')
                          ->join('tb_class','tb_class.id_class=tb_leads_event.id_class','left')
                          ->get('tb_winner')->$param();

        return $query;
    }


	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function marketing_source($param1 = '', $param2 = '', $param3 = ''){
		if ($param1 == 'index') {
			 $data['title'] = 'Marketing Source';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_marketing_source($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_marketing_source($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Marketing_source/marketing_source_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif($param1 == 'detail'){
			 $id_marketing_source = $param2;
			 $data['marketing_source'] = $this->master_model->get_marketing_source_by_id($id_marketing_source);
			 $data['title'] = 'Marketing Source Detail';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_marketing_source_detail($this->input->get(), 'result', $id_marketing_source);
		     $data['pagination_total_page'] = $this->pagination_marketing_source_detail($this->input->get(), 'num_rows', $id_marketing_source);
		     $this->load->view('Marketing/Marketing_source/marketing_source_detail_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif($param1 == 'leads'){
			 $id_marketing_source = $param2;
			 $id_marketing_source_detail = $param3;
			 $data['marketing_source'] = $this->master_model->get_marketing_source_by_id($id_marketing_source);
			 $data['title'] = 'Marketing Source';
			 $data['status'] = $this->leads_model->get_status();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_marketing_source_leads($this->input->get(), 'result', $id_marketing_source, $id_marketing_source_detail);
		     $data['pagination_total_page'] = $this->pagination_marketing_source_leads($this->input->get(), 'num_rows', $id_marketing_source, $id_marketing_source_detail);
		     $this->load->view('Marketing/Marketing_source/marketing_source_leads_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'save_marketing_source') {
			$aksi = $this->master_model->save_marketing_source();
            echo $aksi;
		} elseif ($param1 == 'delete_marketing_source') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_marketing_source($id);
			}
			redirect($url);
		} elseif ($param1 == 'save_marketing_source_detail') {
			$aksi = $this->master_model->save_marketing_source_detail();
            echo $aksi;
		} elseif ($param1 == 'delete_marketing_source_detail') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_marketing_source_detail($id);
			}
			redirect($url);
		} else {

		}
	}

	 public function pagination_marketing_source($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_marketing_source';
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
            $query = $this->db->like('marketing_source', $get['search']);
        }

        $query = $this->db->where('id_marketing_source !=','1')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_marketing_source.marketing_source_updated_by','left')
                          ->get('tb_marketing_source')->$param();

        return $query;
    }

    public function pagination_marketing_source_detail($get = [], $param = 'result', $id_marketing_source = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_marketing_source_detail';
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
            $query = $this->db->like('marketing_source_detail_description', $get['search']);
        }

        $query = $this->db->where('tb_marketing_source.id_marketing_source', $id_marketing_source)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->join('tb_marketing_source','tb_marketing_source.id_marketing_source=tb_marketing_source_detail.id_marketing_source','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_marketing_source_detail.marketing_source_detail_updated_by','left')
                          ->get('tb_marketing_source_detail')->$param();

        return $query;
    }

    public function pagination_marketing_source_leads($get = [], $param = 'result', $id_marketing_source = '', $id_marketing_source_detail = '')
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

        if (isset($get['id_status_filter'])) {
            $query = $this->db->where('tb_leads.id_status', $get['id_status_filter']);
        }

        $query = $this->db->select('*, tb_leads.id_regency, tb_regency.id_province')
        				  ->where('tb_leads.id_marketing_source', $id_marketing_source);

        if ($id_marketing_source_detail != '') {
        	$query = $this->db->join('tb_marketing_source_detail','tb_marketing_source_detail.id_marketing_source_detail = tb_leads.id_detail_ms','left')
        					  ->where('tb_leads.id_detail_ms', $id_marketing_source_detail);
        }
        				  
        	$query = $this->db->order_by($get['sortby'], $get['sortby2'])
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

	public function get_subject_by_course($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_course = $param;
		$result = $this->master_model->get_subject_by_course($id_course);
		$option = "";
		$option .= '<option value=""> -- Select Subject --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_subject."' >".$data->subject_name."</option>";
		}
		echo $option;

	}

	public function get_subject_by_subject() {
		// $layanan =$this->input->post('layanan');
		$id_subject = $this->input->post('id_subject');
		
		$result = $this->db->where('id_subject !=', $id_subject)->get('tb_subject')->result();
		$row = $this->db->where('id_subject', $id_subject)->get('tb_subject')->row();

		$option = "";
		$option .= '<option value="'.$row->id_subject.'" selected="selected"> '.$row->subject_name.' </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_subject."' >".$data->subject_name."</option>";
		}
		echo $option;

	}

	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function online_ads($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Online Adsense - Master';
			 $data['country'] = $this->master_model->get_country();
		     $data['online_ads_type'] = $this->master_model->get_online_ads_type();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_online_ads($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_online_ads($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Adsense/online_ads_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_online_ads();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_online_ads();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_online_ads($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_online_ads($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_online_ads';
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
            $query = $this->db->like('online_ads_name', $get['search'])
            				  ->or_like('online_ads_cost', $get['search'])
            				  ->or_like('post_time', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
        				  ->join('tb_online_ads_type','tb_online_ads_type.id_online_ads_type=tb_online_ads.id_online_ads_type','left')
        				  ->join('tb_regency','tb_regency.id_regency=tb_online_ads.id_regency','left')
        				  ->join('tb_province','tb_province.id_province=tb_regency.id_province','left')
        				  ->join('tb_country','tb_country.id_country=tb_online_ads.id_country','left')
        				   ->join('db_hr.tb_employee','tb_employee.id_employee=tb_online_ads.online_ads_updated_by','left')
        				  //->group_by('tb_school.id_school')
                          ->get('tb_online_ads')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function offline_ads($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Offline Media - Master';
			 $data['country'] = $this->master_model->get_country();
		     $data['offline_platform'] = $this->master_model->get_offline_platform();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_offline_ads($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_offline_ads($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Adsense/offline_ads_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_offline_ads();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_offline_ads();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_offline_ads($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_offline_ads($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_offline_ads';
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
            $query = $this->db->like('offline_ads_name', $get['search'])
            				  ->or_like('platform_name', $get['search'])
            				  ->or_like('post_time', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
        				  ->join('tb_offline_platform','tb_offline_platform.id_offline_platform=tb_offline_ads.id_offline_platform','left')
        				  ->join('tb_offline_ads_type','tb_offline_ads_type.id_offline_ads_type=tb_offline_platform.id_offline_ads_type','left')
        				   ->join('db_hr.tb_employee','tb_employee.id_employee=tb_offline_ads.offline_ads_updated_by','left')
        				
        				  //->group_by('tb_school.id_school')
                          ->get('tb_offline_ads')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function outdoor_ads($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Online Adsense - Master';
			 $data['country'] = $this->master_model->get_country();
		     $data['outdoor_type'] = $this->master_model->get_outdoor_type();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_outdoor($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_outdoor($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Adsense/outdoor_ads_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_outdoor();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_outdoor();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_outdoor($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_outdoor($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_outdoor';
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
            $query = $this->db->like('outdoor_type', $get['search'])
            				  ->or_like('district_name', $get['search'])
            				  ->or_like('regency_name', $get['search'])
            				  ->or_like('village_name', $get['search'])
            				  ->or_like('post_time', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
        				  ->join('tb_outdoor_type','tb_outdoor_type.id_outdoor_type=tb_outdoor.id_outdoor_type','left')
        				  ->join('tb_village','tb_village.id_village=tb_outdoor.id_village','left')
        				  ->join('tb_district','tb_district.id_district=tb_village.id_district','left')
        				  ->join('tb_regency','tb_regency.id_regency=tb_district.id_regency','left')
        				  ->join('tb_province','tb_province.id_province=tb_regency.id_province','left')
        				  //->join('db_hr.tb_employee','tb_employee.id_employee=tb_outdoor_ads.offline_ads_updated_by','left')
        				  //->group_by('tb_school.id_school')
                          ->get('tb_outdoor')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function newsletter($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Newsletter - Master';
			 $data['country'] = $this->master_model->get_country();
		     $data['company'] = $this->master_model->get_company();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_newsletter($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_newsletter($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Adsense/newsletter_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_newsletter();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_newsletter();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_newsletter($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_newsletter($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_newsletter';
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
            $query = $this->db->like('company_name', $get['search'])
            				  ->or_like('newsletter_subject', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
        			      ->join('tb_regency','tb_regency.id_regency=tb_newsletter.id_regency','left')
        				  ->join('tb_province','tb_province.id_province=tb_regency.id_province','left')
        				  ->join('tb_country','tb_country.id_country=tb_newsletter.id_country','left')
        				  ->join('tb_company','tb_company.id_company=tb_newsletter.id_company','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_newsletter.newsletter_updated_by','left')
        				  //->group_by('tb_school.id_school')
                          ->get('tb_newsletter')->$param();

        return $query;
    }

//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function sms_blast($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'SMS Blast - Master';
			 $data['country'] = $this->master_model->get_country();
		     $data['company'] = $this->master_model->get_company();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_sms_blast($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_sms_blast($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Adsense/sms_blast_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_sms_blast();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_sms_blast();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_sms_blast($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_sms_blast($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_sms_blast';
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
            $query = $this->db->like('company_name', $get['search'])
            				  ->or_like('sms_blast_subject', $get['search']);
        }

        $query = $this->db->order_by($get['sortby'], $get['sortby2'])
        				  ->join('tb_company','tb_company.id_company=tb_sms_blast.id_company','left')
        				  ->join('tb_regency','tb_regency.id_regency=tb_sms_blast.id_regency','left')
        				  ->join('tb_province','tb_province.id_province=tb_regency.id_province','left')
        				  ->join('tb_country','tb_country.id_country=tb_sms_blast.id_country','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_sms_blast.sms_blast_updated_by','left')
        				  //->group_by('tb_school.id_school')
                          ->get('tb_sms_blast')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function company($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Company - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_company($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_company($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Company/company_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_company();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_company();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_company($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_company($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_company';
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
            $query = $this->db->like('company_name', $get['search']);
        }

        $query = $this->db ->join('db_hr.tb_employee','tb_employee.id_employee=tb_company.company_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->get('tb_company')->$param();

        return $query;
    }

   

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function offline_platform($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Offline Platform - Master';
			 $data['offline_ads_type'] = $this->master_model->get_offline_ads_type();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_offline_platform($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_offline_platform($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Adsense/offline_platform_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_offline_platform();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_offline_platform();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_offline_platform($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_offline_platform($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_offline_platform';
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
            $query = $this->db->like('platform_name', $get['search'])
            				  ->like('offline_ads_name', $get['search']);
        }

        $query = $this->db->join('tb_offline_ads_type','tb_offline_ads_type.id_offline_ads_type=tb_offline_platform.id_offline_ads_type','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_offline_platform')->$param();

        return $query;
    }
	
	 //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function online_platform($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Online Platform - Master';

		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_online_platform($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_online_platform($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Adsense/online_platform_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_online_platform();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_online_platform();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_online_platform($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_online_platform($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_online_ads_type';
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
            $query = $this->db->like('online_ads_name', $get['search'])
            				  ->like('online_type', $get['search']);
        }

        $query = $this->db
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_online_ads_type')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function outdoor_platform($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Outdoor Platform - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_outdoor_platform($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_outdoor_platform($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Adsense/outdoor_platform_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_outdoor_platform();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_outdoor_platform();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_outdoor_platform($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	public function pagination_outdoor_platform($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_outdoor_type';
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
            $query = $this->db->like('outdoor_type', $get['search']);
        }

        $query = $this->db
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_outdoor_type')->$param();

        return $query;
    }
	
	public function village($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Village - Master';
			 $data['province'] = $this->master_model->get_province();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_village($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_village($this->input->get(), 'num_rows');
		     $this->load->view('Marketing/Location/village_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_village();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_village();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_village($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	public function pagination_village($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_village';
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
            $query = $this->db->like('id_village', $get['search']);
        }

        $query = $this->db->join('tb_district','tb_district.id_district=tb_village.id_district','left')
        				  ->join('tb_regency','tb_regency.id_regency=tb_district.id_regency','left')
        				  ->join('tb_province','tb_province.id_province=tb_regency.id_province','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_village')->$param();

        return $query;
    }
   
     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	
	
}
