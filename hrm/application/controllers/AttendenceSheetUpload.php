<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AttendenceSheetUpload extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('TalentAttendenceSheet', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('holiday', '', TRUE);
        $this->load->model('holidaysWeek', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function dummyAttendanceUpload() {
        $employee_array = $this->talent->getTalentsCode();
        $lastAttendanceUploadDate = $this->defaultValues->getValue(44);
        if (!$lastAttendanceUploadDate) {
            $lastAttendanceUploadDate = $this->TalentAttendenceSheet->lastAttendanceUploadDate();
        }
        if (strtotime($lastAttendanceUploadDate) >= strtotime(date('Y-m-d'))) {
            return;
        }
        $begin = new DateTime($lastAttendanceUploadDate);
        $end = new DateTime();

        for ($i = $begin; $i < $end; $i->modify('+1 day')) {
            $dateOfAttendence = $i->format("Y-m-d");
            if (strtotime($dateOfAttendence) == strtotime(date('Y-m-d'))) {
                break;
            }
            foreach ($employee_array as $employee_array_val) {
                $talent = $employee_array_val['id'];
                $checkAttendenceInserted = $this->TalentAttendenceSheet->getCheckTalentsDateDataExist($talent, $dateOfAttendence);
                if ($checkAttendenceInserted) {
                    continue;
                } else {
                    $leaveStatus = $this->leaves->getDayLeaveStatus($dateOfAttendence, $talent);
                    $isHoliday = $this->checkADayIsHoliday($dateOfAttendence, $talent);
                    $isLeave = 0;
                    if ($leaveStatus === TRUE) {
                        $isLeave = 1;
                    }
                    // insert data with 0 hours
                    $insertData = array(
                        'talent_id' => $talent,
                        'attendance_date' => $dateOfAttendence,
                        'is_holiday' => $isHoliday,
                        'is_leave' => $isLeave,
                    );
                    $this->db->insert('talent_attendance', $insertData);
                }
            }
        }

        $updateData = array(
            'value' => date('Y-m-d'),
        );
        $this->db->where('id', 44);
        $this->db->update('default_values', $updateData);
    }

    public function attendenceSheetUpload() {
        $this->load->helper('date');
        $this->load->helper(array('form'));
        $sess_data = $this->session->userdata('logged_in');
        if ($this->input->post()) {

            $this->load->library('form_validation');


            $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateOfAttendence'));
            $dateOfAttendence = $date->format('Y-m-d');
            $prev_date = date('Y-m-d', strtotime($dateOfAttendence . ' -1 day'));
            //check attendance uploaded for previous date
            $lastDateUpdateCheck = $this->TalentAttendenceSheet->dateUploadCheck($prev_date);
            if ($lastDateUpdateCheck > 0) {

                //Validation Success.  
                // Do insert operation here
                $config['upload_path'] = './attendance_files/';
                $config['allowed_types'] = 'txt|xls|xlsx|doc|docx|pdf';
                $this->load->library('upload', $config);



                $upload_data = $this->upload->data();

                $last = $this->TalentAttendenceSheet->getLastDate();
                $lastDate = isset($last->last_date) ? $last->last_date : null;
                $upload_error = false;
                if (strtotime($dateOfAttendence) < strtotime($lastDate)) {
                    $this->session->set_flashdata('flashError', 'File Upload Failed Because Attendence Are Taken For Monthly Process');
                } else {
                    if ($_FILES['uploadFile']['error'] != 4) {

                        if (!$this->upload->do_upload('uploadFile')) {


                            $upload_error = true;
                            $this->session->set_flashdata('flashError', $this->upload->display_errors());
                        } else {
                            $upload_data = $this->upload->data();
                            $fileName = $upload_data['file_name'];
                        }
                    }
                    if (false === $upload_error) {

                        /* Load data from file and insert to DB */

                        $file = './attendance_files/' . $fileName;
                        //load the excel library
                        $this->load->library('excel');
                        //read file from path
                        $objPHPExcel = PHPExcel_IOFactory::load($file);

                        //get only the Cell Collection
                        $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
                        //extract to a PHP readable array format
                        foreach ($cell_collection as $cell) {
                            $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                            $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                            $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getFormattedValue();
                            //header will/should be in row 1 only. of course this can be modified to suit your need.
                            $arr_data[$row][$column] = $data_value;
                        }

                        $employee_array = $this->talent->getTalentsCode();
                        $dateFromContent = $arr_data['7']['E'];
                        if (strtotime($dateOfAttendence) == strtotime($dateFromContent)) {
                            $insertAttendanceData = array(
                                'attendance_date' => $dateOfAttendence,
                                'uploaded_by_user_id' => $sess_data['id'],
                                'uploaded_on' => date('Y-m-d H:i:s'),
                                'filename' => $fileName
                            );
                            $this->db->trans_begin();
                            $queryResult = $this->db->insert('talents_attendance_sheet', $insertAttendanceData);
                            $talentArray = array();
                            foreach ($employee_array as $employees) {
                                $talentArray[] = $employees['talent_code'];
                            }
                            foreach ($arr_data as $arr_data_val) {
                                $talentCodePossition = 'C';
                                if (isset($arr_data_val[$talentCodePossition])) {
                                    $talentCodeValue = $arr_data_val[$talentCodePossition];
                                    if (strlen($talentCodeValue) == '1') {
                                        $talentCode = '000' . $talentCodeValue;
                                    } else if (strlen($talentCodeValue) == '2') {
                                        $talentCode = '00' . $talentCodeValue;
                                    } else if (strlen($talentCodeValue) == '3') {
                                        $talentCode = '0' . $talentCodeValue;
                                    } else {
                                        $talentCode = $talentCodeValue;
                                    }
                                    if (in_array($talentCode, $talentArray)) {
                                        // insert $arr_data_val data to db
                                        //finding talent
                                        $inOutLog = $arr_data_val['J'] ? $arr_data_val['J'] : $arr_data_val['I'];
                                        $inTime = $arr_data_val['G'] ? $arr_data_val['G'] : $arr_data_val['F'];
                                        $talentId = $this->talent->getTalentsByTalentCode($talentCode);
                                        $talent = isset($talentId->id) ? $talentId->id : null;

                                        if (null !== $talent) {
                                            // check already the data inserted due to od or multiple uploading
                                            //then add od hours to arr_data_val['B'] and check >$minimumHourRequered
                                            $checkAttendenceInserted = $this->TalentAttendenceSheet->getCheckTalentsDateDataExist($talent, $dateOfAttendence);
                                            if ($checkAttendenceInserted) {
                                                foreach ($checkAttendenceInserted as $row) {
                                                    $id = $row->id;
                                                }
                                                if (isset($id)) {
                                                    //finding holidays and leaves between the date
                                                    $leaveStatus = $this->leaves->getDayLeaveStatus($dateOfAttendence, $talent);
                                                    $isLeave = 0;
                                                    if ($leaveStatus === TRUE) {
                                                        $isLeave = 1;
                                                    }
                                                    $isHoliday = $this->checkADayIsHoliday($dateOfAttendence, $talent);
                                                    $updateData = array(
                                                        'actual_hours_spend' => $inTime,
                                                        'is_holiday' => $isHoliday,
                                                        'is_leave' => $isLeave,
                                                        'in_out_log' => $inOutLog,
                                                    );
                                                    $this->db->where('id', $id);
                                                    $this->db->update('talent_attendance', $updateData);
                                                }
                                            } else {
                                                $leaveStatus = $this->leaves->getDayLeaveStatus($dateOfAttendence, $talent);

                                                $isHoliday = $this->checkADayIsHoliday($dateOfAttendence, $talent);
                                                $isLeave = 0;
                                                if ($leaveStatus === TRUE) {
                                                    $isLeave = 1;
                                                }
                                                $insertData = array(
                                                    'talent_id' => $talent,
                                                    'attendance_date' => $dateOfAttendence,
                                                    'actual_hours_spend' => $inTime,
                                                    'is_holiday' => $isHoliday,
                                                    'is_leave' => $isLeave,
                                                    'in_out_log' => $inOutLog,
                                                );
                                                $queryResult = $this->db->insert('talent_attendance', $insertData);
                                                //die();
                                                // remove from $employee_array
                                                $key = array_search($talentCode, $employee_array);
                                                unset($employee_array[$key]);
                                            }
                                        }
                                    }
                                }
                            }

                            foreach ($talentArray as $employee_array_val) {

                                //var_dump($employee_array_val);
                                $talentId = $this->talent->getTalentsByTalentCode($employee_array_val);
                                $talent = $talentId->id;
                                // check already the data inserted due to od or multiple uploading
                                //then add od hours to arr_data_val['B'] and check >$minimumHourRequered
                                $checkAttendenceInserted = $this->TalentAttendenceSheet->getCheckTalentsDateDataExist($talent, $dateOfAttendence);
                                if ($checkAttendenceInserted) {
                                    continue;
                                } else {
                                    $leaveStatus = $this->leaves->getDayLeaveStatus($dateOfAttendence, $talent);
                                    $isHoliday = $this->checkADayIsHoliday($dateOfAttendence, $talent);
                                    $isLeave = 0;
                                    if ($leaveStatus === TRUE) {
                                        $isLeave = 1;
                                    }
                                    // insert data with 0 hours
                                    $insertData = array(
                                        'talent_id' => $talent,
                                        'attendance_date' => $dateOfAttendence,
                                        'is_holiday' => $isHoliday,
                                        'in_out_log' => $inOutLog,
                                        'is_leave' => $isLeave,
                                    );
                                    $queryResult = $this->db->insert('talent_attendance', $insertData);
                                }
                            }

                            /* Load data from file and insert to DB Ends hare */
                            $this->db->trans_commit();
                            $this->session->set_flashdata('flashSuccess', 'File Uploaded successfully');
                            unlink($file);
                            redirect('attendenceSheetUpload/attendenceSheetUpload', 'refresh');
                        } else {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('flashError', 'Sorry date choosed and date in the sheet are not same');
                        }
                    } else {
                        $this->session->set_flashdata('flashError', $this->upload->display_errors());
//                        $this->session->set_flashdata('flashError', 'File Upload Failed');
                    }
                }
            } else {
                $this->session->set_flashdata('flashError', 'Please Upload previous date data');
            }
        }
        $data['attendanceSheets'] = $this->TalentAttendenceSheet->getUploadedData();
        $this->load->view('attendenceManagement/attendenceSheetUpload', $data);
    }

    public function dateContentExistCheck() {
        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateOfAttendence'));
        $dateOfAttendence = $date->format('Y-m-d');
        $count = $this->TalentAttendenceSheet->dateUploadCheckWithoutSingleAttendanceSheet($dateOfAttendence);
        if ($count > 0) {
            echo '1';
        } else {
            echo '0';
        }
    }

    public function uploadedDetailView() {
        //using url for get id
        $this->load->helper('url');
        $attendanceId = $this->uri->segment(4);
        $viewData['attendanceId'] = $attendanceId;
        //$this->output->enable_profiler(TRUE);
        $attendanceDetalils = $this->TalentAttendenceSheet->detailOfAttendance($attendanceId);
        $viewData['attendanceDetalils'] = $attendanceDetalils;
        $this->load->view('attendenceManagement/uploadedDetailView', $viewData);
    }

    public function checkADayIsHoliday($dateOfAttendence, $talentId) {
        $publicHolidayStatus = $this->leaves->getDayHolidayStatus($dateOfAttendence);
        $regularHoliday = false;
        $weekends = $this->holidaysWeek->getHolidayTalent($talentId);
        foreach ($weekends as $weekend) {
            $weekendArray[] = strtolower($weekend['week_day']);
        }
        $day = strtolower(date("l", strtotime($dateOfAttendence)));
        $checkDateInStrictlyWorking = $this->holiday->checkDateInStrictlyWorking($dateOfAttendence);
        if (in_array($day, $weekendArray) && ($checkDateInStrictlyWorking == 0)) {
            $regularHoliday = TRUE;
        }
        $isHoliday = ($regularHoliday || $publicHolidayStatus) ? TRUE : FALSE;
        return $isHoliday;
    }

    public function employeeDataUpload() {
        $file = './attendance_files/data.xlsx';
        //load the excel library
        $this->load->library('excel');
        //read file from path
        $objPHPExcel = PHPExcel_IOFactory::load($file);

        //get only the Cell Collection
        $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();
        //extract to a PHP readable array format
        foreach ($cell_collection as $cell) {
            $column = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
            $row = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
            $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getFormattedValue();
            //header will/should be in row 1 only. of course this can be modified to suit your need.
            $arr_data[$row][$column] = $data_value;
        }
        $i = 0;
        foreach ($arr_data as $data) {
            if ($i == 0) {
                $i++;
                continue;
            }

            $this->db->select('*');
            $this->db->from('task_code');
            $this->db->where('task_code', $data['AE']);
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $task = $query->row()->id;
            } else {
                $insertData = array(
                    'task_code' => $data['AE'],
                    'task_desc' => $data['AF'],
                );
                $this->db->insert('task_code', $insertData);
                $task = $this->db->insert_id();
            }
            echo '<pre>';
            var_dump($task);
            echo '</pre>';


            $this->db->select('*');
            $this->db->from('project');
            $this->db->where('project_code', $data['P']);
            $this->db->limit(1);
            $query = $this->db->get();
            if ($query->num_rows()) {
                $project = $query->row()->id;
            } else {
                $insertData = array(
                    'project_code' => $data['P'],
                    'name' => $data['Q'],
                    'description' => $data['R'],
                    'from_date' => $data['S'],
                    'to_date' => $data['T'],
                );
                $this->db->insert('project', $insertData);
                $project = $this->db->insert_id();
            }
            $i++;
            $this->db->select('*');
            $this->db->from('time_prediction');
            $this->db->where('previous_table_id', $data['A']);
            $this->db->limit(1);
            $query = $this->db->get();

            if (!$query->num_rows()) {
                $insertData = array(
                    'talent' => $data['C'],
                    'Project' => $project,
                    'task' => $task,
                    'date' => $data['D'],
                    'time' => $data['G'],
                    'description' => $data['H'],
                    'send_approval' => 1,
                    'previous_table_id' => $data['A'],
                );
                $this->db->insert('time_prediction', $insertData);
                $project = $this->db->insert_id();
                echo '<pre>';
                var_dump($project);
                echo '</pre>';
            }
        }
        die;
    }

}
