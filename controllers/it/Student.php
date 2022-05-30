<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Student extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Admin_master_model', 'admin_master_model');
        $this->load->model('IT/It_student_model', 'it_student_model');
        $this->load->model('Academic/Academic_master_model', 'academic_master_model');
        $this->load->model('Recruitment/Recruitment_master_model', 'recruitment_master_model');
        $this->link_terakhir = $this->config->item('link_terakhir');

        //ini_set('display_errors', 0);

        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('login');

        }
    }


    public function email_ldap_user($email = '', $cc = '', $title = '', $subject = '',  $message = '',  $attach = '', $id_leads, $id_intake)
    {

         $config = [        
            'protocol' => 'smtp',
            'smtp_host' => 'smtp.office365.com',
            'smtp_user' => 'system@it.jic.ac.id',
            'smtp_pass' => 'Xov48606',
            'smtp_crypto' => 'tls',
            'charset' => 'utf-8',    
            'crlf' => "\r\n",
            'newline' => "\r\n", //must have for office365!
            'smtp_port' => 587,
            'mailtype' => 'html',
            'wordwrap'  => TRUE  
        ];
        $this->load->library('email', $config);        

        $this->email->from('system@it.jic.ac.id', $title); 

        $o = explode(",", $email);
        $recipientArr = $o;
        $this->email->to($recipientArr);

        $o = explode(",", $cc);
        $ccs = $o;
        $this->email->cc($cc);

        $this->email->subject($subject);
        $this->email->message($message);

        $doc = $this->db->where('id_intake', $id_intake)->get('tb_intake_document')->result();

        foreach ($doc as $key) {
            $this->email->attach('uploads/'.$key->intake_document_file);
        }

        $sent = $this->email->send();


        if ($sent) 
        {
            echo 'OK';

            $edit['email_account'] = 1;

            $this->db->where('id_leads', $id_leads)
                     ->update('tb_leads', $edit);
            
        } else {
            echo $this->email->print_debugger();
        }
            
    }
    
    public function generate_name()
    {
        $fullname = "amar";
        $split = explode(" ", $fullname);
        $hasil = "";
        if(count($split) >= 2){
            $a1 = substr(current($split),0,1);
            $a2 = substr(end($split),0,3);
            $hasil = strtolower($a1.$a2);
        } else {
            $hasil = strtolower(substr(current($split),0,4));
        }
        $nomor = 1;
        $nomor = str_pad($nomor, 4, '0', STR_PAD_LEFT);
        $username = $hasil.$nomor;
        $cek_username = $this->db->where('username', $username)->order_by('username', 'desc')->get('tb_leads')->row();
        
        if($cek_username != ''){
            $nomor = preg_replace('/\D/', '', $cek_username->username);
            $nomor = str_pad((int)$nomor + 1, 4, '0', STR_PAD_LEFT);
            $username = $hasil.$nomor;
        }   
        echo $username;
        
    }
    public function finance_clearance($param = '')
    {
        if ($param == 'active_student') {
            $data['title'] = 'Student';
            $data['intended_program'] = $this->academic_master_model->get_intended_program();
            $data['intake_year'] = $this->recruitment_master_model->get_intake_year();
            $data['month'] = $this->recruitment_master_model->get_month();
            $data['course'] = $this->academic_master_model->get_course();
            $data['campus'] = $this->academic_master_model->get_campus();
            $data['left_bar'] = $this->admin_master_model->check_navbar();
            $data['pagination_data'] = $this->it_student_model->pagination_active_student($this->input->get(), 'result_array');
            $data['pagination_total_page'] = $this->it_student_model->pagination_active_student($this->input->get(), 'num_rows');
            $this->load->view('IT/Student/finance_clearance_view', $data);
            $this->session->set_userdata('previous_url', $this->link_terakhir);
        } elseif ($param == 'activate_ldap_user') {
            $hasil = $this->it_student_model->activate_ldap_user();
            echo $hasil;
        } elseif ($param == 'activate_moodle_activated') {
            $hasil = $this->it_student_model->activate_moodle_activated();
            echo $hasil;
        } elseif ($param == 'send_email') {

            $id_student = $this->input->post('id_student');

            $student = $this->db->join('tb_leads','tb_leads.id_leads=tb_student.id_leads')
                                ->join('db_hr.tb_employee','tb_employee.id_employee=tb_student.handled_by','left')
                                ->where('id_student', $id_student)
                                ->get('tb_student')->row();

            $intake = $this->db->join('tb_intake_month','tb_intake_month.id_intake_month=tb_intake.id_intake_month')
                               ->join('tb_month','tb_month.id_month=tb_intake_month.id_month')
                               ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_intake.id_academic_year')
                               ->where('tb_intake.id_intended_program', $student->id_intended_program)
                               ->where('tb_month.month', date('F', strtotime($student->intake_leads)))
                               ->where('tb_academic_year.academic_year', date('Y', strtotime($student->intake_leads)))
                               ->get('tb_intake')
                               ->row();

            $family = $this->db->where('id_leads', $student->id_leads)
                                ->get('tb_family')->result();

            foreach ($family as $pp_) {
               $kk[] = $pp_->email;
            }

            $all_family = implode(",", $kk);

            $receiver = $student->email;
            $cc = $all_family.','.$student->employee_office_email;
            $title = $intake->invitation_title;
            $subject = $intake->invitation_subject;
            $attach = '';
            $message = '

                    <div style="border: solid black 1px;width: 700px">

                    <div style="margin: 20px;">

                    <img style="text-align: left" src="https://ediis.jic.ac.id/intranet/assets/jic.png"> <br><br>

                    Dear '.$student->name.' '.$student->family_name.', <br>

                    <div style="text-align:justify">

                    '.$intake->invitation_opening_message.'

                    </div>

                    <div style="border: solid black 1px;width: 50%; padding: 5px">

                    Your JIC account details are as follows: <br>
                    •   <b> Username </b>: '.$student->username.' <br>
                    •   <b> Password </b>: '.$student->account_password.' <br>
                    •   <b> Email </b>: '.$student->username.'@student.jic.ac.id <br>

                    </div>

                    '.$intake->invitation_closing_message.'

                    </div>
                    </div>';

            echo $message;
           
            $this->email_ldap_user($receiver, $cc, $title, $subject, $message, $attach, $student->id_leads, $intake->id_intake);

        } elseif ($param == 'table_family') {
            $id_leads = $this->input->post('id_leads');

            $leads = $this->db->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                              ->join('db_hr.tb_employee','tb_employee.id_employee = tb_student.handled_by','left')
                               ->where('tb_student.id_leads',$id_leads)
                               ->get('tb_student')
                               ->row();

            $result = $this->db->where('id_leads',$id_leads)
                               ->where('email !=', '')
                               ->get('tb_family')
                               ->result();

            $option = "";
            $option .= '<table class="custom-tables-2">
                            <tr>
                                <th> Name </th>
                                <th> Notes </th>
                                <th> Email </th>
                            </tr>';

             $option .= '<tr>
                                <td> '.$leads->name.' '.$leads->family_name.' </td>
                                <td> Student </td>
                                <td> '.$leads->email.' </td>
                        </tr>';

            foreach ($result as $key) {
             $option .= '<tr>
                                <td> '.$key->name.' </td>
                                <td> '.$key->relationship.' </td>
                                <td> '.$key->email.' </td>
                        </tr>';
                    }

             $option .= '<tr>
                                <td> '.$leads->employee_name.' </td>
                                <td> Recruitment </td>
                                <td> '.$leads->employee_office_email.' </td>
                        </tr>';

             $option .= '</table>';

            echo $option;
        }
    }
}