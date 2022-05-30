<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Enrolment extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Admin_master_model', 'admin_master_model');
        $this->load->model('Recruitment/Leads_model', 'leads_model');

        ini_set('display_errors', 0);
    }
    public function index()
    {
        //error_reporting(0);
        $id_leads = $this->session->userdata('id_leads');
        $data['title'] = 'Enrolment';
        $data['left_bar'] = $this->admin_master_model->check_navbar();
        $data['list_subject'] = $this->get_subject($id_leads);
        $data['leads'] = $this->leads_model->get_detail_leads($id_leads);
        $data['gender'] = $this->leads_model->get_gender();
        $data['country'] = $this->leads_model->get_country();
        $data['province'] = $this->leads_model->get_province();
        $data['recruitment'] = $this->leads_model->get_recruitment();
        $data['trimester'] = $this->get_trimester($id_leads);
        $trimester = $this->get_trimester($id_leads);
        $data['class_student'] = $this->get_class_student_active($id_leads, $trimester->id_trimester);
        $data['class_student_inactive'] = $this->get_class_student_inactive($id_leads, $trimester->id_trimester);
        $this->load->view('Student/Enrollment/enrollment_view', $data);
        // main class , detail course structure, subject
        // id_specialist
    }
    public function action_form()
    {
      $aksi = $this->input->post('aksi');
      $id_leads = $this->input->post('id_leads'); 

      if ($aksi == 'save_subject') {

          $list_subject = json_decode($this->input->post('list_subject'));
          foreach ($list_subject as $key) {

              $this->leads_model->add_class_student_by_student($key, $id_leads);
          }
          $hasil['response'] = true;
          echo json_encode($hasil);
      } elseif ($aksi == 'remove_subject') {
          $list_subject_active = json_decode($this->input->post('list_subject_active')); ;
          foreach ($list_subject_active as $key) {
              $tcs['updated_by_type'] = 1;
              $tcs['class_student_softdel'] = 1;
              $tcs['class_student_updated_by'] = $this->session->userdata('id_leads');
              $tcs['class_student_last_updated'] = date('Y-m-d H:i:s');
              $this->db->where('id_class_student', $key)
                       ->update('tb_class_student',$tcs);

              $this->db->where('id_class_student', $key)
                     ->delete('tb_schedule_student');
          }
          $hasil['response'] = true;
          echo json_encode($hasil);
      } elseif ($aksi == 'save_profile') {
        
          $data['name'] = $this->input->post('name');
          $data['family_name'] = $this->input->post('family_name');
          $data['id_gender'] = $this->input->post('id_gender');
          $data['id_country'] = $this->input->post('id_country');
          $data['address'] = $this->input->post('address');
          $data['address'] = $this->input->post('address');
          $data['id_province'] = $this->input->post('id_province');
          $data['id_regency'] = $this->input->post('id_regency');
          $data['phone'] = $this->input->post('phone');
          $data['homephone'] = $this->input->post('homephone');
          $data['email'] = $this->input->post('email');
          $data['dob'] = $this->leads_model->format_tanggal($this->input->post('dob'));
          $this->db->where('id_leads', $id_leads)->update('tb_leads', $data);

      } else {
         
      }
       
    }

    public function get_subject($id_leads = '')
    {

        $leads = $this->db->join('tb_student','tb_student.id_leads=tb_leads.id_leads','left')
                          ->where('student_active', '1')
                          ->where('tb_leads.id_leads', $id_leads)
                          ->get('tb_leads')
                          ->row();

        $co = $this->db->where('id_course', $leads->id_course)->get('tb_course')->row();

        $row = array();
        $query = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure')
                          ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
                          ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester')
                          ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure')
                          ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left');

        if ($leads->check_1a == 1) {

             $query = $this->db->where('tb_course.course_group', $co->course_group);

        } else {
          if ($leads->manual_enrollment == 0) {
            $query = $this->db->join('tb_class_specialist', 'tb_class_specialist.id_main_class = tb_main_class.id_main_class')
                          ->join('tb_specialist', 'tb_specialist.id_specialist = tb_class_specialist.id_specialist')
                          ->where('tb_specialist.id_specialist', (int)$leads->id_specialist);
          } else {
            $query = $this->db->where('tb_course_structure.id_course', (int)$leads->id_course);
          }
        }
                         
          $query = $this->db->where('enrolment_start_date <=', date('Y-m-d'))
                          ->where('enrolment_end_date >=', date('Y-m-d'))
                          ->where('tb_main_class.id_campus', $leads->id_campus);

        if ($leads->id_intended_program == '3') {
            $query = $this->db->order_by('tb_subject.subject_code','asc');
        } else {
            $query = $this->db->order_by('tb_detail_course_structure.trimester_course_structure','asc')
                              ->order_by('tb_subject.subject_name','asc');
        }

          $query = $this->db->get('tb_main_class')
                            ->result();


            foreach ($query as $key) {
                $check_class = $this->db->join('tb_main_class','tb_main_class.id_main_class=tb_class_student.id_main_class')
                                        ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure')
                                        ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
                                          ->where('tb_subject.id_subject', $key->id_subject)
                                          ->where('id_leads', $id_leads)
                                          ->where('class_student_softdel', 0)
                                          ->get('tb_class_student')
                                          ->num_rows();

                if ($check_class == 0) {
                        $cek = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure')
                            ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
                            ->where('tb_main_class.id_main_class', $key->id_main_class);

                      if ($key->elective == '1') {
                        # code...
                      } else {
                        if ($key->packet == '1') {
                          $cek = $this->db->where('trimester_course_structure', $leads->trimester_active);
                        } else {

                        }
                      }

                      $cek = $this->db->get('tb_main_class')
                                        ->row();

                      if ($cek != '' && $cek != null) {
                          $row[] = $cek;
                      }
                } 
            }
        return $row;
    }

    public function get_class_student_active($id_leads = '', $id_trimester = '')
    {

        $now = date('Y-m-d');
        $query = $this->db->join('tb_main_class','tb_main_class.id_main_class=tb_class_student.id_main_class')
                          ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure')
                          ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
                          ->where('id_leads',$id_leads)
                          ->where('id_trimester', $id_trimester)
                          ->where('class_student_softdel','0')
                          ->get('tb_class_student')
                          ->result();

        return $query;
    }

    public function get_class_student_inactive($id_leads = '', $id_trimester = '')
    {

        $now = date('Y-m-d');
        $query = $this->db->join('tb_main_class','tb_main_class.id_main_class=tb_class_student.id_main_class')
                          ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure')
                          ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
                          ->where('id_leads',$id_leads)
                          ->where('id_trimester', $id_trimester)
                          ->where('class_student_softdel','1')
                          ->get('tb_class_student')
                          ->result();

        //echo $query->id_trimester;

        return $query;
    }
    
    public function get_regency()
    {
        $id_province = $this->input->post('id_province');
        $data = $this->leads_model->get_regency($id_province);
        echo json_encode($data);
    }
}
