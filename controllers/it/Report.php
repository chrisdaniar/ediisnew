<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        ini_set('display_errors', 0);
    }

    public function export_excel_finance_clearance()
    {
        $id_campus = $this->input->get('id_campus');
        $id_course = $this->input->get('id_course');
        $id_intended_program = $this->input->get('id_intended_program');
        $search = $this->input->get('search');
        $export_type = $this->input->get('export_type');
        $paid_type = $this->input->get('paid_type');
        $student_type = $this->input->get('student_type');

        $check_course = $this->db->where('id_course', $id_course)->get('tb_course')->row();
        $check_intended_program = $this->db->where('id_intended_program', $id_intended_program)->get('tb_intended_program')->row();
        $check_campus = $this->db->where('id_campus', $id_campus)->get('tb_campus')->row();

        if ($check_course == null) {
            $course_name = 'All';
        } else {
            $course_name = $check_course->course_name;
        }

        if ($check_intended_program == null) {
            $intended_program_name = 'All';
        } else {
            $intended_program_name = $check_intended_program->intended_program_name;
        }

        if ($check_campus == null) {
            $campus_name = 'All';
        } else {
            $campus_name = $check_campus->campus_name;
        }

        if ($paid_type == 0) {
            $student_status = 'Unpaid';
        } elseif ($paid_type == 1) {
            $student_status = 'Paid';
        } else {
            $student_status = 'Enrolled';
        }

        if ($export_type == 'user') {

            header('Content-Type: application/vnd.ms-excel');  
            header('Content-disposition: attachment; filename=User Finance Clearance '.date('d-m-y').'.xls');
            
            $query = $this->db->select('*');
            if ($search != '') {
                $query = $this->db->like('name', $search)
                                  ->where('ldap_user', $paid_type);
            }
            if ($id_course != '') {
                $query = $this->db->where('tb_course.id_course', $id_course);
            } else {

            }
            if ($id_campus != '') {
                $query = $this->db->where('tb_campus.id_campus', $id_course);
            }
            if ($id_intended_program != '') {
                $query = $this->db->where('tb_leads.id_intended_program', $id_intended_program);
            }

            if ($student_type == 'new_student') {
                $query = $this->db->where('trimester_active', '1');
            } else {
                $query = $this->db->where('trimester_active >', '1');
            }

            $query = $this->db->join('tb_leads', 'tb_leads.id_leads = tb_student.id_leads', 'left')
                ->join('tb_campus', 'tb_campus.id_campus = tb_student.id_campus', 'left')
                ->join('tb_course', 'tb_course.id_course = tb_student.id_course', 'left')
                ->where('student_active', '1')
                ->where('ldap_user', $paid_type)
                ->order_by('name')
                ->get('tb_student');

            $student_result = $query->result();
            $student_total = $query->num_rows();

            $show = "";
            $show .= '<table>
                        <tr>
                            <td> Intended Program </td>
                            <td> : '.$intended_program_name.'</td>
                        <tr>
                        <tr>
                            <td> Course </td>
                            <td> : '.$course_name.'</td>
                        <tr>
                        <tr>
                            <td> Campus </td>
                            <td> : '.$campus_name.'</td>
                        <tr>
                        <tr>
                            <td> Status </td>
                            <td> : '.$student_status.'</td>
                        <tr>
                        </table>

                        <table border="1">
                        <tr>
                            <th> Full Name </th>
                            <th> First Name </th>
                            <th> Last Name </th>
                            <th> Username </th>
                            <th> Password </th>
                            <th> Course </th>
                            <th> Campus </th>
                        </tr>


                        '; foreach ($student_result as $key) { 

                        $low_name = strtolower($key->name);
                        $upnlow_name = ucwords($low_name);

                        $low_fname = strtolower($key->family_name);
                        $upnlow_fname = ucwords($low_fname);

                        $show .= '

                        <tr>
                            <td>'.$upnlow_name.' '.$upnlow_fname.'</td>
                            <td>'.$upnlow_name.'</td>
                            <td>'.$upnlow_fname.'</td>
                            <td>'.$key->username.'</td>
                            <td>'.$key->account_password.'</td>
                            <td>'.$key->course.'</td>
                            <td>'.$key->campus_name.'</td>
                        </tr>

                        '; } 

                        $show .='
                    </table>';

            echo $show;

        } else {

            header('Content-Type: application/vnd.ms-excel');  
            header('Content-disposition: attachment; filename=Moodle Finance Clearance '.date('d-m-y').'.xls');

            $query = $this->db->select('*');
            if ($search != '') {
                $query = $this->db->like('name', $search)
                                  ->or_like('family_name', $search);
            }
            if ($id_course != '') {
                $query = $this->db->where('tb_course.id_course', $id_course);
            } else {

            }
            if ($id_campus != '') {
                $query = $this->db->where('tb_campus.id_campus', $id_course);
            }
            if ($id_intended_program != '') {
                $query = $this->db->where('tb_leads.id_intended_program', $id_intended_program);
            }

            if ($student_type == 'new_student') {
                $query = $this->db->where('trimester_active', '1');
            } else {
                $query = $this->db->where('trimester_active >', '1');
            }

            $query = $this->db
                ->join('tb_campus', 'tb_campus.id_campus = tb_student.id_campus', 'left')
                ->join('tb_course', 'tb_course.id_course = tb_student.id_course', 'left')
                ->join('tb_leads','tb_leads.id_leads=tb_student.id_leads','left')
                ->where('student_active', '1')
                ->where('ldap_user', $paid_type)
                ->order_by('name')
                ->get('tb_student');

            $student_result = $query->result();
            $student_total = $query->num_rows();

            $show = "";
            $show .= '<table>
                        <tr>
                            <td> Intended Program </td>
                            <td> : '.$intended_program_name.'</td>
                        <tr>
                        <tr>
                            <td> Course </td>
                            <td> : '.$course_name.'</td>
                        <tr>
                        <tr>
                            <td> Campus </td>
                            <td> : '.$campus_name.'</td>
                        <tr>
                        <tr>
                            <td> Status </td>
                            <td> : '.$student_status.'</td>
                        <tr>
                        </table>

                        <table border="1">
                        <tr>
                            <th> Name </th>
                            <th> Username </th>
                            <th> Course </th>
                            <th> Campus </th>
                            <th> Code </th>
                            <th> Subject </th>
                        </tr>


                        '; foreach ($student_result as $key) { 

                        $low_name = strtolower($key->name);
                        $upnlow_name = ucwords($low_name);

                        $low_fname = strtolower($key->family_name);
                        $upnlow_fname = ucwords($low_fname);

                        $enrol = $this->db->join('tb_main_class','tb_main_class.id_main_class=tb_class_student.id_main_class')
                                          ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure')
                                          ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
                                          ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester')
                                          ->where('tb_class_student.id_student', $key->id_student)
                                          ->where('finance_clearance', '1')
                                          ->where('trimester_start_date <=', date('Y-m-d'))
                                          ->where('trimester_end_date >=', date('Y-m-d'))
                                          ->get('tb_class_student'); 

                        $enrol_result = $enrol->result();
                        $enrol_total = $enrol->num_rows();

                        $show .= '

                        <tr>
                            <td style="text-align:center;">'.$upnlow_name.' '.$upnlow_fname.'</td>
                            <td style="text-align:center;">'.$key->username.'</td>
                            <td style="text-align:center;">'.$key->course.'</td>
                            <td style="text-align:center;">'.$key->campus_name.'</td>
                            <td>
                                <table>
                                 '; foreach ($enrol_result as $data) { 
                                     $show .= '
                                        <tr>
                                            <td>'.$data->subject_code.'</td>
                                        </tr>
                                     '; } 

                                     $show .= '
                                </table>
                           </td>
                           <td>
                                <table>
                                 '; foreach ($enrol_result as $data) { 
                                     $show .= '
                                        <tr>
                                            <td>'.$data->subject_name.'</td>
                                        </tr>
                                     '; } 

                                     $show .= '
                                </table>
                           </td>
                            
                           
                        </tr>

                        '; } 

                        $show .='
                    </table>';

            echo $show;
        }
    }
}