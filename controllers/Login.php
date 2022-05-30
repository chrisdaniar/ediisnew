<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Admin_master_model', 'admin_master_model');

        $this->load->library('user_agent');

        ini_set('display_errors', 0);
    }

    public function index()
    {

        //$url = $this->session->userdata('previous_url');

        $data['phone'] = $this->phone_detector();

        if ($this->session->userdata('logged_in') == true) {

            $data['acreditation'] = $this->admin_master_model->check_acreditation();

                if ($this->session->userdata('id_level') == 1) {
                    if ($data['acreditation'] == 1) {
                        redirect('admin/dashboard/v2'); 
                    } else {
                        redirect('recruitment/dashboard'); 
                    } //admin
                } elseif ( $this->session->userdata('id_level') == 2 or $this->session->userdata('id_level') == 3 or $this->session->userdata('id_level') == 4) {
                    redirect('marketing/dashboard');  //marketing
                } elseif ($this->session->userdata('id_level') == 5 or $this->session->userdata('id_level') == 6 or $this->session->userdata('id_level') == 7) {
                    redirect('recruitment/dashboard'); //recruitment
                } elseif ($this->session->userdata('id_level') == 8 or $this->session->userdata('id_level') == 9 or $this->session->userdata('id_level') == 10) {
                    redirect('marketing/dashboard'); //finance
                } elseif ($this->session->userdata('id_level') == 11 or $this->session->userdata('id_level') == 12 or $this->session->userdata('id_level') == 13) {
                    redirect('marketing/dashboard'); //academic
                } elseif ($this->session->userdata('id_level') == 14 or $this->session->userdata('id_level') == 15 or $this->session->userdata('id_level') == 16) {
                    redirect('marketing/dashboard');
                } elseif ($this->session->userdata('id_level') == 17) {
                    redirect('student/activity/personal_detail'); //student
                } elseif ($this->session->userdata('id_level') == 18 or $this->session->userdata('id_level') == 19 or $this->session->userdata('id_level') == 20) {
                    redirect('marketing/dashboard'); //transfer
                } elseif ($this->session->userdata('id_level') == 21) {
                    redirect('marketing/dashboard'); //transfer
                } elseif ($this->session->userdata('id_level') == 22) {
                    redirect('academic/master/class_lecture/schedule_for_today'); 
                }

        } else {
            $header['title'] = "Login";
            $this->load->view('Login/login_view', $data);
        }
    }

    public function login()
    {
        $header['title'] = "Login";
        $site_key = '6LckG_cdAAAAAFyQUR95aiQI0xpKD7wwBTGlAnG7'; // Diisi dengan site_key API Google reCapthca yang sobat miliki
        $secret_key = '6LckG_cdAAAAAB5d2Aj7K8EsVeHKVH3jte97ZOze'; // Diisi dengan secret_key API Google reCapthca yang sobat miliki

        $api_url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response'];
        $response = @file_get_contents($api_url);
        $dataxx = json_decode($response, true);

       /* if ($dataxx['success']) {*/
            
            $adServer = "10.10.0.10";

            $ldap = ldap_connect($adServer);
            $username = $_POST['username'];
            $password = $_POST['password'];

            $cek_status = $this->db->where('employee_username', $username)
                                   ->get('db_hr.tb_employee')
                                   ->row();

            if ($cek_status != null) {
                $account = $username.'@jic.ac.id';
            } else {
                $account = $username.'@student.jic.ac.id';
            }

            $employee = $this->db->join('db_hr.tb_employee','tb_employee.id_employee = tb_user.id_employee')
                                 ->where('username', $username)
                                 ->get('tb_user')
                                 ->row();

            $acreditation = $this->check_acreditation();


            if($employee != ''){

                $check_level = $this->db->where('id_employee', $employee->id_employee)
                                        ->get('tb_level_system')
                                        ->row();

                $id_employee = $employee->id_employee;
                $id_leads = '';

                $sess_data['id_employee'] = $employee->id_employee;
                $sess_data['id_leads'] = '';
                $sess_data['employee_name'] = $employee->employee_name;
                $sess_data['employee_initial'] = $employee->employee_initial;
                $sess_data['id_level'] = $check_level->id_level;
                $sess_data['lecturer'] = $employee->lecturer;
                $sess_data['id_employee_moodle'] = $employee->id_employee_moodle;
                $sess_data['id_user'] = $employee->id_user;
                $sess_data['id_campus'] = $employee->campus_based;
                $sess_data['acreditation'] = $acreditation;
                        
            } else {

                $leads = $this->db->join('tb_leads','tb_leads.id_leads = tb_user.id_leads')
                                  ->where('tb_user.username', $username)
                                  ->get('tb_user')
                                  ->row();

                $id_employee = '';
                $id_leads = $leads->id_leads;

                $check_level = $this->db->where('username', $username)->get('tb_leads')->row();
                $sess_data['id_employee'] = '';
                $sess_data['id_leads'] = $leads->id_leads;
                $sess_data['leads_name'] = $leads->name.' '.$leads->family_name;
                $sess_data['id_level'] = 17;
                $sess_data['id_user_moodle'] = $leads->id_user_moodle;
                $sess_data['id_user'] = $leads->id_user;
                $sess_data['id_campus'] = '';
                $sess_data['acreditation'] = $acreditation;
            }

            $ldaprdn = $account;

            ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

            $bind = @ldap_bind($ldap, $ldaprdn, $password);
            if ($bind) {

                $filter = "(sAMAccountName=$username)";
                $result = ldap_search($ldap, "dc=jic,dc=ac,dc=id", $filter);
                $info = ldap_get_entries($ldap, $result);
                foreach ($info as $sesd) {
                    $sess_data2['username'] = $sesd['cn'][0];
                    $userDn = $sesd['memberof'][0];
                    $ss = explode(",", $userDn);
                    $sess_data2['group'] = substr($ss[0], 3);
                }

                foreach ($info as $sess) {
                    $kk = $sess['cn'][0];
                    $userDn = $sess['memberof'][0];
                    $ss = explode(",", $userDn);

                    $sess_data['logged_in'] = true;
                    $sess_data['username'] = $sess['cn'][0];
                    $sess_data['group'] = substr($ss[0], 3);
                    $sess_data['email'] = $ldaprdn;

                }
                
                @ldap_close($ldap);

                if ($check_level != null) { 


                    if ($check_level->locked == '1') {

                         $log_menu = 'Login';
                         $log_action = 'Failed Login';
                         $log_notes = $username. ' has been blocked by system';
                         $log_id_items = '';
                         $log_id_items_name = '';
                         $log_link = '';

                         $this->admin_master_model->insert_log($log_menu, $log_action, $log_notes, $log_id_items, $log_id_items_name, $log_link);
                    

                         $this->session->set_flashdata('message', '<div class="alert alert-danger"><p>You do not have access! </p></div>');
                        redirect(base_url('login'));
                    } else {

                        $this->session->set_userdata($sess_data);

                         $log_menu = 'Login';
                         $log_action = 'Successful Login';
                         $log_notes = $username. ' login successfully';
                         $log_id_items = '';
                         $log_id_items_name = '';
                         $log_link = '';

                        $this->admin_master_model->insert_log($log_menu, $log_action, $log_notes, $log_id_items, $log_id_items_name, $log_link);

                        redirect('login');
                    }

                } else {

                    $this->session->set_flashdata('message', '<div class="alert alert-danger"><p>You are not registered in system! </p></div>');
                    redirect(base_url('login'));
                }
            } else {

                $account2 = $username.'@jic.ac.id';

                $bind2 = @ldap_bind($ldap, $account2, $password);

                if ($bind2) {
                    
                $filter = "(sAMAccountName=$username)";
                $result = ldap_search($ldap, "dc=jic,dc=ac,dc=id", $filter);
                $info = ldap_get_entries($ldap, $result);
                foreach ($info as $sesd) {
                    $sess_data2['username'] = $sesd['cn'][0];
                    $userDn = $sesd['memberof'][0];
                    $ss = explode(",", $userDn);
                    $sess_data2['group'] = substr($ss[0], 3);
                }

                foreach ($info as $sess) {
                    $kk = $sess['cn'][0];
                    $userDn = $sess['memberof'][0];
                    $ss = explode(",", $userDn);

                    $sess_data['logged_in'] = true;
                    $sess_data['username'] = $sess['cn'][0];
                    $sess_data['group'] = substr($ss[0], 3);
                    $sess_data['email'] = $account2;
                }
                
                @ldap_close($ldap);

                if ($check_level != null) {

                     if ($check_level->locked == '1') {

                         $log_menu = 'Login';
                         $log_action = 'Failed Login';
                         $log_notes = $username. ' has been blocked by system';
                         $log_id_items = '';
                         $log_id_items_name = '';
                         $log_link = '';

                         $this->admin_master_model->insert_log($log_menu, $log_action, $log_notes, $log_id_items, $log_id_items_name, $log_link);

                         $this->session->set_flashdata('message', '<div class="alert alert-danger"><p>You do not have access! </p></div>');

                        redirect(base_url('login'));

                    } else {

                        $this->session->set_userdata($sess_data);

                         $log_menu = 'Login';
                         $log_action = 'Successful Login';
                         $log_notes = $username. ' login successfully';
                         $log_id_items = '';
                         $log_id_items_name = '';
                         $log_link = '';

                        $this->admin_master_model->insert_log($log_menu, $log_action, $log_notes, $log_id_items, $log_id_items_name, $log_link);

                        redirect('login');
                    }

                } else {

                    $log_menu = 'Login';
                    $log_action = 'Failed Login';
                    $log_notes = $username. ' is not registered in EdIIS';
                    $log_id_items = '';
                    $log_id_items_name = '';
                    $log_link = '';

                    $this->admin_master_model->insert_log($log_menu, $log_action, $log_notes, $log_id_items, $log_id_items_name, $log_link);

                    $this->session->set_flashdata('message', '<div class="alert alert-danger"><p>You are not registered in system! </p></div>');
                    redirect(base_url('login'));
                }

                } else {

                    $log_menu = 'Login';
                    $log_action = 'Failed Login';
                    $log_notes = $username. ' has entered wrong username ('.$username.') / password ('.$password.')';
                    $log_id_items = '';
                    $log_id_items_name = '';
                    $log_link = '';

                    $this->admin_master_model->insert_log($log_menu, $log_action, $log_notes, $log_id_items, $log_id_items_name, $log_link);

                    $this->session->set_flashdata('message', '<div class="alert alert-danger">Wrong username or password!</div>');
                    redirect(base_url('login'));
                }
            }
        } /*else {

            $log_menu = 'Login';
            $log_action = 'Failed Captcha';
            $log_notes = $username. ' has entered failed captcha';
            $log_id_items = '';
            $log_id_items_name = '';
            $log_link = '';

            $this->admin_master_model->insert_log($log_menu, $log_action, $log_notes, $log_id_items, $log_id_items_name, $log_link);

            $this->session->set_flashdata('message', '<div class="alert alert-danger"><p> Failed Captcha!</p></div>');
            redirect('login');
        }
    }*/

    public function check_acreditation(){
        return $this->db->where('id_setting', '1')
                        ->get('tb_setting')
                        ->row()->acreditation;
    }

    public function get_user_data($username){

            $employee = $this->db->where('employee_username', $username)
                        ->get('db_hr.tb_employee')->row();

            if($employee != ''){

                $check_level = $this->db->where('id_employee', $employee->id_employee)
                                        ->get('tb_level_system')
                                        ->row();

                $sess_data['id_employee'] = $employee->id_employee;
                $sess_data['employee_name'] = $employee->employee_name;
                $sess_data['employee_initial'] = $employee->employee_initial;
                $sess_data['id_level'] = $check_level->id_level;
                $sess_data['lecturer'] = $employee->lecturer;
                $sess_data['id_employee_moodle'] = $employee->id_employee_moodle;
                        
            } else {

                $leads = $this->db->where('username', $username)->get('tb_leads')->row();

                $check_level = $this->db->where('username', $username)->get('tb_leads')->row();
                $sess_data['id_leads'] = $leads->id_leads;
                $sess_data['leads_name'] = $leads->name.' '.$leads->family_name;
                $sess_data['id_level'] = 17;
                $sess_data['id_user_moodle'] = $leads->id_user_moodle;
            }

            return $sess_data;
    }

    public function change_level($id_level, $id_employee){

        $sess_data['id_level'] = $id_level;

        $this->session->set_userdata($sess_data);

        redirect('login');

    }



    public function logout()
    {
        $log_menu = 'Logout';
        $log_action = 'Logout';
        $log_notes = '';
        $log_id_items = '';
        $log_id_items_name = '';
        $log_link = '';

        $this->admin_master_model->insert_log($log_menu, $log_action, $log_notes, $log_id_items, $log_id_items_name, $log_link);

        $this->session->sess_destroy();
        redirect('login');
    }

    public function login_v2()
    {
        $this->load->view('Login/login_v2_view');
    }

    public function cek_login_v2()
    {

        $username = $this->input->post('username');

        $acreditation = $this->check_acreditation();

        $employee = $this->db->join('db_hr.tb_employee','tb_employee.id_employee = tb_user.id_employee')
            ->like('employee_username', $username)
            ->get('tb_user')
            ->row();

        if ($employee != '') {
              $cek_level = $this->db->where('id_employee', $employee->id_employee)
            ->get('tb_level_system')->row();

              $level = $cek_level->id_level;

              $sess_data['id_employee'] = $employee->id_employee;
              $sess_data['employee_name'] = $employee->employee_name;
              $sess_data['employee_initial'] = $employee->employee_initial;
              $sess_data['id_level'] = $level;
              $sess_data['lecturer'] = $employee->lecturer;
              $sess_data['id_employee_moodle'] = $employee->id_employee_moodle;
              $sess_data['id_user'] = $employee->id_user;
              $sess_data['id_campus'] = $employee->campus_based;
              $sess_data['acreditation'] = $acreditation;

        } else {
            $leads = $this->db->join('tb_leads','tb_leads.id_leads = tb_user.id_leads')
                          ->where('tb_user.username', $username)
                          ->get('tb_user')
                          ->row();
            $level = 17;
            
            $sess_data['id_leads'] = $leads->id_leads;
            $sess_data['leads_name'] = $leads->name.' '.$leads->family_name;
            $sess_data['id_level'] = $level;
            $sess_data['id_user_moodle'] = $leads->id_user_moodle;
            $sess_data['id_user'] = $leads->id_user;
            $sess_data['id_campus'] = '';
            $sess_data['acreditation'] = $acreditation;

        }

        if ($level != '' && $level != null) {

            $sess_data['logged_in'] = true;

            $this->session->set_userdata($sess_data);

            redirect('login');
        } else {
            redirect('login/login_v2');
        }
    }

    public function phone_detector(){

        $abc = preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo 
        |fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i" 
        , $_SERVER["HTTP_USER_AGENT"]);

        if($abc){ 
            return 1; 
        } else { 
            return 0; 
        }
    }

}
