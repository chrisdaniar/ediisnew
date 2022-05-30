<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Master extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Academic/Academic_master_model','master_model');
		$this->load->model('Marketing/Marketing_master_model','marketing_model');
		$this->load->model('Recruitment/Recruitment_master_model','recruitment_model');
		$this->load->model('Admin/Admin_master_model','admin_model');
		$this->link_terakhir = $this->config->item('link_terakhir');

		if ($this->session->userdata('logged_in') == FALSE) {
			redirect('login');
		}

    	ini_set('display_errors', 0);
	}

	public function cek_student_schedule_backup(){

		$student = $this->input->post('id_student');
		$id_main_class = $this->input->post('id_main_class');

		$sch_desc = $this->db->order_by('id_schedule','desc')->get('tb_schedule_detail')->row();

		if ($sch_desc != null) {

		 $ee = array();
         $ea =array();

		foreach ($student as $id_student) {

		$cek_schedule = $this->db->where('tb_class_student.id_student', $id_student)
								 ->where('tb_schedule_student.id_schedule', $sch_desc->id_schedule)
                                 ->where('class_student_softdel', 0)
								 ->join('tb_class_student','tb_class_student.id_class_student=tb_schedule_student.id_class_student')
								 ->join('tb_schedule_detail','tb_schedule_detail.class_lecture_join=tb_schedule_student.class_lecture_join')
								 ->join('tb_class_lecture','tb_class_lecture.class_lecture_join=tb_schedule_detail.class_lecture_join')
                                 ->join('tb_student','tb_student.id_student=tb_class_student.id_student')
								 ->join('tb_leads','tb_leads.id_leads=tb_student.id_leads')
								 ->group_by('tb_class_lecture.id_class_lecture')
                                 ->get('tb_schedule_student')
                                 ->result();

         $main = $this->db->where('id_main_class', $id_main_class)->get('tb_main_class')->row();
         $all_main = $this->db->where('main_class_join', $main->main_class_join)->get('tb_main_class')->result();

         $lecture = 0;
         $no = 1;
         foreach ($all_main as $key) {
         	if ($no == 1) {
         		$wh = 'where';
         	} else {
         		$wh = 'or_where';
         	}

         	$lecture = $this->db->$wh('id_main_class', $key->id_main_class);

         	$no++;
         }

         $lecture = $this->db->where('class_lecture_type','L')
         					 ->where('class_lecture_join !=',0)
         					 ->group_by('split')
         					 ->order_by('split','desc')
         					 ->get('tb_class_lecture');

         $result_lecture = $lecture->row();

         $tutorial = 0;
         $no = 1;
         foreach ($all_main as $key) {
         	if ($no == 1) {
         		$wh = 'where';
         	} else {
         		$wh = 'or_where';
         	}

         	$tutorial = $this->db->$wh('id_main_class', $key->id_main_class);

         	$no++;
         }

         $tutorial = $this->db->where('class_lecture_type','T')
         					  ->where('class_lecture_join !=',0)
         					  ->group_by('split')
         					  ->order_by('split','desc')
         				      ->get('tb_class_lecture');

         $result_tutorial = $tutorial->row();
       
         $cek_class = $this->db->join('tb_class_lecture','tb_class_lecture.class_lecture_join=tb_schedule_detail.class_lecture_join')
         							  ->where('tb_class_lecture.id_main_class', $result_lecture->id_main_class)
                                      ->where('tb_class_lecture.split', $result_lecture->split)
                                      ->where('tb_class_lecture.class_lecture_type', 'L')
                                      ->where('id_schedule', $sch_desc->id_schedule)
                                      ->or_where('tb_class_lecture.split', $result_tutorial->split)
                                      ->where('tb_class_lecture.id_main_class', $result_tutorial->id_main_class)
                                      ->where('tb_class_lecture.class_lecture_type', 'T')
                                      ->where('id_schedule', $sch_desc->id_schedule)
                                      ->get('tb_schedule_detail')
                                      ->result();

            foreach ($cek_schedule as $key) {

         	$akhir = date('H:i',strtotime('+'.$key->class_lecture_hours.' hour',strtotime($key->start)));

	         	foreach ($cek_class as $sch) {
	         	 	 if ($sch->day == $key->day) {

	         	 	  $checking_lecture = $this->cek_jam($sch->start, $sch->class_lecture_hours, $key->start, $akhir);

	         	 	  if ($checking_lecture == 'false') {
	         	 	  	 $subject = $this->db->join('tb_class_lecture','tb_class_lecture.class_lecture_join=tb_schedule_student.class_lecture_join')
	         	 	  	 				  ->join('tb_class_student','tb_class_student.id_class_student=tb_schedule_student.id_class_student')
	         	 	  	 				  ->join('tb_student','tb_student.id_student=tb_class_student.id_student')
                                          ->join('tb_leads','tb_leads.id_leads=tb_student.id_leads')
	         	 	  	 				  ->join('tb_main_class','tb_main_class.id_main_class=tb_class_lecture.id_main_class')
	         	 	  	 				  ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure')
	         	 	  	 				  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
	         	 	  	 				  ->join('tb_schedule_detail','tb_schedule_detail.class_lecture_join=tb_schedule_student.class_lecture_join')
                                          ->where('class_student_softdel', 0)
	         	 	  	 				  ->where('id_schedule_student', $key->id_schedule_student)
	         	 	  					  ->get('tb_schedule_student')->row();

	         	 	  	  $summary_time = date('H:i:s',strtotime('+'.$subject->class_lecture_hours.' hour',strtotime($subject->start)));
	         	 	  	 $ee['subject'] = $subject;
	         	 	  	 $ee['summary_time'] = $summary_time;
	         	 	  	 $ea[] = $ee;
	         	 	  } 
	         	   }
	           } 
            }

          } 
	    }	else {
	    	$ea = '';
	    }

	    echo json_encode($ea);
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

	public function fet(){
		chdir('D:/My files/jic files/fet/new folder/fet-5.42.3');
		$out = shell_exec('fet-cl --inputfile=schedule1.fet');
		// $out = shell_exec('D:\My Files\JIC FILES\FET\New folder\fet-5.42.3\fet.exe');
		echo '<pre>'.$out.'</pre>';
	}

	 public function autocomplete(){
          $this->load->view('autocomplete_view');
    }
 
    public function search_student(){
 
        $term = $this->input->get('term');
 
        $data = $this->db->join('tb_leads','tb_leads.id_leads=tb_student.id_leads')
                        ->like('name', $term)
        				->or_like('family_name', $term)
        				->or_like('student_id', $term)
        				->or_like('username', $term)
        				->order_by('tb_leads.name','asc')
        				->order_by('tb_student.id_student','desc')
        				->group_by('tb_student.id_leads')
        				->get('tb_student')
        				->result();

        foreach ($data as $row) 
					$result_array[] = array(
						'label' => $row->name.' '.$row->family_name.' - '.$row->student_id.' | '.$row->username,
						'id_leads' => $row->id_leads);
				echo json_encode($result_array);
    }

	public function yoi(){
		$a = $this->db->where('id_aaa >=', 1)->where('id_aaa <=', 250)->get('tb_aaa')->result();
		foreach ($a as $data) {
			$b = $this->db->where('name', $data->given_name)->where('family_name', $data->family_name)->get('tb_leads')->row();
			$param = array('id_leads' => $b->id_leads );
			$this->db->where('id_aaa', $data->id_aaa)
        ->update('tb_aaa', $param);

        	$subject = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_subject=tb_subject.id_subject')
        						->where('subject_code', $data->subject_code)
        						->get('tb_subject')
        						->row();
        	$param_subject = array('subject_code' => $subject->id_detail_course_structure );

        	$this->db->where('id_aaa', $data->id_aaa)
           ->update('tb_aaa', $param_subject);

		}
		echo 'success';
	}

	public function get_autocomplete_student(){
		if(isset($_GET['term'])){
			$result = $this->master_model->get_autocomplete_student($_GET['term']);
			if(count($result) > 0){
				foreach ($result as $row) {

					$result_array[] = array(
						'label' => $row->family_name,
						'id' => $row->id_leads);
				}
				echo json_encode($result_array);
			
			}
		}
	}

	public function get_subject_by_subject() {
		// $layanan =$this->input->post('layanan');
		$id_subject = $this->input->post('id_subject');
		
		$result = $this->db->where('id_subject !=', $id_subject)->get('tb_subject')->result();

		$option = "";
		$option .= '<option value=""> -- Select Subject -- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_subject."' >".$data->subject_code." - ".$data->subject_name."</option>";
		}
		echo $option;

	}

	public function get_subject_course_structure_by_subject() {
		// $layanan =$this->input->post('layanan');
		$id_subject = $this->input->post('id_subject');
		$id_subject_add = $this->input->post('id_subject_add');
		$id_course_structure = $this->input->post('id_course_structure');
		
		$result = $this->db->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
						   ->where('tb_subject.id_subject !=', $id_subject)
						   ->where('tb_subject.id_subject !=', $id_subject_add)
						   ->where('id_course_structure', $id_course_structure)
						   ->where('course_structure_active', '1')
						   ->where('id_subject_add', '')
						   ->where('id_subject_add', ' ')
						   ->get('tb_detail_course_structure')->result();

		$row = $this->db->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject_add')
						   ->where('tb_subject.id_subject', $id_subject_add)
						   ->where('id_course_structure', $id_course_structure)
						   ->get('tb_detail_course_structure')->row();

		$option = "";
		$option .= "<option value='".$row->id_subject."' selected='selected'>".$row->subject_code." - ".$row->subject_name."</option>";
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_subject."' > ".$data->subject_code." - ".$data->subject_name."</option>";
		}
		echo $option;
	}

	public function get_subject_crash() {
		$id_detail_course_structure = $this->input->post('id_detail_course_structure');
		$data = $this->master_model->get_subject_crash($id_detail_course_structure);
		echo json_encode($data);
	}

	public function get_special_room_by_room() {
		$id_room = $this->input->post('id_room');
		$data = $this->master_model->get_special_room_by_room($id_room);
		echo json_encode($data);
	}

	public function get_joined_class() {
		$id_main_class = $this->input->post('id_main_class');
		$data = $this->master_model->get_joined_class($id_main_class);
		echo json_encode($data);
	}

	public function get_total_student_estimation(){
		ini_set('display_errors', 0);
		$post = json_decode($this->input->post('data'));
			$total = 0;
			foreach ($post as $key) {
					$cek = $this->db->select('count(id_class_student) as total')
									->where('id_main_class', $key->id_main_class)
									->get('tb_class_student')
									->row();
					
					$total += $cek->total;
				}	

			echo json_encode($total);
			
	}

	public function get_subject_highest_duration(){
		ini_set('display_errors', 0);
		$post = $this->input->post('data');
			
		$cek = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure')
						->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
						->where_in('id_main_class', $post)
						->order_by('subject_duration','desc')
						->get('tb_main_class')
						->row();

		$highest_duration = $cek->subject_duration;
					
		echo $highest_duration;
			
	}

	 public function get_intended_program_by_program($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_program = $param;
		$result = $this->db->where('id_program', $id_program)->get('tb_intended_program')->result();
		$option = "";
		$option .= '<option value=""> -- Select Intended Program --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intended_program."' >".$data->intended_program."</option>";
		}
		echo $option;

	}

	public function get_intake_month_by_intended_program($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_intended_program = $param;
		$result = $this->db->join('tb_month','tb_month.id_month=tb_intake_month.id_month')
							->where('id_intended_program', $id_intended_program)
							->get('tb_intake_month')->result();
		$option = "";
		$option .= '<option value=""> -- Select Month --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intake_month."' >".$data->month."</option>";
		}
		echo $option;

	}
	public function get_intake_month_by_course($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_course= $param;
		$id_intended_program = $this->db->join('tb_intended_program','tb_intended_program.id_intended_program=tb_course.id_intended_program')
										->where('id_course', $id_course)
										->get('tb_course')->row();

		$result = $this->db->join('tb_month','tb_month.id_month=tb_intake_month.id_month')
							->where('id_intended_program', $id_intended_program->id_intended_program)
							->get('tb_intake_month')->result();
		$option = "";
		$option .= '<option value=""> -- Month --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intake_month."' >".$data->month."</option>";
		}
		echo $option;

	}

	public function get_course_by_intended_program($param = NULL) {
		// $layanan =$this->input->post('layanan');
		$id_intended_program = $param;
		$result = $this->db->where('id_intended_program', $id_intended_program)->get('tb_course')->result();
		$option = "";
		$option .= '<option value=""> -- Select Course --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_course."' >".$data->course."</option>";
		}
		echo $option;

	}

	public function get_intended_program_by_intended_program() {
		$id_program = $this->input->post('id_program');
		$id_intended_program = $this->input->post('id_intended_program');
		
		$result = $this->db->where('id_program', $id_program)->where('id_intended_program !=', $id_intended_program)->get('tb_intended_program')->result();
		$row = $this->db->where('id_intended_program', $id_intended_program)->get('tb_intended_program')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Intended Program -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_intended_program.'" selected="selected"> '.$row->intended_program.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intended_program."' >".$data->intended_program."</option>";
		}
		echo $option;

	}

	public function get_course_by_course() {
		$id_course = $this->input->post('id_course');
		$id_intended_program = $this->input->post('id_intended_program');
		
		$result = $this->db->where('id_intended_program', $id_intended_program)->where('id_course !=', $id_course)->get('tb_course')->result();
		$row = $this->db->where('id_course', $id_course)->get('tb_course')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Course -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_course.'" selected="selected"> '.$row->course.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_course."' >".$data->course."</option>";
		}
		echo $option;

	}

	public function get_course() {
		$id_course = $this->input->post('id_course');
		
		$result = $this->db->where('id_course !=', $id_course)->get('tb_course')->result();
		$row = $this->db->where('id_course', $id_course)->get('tb_course')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Course -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_course.'" selected="selected"> '.$row->course.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_course."' >".$data->course."</option>";
		}
		echo $option;

	}

	public function get_intake_month_by_intake_month() {
		$id_intake_month = $this->input->post('id_intake_month');
		$id_intended_program = $this->input->post('id_intended_program');
		
		$result = $this->db->join('tb_month','tb_month.id_month=tb_intake.id_month')
						   ->where('id_intended_program', $id_intended_program)->where('id_intake_month !=', $id_intake_month)->get('tb_intake_month')->result();
		$row = $this->db->where('id_intake_month', $id_intake_month)->get('tb_intake_month')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Month -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_intake_month.'" selected="selected"> '.$row->month.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intake_month."' >".$data->month."</option>";
		}
		echo $option;

	}

	public function get_intake_month_by_intake_month_course() {
		$id_intake_month = $this->input->post('id_intake_month');
		$id_course = $this->input->post('id_course');

		$cek_intended_program = $this->db->join('tb_intended_program','tb_intended_program.id_intended_program=tb_course.id_intended_program')
										->where('id_course', $id_course)
										->get('tb_course')->row();

		$id_intended_program = $cek_intended_program->id_intended_program;
		
		$result = $this->db->join('tb_month','tb_month.id_month=tb_intake_month.id_month')
						   ->where('id_intended_program', $id_intended_program)
						   ->where('id_intake_month !=', $id_intake_month)
						   ->get('tb_intake_month')->result();

		$row = $this->db->join('tb_month','tb_month.id_month=tb_intake_month.id_month')->where('id_intake_month', $id_intake_month)->get('tb_intake_month')->row();

		if ($row == null) {
			$option = "";
			$option .= '<option value="" selected="selected"> -- Select Month -- </option>';
		} else {
			$option = "";
			$option .= '<option value="'.$row->id_intake_month.'" selected="selected"> '.$row->month.' </option>';
		}
		
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_intake_month."' >".$data->month."</option>";
		}
		echo $option;

	}

	public function get_teaching_period_by_year() {

		$academic_year = $this->input->post('academic_year');
		$result = $this->db->where('year(teaching_period_end)', $academic_year)
						   ->get('tb_teaching_period')
						   ->result();
		$option = "";
		$option .= '<option value=""> -- Select Teaching Period -- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_teaching_period."' >(".date('M', strtotime($data->teaching_period_end)).") - ".date('d M Y', strtotime($data->teaching_period_start))." - ".date('d M Y', strtotime($data->teaching_period_end))."</option>";
		}
		echo $option;

	}

	public function get_schedule_by_date_and_type() {

		$attendance_date = $this->input->post('attendance_date');
		$attendance_type = $this->input->post('attendance_type');
		$main_class_join = $this->input->post('main_class_join');

		$result = $this->db->join('tb_class_lecture','tb_class_lecture.class_lecture_join=tb_schedule_detail.class_lecture_join')
						   ->join('tb_main_class','tb_main_class.id_main_class=tb_class_lecture.id_main_class')
						   ->join('tb_day','tb_day.id_day=tb_schedule_detail.day')
						   ->join('tb_room','tb_room.id_room=tb_schedule_detail.id_room');

		if ($attendance_type == 'Regular') {
			$result = $this->db->where('day', date('N', strtotime($attendance_date)));
		}

		if ($attendance_type == 'Replacement') {
			# code...
		} else {

			if ($this->session->userdata('id_level') == '22') {

				$result = $this->db->where('id_employee', $this->session->userdata('id_employee'));

			}
		}

		$result = $this->db->where('tb_main_class.main_class_join', $main_class_join)		
						   ->get('tb_schedule_detail')
						   ->result();


		$option = "";
		$option .= '<option value=""> -- Select Schedule--- </option>';
		
		foreach ($result as $data) {

			$check_attendance = $this->db->where('id_schedule_detail', $data->id_schedule_detail)
										 ->get('tb_class_attendance')
										 ->num_rows();

      $check_filled_attendance = $this->db->where('id_schedule_detail', $data->id_schedule_detail)
                                          ->where('attendance_date', $this->admin_model->format_tanggal($attendance_date))
                                          ->where('attendance_type', $attendance_type)
                                          ->get('tb_class_attendance')
                                          ->row();

			if ($attendance_type == 'Additional') {

				if ($check_attendance >= $data->meeting_total) {
            
					     $option .= "<option value='".$data->id_schedule_detail."' >".$data->day_name." | ".date('H:i', strtotime($data->start))." - ". date('H:i',strtotime('+'.($data->class_lecture_hours * 60).' minute',strtotime($data->start))) ." (". $data->room_name .") | ". $data->class_lecture_type ." - ". $data->split."</option>";

					} else {

              if ($check_filled_attendance != null) {

                  $option .= "<option value='".$data->id_schedule_detail."' >".$data->day_name." | ".date('H:i', strtotime($data->start))." - ". date('H:i',strtotime('+'.($data->class_lecture_hours * 60).' minute',strtotime($data->start))) ." (". $data->room_name .") | ". $data->class_lecture_type ." - ". $data->split."</option>";

              }
					}

				} else {

					if ($check_attendance < $data->meeting_total OR $data->meeting_total == 0) {

						    $option .= "<option value='".$data->id_schedule_detail."' >".$data->day_name." | ".date('H:i', strtotime($data->start))." - ". date('H:i',strtotime('+'.($data->class_lecture_hours * 60).' minute',strtotime($data->start))) ." (". $data->room_name .") | ". $data->class_lecture_type ." - ". $data->split."</option>";

					} else {

              if ($check_filled_attendance != null) {

                  $option .= "<option value='".$data->id_schedule_detail."' >".$data->day_name." | ".date('H:i', strtotime($data->start))." - ". date('H:i',strtotime('+'.($data->class_lecture_hours * 60).' minute',strtotime($data->start))) ." (". $data->room_name .") | ". $data->class_lecture_type ." - ". $data->split."</option>";
                  
              }
					}
			}
		}
		echo $option;

	}

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function subject($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Subject';
			 $data['course'] = $this->marketing_model->get_course();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_subject($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_subject($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/subject_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_subject();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_subject();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_subject($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_subject($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_subject';
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
            $query = $this->db->like('subject_name', $get['search'])
            				  ->or_like('subject_code', $get['search'])
            				  ->or_like('subject_credit', $get['search'])
            				  ->or_like('subject_duration', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_subject.subject_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_subject')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

    public function room($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Room';
			 $data['campus'] = $this->marketing_model->get_campus();
			 $data['subject'] = $this->master_model->get_subject();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_room($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_room($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/room_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_room();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_room();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_room($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} elseif ($param1 == 'add_special_room') {
			$aksi = $this->master_model->add_special_room();
            echo $aksi;
		} elseif ($param1 == 'delete_special_room') {
			$id_special_room = $this->input->post('id_special_room');
			$aksi = $this->master_model->delete_special_room($id_special_room);
			echo $aksi;
		} else {

		}
	}

	 public function pagination_room($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_room';
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
            $query = $this->db->like('room_name', $get['search'])
            				  ->or_like('subject_code', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_room.room_updated_by','left')
        				  ->join('tb_campus','tb_campus.id_campus=tb_room.id_campus','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_room')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

    public function course_structure($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Course Structure';
			 $data['course'] = $this->marketing_model->get_course();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_course_structure($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_course_structure($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/course_structure_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_course_structure();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_course_structure();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_course_structure($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_course_structure($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_course_structure';
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
            $query = $this->db->like('course_structure_name', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_course_structure.course_structure_updated_by','left')
        				  ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
        				  ->join('tb_intake_month','tb_intake_month.id_intake_month=tb_course_structure.id_intake_month','left')
        				   ->join('tb_month','tb_month.id_month=tb_intake_month.id_month','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_course_structure')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function detail_course_structure($param1 = '', $param2 = ''){
		if ($param1 == 'view') {
			 $id_course_structure = $param2;
			 $data['title'] = 'Course Structure';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['subject'] = $this->master_model->get_subject();
		     $data['subject_cs'] = $this->master_model->get_subject_course_structure($id_course_structure);
		     $data['course_structure'] = $this->master_model->get_course_structure_by_id($id_course_structure);
		     $data['pagination_data'] = $this->pagination_detail_course_structure($this->input->get(), 'result', $id_course_structure);
		     $data['pagination_total_page'] = $this->pagination_detail_course_structure($this->input->get(), 'num_rows', $id_course_structure);
		     $this->load->view('Academic/Master/detail_course_structure_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_detail_course_structure();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_detail_course_structure();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_detail_course_structure($id);
			}
			redirect($url);
		} elseif ($param1 == 'delete_subject_add') {
			$aksi = $this->master_model->delete_subject_add();
			echo $aksi;
		} elseif ($param1 == 'add_subject_crash') {
			$aksi = $this->master_model->add_subject_crash();
            echo $aksi;
		} elseif ($param1 == 'delete_subject_crash') {
			 $id_subject_crash = $this->input->post('id_subject_crash');
			 $cek = $this->db->where('id_subject_crash', $id_subject_crash)
                            ->get('tb_subject_crash')
                            ->row();
			$aksi = $this->master_model->delete_subject_crash($cek->id_detail_course_structure, $cek->id_detail_course_structure_crash);
            echo $aksi;
		} elseif ($param1 == 'grouping') {
			$aksi = $this->master_model->grouping();
            echo $aksi;
		} elseif ($param1 == 'delete_grouping') {
			$id_detail_course_structure = $this->input->post('id_detail_course_structure');
			$aksi = $this->master_model->delete_grouping($id_detail_course_structure);
            echo $aksi;
		} elseif ($param1 == 'selection') {
			$aksi = $this->master_model->selection();
            echo $aksi;
		} elseif ($param1 == 'delete_selection') {
			$id_detail_course_structure = $this->input->post('id_detail_course_structure');
			$aksi = $this->master_model->delete_selection($id_detail_course_structure);
            echo $aksi;
		}
	}

	public function pagination_detail_course_structure($get = [], $param = 'result', $id_course_structure = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 50;
        if(!isset($get['sortby'])) $get['sortby'] = 'trimester_course_structure';
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
            $query = $this->db->where('id_course_structure', $id_course_structure)
            				  ->like('tb_subject_1.subject_name', $get['search'])
            				  ->or_like('tb_subject_2.subject_name', $get['search'])
            				  ->or_like('tb_subject_1.subject_code', $get['search'])
            				  ->or_like('tb_subject_2.subject_code', $get['search']);
        }

        $query = $this->db->select('*, tb_subject_1.subject_name as subject_name_1, tb_subject_1.subject_code as subject_code_1, tb_subject_1.subject_duration as subject_duration_1, tb_subject_2.subject_name as subject_name_2, tb_subject_2.subject_code as subject_code_2,tb_subject_2.subject_duration as subject_duration_2,')
        				  ->join('tb_subject as tb_subject_2','tb_subject_2.id_subject=tb_detail_course_structure.id_subject_add','left')
        				  ->join('tb_subject as tb_subject_1','tb_subject_1.id_subject=tb_detail_course_structure.id_subject','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_detail_course_structure.detail_course_structure_updated_by','left')
        				  ->where('id_course_structure', $id_course_structure)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->order_by('subject_name_1', 'asc')
        				  //->order_by('subject_name_1', 'asc')
        				  //->group_by('tb_school.id_school')
                          ->get('tb_detail_course_structure')->$param();

        return $query;
    }

    public function get_autocomplete_subject(){
		if(isset($_GET['term'])){
			$result = $this->master_model->get_autocomplete_subject($_GET['term']);
			if(count($result) > 0){
				foreach ($result as $row) {


					$result_array[] = array(
						'label' => $row->subject_code.' - '.$row->subject_name,
						'id' => $row->id_subject);
				}
				echo json_encode($result_array);
			
			}
		}
	}

	 //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

    public function teacher($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Teacher';
			 $data['employee_type'] = $this->master_model->get_employee_type();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_teacher($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_teacher($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Teacher/teacher_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_teacher();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_teacher();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_teacher($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	 public function pagination_teacher($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_employee';
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
            $query = $this->db->like('employee_name', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee_type','tb_employee_type.id_employee_type=tb_employee.id_employee_type','left')
        				  ->join('tb_campus','tb_campus.id_campus=tb_employee.id_campus','left')
        				  ->where('lecturer','1')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('db_hr.tb_employee')->$param();
                          
        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function teacher_subject($param1 = '', $param2 = ''){
		if ($param1 == 'view') {
			 $id_employee = $param2;
			 $data['title'] = 'Teacher Subject';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['subject'] = $this->master_model->get_subject();
		     $data['teacher'] = $this->master_model->get_employee_by_id($id_employee);
		     $data['pagination_data'] = $this->pagination_teacher_subject($this->input->get(), 'result', $id_employee);
		     $data['pagination_total_page'] = $this->pagination_teacher_subject($this->input->get(), 'num_rows', $id_employee);
		     $this->load->view('Academic/Teacher/teacher_subject_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_teacher_subject();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_teacher_subject();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_teacher_subject($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_teacher_subject($get = [], $param = 'result', $id_employee = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 50;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_teacher_subject';
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
            $query = $this->db->like('subject_name', $get['search']);
        }

        $query = $this->db->join('tb_subject','tb_subject.id_subject=tb_teacher_subject.id_subject','left')
        				  ->where('id_employee', $id_employee)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_teacher_subject')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function teacher_availability($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Teacher Availability';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['teacher'] = $this->master_model->get_teacher();
		     $data['pagination_data'] = $this->pagination_teacher_availability($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_teacher_availability($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Teacher/teacher_availability_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} if ($param1 == 'detail') {
			 $id_teacher_trimester = $param2;
			 $data['title'] = 'Teacher Availability';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['active_time'] = $this->master_model->get_active_time();
		     $data['teacher_trimester'] = $this->master_model->get_teacher_trimester($id_teacher_trimester);
		     $this->load->view('Academic/Teacher/detail_teacher_availability_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add_teacher') {
			$aksi = $this->master_model->add_teacher_trimester();
            echo $aksi;
		} elseif ($param1 == 'edit_teacher') {
			$aksi = $this->master_model->edit_teacher_trimester();
            echo $aksi;
		} elseif ($param1 == 'delete_teacher') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_teacher_trimester($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} elseif ($param1 == 'add_teacher_availability') {
			$aksi = $this->master_model->add_teacher_availability();
            echo $aksi;

		} elseif ($param1 == 'delete_teacher_availability') {
			$id_teacher_availability = $this->input->post('id_teacher_availability');
			$this->master_model->delete_teacher_availability($id_teacher_availability);
		} elseif ($param1 == 'activate_availability') {
			echo $this->input->post('teacher_active');
			$this->master_model->activate_teacher_availability();
		} else {
			
		}
	}

	public function pagination_teacher_availability($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 50;
        if(!isset($get['sortby'])) $get['sortby'] = 'employee_name';
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
            $query = $this->db->like('employee_name', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_teacher_trimester.id_employee')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_teacher_trimester')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function class_lecture($param1 = '', $param2 = '', $param3 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Class Lecture';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['teacher'] = $this->master_model->get_teacher();
		     $data['course'] = $this->recruitment_model->get_course();
		     $data['session'] = $this->master_model->get_session();
		     $data['campus'] = $this->marketing_model->get_campus();
		     $data['subject'] = $this->master_model->get_subject();
		     $data['academic_year'] = $this->recruitment_model->get_academic_year();
		     $data['teacher_hours'] = $this->master_model->get_teacher_hours();
		     $data['course_structure'] = $this->master_model->get_course_structure();
		     $data['check_requested_class'] = $this->master_model->check_requested_class();
		     $data['check_requested_score'] = $this->master_model->check_requested_score();
		     $data['pagination_data'] = $this->pagination_main_class($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_main_class($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Activity/class_lecture_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'requested_class') {
			 $data['title'] = 'Requested Class';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['course'] = $this->recruitment_model->get_course();
		     $data['subject'] = $this->master_model->get_subject();
		     $data['academic_year'] = $this->recruitment_model->get_academic_year();
		     $data['pagination_data'] = $this->pagination_requested_class($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_requested_class($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Activity/requested_class_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'requested_score') {
			 $data['title'] = 'Requested Score';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['course'] = $this->recruitment_model->get_course();
		     $data['subject'] = $this->master_model->get_subject();
		     $data['academic_year'] = $this->recruitment_model->get_academic_year();
		     $data['pagination_data'] = $this->pagination_requested_score($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_requested_score($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Activity/requested_score_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'schedule_for_today') {
	         $data['title'] = 'Schedule';
	         $data['left_bar'] = $this->admin_model->check_navbar();
	         $data['course'] = $this->recruitment_model->get_course();
	         $data['campus'] = $this->master_model->get_campus();
	         $data['day'] = $this->master_model->get_day();
	         $data['employee'] = $this->admin_model->get_employee_by_id($this->session->userdata('id_employee'));
	         $data['pagination_data'] = $this->pagination_schedule_for_today($this->input->get(), 'result', $data['employee']->id_employee, $data['employee']->lecturer);
	         $data['pagination_total_page'] = $this->pagination_schedule_for_today($this->input->get(), 'num_rows', $data['employee']->id_employee, $data['employee']->lecturer);
	         $this->load->view('Academic/Activity/schedule_for_today_view', $data);
	         $this->session->set_userdata('previous_url', $this->link_terakhir);
    } else if ($param1 == 'student') {
			 $id_main_class = $param2;
			 $main_class = $this->master_model->get_main_class_by_id($id_main_class);
			 $data['title'] = 'Class Lecture';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['main_class'] = $this->master_model->get_main_class_by_id($id_main_class);
		     $data['leads'] = $this->master_model->get_student_for_class($main_class->id_course, $id_main_class);

		     $data['class_student'] = $this->master_model->get_student_by_main_class($id_main_class);
		     $data['student_total'] = $this->master_model->get_total_student_by_class($id_main_class);
		     $data['pagination_data'] = $this->pagination_detail_class_student($this->input->get(), 'result', $id_main_class);
		     $data['pagination_total_page'] = $this->pagination_detail_class_student($this->input->get(), 'num_rows', $id_main_class);
		     $this->load->view('Academic/Activity/student_class_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		}  else if ($param1 == 'schedule') {
			 $id_main_class = $param2;
			 $main_class = $this->master_model->get_main_class_by_id($id_main_class);
			 $data['title'] = 'Schedule';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['teacher'] = $this->master_model->get_teacher();
		     $data['room'] = $this->master_model->get_room();
		     $data['class_student'] = $this->master_model->get_class_student_by_main_class_join($main_class->main_class_join);
		     $data['main_class'] = $this->master_model->get_main_class_by_id($id_main_class);
		     $data['student_total'] = $this->master_model->get_total_student_by_class($id_main_class);
		     $data['pagination_data'] = $this->pagination_class_lecture($this->input->get(), 'result', $main_class->main_class_join);
		     $data['pagination_total_page'] = $this->pagination_class_lecture($this->input->get(), 'num_rows', $main_class->main_class_join);
		     $this->load->view('Academic/Activity/schedule_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		}  else if ($param1 == 'specialist') {
			 $id_main_class = $param2;
			 $main_class = $this->master_model->get_main_class_by_id($id_main_class);
			 $data['title'] = 'Specialist';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['main_class'] = $this->master_model->get_main_class_by_id($id_main_class);
		     $data['class_specialist'] = $this->master_model->get_specialist_for_class($main_class->id_course, $id_main_class);
		     $data['student_total'] = $this->master_model->get_total_student_by_class($id_main_class);
		     $data['pagination_data'] = $this->pagination_class_specialist($this->input->get(), 'result', $id_main_class);
		     $data['pagination_total_page'] = $this->pagination_class_specialist($this->input->get(), 'num_rows', $id_main_class);
		     $this->load->view('Academic/Activity/class_specialist_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} else if ($param1 == 'attendance') {
			 $id_main_class = $param2;
			 $main_class = $this->master_model->get_main_class_by_id($id_main_class);
			 $data['title'] = 'Attendance';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['main_class'] = $this->master_model->get_main_class_by_id($id_main_class);
		     $data['schedule'] = $this->master_model->get_schedule_by_main_class($id_main_class);
             $data['student_total'] = $this->master_model->get_total_student_by_class($id_main_class);
             $data['check_teacher_in_class_lecture'] = $this->master_model->check_teacher_in_class_lecture($id_main_class);
		     $this->load->view('Academic/Activity/class_attendance_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} else if ($param1 == 'attendance_history') {
             $id_main_class = $param2;
             $main_class = $this->master_model->get_main_class_by_id($id_main_class);
             $data['title'] = 'Attendance History';
             $data['left_bar'] = $this->admin_model->check_navbar();
             $data['main_class'] = $this->master_model->get_main_class_by_id($id_main_class);
             $data['schedule'] = $this->master_model->get_schedule_by_main_class($id_main_class);
             $data['student_total'] = $this->master_model->get_total_student_by_class($id_main_class);
             $data['all_student'] = $this->master_model->get_student_by_main_class_join($main_class->main_class_join);
             $data['check_teacher_in_class_lecture'] = $this->master_model->check_teacher_in_class_lecture($id_main_class);
             $this->load->view('Academic/Activity/attendance_history_view', $data);
             $this->session->set_userdata('previous_url', $this->link_terakhir);
        } elseif ($param1 == 'setting') {
        	 $id_main_class = $param2;
        	 $data['title'] = 'Class Lecture Setting';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['main_class'] = $this->master_model->get_main_class_by_id($id_main_class);
		     $data['teacher'] = $this->master_model->get_teacher();
		     $data['session'] = $this->master_model->get_session();
		     $data['campus'] = $this->marketing_model->get_campus();
		     $data['academic_year'] = $this->recruitment_model->get_academic_year();
		     $data['course_structure'] = $this->master_model->get_course_structure();
		     $this->load->view('Academic/Activity/main_class_setting_view', $data);

        } else if ($param1 == 'display_table_attendance') {
			  //ini_set('display_errors', 0);

			  $id_main_class = $param2;
			  $attendance = $this->input->post('attendance_date');
			  $attendance_type = $this->input->post('attendance_type');
			  $visit = $this->input->post('visit');

			  if ($attendance_type == '') {
        	  	 redirect('academic/master/class_lecture/attendance/'.$id_main_class);
        	  }

			  $id_schedule_detail = $this->input->post('id_schedule_detail');

			  $attendance_date = $this->admin_model->format_tanggal($attendance);
			  $main_class = $this->master_model->get_main_class_by_id($id_main_class);

			  $data['title'] = 'Attendance';
              $data['left_bar'] = $this->admin_model->check_navbar();
			  $data['main_class'] = $this->master_model->get_main_class_by_id($id_main_class);
			  $data['student_total'] = $this->master_model->get_total_student_by_class($id_main_class);
			  $data['student'] = $this->master_model->get_student_by_schedule($id_schedule_detail);
              $data['attendance_class'] = $this->master_model->get_attendance_detail($id_main_class, $attendance_date, $id_schedule_detail, $attendance_type);
              $data['schedule_detail'] = $this->master_model->get_schedule_by_id($id_schedule_detail);
              $data['check_teacher_in_class_lecture'] = $this->master_model->check_teacher_in_class_lecture($id_main_class);
			  $data['id_main_class'] = $id_main_class;
			  $data['id_schedule_detail'] = $id_schedule_detail;
			  $data['attendance_type'] = $attendance_type;
			  $data['attendance_date'] = $attendance;
			  $data['visit'] = $visit;

		      $data['schedule'] = $this->master_model->get_schedule_by_main_class($id_main_class);

	         $this->load->view('Academic/Activity/display_class_attendance_view_new', $data);
		} else if ($param1 == 'edit_class_attendance') {

			  $id_main_class = $this->uri->segment(5);
			  $id_class_attendance = $this->uri->segment(6);
			  $attendance_type = $this->uri->segment(7);
			  $class_attendance = $this->master_model->get_class_attendance_by_id($id_class_attendance);
			  $attendance_date = $class_attendance->attendance_date;
			  $attendance_type = $class_attendance->attendance_type;
			  $id_schedule_detail = $class_attendance->id_schedule_detail;
			  $visit = $class_attendance->visit;

			  $main_class = $this->master_model->get_main_class_by_id($id_main_class);

			  $data['title'] = 'Attendance';
              $data['left_bar'] = $this->admin_model->check_navbar();
			  $data['main_class'] = $this->master_model->get_main_class_by_id($id_main_class);
			  $data['student_total'] = $this->master_model->get_total_student_by_class($id_main_class);
			  $data['student'] = $this->master_model->get_student_by_schedule($id_schedule_detail);
              $data['attendance_class'] = $this->master_model->get_attendance_detail($id_main_class, $attendance_date, $id_schedule_detail, $attendance_type);
              $data['check_teacher_in_class_lecture'] = $this->master_model->check_teacher_in_class_lecture($id_main_class);
          	  $data['schedule_detail'] = $this->master_model->get_schedule_by_id($id_schedule_detail);
			  $data['id_main_class'] = $id_main_class;
			  $data['id_schedule_detail'] = $id_schedule_detail;
			  $data['attendance_type'] = $attendance_type;
			  $data['attendance_date'] = $attendance_date;
			  $data['visit'] = $visit;

		      $data['schedule'] = $this->master_model->get_schedule_by_main_class($id_main_class);

	         $this->load->view('Academic/Activity/display_class_attendance_view_new', $data);
		} else if ($param1 == 'display_student_by_main_class_join') {

              ini_set('display_errors', 0);

              $main_class_join = $this->input->post('main_class_join');
              $data['id_schedule_detail'] = $this->input->post('id_schedule_detail');
              $data['student'] = $this->master_model->get_student_by_main_class_join($main_class_join);
              $display = $this->load->view('Academic/Activity/display_student_by_main_class_join_view', $data);
             
             echo $display;
        } else if ($param1 == 'score') {
			 $id_main_class = $param2;
			 $main_class = $this->master_model->get_main_class_by_id($id_main_class);
			 $data['title'] = 'Score';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['main_class'] = $this->master_model->get_main_class_by_id($id_main_class);
		     $data['score'] = $this->master_model->get_score();
		     $data['class_student'] = $this->master_model->get_student_by_main_class($id_main_class);
		     $data['class_score'] = $this->master_model->get_class_score_by_main_class($id_main_class);
             $data['student_total'] = $this->master_model->get_total_student_by_class($id_main_class);
		     $this->load->view('Academic/Activity/class_score_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} else if ($param1 == 'score_form') {
			 $id_main_class = $param2;
			 $id_class_score = $param3;
			 $main_class = $this->master_model->get_main_class_by_id($id_main_class);
			 $data['title'] = 'Score';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['main_class'] = $this->master_model->get_main_class_by_id($id_main_class);
		     $data['score'] = $this->master_model->get_score();
		     $data['class_student'] = $this->master_model->get_student_by_main_class($id_main_class);
		     $data['class_score'] = $this->master_model->get_class_score_by_filter($id_main_class, $id_class_score);
             $data['student_total'] = $this->master_model->get_total_student_by_class($id_main_class);
		     $this->load->view('Academic/Activity/class_score_form_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'get_schedule_for_copy_student') {

            $result = $this->master_model->get_schedule_for_copy_student();
            $option = "";
            $option .= '<option value=""> -- Select Schedule --- </option>';
            foreach ($result as $data) {


                $option .= "<option value='".$data->id_schedule_detail."' > ".$data->class_lecture_type." | ".$data->day_name." ".date('H:i', strtotime($data->start))." - ".date('H:i',strtotime('+'.($data->class_lecture_hours * 60).' minutes',strtotime($data->start)))." | ".$data->split." </option>";
            }
            echo $option;

        }  elseif ($param1 == 'save_attendance') {
			$aksi = $this->master_model->save_attendance();
		    echo $aksi;
		}  elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_class_lecture();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_class_lecture();
            echo $aksi;
		} elseif ($param1 == 'edit_setting') {
			$aksi = $this->master_model->edit_setting_main_class();
            echo $aksi;
		} elseif ($param1 == 'edit_main_class') {
			$aksi = $this->master_model->edit_main_class();
            echo $aksi;
		} elseif ($param1 == 'add_class_specialist') {
			echo $this->input->post('id_main_class');
			$aksi = $this->master_model->add_class_specialist();
            echo $aksi;
		} elseif ($param1 == 'edit_class_specialist') {
			$aksi = $this->master_model->edit_class_specialist();
            echo $aksi;
		} elseif ($param1 == 'edit_schedule') {
			$aksi = $this->master_model->edit_schedule();
            echo $aksi;
		} elseif ($param1 == 'submit_score') {

			$id_main_class = $this->input->post('id_main_class');
			$submit_score_status = $this->input->post('submit_score_status');
			$submit_score_notes = $this->input->post('submit_score_notes');

			$aksi = $this->master_model->submit_score($id_main_class, $submit_score_status, $submit_score_notes);
            echo $aksi;
		} elseif ($param1 == 'add_class_join') {
			ini_set('display_errors', 0);

            $post = json_decode($this->input->post('data'));
            $id_main_class = $this->input->post('joins');
            $main_class_join = $this->input->post('main_class_join');

			$str = 1;
			$class = 0;
			foreach ($post as $key) {
            $jk = 'where';
            if ($str > 1) {
                $jk = 'or_where';
            }

            $class = $this->db->$jk('id_main_class', $key->id_main_class);
 
            $str++;
        }

        	 $class = $this->db->order_by('main_class_estimation','desc')
            				  ->get('tb_main_class')
            				  ->row();

            foreach ($post as $key) {

            	if ($main_class_join == '') {
            		$data['main_class_join'] = $class->id_main_class;
            	} else {
            		$data['main_class_join'] = $main_class_join;
            	}
            	
            	$data['main_class_updated_by'] = $this->session->userdata('id_employee');
            	$data['main_class_last_updated'] = date('Y-m-d H:i:s');

				$this->db->where('id_main_class', $key->id_main_class)
                         ->update('tb_main_class', $data);

                $main_class = $this->db->where('id_main_class', $class->id_main_class)
                					   ->get('tb_main_class')->row();

                $cl['main_class_join'] = $main_class->main_class_join;

                $this->db->where('id_main_class', $key->id_main_class)
                		 ->update('tb_class_lecture', $cl);
            }

            echo 'true';
            
		} elseif ($param1 == 'edit_join') {
			$aksi = $this->master_model->edit_class_join();
            echo $aksi;
		} elseif ($param1 == 'add_student') {

			$student = $this->input->post('id_student');
			$id_main_class = $this->input->post('id_main_class');

			foreach ($student as $id_student) {
				$aksi = $this->master_model->add_class_student($id_student, $id_main_class);
			}

			echo $aksi;

		} elseif ($param1 == 'split_class') {
			$aksi = $this->master_model->add_split_class();
			echo $aksi;
		} elseif ($param1 == 'delete_student') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_class_student($id);
			}
			redirect($url);
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_main_class($id);
			}
			redirect($url);
		} elseif ($param1 == 'delete_class_lecture') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_class_lecture($id);
			}
			redirect($url);
		} elseif ($param1 == 'delete_class_specialist') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_class_specialist($id);
			}
			redirect($url);
		} elseif ($param1 == 'delete_class_join') {
			$id_main_class = $this->input->post('id_main_class');
			$aksi = $this->master_model->delete_class_join($id_main_class);
			echo $aksi;
		} else if ($param1 == 'get_student_by_schedule') {
			$main_class_join = $this->input->post('main_class_join');
			$data = $this->master_model->get_student_by_main_class_join($main_class_join);
			echo json_encode($data);
		} elseif ($param1 == 'add_schedule_student') {
			$aksi = $this->master_model->add_schedule_student();
            echo $aksi;

		} elseif ($param1 == 'delete_schedule_student') {
			$id_schedule_student = $this->input->post('id_schedule_student');
			$aksi = $this->master_model->delete_schedule_student($id_schedule_student);
			echo $aksi;
		} elseif ($param1 == 'save_copy_student') {
            $aksi = $this->master_model->save_copy_student();
            echo $aksi;
        } elseif ($param1 == 'edit_attendance_date') {
            $aksi = $this->master_model->edit_attendance_date();
            echo $aksi;
        } elseif ($param1 == 'delete_attendance_date') {
            $aksi = $this->master_model->delete_attendance_date();
            echo $aksi;
        } elseif ($param1 == 'edit_lecturer_attendance') {
            $aksi = $this->master_model->edit_lecturer_attendance();
            echo $aksi;
        } elseif ($param1 == 'add_class_score') {
            $aksi = $this->master_model->add_class_score();
            echo $aksi;
        } elseif ($param1 == 'edit_class_score') {
            $aksi = $this->master_model->edit_class_score();
            echo $aksi;
        } elseif ($param1 == 'edit_visit') {
            $aksi = $this->master_model->edit_visit();
            echo $aksi;
        } elseif ($param1 == 'delete_class_score') {
        	$id_class_score = $this->input->post('id_class_score');
            $aksi = $this->master_model->delete_class_score($id_class_score);
            echo $aksi;
        } elseif ($param1 == 'save_student_score') {

        	$student_point = $this->input->post('student_point');
            $id_class_student = $this->input->post('id_class_student');
            $id_class_score = $this->input->post('id_class_score');
            $student_score_notes = $this->input->post('student_score_notes');

            $aksi = $this->master_model->save_student_score($student_point, $id_class_student, $id_class_score, $student_score_notes);
            
            echo $aksi;
        } elseif ($param1 == 'save_attendance_academic_approval') {
            $aksi = $this->master_model->save_attendance_academic_approval();
            echo $aksi;
        } else {
			
		}
	}

	public function pagination_main_class($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_main_class.id_main_class';
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

        	if ($this->session->userdata('id_level') == '22') {
        	    $query = $this->db->like('subject_name', $get['search'])
            				  	  ->where('tb_class_lecture.id_employee', $this->session->userdata('id_employee'))
            				  	  ->or_like('subject_code', $get['search'])
            				  	  ->where('tb_class_lecture.id_employee', $this->session->userdata('id_employee'));
        	} else {
        		$query = $this->db->like('subject_name', $get['search'])
            				  ->or_like('subject_code', $get['search'])
            				  ->or_like('employee_name', $get['search'])
            				  ->or_like('id_kp', $get['search']);
        	}
        }

        if (isset($get['id_course_filter'])) {
            $query = $this->db->where('tb_course.id_course', $get['id_course_filter']);
        }

        if (isset($get['id_campus_filter'])) {
            $query = $this->db->where('tb_campus.id_campus', $get['id_campus_filter']);
        }

        if (isset($get['trimester_filter'])) {
            $query = $this->db->where('tb_trimester.trimester', $get['trimester_filter']);
        } else {
        	if ($this->session->userdata('id_level') == '22') {
        		$query = $this->db->where('trimester_start_date <=', date('Y-m-d'))
        					      ->where('trimester_end_date >=', date('Y-m-d'));
        	}
        }

        if (isset($get['id_subject_filter'])) {
            $query = $this->db->where('tb_subject.id_subject', $get['id_subject_filter']);
        }
        if (isset($get['id_academic_year_filter'])) {
            $query = $this->db->where('tb_academic_year.id_academic_year', $get['id_academic_year_filter']);
        } else {
        	if ($this->session->userdata('id_level') == '22') {
        		$query = $this->db->where('trimester_start_date <=', date('Y-m-d'))
        					      ->where('trimester_end_date >=', date('Y-m-d'));
        	}
        }

        if ($this->session->userdata('id_level') == '22') {
        	$query = $this->db->where('tb_class_lecture.id_employee', $this->session->userdata('id_employee'));
        }

        $query = $this->db->select('*, tb_main_class.id_main_class, tb_main_class.main_class_join')
        				  ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
        				  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
        				  ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester','left')
        				  ->join('tb_semester','tb_semester.id_semester=tb_main_class.id_semester','left')
        				  ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
        				  ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
        				  ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year','left')
        				  ->join('tb_class_lecture','tb_class_lecture.main_class_join=tb_main_class.main_class_join','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_class_lecture.id_employee','left')
        				  ->join('tb_campus','tb_campus.id_campus=tb_main_class.id_campus','left')
        				  ->join('tb_session','tb_session.id_session=tb_main_class.id_session','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->group_by('tb_main_class.id_main_class')
                          ->get('tb_main_class')->$param();
        return $query;
    }

    public function pagination_requested_class($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_class_attendance.attendance_date';
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

        	if ($this->session->userdata('id_level') == '22') {
        	    $query = $this->db->like('subject_name', $get['search'])
            				  	  ->where('tb_class_attendance.id_employee', $this->session->userdata('id_employee'))
            				  	  ->or_like('subject_code', $get['search'])
            				  	  ->where('tb_class_attendance.id_employee', $this->session->userdata('id_employee'));
        	} else {
        		$query = $this->db->like('subject_name', $get['search'])
            				  ->or_like('subject_code', $get['search'])
            				  ->or_like('employee_name', $get['search']);
        	}
        }

        if (isset($get['id_course_filter'])) {
            $query = $this->db->where('tb_course.id_course', $get['id_course_filter']);
        }
        if (isset($get['trimester_filter'])) {
            $query = $this->db->where('tb_trimester.trimester', $get['trimester_filter']);
        } else {
        	if ($this->session->userdata('id_level') == '22') {
        		$query = $this->db->where('trimester_start_date <=', date('Y-m-d'))
        					      ->where('trimester_end_date >=', date('Y-m-d'));
        	}
        }

        if (isset($get['id_subject_filter'])) {
            $query = $this->db->where('tb_subject.id_subject', $get['id_subject_filter']);
        }
        if (isset($get['id_academic_year_filter'])) {
            $query = $this->db->where('tb_academic_year.id_academic_year', $get['id_academic_year_filter']);
        } else {
        	if ($this->session->userdata('id_level') == '22') {
        		$query = $this->db->where('trimester_start_date <=', date('Y-m-d'))
        					      ->where('trimester_end_date >=', date('Y-m-d'));
        	}
        }

        if ($this->session->userdata('id_level') == '22') {
        	$query = $this->db->where('tb_class_lecture.id_employee', $this->session->userdata('id_employee'));
        }

        $query = $this->db->select('*, tb_main_class.id_main_class, tb_main_class.main_class_join, tb_class_attendance.attendance_type')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_class_attendance.id_employee','left')
        				  ->join('tb_schedule_detail','tb_schedule_detail.id_schedule_detail = tb_class_attendance.id_schedule_detail')
        				  ->join('tb_class_lecture','tb_class_lecture.class_lecture_join=tb_schedule_detail.class_lecture_join','left')
        				  ->join('tb_main_class','tb_main_class.id_main_class = tb_class_lecture.id_main_class')
        				  ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
        				  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
        				  ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester','left')
        				  ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
        				  ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
        				  ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year','left')
        				  ->join('tb_campus','tb_campus.id_campus=tb_main_class.id_campus','left')
        				  ->where('tb_class_attendance.attendance_type !=','Regular')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_class_attendance')->$param();
        return $query;
    }

    public function pagination_requested_score($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_main_class.submit_score_date';
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

        	if ($this->session->userdata('id_level') == '22') {
        	    $query = $this->db->like('subject_name', $get['search'])
            				  	  ->where('tb_class_attendance.id_employee', $this->session->userdata('id_employee'))
            				  	  ->or_like('subject_code', $get['search'])
            				  	  ->where('tb_class_attendance.id_employee', $this->session->userdata('id_employee'));
        	} else {
        		$query = $this->db->like('subject_name', $get['search'])
            				  	  ->or_like('subject_code', $get['search'])
            				  	  ->or_like('employee_name', $get['search']);
        	}
        }

        if (isset($get['id_course_filter'])) {
            $query = $this->db->where('tb_course.id_course', $get['id_course_filter']);
        }
        if (isset($get['trimester_filter'])) {
            $query = $this->db->where('tb_trimester.trimester', $get['trimester_filter']);
        } else {
        	if ($this->session->userdata('id_level') == '22') {
        		$query = $this->db->where('trimester_start_date <=', date('Y-m-d'))
        					      ->where('trimester_end_date >=', date('Y-m-d'));
        	}
        }

        if (isset($get['id_subject_filter'])) {
            $query = $this->db->where('tb_subject.id_subject', $get['id_subject_filter']);
        }
        if (isset($get['id_academic_year_filter'])) {
            $query = $this->db->where('tb_academic_year.id_academic_year', $get['id_academic_year_filter']);
        } else {
        	if ($this->session->userdata('id_level') == '22') {
        		$query = $this->db->where('trimester_start_date <=', date('Y-m-d'))
        					      ->where('trimester_end_date >=', date('Y-m-d'));
        	}
        }

        if ($this->session->userdata('id_level') == '22') {
        	$query = $this->db->where('tb_main_class.teacher_in_charge', $this->session->userdata('id_employee'));
        }

        $query = $this->db->select('*')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_main_class.teacher_in_charge','left')
        				  ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
        				  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
        				  ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester','left')
        				  ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
        				  ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
        				  ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year','left')
        				  ->join('tb_campus','tb_campus.id_campus=tb_main_class.id_campus','left')
        				  ->where('tb_main_class.submit_score_status',1)
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_main_class')->$param();
        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function consecutive_class($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Consecutive Class';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['teacher'] = $this->master_model->get_teacher();
		     $data['course'] = $this->recruitment_model->get_course();
		     $data['subject'] = $this->master_model->get_subject();
		     $data['academic_year'] = $this->recruitment_model->get_academic_year();
		     $data['class_lecture_active'] = $this->master_model->get_class_lecture_active();
		     $data['pagination_data'] = $this->pagination_consecutive_class($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_consecutive_class($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Activity/consecutive_class_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_consecutive_class();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_consecutive_class();
            echo $aksi;
		} elseif ($param1 == 'same_room_activation') {
			$aksi = $this->master_model->same_room_activation();
            echo $aksi;
		} elseif ($param1 == 'delete') {

			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_consecutive_class($id);
			}
			redirect($url);
		}
	}

	public function pagination_consecutive_class($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_class_lecture.id_class_lecture';
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
            $query = $this->db->like('subject_name', $get['search'])
            				  ->or_like('subject_code', $get['search'])
            				  ->or_like('course', $get['search'])
            				  ->or_like('course_abstract', $get['search'])
            				  ->or_like('course_code', $get['search'])
            				  ->or_like('employee_name', $get['search']);
        }

        if (isset($get['id_course_filter'])) {
            $query = $this->db->where('tb_course.id_course', $get['id_course_filter']);
        }
        if (isset($get['trimester_filter'])) {
            $query = $this->db->where('tb_trimester.trimester', $get['trimester_filter']);
        }
        if (isset($get['id_subject_filter'])) {
            $query = $this->db->where('tb_subject.id_subject', $get['id_subject_filter']);
        }
        if (isset($get['id_academic_year_filter'])) {
            $query = $this->db->where('tb_academic_year.id_academic_year', $get['id_academic_year_filter']);
        }

        $query = $this->db->select('*, tb_class_lecture.id_class_lecture')
        				  ->join('tb_class_lecture','tb_class_lecture.id_class_lecture=tb_consecutive_class.id_class_lecture')
        				  ->join('tb_main_class','tb_main_class.id_main_class=tb_class_lecture.id_main_class')
        				  ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
        				  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
        				  ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester','left')
        				  ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
        				  ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
        				  ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_class_lecture.id_employee','left')
        				  ->join('tb_session','tb_session.id_session=tb_main_class.id_session','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->group_by('consecutive_group')
        				  //->group_by('tb_school.id_school')
                          ->get('tb_consecutive_class')->$param();
        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function joined_class($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Joined Class';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['academic_year'] = $this->recruitment_model->get_academic_year();
		     $data['pagination_data'] = $this->pagination_joined_class($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_joined_class($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Activity/joined_class_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_consecutive_class();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_consecutive_class();
            echo $aksi;
		} elseif ($param1 == 'same_room_activation') {
			$aksi = $this->master_model->same_room_activation();
            echo $aksi;
		} elseif ($param1 == 'delete') {

			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_consecutive_class($id);
			}
			redirect($url);
		}
	}

	public function pagination_joined_class($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_main_class.main_class_last_updated';
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
            $query = $this->db->like('subject_name', $get['search'])
            				  ->or_like('subject_code', $get['search'])
            				  ->or_like('course', $get['search'])
            				  ->or_like('course_abstract', $get['search'])
            				  ->or_like('course_code', $get['search']);
        }

      
        if (isset($get['trimester_filter'])) {
            $query = $this->db->where('tb_trimester.trimester', $get['trimester_filter']);
        }
      
        if (isset($get['id_academic_year_filter'])) {
            $query = $this->db->where('tb_academic_year.id_academic_year', $get['id_academic_year_filter']);
        }

        $query = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
        				  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
        				  ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester','left')
        				  ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
        				  ->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
        				  ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year','left')
        				  ->join('tb_session','tb_session.id_session=tb_main_class.id_session','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->group_by('tb_main_class.main_class_join')
        				  ->having('COUNT(main_class_join) >', '1')
        				  //->group_by('tb_school.id_school')
                          ->get('tb_main_class')->$param();
        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

    public function pagination_detail_class_student($get = [], $param = 'result', $id_main_class = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'name';
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
            $query = $this->db->where('id_main_class', $id_main_class)
            				  ->where('class_student_softdel','0')
            				  ->like('name', $get['search'])
            				  ->or_like('family_name', $get['search'])
            				  ->where('class_student_softdel','0')
            				  ->where('id_main_class', $id_main_class);
        }

        $query = $this->db->join('tb_student','tb_student.id_student=tb_class_student.id_student')
                          ->join('tb_leads','tb_leads.id_leads=tb_student.id_leads')
        				  ->where('id_main_class', $id_main_class)
        				  ->where('class_student_softdel','0')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_class_student')->$param();

        return $query;
    }

    public function pagination_class_lecture($get = [], $param = 'result', $main_class_join = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 50;
        if(!isset($get['sortby'])) $get['sortby'] = 'class_lecture_type';
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
            $query = $this->db->where('tb_class_lecture.main_class_join', $main_class_join)
            				  ->like('class_lecture_hours', $get['search'])
            				  ->or_where('tb_class_lecture.main_class_join', $main_class_join)
            				  ->like('class_lecture_type', $get['search'])
            				  ->or_where('tb_class_lecture.main_class_join', $main_class_join)
            				  ->like('employee_name', $get['search'])
            				  ->or_where('tb_class_lecture.main_class_join', $main_class_join)
            				  ->like('split', $get['search']);
        }

        $query = $this->db->join('tb_main_class','tb_main_class.id_main_class=tb_class_lecture.id_main_class')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_class_lecture.id_employee')
        				  ->where('tb_main_class.main_class_join', $main_class_join)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  ->order_by('class_lecture_type', 'asc')
        				  ->order_by('split', 'asc')
                          ->get('tb_class_lecture')->$param();

        return $query;
    }

    public function pagination_schedule_for_today($get = [], $param = 'result', $id_employee = '', $lecturer = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'tb_schedule_detail.start';
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

        if (isset($get['id_course_filter'])) {
            $query = $this->db->where('tb_course_structure.id_course', $get['id_course_filter']);
        }

        if (isset($get['id_campus_filter'])) {
            $query = $this->db->where('tb_main_class.id_campus', $get['id_campus_filter']);
        }

        if (isset($get['day_filter'])) {
            $query = $this->db->where('tb_schedule_detail.day', $get['day_filter']);
        } else {
            $query = $this->db->where('tb_schedule_detail.day', date('N'));
        }

        if ($lecturer == '1') {
           $query = $this->db->where('tb_class_lecture.id_employee', $id_employee);
        }

        $query = $this->db->join('tb_class_lecture','tb_class_lecture.class_lecture_join= tb_schedule_detail.class_lecture_join')
                  ->join('tb_main_class','tb_main_class.id_main_class=tb_class_lecture.id_main_class')
                  ->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
                  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
                  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_class_lecture.id_employee')
                  ->join('tb_trimester','tb_trimester.id_trimester = tb_main_class.id_trimester')
                  ->join('tb_campus','tb_campus.id_campus = tb_main_class.id_campus')
                  ->join('tb_course_structure','tb_course_structure.id_course_structure = tb_detail_course_structure.id_course_structure')
                  ->where('tb_trimester.trimester_start_date <=', date('Y-m-d'))
                  ->where('tb_trimester.trimester_end_date >=', date('Y-m-d'))
                  ->order_by($get['sortby'], $get['sortby2'])
                  ->get('tb_schedule_detail')->$param();

        return $query;
    }

    public function pagination_class_specialist($get = [], $param = 'result', $id_main_class = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 50;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_class_specialist';
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
            $query = $this->db->where('id_main_class', $id_main_class)
            				  ->like('specialist_name', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_class_specialist.class_specialist_updated_by')
        				  ->join('tb_specialist','tb_specialist.id_specialist=tb_class_specialist.id_specialist')
        				  ->join('tb_course','tb_course.id_course=tb_specialist.id_course')
        				  ->where('id_main_class', $id_main_class)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_class_specialist')->$param();

        return $query;
    }

   
    public function cek_semester_trimester() {
		// $layanan =$this->input->post('layanan');
		$id_intended_program = $this->input->post('id_intended_program');

		$result = $this->db->where('id_intended_program', $id_intended_program)
						   ->get('tb_intended_program')
						   ->row();

		echo $result->period;

	}

	public function get_credit_by_course_structure() {
		// $layanan =$this->input->post('layanan');
		$id_detail_course_structure = $this->input->post('id_detail_course_structure');

		$result1 = $this->db->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
						   ->where('id_detail_course_structure', $id_detail_course_structure)
						   ->get('tb_detail_course_structure')
						   ->row();
						   
		$result2 = $this->db->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject_add','left')
						   ->where('id_detail_course_structure', $id_detail_course_structure)
						   ->get('tb_detail_course_structure')
						   ->row();

		$total = $result1->subject_duration + $result2->subject_duration;

		echo $total;

	}

	public function get_main_class_by_trimester() {
		// $layanan =$this->input->post('layanan');
		$id_trimester = $this->input->post('id_trimester');

		$result = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
        				  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
						   ->where('id_trimester', $id_trimester)
						   ->get('tb_main_class')
						   ->result();

		$option = "";
		$option .= '<option value=""> -- Select Class --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_main_class."' >".$data->subject_name."</option>";
		}
		echo $option;
	}

    
	public function get_main_class_by_course() {
		// $layanan =$this->input->post('layanan');
		$id_course = $this->input->post('id_course');

		$result = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
        				  ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject','left')
        				  ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure')
						   ->where('tb_course_structure.id_course', $id_course)
						   ->get('tb_main_class')
						   ->result();

		$option = "";
		$option .= '<option value=""> -- Select Class --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_main_class."' >".$data->subject_name."</option>";
		}
		echo $option;
	}

	public function get_course_by_course_structure() {
		// $layanan =$this->input->post('layanan');
		$id_detail_course_structure = $this->input->post('id_detail_course_structure');

		$result = $this->db->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
						   ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure')
						   ->join('tb_course','tb_course.id_course=tb_course_structure.id_course')
						   ->where('id_detail_course_structure', $id_detail_course_structure)
						   ->get('tb_detail_course_structure')
						   ->row();

			$result_array = array(
						'id_course' => $result->id_course,
						'course' => $result->course);
				
			echo json_encode($result_array);
	}

	public function get_student_by_course() {

		$id_course = $this->input->post('id_course');

		$total_enrolled = $this->db->where('id_status', 5)
						   ->where('id_course', $id_course)
						   ->get('tb_leads')
						   ->num_rows();

		echo $total_enrolled;

	}

	public function check_subject_in_main_class() {

		$id_detail_course_structure = $this->input->post('id_detail_course_structure');
		$id_trimester = $this->input->post('id_trimester');
		$id_course = $this->input->post('id_course');
		$id_session = $this->input->post('id_session');
		$id_campus = $this->input->post('id_campus');

		$total_class = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure','left')
								->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure','left')
								->join('tb_course','tb_course.id_course=tb_course_structure.id_course','left')
								->where('tb_main_class.id_detail_course_structure', $id_detail_course_structure)
						   		->where('id_trimester', $id_trimester)
						   		->where('id_session', $id_session)
						   		->where('tb_course.id_course', $id_course)
						   		->where('tb_main_class.id_campus', $id_campus)
						   		->get('tb_main_class')
						   		->num_rows();

		echo $total_class;
	}

	public function get_trimester_by_course() {
		
		$id_course = $this->input->post('id_course');

		$course = $this->db->where('id_course', $id_course)->get('tb_course')->row();

		$result = $this->db->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year')
						   ->where('id_intended_program', $course->id_intended_program)
						   ->where('trimester_start_date <=', date('Y-m-d'))
						   ->where('trimester_end_date >=', date('Y-m-d'))
						   ->get('tb_trimester')
						   ->result();
		$option = "";
		$option .= '<option value=""> -- Select Trimester --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_trimester."' >".$data->trimester." - ".$data->academic_year."</option>";
		}
		echo $option;

	}

  public function get_semester_by_course() {
    
    $id_course = $this->input->post('id_course');

    $course = $this->db->where('id_course', $id_course)->get('tb_course')->row();

    $result = $this->db->join('tb_academic_year','tb_academic_year.id_academic_year=tb_semester.id_academic_year')
                       ->where('id_intended_program', $course->id_intended_program)
                       ->where('semester_start_date <=', date('Y-m-d'))
                       ->where('semester_end_date >=', date('Y-m-d'))
                       ->get('tb_semester')
                       ->result();

    $option = "";
    $option .= '<option value=""> -- Select Semester -- </option>';
    foreach ($result as $data) {
      $option .= "<option value='".$data->id_semester."' >".$data->semester." - ".$data->academic_year."</option>";
    }
    echo $option;

  }


  	public function get_trimester_by_academic_year_and_course() {
		
		$id_course = $this->input->post('id_course');
		$id_academic_year = $this->input->post('id_academic_year');

		$course = $this->db->where('id_course', $id_course)->get('tb_course')->row();

		$result = $this->db->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year')
						   ->where('id_intended_program', $course->id_intended_program)
						   ->where('tb_trimester.id_academic_year', $id_academic_year)
						   ->get('tb_trimester')
						   ->result();
		$option = "";
		$option .= '<option value=""> -- Select Trimester --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_trimester."' >".$data->trimester." - ".$data->academic_year."</option>";
		}
		echo $option;

	}

	public function get_semester_by_academic_year_and_course() {
		
		$id_course = $this->input->post('id_course');
		$id_academic_year = $this->input->post('id_academic_year');

		$course = $this->db->where('id_course', $id_course)->get('tb_course')->row();

		$result = $this->db->join('tb_academic_year','tb_academic_year.id_academic_year=tb_semester.id_academic_year')
						   ->where('id_intended_program', $course->id_intended_program)
						   ->where('tb_semester.id_academic_year', $id_academic_year)
						   ->get('tb_semester')
						   ->result();
		$option = "";
		$option .= '<option value=""> -- Select Semester --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_semester."' >".$data->semester." - ".$data->academic_year."</option>";
		}
		echo $option;

	}

	public function get_all_trimester_by_course() {
		
		$id_course = $this->input->post('id_course');

		$course = $this->db->where('id_course', $id_course)->get('tb_course')->row();

		$result = $this->db->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year')
						   ->where('id_intended_program', $course->id_intended_program)
						   //->where('trimester_start_date <=', date('Y-m-d'))
						   //->where('trimester_end_date >=', date('Y-m-d'))
						   ->get('tb_trimester')
						   ->result();
		$option = "";
		$option .= '<option value=""> -- Select Trimester --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_trimester."' >".$data->trimester." - ".$data->academic_year."</option>";
		}
		echo $option;

	}

    public function get_semester_by_academic_year() {
		// $layanan =$this->input->post('layanan');
		$id_intended_program = $this->input->post('id_intended_program');
		$id_academic_year = $this->input->post('id_academic_year');

		$result = $this->db->where('id_intended_program', $id_intended_program)
						   ->where('id_academic_year', $id_academic_year)
						   ->where('semester_start_date <=', date('Y-m-d'))
						   ->where('semester_end_date >=', date('Y-m-d'))
						   ->get('tb_semester')
						   ->result();
		$option = "";
		$option .= '<option value=""> -- Select Semester --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_semester."' >".$data->semester."</option>";
		}
		echo $option;

	}

	public function get_trimester_by_academic_year() {
		// $layanan =$this->input->post('layanan');
		$id_intended_program = $this->input->post('id_intended_program');
		$id_academic_year = $this->input->post('id_academic_year');
		
		$result = $this->db->where('id_intended_program', $id_intended_program)
						   ->where('id_academic_year', $id_academic_year)
						   ->where('trimester_start_date <=', date('Y-m-d'))
						   ->where('trimester_end_date >=', date('Y-m-d'))
						   ->get('tb_trimester')
						   ->result();
		$option = "";
		$option .= '<option value=""> -- Select Trimester --- </option>';
		foreach ($result as $data) {
			$option .= "<option value='".$data->id_trimester."' >".$data->trimester."</option>";
		}
		echo $option;

	}

	public function get_class_for_joined_class() {
		
		$result = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure')
						   ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure')
						   ->join('tb_course','tb_course.id_course=tb_course_structure.id_course')
        				   ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
        				   ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester')
                           ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year')
                            ->join('tb_session','tb_session.id_session=tb_main_class.id_session','left')
                           ->join('tb_campus','tb_campus.id_campus = tb_main_class.id_campus')
						   ->where('trimester_start_date <=', date('Y-m-d'))
						   ->where('trimester_end_date >=', date('Y-m-d'))
						   ->group_by('tb_main_class.main_class_join')
						   ->having('count(main_class_join) <', 2)
						   ->get('tb_main_class')
						   ->result();
		$option = "";
		$option .= '<option value=""> -- Select Class --- </option>';
		foreach ($result as $data) {

			$option .= "<option value='".$data->id_main_class."' > ".$data->subject_code." - ".$data->subject_name." - ".$data->course_abstract." - ".$data->academic_year."-".$data->trimester." - ".$data->session." - (".$data->campus_code.")</option>";
		}
		echo $option;

	}

	public function get_active_class() {
		
		$result = $this->db->join('tb_detail_course_structure','tb_detail_course_structure.id_detail_course_structure=tb_main_class.id_detail_course_structure')
						   ->join('tb_course_structure','tb_course_structure.id_course_structure=tb_detail_course_structure.id_course_structure')
						   ->join('tb_course','tb_course.id_course=tb_course_structure.id_course')
        				   ->join('tb_subject','tb_subject.id_subject=tb_detail_course_structure.id_subject')
        				   ->join('tb_trimester','tb_trimester.id_trimester=tb_main_class.id_trimester')
                           ->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year')
                           ->join('tb_session','tb_session.id_session= tb_main_class.id_session','left')
						   ->where('trimester_start_date <=', date('Y-m-d'))
						   ->where('trimester_end_date >=', date('Y-m-d'))
						   ->get('tb_main_class')
						   ->result();
		$option = "";
		$option .= '<option value=""> -- Select Class --- </option>';
		foreach ($result as $data) {

			$option .= "<option value='".$data->id_main_class."' > ".$data->subject_code." - ".$data->subject_name." - ".$data->course_abstract." - ".$data->academic_year."-".$data->trimester." - ".$data->session." - (".$data->campus_code.")</option>";
		}
		echo $option;

	}

	public function get_class_join() {

		$result = $this->master_model->get_class_join();
		$option = "";
		$option .= '<option value=""> -- Select Class --- </option>';
		foreach ($result as $data) {

			$option .= "<option value='".$data->id_main_class."' > ".$data->subject_code." - ".$data->subject_name." - ".$data->course_abstract." - ".$data->academic_year."-".$data->trimester." - ".$data->session." - (".$data->campus_code.")</option>";
		}
		echo $option;

	}

	public function get_specialist_by_course() {
		$id_course = $this->input->post('id_course');
		$result = $this->master_model->get_specialist_by_course($id_course);
		$option = "";
		$option .= '<option value=""> -- Select Specialist --- </option>';
		foreach ($result as $data) {

			$option .= "<option value='".$data->id_specialist."' > ".$data->specialist_code." - ".$data->specialist_name."</option>";
		}
		echo $option;

	}

	public function get_specialists_by_course() {
		$id_course = $this->input->post('id_course');
		$result = $this->master_model->get_specialists_by_course($id_course);
		$option = "";
		$option .= '<option value=""> -- Select Specialist --- </option>';
		foreach ($result as $data) {

			$option .= "<option value='".$data->id_specialist."' > ".$data->specialist_code." - ".$data->specialist_name."</option>";
		}
		echo $option;

		$post = $this->input->post('data');

		print_r($post);

	}



	public function check_class_join() {

		$id_main_class = $this->input->post('id_main_class');


		$cek = 	 $this->db->where('id_main_class', $id_main_class)
						   ->get('tb_main_class')
						   ->row();

		$join =  $this->db->where('id_main_class !=', $id_main_class)
						  ->where('main_class_join', $cek->main_class_join)
						  ->get('tb_main_class')
						  ->num_rows();
		echo $join;

	}


	//------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function academic_year($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Academic Year';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_academic_year($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_academic_year($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/academic_year_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_academic_year();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_academic_year();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_academic_year($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_academic_year($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_academic_year';
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
            $query = $this->db->like('academic_year', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_academic_year.academic_year_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_academic_year')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function trimester($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Trimester';
			 $data['academic_year'] = $this->recruitment_model->get_academic_year();
			 $data['intended_program'] = $this->recruitment_model->get_intended_program();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_trimester($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_trimester($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/trimester_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_trimester();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_trimester();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_trimester($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_trimester($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_trimester';
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
            $query = $this->db->like('trimester', $get['search'])
            				          ->or_like('academic_year', $get['search'])
                              ->or_like('trimester_name', $get['search'])
                              ->or_like('intended_program', $get['search']);
        }

        $query = $this->db->join('tb_academic_year','tb_academic_year.id_academic_year=tb_trimester.id_academic_year','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_trimester.trimester_updated_by','left')
        				  ->join('tb_intended_program','tb_intended_program.id_intended_program=tb_trimester.id_intended_program','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_trimester')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function semester($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Semester';
			 $data['intended_program'] = $this->master_model->get_intended_program();
			 $data['academic_year'] = $this->recruitment_model->get_academic_year();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_semester($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_semester($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/semester_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_semester();
      echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_semester();
      echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_semester($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {
			
		}
	}

	public function pagination_semester($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_semester';
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
            $query = $this->db->like('semester', $get['search'])
            				  ->or_like('academic_year', $get['search']);
        }

        $query = $this->db->join('tb_academic_year','tb_academic_year.id_academic_year=tb_semester.id_academic_year','left')
        				          ->join('tb_intended_program','tb_intended_program.id_intended_program=tb_semester.id_intended_program','left')
        				          ->join('db_hr.tb_employee','tb_employee.id_employee=tb_semester.semester_updated_by','left')
        				          ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_semester')->$param();

        return $query;
    }

    //-------------------------------------------------------------------------------------------------------//

	public function specialist($param1 = '', $param2 = ''){
		if ($param1 == 'view') {
			 $id_course = $param2;
			 $data['title'] = 'Specialist';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['course'] = $this->master_model->get_course_by_id($id_course);
		     $data['pagination_data'] = $this->pagination_specialist($this->input->get(), 'result', $id_course);
		     $data['pagination_total_page'] = $this->pagination_specialist($this->input->get(), 'num_rows', $id_course);
		     $this->load->view('Academic/Master/specialist_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add_specialist') {

			$aksi = $this->master_model->add_specialist();
            echo $aksi;
		} elseif ($param1 == 'edit_specialist') {
			$aksi = $this->master_model->edit_specialist();
            echo $aksi;
		} elseif ($param1 == 'delete_specialist') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_specialist($id);
			}
			redirect($url);
		} 
	}

	public function pagination_specialist($get = [], $param = 'result', $id_course = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 50;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_specialist';
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
            $query = $this->db->where('id_course', $id_course)
            				  ->like('specialist_name', $get['search'])
            				  ->or_like('specialist_code', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_specialist.specialist_updated_by')
        				  ->where('id_course', $id_course)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->order_by('subject_name_1', 'asc')
        				  //->group_by('tb_school.id_school')
                          ->get('tb_specialist')->$param();

        return $query;
    }

    //-------------------------------------------------------------------------------------------------------//

	public function program_type($param1 = '', $param2 = ''){
		if ($param1 == 'view') {
			 $id_intended_program = $param2;
			 $data['title'] = 'Program Type';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['duration_type'] = $this->master_model->get_duration_type();
		     $data['intended_program'] = $this->master_model->get_intended_program_by_id($id_intended_program);
		     $data['pagination_data'] = $this->pagination_program_type($this->input->get(), 'result', $id_intended_program);
		     $data['pagination_total_page'] = $this->pagination_program_type($this->input->get(), 'num_rows', $id_intended_program);
		     $this->load->view('Academic/Master/program_type_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {

			$aksi = $this->master_model->add_program_type();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_program_type();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_program_type($id);
			}
			redirect($url);
		} 
	}

	public function pagination_program_type($get = [], $param = 'result', $id_intended_program = '')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 50;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_program_type';
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
            $query = $this->db->where('id_intended_program', $id_intended_program)
            				  ->like('program_type', $get['search'])
            				  ->or_like('program_type_code', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_program_type.program_type_updated_by')
        				  ->join('tb_duration_type','tb_duration_type.id_duration_type=tb_program_type.id_duration_type','left')
        				  ->where('id_intended_program', $id_intended_program)
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->order_by('subject_name_1', 'asc')
        				  //->group_by('tb_school.id_school')
                          ->get('tb_program_type')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function intended_program($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Intended Program - Master';
			 $data['program'] = $this->master_model->get_program();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_intended_program($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_intended_program($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/intended_program_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_intended_program();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_intended_program();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_intended_program($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	public function pagination_intended_program($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_intended_program';
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
            $query = $this->db->like('intended_program', $get['search']);
        }

        $query = $this->db ->join('db_hr.tb_employee','tb_employee.id_employee=tb_intended_program.intended_program_updated_by','left')
        				  ->join('tb_program','tb_program.id_program=tb_intended_program.id_program','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_intended_program')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function program($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Program - Master';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_program($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_program($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/program_view', $data);
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

	public function pagination_program($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_program';
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
            $query = $this->db->like('id_program', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_program.program_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_program')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function course($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Course - Master';
			 $data['program'] = $this->master_model->get_program();
			 $data['duration_type'] = $this->master_model->get_duration_type();
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_course($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_course($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/course_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_course();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_course();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_course($id);
			}
			$this->session->set_flashdata('message', '<div class="alert alert-success alert-dismissible" style="margin-left: -20px;margin-right: -20px; margin-top: -15px">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <p><i class="icon fa fa-check"></i> Deleted </p>
                </div><script> window.setTimeout(function() { $(".alert-success").fadeTo(500, 0).slideUp(500, function(){ $(this).remove(); }); }, 5000); </script>');
			redirect($url);
		} else {

		}
	}

	public function pagination_course($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_course';
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
            $query = $this->db->like('program_name', $get['search'])
            				  ->or_like('intended_program', $get['search'])
            				  ->or_like('course', $get['search']);
        }

        $query = $this->db->join('tb_intended_program','tb_intended_program.id_intended_program=tb_course.id_intended_program','left')
        				  ->join('tb_program','tb_program.id_program=tb_intended_program.id_program','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_course.course_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_course')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function time($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Time';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_time($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_time($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/time_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'detail') {
			 $data['title'] = 'Time';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		   	 $data['time1'] = $this->master_model->get_time1();
		   	 $data['time2'] = $this->master_model->get_time2();
		   	 $data['time3'] = $this->master_model->get_time3();
		     $this->load->view('Academic/Master/detail_time_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'break_time') {
			 $data['title'] = 'Break Time';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		   	 $data['active_time'] = $this->master_model->get_active_time();
		     $this->load->view('Academic/Master/break_time_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'class_starting_time') {
			 $data['title'] = 'Class Starting Time';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		   	 $data['active_time'] = $this->master_model->get_active_time();
		     $this->load->view('Academic/Master/class_starting_time_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_time();
            echo $aksi;
		} elseif ($param1 == 'add_break_time') {
			$aksi = $this->master_model->add_break_time();
            echo $aksi;
		} elseif ($param1 == 'add_class_starting_time') {
			$aksi = $this->master_model->add_class_starting_time();
            echo $aksi;
		} else {

		}
	}

	 public function pagination_time($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 50;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_time';
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
            $query = $this->db->like('time_name', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_time.time_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_time')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function teaching_period($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Teaching Duration';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['month'] = $this->master_model->get_month();
		     $data['pagination_data'] = $this->pagination_teaching_period($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_teaching_period($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Teacher/teaching_period_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'claim_form') {
			 $id_teaching_period = $param2;
			 $data['title'] = 'Claim Form';
		     $data['left_bar'] = $this->admin_model->check_navbar();
         $data['campus'] = $this->marketing_model->get_campus();
		     $data['teaching_period'] = $this->master_model->get_teaching_period_by_id($id_teaching_period);
		     $data['teacher'] = $this->master_model->get_teacher();
		     $data['academic_year'] = $this->recruitment_model->get_academic_year();
		     $data['pagination_data'] = $this->pagination_claim_form($this->input->get(), 'result', $id_teaching_period);
		     $data['pagination_total_page'] = $this->pagination_claim_form($this->input->get(), 'num_rows', $id_teaching_period);
		     $this->load->view('Academic/Teacher/claim_form_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_teaching_period();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_teaching_period();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_teaching_period($id);
			}
			redirect($url);
		} elseif ($param1 == 'edit_claim_form') {
			$aksi = $this->master_model->edit_claim_form();
            echo $aksi;
		} else {
			
		}
	}

	public function pagination_teaching_period($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'teaching_period_start';
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
            $query = $this->db->like('teaching_duration_name', $get['search']);
        }

        $query = $this->db->join('tb_month','tb_month.id_month=tb_teaching_period.id_month','left')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_teaching_period.teaching_period_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_teaching_period')->$param();

        return $query;
    }

     public function pagination_claim_form($get = [], $param = 'result', $id_teaching_period)
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_claim_form';
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
            $query = $this->db->like('employee_name', $get['search']);
        }

        if (isset($get['id_campus_filter'])) {
            $query = $this->db->where('tb_main_class.id_campus', $get['id_campus_filter']);
        }

        if ($this->session->userdata('id_level') == 22) {
        	$query = $this->db->where('tb_claim_form.id_employee', $this->session->userdata('id_employee'));
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee = tb_class_attendance.id_employee')
                          ->join('tb_claim_form','tb_claim_form.id_employee = tb_employee.id_employee')
                          ->join('tb_main_class','tb_main_class.main_class_join=tb_class_attendance.main_class_join')
                          ->where('id_teaching_period', $id_teaching_period)
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->group_by('tb_employee.id_employee')
                          ->get('tb_class_attendance')->$param();

        return $query;
    }

    //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function score($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Score';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_score($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_score($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/score_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_score();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_score();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_score($id);
			}
			redirect($url);
		} else {

		}
	}

	 public function pagination_score($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_score';
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
            $query = $this->db->like('score_name', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee=tb_score.score_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_score')->$param();

        return $query;
    }

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function score_scale($param1 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Score Scale';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['intended_program'] = $this->master_model->get_intended_program();
		     $data['pagination_data'] = $this->pagination_score_scale($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_score_scale($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Master/score_scale_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
			$aksi = $this->master_model->add_score_scale();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_score_scale();
            echo $aksi;
		} elseif ($param1 == 'delete') {
			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_score_scale($id);
			}
			redirect($url);
		} else {

		}
	}

	 public function pagination_score_scale($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_score_scale';
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
            $query = $this->db->like('score_name', $get['search']);
        }

        $query = $this->db->join('tb_intended_program','tb_intended_program.id_intended_program=tb_score_scale.id_intended_program')
        				  ->join('db_hr.tb_employee','tb_employee.id_employee=tb_score_scale.score_scale_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
        				  //->group_by('tb_school.id_school')
                          ->get('tb_score_scale')->$param();

        return $query;
    }
	

     //------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------//

	public function warning($param1 = '', $param2 = ''){
		if ($param1 == '') {
			 $data['title'] = 'Warning';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['pagination_data'] = $this->pagination_warning($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_warning($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Student/warning_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add') {
            $aksi = $this->master_model->add_warning();
            echo $aksi;
		} elseif ($param1 == 'edit') {
			$aksi = $this->master_model->edit_warning();
            echo $aksi;
		} elseif ($param1 == 'delete') {

			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_warning($id);
			}
			redirect($url);
		} elseif ($param1 == 'student') {
			 $data['title'] = 'Student Warning';
		     $data['left_bar'] = $this->admin_model->check_navbar();
		     $data['student'] = $this->master_model->get_student_active();
		     $data['warning'] = $this->master_model->get_warning();
		     $data['pagination_data'] = $this->pagination_student_warning($this->input->get(), 'result');
		     $data['pagination_total_page'] = $this->pagination_student_warning($this->input->get(), 'num_rows');
		     $this->load->view('Academic/Student/student_warning_view', $data);
		     $this->session->set_userdata('previous_url', $this->link_terakhir);
		} elseif ($param1 == 'add_student_warning') {

            $student = $this->input->post('id_student');
			$id_warning = $this->input->post('id_warning');
			$student_warning_notes = $this->input->post('student_warning_notes');

			foreach ($student as $id_student) {
				$aksi = $this->master_model->add_student_warning($id_student, $id_warning, $student_warning_notes);
			}

			echo $aksi;

		} elseif ($param1 == 'edit_student_warning') {
			$aksi = $this->master_model->edit_student_warning();
            echo $aksi;
		} elseif ($param1 == 'delete_student_warning') {

			$url = $this->input->post('url');
			foreach ($_POST['id'] as $id) {
				$this->master_model->delete_student_warning($id);
			}
			redirect($url);
		}
	}

	public function pagination_warning($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_warning';
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

            $query = $this->db->like('warning_code', $get['search'])
            				  ->or_like('warning_name', $get['search']);
        }

        $query = $this->db->join('db_hr.tb_employee','tb_employee.id_employee = tb_warning.warning_updated_by','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_warning')->$param();

        return $query;
    }

    public function pagination_student_warning($get = [], $param = 'result')
    {
        if(!isset($get['page'])) $get['page'] = 1;
        if(!isset($get['limit'])) $get['limit'] = 10;
        if(!isset($get['sortby'])) $get['sortby'] = 'id_student_warning';
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

        if (isset($get['id_warning_filter'])) {
            $query = $this->db->where('tb_student_warning.id_warning', $get['id_warning_filter']);
        }

        if(isset($get['search'])){

            $query = $this->db->like('name', $get['search'])
            				  ->or_like('family_name', $get['search'])
            				  ->or_like('course_code', $get['search'])
            				  ->or_like('course', $get['search'])
            				  ->or_like('student_id', $get['search']);
        }

        $query = $this->db->join('tb_student','tb_student.id_student=tb_student_warning.id_student')
        				  ->join('tb_leads','tb_leads.id_leads=tb_student.id_leads','left')
        				  ->join('tb_course','tb_course.id_course = tb_student.id_course','left')
        				  ->join('tb_warning','tb_warning.id_warning = tb_student_warning.id_warning','left')
        				  ->order_by($get['sortby'], $get['sortby2'])
                          ->get('tb_student_warning')->$param();

        return $query;
    }

	
}
