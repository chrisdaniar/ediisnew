<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->load->model('Admin/Admin_master_model','admin_model');
        $this->load->model('Library/Master_model','master_model');
        $this->link_terakhir = $this->config->item('link_terakhir');
    }
    public function catalogue($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Catalogue Group';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->master_model->pagination_catalogue($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->master_model->pagination_catalogue($this->input->get(), 'num_rows');
		     $this->load->view('Library/Master/catalogue_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_program();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_program();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_program($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}
}