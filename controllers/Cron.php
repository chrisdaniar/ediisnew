<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->link_terakhir = $this->config->item('link_terakhir');

        ini_set('display_errors', 0);
	}

	public function run(){
		$data['cron'] = date('Y-m-d H:i:s');

		$this->db->insert('tb_cron', $data);
	}
}