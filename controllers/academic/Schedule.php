<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Schedule extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Admin/Admin_master_model', 'admin_master_model');
        $this->load->model('Schedule/Xml_model', 'xml_model');

        if ($this->session->userdata('logged_in') == false) {
            redirect('login');
        }
    }

    public function oko()
    {
        
    }
    
    function list() {
        $data['title'] = 'List Schedule';
        $data['left_bar'] = $this->admin_master_model->check_navbar();
        $data['pagination_data'] = $this->xml_model->pagination_schedule($this->input->get(), 'result');
        $data['pagination_total_page'] = $this->xml_model->pagination_schedule($this->input->get(), 'num_rows');
        $this->load->view('Academic/Schedule/list_view', $data);
    }

    public function timetables()
    {
        /*if (isset($_GET['p'])) {
            $cek = $this->db->where('id_schedule', $_GET['p']);
        }
        
        $cek = $this->db->where('ds', '1')->get('tb_schedule')->row();
        if ($cek != '') {
            $data['schedule'] = $cek;
        } else {
            $data['schedule'] = '';
        }*/

        $data['title'] = 'Schedule';
        $data['left_bar'] = $this->admin_master_model->check_navbar();
        $this->load->view('Academic/Schedule/timetables_view', $data);
    }

    public function export_timetables()
    {

        header('Content-Type: application/vnd.ms-excel');  
        header('Content-disposition: attachment; filename=JIC_Employee_Report.xls');

        if (isset($_GET['p'])) {
            $cek = $this->db->where('id_schedule', $_GET['p']);
        }
        $cek = $this->db->order_by('id_schedule', 'desc')->get('tb_schedule')->row();
        if ($cek != '') {
            $data['schedule'] = $cek;
        } else {
            $data['schedule'] = '';
        }

        $data['title'] = 'Schedule';
        $data['left_bar'] = $this->admin_master_model->check_navbar();
        $this->load->view('Academic/Schedule/export_timetables_view', $data);
    }

     public function coba_export()
    {
        if (isset($_GET['p'])) {
            $cek = $this->db->where('id_schedule', $_GET['p']);
        }
        $cek = $this->db->order_by('id_schedule', 'desc')->get('tb_schedule')->row();
        if ($cek != '') {
            $data['schedule'] = $cek;
        } else {
            $data['schedule'] = '';
        }

        $data['title'] = 'Schedule';
        $data['left_bar'] = $this->admin_master_model->check_navbar();
        $this->load->view('Academic/Schedule/coba_export', $data);
    }

    public function generate()
    {
        $data['title'] = 'Schedule';
        $data['left_bar'] = $this->admin_master_model->check_navbar();
        $this->load->view('Academic/Schedule/generate_view', $data);
    }
    public function ini_split()
    {

        $gen = $this->input->post('generate');
        $this->db->where('id_schedule', '')->delete('tb_schedule_student');

        $h = explode('|', $gen);
        $d = array();
        foreach ($h as $dd) {
            $ss = explode(':', $dd);
            array_push($d, $ss);
        }
        $str = 1;
        foreach ($d as $keyk) {
            $jw = 'where';
            if ($str > 1) {
                $jw = 'or_where';
            }
            $query_split = $this->db
                ->$jw('tb_course.id_intended_program', $keyk[0])
                ->where('id_trimester', $keyk[1]);
            $str++;
        }
        $query_split = $this->db
            ->join('tb_main_class', 'tb_main_class.id_main_class = tb_class_student.id_main_class')
            ->join('tb_detail_course_structure', 'tb_detail_course_structure.id_detail_course_structure = tb_main_class.id_detail_course_structure', 'left')
            ->join('tb_course_structure', 'tb_course_structure.id_course_structure = tb_detail_course_structure.id_course_structure', 'left')
            ->join('tb_course', 'tb_course.id_course = tb_course_structure.id_course', 'left')
            ->join('tb_leads','tb_leads.id_leads=tb_class_student.id_leads','left')
            ->where('class_student_softdel','0')
            ->where('tb_leads.id_campus',3)
            ->order_by('main_class_join', 'asc')
            ->group_by('main_class_join')
            ->get('tb_class_student')
            ->result();
        foreach ($query_split as $key) {
            $str = 1;
            foreach ($d as $keyk) {
                $jw = 'where';
                if ($str > 1) {
                    $jw = 'or_where';
                }
                $student = $this->db
                    ->$jw('tb_course.id_intended_program', $keyk[0])
                    ->where('id_trimester', $keyk[1])
                    ->where('main_class_join', $key->main_class_join);
                $str++;
            }
            $student = $this->db->select('id_class_student, name')
                ->order_by('name', 'asc')
                ->join('tb_leads', 'tb_leads.id_leads = tb_class_student.id_leads')
                ->join('tb_main_class', 'tb_main_class.id_main_class = tb_class_student.id_main_class')
                ->join('tb_detail_course_structure', 'tb_detail_course_structure.id_detail_course_structure = tb_main_class.id_detail_course_structure', 'left')
                ->join('tb_course_structure', 'tb_course_structure.id_course_structure = tb_detail_course_structure.id_course_structure', 'left')
                ->join('tb_course', 'tb_course.id_course = tb_course_structure.id_course', 'left')
                ->where('class_student_softdel','0')
                ->where('tb_main_class.id_campus',3)
                ->get('tb_class_student');
            $array_id_main_class = $this->db->where('main_class_join', $key->main_class_join)
                ->get('tb_main_class')
                ->result();
            $tc = 0;
            foreach ($array_id_main_class as $aic) {
                if ($tc == 0) {
                    $wh = 'where';
                } else {
                    $wh = 'or_where';
                }
                $total_class_lecture = $this->db->$wh('id_main_class', $aic->id_main_class)
                    ->where('class_lecture_join !=', '0')
                    ->where('class_lecture_type', 'L');
                $tc++;
            }
            $total_class_lecture = $this->db->group_by('split')->get('tb_class_lecture');
            if ($student->num_rows() == 0 || $total_class_lecture->num_rows() == 0) {
                $chunk_lecture = 0;
            } else {
                $chunk_lecture = ceil($student->num_rows() / $total_class_lecture->num_rows());
            }

            if ($chunk_lecture > 0) {
                $split_student_lecture = array_chunk($student->result_array(), $chunk_lecture);
            } else {
                $split_student_lecture = $student->result_array();
            }

            $tc = 0;
            foreach ($array_id_main_class as $aic) {
                if ($tc == 0) {
                    $wh = 'where';
                } else {
                    $wh = 'or_where';
                }
                $total_class_tutor = $this->db->$wh('id_main_class', $aic->id_main_class)
                    ->where('class_lecture_join !=', '0')
                    ->where('class_lecture_type', 'T');
                $tc++;
            }
            $total_class_tutor = $this->db->group_by('split')->get('tb_class_lecture');

            if ($student->num_rows() == 0 || $total_class_tutor->num_rows() == 0) {
                $chunk_tutor = 0;
            } else {
                $chunk_tutor = ceil($student->num_rows() / $total_class_tutor->num_rows());
            }

            if ($chunk_tutor > 0) {
                $split_student_tutor = array_chunk($student->result_array(), $chunk_tutor);
            } else {
                $split_student_tutor = $student->result_array();
            }

            $tc = 0;
            foreach ($array_id_main_class as $aic) {
                if ($tc == 0) {
                    $wh = 'where';
                } else {
                    $wh = 'or_where';
                }
                $class_lecture_join = $this->db->$wh('id_main_class', $aic->id_main_class)
                    ->where('class_lecture_join !=', '0');
                $tc++;
            }
            $class_lecture_join = $this->db->get('tb_class_lecture')->result();

            $type_l = 0;
            $type_t = 0;
            $uu = 0;

            foreach ($class_lecture_join as $clj) {
                if ($clj->class_lecture_type == 'L') {

                    if (count($split_student_lecture[$type_l]) > 0) {
                        $loop_clj = $this->db->where('tb_class_lecture.id_main_class', $clj->id_main_class)
                            ->where('class_lecture_type', $clj->class_lecture_type)
                            ->where('split', $clj->split)
                            ->get('tb_class_lecture')
                            ->result();

                        foreach ($loop_clj as $lclj) {
                            foreach ($split_student_lecture[$type_l] as $split_students) {
                                $schedule_student['id_class_student'] = $split_students['id_class_student'];
                                $schedule_student['class_lecture_join'] = $lclj->class_lecture_join;
                                
                                $cek = $this->db->where('id_class_student', $schedule_student['id_class_student'])
                                    ->where('class_lecture_join', $schedule_student['class_lecture_join'])
                                    ->where('id_schedule', '')
                                    ->get('tb_schedule_student')
                                    ->num_rows();

                                if ($cek == 0) {
                                    $this->db->insert('tb_schedule_student', $schedule_student);
                                }
                            }
                        }
                        if ($type_t < (count($split_student_tutor) - 1)) {
                            $type_l++;
                        }
                    }
                } else if ($clj->class_lecture_type == 'T') {
                    if (count($split_student_tutor[$type_t]) > 0) {
                        $loop_clj = $this->db->where('tb_class_lecture.id_main_class', $clj->id_main_class)
                            ->where('class_lecture_type', $clj->class_lecture_type)
                            ->where('split', $clj->split)
                            ->get('tb_class_lecture')
                            ->result();
                        foreach ($loop_clj as $lclj) {
                            foreach ($split_student_tutor[$type_t] as $split_students) {
                                $schedule_student['id_class_student'] = $split_students['id_class_student'];
                                $schedule_student['class_lecture_join'] = $lclj->class_lecture_join;
                                $cek = $this->db->where('id_class_student', $schedule_student['id_class_student'])
                                    ->where('class_lecture_join', $schedule_student['class_lecture_join'])
                                    ->where('id_schedule', '')
                                    //->where('class_student_softdel','0')
                                    ->get('tb_schedule_student')
                                    ->num_rows();
                                if ($cek == 0) {
                                    $this->db->insert('tb_schedule_student', $schedule_student);
                                }
                            }
                        }
                        if ($type_t < (count($split_student_tutor) - 1)) {
                            $type_t++;
                        }
                    }
                }
            }
        }
        $this->create_xml($d);
    }
    public function create_xml($gen = '')
    {
        $this->load->helper('file');
        $data_subject = $this->xml_model->get_subject($gen);
        $data_teacher = $this->xml_model->get_teacher($gen);
        $data_student = $this->xml_model->get_student($gen);
        $data_activities = $this->xml_model->get_activities($gen);
        $data_room = $this->xml_model->get_room('All');
        $data_room_lecture = $this->xml_model->get_room('lecture');
        $data_room_tutorial = $this->xml_model->get_room('tutorial');
        $data_room_tanpa_special = $this->xml_model->get_room('tanpa_special');
        $data_room_special = $this->xml_model->get_room('special');
        $data_time = $this->xml_model->get_time();
        $data_teacher_availability = $this->xml_model->get_teacher_availability();
        $data_ascending_class = $this->xml_model->get_ascending_class($gen);
        $data_special_room = $this->xml_model->get_special_room($gen);
        $data_break_time = $this->xml_model->get_break_time();
        $data_consecutive = $this->xml_model->get_consecutive($gen);
        $data_consecutive_same_room = $this->xml_model->get_consecutive_same_room($gen);
        
        $data_startingtimes = $this->xml_model->get_startingtimes();

        $data = '<?xml version="1.0" encoding="utf-8"?>';
        $data .= "<fet version='5.42.3'>

        <Institution_Name>Default institution</Institution_Name>

        <Comments>Default comments</Comments>

        <Days_List>
            <Number_of_Days>5</Number_of_Days>
            <Day>
                <Name>Monday</Name>
            </Day>
            <Day>
                <Name>Tuesday</Name>
            </Day>
            <Day>
                <Name>Wednesday</Name>
            </Day>
            <Day>
                <Name>Thursday</Name>
            </Day>
            <Day>
                <Name>Friday</Name>
            </Day>
        </Days_List>

        <Hours_List>
            <Number_of_Hours>" . count($data_time) . "</Number_of_Hours> ";

        foreach ($data_time as $time) {
            $data .= "<Hour>
                    <Name>$time->time_name</Name>
                </Hour>";
        }
        $data .= "
        </Hours_List>
        <Subjects_List>";
        foreach ($data_subject as $subject) {
            $data .= "<Subject>
            <Name>$subject->subject_code</Name>
            <Comments></Comments>
            </Subject>";
        }
        $data .= "
        </Subjects_List>

        <Activity_Tags_List>
            <Activity_Tag>
                <Name>LECTURE</Name>
                <Printable>true</Printable>
                <Comments></Comments>
            </Activity_Tag>
            <Activity_Tag>
                <Name>TUTORIAL</Name>
                <Printable>true</Printable>
                <Comments></Comments>
            </Activity_Tag>
            ";
        foreach ($data_room_special as $room_special) {
            $data .= "<Activity_Tag>
                <Name>SPECIAL-$room_special->room_name</Name>
                <Printable>true</Printable>
                <Comments></Comments>
            </Activity_Tag>";
        }
        $data .= "
        </Activity_Tags_List>

        <Teachers_List>";

        foreach ($data_teacher as $teacher) {
            $name = $teacher['employee_name'];
            $subject = '';
            foreach ($teacher['subject'] as $t_subject) {
                $sj = $t_subject->subject_code;
                $subject .= "<Qualified_Subject>$sj</Qualified_Subject>";
            }
            $data .= "<Teacher>
                <Name>$name</Name>
                <Target_Number_of_Hours>0</Target_Number_of_Hours>
                <Qualified_Subjects>
                    $subject
                </Qualified_Subjects>
                <Comments></Comments>
            </Teacher>";
        }

        $data .= "
        </Teachers_List>

        <Students_List>
            <Year>
                <Name>T2-2019 - MC</Name>
                <Number_of_Students>" . count($data_student) . "</Number_of_Students>
                <Comments></Comments>";
        foreach ($data_student as $student) {
            $data .= "<Group>
                <Name>$student->id_leads</Name>
                <Number_of_Students>1</Number_of_Students>
                <Comments></Comments>
            </Group>";
        }
        $data .= "

            </Year>
        </Students_List>

        <Activities_List>";
        foreach ($data_activities as $activities) {
            $teacher = $activities['teacher'];
            $subject = $activities['subject'];
            $activity_tag = $activities['activity_tag'];
            $duration = $activities['duration'];
            $id = $activities['id'];

            $data .= "
            <Activity>
                <Teacher>$teacher</Teacher>
                <Subject>$subject</Subject>
                <Activity_Tag>$activity_tag</Activity_Tag>";

            $nos = count($activities['student']);
            foreach ($activities['student'] as $student) {
                $data .= "
                <Students>$student->id_leads</Students>";
            }

            $data .= "
                <Duration>$duration</Duration>
                <Total_Duration>$duration</Total_Duration>
                <Id>$id</Id>
                <Activity_Group_Id>0</Activity_Group_Id>
                <Number_Of_Students>$nos</Number_Of_Students>
                <Active>true</Active>
                <Comments></Comments>
            </Activity>";
        }

        $data .= "
        </Activities_List>

        <Buildings_List>
            <Building>
                <Name>IMAM BONJOL</Name>
                <Comments></Comments>
            </Building>
        </Buildings_List>

        <Rooms_List>";

        foreach ($data_room as $room) {
            $data .= "<Room>
            <Name>$room->room_name</Name>
            <Building>IMAM BONJOL</Building>
            <Capacity>$room->room_capacity</Capacity>
            <Virtual>false</Virtual>
            <Comments></Comments>
        </Room>";
        }

        $data .= "
        </Rooms_List>

        <Time_Constraints_List>
            <ConstraintBasicCompulsoryTime>
                <Weight_Percentage>100</Weight_Percentage>
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintBasicCompulsoryTime>";

        foreach ($data_teacher_availability as $teacher_availability) {
            $data .= "
            <ConstraintTeacherNotAvailableTimes>
                <Weight_Percentage>100</Weight_Percentage>
                <Teacher>" . $teacher_availability['teacher'] . "</Teacher>
                <Number_of_Not_Available_Times>" . count($teacher_availability['not_available']) . "</Number_of_Not_Available_Times>";

            foreach ($teacher_availability['not_available'] as $not_available) {
                $data .= "<Not_Available_Time>
                    <Day>" . $this->xml_model->format_day($not_available->day) . "</Day>
                    <Hour>$not_available->time_name</Hour>
                </Not_Available_Time>";
            }

            $data .= "
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintTeacherNotAvailableTimes>";
        }

        foreach ($data_ascending_class as $ascending_class) {
            $data .= "<ConstraintTwoActivitiesOrdered>
            <Weight_Percentage>100</Weight_Percentage>
            <First_Activity_Id>" . $ascending_class['id'] . "</First_Activity_Id>
            <Second_Activity_Id>" . $ascending_class['id2'] . "</Second_Activity_Id>
            <Active>true</Active>
            <Comments></Comments>
        </ConstraintTwoActivitiesOrdered>
        ";
        }
        $data .= "
            <ConstraintStudentsSetNotAvailableTimes>
                <Weight_Percentage>100</Weight_Percentage>
                <Students>T2-2019 - MC</Students>
                <Number_of_Not_Available_Times>" . count($data_break_time) . "</Number_of_Not_Available_Times>";
        foreach ($data_break_time as $break_time) {
            $data .= "
                    <Not_Available_Time>
                        <Day>" . $this->xml_model->format_day($break_time->break_time_day) . "</Day>
                        <Hour>" . $break_time->time_name . "</Hour>
                    </Not_Available_Time>";
        }
        $data .= "
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintStudentsSetNotAvailableTimes>";

        foreach ($data_consecutive as $consecutive) {
            $data .= "
            <ConstraintTwoActivitiesConsecutive>
                <Weight_Percentage>100</Weight_Percentage>
                <First_Activity_Id>" . $consecutive['id'] . "</First_Activity_Id>
                <Second_Activity_Id>" . $consecutive['id2'] . "</Second_Activity_Id>
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintTwoActivitiesConsecutive>";
        }
        foreach ($data_startingtimes as $startingtimes) {
            $data .= "
            <ConstraintActivitiesPreferredStartingTimes>
            <Weight_Percentage>".$startingtimes['detail']->class_starting_time_percentage."</Weight_Percentage>
            <Teacher_Name></Teacher_Name>
            <Students_Name></Students_Name>
            <Subject_Name></Subject_Name>
            <Activity_Tag_Name></Activity_Tag_Name>
            <Duration></Duration>
            <Number_of_Preferred_Starting_Times>" . count($startingtimes['data']) . "</Number_of_Preferred_Starting_Times>
            ";
            foreach ($startingtimes['data'] as $starting_times) {
                $data .= "
                <Preferred_Starting_Time>
                    <Preferred_Starting_Day>" . $this->xml_model->format_day($starting_times->class_starting_time_day) . "</Preferred_Starting_Day>
                    <Preferred_Starting_Hour>$starting_times->time_name</Preferred_Starting_Hour>
                </Preferred_Starting_Time>";
            }    
            $data .= "
            <Active>true</Active>
                <Comments></Comments>
            </ConstraintActivitiesPreferredStartingTimes>";
        };
        $data .= "
            
        </Time_Constraints_List>

        <Space_Constraints_List>
            <ConstraintBasicCompulsorySpace>
                <Weight_Percentage>100</Weight_Percentage>
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintBasicCompulsorySpace>
            <ConstraintActivityTagPreferredRooms>
                <Weight_Percentage>100</Weight_Percentage>
                <Activity_Tag>LECTURE</Activity_Tag>";

        $total_data_room_tanpa_special = count($data_room_tanpa_special);
        $total_data_room_special = count($data_room_special);
        $total_room = count($data_room);

        $data .= "
                <Number_of_Preferred_Rooms>" . count($data_room_lecture) . "</Number_of_Preferred_Rooms>";
        foreach ($data_room_lecture as $room_lecture) {
            $data .= "
            <Preferred_Room>$room_lecture->room_name</Preferred_Room>";
        }
        $data .= "
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintActivityTagPreferredRooms>
            <ConstraintActivityTagPreferredRooms>
                <Weight_Percentage>100</Weight_Percentage>
                <Activity_Tag>TUTORIAL</Activity_Tag>
                <Number_of_Preferred_Rooms>" . count($data_room_tutorial) . "</Number_of_Preferred_Rooms>";

        foreach ($data_room_tutorial as $room_tutorial) {
            $data .= "
            <Preferred_Room>$room_tutorial->room_name</Preferred_Room>";
        }

        $data .= "
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintActivityTagPreferredRooms>
            ";
        foreach ($data_room_special as $special) {
            $data .= "
            <ConstraintActivityTagPreferredRooms>
                <Weight_Percentage>100</Weight_Percentage>
                <Activity_Tag>SPECIAL-$special->room_name</Activity_Tag>
                <Number_of_Preferred_Rooms>1</Number_of_Preferred_Rooms>
                <Preferred_Room>$special->room_name</Preferred_Room>
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintActivityTagPreferredRooms>";
        }

        foreach ($data_special_room as $special_room) {
            $data .= "
            <ConstraintActivityPreferredRoom>
                <Weight_Percentage>100</Weight_Percentage>
                <Activity_Id>$special_room->class_lecture_join</Activity_Id>
                <Room>$special_room->room_name</Room>
                <Permanently_Locked>true</Permanently_Locked>
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintActivityPreferredRoom>";
        }
        foreach ($data_consecutive_same_room as $consecutive_same_room) {
            $data .= "
            <ConstraintActivitiesSameRoomIfConsecutive>
                <Weight_Percentage>100</Weight_Percentage>
                <Number_of_Activities>2</Number_of_Activities>
                <Activity_Id>" . $consecutive_same_room['id'] . "</Activity_Id>
                <Activity_Id>" . $consecutive_same_room['id2'] . "</Activity_Id>
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintActivitiesSameRoomIfConsecutive>";
        }

        $data .= "
        </Space_Constraints_List>

        </fet>";
        /* header('Content-Type: application/xml; charset=utf-8');
        echo $data; */
        $name_file = date('ymdhis');
        if (!write_file("./xml_fet/$name_file.fet", $data)) {
            return 'Unable to write the file';
        } else {
            $this->fet($name_file);
        }
    }
    public function fet($fet = '', $p = '')
    {
        //$local_dir = '/applications/xampp/htdocs/fet';
        //$local_file = '../jic_system';

         $local_dir = '/usr/share/nginx/html/fet/';
         $local_file = '../intranet';

        chdir($local_dir);
        $out = shell_exec("./fet-cl --inputfile=$local_file/xml_fet/$fet.fet --warnsubgroupswiththesameactivities=false --timelimitseconds=2");
        $hasil = substr($out, -22);
        $hasilx = substr($hasil, 0, -1);
        $hasil_te = substr($hasilx, -13);

        if ($hasilx == 'Simulation successful') {

            $name['schedule'] = date('m-Y');
            $name['file_fet'] = $fet;
            $name['create_date'] = date('Y-m-d H:i:s');
            $name['update_date'] = date('Y-m-d H:i:s');
            $id_schedule = '';
            if ($this->db->where('schedule', $name['schedule'])->where('file_fet', $name['file_fet'])->get('tb_schedule')->num_rows() == 0) {
                $this->db->insert('tb_schedule', $name);
                $id_schedule = $this->db->insert_id();

                $ss['id_schedule'] = $id_schedule;
                $this->db->where('id_schedule', '')->update('tb_schedule_student', $ss);
            } else {
                $id_schedule = $this->db->where('schedule', $name['schedule'])->where('file_fet', $name['file_fet'])->get('tb_schedule')->row()->id_schedule;

                $ss['update_date'] = date('Y-m-d H:i:s');
                $this->db->where('id_schedule', $id_schedule)->update('tb_schedule', $ss);
            }

            $this->insert_schedule($fet, $id_schedule);
            $arr['status'] = 'Success';
            $arr['logs'] = $out;
            if ($p == '') {
                echo json_encode($arr);
            } else {
                redirect('academic/schedule/timetables');
            }
        } else if ($hasil_te == 'Time exceeded') {
            $arr['status'] = 'Time Exceeded';
            $arr['logs'] = file_get_contents('../fet/logs/difficult_activities.txt');
            if ($p == '') {
                echo json_encode($arr);
            } else {
                redirect('academic/schedule/timetables');
            }
        } else {
            $arr['status'] = 'Failed';
            $arr['logs'] = $out;
            if ($p == '') {
                echo json_encode($arr);
            } else {
                redirect('academic/schedule/timetables');
            }
        }
    }
    public function insert_schedule($fet = '', $id_schedule = '')
    {
        //$local_dir = '/applications/xampp/htdocs/fet/';
         $local_dir = '/usr/share/nginx/html/fet/'; 
        $this->db->where('id_schedule', $id_schedule)->delete('tb_schedule_detail');
        $path = $local_dir . 'timetables/' . $fet . '/' . $fet . '_activities.xml';
        $xmlfile = file_get_contents($path);
        $new = simplexml_load_string($xmlfile);
        $con = json_encode($new);
        $newArr = json_decode($con, true);
        foreach ($newArr['Activity'] as $key) {
            $schedule['class_lecture_join'] = $key['Id'];
            $schedule['day'] = $this->xml_model->format_day_number($key['Day']);
            $schedule['start'] = substr($key['Hour'], 0, 5);
            $schedule['id_room'] = $this->db->where('room_name', $key['Room'])->get('tb_room')->row()->id_room;
            $schedule['id_schedule'] = $id_schedule;
            $this->db->insert('tb_schedule_detail', $schedule);
        }
        $this->pindah($id_schedule);
    }
    public function pindah($id_schedule = '')
    {

        $special_room = $this->db
            ->where('tb_schedule_detail.id_schedule', $id_schedule)
            ->join('tb_class_lecture', 'tb_class_lecture.class_lecture_join = tb_schedule_detail.class_lecture_join')
            ->join('tb_main_class', 'tb_main_class.id_main_class = tb_class_lecture.id_main_class')
            ->join('tb_detail_course_structure', 'tb_detail_course_structure.id_detail_course_structure = tb_main_class.id_detail_course_structure', 'left')
            ->join('tb_subject', 'tb_subject.id_subject = tb_detail_course_structure.id_subject')
            ->join('tb_special_room', 'tb_special_room.id_subject = tb_subject.id_subject')
            ->where('tb_main_class.id_campus',3)
            ->group_by('tb_special_room.id_room')
            ->order_by('tb_schedule_detail.id_room', 'asc')
            ->get('tb_schedule_detail')
            ->result();
        // echo '<pre>';
        /* print_r($special_room); */
        foreach ($special_room as $s_room) {
            $subject_special_room = $this->db
                ->where('tb_schedule_detail.id_room', $s_room->id_room)
                ->where('tb_class_lecture.class_lecture_join !=', 0)
                ->where('tb_main_class.id_campus',3)
                ->join('tb_detail_course_structure', 'tb_detail_course_structure.id_subject = tb_special_room.id_subject')
                ->join('tb_main_class', 'tb_main_class.id_detail_course_structure = tb_detail_course_structure.id_detail_course_structure')
                ->join('tb_class_lecture', 'tb_class_lecture.id_main_class = tb_main_class.id_main_class')
                ->join('tb_schedule_detail', 'tb_schedule_detail.class_lecture_join = tb_class_lecture.class_lecture_join')
                ->get('tb_special_room')
                ->result();
                
            $a = array();
            foreach ($subject_special_room as $subject_special) {
                array_push($a, $subject_special->class_lecture_join);
            }
            /* print_r($a); */
            $non = $this->db
                ->where('id_room', $s_room->id_room)
                ->where('tb_schedule_detail.id_schedule', $id_schedule)
                ->where_not_in('tb_schedule_detail.class_lecture_join', $a)
                ->join('tb_class_lecture', 'tb_class_lecture.class_lecture_join = tb_schedule_detail.class_lecture_join')
                ->get('tb_schedule_detail')
                ->result();
            // print_r($non);
            $tanpa_special = $this->xml_model->get_room('tanpa_special');
            // print_r($tanpa_special);
            foreach ($non as $nons) {

                foreach ($tanpa_special as $ts) {
                    $start = $nons->start;
                    $durasi_jam = $nons->class_lecture_hours;
                    $id_room = $ts->id_room;
                    $day = $nons->day;
                    if ($this->cek_room($start, $durasi_jam, $id_room, $day, $id_schedule) == 'true') {
                        $total_student = $this->db->where('id_schedule', $id_schedule)->where('class_lecture_join', $nons->class_lecture_join)->get('tb_schedule_student')->num_rows();
                        // echo $start.' - '.$id_room.'<br>';
                        if ($total_student <= $ts->room_capacity) {
                            $schedule['id_room'] = $id_room;
                            $this->db->where('id_schedule_detail', $nons->id_schedule_detail)->update('tb_schedule_detail', $schedule);
                            break;
                        }
                    }
                }
            }
        }
        /* echo 'sjk'; */
        /* echo '<pre>';
    print_r($subject_special); */
    }
    public function cek_room($start, $durasi_jam, $id_room, $day, $id_schedule)
    {
        $hasil = 'true';
        $query = $this->db->where('day', $day)->where('id_room', $id_room)->where('id_schedule', $id_schedule)->get('tb_schedule_detail')->result();
        foreach ($query as $key) {
            $durasi_minute = $durasi_jam * 60;
            $end = date('H:i', strtotime("+$durasi_minute minutes", strtotime($key->start)));
            if ($this->cek_jam($start, $durasi_jam, $key->start, $end) == 'false') {
                $hasil = 'false';
            }
        }
        return $hasil;
    }
    public function cek_jam($jam_awal = '', $durasi_jam = 0, $dil_awal = '', $dil_akhir = '')
    {
        $durasi_jam = $durasi_jam * 60;
        $start = date('Y-m-d H:i', strtotime($jam_awal));
        $end = date('Y-m-d H:i', strtotime("+$durasi_jam minutes", strtotime($start)));
        $dilarang_awal = date('Y-m-d H:i', strtotime($dil_awal));
        $dilarang_akhir = date('Y-m-d H:i', strtotime($dil_akhir));

        $cek_datetime1 = new DateTime($start);
        $cek_datetime2 = new DateTime($dilarang_akhir);
        $cek_difference = $cek_datetime1->diff($cek_datetime2);
        $cek_minute = $cek_difference->h * 60 + $cek_difference->i;
        $cek_minute = $cek_difference->format('%r') . $cek_minute;

        if ($cek_minute > 0) {
            $datetime1 = new DateTime($start);
            $datetime2 = new DateTime($dilarang_awal);
            $difference = $datetime1->diff($datetime2);
            $minute = $difference->h * 60 + $difference->i;
            $minute = $difference->format('%r') . $minute;

            $datetime1_2 = new DateTime($end);
            $datetime2_2 = new DateTime($dilarang_awal);
            $difference_2 = $datetime1_2->diff($datetime2_2);
            $minute_2 = $difference_2->h * 60 + $difference_2->i;
            $minute_2 = $difference_2->format('%r') . $minute_2;
        } else {
            $datetime1 = new DateTime($dilarang_akhir);
            $datetime2 = new DateTime($start);
            $difference = $datetime1->diff($datetime2);
            $minute = $difference->h * 60 + $difference->i;
            $minute = $difference->format('%r') . $minute;

            $datetime1_2 = new DateTime($dilarang_akhir);
            $datetime2_2 = new DateTime($end);
            $difference_2 = $datetime1_2->diff($datetime2_2);
            $minute_2 = $difference_2->h * 60 + $difference_2->i;
            $minute_2 = $difference_2->format('%r') . $minute_2;
        }

        if ($minute < 0 || $minute_2 < 0) {
            return 'false';
        } else {
            return 'true';
        }
    }
    public function get_list_student()
    {
        $class_lecture_join = $this->input->post('class_lecture_join');
        $id_schedule = $this->input->post('id_schedule');
        $query = $this->db->where('class_lecture_join', $class_lecture_join)
            ->where('id_schedule', $id_schedule)
            ->join('tb_class_student', 'tb_class_student.id_class_student = tb_schedule_student.id_class_student')
            ->join('tb_leads', 'tb_leads.id_leads = tb_class_student.id_leads')
            ->get('tb_schedule_student')
            ->result();
        $data = array();
        foreach ($query as $key) {
            $datax['name'] = $key->name;
            $datax['family_name'] = $key->family_name;
            $data[] = $datax;
        }
        $list_room = $this->list_room_available($class_lecture_join, $id_schedule);
        $kk['data'] = $data;
        $kk['room'] = $list_room;
        echo json_encode($kk);
    }
    public function list_room_available($class_lecture_join, $id_schedule)
    {

        $avail = array();
        $xd = $this->db
            ->where('tb_schedule_detail.class_lecture_join', $class_lecture_join)
            ->where('id_schedule', $id_schedule)
            ->join('tb_class_lecture', 'tb_class_lecture.class_lecture_join = tb_schedule_detail.class_lecture_join')
            ->join('tb_room', 'tb_room.id_room = tb_schedule_detail.id_room')
            ->get('tb_schedule_detail')
            ->row();
        $day = $xd->day;
        $total_student = $this->db->where('id_schedule', $id_schedule)->where('class_lecture_join', $xd->class_lecture_join)->get('tb_schedule_student')->num_rows();
        $durasi_minute = (float) $xd->class_lecture_hours * 60;
        $end = date('H:i', strtotime("+$durasi_minute minutes", strtotime($xd->start)));
        $room = $this->db->where('room_capacity >=', $total_student)->order_by('room_name', 'asc')->get('tb_room')->result();
        foreach ($room as $rooms) {
            $jo = true;
            $query = $this->db
                ->where('id_schedule', $id_schedule)
                ->where('day', $day)
                ->where('start <=', $end)
                ->where('tb_room.id_room', $rooms->id_room)
                ->order_by('start')
                ->join('tb_schedule_detail', 'tb_schedule_detail.id_room = tb_room.id_room')
                ->join('tb_class_lecture', 'tb_class_lecture.class_lecture_join = tb_schedule_detail.class_lecture_join')
                ->get('tb_room')
                ->result();
            foreach ($query as $key) {
                $durasi_minute_2 = (float) $key->class_lecture_hours * 60;
                $end_2 = date('H:i', strtotime("+$durasi_minute_2 minutes", strtotime($key->start)));
                if ($this->cek_jam($xd->start, $xd->class_lecture_hours, $key->start, $end_2) == 'false') {
                    $jo = false;
                }
            }
            if ($jo == true) {
                array_push($avail, $rooms->id_room . '|' . $rooms->room_name);
            }

        }
        return $avail;
    }
    public function move_room()
    {
        $ss['id_room'] = $this->input->post('id_room');
        $this->db->where('id_schedule', $this->input->post('id_schedule'))->where('class_lecture_join', $this->input->post('class_lecture_join'))->update('tb_schedule_detail', $ss);
    }
    public function regenerate($fet)
    {
        $this->fet($fet, 'regenerate');
    }
}
