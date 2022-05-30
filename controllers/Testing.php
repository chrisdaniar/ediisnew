<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testing extends CI_Controller {

  public function __construct()
  {
    parent::__construct();
    $this->load->model('Academic/Academic_master_model','master_model');
    $this->load->model('Marketing/Marketing_master_model','marketing_model');
    $this->load->model('Recruitment/Recruitment_master_model','recruitment_model');
    $this->load->model('Admin/Admin_master_model','admin_model');
    $this->load->model('Recruitment/Leads_model','leads_model');
    $this->load->model('Admin/Moodle_model','moodle_model');
    $this->load->model('IT/It_student_model','it_student_model');
    $this->load->model('Library/Library_model','library_model');
    $this->load->model('Student/Student_model','student_model');
    $this->load->library('excel');
    $this->link_terakhir = $this->config->item('link_terakhir');

    $this->load->library('user_agent');

    if ($this->session->userdata('logged_in') == FALSE) {
      redirect('login');
    }

    //ini_set('display_errors', 0);
  }

  public function add_leads(){

            $event = $this->marketing_master_model->get_event_by_id($this->input->post('id_event'));
            $handled_by = $this->input->post('handled_by');
            $destination = $this->input->post('id_country');
            $id_class = $this->input->post('id_class');
        
            $data['name'] = ucwords(strtolower($this->input->post('name')));
            $data['last_name'] = ucwords(strtolower($this->input->post('last_name')));
            $data['address'] = $this->input->post('address');
            $data['handled_by'] = $this->input->post('handled_by');
            $data['id_regency'] = $this->input->post('id_regency');
            $data['phone'] = $this->input->post('phone');
            $data['email'] = $this->input->post('email');
            $data['instagram'] = $this->input->post('instagram');
            $data['id_class'] = $id_class;
            $data['id_event'] = $this->input->post('id_event');
            $data['destination_country'] = $this->input->post('id_country');
            $data['major'] = $this->input->post('major');
            $data['leads_school_name'] = $this->input->post('leads_school_name');
            $data['dob'] = $this->leads_model->format_tanggal($this->input->post('dob'));
            $data['leads_event_last_updated'] = date('Y-m-d H:i:s');

            if ($event->id_campus == 1) {
                $data['workplace'] = $this->input->post('workplace');
                $data['session'] = $this->input->post('session');
                $data['work'] = $this->input->post('work');
            }

            $hasil = $this->db->insert('tb_leads_event', $data);
            $id_leads_event = $this->db->insert_id();

            $event = $this->marketing_master_model->get_event_by_id($this->input->post('id_event'));  

            if ($handled_by == '') {
              if ($event->id_campus == '3') {    
                 if ($destination == '13' || $destination == '134') {
                    $check = $this->db->select('tb_leads_event.id_leads')
                                      ->where('id_class', $id_class)
                                      ->where('id_leads !=','')
                                      ->where('handled_by','')
                                      ->where('destination_country', $destination)
                                      ->order_by('id_leads_event','desc')
                                      ->get('tb_leads_event')
                                      ->row();
                    if ($check == null) {
                        $pop = $this->check_initial_monash_position_program($event->id_campus);
                        if ($pop == null) {
                          $handled_by = '';
                        } else {
                          $handled_by = $pop->id_employee;
                        }
                    } else {
                      $adm = $this->check_last_handled_by_monash($check->id_leads, $event->id_campus);
                        if ($adm == null) {
                            $pop = $this->check_initial_monash_position_program($event->id_campus);
                            if ($pop == null) {
                                $handled_by = '';
                            } else {
                                $handled_by = $pop->id_employee;
                            }
                        } else {
                            $handled_by = $adm->id_employee;
                        }
                    }
                    if ($handled_by != '') {
                      $this->leads_model->save_leads_event_to_leads($id_leads_event, $handled_by);
                    }
                } else if($destination == '232'){
                    $check = $this->db->select('tb_leads_event.id_leads')
                                      ->where('id_class', $id_class)
                                      ->where('id_leads !=','')
                                      ->where('handled_by','')
                                      ->where('destination_country', $destination)
                                      ->order_by('id_leads_event','desc')
                                      ->get('tb_leads_event')
                                      ->row();
                    if ($check == null) {
                        $pop = $this->check_initial_wmu_position_program($event->id_campus);
                        if ($pop == null) {
                          $handled_by = '';
                        } else {
                          $handled_by = $pop->id_employee;
                        }
                    } else {
                      $adm = $this->check_last_handled_by_wmu($check->id_leads, $event->id_campus);
                        if ($adm == null) {
                            $pop = $this->check_initial_wmu_position_program($event->id_campus);
                            if ($pop == null) {
                                $handled_by = '';
                            } else {
                                $handled_by = $pop->id_employee;
                            }
                        } else {
                            $handled_by = $adm->id_employee;
                        }
                    }
                    if ($handled_by != '') {
                      $this->leads_model->save_leads_event_to_leads($id_leads_event, $handled_by);
                    }
                }
              } elseif($event->id_campus == '4'){
                    $check = $this->db->select('tb_leads_event.id_leads')
                                      ->where('id_class', $id_class)
                                      ->where('id_leads !=','')
                                      ->where('handled_by','')
                                      ->where('destination_country', $destination)
                                      ->order_by('id_leads_event','desc')
                                      ->get('tb_leads_event')
                                      ->row();
                    if ($check == null) {
                        $pop = $this->check_initial_monash_position_program($event->id_campus);
                        if ($pop == null) {
                          $handled_by = '';
                        } else {
                          $handled_by = $pop->id_employee;
                        }
                    } else {
                      $adm = $this->check_last_handled_by_monash($check->id_leads, $event->id_campus);
                        if ($adm == null) {
                            $pop = $this->check_initial_monash_position_program($event->id_campus);
                            if ($pop == null) {
                                $handled_by = '';
                            } else {
                                $handled_by = $pop->id_employee;
                            }
                        } else {
                            $handled_by = $adm->id_employee;
                        }
                    }
                    if ($handled_by != '') {
                      $this->leads_model->save_leads_event_to_leads($id_leads_event, $handled_by);
                    }
              } else {

              }
            } else {
                $this->leads_model->save_leads_event_to_leads($id_leads_event, $handled_by);
            } 
            $result['status'] = $hasil;
            return json_encode($result);
    }

  public function check_initial_monash_position_program($id_campus){
      return  $this->db->select('tb_position_program.id_employee')
                       ->join('db_hr.tb_employee','tb_employee.id_employee = tb_position_program.id_employee')
                       ->where('id_intended_program', 1)
                       ->where('position_program_order >',0)
                       ->where('tb_employee.campus_based', $id_campus)
                       ->or_where('id_intended_program', 4)
                       ->where('position_program_order >',0)
                       ->where('tb_employee.campus_based', $id_campus)
                       ->group_by('tb_position_program.id_employee')
                       ->order_by('tb_position_program.id_position_program','asc')
                       ->get('db_hr.tb_position_program')
                       ->row();
  }

  public function check_last_handled_by_monash($id_leads, $id_campus){
      $leads = $this->db->select('tb_initial_contact.handled_by')
                                      ->where('id_leads', $id_leads)
                                      ->get('tb_initial_contact')
                                      ->row();

      $pp = $this->db->where('id_employee', $leads->handled_by)
                     ->get('db_hr.tb_position_program')
                     ->row();

      return $adm = $this->db->select('tb_position_program.id_employee')
                      ->join('db_hr.tb_employee','tb_employee.id_employee = tb_position_program.id_employee')
                      ->where('id_intended_program', 1)
                      ->where('id_position_program >', $pp->id_position_program)
                      ->where('tb_employee.campus_based', $id_campus)
                      ->where('position_program_order >',0)
                      ->or_where('id_intended_program', 4)
                      ->where('id_position_program >', $pp->id_position_program)
                      ->where('tb_employee.campus_based', $id_campus)
                      ->where('position_program_order >',0)
                      ->get('db_hr.tb_position_program')
                      ->row();
  }

  public function check_initial_wmu_position_program($id_campus){
      return  $this->db->select('tb_position_program.id_employee')
                       ->join('db_hr.tb_employee','tb_employee.id_employee = tb_position_program.id_employee')
                       ->where('id_intended_program', 3)
                       ->where('position_program_order >',0)
                       ->where('tb_employee.campus_based', $id_campus)
                       ->group_by('tb_position_program.id_employee')
                       ->order_by('tb_position_program.id_position_program','asc')
                       ->get('db_hr.tb_position_program')
                       ->row();
  }

  public function check_last_handled_by_wmu($id_leads, $id_campus){
      $leads = $this->db->select('tb_initial_contact.handled_by')
                                      ->where('id_leads', $id_leads)
                                      ->get('tb_initial_contact')
                                      ->row();

      $pp = $this->db->where('id_employee', $leads->handled_by)
                     ->get('db_hr.tb_position_program')
                     ->row();

      return $adm = $this->db->select('tb_position_program.id_employee')
                      ->join('db_hr.tb_employee','tb_employee.id_employee = tb_position_program.id_employee')
                      ->where('id_intended_program', 3)
                      ->where('id_position_program >', $pp->id_position_program)
                      ->where('tb_employee.campus_based', $id_campus)
                      ->where('position_program_order >',0)
                      ->get('db_hr.tb_position_program')
                      ->row();
  }

  public function proof_of_payment_notification(){
    $id_leads_payment = 43;
    $datas['view'] = 'receiver';
    $datas['leads_payment'] = $this->leads_model->get_detail_leads_payment($id_leads_payment);
    echo $message = $this->load->view('Email/Leads/proof_of_payment_notification_view', $datas, TRUE);
  }

  public function duplicate(){
    $leads = $this->db->where('id_campus','3')
                      //->where('duplicate','1')
                      ->get('tb_leads')
                      ->result();

    foreach ($leads as $key) {
      $this->leads_model->cek_duplicate_detail($key->id_leads);

      echo $key->id_leads.' '.$key->name.''.$key->family_name.'<br>';

    }

    echo 'haha';
    
    echo 'sukses';
    //header("refresh: 0.5");
    
  }

  public function cek_duplicate_detail($id_leads)
    {
        $leads = $this->leads_model->get_detail_leads($id_leads);

        $leads1['duplicate'] = 0;
        $this->db->where('id_leads', $id_leads)
                ->update('tb_leads', $leads1);

        $this->db->where('id_leads', $id_leads)
                 ->or_where('id_leads_duplicate', $id_leads)
                 ->delete('tb_leads_duplicate');

        if ($leads->phone != '' && $leads->email != '') {
            $check = $this->db->where('phone', $leads->phone)
                              ->where('email', $leads->email)
                              ->where('id_leads !=', $id_leads)
                              ->get('tb_leads')
                              ->result();
            foreach ($check as $key) {
                $duplicate_column = 2;
                $duplicate_notes = 'Email, Phone';
                //$this->insert_cek_duplicate_detail($id_leads, $key->id_leads, $duplicate_column, $duplicate_notes);
            }
        } 

        if ($leads->phone != '' && $leads->dob != '0000-00-00') {
            $check = $this->db->where('phone', $leads->phone)
                              ->where('dob', $leads->dob)
                              ->where('id_leads !=', $id_leads)
                              ->get('tb_leads')
                              ->result();
            foreach ($check as $key) {
                $duplicate_column = 2;
                $duplicate_notes = 'DOB, Phone';
                //$this->insert_cek_duplicate_detail($id_leads, $key->id_leads, $duplicate_column, $duplicate_notes);
            }
        } 

        if ($leads->email != '' && $leads->dob != '0000-00-00') {
            $check = $this->db->where('email', $leads->email)
                              ->where('dob', $leads->dob)
                              ->where('id_leads !=', $id_leads)
                              ->get('tb_leads')
                              ->result();
            foreach ($check as $key) {
                $duplicate_column = 2;
                $duplicate_notes = 'DOB, Email';
                //$this->insert_cek_duplicate_detail($id_leads, $key->id_leads, $duplicate_column, $duplicate_notes);
            }
        } 


        /*if ($leads->name != '' && $leads->email != '') {

            $name_explode = explode(" ",$leads->name);

            foreach ($name_explode as $key) {
                $check = $this->db->like('name', $key)
                              ->where('email', $leads->email)
                              ->where('id_leads !=', $id_leads)
                              ->get('tb_leads')
                              ->result();
                foreach ($check as $key) {
                    $duplicate_column = 2;
                    $duplicate_notes = 'Name, Email';
                    $this->insert_cek_duplicate_detail($id_leads, $key->id_leads, $duplicate_column, $duplicate_notes);
                }
            }
        }*/ 

        if ($leads->name != '' && $leads->dob != '0000-00-00') {

            $name_explode = explode(" ",$leads->name);

            foreach ($name_explode as $key) {
              if ($key != '') {
                
                  $check = $this->db->like('name', $key)
                                    ->where('dob', $leads->dob)
                                    ->where('id_leads !=', $id_leads)
                                    ->get('tb_leads')
                                    ->result();
                                    
                  foreach ($check as $row) {
                      $duplicate_column = 2;
                      $duplicate_notes = 'Name, DOB';
                      //$this->insert_cek_duplicate_detail($id_leads, $row->id_leads, $duplicate_column, $duplicate_notes);

                      echo $leads->name.' | '.$key.'<br>';
                  }
                }
            }
        } 

        /*if ($leads->name != '' && $leads->phone != '') {

            $name_explode = explode(" ",$leads->name);

            foreach ($name_explode as $key) {
                $check = $this->db->like('name', $key)
                              ->where('phone', $leads->phone)
                              ->where('id_leads !=', $id_leads)
                              ->get('tb_leads')
                              ->result();
                foreach ($check as $key) {
                    $duplicate_column = 2;
                    $duplicate_notes = 'Name, Phone';
                    $this->insert_cek_duplicate_detail($id_leads, $key->id_leads, $duplicate_column, $duplicate_notes);
                }
            }
        } */
    }

  public function gooo(){


    $intake_year = '2022';
    $intake_month = '2';

    $intake_leads = $intake_year.'-'.$intake_month.'-01';

    $student = $this->db->select('*, tb_student.id_student, tb_student.id_intended_program, tb_student.id_course, tb_student.id_program_type, tb_student.id_specialist, year(tb_student.intake_leads) as intake_year, tb_student.id_campus, month(tb_student.intake_leads) as intake_month, employee_name, tb_student.intake_leads')
                          ->join('tb_intended_program','tb_intended_program.id_intended_program=tb_student.id_intended_program','left')
                          ->join('tb_course','tb_course.id_course=tb_student.id_course','left')
                          ->join('tb_specialist','tb_specialist.id_specialist=tb_student.id_specialist','left')
                          ->join('tb_campus','tb_campus.id_campus=tb_student.id_campus','left')
                          ->join('tb_program_type','tb_program_type.id_program_type=tb_student.id_program_type','left')
                          ->join('db_hr.tb_employee','tb_employee.id_employee = tb_student.handled_by','left')
                          ->join('tb_student_status','tb_student_status.id_student_status = tb_student.id_student_status')
                          ->where('id_student', '2483')
                          ->get('tb_student')
                          ->row();

    echo $student->intake_leads;

    if ($student->intake_leads == $intake_leads) {
                echo 'a';
    } else {
                echo 'b';
    }

    
  }

  public function leads_school(){
    $leads = $this->db->select('*, tb_school.id_qualification')
                      ->join('tb_school','tb_school.id_school = tb_leads.id_school')
                      ->get('tb_leads')
                      ->result();
    $no = 1;
    foreach ($leads as $key) {
      $no++;
      $data['id_qualification'] = $key->id_qualification;

      $this->db->where('id_leads', $key->id_leads)
               ->update('tb_leads', $data);
    }
    echo $no;

    echo 'sukses';
  }

  public function check_admission_doc(){
    $doc = $this->db->join('tb_document_type','tb_document_type.id_document_type = tb_document.id_doc_type')
                    ->where('id_category','3')
                    ->get('tb_document')
                    ->result();

    print_r($doc);
  }

  public function username_ediis(){
    $student_stie = $this->db->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                             ->where('tb_student.id_campus', '1')
                             ->get('tb_student')
                             ->result();

    foreach ($student_stie as $key) {
      $data['username'] = $key->student_id;

      $this->db->where('id_leads', $key->id_leads)
               ->update('tb_leads', $data);

      $leads = $this->db->where('id_leads', $key->id_leads)
                        ->get('tb_leads')
                        ->row();

      $check_username = $this->db->where('username', $leads->username)
                                 ->get('tb_user')
                                 ->row();
      if ($check_username == null) {
         $us['username'] = $leads->username;
         $us['id_leads'] = $key->id_leads;
         $us['user_last_updated'] = date('Y-m-d H:i:s');
         $us['user_full_name'] = $key->name.' '.$key->family_name;

         $this->db->insert('tb_user', $us);

         echo $key->username.'-'.$key->name.' '.$key->family_name.'<br>';
      }
    }
  }

  public function no_expired_date(){
    $leads = $this->db->where('expired_date','0000-00-00')
                      ->get('tb_leads')
                      ->result();

    foreach ($leads as $key) {
      $date = date('Y-m-d', strtotime($key->create_date));
      $date_new = date('Y-m-d', strtotime('+90 day', strtotime($date)));

       echo $key->name.' '.$key->family_name.' - '.$date.' '.$date_new.' <br>';
       
       $data['expired_date'] = $date_new;

       $this->db->where('id_leads', $key->id_leads)
                ->update('tb_leads', $data);
    }

    echo 'sukses';
  }

  public function get2080(){
    $main = $this->db->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                     ->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                     ->where('id_class_lecture','')
                     ->get('tb_class_student')
                     ->result();
  }

  public function form_barcode()
  {
    $form = $this->db->where('form_barcode','')
                     ->where('id_leads_form >=', '2763')
                     ->limit(1)
                     ->get('tb_leads_form')
                     ->result();

    foreach ($form as $key) {

      if($key->form_barcode == '') {
      
        echo $link = 'https://ediis.jic.ac.id/intranet/student/form/electronic_signature/'.$key->id_leads_form;

        $file = $this->student_model->generate_code($link, $key->id_leads_form);

        $data['form_barcode'] = $file;

        $this->db->where('id_leads_form', $key->id_leads_form)
                 ->update('tb_leads_form', $data);
      }
      
    }

    echo 'sukses';
    header("refresh: 0.5");
  }

  public function cek_string(){
    $numbers = array('A001', 'A002', 'A111', 'A009', 'A008');
    sort($numbers);

    $arrlength = count($numbers);
    for($x = 0; $x < $arrlength; $x++) {
        echo $numbers[$x];
        echo "<br>";
    }
  }

  public function cek_2410(){

    $attendance_range_min = 80;
    $attendance_range_max = 100;

    $id_main_class = 2410;

    $abc = $this->academic_master_model->get_inter_class_student_by_main_class($id_main_class);
    $check_class_score = $this->db->where('id_main_class', $id_main_class)
                                      ->where('id_score', '1')
                                      ->get('tb_class_score')
                                      ->row();
    foreach ($abc as $key) {

       $this->generate_attendance_score($id_main_class,$key->id_class_student, $attendance_range_min, $attendance_range_max, $check_class_score->id_class_score,'round');

    }
    
  }

  public function looping_floor($id_class_student, $attendance_range_min, $attendance_range_max, $type){

  }

  public function generate_attendance_score($id_main_class,$id_class_student, $attendance_range_min, $attendance_range_max, $id_class_score, $round_type){
        $score = $this->academic_master_model->random_score($attendance_range_min, $attendance_range_max);
        $total_meeting = $this->academic_master_model->total_meeting_by_class_student($id_class_student, 'total');
        $formula = $round_type(($score * $total_meeting) / 100);
        $attendance = $this->academic_master_model->total_meeting_by_class_student($id_class_student, 'result');
        $absent = $total_meeting - $formula;
        $point = 100 / $total_meeting;
        $set_point = (100 / $total_meeting) / 2;

        $no = 1;
        $total = 0;

        foreach ($attendance as $att) {
            $data['id_class_attendance'] = $att->id_class_attendance;
            $data['id_class_student'] = $id_class_student;

            $sisa = $score - $total;

            if ($sisa >= $point) {
              if ($attendance_range_max <= 50) {
                $attend = substr(str_shuffle('pse'), 0, 1);
              } else {
                $attend = substr(str_shuffle('p'), 0, 1);
              }
            } else {
              if ($sisa > 0) {
                $attend = substr(str_shuffle('se'), 0, 1);
              } else {
                $attend = substr(str_shuffle('a'), 0, 1);
              }
            }

            if ($attend == 'p') {
              $hmm = $point * 1;
            } else if($attend == 's') {
              $hmm = $point * 0.5;
            } else if($attend == 'e') {
              $hmm = $point * 0.5;
            } else {
              $hmm = 0;
            }

            $total += $hmm;            

            echo $sisa.' / ';

           /* if ($no <= $absent) {
                if ($round_type == 'floor') {
                    $attend = 'a';
                } else {    
                    $attend = substr(str_shuffle('se'), 0, 1);
                }
            } else {
                $attend = 'p';
            }*/

            $no++;

            $data['attendance'] = $attend;
            $data['class_attendance_detail_last_updated'] = date('Y-m-d H:i:s');
            $data['class_attendance_detail_updated_by'] = $this->session->id_employee;

            $check_att = $this->db->select('id_class_attendance_detail')
                                  ->where('id_class_attendance', $att->id_class_attendance)
                                  ->where('id_class_student', $id_class_student)
                                  ->get('tb_class_attendance_detail')
                                  ->row();

            if ($check_att == null) {
                $this->db->insert('tb_class_attendance_detail', $data);
            } else {
                $this->db->where('id_class_attendance_detail', $check_att->id_class_attendance_detail)
                         ->update('tb_class_attendance_detail', $data);
            }
        }

        echo '<br>';

        $this->academic_master_model->save_attendance_score($id_main_class, $id_class_student, $id_class_score);

        $check = $this->db->where('id_class_student', $id_class_student)
                          ->where('id_class_score', $id_class_score)
                          ->get('tb_class_student_score')
                          ->row();

        if ($check->student_score > $attendance_range_max) {

          //$this->generate_attendance_score($id_main_class,$id_class_student, $attendance_range_min, $attendance_range_max, $id_class_score, 'floor');

        } /*else if ($check->student_score < $attendance_range_min) {

          $this->generate_attendance_score($id_main_class,$id_class_student, $attendance_range_min, $attendance_range_max, $id_class_score);

        } else {

        }*/

        //return true;

    }

  public function random_employee(){
    $employee = $this->db->order_by('id_employee','random')
                         ->get('db_hr.tb_employee')
                         ->result();
    foreach ($employee as $key) {
      echo $key->id_employee.'<br>';
    }
  }

  public function tes_score_scale(){

        $id_main_class = 2162;

        $intake_start = $this->db->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                                 ->where('id_main_class', $id_main_class)
                                 ->order_by('intake_leads','asc')
                                 ->get('tb_class_student')
                                 ->row()->intake_leads;

       $intake_end = $this->db->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                                 ->where('id_main_class', $id_main_class)
                                 ->order_by('intake_leads','desc')
                                 ->get('tb_class_student')
                                 ->row()->intake_leads;

         echo $intake_start.'-'.$intake_end;

        $score_scale_group = $this->db->where('intake_start_date <=', $intake_start)
                                      ->where('intake_end_date >=', $intake_start)
                                      ->or_where('intake_end_date >=', $intake_end)
                                      ->where('intake_start_date <=', $intake_end)
                                      ->get('tb_score_scale_group')
                                      ->result();

        foreach ($score_scale_group as $key) {
           echo $key->id_score_scale_group;
        }
    
  }

  public function random(){
    for ($i=0; $i < 10; $i++) { 
       echo (rand(76, 95) . "<br>");
    }
  }

  public function get_student_papua(){
    $student = $this->db->where('id_intended_program', 8)
                        ->or_where('id_intended_program', 9)
                        ->or_where('id_intended_program', 10)
                        ->get('tb_student')
                        ->result();

    foreach ($student as $key) {
      
       $papua['papua'] = 1;

       $this->db->where('id_leads', $key->id_leads)
                ->update('tb_leads', $papua);
    }

    echo 'sukses';
  }

  public function add_campus_to_leads(){
    $leads = $this->db->where('id_campus','')
                      ->get('tb_leads')
                      ->result();

    foreach ($leads as $key) {
       $student = $this->db->where('id_leads', $key->id_leads)
                           ->order_by('id_student','asc')
                           ->get('tb_student')
                           ->row();

       if ($student != null) {
          $data['id_campus'] = $student->id_campus;

           $this->db->where('id_leads', $key->id_leads)
                    ->update('tb_leads', $data);
       }
    }

    echo 'suskes';
  }

  public function change_new_student(){
    $data['new_student'] = 2;

    $this->db->where('new_student',0)
             ->update('tb_student', $data);

    $datas['new_student'] = 3;

    $this->db->where('new_student',1)
             ->update('tb_student', $datas);


    $data['new_student'] = 1;

    $this->db->where('new_student', 2)
             ->update('tb_student', $data);

    $datas['new_student'] = 0;

    $this->db->where('new_student', 3)
             ->update('tb_student', $datas);

    echo 'sukses';
  }

  public function generate_barcode_form(){
    $form = $this->db->where('form_barcode','')
                     ->limit(1)
                     ->get('tb_leads_form')
                     ->result();

    foreach ($form as $key) {
      if($key->form_barcode == '') {
      
        $link = base_url().'student/form/electronic_signature/'.$key->id_leads_form;
        $file = $this->student_model->generate_code($link, $key->id_leads_form);

        $data['form_barcode'] = $file;

        $this->db->where('id_leads_form', $key->id_leads_form)
                 ->update('tb_leads_form', $data);
      }
    }

    echo 'sukses';
    header("refresh: 0.5");
  }

  public function get_family_for_leads_form(){
    $form = $this->db->join('tb_family','tb_family.id_family = tb_leads_form.id_family')
                     ->get('tb_leads_form')
                     ->result();

    foreach ($form as $key) {
       $data['guardian_name'] = $key->name;

       $this->db->where('id_leads_form', $key->id_leads_form)
                ->update('tb_leads_form', $data);
    }

    echo 'sukses';
  }

  public function get_family_for_form(){
    $form = $this->db->where('guardian_name','')
                     ->get('tb_leads_form')
                     ->result();

    foreach ($form as $key) {
       $family = $this->db->where('id_leads', $key->id_leads)
                          ->get('tb_family')
                          ->row();

       $data['guardian_name'] = $family->name;

       $this->db->where('id_leads_form', $key->id_leads_form)
                ->update('tb_leads_form', $data);
    }
  }

  public function get_leads_for_mobile_phone(){
    $leads = $this->db->get('tb_leads')
                      ->result();

    foreach ($leads as $key) {
       
    }
  }

  public function dashboard_admission(){
    $query = $this->db->select('count(id_student) as count')
                      ->where('id_student_status', 1)
                      ->where('intake_leads !=','0000-00-00')
                      ->group_by('YEAR(intake_leads)')
                      ->order_by('intake_leads','asc')
                      ->get('tb_student');

    $data['click'] = json_encode(array_column($query->result(), 'count'),JSON_NUMERIC_CHECK);
   
    $query = $this->db->select('count(id_student) as count')
                      ->where('intake_leads !=','0000-00-00')
                      ->group_by('YEAR(intake_leads)')
                      ->order_by('intake_leads','asc')
                      ->get('tb_student');
    
    $data['viewer'] = json_encode(array_column($query->result(), 'count'),JSON_NUMERIC_CHECK);

    $query = $this->db->select('intake_leads')
                      ->where('intake_leads !=','0000-00-00')
                      ->group_by('YEAR(intake_leads)')
                      ->order_by('intake_leads','asc')
                      ->get('tb_student')
                      ->result();

    $data['year'] = $query;
   
    $this->load->view('Testing/my_chart', $data);
  }

  public function date75(){
    $date_75 = date('Y-m-d', strtotime('-75 day', strtotime(date('Y-m-d'))));

    echo $date_75;
  }

  public function stie_2021(){
    $student = $this->db->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                        ->like('student_id', '2121')
                        ->order_by('name')
                        ->get('tb_student')
                        ->result();

    foreach ($student as $key) {
       echo $key->id_student.' '.$key->name.' '.$key->family_name.' '.$key->student_id.' | '.$key->id_session.'<br>';
    }
  }

  public function get_replacement_december(){
    $class = $this->db->select('*,ori.employee_name as ori_name, replacement.employee_name as replacement_name, ori.id_employee as id_ori, replacement.id_employee as id_replacement')
                      ->join('db_hr.tb_employee as replacement','replacement.id_employee = tb_class_attendance.class_attendance_updated_by')
                      ->join('db_hr.tb_employee as ori','ori.id_employee = tb_class_attendance.id_employee')
                      ->join('tb_main_class','tb_main_class.id_main_class = tb_class_attendance.main_class_join')
                      ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                      ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                      ->where('attendance_date >=', '2021-11-16')
                      ->where('attendance_date <=', '2021-12-15')
                      ->where('tb_class_attendance.attendance_type', 'Replacement')
                      ->get('tb_class_attendance')
                      ->result();

    $data['class'] = $class;

    $this->load->view('Replacement_class_view', $data);

  }



  public function no_receipt(){
     $this->db->SELECT('RIGHT(tb_leads_payment.no_receipt,3) as kode', FALSE)
                    ->order_by('no_receipt','DESC')  
                    ->limit(1); 

          $query = $this->db->get('tb_leads_payment');      //cek dulu apakah ada sudah ada kode di tabel.    
          if($query->num_rows() <> 0){      
           //jika kode ternyata sudah ada.      
           $data = $query->row();      
           $kode = intval($data->kode) + 1;    
          }
          else {      
           //jika kode belum ada      
           $kode = 1;    
          }
          $kodemax = str_pad($kode, 3, "0", STR_PAD_LEFT); // angka 4 menunjukkan jumlah digit angka 0
          $kodejadi = "RC".date('ymd').$kodemax;    // hasilnya ODJ-991-0001 dst.
          echo $kodejadi; 
  }

  public function change_program_type(){
    $id_intended_program = 3;

    $student = $this->db->join('tb_program_type','tb_program_type.id_program_type = tb_student.id_program_type')
                        ->where('tb_student.id_intended_program', $id_intended_program)
                        ->get('tb_student')
                        ->result();

    foreach ($student as $key) {
       $check_specialist = $this->db->where('specialist_code', $key->program_type_code)
                                    ->get('tb_specialist')
                                    ->row();

       $data['id_specialist'] = $check_specialist->id_specialist;

       $this->db->where('id_student', $key->id_student)
                ->update('tb_student', $data);
    }

    echo 'sukses';
  }

  public function change_student_status(){
    $student = $this->db->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                        ->where('id_status !=','10')
                        ->get('tb_student')
                        ->result();

    foreach ($student as $key) {

       if ($key->id_student_status == 1) {

         $check_enrollment = $this->db->where('class_student_softdel',0)
                                      ->where('id_student', $key->id_student)
                                      ->get('tb_class_student')
                                      ->row();

          if ($check_enrollment != null) {
            $data['id_status'] = '5';
          } else {
            $data['id_status'] = $key->id_status;
          }
         
       } else {
         //$data['id_status'] = '10';
       }

       $this->db->where('id_leads', $key->id_leads)
                ->update('tb_leads', $data);
    }

    echo 'sukses';
  }

  public function change_papua(){
    $student = $this->db->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                        ->get('tb_student')
                        ->result();
  }

  public function attendance(){
    $id_main_class = '113';


    $schedule_student = $this->db->join('tb_class_student','tb_class_student.id_class_student = tb_schedule_student.id_class_student')
                                 ->join('tb_main_class','tb_main_class.id_main_class = tb_class_student.id_main_class')
                                 ->where('main_class_join', $id_main_class)
                                 ->get('tb_schedule_student')
                                 ->result();

    foreach ($schedule_student as $ss) {
       $check_ss = $this->add_schedule_student($ss->id_student, $ss->id_class_lecture);
       //echo $ss->id_student.'-'.$ss->id_class_lecture.'<br>';
    }

     $detail = $this->db->join('tb_class_attendance','tb_class_attendance.id_class_attendance = tb_class_attendance_detail.id_class_attendance')
                        ->join('tb_class_student','tb_class_student.id_class_student = tb_class_attendance_detail.id_class_student')
                        ->where('main_class_join', $id_main_class)
                        ->get('tb_class_attendance_detail')
                        ->result();

     foreach ($detail as $key) {

        $check_ss = $this->db->join('tb_class_student','tb_class_student.id_class_student = tb_schedule_student.id_class_student')
                             ->where('id_class_lecture', $key->id_class_lecture)
                             ->where('id_student', $key->id_student)
                             ->get('tb_schedule_student')
                             ->result();

        foreach ($check_ss as $ss) {

          $search = $this->db->where('id_class_student', $ss->id_class_student)
                              ->where('id_class_attendance', $key->id_class_attendance)
                              ->get('tb_class_attendance_detail')
                              ->row();

          if ($search == null) {
            $data['id_class_attendance'] = $key->id_class_attendance;
            $data['id_class_student'] = $ss->id_class_student;
            $data['attendance'] = $key->attendance;
            $data['notes_attendance'] = $key->notes_attendance;
            $data['class_attendance_detail_updated_by'] = 69;
            $data['class_attendance_detail_last_updated'] = date('Y-m-d H:i:s');

            $this->db->insert('tb_class_attendance_detail', $data);
          }
          
        }
     }

    

    echo 'sukses';
  }


  public function add_leads_to_library(){
    $leads = $this->db->get('tb_leads')
                      ->result();

    foreach ($leads as $key) {
      $this->library_model->add_library_user($key->id_leads,'','3');
    }

    echo 'sukses';
  }

  public function add_employee_to_library(){
    $leads = $this->db->get('db_hr.tb_employee')
                      ->result();

    foreach ($leads as $key) {
      $this->library_model->add_library_user('',$key->id_employee,'8');
    }

    echo 'sukses';
  }

  public function add_schedule_student($id_student, $id_class_lecture){

                $cl = $this->master_model->get_class_lecture_by_id($id_class_lecture);

                $main_class = $this->db->join('tb_main_class','tb_main_class.id_main_class = tb_class_student.id_main_class')
                                       ->where('id_student', $id_student)
                                       ->where('main_class_join', $cl->main_class_join)
                                       ->get('tb_class_student')
                                       ->result();

                foreach ($main_class as $mc) {
                    
                    $data['id_class_student'] = $mc->id_class_student;
                    $data['id_class_lecture'] = $id_class_lecture;

                        $cek = $this->db->where('id_class_student', $mc->id_class_student)
                                        ->where('id_class_lecture', $id_class_lecture)
                                        ->get('tb_schedule_student')
                                        ->row();

                        if ($cek == null) {
                            $hasil = $this->db->insert('tb_schedule_student', $data);

                            $log_menu = 'Student Schedule';
                            $log_action = 'Delete';
                            $log_notes = 'Add student ('.$id_student.') in schedule ('.$id_class_lecture.')';
                            $log_id_items = $this->db->insert_id();
                            $log_id_items_name = 'id_schedule_student';
                            $log_link = 'academic/master/class_lecture/student/'.$cl->id_main_class;

                            $this->admin_master_model->insert_log($log_menu, $log_action, $log_notes, $log_id_items, $log_id_items_name, $log_link);
                        }
                }



        
        $result['status'] = $hasil;
        return json_encode($result);
    }

  public function student_for_class(){
      $id_main_class = 2123;

      $specialist = $this->academic_master_model->get_class_specialist_by_main_class($id_main_class);

      print_r($specialist);

      $leads = $this->db->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                          ->join('tb_status','tb_status.id_status = tb_leads.id_status')
                          ->join('tb_course','tb_course.id_course=tb_student.id_course','left')
                          ->join('tb_campus','tb_campus.id_campus = tb_student.id_campus','left')
                          ->where('id_student_status', '1')
                          ->where('tb_status.id_status_type', '1')
                          ->where_in('tb_student.id_specialist', $specialist)
                          ->get('tb_student')
                          ->result();
      foreach ($leads as $key) {
         echo $key->name.'<br>';
      }
  }

  public function whatsapp(){

      $apiURL = 'https://api.chat-api.com/instanceYYYYY/';
      $token = 'abcdefgh12345678';

      $message = 'helo';
      $phone = '081654973361';

      $data = json_encode(
          array(
              'chatId'=>$phone.'@c.us',
              'body'=>$message
          )
      );
      $url = $apiURL.'message?token='.$token;
      $options = stream_context_create(
          array('http' =>
              array(
                  'method'  => 'POST',
                  'header'  => 'Content-type: application/json',
                  'content' => $data
              )
          )
      );
      $response = file_get_contents($url,false,$options);
      echo $response; exit;
  }
  

  public function get_semester_active($id_class_student = '', $id_trimester = '', $id_semester = ''){

      $student = $this->get_class_student_by_id($id_class_student);

      $get_highest_trimester = $this->db->select('trimester_course_structure')
                                        ->join('tb_main_class','tb_main_class.id_main_class=tb_class_lecture.id_main_class')
                                        ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                                        ->where('tb_class_student.id_class_student', $id_class_student)
                                        ->order_by('trimester_course_structure','desc')
                                        ->get('tb_class_student')
                                        ->row()->trimester_course_structure;
      $get_highest_semester = $this->db->select('semester_course_structure')
                                        ->join('tb_main_class','tb_main_class.id_main_class=tb_class_lecture.id_main_class')
                                        ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                                        ->where('tb_class_student.id_class_student', $id_class_student)
                                        ->order_by('semester_course_structure','desc')
                                        ->get('tb_class_student')
                                        ->row()->semester_course_structure;
  }

  public function total_row(){
    $query = $this->db->select('tb_employee.id_employee')
                      ->join('db_hr.tb_employee','tb_employee.id_employee = tb_log.id_employee')
                      ->group_by('tb_log.id_employee')
                      ->count_all_results('tb_log');

    echo $query;
  }

  public function add_initial_contact(){

    //Yogi 132
    //SHeila 67
    //Septi 98

    $leads = $this->db->where('id_campus','1')
                      ->get('tb_student')
                      ->result();

    foreach ($leads as $key) {

      $ic['handled_by'] = 98;
      $ic['initial_date'] = date('Y-m-d H:i:s');
      $ic['insert_date'] = date('Y-m-d H:i:s');
      $ic['id_leads'] = $key->id_leads;
      $ic['assigned_by'] = 69;

      $check = $this->db->where('id_leads', $key->id_leads)
                        ->where('handled_by', 98)
                        ->get('tb_initial_contact')
                        ->row();

      if ($check == null) {
        $this->db->insert('tb_initial_contact', $ic);
      }
    }

    echo 'sukses';

  }

  public function autocomplete()
  {
   /* $query = $this->db->like('name')
                      ->get('tb_leads')
                      ->result();*/

    $query = $this->request->getVar("query");

    $builder = $this->db->table("tb_leads");

    $builder->select('*');
    $builder->like('name', $query, 'both');
    $query = $builder->get();

    $data = $query->getResult();

    //echo $this->db->getLastQuery();
    $countries = [];
    
    if (count($data) > 0) {

      foreach ($query as $country) {
        $countries[] = $country->name;
      }
    }
    return json_encode($countries);
  }

  public function initial_contact(){
    $initial_contact = $this->db->get('tb_initial_contact')
                                ->result();

    foreach ($initial_contact as $key) {
       $ic['insert_date'] = $key->initial_date;

       $this->db->where('id_initial_contact', $key->id_initial_contact)
                ->update('tb_initial_contact', $ic);
    }

    echo 'sukses';
  }

  public function handled_by(){
    $leads = $this->db->get('tb_leads')
                      ->result();

    foreach ($leads as $key) {
       $std['handled_by'] = $key->id_owner;

       $this->db->where('id_leads', $key->id_leads)
                ->update('tb_student', $std);

       $ic['id_leads'] = $key->id_leads;
       $ic['assigned_by'] = 69;
       $ic['handled_by'] = $key->id_owner;
       $ic['initial_date'] = $key->initial_contact;
       
       $check = $this->db->where('id_leads', $key->id_leads)
                ->where('handled_by', $key->id_owner)
                ->get('tb_initial_contact')
                ->row();

        if ($check == null) {
          $this->db->insert('tb_initial_contact', $ic);
        } else {

        }

    }

    echo 'suskes';
  }

  public function scale(){

    $actual_score = 79.3;

    $scaled_grade = $this->db->where('range_based_min <=', $actual_score)
                                 ->where('range_based_max >=', $actual_score)
                                 ->where('id_intended_program', 3)
                                 ->get('tb_scaled_grade')
                                 ->row();

    $final_score = round(((($actual_score - $scaled_grade->range_based_min) * $scaled_grade->co_eff) / $scaled_grade->scaled_co_eff) + $scaled_grade->actual_range, 2);

    echo $final_score;
  }

  public function change_month_of_intake(){
    $mufy = $this->db->where('month(intake_leads)', '06')
                     ->where('id_intended_program','1')
                     ->get('tb_student')
                     ->result();

    $monash = $this->db->where('month(intake_leads)', '01')
                     ->where('id_intended_program','4')
                     ->get('tb_student')
                     ->result();

    $wmu = $this->db->where('month(intake_leads)', '01')
                     ->where('id_intended_program','3')
                     ->get('tb_student')
                     ->result();


    foreach ($mufy as $key) {
      $data['intake_leads'] = date('Y', strtotime($key->intake_leads)).'-07-'.date('d', strtotime($key->intake_leads));

      $this->db->where('id_student', $key->id_student)
               ->update('tb_student', $data);
    }

    foreach ($monash as $key) {
      $data['intake_leads'] = date('Y', strtotime($key->intake_leads)).'-02-'.date('d', strtotime($key->intake_leads));

      $this->db->where('id_student', $key->id_student)
               ->update('tb_student', $data);
    }

    foreach ($wmu as $key) {
      $data['intake_leads'] = date('Y', strtotime($key->intake_leads)).'-02-'.date('d', strtotime($key->intake_leads));

      $this->db->where('id_student', $key->id_student)
               ->update('tb_student', $data);
    }

    echo 'sukses';
  }

  public function get_leads(){
    $leads = $this->db->get('tb_leads')
                      ->result();

    foreach ($leads as $key) {
       $this->it_student_model->create_user($key->id_leads,'');
    }
  }

  public function get_employee_user(){
    $leads = $this->db->get('db_hr.tb_employee')
                      ->result();

    foreach ($leads as $key) {
       $this->it_student_model->create_user('', $key->id_employee);
    }
  }

  public function get_student_library(){
    $user = $this->db->get('library.user')
                     ->result();
    print_r($user);
  }

  public function tes_log(){
    $row = $this->db->select('COUNT(*) as abc')
                    //->join('db_hr.tb_employee','tb_employee.id_employee = tb_log.id_employee')
                    ->get('tb_log')
                    ->row();

    echo $row->abc;
  }

  public function student_papua(){
      $transit = $this->db->where('kloter','5')
                          ->get('tb_transit')
                          ->result();

      $no = 0;
      foreach ($transit as $key) {
         ++$no;

         $check_leads = $this->db->where('name', $key->nama)
                                 ->where('family_name', $key->nama_belakang)
                                 ->where('phone', $key->hp)
                                 ->get('tb_leads')
                                 ->row();

         $leads['name'] = ucwords(strtolower($key->nama));
         $leads['family_name'] = ucwords(strtolower($key->nama_belakang));
         $leads['id_country'] = 102;
         $leads['phone'] = $key->hp;
         $leads['email'] = $key->email;
         $leads['religion'] = $key->agama;
         $leads['dob'] = $key->dob;
         $leads['id_status'] = 4;
         $leads['id_gender'] = $key->jenis_kelamin;

          if ($check_leads == null) {

             $this->db->insert('tb_leads', $leads);
          } else {

             $this->db->where('id_leads', $check_leads->id_leads)
                      ->update('tb_leads', $leads);

          }

          $check = $this->db->where('name', $key->nama)
                            ->where('family_name', $key->nama_belakang)
                            ->where('phone', $key->hp)
                            ->get('tb_leads')
                            ->row();

          $this->it_student_model->generate_username($check->id_leads);

          if ($check->id_user_moodle == '') {
            $this->moodle_model->synchronize_user($check->id_leads,'');
          }

          $this->library_model->add_library_user($check->id_leads,'','3');

          $check_student = $this->db->where('id_leads', $check->id_leads)
                                    ->get('tb_student')
                                    ->row();

          if ($key->tipe == 'VJ') {
              $std['id_leads'] = $check->id_leads;
              $std['id_intended_program'] = 10;
              $std['id_course'] = 27;
              $std['id_specialist'] = '28';
              $std['id_program_type'] = 16;
              $std['id_program'] = 2;
              $std['id_campus'] = 5;
              $std['id_session'] = 1;

            } else {
              $std['id_leads'] = $check->id_leads;
              $std['id_intended_program'] = 8;
              $std['id_course'] = 24;
              $std['id_specialist'] = '22';
              $std['id_program_type'] = 13;
              $std['id_program'] = 3;
              $std['id_campus'] = 5;
              $std['id_session'] = 1;
              
            }
            $std['student_active'] = 1;
            $std['id_student_status'] = 1;
            $std['intake_leads'] = '2021-10-01';
            $std['student_last_updated'] = '2021-09-22 19:55:00';
            $std['semester_active'] = 1;
            $std['trimester_active'] = 1;

          if ($check_student == null) {
            
            $this->db->insert('tb_student', $std);
          } else {
            $this->db->where('id_student', $check_student->id_student)
                     ->update('tb_student', $std);
          }

          $check_father = $this->db->where('id_leads', $check->id_leads)
                                   ->where('name', $key->nama_ayah)
                                   ->get('tb_family')
                                   ->row();

          $fat['name'] = ucwords(strtolower($key->nama_ayah));
          $fat['relationship'] = 'Father';
          $fat['phone'] = $key->no_telepon_ayah;
          $fat['id_leads'] = $check->id_leads;

            if ($check_father == null) {
              $this->db->insert('tb_family', $fat);
            } else {
              $this->db->where('id_leads', $check->id_leads)
                       ->update('tb_family', $fat);
            }

          $check_mother = $this->db->where('id_leads', $check->id_leads)
                                   ->where('name', $key->nama_ibu)
                                   ->get('tb_family')
                                   ->row();
          

          /*$mom['name'] = ucwords(strtolower($key->nama_ibu));
          $mom['relationship'] = 'Mother';
          $mom['phone'] = $key->no_telepon_ibu;
          $mom['id_leads'] = $check->id_leads;

             if ($check_mother == null) {

              $this->db->insert('tb_family', $mom);
            } else {
              $this->db->where('id_leads', $check->id_leads)
                       ->update('tb_family', $mom);
            }*/
      }

      echo $no;
      echo 'sukses';
  }

  public function change_vokasi(){
    $vokasi = $this->db->get('tb_student')
                       ->result();

    $no = 0;
    foreach ($vokasi as $key) {
       $no++;
       echo $no.' '.$key->id_student.'<br>';

       $check = $this->db->where('id_leads', $key->id_leads)
                         ->get('tb_leads')
                         ->row();

       if ($check == null) {
          $this->db->where('id_leads', $key->id_leads)
                   ->delete('tb_student');
       }

       /*$data['id_intended_program'] = '10';
       $data['id_course'] = '27';
       $data['id_specialist'] = '28';
       $data['id_program_type'] = '16';

       $this->db->where('id_student', $key->id_student)
                ->update('tb_student', $data);*/
    }

  }

  public function hahaha(){
    phpinfo();
  }

  public function pdf(){

    $file = "somefile.xlsx";
$mime = 'application/application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
ob_end_clean(); // this is solution
header('Content-Description: File Transfer');
header('Content-Type: ' . $mime);
header("Content-Transfer-Encoding: Binary");
header("content-disposition", "attachment; filename=somefile.xlsx");
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
readfile($file);

  }

  public function student_stie_username(){
    $leads = $this->db->join('tb_student','tb_student.id_leads = tb_leads.id_leads')
                      ->where('tb_leads.id_leads >=', '1283')
                      ->where('id_user_moodle','')
                      ->get('tb_leads')
                      ->result();

    foreach ($leads as $key) {
       /*$data['username'] = $key->student_id;

       $this->db->where('id_leads', $key->id_leads)
                ->update('tb_leads', $data);*/

       $moodle = $this->moodle_model->synchronize_user($key->id_leads, '');
    }

    echo 'sukses';
  }

  public function change_employee_in_attendance(){
    $attendance = $this->db->select('id_class_attendance, tb_class_lecture.id_employee, attendance_start, attendance_hours, start, class_lecture_hours')
                           ->join('tb_class_lecture','tb_class_lecture.id_class_lecture = tb_class_attendance.id_class_lecture')
                           ->where('id_class_attendance >=','10477')
                           ->get('tb_class_attendance')
                           ->result();


    foreach ($attendance as $key) {

      if ($key->attendance_start == '00:00:00') {

      $data['attendance_start'] = $key->start;
      $data['attendance_hours'] = $key->class_lecture_hours;

      $this->db->where('id_class_attendance', $key->id_class_attendance)
               ->update('tb_class_attendance', $data);

      }
    }

    echo 'sukses';
  }

  public function change_class_lecture(){
    $class_lecture = $this->db->get('tb_class_lecture')
                              ->result();

    foreach ($class_lecture as $key) {
       $schedule = $this->db->where('class_lecture_join',$key->class_lecture_join)
                            ->get('tb_schedule_detail')
                            ->row();

        $data['day'] = $schedule->day;
        $data['start'] = $schedule->start;
        $data['id_room'] = $schedule->id_room;

        $this->db->where('id_class_lecture', $key->id_class_lecture)
                 ->update('tb_class_lecture', $data);
    }

    echo 'sukses';
  }

  public function change_class_attendance(){
    $class_lecture = $this->db->get('tb_class_lecture')
                                 ->result();

    foreach ($class_lecture as $key) {

       $schedule = $this->db->join('tb_class_lecture','tb_class_lecture.class_lecture_join = tb_schedule_detail.class_lecture_join')
                            ->where('tb_class_lecture.class_lecture_join',$key->class_lecture_join)
                            ->get('tb_schedule_detail')
                            ->row();

        $data['id_class_lecture'] = $schedule->id_class_lecture;

        $this->db->where('id_schedule_detail', $schedule->id_schedule_detail)
                 ->update('tb_class_attendance', $data);
    }

    echo 'sukses';
  }

  public function change_schedule_student(){
    $class_lecture = $this->db->get('tb_class_lecture')
                                 ->result();

    foreach ($class_lecture as $key) {

       $schedule = $this->db->join('tb_class_lecture','tb_class_lecture.class_lecture_join = tb_schedule_student.class_lecture_join')
                            ->where('tb_schedule_student.class_lecture_join',$key->class_lecture_join)
                            ->get('tb_schedule_student')
                            ->row();

        $data['id_class_lecture'] = $schedule->id_class_lecture;

        $this->db->where('class_lecture_join', $schedule->class_lecture_join)
                 ->update('tb_schedule_student', $data);
    }

    echo 'sukses';
  }

  public function hehe(){
    if ($this->agent->is_referral())
    {
        echo $refer =  $this->agent->referrer();
    } else {
      echo 'hoho';
    }

    echo $this->agent->is_referral();
  }

  public function export_excel(){

    header("Content-Type: application/vnds-ms-excel");
    header("Content-disposition: attachment; filename=\"hola.xlsx\"");

  }

  public function change_to_manual(){
    $moodle = $this->load->database('moodle', TRUE);

    $abc = '2021-05-15 00:00:00';

    $student_moodle = $moodle->select('mdl_user_enrolments.id as id_user, mdl_enrol.id as id_enroll, mdl_enrol.courseid as id_course')
                             ->join('mdl_enrol','mdl_enrol.id = mdl_user_enrolments.enrolid')
                             ->where('enrol','self')
                             ->where('courseid !=','603')
                             ->get('mdl_user_enrolments')
                             ->result();

    foreach ($student_moodle as $key) {

       $enrol = $moodle->where('courseid', $key->id_course)
                       ->where('enrol', 'manual')
                       ->get('mdl_enrol')
                       ->row();

       $data['enrolid'] = $enrol->id;

       echo $key->id_user.' - '.$enrol->id.'<br>';

       $moodle->where('mdl_user_enrolments.id', $key->id_user)
                ->update('mdl_user_enrolments', $data);
    }

    echo 'sukses';

    
  }

  public function sinkron_user_moodle(){
    $leads = $this->db->where('id_user_moodle','')
                      ->get('tb_leads')
                      ->result();

    foreach ($leads as $key) {
       $this->moodle_model->synchronize_user($key->id_leads,'');
    }

    echo 'sukses';
  }

  public function claim_form_status(){
    $claim = $this->db->get('tb_claim_form')
                      ->result();

    foreach ($claim as $key) {

       if ($key->teacher != '') {
         $data['teacher_status'] = 1;
       }

       if ($key->academic_staff != '') {
         $data['academic_staff_status'] = 1;
       }

       if ($key->academic_director != '') {
         $data['academic_director_status'] = 1;
       }

       if ($key->finance_staff != '') {
         $data['finance_staff_status'] = 1;
       }

       if ($key->finance_manager != '') {
         $data['finance_manager_status'] = 1;
       }

       $this->db->where('id_claim_form', $key->id_claim_form)
                ->update('tb_claim_form', $data);

    }

    echo 'sukses';
  }


  public function generate_username(){

        $id_leads = 1157;

        $leads = $this->db->where('id_leads', $id_leads)->get('tb_leads')->row();

        $first_name = substr($leads->name,0,1);
        $last_name = substr($leads->family_name,0,3);
        $result = strtolower($first_name.''.$last_name);

        $check_username = $this->db->like('username', $result)
                                   ->order_by('username','desc')
                                   ->get('tb_leads')
                                   ->row();

        if ($check_username == null) {

          $username = $result.'0001';
        } else {

          $username_number = intval(substr($check_username->username, -4)) + 1;

          $digit = strlen($username_number);

          if ($digit == 1) {
            $zero = '000';
          } elseif ($digit == 2) {
            $zero = '00';
          } elseif ($digit == 3) {
            $zero = '0';
          } else {
            $zero = '';
          }

          echo $username = $result.''.$zero.''.$username_number;
        }

        if($leads->username == ''){

            $user['username'] = $username;
            //$user['account_password'] = $this->generate_password();
        
           /* $this->db->where('id_leads', $id_leads)
                    ->update('tb_leads', $user);*/
        }
  }

  public function get_device(){
    echo gethostname();
    echo gethostbyname(gethostname());
    echo $_SERVER['HTTP_HOST'];
    echo $_SERVER['SERVER_SIGNATURE'];
    echo $_SERVER['SERVER_NAME'];
    echo $_SERVER['SERVER_ADDR'];
    echo $_SERVER['SERVER_PORT'];
    echo $_SERVER['REMOTE_ADDR'];
    echo gethostbyaddr($_SERVER['REMOTE_ADDR']);
    echo php_uname();
  }

  public function claim_form(){
    $result = $this->db->select('tb_employee.id_employee, tb_claim_form.id_teaching_period, tb_claim_form.teacher, tb_claim_form.academic_staff,tb_claim_form.academic_director, tb_claim_form.finance_staff, tb_claim_form.finance_manager, tb_employee.employee_name, teacher_last_updated, academic_staff_last_updated, academic_director_last_updated, finance_staff_last_updated, finance_manager_last_updated')
                  ->join('db_hr.tb_employee','tb_employee.id_employee = tb_class_attendance.id_employee')
                          ->join('tb_claim_form','tb_claim_form.id_employee = tb_employee.id_employee')
                          ->join('tb_main_class','tb_main_class.main_class_join=tb_class_attendance.main_class_join')
                          //->where('id_teaching_period', $id_teaching_period)
                          //->order_by($get['sortby'], $get['sortby2'])
                          ->group_by('tb_employee.id_employee')
                          ->get('tb_class_attendance')
                          ->result();

      print_r($result);
  }

  public function change_teacher_in_charge(){
    $main_class = $this->db->where('teacher_in_charge','')
                           ->get('tb_main_class')
                           ->result();

    $no = 0;
    foreach ($main_class as $mc) {
       $cl = $this->db->where('main_class_join', $mc->main_class_join)
                      ->get('tb_class_lecture')
                      ->row();

        $data['teacher_in_charge'] = $cl->id_employee;

        $this->db->where('id_main_class', $mc->id_main_class)
                 ->update('tb_main_class', $data);

        ++$no;
    }

    echo 'sukses'.$no;
  }

  public function change_employee(){
    $employee = $this->db->get('db_hr.tb_employee')
             ->result();

    foreach ($employee as $key) {
       $data['employee_degree'] = $key->employee_name;
       $data['employee_local_degree'] = $key->employee_name;

       $upd = $this->db->where('id_employee', $key->id_employee)
                       ->update('db_hr.tb_employee', $data);
    }

    echo 'sukses';

  }

  public function check_schedule(){
     $schedule = $this->db->join('tb_schedule_detail','tb_schedule_detail.class_lecture_join = tb_schedule_student.class_lecture_join')
                        ->join('tb_class_lecture','tb_class_lecture.class_lecture_join=tb_schedule_detail.class_lecture_join')
                        ->join('tb_class_student','tb_class_student.id_class_student = tb_schedule_student.id_class_student')
                        ->where('tb_class_student.id_class_student', 6071)
                        ->where('tb_class_lecture.main_class_join', 330)
                        //->where('tb_class_student.class_student_softdel','0')
                        ->group_by('tb_schedule_detail.id_schedule_detail')
                        ->get('tb_schedule_student')
                        ->result();

        $all = 0;

        foreach ($schedule as $data) {

            $all += $this->db->where('id_schedule_detail', $data->id_schedule_detail)
                             ->get('tb_class_attendance')
                             ->num_rows();

            echo $data->id_class_student.'-'.$data->id_schedule_detail.'<br>';                             
            
        }   

        echo $all; 
  }

  public function get_leads_moodle(){
    $leads = $this->db->get('tb_leads')
                      ->result();

    foreach ($leads as $key) {
       $this->moodle_model->synchronize_user($key->id_leads,'');
    }

    echo 'sukses';
  }

  public function get_employee_moodle(){
    $leads = $this->db->get('db_hr.tb_employee')
                      ->result();

    foreach ($leads as $key) {
       $this->moodle_model->synchronize_user('', $key->id_employee);
    }

    echo 'sukses';
  }

  public function define_employee_username(){
    $leads = $this->db->get('db_hr.tb_employee')
                      ->result();

    foreach ($leads as $key) {
        $email = $key->employee_email;

        $username = explode('@', $key->employee_email);

        $data['employee_username'] = $username[0];

        $this->db->where('id_employee', $key->id_employee)
                 ->update('db_hr.tb_employee', $data);
    }

    echo 'sukses';
  }

  public function enroll_to_moodle($id_class_student, $id_user_moodle, $id_course_moodle, $enrollment_type){

        /*$id_class_student = 9624;
        $id_user_moodle = 2568;
        $id_course_moodle = 672;
        $enrollment_type = 'manual';*/

        $moodle = $this->load->database('moodle', TRUE);

        $enrol = $moodle->where('courseid', $id_course_moodle)
                               ->where('enrol', $enrollment_type)
                               ->get('mdl_enrol')
                               ->row();

        $check_enrollment = $moodle->where('userid', $id_user_moodle)
                                   ->where('enrolid', $enrol->id)
                                   ->get('mdl_user_enrolments')
                                   ->row();

        if ($enrollment_type == 'manual') {
          $modifierid = $this->session->userdata('id_employee_moodle');
        } else {
          $modifierid = $this->session->userdata('id_user_moodle');
        }

        if ($check_enrollment == null) {
            $user['enrolid'] = $enrol->id;
            $user['userid'] = $id_user_moodle;
            $user['timestart'] = time();
            $user['timecreated'] = time();
            $user['timemodified'] = time();
            $user['modifierid'] = $modifierid;

            $hasil = $moodle->insert('mdl_user_enrolments', $user);

            $id_class_student_moodle = $moodle->insert_id();

        } else {

            $id_class_student_moodle = $check_enrollment->id;
        }

        $ul['id_class_student_moodle'] = $id_class_student_moodle;

        $this->db->where('id_class_student', $id_class_student)
                 ->update('tb_class_student', $ul);

        echo 'sukses';
    }

    public function tes_waktu(){
      $cs= '1614847799';

      echo date('d M Y H:i:s', $cs);

     /* $new = '2021-03-04 14:28:33';

      //echo date('d M Y H:i:s', $cs);
      echo strtotime($new);*/
    }

  public function moodle(){
    $moodle = $this->load->database('moodle', TRUE);

    $user = $moodle->where('username','kuma0001')
                      ->get('mdl_user')
                      ->row();

    echo $user->username.'-'.$user->id;
  }

  public function ubah_prodi(){
    $kelas = $this->db->get('tb_kelas_siakad')
                      ->result();

    foreach ($kelas as $key) {
      if ($key->prodi == 'Manajemen') {
        $data['id_course'] = '20';
      } else {
        $data['id_course'] = '21';
      }

      $this->db->where('id_kelas', $key->id_kelas)
               ->update('tb_kelas_siakad', $data);
    }

    echo 'sukses';
  }

  public function ubah_semester(){
    $kelas = $this->db->get('tb_kelas_siakad')
                      ->result();

    foreach ($kelas as $key) {
      $check = $this->db->where('semester_name', $key->semester)
                        ->where('id_intended_program','7')
                        ->get('tb_semester')
                        ->row();

      if ($check != null) {

        $data['id_semester'] = $check->id_semester;

        $this->db->where('id_kelas', $key->id_kelas)
                  ->update('tb_kelas_siakad', $data);
      }
    }

    echo 'sukses';
  }

  public function ubah_teacher(){
    $kelas = $this->db->get('tb_kelas_siakad')
                      ->result();

    foreach ($kelas as $key) {

      $check = $this->db->where('employee_name', $key->lecturer)
                        ->get('db_hr.tb_employee')
                        ->row();

      if ($check != null) {

        $data['teacher_in_charge'] = $check->id_employee;

        $this->db->where('id_kelas', $key->id_kelas)
                 ->update('tb_kelas_siakad', $data);
      }
    }

    echo 'sukses';
  }

  public function ubah_trimester(){
    $kelas = $this->db->get('tb_kelas_siakad')
                      ->result();

    foreach ($kelas as $key) {

      $tmt = substr($key->trimester, -1);

      $gbg = $key->ta.' '.$tmt;

      $check = $this->db->where('trimester_name', $gbg)
                        ->where('id_intended_program', '7')
                        ->get('tb_trimester')
                        ->row();

      if ($check != null) {

        $data['id_trimester'] = $check->id_trimester;

        $this->db->where('id_kelas', $key->id_kelas)
                 ->update('tb_kelas_siakad', $data);
      } 
    }

    echo 'sukses';
  }

  public function ubah_session(){
    $kelas = $this->db->get('tb_kelas_siakad')
                      ->result();

    foreach ($kelas as $key) {

      if ($key->waktu == 'Pagi') {
        $id_session = 1;
      } else {
        $id_session = 2;
      }

      $data['id_session'] = $id_session;

        $this->db->where('id_kelas', $key->id_kelas)
                 ->update('tb_kelas_siakad', $data);
    }

    echo 'sukses';
  }

  public function ubah_course_structure(){
    $kelas = $this->db->get('tb_kelas_siakad')
                      ->result();

    foreach ($kelas as $key) {

       if ($key->wajib == 'Y') {
         $elec = 0;
       } else {
         $elec = 1;
       } 

       $check = $this->db->select('tb_detail_course_structure.id_detail_course_structure, trimester_course_structure')
                ->join('tb_course_structure','tb_course_structure.id_course_structure = tb_detail_course_structure.id_course_structure')
                ->join('tb_subject','tb_subject.id_subject = tb_detail_course_structure.id_subject')
                ->where('id_course', $key->id_course)
                ->where('subject_name', $key->nama_matkul)
                ->where('subject_code', $key->kode_matkul)
                ->where('elective', $elec)
                ->get('tb_detail_course_structure')
                ->row();

       $data['id_detail_course_structure'] = $check->id_detail_course_structure;
       $data['trimester'] = $check->trimester_course_structure;

       $this->db->where('id_kelas', $key->id_kelas)
               ->update('tb_kelas_siakad', $data);
    }

    echo 'sukses';
  }

  public function tes_schedule(){
     $schedule = $this->db->join('tb_schedule','tb_schedule.id_schedule=tb_schedule_student.id_schedule')
                        ->join('tb_schedule_detail','tb_schedule_detail.id_schedule = tb_schedule.id_schedule')
                        ->join('tb_class_lecture','tb_class_lecture.class_lecture_join=tb_schedule_detail.class_lecture_join')

                        ->where('tb_schedule_student.id_class_student', 6984)
                        ->where('tb_class_lecture.main_class_join', 438)
                        ->group_by('tb_schedule_detail.id_schedule_detail')
                        ->get('tb_schedule_student')
                        ->result();

     print_r($schedule);
  }

  public function score_stie(){
      $stie = $this->db//->where('id_main_class >=', 2619)
                       ->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                       ->where('id_campus','1')
                       ->where('semester_name','2021/2022 Ganjil')
                       ->get('tb_main_class')
                       ->result();

      foreach ($stie as $key) {

         $data1['id_score'] = 1;
         $data1['id_main_class'] = $key->id_main_class;
         $data1['score_percentage'] = 20;
         $data1['class_score_max'] = 100;

         $check1 = $this->db->where('id_score', 1)
                            ->where('id_main_class', $key->id_main_class)
                            ->get('tb_class_score')
                            ->row();

         if ($check1 == null) {
           $this->db->insert('tb_class_score', $data1);
         }


         $data2['id_score'] = 47;
         $data2['id_main_class'] = $key->id_main_class;
         $data2['score_percentage'] = 20;
         $data2['class_score_max'] = 100;

         $check2 = $this->db->where('id_score', 47)
                            ->where('id_main_class', $key->id_main_class)
                            ->get('tb_class_score')
                            ->row();

         if ($check2 == null) {
           $this->db->insert('tb_class_score', $data2);
         }


        /* $data3['id_score'] = 48;
         $data3['id_main_class'] = $key->id_main_class;
         $data3['score_percentage'] = 10;
         $data3['class_score_max'] = 100;

         $check3 = $this->db->where('id_score', 48)
                            ->where('id_main_class', $key->id_main_class)
                            ->get('tb_class_score')
                            ->row();
         if ($check3 == null) {
           $this->db->insert('tb_class_score', $data3);
         }*/

         $data4['id_score'] = 49;
         $data4['id_main_class'] = $key->id_main_class;
         $data4['score_percentage'] = 20;
         $data4['class_score_max'] = 100;

         $check4 = $this->db->where('id_score', 49)
                            ->where('id_main_class', $key->id_main_class)
                            ->get('tb_class_score')
                            ->row();
         if ($check4 == null) {
           $this->db->insert('tb_class_score', $data4);
         }

         $data5['id_score'] = 50;
         $data5['id_main_class'] = $key->id_main_class;
         $data5['score_percentage'] = 40;
         $data5['class_score_max'] = 100;

         $check5 = $this->db->where('id_score', 50)
                            ->where('id_main_class', $key->id_main_class)
                            ->get('tb_class_score')
                            ->row();

         if ($check5 == null) {
           $this->db->insert('tb_class_score', $data5);
         }

      }

      echo 'sukses';


  }


  public function upload_absensi(){
    $this->load->view('Excel/upload');
  }

  public function myupload()
{
    $this->load->library('upload');//loading the library
    $imagePath = realpath('./uploads/');//this is your real path APPPATH means you are at the application folder
    $number_of_files_uploaded = count($_FILES['files']['name']);
    if ($number_of_files_uploaded > 5){ // checking how many images your user/client can upload
        $carImages['return'] = false;
        $carImages['message'] = "You can upload 5 Images";
        echo json_encode($carImages);
    }
    else{
        for ($i = 0; $i <  $number_of_files_uploaded; $i++) {
            $_FILES['userfile']['name']     = $_FILES['files']['name'][$i];
            $_FILES['userfile']['type']     = $_FILES['files']['type'][$i];
            $_FILES['userfile']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
            $_FILES['userfile']['error']    = $_FILES['files']['error'][$i];
            $_FILES['userfile']['size']     = $_FILES['files']['size'][$i];
            //configuration for upload your images
            $config = array(
                'file_name'     => $_FILES['files']['name'][$i],
                'allowed_types' => 'jpg|jpeg|png|gif|pdf',
                'max_size'      => 3000,
                'overwrite'     => FALSE,
                'upload_path'
                =>$imagePath
            );
            $this->upload->initialize($config);
            $errCount = 0;//counting errrs
            if (!$this->upload->do_upload())
            {
                $error = array('error' => $this->upload->display_errors());
                $carImages[] = array(
                    'errors'=> $error
                );//saving arrors in the array
            }
            else
            {
                $filename = $this->upload->data();
                $carImages[] = array(
                    'fileName'=>$filename['file_name'],
                );
            }//if file uploaded
            
        }//for loop ends here

        echo json_encode($carImages);//sending the data to the jquery/ajax or you can save the files name inside your database.
    }//else

    }

    public function get_student_for_library(){
        if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 1; $row <= $highestRow; $row++)
            {
              $data = array();
              $id_leads = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
              $username = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
              $name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $family_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();

              $fullname = $name.' '.$family_name;

              $student['id_leads'] = $id_leads;
              $student['ediis_name'] = $fullname;

              $check = $this->db->where('userStudentId', $username)
                                ->get('library.user')
                                ->row();

              if ($check != null) {
                 $hasil = $this->db->where('userId', $check->userId)
                                   ->update('library.user', $student);

                if ($hasil == true) {
                  $no++;
                }
              }
              
            }

            echo $no;
          }
        }

    }


    public function get_subject(){
        if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 1; $row <= $highestRow; $row++)
            {
              $data = array();

              $subject_name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $subject_alias = $worksheet->getCellByColumnAndRow(2, $row)->getValue();

              $subject['subject_alias'] = $subject_alias;

              $hasil = $this->db->where('subject_name', $subject_name)
                                ->update('tb_subject', $subject);

              if ($hasil == true) {
                $no++;
              }
              
            }

            echo $no;
          }
        }

    }

    public function get_teacher_in_charge(){
        if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 1; $row <= $highestRow; $row++)
            {
              $data = array();

              $teacher_name = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $student_id = $worksheet->getCellByColumnAndRow(0, $row)->getValue();

              $student = $this->db->where('student_id', $student_id)
                                  ->get('tb_student')
                                  ->row();

              $employee = $this->db->where('employee_name', $teacher_name)
                                   ->get('db_hr.tb_employee')
                                   ->row();

              if ($student->intake_leads >= '2019-06-01' AND $student->id_course == '20') {

                $dosen_pa = $this->db->where('id_course', $student->id_course)
                                     ->get('tb_course')
                                     ->row();

                $id_employee = $dosen_pa->head_of_program;
              } elseif ($student->intake_leads >= '2020-06-01' AND $student->id_course == '21') {

                $dosen_pa = $this->db->where('id_course', $student->id_course)
                                     ->get('tb_course')
                                     ->row();

                $id_employee = $dosen_pa->head_of_program;
              } else {
                $id_employee = $employee->id_employee;
              }

              $data['pa_teacher'] = $id_employee;


              $hasil = $this->db->where('student_id', $student_id)
                                ->update('tb_student', $data);

              if ($hasil == true) {
                $no++;
              }
              
            }

            echo $no;
          }
        }

    }

    public function get_course_structure(){
        if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 1; $row <= $highestRow; $row++)
            {
              $data = array();
              $id_cs = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $kode_matkul = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
              $nama_matkul = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
              $semester = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
              $elective = $worksheet->getCellByColumnAndRow(6, $row)->getValue();

              $sb = $this->db->where('subject_code', $kode_matkul)
                             ->where('subject_name', $nama_matkul)
                             ->get('tb_subject')
                             ->row();

              if ($sb != null) {

              $student['id_course_structure'] = $id_cs;
              $student['id_subject'] = $sb->id_subject;
              $student['elective'] = $elective;
              $student['semester_course_structure'] = $semester;
              $student['course_structure_active'] = 1;

              $check = $this->db->where('id_subject', $sb->id_subject)
                                ->where('elective', $elective)
                                ->where('id_course_structure', $id_cs)
                                ->get('tb_detail_course_structure')
                                ->row();

                if ($check == null) {
                  $hasil = $this->db->insert('tb_detail_course_structure', $student);
                }

              }

              if ($hasil == true) {
                $no++;
              }
              
            }

            echo $no;
          } 
        }

    }

    public function get_phone_code(){
        if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 1; $row <= $highestRow; $row++)
            {
              $data = array();
              $country_name = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
              $phone_code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();

              $student['phone_code'] = $phone_code;

                 $hasil = $this->db->where('country_name', $country_name)
                                   ->update('tb_country', $student);

                if ($hasil == true) {
                  $no++;
                }
            }

            echo $no;
          }
        }

    }

    public function get_agent_excel(){
        if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 2; $row <= $highestRow; $row++)
            {
              $data = array();

              $id_agent = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $agent_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
              $email = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
              $id_company = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
              $address = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
              $id_agent_registered = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
              $contact_person = $worksheet->getCellByColumnAndRow(5, $row)->getValue();

              $company_name = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
              $agent_registered = $worksheet->getCellByColumnAndRow(8, $row)->getValue();

              $agent['agent_name'] = $agent_name;
              $agent['id_company'] = $id_company;
              $agent['id_agent_registered'] = $id_agent_registered;
              $agent['agent_email'] = $email;
              $agent['agent_contact_person'] = $contact_person;
              $agent['agent_address'] = $address;
              $agent['agent_last_updated'] = date('Y-m-d H:i:s');
              $agent['agent_inserted_at'] = date('Y-m-d H:i:s');
              $agent['agent_updated_by'] = 69;

              $check_agent = $this->db->where('agent_name', $agent_name)
                                        ->get('tb_agent')
                                        ->row();

              if ($check_agent == null) {
                 $hasil = $this->db->insert('tb_agent', $agent);
              }

              $company['id_company'] = $id_company;
              $company['company_name'] = $company_name;

              $check_company = $this->db->where('company_name', $company_name)
                                        ->get('tb_company')
                                        ->row();
              if ($check_company == null) {
                $this->db->insert('tb_company', $company);
              }

              $ar['id_agent_registered'] = $id_agent_registered;
              $ar['agent_registered'] = $agent_registered;

              $check_agent_registered = $this->db->where('agent_registered', $agent_registered)
                                                 ->get('tb_agent_registered')
                                                 ->row();

              if ($check_agent_registered == null) {
                $this->db->insert('tb_agent_registered', $ar);
              }


                if ($hasil == true) {
                  $no++;
                }
            }

            echo $no;
          }
        }

    }

    public function get_main_class(){
        if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 1; $row <= $highestRow; $row++)
            {
              $data = array();
              $nim = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
              $id_main_class = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
              $final_score = $worksheet->getCellByColumnAndRow(18, $row)->getValue();
              $id_kp = $worksheet->getCellByColumnAndRow(4, $row)->getValue();


              $nilai_absensi = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
              $nilai_tugas = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
              $nilai_paper = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
              $nilai_uts = $worksheet->getCellByColumnAndRow(16, $row)->getValue();
              $nilai_uas = $worksheet->getCellByColumnAndRow(17, $row)->getValue();

              $main_class = $this->db->where('id_kp', $id_kp)
                                     ->get('tb_main_class')
                                     ->row();

              $student = $this->db->where('student_id', $nim)
                                     ->get('tb_student')
                                     ->row();

              $score_scale = $this->db->where('min_score <=', $final_score)
                                      ->where('max_score >=', $final_score)
                                      ->where('id_intended_program',7)
                                      ->get('tb_score_scale')
                                      ->row();

              if ($nim != 1) {
                

                $class_student['id_student'] = $student->id_student;
                $class_student['id_main_class'] = $main_class->id_main_class;
                $class_student['finance_clearance'] = 1;
                $class_student['class_student_updated_by'] = 69;
                $class_student['final_score'] = $final_score;
                $class_student['id_score_scale'] = $score_scale->id_score_scale;

                $check = $this->db->where('id_student', $student->id_student)
                                  ->where('id_main_class', $main_class->id_main_class)
                                  ->get('tb_class_student')
                                  ->row();

                if ($check == null) {

                  $hasil = $this->db->insert('tb_class_student', $class_student);
                  $id_class_student = $this->db->insert_id();

                  $score_attendance = 1;
                  $score_tugas = 47;
                  $score_paper = 48;
                  $score_uts = 49;
                  $score_uas = 50;

                  $class_score_absensi = $this->db->where('id_main_class', $main_class->id_main_class)
                                                  ->where('id_score', $score_attendance)
                                                  ->get('tb_class_score')
                                                  ->row()->id_class_score;

                  $class_score_tugas = $this->db->where('id_main_class', $main_class->id_main_class)
                                                  ->where('id_score', $score_tugas)
                                                  ->get('tb_class_score')
                                                  ->row()->id_class_score;

                  $class_score_paper = $this->db->where('id_main_class', $main_class->id_main_class)
                                                  ->where('id_score', $score_paper)
                                                  ->get('tb_class_score')
                                                  ->row()->id_class_score;

                  $class_score_uts = $this->db->where('id_main_class', $main_class->id_main_class)
                                                  ->where('id_score', $score_uts)
                                                  ->get('tb_class_score')
                                                  ->row()->id_class_score;

                  $class_score_uas = $this->db->where('id_main_class', $main_class->id_main_class)
                                                  ->where('id_score', $score_uas)
                                                  ->get('tb_class_score')
                                                  ->row()->id_class_score;

                  $att['student_score'] = $nilai_absensi;
                  $att['student_point'] = $nilai_absensi;
                  $att['id_class_score'] = $class_score_absensi;
                  $att['id_class_student'] = $id_class_student;
                  $att['student_score_last_updated'] = date('Y-m-d H:i:s');
                  $att['student_score_updated_by'] = 69;

                  $this->db->insert('tb_class_student_score', $att);

                  $tgs['student_score'] = $nilai_tugas;
                  $tgs['student_point'] = $nilai_tugas;
                  $tgs['id_class_score'] = $class_score_tugas;
                  $tgs['id_class_student'] = $id_class_student;
                  $tgs['student_score_last_updated'] = date('Y-m-d H:i:s');
                  $tgs['student_score_updated_by'] = 69;

                  $this->db->insert('tb_class_student_score', $tgs);

                  $pap['student_score'] = $nilai_paper;
                  $pap['student_point'] = $nilai_paper;
                  $pap['id_class_score'] = $class_score_paper;
                  $pap['id_class_student'] = $id_class_student;
                  $pap['student_score_last_updated'] = date('Y-m-d H:i:s');
                  $pap['student_score_updated_by'] = 69;

                  $this->db->insert('tb_class_student_score', $pap);

                  $uts['student_score'] = $nilai_uts;
                  $uts['student_point'] = $nilai_uts;
                  $uts['id_class_score'] = $class_score_uts;
                  $uts['id_class_student'] = $id_class_student;
                  $uts['student_score_last_updated'] = date('Y-m-d H:i:s');
                  $uts['student_score_updated_by'] = 69;

                  $this->db->insert('tb_class_student_score', $uts);

                  $uas['student_score'] = $nilai_uas;
                  $uas['student_point'] = $nilai_uas;
                  $uas['id_class_score'] = $class_score_uas;
                  $uas['id_class_student'] = $id_class_student;
                  $uas['student_score_last_updated'] = date('Y-m-d H:i:s');
                  $uas['student_score_updated_by'] = 69;

                  $this->db->insert('tb_class_student_score', $uas);

                }

              }
             
              

              if ($hasil == true) {
                $no++;
              } 
            }
            echo $no;
          }
       }
    }

    public function subject_name(){

      $subject = $this->db->get('tb_subject')
                          ->result();

      foreach ($subject as $key) {
         $data['subject_name'] = ucwords(strtolower($key->subject_name));

         $this->db->where('id_subject', $key->id_subject)
                  ->update('tb_subject', $data);
      }
    }

    public function update_trimester(){
        $student = $this->db->where('id_leads <','1105')
                            ->get('tb_student')
                            ->result();

        foreach ($student as $key) {

            $std['trimester_active'] = $key->trimester_active + 1;

            $this->db->where('id_student', $key->id_student)
                     ->update('tb_student', $std);
        }

        echo 'sukses';
    }

  public function get_employee(){

    $employee_type = $this->db->where('id_employee_type','5')
                  ->get('db_hr.tb_employee')
                  ->result();

    foreach ($employee_type as $keys) {
      $bb[] = $keys->id_employee;
    }

      $a = array();
            foreach ($employee_type as $key) {
                array_push($a, $key->id_employee);
            }

            if ($a == null) {

              $b = '';
            } else {
              $b = $a;
            }

    $employee = $this->db->where_not_in('id_employee', $b)
               ->get('db_hr.tb_employee')
               ->result();

    foreach ($employee as $key) {
      echo $key->employee_name;
    }
  }

  public function change_claim_form(){

    $id_main_class = $this->uri->segment(4);

    $attendance = $this->db->join('tb_schedule_detail','tb_schedule_detail.id_schedule_detail = tb_class_attendance.id_schedule_detail')
         ->join('tb_class_lecture','tb_class_lecture.class_lecture_join = tb_schedule_detail.class_lecture_join')
         ->where('tb_class_attendance.main_class_join', $id_main_class)
         ->get('tb_class_attendance')
         ->result();

         foreach ($attendance as $key) {
           $att['class_attendance_claim_form'] = $key->class_lecture_claim_form;

           $this->db->where('id_class_attendance', $key->id_class_attendance)
                ->update('tb_class_attendance', $att);
         }

         echo 'sukses';
  }


  public function check_requested_class(){

        $check_requested_class = $this->db->where('attendance_type !=','Regular')
                                          ->where('ca_academic_approval','0');

        if ($this->session->userdata('id_level') == '22') {
        
        $check_requested_class = $this->db->where('id_employee', $this->session->userdata('id_employee'));
        }

        $check_requested_class = $this->db->get('tb_class_attendance')
                                          ->num_rows();

        echo $check_requested_class;
    }

  public function change_class_attendance_claim_form(){
    $claim_form = $this->db->join('tb_schedule_detail','tb_schedule_detail.id_schedule_detail = tb_class_attendance.id_schedule_detail')
                 ->join('tb_class_lecture','tb_class_lecture.class_lecture_join = tb_schedule_detail.class_lecture_join')
                 ->get('tb_class_attendance')
                 ->result();

    foreach ($claim_form as $key) {
      $claim['class_attendance_claim_form'] = $key->class_lecture_claim_form;

      $this->db->where('id_class_attendance', $key->id_class_attendance)
           ->update('tb_class_attendance', $claim);
    }
    
    echo 'sukses';
  }

  public function main_class_join(){
        $main_class_join = $this->db->join('tb_main_class','tb_main_class.id_main_class=tb_class_lecture.id_main_class')
                       ->where('tb_class_lecture.main_class_join','')
                                             ->get('tb_class_lecture')
                                             ->result();

                foreach ($main_class_join as $key) {
                  $data['main_class_join'] = $key->main_class_join;

                  $this->db->where('id_class_lecture', $key->id_class_lecture)
                       ->update('tb_class_lecture', $data);
                }
            echo 'sukses';
  }

  public function attendance_start(){
        $class_attendance = $this->db->join('tb_schedule_detail','tb_schedule_detail.id_schedule_detail=tb_class_attendance.id_schedule_detail')
                                             ->get('tb_class_attendance')
                                             ->result();

                foreach ($class_attendance as $key) {
                  $data['attendance_start'] = $key->start;

                  $this->db->where('id_class_attendance', $key->id_class_attendance)
                       ->update('tb_class_attendance', $data);
                }
            echo 'sukses';
  }

  public function change_account(){
    $student = $this->db->where('trimester_active >=', 2)
              ->get('tb_student')
              ->result();
    foreach ($student as $key) {
      $up['ldap_user'] = '0';

      $this->db->where('id_leads', $key->id_leads)
           ->update('tb_leads', $up);
    }

    echo 'sukses';
  }

  public function change_meeting_total2333(){
    $class_lecture = $this->db->join('tb_main_class','tb_main_class.id_main_class=tb_class_lecture.id_main_class')
                  ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                        ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
                      ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
                  ->get('tb_class_lecture')
                  ->result();

    foreach ($class_lecture as $key) {

      $program = $this->db->where('id_intended_program', $key->id_intended_program)
                ->get('tb_intended_program')
                ->row();

      $data['meeting_total'] = $program->meeting_total;

      $this->db->where('id_class_lecture', $key->id_class_lecture)
           ->update('tb_class_lecture', $data);
    }

    echo 'sukses';
  }

  public function ubah_attendance(){

    $attendance = $this->db->get('tb_class_attendance')->result();

    foreach ($attendance as $key) {

      $cl = $this->db->join('tb_class_lecture','tb_class_lecture.class_lecture_join=tb_schedule_detail.class_lecture_join')
               ->where('id_schedule_detail', $key->id_schedule_detail)
               ->get('tb_schedule_detail')->row();

      $data['id_employee'] = $cl->id_employee;

      $this->db->where('id_class_attendance', $key->id_class_attendance)
           ->update('tb_class_attendance', $data);
    }

    echo 'sukses';
  }

    public function change_alphabet_format($alphabet){
        $name = strtolower($alphabet);
        return ucwords($name);
    }

    public function change_name(){
        $leads = $this->db->get('tb_leads')->result();

        foreach ($leads as $key) {
            $up['name'] = $this->change_alphabet_format($key->name);
            $up['family_name'] = $this->change_alphabet_format($key->family_name);

            $this->db->where('id_leads', $key->id_leads)->update('tb_leads', $up);

        }

        echo 'sukses';
    }

  public function cek_student_schedule(){

    $leads = $this->input->post('id_leads');
    $id_main_class = $this->input->post('id_main_class');

    $no = 1;
    foreach ($leads as $key) {
      if ($no == 1) {
        $wh = 'where';
      } else {
        $wh = 'or_where';
      }

      $get_leads = $this->db->$wh('id_leads', $key);

      $no++;
    }

    $get_leads = $this->db->order_by('name','asc')->get('tb_leads')->result();

    $data['id_main_class'] = $id_main_class;
      $data['leads'] = $get_leads;
      $data['left_bar'] = $this->admin_model->check_navbar();
      $this->load->view('Academic/Activity/list_student_schedule_crash_view', $data);
     
  }

  public function abc() {
    $data =  $this->session->flashdata('data');
    
  }

   public function get_student_siakad(){
        
        $mahasiswa = $this->db->join('db_akademi.tb_konsentrasi','tb_konsentrasi.id_konsentrasi=tb_mahasiswa.id_konsentrasi')
                              ->join('db_akademi.tb_prodi','tb_prodi.id_prodi=tb_konsentrasi.id_prodi') 
                              ->join('db_akademi.tb_bio','tb_bio.id_mahasiswa=tb_mahasiswa.id_mahasiswa','left') 
                              ->join('db_akademi.tb_status_mhs','tb_status_mhs.id_status=tb_mahasiswa.id_status','left')
                              ->join('db_akademi.tb_kelamin','tb_kelamin.id_kelamin=tb_bio.id_kelamin','left')
                              ->join('db_akademi.tb_waktu','tb_waktu.id_waktu=tb_mahasiswa.id_waktu','left') 
                              ->join('db_akademi.tb_grade','tb_grade.id_grade=tb_mahasiswa.id_grade','left')
                              ->join('db_akademi.tb_dosen','tb_dosen.id_dosen = tb_mahasiswa.dosen_pa','left')
                              ->join('db_akademi.tb_agama','tb_agama.id_agama = tb_bio.id_agama','left')
                              ->join('db_akademi.tb_ibu','tb_ibu.id_mahasiswa = tb_mahasiswa.id_mahasiswa','left')
                              ->join('db_akademi.tb_alamat','tb_alamat.id_mahasiswa = tb_mahasiswa.id_mahasiswa','left')
                              ->get('db_akademi.tb_mahasiswa')
                              ->result();

        foreach ($mahasiswa as $key) {

           $check = $this->db->where('username', $key->nim)
                             ->get('tb_leads')
                             ->row();

          /* if ($check == null) {
             //echo $key->nama_mahasiswa.' '.$key->nim.' | '.$check->name.'<br>';
           } else {
             echo $key->nama_mahasiswa.' '.$key->nim.' | '.$check->name.'<br>';
           }*/

         /*  $data['nik'] = $key->nik;
           $data['email'] = $key->email;
           $data['phone'] = $key->no_hp;
           $data['id_country'] = 102;
           $data['pob'] = $key->tempat_lahir;
           $data['address'] = $key->jalan.' '.$key->dusun.' '.$key->kelurahan.' '.$key->kecamatan.' '.$key->alamat_mhs;
           $data['postal_code'] = $key->kode_pos;
           $data['religion'] = $key->agama;*/

          /* $data['phone'] = $key->no_hp;

           $this->db->where('id_leads', $check->id_leads)
                    ->update('tb_leads', $data);*/

            if ($key->nama_ibu != '') {

              $ibu['name'] = $key->nama_ibu;
              $ibu['id_leads'] = $check->id_leads;

              $check_ibu = $this->db->where('id_leads', $check->id_leads)
                                    ->where('name', $key->nama_ibu)
                                    ->get('tb_family')
                                    ->row();

              if ($check_ibu == null) {
                $this->db->insert('tb_family', $ibu);
              }
              
            }
           
        }
    }

    public function get_student_stie_for_acreditation(){
        if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 3;

            for($row = 4; $row <= $highestRow; $row++)
            {
              $data = array();
              $no_student = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
              $student_type = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $student_id = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
              $nik = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
              $student_name = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
              $program = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
              $gender = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
              $ttl = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
              $religion = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
              $address = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
              $year_intake = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
              $student_name = trim(ucwords(strtolower($student_name)));
              $parts = explode(" ", $student_name);
              if(count($parts) > 1) {
                  $lastname = array_pop($parts);
                  $firstname = implode(" ", $parts);
              } else {
                  $firstname = $student_name;
                  $lastname = " ";
              }   
              $ttl_full = explode(",", $ttl);
              $pob = trim(ucwords(strtolower($ttl_full[0])));

              if ($no_student > 494) {

                $ymd = '';
                $exp = explode(" ", $ttl_full[1]);
                echo $date = $exp[1];
                echo $month = $exp[2];
                echo $year = $exp[3];

                if ($month == 'January') {
                  $mf = '01';
                } elseif ($month == 'February') {
                  $mf = '02';
                } elseif ($month == 'March') {
                  $mf = '03';
                } elseif ($month == 'April') {
                  $mf = '04';
                } elseif ($month == 'May') {
                  $mf = '05';
                } elseif ($month == 'June') {
                  $mf = '06';
                } elseif ($month == 'July') {
                  $mf = '07';
                } elseif ($month == 'August') {
                  $mf = '08';
                } elseif ($month == 'September') {
                  $mf = '09';
                } elseif ($month == 'October') {
                  $mf = '10';
                } elseif ($month == 'November') {
                  $mf = '11';
                } elseif ($month == 'December') {
                  $mf = '12';
                } else {
                  $mf = '00';
                }

                $dob = $year.'-'.$mf.'-'.$date;
              } else {
                $ymd = '';
                if ($ttl_full[1] != '') {
                    $ex = explode("-", $ttl_full[1]);
                    $ymd = $ex['2'] . '-' . $ex['1'] . '-' . $ex['0'];
                }
                $dob = $ymd;
              }

              if ($address != '' OR $address != '-') {

                $address = explode(" - ", $address);
                $kec = $address[0];
                $kab = $address[1];
                $prov = $address[2];

                $kab = trim(str_replace("Kab.","Kabupaten",$kab));
                $prov = str_replace(".","",$prov);
                $prov = trim(str_replace("Prov","",$prov));

                $check_kab = $this->db->where('regency_name',$kab)
                                      ->get('tb_regency')
                                      ->row();

                $check_prov = $this->db->where('province_name',$prov)
                                      ->get('tb_province')
                                      ->row();

                if ($check_kab == null) {
                  $kab_text = '<span style="color:red">'.$kab.'</span>';
                } else {
                  $kab_text = $check_kab->regency_name;
                }

                if ($check_prov == null) {
                  $prov_text = '<span style="color:red">'.$prov.'</span>';
                } else {
                  $prov_text = $check_prov->province_name;
                }
              } else {

              }
              

              if ($religion == 'Katolik' OR $religion == 'Katholik') {
                $religion = 'Katolik';
              } else {
                $religion = $religion;
              }

              $intake_leads = $year_intake.'-09-01';

              if($religion == 'Tidak diisi'){
                $religion = '';
              } else {
                $religion = $religion;

                $religion = $this->db->where('religion_indo', $religion)
                                   ->get('tb_religion')
                                   ->row()->religion;
              }

              

              if ($gender == 'L') {
                $id_gender = '1';
              } else {
                $id_gender = '2';
              }

              $student_name = trim($student_name);

              if ($nik == '-') {
                $nik = '';
              } else {
                $nik = $nik;
              }


              $data['name'] = $firstname;
              $data['family_name'] = $lastname;
              $data['id_status'] = 5;
              $data['id_country'] = 102;
              $data['create_date'] = $intake_leads.' '.date('H:i');
              $data['religion'] = $religion;
              $data['nik'] = $nik;
              $data['dob'] = $dob;
              $data['pob'] = $pob;
              $data['id_campus'] = 1;
              $data['id_gender'] = $id_gender;
              $data['create_id'] = 1188;

              $check_leads = $this->db->where('name', $firstname)
                                      ->where('family_name', $lastname)
                                      ->where('dob', $dob)
                                      ->get('tb_leads')
                                      ->row();

              if($student_type == 'inter'){

                if ($check_leads == null) {

                  if ($address != '') {
                    if ($check_prov != null) {
                      $data['id_province'] = $check_prov->id_province;
                    }
                    if ($check_kab != null) {
                      $data['id_regency'] = $check_kab->id_regency;
                    }
                     $data['address'] = $kec;
                  }

                  $this->db->insert('tb_leads', $data);
                  $id_leads = $this->db->insert_id();
                } else {
                  $this->db->where('id_leads', $check_leads->id_leads)
                           ->update('tb_leads', $data);
                  $id_leads = $check_leads->id_leads;
                }


                 /* if ($no_student > 494) {
                    $id1 = substr_replace($student_id, '.', 4, 0 );
                    $id1 = substr_replace($id1, '.', 9, 0 );
                  } else {
                    $id1 = substr_replace($student_id, '.', 6, 0 );
                    $id1 = substr_replace($id1, '.', 11, 0 );
                  }*/
                  
                  //$student_id = $id1;
                  $std['id_leads'] = $id_leads;
                  $std['student_id'] = $student_id;
                  $std['id_campus'] = 1;
                  $std['student_acreditation'] = 1;
                  $std['id_program'] = 1;
                  $std['id_intended_program'] = 7;
                  if ($program == 'S1 Akuntansi') {
                    $std['id_course'] = 21;
                    $std['id_specialist'] = 13;
                  } else {
                    $std['id_course'] = 20;
                    $std['id_specialist'] = 14;
                  }
                  $std['intake_leads'] = $intake_leads;
                  $std['handled_by'] = 85;
                  $std['id_session'] = 1;
                  $std['student_active'] = 1;
                  $std['id_student_status'] = 1;
                  $std['new_student'] = 1;

                  $check_student = $this->db->where('id_leads', $id_leads)
                                            ->where('student_id', $student_id)
                                            ->get('tb_student')
                                            ->row();
                  if ($check_student == null) {
                     $this->db->insert('tb_student', $std);
                     $id_student = $this->db->insert_id();
                  } else {
                    $this->db->where('id_student', $check_student->id_student)
                             ->update('tb_student', $std);
                     $id_student = $check_student->id_student;
                  }

                  echo $id_student.' '.$student_id.' '.$nik.' | '.$dob.' '.$firstname.' '.$lastname.'<br>';
            }
             
              
            }
          }
        }

    }


    public function coba_upload_nilai_1(){
      if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 2; $row <= $highestRow; $row++)
            {
              $data = array();
              $student_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $student_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
              $prodi = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
              $intake_year = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
              $score = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
              $class = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
              $feeder_subject = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
              $semester = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
              $ediis_subject = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
              $feeder_subject_code = $worksheet->getCellByColumnAndRow(10, $row)->getValue();

              $explode_year = explode(" ", $semester);
              $academic_year = $explode_year[0];
              $year = '';


              $check_subject_trimester = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                                                  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                                                  ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester')
                                                  ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
                                                  ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
                                                  ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year','left')
                                                  ->where('subject_name', $ediis_subject)
                                                  ->where('academic_year_name', $academic_year)
                                                  ->get('tb_main_class')
                                                  ->row();

              if ($check_subject_trimester == null) {
                 $trimester = 'hihi<br>';
              } else {
                 $trimester = $check_subject_trimester->trimester.'<br>';
              }

              if ($student_id != '') {

                $check_smt = $this->db->where('semester_name', $semester)
                                      ->get('tb_semester')->row()->id_semester;

                //echo $check_subject_trimester->id_academic_year;


                /*if ($check_semester == null) {
                  echo $no++.' - '.$check_smt.'<br>';
                } else {
                  echo 'hihihi<br>';
                }*/

                $check_tmt = $this->db->where('trimester', $check_subject_trimester->trimester)
                                      ->where('id_academic_year', $check_subject_trimester->id_academic_year)
                                      ->where('id_intended_program','7')
                                      ->get('tb_trimester')->row()->id_trimester;

                //echo $check_tmt;



                if ($check_subject_trimester == null) {
                  $id_trimester = 'Kosong';
                } else {
                  $id_trimester = $check_tmt;
                }

                if ($feeder_subject == $ediis_subject) {
                  $ediis_subjects = $ediis_subject;
                } else {
                  $ediis_subjects = '<span style="color:red;font-weight:bold">'.$ediis_subject.'</span>';
                }

                if ($prodi == 'Akuntansi') {
                  $id_course = '21';
                } else {
                  $id_course = '20';
                }

                $check_dcs = $this->db->join('tb_subject','tb_subject.id_subject = tb_detail_course_structure.id_subject')
                                      ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
                                      ->join('tb_course','tb_course.id_course = tb_course_structure.id_course')
                                      ->where('subject_name',$ediis_subject)
                                      ->where('tb_course_structure.id_course', $id_course)
                                      //->like('course_structure_name','2016')
                                      ->order_by('intake_start_year','desc')
                                      ->order_by('elective','asc')
                                      ->order_by('id_detail_course_structure','desc')
                                      ->get('tb_detail_course_structure')
                                      ->row();

                echo $check_dcs->id_detail_course_structure.' '.$id_course.' '.'<br>';

                $check_main_class = $this->db->select('*, tb_main_class.id_main_class')
                                             ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                                             ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                                             ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
                                             ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
                                             ->where('tb_course_structure.id_course', $id_course)
                                             ->where('tb_subject.subject_name', $ediis_subject)
                                             ->where('id_semester', $check_smt)
                                             ->where('main_class_name', $class)
                                             ->order_by('elective','asc')
                                             ->get('tb_main_class')
                                             ->row();


                if ($id_trimester == 'kosong') {
                  $id_trimester = 'a';
                } else {
                  $id_trimester = $id_trimester;
                }


                if ($check_main_class == null) {

                  $id_detail_course_structure = $check_dcs->id_detail_course_structure;
                  $id_subject = $check_dcs->id_subject;

                  $main_class = '| Empty |';


                  $data['id_campus'] = 1;
                  $data['id_detail_course_structure'] = $id_detail_course_structure;
                  $data['id_semester'] = $check_smt;
                  $data['id_trimester'] = $id_trimester;
                  $data['id_session'] = 1;
                  $data['main_class_name'] = $class;
                  $data['main_class_subject_name'] = $feeder_subject;
                  $data['main_class_subject_code'] = $feeder_subject_code;

                  $this->db->insert('tb_main_class', $data);

                  $id_main_class = $this->db->insert_id();

                  $data['main_class_join'] = $id_main_class;

                  $this->db->where('id_main_class', $id_main_class)
                           ->update('tb_main_class', $data);

                  $abc = 'Haha';

                } else {

                  $id_detail_course_structure = $check_main_class->id_detail_course_structure;
                  $id_subject = $check_main_class->id_subject;

                  $main_class = '|'.$check_main_class->id_main_class.'|';


                  //$data['id_trimester'] = $check_tmt;
                  $data['main_class_name'] = $class;
                  $data['main_class_subject_name'] = $feeder_subject;
                  $data['main_class_subject_code'] = $feeder_subject_code;

                  $this->db->where('tb_main_class.id_main_class', $check_main_class->id_main_class)
                           ->update('tb_main_class', $data);

                  $abc = 'hehe';

                  $id_main_class = $check_main_class->id_main_class;

                }
                

                echo $no++.''.$feeder_subject.' Subject Ediis : '.$ediis_subjects.' Trimester '.$academic_year.' '.$trimester.' - ('.$id_trimester.') - '.$main_class.' Kelas : '.$class.' ('.$check_dcs->id_detail_course_structure.') <br><br>';

                echo $id_course.'-'.$ediis_subject.'-'.$check_smt.'-'.$abc.'<br><br>';


                $student_id = trim($student_id);


                        $check_student = $this->db->join('tb_main_class','tb_main_class.id_main_class = tb_class_student.id_main_class')
                                                   ->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                                                   ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                                                    ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                                                   ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
                                                   ->where('student_id', $student_id)
                                                   ->where('main_class_name', $class)
                                                   ->where('tb_main_class.id_semester', $check_smt)
                                                   ->where('tb_subject.subject_name', $ediis_subject)
                                                   ->where('tb_course_structure.id_course', $id_course)
                                                   ->where('class_student_softdel',0)
                                                   ->get('tb_class_student')
                                                   ->row();

                if ($check_student == null) {

                  $student = $this->db->where('student_id', $student_id)
                                      ->get('tb_student')
                                      ->row();

                  if ($student == null) {
                      
                      echo 'No Student | '.$student_id.' - '.$student_name.' ('.$feeder_subject.')<br>';

                  } else {

                      $std['id_student'] = $student->id_student;
                      $std['id_main_class'] = $id_main_class;

                      if ($score != null) {
                         $std['final_score'] = $score;
                         $std['actual_score'] = $score;
                      }
                     
                      $std['score_used'] = 1;

                      $this->db->insert('tb_class_student', $std);

                      $id_class_student = $this->db->insert_id();

                      if($score != null){
                        $score_scale = $this->academic_master_model->get_score_scale($id_class_student, 7, $student->intake_leads, $score);
                      }

                      echo 'Available | '.$student_id.' - '.$student_name.' ('.$feeder_subject.')<br>';
                  }

                } else {

                    $id_class_student = $check_student->id_class_student;
                    $id_main_class = $check_student->id_main_class;

                    echo 'Student in Class | '.$id_class_student.' '.$student_id.' - '.$student_name.'  ('.$feeder_subject.')<br>';

                    
                }


                $fee['id_main_class'] = $id_main_class;
                $fee['nim'] = $student_id;
                $fee['nama_mahasiswa'] = $student_name;
                $fee['semester'] = $semester;
                $fee['matkul_feeder'] = $feeder_subject;
                $fee['kode_matkul_feeder'] = $feeder_subject_code;
                $fee['id_class_student'] = $id_class_student;
                $fee['id_detail_course_structure'] = $id_detail_course_structure;
                $fee['id_subject'] = $id_subject;
                $fee['kelas'] = $class;
                $fee['id_course'] = $id_course;
                if($score != null){
                  $fee['nilai'] = $score;
                }
                $fee['id_semester'] = $check_smt;
                $fee['jurusan'] = $prodi;
                $fee['angkatan'] = $intake_year;

                $check = $this->db->where('id_class_student', $id_class_student)
                                  ->get('tb_feeder_class_student')
                                  ->row();

                if ($check == null) {
                   $this->db->insert('tb_feeder_class_student', $fee);
                } else {
                   $this->db->where('id_feeder', $check->id_feeder)
                            ->update('tb_feeder_class_student', $fee);
                }
              }
            }
          }
        }
    }

    public function check_student_id(){

      $student_id = '202012121128';

      $student = $this->db->where('student_id', $student_id)
                          ->get('tb_student')
                          ->row();

      print_r($student);
    }

    public function remove_point_student_id(){
      $student = $this->db->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                          ->where('tb_student.id_campus','1')
                          //->where('id_student >=',2657)
                          //->where('id_student <=', 3047)
                          ->like('student_id','?')
                          ->get('tb_student')
                          ->result();

      foreach ($student as $key) {

        //$new_id =  ltrim(str_replace(' ', '', $key->student_id));

         //$kar = 'a'.$new_id;

        //$abc = str_replace('.', '', $key->student_id);

         $abc = str_replace('?', '', $key->student_id);

         /*$abc = substr($key->student_id ,0,1);*/

         /*$abc = $key->student_id;
         $abc = substr($abc,1);*/


         $std['student_id'] = $abc;

        $this->db->where('id_student', $key->id_student)
                  ->update('tb_student', $std);

         echo $abc.' '.$key->name.''.$key->family_name.' '.'<br>';
      }
    }

    public function check_main_class_join(){
      $zero = $this->db->where('main_class_join','')
                       ->get('tb_main_class')
                       ->result();

      foreach ($zero as $key) {
         $main['main_class_join'] = $key->id_main_class;

         $this->db->where('id_main_class', $key->id_main_class)
                  ->update('tb_main_class', $main);
      }
    }

    public function check_main_class(){

      $id_course = 21;
      $ediis_subject = 'KEPEMIMPINAN DAN TIM KERJA';
      $id_semester = 16;
      $class = '01';

      $check_main_class = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                                             ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                                             ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
                                             ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
                                             ->where('tb_course_structure.id_course', $id_course)
                                             ->where('subject_name', $ediis_subject)
                                             ->where('id_semester', $id_semester)
                                             ->where('main_class_name', $class)
                                             ->get('tb_main_class')
                                             ->row();
      print_r($check_main_class);
    }

    public function check_specialist(){
      $id_main_class = 2942;
      $specialist = $this->academic_master_model->get_class_specialist_by_main_class($id_main_class);

      if ($specialist == '') {
        echo 'haha';
      } else {
        echo 'hihi';
      }

      print_r($specialist);
    }


    public function check_duplicate_student_id(){

      $query = $this->db->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                        ->where('student_id !=','')
                        ->where('tb_student.id_campus','1')
                        ->order_by('student_id','asc')
                        ->get('tb_student')
                        ->result();

      foreach ($query as $key) {

        $check = $this->db->where('student_id', $key->student_id)
                          ->get('tb_student')
                          ->num_rows();
        if ($key->student_acreditation == '1') {
          $abc = '';
        } else {
          $abc = 'Hahaha';
        }

        if ($check > 1) {

          $dob = $this->db->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                          ->where('student_id', $key->student_id)
                          ->where('dob !=','0000-00-00')
                          ->get('tb_student')
                          ->row()->dob;

          $data['dob'] = $dob;

          $this->db->where('id_leads', $key->id_leads)
                   ->update('tb_leads', $data);

          echo $key->name.'  '.$key->family_name.' '.$key->student_id.' '.$abc.' - '.$key->nik.'  - '.$key->dob.'<br>';

          if ($key->nik == '') {

              $this->db->where('id_student', $key->id_student)
                       ->delete('tb_student');

              $this->db->where('id_leads', $key->id_leads)
                       ->delete('tb_leads');
          }

        } else {
          
        }
      }
    }


    public function check_double_class(){
       if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 2; $row <= $highestRow; $row++)
            {
              $data = array();

              $no_feeder = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
              $prodi = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
              $class_name = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
              $feeder_subject = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

              if ($no_feeder == '1') {
                echo $no.' - '.$prodi.' '.$class_name.' '.$feeder_subject.'<br>';
                $no++;
              } else {

              }
            }

            //echo $no;
          }
        }
    }


     public function check_khs_score(){
       if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 3; $row <= $highestRow; $row++)
            {
              $data = array();

              $student_id = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $student_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
              $prodi = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
              $feeder_subject_code = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
              $feeder_subject_name = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
              $score = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
              $semester = $worksheet->getCellByColumnAndRow(10, $row)->getValue();

              $id = substr($student_id, 2);

              if ($score != null) {
               

                $check = $this->db->join('tb_class_student','tb_class_student.id_class_student = tb_feeder_class_student.id_class_student')
                                  ->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                                  ->where('semester', $semester)
                                  ->where('matkul_feeder', $feeder_subject_name)
                                  ->where('nim', $id)
                                  ->where('class_student_hide','0')
                                  ->get('tb_feeder_class_student')
                                  ->row();

                if ($check == null) {
                  $fcs = 'Kosong';
                } else {
                  $fcs = $check->id_class_student;

                  if ($check->final_score == '0') {
                     echo $no++.' <span style="background-color:yellow">'.$id.'</span> - '.$student_name.' - '.$feeder_subject_name.' '.$feeder_subject_code.' | '.$fcs.'<br>';

                     $std['final_score'] = $score;
                     $std['actual_score'] = $score;

                     $this->db->where('id_class_student', $check->id_class_student)
                              ->update('tb_class_student', $std);

                     if($score != null){
                          $score_scale = $this->academic_master_model->get_score_scale($check->id_class_student, 7, $check->intake_leads, $score);
                      }
                  }

                }

              }
            
            }

            //echo $no;
          }
        }
    }

    public function component_score_stie(){
      $main_class = $this->db->select('*')
                             ->join('tb_main_class','tb_main_class.id_main_class = tb_class_student.id_main_class')
                             ->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                             ->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                             ->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                             ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure = tb_main_class.id_detail_course_structure')
                             ->join('tb_subject','tb_subject.id_subject = tb_detail_course_structure.id_subject')
                             ->join('tb_course_structure','tb_course_structure.id_course_structure = tb_detail_course_structure.id_course_structure')
                             ->join('tb_course','tb_course.id_course = tb_course_structure.id_course')
                             ->where('semester_name', '2017/2018 Genap')
                             ->where('class_student_hide','0')
                             ->where('class_student_softdel','0')
                             ->get('tb_class_student')
                             ->result();

      foreach ($main_class as $key) {

        $score = $this->db->where('id_main_class', $key->id_main_class)
                          ->get('tb_class_score')
                          ->result();

        foreach ($score as $sc) {
           $check = $this->db->where('id_class_score', $sc->id_class_score)
                             ->where('id_class_student', $key->id_class_student)
                             ->get('tb_class_student_score')
                             ->row();

           if ($check == null) {
                $score = 'Kosong';

                if ($sc->id_score == '1') {
                  $tambahan = 12;
                } elseif ($sc->id_score == '47') {
                  $tambahan = -3;
                } elseif ($sc->id_score == '48') {
                  $tambahan = -1;
                } elseif ($sc->id_score == '49') {
                  $tambahan = -9;
                } elseif ($sc->id_score == '50') {
                  $tambahan = 1;
                } else {

                }

                $score = $check->final_score;
                $divided_score = $score * $check->score_percentage / 100;

           } else {
                $score = $check->student_score;
           }

           echo $score.' '.$key->name.' '.$key->family_name.' '.$key->course.' '.$key->subject_name.' '.$key->main_class_name.' <br>';
        }
      }
    }

    public function test_duplicate(){

            $id_leads = 128;

            $leads = $this->leads_model->get_detail_leads($id_leads);

            $name_explode = explode(" ",$leads->name);

            foreach ($name_explode as $key) {
                $check = $this->db->like('name', $key)
                              ->where('email', $leads->email)
                              ->where('id_leads !=', $id_leads)
                              ->get('tb_leads')
                              ->result();

                foreach ($check as $key) {
                    $duplicate_column = 2;
                    $duplicate_notes = 'Name, Email';

                  echo $key;
                } 
            }
        
    }

    public function generate_score(){
        $class_student = $this->db
                             ->join('tb_main_class','tb_main_class.id_main_class = tb_class_student.id_main_class')
                             ->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                             ->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                             ->join('tb_leads','tb_leads.id_leads = tb_student.id_leads')
                             ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure = tb_main_class.id_detail_course_structure')
                             ->join('tb_subject','tb_subject.id_subject = tb_detail_course_structure.id_subject')
                             ->join('tb_course_structure','tb_course_structure.id_course_structure = tb_detail_course_structure.id_course_structure')
                             ->join('tb_course','tb_course.id_course = tb_course_structure.id_course')
                             ->where('semester_name', '2017/2018 Genap')
                             ->where('class_student_hide','0')
                             ->where('class_student_softdel','0')
                             ->where('process','0')
                             ->limit(1000)
                             ->get('tb_class_student')
                             ->result();

        foreach ($class_student as $key) {

            $score = $key->final_score;
            $a = array("100","92","86");
            shuffle($a);
            $hasil = array_shift($a);
            $attendance_score =  $hasil;

            $final_score = $score;

            $tugas = round(((($score * 4) - $attendance_score) / 3));
            if ($tugas > 100) {
              $tugas = 100;
            } else {
              $tugas = $tugas;
            }

            $paper = $tugas;
            $uts = round($tugas - 5);
            if ($uts > 100) {
              $uts = 100;
            } else {
              $uts = $uts;
            }

            $uas = round($tugas + 5);
            if ($uas > 100) {
              $uas = 100;
            } else {
              $uas = $uas;
            }

            $check_attendance = $this->db->join('tb_class_score','tb_class_score.id_class_score = tb_class_student_score.id_class_score')
                                      ->where('id_class_student', $key->id_class_student)
                                      ->where('id_score','47')
                                      ->get('tb_class_student_score')
                                      ->row();


           /*                           ->row();
            $check_tugas = $this->db->join('tb_class_score','tb_class_score.id_class_score = tb_class_student_score.id_class_score')
                                      ->where('id_class_student', $key->id_class_student)
                                      ->where('id_score','47')
                                      ->get('tb_class_student_score')
                                      ->row();
            $check_paper = $this->db->join('tb_class_score','tb_class_score.id_class_score = tb_class_student_score.id_class_score')
                                      ->where('id_class_student', $key->id_class_student)
                                      ->where('id_score','48')
                                      ->get('tb_class_student_score')
                                      ->row();
            $check_uts = $this->db->join('tb_class_score','tb_class_score.id_class_score = tb_class_student_score.id_class_score')
                                      ->where('id_class_student', $key->id_class_student)
                                      ->where('id_score','49')
                                      ->get('tb_class_student_score')
                                      ->row();
            $check_uas = $this->db->join('tb_class_score','tb_class_score.id_class_score = tb_class_student_score.id_class_score')
                                      ->where('id_class_student', $key->id_class_student)
                                      ->where('id_score','50')
                                      ->get('tb_class_student_score')
                                      ->row();*/

            if ($check_attendance == null) {

              $class_score = $this->db->where('id_main_class', $key->id_main_class)
                                      ->get('tb_class_score')
                                      ->result();

              foreach ($class_score as $cs) {

                if ($cs->id_score == '1') {
                  $score = $attendance_score;
                } elseif ($cs->id_score == '47') {
                  $score = $tugas;
                } elseif ($cs->id_score == '48') {
                  $score = $paper;
                } elseif ($cs->id_score == '49') {
                  $score = $uts;
                } elseif ($cs->id_score == '50') {
                  $score = $uas;
                } else {

                }

                $data_at['id_class_score'] = $cs->id_class_score;
                $data_at['id_class_student'] = $key->id_class_student;
                $data_at['student_score'] = $score;
                $data_at['student_point'] = $score;
                $data_at['student_score_last_updated'] = date('Y-m-d H:i:s');
                $data_at['student_score_updated_by'] = 69;
                $data_at['generated_score'] = 1;

                $this->db->insert('tb_class_student_score', $data_at);

                $hmm['process'] = 1;

                $this->db->where('id_class_student', $key->id_class_student)
                         ->update('tb_class_student', $hmm);

                echo $key->name.' '.$key->family_name.' '.$key->id_class_student.' '.$score.' '.$key->id_main_class.'<br>';
              } 

              
            } else {
               $hmm2['process'] = 1;

               $this->db->where('id_class_student', $key->id_class_student)
                        ->update('tb_class_student', $hmm2);

               echo 'haha';
            }

            echo 'hahaha';
        }

        
    }

    public function student_hantu_0(){
      $student = $this->db->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                          ->where('final_score','0')
                          ->where('student_acreditation','1')
                          ->like('student_id','1111')
                          ->or_where('final_score','0')
                          ->where('student_acreditation','1')
                          ->like('student_id','1112')
                          ->get('tb_class_student')
                          ->result();
      foreach ($student as $key) {
         echo $key->student_id.'<br>';
      }
    }


    public function upload_dosen(){
       if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 2; $row <= $highestRow; $row++)
            {
              $data = array();

              $employee_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
              $nidn = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
              $gender = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
              $religion = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
              $status = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
              $dob = $worksheet->getCellByColumnAndRow(7, $row)->getValue();

              if ($gender == 'Laki - Laki') {
                $id_gender = 1;
              } else {
                $id_gender = 2;
              }

              echo $dob.'<br>';

              $dob_explode = explode('/', $dob);

              $date = $dob_explode[0];
              $month = $dob_explode[1];
              $year = $dob_explode[2];

              //echo $dob = $year.'-'.$month.'-'.$date;

              $check = $this->db->where('employee_name', $employee_name)
                                ->get('db_hr.tb_employee')
                                ->row();

              $data['employee_name'] = $employee_name;
              $data['employee_birthdate'] = $dob;
              $data['id_gender'] = $id_gender;
              $data['id_religion'] = $religion;
              $data['id_employee_status'] = 1;
              $data['lecturer'] = 1;
              $data['campus_based'] = 1;
              $data['id_account'] = 1;

              if ($check == null) {
                
                  $this->db->insert('db_hr.tb_employee', $data);

                  $id_employee = $this->db->insert_id();

                  $teach['nidn'] = $nidn;
                  $teach['id_employee'] = $id_employee;

                  $this->db->insert('tb_teacher', $teach);

              } else {

                  $this->db->where('id_employee', $check->id_employee)
                           ->update('db_hr.tb_employee', $data);

                  $check_teach = $this->db->where('id_employee', $check->id_employee)
                                          ->get('tb_teacher')
                                          ->row();

                  $teach['nidn'] = $nidn;
                  $teach['id_employee'] = $check->id_employee;

                  if ($check_teach == null) {
                      $this->db->insert('tb_teacher', $teach);
                  } else {
                      $this->db->where('id_employee', $check->id_employee)
                               ->insert('tb_teacher', $teach);
                  }
              }


            }

            //echo $no;
          }
        }
    }

    public function upload_teacher_in_class(){
      if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 2; $row <= $highestRow; $row++)
            {
              
              $semester = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
              $subject_name = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
              $class_name = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
              $teacher = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
              $prodi = $worksheet->getCellByColumnAndRow(10, $row)->getValue();


              if ($semester != '2021/2022 Ganjil') {

              //echo $teacher;

              $check_teacher = $this->db->where('employee_name', $teacher)
                                        ->get('db_hr.tb_employee')
                                        ->row();
              if ($check_teacher == null) {
                 echo 'No Teacher';
              } else {
                 echo $check_teacher->id_employee;
              }

              if ($prodi == 'Management') {
                $id_course = '20';
              } else {
                $id_course = '21';
              }

              $class = '0'.$class_name;

              
            


                $check_class = $this->db->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                                        ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure = tb_main_class.id_detail_course_structure')
                                        ->join('tb_course_structure','tb_course_structure.id_course_structure = tb_detail_course_structure.id_course_structure')
                                        ->where('main_class_name', $class)
                                        ->where('semester_name', $semester)
                                        ->where('main_class_subject_name', $subject_name)
                                        ->where('tb_course_structure.id_course', $id_course)
                                        ->get('tb_main_class')
                                        ->result();

                foreach ($check_class as $key) {

                    if ($check_class == null) {
                      //echo ' No Class '.$subject_name.' '.$class.' '.$semester.' '.$prodi.' - '.$id_course.''.$teacher;
                    } else {
                      //echo ' '.$check_class->id_main_class;


                      $teach['teacher_in_charge'] = $check_teacher->id_employee;

                      $this->db->where('id_main_class', $key->id_main_class)
                               ->update('tb_main_class', $teach);
                    }

                    echo '<br>';
                }

                

              }
            
            }

            //echo $no;
          }
        }
    }

    public function main_class_name(){
      $main = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                       ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                       ->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                       ->where('main_class_subject_name','')
                       ->where('semester_name','2021/2022 Ganjil')
                       ->get('tb_main_class')
                       ->result();
      foreach ($main as $key) {
         $data['main_class_subject_name'] = $key->subject_name;
         $data['main_class_subject_code'] = $key->subject_code;

         $this->db->where('id_main_class', $key->id_main_class)
                  ->update('tb_main_class', $data);

      }

      echo 'sukses';
    }

    public function upload_mhs_2021(){
      if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 0; $row <= $highestRow; $row++)
            {
              $data = array();

              $student_name = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
              $nim1 = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $nim2 = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
              $status = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
              $id_course = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
              $id_specialist = $worksheet->getCellByColumnAndRow(6, $row)->getValue();

              //echo $status;

              if ($status == '') {

                $leads = $this->db->like('CONCAT(name," ",family_name)', $student_name)
                                ->get('tb_leads')
                                ->row();

                $double = $this->db->like('CONCAT(name," ",family_name)', $student_name)
                                ->get('tb_leads')
                                ->num_rows();

                if ($double > 1) {
                  $double = '2';
                } else {
                  $double = '1';
                }

                $nim = $nim1.''.$nim2;
                $new_nim = str_replace('.', '', $nim);

                echo $new_nim.' | '.$id_course.' | ';


                if ($leads == null) {

                  echo '<b style="color:red">tidak ada</b>'.' | '.$student_name;
                } else {

                  if ($double == '1') {
                      echo $leads->id_leads.' '.$student_name.' | '.$leads->name.' '.$leads->family_name.' '.$double;

                      $data['id_leads'] = $leads->id_leads;
                      $data['id_course'] = $id_course;
                      $data['id_specialist'] = $id_specialist;
                      $data['id_campus'] = 1;
                      $data['id_program'] = 1;
                      $data['id_intended_program'] = 7;
                      $data['student_active'] = 1;
                      $data['student_acreditation'] = 1;
                      $data['intake_leads'] = '2021-09-01';
                      $data['handled_by'] = 69;
                      $data['new_student'] = 1;
                      $data['student_id'] = $new_nim;

                      $check = $this->db->where('id_leads', $leads->id_leads)
                                        ->where('id_campus','1')
                                        ->get('tb_student')
                                        ->row();
                      if ($check == null) {
                        $this->db->insert('tb_student', $data);
                      } else {

                      }
                  } else {
                    echo '<b style="color:red">double</b>'.' | '.$student_name;
                  }

                  
                }

                echo '<br>';

              }
            }

            //echo $no;
          }
        }
    }

    public function student_status(){
      $leads = $this->db->join('tb_status','tb_status.id_status = tb_leads.id_status')
                        ->where('tb_leads.id_status','4')
                        ->get('tb_leads')
                        ->result();

      foreach ($leads as $key) {
          $enroll = $this->db->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                             ->join('tb_main_class','tb_main_class.id_main_class = tb_class_student.id_main_class')
                             ->join('tb_trimester','tb_trimester.id_trimester = tb_main_class.id_trimester')
                             ->where('tb_student.id_leads', $key->id_leads)
                             ->where('class_student_softdel',0)
                             ->get('tb_class_student')
                             ->row();

          if ($enroll != null) {
             $status['id_status'] = 5;
          } else {
             $status['id_status'] = $key->id_status;
          }


          echo $key->id_leads.' '.$key->name.' '.$key->family_name.'  '.$status['id_status'];

          echo '<br>';


          $this->db->where('id_leads', $key->id_leads)
                   ->update('tb_leads', $status);
      }
    }

    public function upload_krs(){
      if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 3; $row <= $highestRow; $row++)
            {
              $data = array();

              $no_ex = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
              $nim = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $student_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
              $id_main_class = $worksheet->getCellByColumnAndRow(3, $row)->getValue();


              /*$nim = str_replace('?','',$nim);*/

              if($no_ex > 3180){
                $nim = $nim;
              } else {
                $nim = substr($nim, 2);
              }

              echo $no++.' ';

              if ($nim == '202024111473') {
                echo 'aaaa';
                $nim = '202024111456';
              } elseif ($nim == '202024111474') {
                echo 'bbbb';
                $nim = '202024111457';
              } elseif ($nim == '202024111475') {
                echo 'cccc';
                $nim = '202024111458';
              } else {
                echo 'dddd';
                $nim = $nim;
              }


              if ($student_name != 'Alexandra Anya Ivkovic') {
                
                  $check_student = $this->db->where('student_id', $nim)
                                            ->get('tb_student')
                                            ->row();

                  if ($check_student == null) {
                     echo 'tidak ada | ';
                  } else {
                     echo $check_student->id_leads;
                  }

                  echo $student_name.' | '.$id_main_class;


                  $check_cs = $this->db->where('id_student', $check_student->id_student)
                                       ->where('id_main_class', $id_main_class)
                                       ->get('tb_class_student')
                                       ->row();

                  if ($check_cs == null) {
                    $data['id_student'] = $check_student->id_student;
                    $data['id_main_class'] = $id_main_class;
                    $data['class_student_last_updated'] = date('Y-m-d H:i:s');
                    $data['class_student_updated_by'] = 69;

                    $this->db->insert('tb_class_student', $data);
                  } else {

                  }

                  echo '<br>';
              }

              

              
            }

            //echo $no;
          }
        }
    }

    public function main_class_group(){
      $mcg = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                      ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                      ->join('tb_semester','tb_semester.id_semester=tb_main_class.id_semester','left')
                      ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
                      ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
                      ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_semester.id_academic_year','left')
                      ->join('tb_campus','tb_campus.id_campus=tb_main_class.id_campus','left')
                      ->join('db_hr.tb_employee as teacher_in_charge','teacher_in_charge.id_employee = tb_main_class.teacher_in_charge','left')
                      ->group_by('tb_main_class.main_class_name')
                      ->group_by('tb_course.id_course')
                      ->group_by('tb_subject.id_subject')
                      ->group_by('tb_semester.id_semester')
                      ->where('tb_main_class.id_campus','1')
                      ->where('tb_main_class.main_class_subject_name !=','')
                      ->where('tb_semester.semester_name','2017/2018 Ganjil')
                      ->get('tb_main_class')
                      ->result();

      $no = 1;
      foreach ($mcg as $key) {
        
        echo $no.' | ';

        echo $key->id_main_class;

        echo '<br>';
        $no++;
      }
    }

    public function student_acreditation(){
      if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 3; $row <= $highestRow; $row++)
            {

                $data = array();

              $no_ex = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
              $nim = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $student_name = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
              $id_main_class = $worksheet->getCellByColumnAndRow(3, $row)->getValue();


              /*$nim = str_replace('?','',$nim);*/

              if($no_ex > 3180){
                $nim = $nim;
              } else {
                $nim = substr($nim, 2);
              }

              echo $no++.' ';

              if ($nim == '202024111473') {
                echo 'aaaa';
                $nim = '202024111456';
              } elseif ($nim == '202024111474') {
                echo 'bbbb';
                $nim = '202024111457';
              } elseif ($nim == '202024111475') {
                echo 'cccc';
                $nim = '202024111458';
              } else {
                echo 'dddd';
                $nim = $nim;
              }

              $student['student_acreditation'] = 1;


              $check = $this->db->where('student_id', $nim)
                                ->update('tb_student', $student);

                echo '<br>';
              
            }

        }


        }
    }


    public function class_lecture_acreditation(){
        $cs = $this->db->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                       ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                      ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                       ->where('semester_name','2021/2022 Ganjil')
                       ->get('tb_main_class')
                       ->result();

        foreach ($cs as $key) {
               
                $cl['main_class_join'] = $key->main_class_join;
                $cl['id_employee'] = $key->teacher_in_charge;
                $cl['class_lecture_type'] = 'L';
                $cl['split'] = 'A';
                $cl['class_lecture_hours'] = $key->subject_duration;
                $cl['id_main_class'] = $key->id_main_class;
                $cl['meeting_total'] = 14;
                $cl['class_lecture_acreditation'] = 1;
                $cl['class_lecture_claim_form'] = 0;

                if ($key->main_class_name == '02') {
                    $time = '18:15:00';
                } else {
                    $time = '08:30:00';
                }


                $total_student = $this->db->where('class_student_softdel','0')
                                          ->where('class_student_hide','0')
                                          ->where('id_main_class', $key->id_main_class)
                                          ->get('tb_class_student')
                                          ->num_rows();

                if ($total_student > 50) {
                    $total_student = 50;
                } else {
                    $total_student = $total_student;
                }

                $room = $this->db->where('room_capacity >=', $total_student)
                                 ->where('id_campus','1')
                                 ->where('classroom','1')
                                 ->order_by('id_room','random')
                                 ->get('tb_room')
                                 ->row();

                $day = $this->random_day($room->id_room, $key->teacher_in_charge, $key->id_semester, $time);

                if ($day != '') {
                    $cl['day'] = $day;
                    $cl['id_room'] = $room->id_room;
                    $cl['start'] = $time;


                    $check = $this->db->where('id_main_class', $key->id_main_class)
                                      ->where('class_lecture_acreditation','1')
                                      ->get('tb_class_lecture')
                                      ->row();

                    if ($check == null) {
                        $this->db->insert('tb_class_lecture', $cl);
                    } else {

                        $this->db->where('id_class_lecture', $check->id_class_lecture)
                                 ->update('tb_class_lecture', $cl);
                    }
                }

        }

        echo 'sukses';
    }

    

    public function random_day($id_room, $teacher_in_charge, $id_semester, $time){



        $day = $this->db->where('id_day >=',1)
                            ->where('id_day <=',5)
                            ->order_by('id_day','random')
                            ->get('tb_day')
                            ->row()->id_day;

        $check_schedule = $this->db->join('tb_main_class','tb_main_class.id_main_class = tb_class_lecture.id_main_class')
                                    ->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                                    ->where('tb_main_class.id_semester', $id_semester)
                                        ->where('id_employee', $teacher_in_charge)
                                       //->where('id_room', $room->id_room)
                                       ->where('start', $time)
                                       //->where('day', $day)
                                       ->where('class_lecture_acreditation','1')
                                       ->group_by('tb_class_lecture.day')
                                       ->get('tb_class_lecture')
                                       ->result();

        $row = '';
        foreach ($course as $key) {
            $sp = $key->id_day;
            $row[] = $sp;
        }

        $row;

        $check = $this->db->join('tb_main_class','tb_main_class.id_main_class = tb_class_lecture.id_main_class')
                                    ->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                                    ->where('tb_main_class.id_semester', $id_semester)
                                        ->where('id_employee', $teacher_in_charge)
                                       //->where('id_room', $room->id_room)
                                       ->where('start', $time)
                                       ->where_not_in('day')
                                       //->where('day', $day)
                                       ->where('class_lecture_acreditation','1')
                                       ->group_by('tb_class_lecture.day')
                                       ->get('tb_class_lecture')
                                       ->row();

        if ($check_schedule == null) {
            return $day;
        } else {
            //$this->random_day($id_room, $teacher_in_charge, $id_semester, $time);
            return '';
        }
    }

    public function delete_class(){
        $dl = $this->db->join('tb_main_class','tb_main_class.id_main_class = tb_class_lecture.id_main_class')
                       ->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                       ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                      ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                       ->where('semester_name','2021/2022 Ganjil')
                       ->where('class_lecture_acreditation','0')
                       ->where('start','00:00:00')
                       ->where('id_room','')
                       ->get('tb_class_lecture')
                       ->result();

        foreach ($dl as $key) {
            $this->db->where('id_class_lecture', $key->id_class_lecture)
                     ->delete('tb_class_lecture');
        }
    }

    public function generate_attendance(){
        $cl = $this->db->join('tb_main_class','tb_main_class.id_main_class = tb_class_lecture.id_main_class')
                       ->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                       ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                      ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                       ->where('semester_name','2021/2022 Ganjil')
                       ->where('class_lecture_acreditation','1')
                       ->get('tb_class_lecture')
                       ->result();

        foreach ($cl as $key) {
            $total_meeting = $key->meeting_total;

            for ($i=1; $i <= $total_meeting ; $i++) { 

                if ($i <= 9) {
                    $date = '0'.$i;
                } else {    
                    $date = $i;
                }

                $attendance_date = '2022-01-'.$date;

                $att['attendance_date'] = $attendance_date;
                $att['id_employee'] = $key->teacher_in_charge;
                $att['id_class_lecture'] = $key->id_class_lecture;
                $att['attendance_type'] = 'Regular';
                $att['attendance_hours'] = $key->class_lecture_hours;
                $att['visit'] = 1;
                $att['main_class_join'] = $key->id_main_class;
                $att['class_attendance_claim_form'] = 0;

                $check = $this->db->where('id_class_lecture', $key->id_class_lecture)
                                  ->where('attendance_date', $attendance_date)
                                  ->get('tb_class_attendance')
                                  ->row();

                echo $attendance_date;

                echo '<br>';
                if ($check == null) {
                    $this->db->insert('tb_class_attendance', $att);
                }
            }
        }
    }

    public function add_student_to_schedule(){
        $class_student = $this->db->join('tb_main_class','tb_main_class.id_main_class = tb_class_student.id_main_class')
                                  ->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                                  ->where('semester_name','2021/2022 Ganjil')
                                  //->where('tb_main_class.id_main_class','1960')
                                  ->get('tb_class_student')
                                  ->result();

        foreach ($class_student as $key) {

            $check = $this->db->where('class_lecture_acreditation','1')
                              ->where('id_main_class', $key->id_main_class)
                              ->get('tb_class_lecture')
                              ->row();

            $att['id_class_lecture'] = $check->id_class_lecture;
            $att['id_class_student'] = $key->id_class_student;

            $check_cs = $this->db->where('id_class_lecture', $check->id_class_lecture)
                                 ->where('id_class_student', $key->id_class_student)
                                 ->get('tb_schedule_student')
                                ->row();

            if ($check_cs == null) {
                $this->db->insert('tb_schedule_student', $att);
            }
        }
    }

    public function delete_schedule_acreditation(){
        $abc = $this->db->where('class_lecture_acreditation','1')
                        ->get('tb_class_lecture')
                        ->result();
        foreach ($abc as $key) {
            $this->db->where('id_class_lecture', $key->id_class_lecture)
                     ->delete('tb_schedule_student');
        }
    }

    public function generate_score_stie_acreditation(){

        $main_class = $this->db->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                               ->where('semester_name','2021/2022 Ganjil')
                               ->where('id_main_class >=', 3999)
                               ->where('id_main_class <=', 4083)
                               ->where('id_campus','1')
                               ->get('tb_main_class')
                               ->result();

        foreach ($main_class as $key) {
            $id_main_class = $key->id_main_class;
            $range_min = '68';
            $range_max = '94';
            $attendance_range_min = '80';
            $attendance_range_max = '100';

            $this->master_model->generate_score($id_main_class, $range_min, $range_max, $attendance_range_min, $attendance_range_max);
        }

        echo 'sukses';
    }

    public function haha(){
            $id_main_class = $key->id_main_class;
            $range_min = '70';
            $range_max = '93';
            $attendance_range_min = '80';
            $attendance_range_max = '100';

            $this->master_model->generate_score($id_main_class, $range_min, $range_max, $attendance_range_min, $attendance_range_max);
    }

    public function test_schedule(){

        $schedule = $this->db->join('tb_class_lecture','tb_class_lecture.id_class_lecture=tb_schedule_student.id_class_lecture')
                        ->join('tb_class_student','tb_class_student.id_class_student = tb_schedule_student.id_class_student')
                        ->where('tb_schedule_student.id_class_student', $id_class_student)
                        ->where('tb_class_lecture.main_class_join', $class->main_class_join)
                        ->where('tb_class_student.class_student_softdel','0');
        if ($acreditation == 1) {
            $schedule = $this->db->where('class_lecture_acreditation','1');
        }
        $schedule = $this->db->group_by('tb_class_lecture.id_class_lecture')
                        ->get('tb_schedule_student')
                        ->row();
    }

    public function upload_mhs_inter(){
      if(isset($_FILES["file"]["name"])){
          $path = $_FILES["file"]["tmp_name"];
          $object = PHPExcel_IOFactory::load($path);


          foreach ($object->getWorksheetIterator() as $worksheet) {
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            $no = 1;

            for($row = 4; $row <= $highestRow; $row++)
            {
              $data = array();

              $no = $worksheet->getCellByColumnAndRow(0, $row)->getValue();
              $type = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
              $nim = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
              $student_name = $worksheet->getCellByColumnAndRow(6, $row)->getValue();

              if ($type == 'inter') {
                 

                 /* if ($no < 495) {
                      $nim = substr($nim, 2);
                      echo 'haha';
                  } else {
                      $nim = $nim;
                      echo 'hehe';
                  }*/

                  if ($nim == '202024111473') {
                    //echo 'aaaa';
                    $nim = '202024111456';
                  } elseif ($nim == '202024111474') {
                    //echo 'bbbb';
                    $nim = '202024111457';
                  } elseif ($nim == '202024111475') {
                    //echo 'cccc';
                    $nim = '202024111458';
                  } else {
                    //echo 'dddd';
                    $nim = $nim;
                  }

                  $check = $this->db->where('student_id', $nim)
                                    ->get('tb_student')
                                    ->row();

                 echo $nim;

                 if ($check == null) {
                     echo 'No Student '.$student_name;
                 } else {
                     echo $check->id_student;

                     $data['student_acreditation'] = 1;

                     $this->db->where('id_student', $check->id_student)
                              ->update('tb_student', $data);
                 }

                 echo '<br>';
            }
              

              
            }

            //echo $no;
          }
        }
    }

    public function check_score_inter(){
        $total = $this->db->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                          ->where('id_main_class','3994')
                          ->where('student_acreditation','1')
                          ->where('class_student_softdel','0')
                          ->where('class_student_hide','0')
                          ->where('final_score','0')
                          ->where('id_campus','1')
                          ->get('tb_class_student')
                          ->result();
        $no = 1;
        foreach ($total as $key) {
            echo $no++;

            //echo $key->id_class_student;



            $range_min = 80;
            $range_max = 91;

            $score = $this->master_model->random_score($range_min, $range_max);

            $score_scale = $this->academic_master_model->get_score_scale($key->id_class_student, 7, $key->intake_leads, $score);

            $data['final_score'] = $score;
            $data['actual_score'] = $score;
            $data['process'] = 1;
            $data['cs_generated_score'] = 1;

            $this->db->where('id_class_student',$key->id_class_student)
                     ->update('tb_class_student',$data);

            echo '<br>';
        }

        echo 'sukses';
    }

    public function check_sco(){
       echo  $total = $this->db->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                          //->where('id_main_class','3956')
                          ->where('student_acreditation','1')
                          ->where('class_student_softdel','0')
                          ->where('class_student_hide','0')
                          //->where('final_score','0')
                          ->where('cs_generated_score','1')
                          ->where('id_campus','1')
                          ->get('tb_class_student')
                          ->num_rows();
    }


    public function generate_score_cs(){
        $class_student = $this->db->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                             ->where('id_main_class','3994')
                             ->where('student_acreditation','1')
                             ->where('class_student_softdel','0')
                             ->where('class_student_hide','0')
                             //->where('cs_generated_score','0')
                             ->where('id_campus','1')
                             //->where('process','0')
                             ->limit(10000)
                             ->get('tb_class_student')
                             ->result();

        $no_student = 1;
        foreach ($class_student as $key) {

            $score = $key->final_score;
            $a = array("100","92","86");
            shuffle($a);
            $hasil = array_shift($a);
            $attendance_score =  $hasil;

            $final_score = $score;

            $tugas = round(((($score * 4) - $attendance_score) / 3));
            if ($tugas > 100) {
              $tugas = 100;
            } else {
              $tugas = $tugas;
            }

            $paper = $tugas;
            $uts = round($tugas - 5);
            if ($uts > 100) {
              $uts = 100;
            } else {
              $uts = $uts;
            }

            $uas = round($tugas + 5);
            if ($uas > 100) {
              $uas = 100;
            } else {
              $uas = $uas;
            }


              $class_score = $this->db->where('id_main_class', $key->id_main_class)
                                      ->get('tb_class_score')
                                      ->result();

              foreach ($class_score as $cs) {

                if ($cs->id_score == '1') {
                  $score = $attendance_score;
                } elseif ($cs->id_score == '47') {
                  $score = $tugas;
                } elseif ($cs->id_score == '48') {
                  $score = $paper;
                } elseif ($cs->id_score == '49') {
                  $score = $uts;
                } elseif ($cs->id_score == '50') {
                  $score = $uas;
                } else {

                }

                $data_at['id_class_score'] = $cs->id_class_score;
                $data_at['id_class_student'] = $key->id_class_student;
                $data_at['student_score'] = $score;
                $data_at['student_point'] = $score;
                $data_at['student_score_last_updated'] = date('Y-m-d H:i:s');
                $data_at['student_score_updated_by'] = 69;
                $data_at['generated_score'] = 1;

                $checks = $this->db->where('id_class_student', $key->id_class_student)
                                  ->where('id_class_score', $cs->id_class_score)
                                  ->get('tb_class_student_score')
                                  ->row();

                if ($checks == null) {
                   $this->db->insert('tb_class_student_score', $data_at);
                } else {
                   /*$this->db->where('id_student_score',$checks->id_student_score)
                            ->update('tb_class_student_score', $data_at);*/
                }

              } 

              $css['process'] = 1;

              $this->db->where('id_class_student',$key->id_class_student)
                       ->update('tb_class_student', $css);
              

              echo $no_student++;

            

            echo '<br>';
        }

        
    }


    public function check_score_local(){
        $class_student = $this->db->join('tb_student','tb_student.id_student = tb_class_student.id_student')
                             ->join('tb_main_class','tb_main_class.id_main_class = tb_class_student.id_main_class')
                             ->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                             ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                             ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                             //->where('id_main_class','3860')
                             ->where('student_acreditation','0')
                             ->where('class_student_softdel','0')
                             ->where('class_student_hide','0')
                             ->where('cs_generated_score','0')
                             ->where('tb_student.id_campus','1')
                             ->where('semester_name','2020/2021 Genap')
                             //->limit('5000')
                             ->get('tb_class_student')
                             ->result();

        $no_student = 1;
        foreach ($class_student as $key) {


              $class_score = $this->db->where('id_main_class', $key->id_main_class)
                                      ->get('tb_class_score')
                                      ->result();

              foreach ($class_score as $cs) {

                    $check1 = $this->db->where('id_class_score', $cs->id_class_score)
                                       ->where('id_class_student', $key->id_class_student)
                                       ->get('tb_class_student_score')
                                       ->row();

                    if ($check1 == null OR $check1->student_score < 1) {
                       echo 'haha';
                    }

              } 

             /* $css['process'] = 1;

              $this->db->where('id_class_student',$key->id_class_student)
                       ->update('tb_class_student', $css);*/
              

              echo $no_student++;


              echo $key->subject_name.' | '.$key->main_class_name.' ';

            

            echo '<br>';
        }
    }

    public function score_o(){
        $score = $this->db->join('tb_main_class','tb_main_class.id_main_class = tb_class_score.id_main_class')
                          ->join('tb_semester','tb_semester.id_semester = tb_main_class.id_semester')
                          ->where('semester_name','2021/2022 Ganjil')
                          ->where('score_percentage','0')
                          ->where('id_score','48')
                          ->get('tb_class_score')
                          ->result();

        foreach ($score as $key) {
            $this->db->where('id_class_score', $key->id_class_score)
                     ->delete('tb_class_score');

            $this->db->where('id_class_score', $key->id_class_score)
                     ->delete('tb_class_student_score');
        }

        echo 'sukses';
    }

}