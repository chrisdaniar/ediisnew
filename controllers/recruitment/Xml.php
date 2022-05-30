<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Xml extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Schedule/Xml_model', 'xml_model');

        if ($this->session->userdata('logged_in') == FALSE) {
            redirect('login');
        }
    }
    public function cekek()
    {
        for ($i=1; $i <= 437 ; $i++) { 
            if($this->db->where('id_form', $i)->get('jos_form_registration')->num_rows() == 0){
                echo $i.'<br>';
            }
        }
        /* $query = ; */
    }
    public function hehe()
    {
        $data = $this->db->where('class_lecture_join !=', 0)
        ->join('tb_main_class', 'tb_main_class.id_main_class = tb_class_lecture.id_main_class' , 'left')
        ->join('tb_detail_course_structure', 'tb_detail_course_structure.id_detail_course_structure = tb_main_class.id_detail_course_structure', 'left')
        ->join('tb_subject', 'tb_subject.id_subject = tb_detail_course_structure.id_subject' , 'left')
        ->join('tb_course_structure', 'tb_course_structure.id_course_structure = tb_detail_course_structure.id_course_structure', 'left')
        ->join('tb_course', 'tb_course.id_course = tb_course_structure.id_course', 'left')
        ->join('db_hr.tb_employee', 'tb_employee.id_employee = tb_class_lecture.id_employee' , 'left')
        ->get('tb_class_lecture')
        ->result();
        echo '<pre>';
        print_r($data);
    }
    public function index()
    {
        $this->load->helper('file');
        /* $data = header('Content-Type: application/xml; charset=utf-8'); */
        $data_subject = $this->xml_model->get_subject();
        $data_teacher = $this->xml_model->get_teacher();
        $data_student = $this->xml_model->get_student();
        $data_activities = $this->xml_model->get_activities();
        $data_room = $this->xml_model->get_room();
        $data_room_tanpa_special = $this->xml_model->get_room('tanpa_special');
        $data_room_special = $this->xml_model->get_room('special');
        $data_time = $this->xml_model->get_time();
        $data_teacher_availability = $this->xml_model->get_teacher_availability();
        $data_ascending_class = $this->xml_model->get_ascending_class();
        $data_special_room = $this->xml_model->get_special_room();
        $data_break_time = $this->xml_model->get_break_time();


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
                <Number_of_Students>".count($data_student)."</Number_of_Students>
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

            $data .= "<Activity>
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
                <Number_of_Not_Available_Times>".count($data_break_time)."</Number_of_Not_Available_Times>";
                foreach ($data_break_time as $break_time) {
                    $data .= "<Not_Available_Time>
                        <Day>".$this->xml_model->format_day($break_time->break_time_day)."</Day>
                        <Hour>".$break_time->time_name."</Hour>
                    </Not_Available_Time>";
                }
        $data .= "
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintStudentsSetNotAvailableTimes>
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
                <Number_of_Preferred_Rooms>$total_data_room_tanpa_special</Number_of_Preferred_Rooms>";
        foreach ($data_room_tanpa_special as $tanpa_special) {
            $data .= "<Preferred_Room>$tanpa_special->room_name</Preferred_Room>";
        }
        $data .= "
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintActivityTagPreferredRooms>
            <ConstraintActivityTagPreferredRooms>
                <Weight_Percentage>100</Weight_Percentage>
                <Activity_Tag>TUTORIAL</Activity_Tag>
                <Number_of_Preferred_Rooms>$total_room</Number_of_Preferred_Rooms>";

        foreach ($data_room as $room) {
            $data .= "<Preferred_Room>$room->room_name</Preferred_Room>";
        }

        $data .= "
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintActivityTagPreferredRooms>
            <ConstraintActivityTagPreferredRooms>
                <Weight_Percentage>20</Weight_Percentage>
                <Activity_Tag>LECTURE</Activity_Tag>
                <Number_of_Preferred_Rooms>$total_room</Number_of_Preferred_Rooms>";

        foreach ($data_room as $room) {
            $data .= "<Preferred_Room>$room->room_name</Preferred_Room>";
        }

        $data .= "
                <Active>true</Active>
                <Comments></Comments>
            </ConstraintActivityTagPreferredRooms>
            "
            ;
        
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
        $data .= "
        </Space_Constraints_List>
        
        </fet>";
        /* header('Content-Type: application/xml; charset=utf-8');
        echo $data; */
        $name_file = date('ymdhis');
        if (!write_file("./xml_fet/$name_file.fet", $data)) {
            echo 'Unable to write the file';
        } else {
            echo 'File written!';
        }
    }
    public function insert_schedule()
    {
        $path = base_url('xml_fet/200220111838_activities.xml');
        $xmlfile = file_get_contents($path);
        $new = simplexml_load_string($xmlfile);
        
        $con = json_encode($new);
        $newArr = json_decode($con, true);

        echo '<pre>';
        /* echo $newArr['activity'][0]['id']; */
        /* foreach($results['data'] as $result) {
            echo $result['type'], '<br>';
        } */
        print_r($newArr['Activity'][0]);
        foreach ($newArr['Activity'] as $key) {
            $schedule['class_lecture_join'] = $key['Id'];
            $schedule['day'] = $this->xml_model->format_day_number($key['Day']);
            $schedule['start'] = substr($key['Hour'],0,5);
            $schedule['id_room'] = $this->db->where('room_name', $key['Room'])->get('tb_room')->row()->id_room;
            print_r($schedule);
            /* print_r($a); */
            /* print_r($key); */
            /* echo $key['id'].'<br>'; */
            
        }
        /* echo '<pre>';
        print_r($newArr); */
    }
    public function kok()
    {
        $path = base_url('xml_fet/file_subgroups.xml');
        $xmlfile = file_get_contents($path);
        $new = simplexml_load_string($xmlfile);
        $con = json_encode($new);
        $newArr = json_decode($con, true);

        echo '<pre>';
        print_r($newArr);
    }
    public function teca()
    {
        $query = $this->db
            
            ->get('tb_break_time')
            ->result();
        echo '<pre>';
        print_r($query);
    }
}
