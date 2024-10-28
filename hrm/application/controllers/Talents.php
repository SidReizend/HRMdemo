<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require('AllTalentsMonthlyReport.php');

class Talents extends AllTalentsMonthlyReport {

    function __construct() {
        parent::__construct();
        $this->load->model('roles', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('user', '', TRUE);
        $this->load->model('talentAttendance', '', TRUE);
        $this->load->model('talentAttendenceSheet', '', TRUE);
        $this->load->model('leaves', '', TRUE);
        $this->load->model('myOd', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
        $this->load->model('AttendanceManagementModel', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //get details


            $data['talentDetails'] = $this->talent->getTalents();
            $data['resignedTalentDetails'] = $this->talent->getResignedTalentsDetails();

            $reportingManagerDetails = $this->talent->getTalents();
            $reportingManagerArrray = array();
            foreach ($reportingManagerDetails as $reportingManager) {
                $reportingManagerArrray[$reportingManager['id']] = $reportingManager['name'];
            }
            $data['reportingManagers'] = $reportingManagerArrray;
            $this->load->view('talentManagement/talents', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function removeRestrictionForTalent() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //get details


            $data['talentDetails'] = $this->talent->getTalents();

            $this->load->view('talentManagement/talentsRemoveRestrictionForTalent', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addNewTalent() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $roles = $this->roles->getRole();
            $rolesArray = array(
                '' => '[SELECT]'
            );
            foreach ($roles as $role) {
                $rolesArray[$role['id']] = $role['role_name'] . '-' . $role['description'];
            }
            $data['roles'] = $rolesArray;

            $reportingManagers = $this->talent->getReportingManagers();
            $reportingManagersArray = array(
                '' => '[SELECT]'
            );
            foreach ($reportingManagers as $reportingManager) {
                $reportingManagersArray[$reportingManager['id']] = $reportingManager['name'];
            }
            $data['reportingManagers'] = $reportingManagersArray;

            //talent code generation
            $talentCodes = $this->talent->getTalentCode();
            foreach ($talentCodes as $talentCodeSpecific) {
                $talentCodeValue = $talentCodeSpecific['value'];
            }
            if (strlen($talentCodeValue) == '1') {
                $talentCode = '000' . $talentCodeValue;
            } else if (strlen($talentCodeValue) == '2') {
                $talentCode = '00' . $talentCodeValue;
            } else if (strlen($talentCodeValue) == '3') {
                $talentCode = '0' . $talentCodeValue;
            } else {
                $talentCode = $talentCodeValue;
            }
            $data['talentCode'] = $talentCode;

            $this->load->helper(array('form'));
            $this->load->helper('date');

            $this->load->library('form_validation');

            $this->form_validation->set_rules('firstName', 'First Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('middleName', 'Middle Name', 'trim|xss_clean');
            $this->form_validation->set_rules('lastName', 'Last Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('role', 'Role', 'trim|required|xss_clean');
            $this->form_validation->set_rules('address', 'Permanent Address', 'trim|required|xss_clean');
            $this->form_validation->set_rules('present_address', 'Present Address', 'trim|required|xss_clean');
            $this->form_validation->set_rules('qualification', 'Qualification', 'trim|required|xss_clean');
            $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|callback_check_email_valid');
            $this->form_validation->set_rules('dateOfJoining', 'Date of Joining', 'trim|required|xss_clean');
            $this->form_validation->set_rules('emergencyContactName', 'Emergency Contact Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('emergencyContactRelationship', 'Emergency Contact Relationship', 'trim|required|xss_clean');
            $this->form_validation->set_rules('emergencyContactNumber', 'Emergency Contact Number', 'trim|required|xss_clean');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|callback_check_database');

            $role = $this->input->post('role');
            if ($role) {
                $roleDetails = $this->roles->getRoleDetailsById($role);
                $managementRoleStream = $this->defaultValues->getValue(28);
                if ($roleDetails->role_stream != $managementRoleStream) {
                    $this->form_validation->set_rules('reportingManager', 'Reporting Manager', 'trim|required|xss_clean');
                }
            }
            $data['selected_role'] = $this->input->post('role');
            $data['selected_reportingManager'] = $this->input->post('reportingManager');
            $data['selected_isEnabled'] = $this->input->post('isEnabled');

            if ($this->form_validation->run() == TRUE) {
                //Validation Success.  
                $talentCode = $this->input->post('talentCode');
                $firstName = $this->input->post('firstName');
                $middleName = $this->input->post('middleName');
                $lastName = $this->input->post('lastName');
                $role = $this->input->post('role');
                $address = $this->input->post('address');
                $present_address = $this->input->post('present_address');
                $track = $this->input->post('track');
                $qualification = $this->input->post('qualification');
                $gender = $this->input->post('gender');
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dob'));
                $dob = $date->format('Y-m-d');
                $mobile = $this->input->post('mobile');
                $email = $this->input->post('email');
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateOfJoining'));
                $dateOfJoining = $date->format('Y-m-d');
                $emergencyContactName = $this->input->post('emergencyContactName');
                $emergencyContactRelationship = $this->input->post('emergencyContactRelationship');
                $emergencyContactNumber = $this->input->post('emergencyContactNumber');
                $username = $this->input->post('username');
                $password = MD5($this->input->post('password'));
                $is_enabled = $this->input->post('isEnabled');
                // Do insert operation here


                $insertUserData = array(
                    'username' => $username,
                    'password' => md5($username),
                    'is_enabled' => $is_enabled,
                    'email' => $email,
                    'force_password_change' => '1'
                );
                $queryResult = $this->db->insert('users', $insertUserData);
                $insert_id = $this->db->insert_id();
                if ($queryResult) {
                    $talentCodeValue = $talentCode;
                    if (strlen($talentCodeValue) == '1') {
                        $talentCode = '000' . $talentCodeValue;
                    } else if (strlen($talentCodeValue) == '2') {
                        $talentCode = '00' . $talentCodeValue;
                    } else if (strlen($talentCodeValue) == '3') {
                        $talentCode = '0' . $talentCodeValue;
                    } else {
                        $talentCode = $talentCodeValue;
                    }
                    $insertTalentData = array(
                        'talent_code' => $talentCode,
                        'first_name' => $firstName,
                        'middle_name' => $middleName,
                        'last_name' => $lastName,
                        'role' => $role,
                        'address' => $address,
                        'present_address' => $present_address,
                        'qualification' => $qualification,
                        'gender' => $gender,
                        'dob' => $dob,
                        'mobile' => $mobile,
                        'email' => $email,
                        'date_of_joining' => $dateOfJoining,
                        'emergency_contact_name' => $emergencyContactName,
                        'emergency_contact_relationship' => $emergencyContactRelationship,
                        'emergency_contact_number' => $emergencyContactNumber,
                        'reporting_manager' => $data['selected_reportingManager'],
                        'timesheet_manager' => $data['selected_reportingManager'],
                        'user' => $insert_id,
                        'track' => $track
                    );
                    $queryResult = $this->db->insert('talents', $insertTalentData);

                    $insert_id = $this->db->insert_id();
                    $monthOfJoining = date('m', strtotime($dateOfJoining));
                    $yearOfJoining = date('Y', strtotime($dateOfJoining));
                    //finds the el of the employee in the quarter
                    $roles = $this->roles->getRoleById($role);
                    foreach ($roles as $role) {
                        $quarterEL = $role['quarterly_earned_leaves'];
                    }
                    if (1 <= $monthOfJoining && $monthOfJoining < 4) {
                        $date1 = date_create($dateOfJoining);
                        $date2 = date_create("$yearOfJoining-03-31");
                        $difference = date_diff($date1, $date2);
                        $dateDifference = $difference->format("%a");
                        $EL = round(($dateDifference / 90) * $quarterEL);
                    } else if (4 <= $monthOfJoining && $monthOfJoining < 7) {
                        $date1 = date_create($dateOfJoining);
                        $date2 = date_create("$yearOfJoining-06-30");
                        $difference = date_diff($date1, $date2);
                        $dateDifference = $difference->format("%a");
                        $EL = round(($dateDifference / 91) * $quarterEL);
                    } else if (7 <= $monthOfJoining && $monthOfJoining < 10) {
                        $date1 = date_create($dateOfJoining);
                        $date2 = date_create("$yearOfJoining-09-30");
                        $difference = date_diff($date1, $date2);
                        $dateDifference = $difference->format("%a");
                        $EL = round(($dateDifference / 92) * $quarterEL);
                    } else if (10 <= $monthOfJoining) {
                        $date1 = date_create($dateOfJoining);
                        $date2 = date_create("$yearOfJoining-12-31");
                        $difference = date_diff($date1, $date2);
                        $dateDifference = $difference->format("%a");
                        $EL = round(($dateDifference / 92) * $quarterEL);
                    }
                    $insertLeaveData = array(
                        'talent' => $insert_id,
                        'leave_type' => 2,
                        'leave_left' => $EL
                    );
                    $queryResult = $this->db->insert('talent_leave_left', $insertLeaveData);
                    $insertCredit = array(
                        'talent' => $insert_id,
                        'credicted_on' => date('Y-m-d'),
                        'count' => $EL
                    );
                    $queryResult = $this->db->insert('talent_el', $insertCredit);

                    $insertLeaveData = array(
                        'talent' => $insert_id,
                        'leave_type' => 3,
                        'leave_left' => 10
                    );
                    $queryResult = $this->db->insert('talent_leave_left', $insertLeaveData);

                    if ($gender == 'F') {
                        $insertLeaveData = array(
                            'talent' => $insert_id,
                            'leave_type' => 4,
                            'leave_left' => 84
                        );
                    } else {
                        $insertLeaveData = array(
                            'talent' => $insert_id,
                            'leave_type' => 5,
                            'leave_left' => 10
                        );
                    }
                    $queryResult = $this->db->insert('talent_leave_left', $insertLeaveData);

                    $insertLeaveData = array(
                        'talent' => $insert_id,
                        'leave_type' => 1,
                        'leave_left' => 0
                    );
                    $queryResult = $this->db->insert('talent_leave_left', $insertLeaveData);

                    //update new talent code

                    $updateTalentCodeValue = array(
                        'value' => $talentCodeValue + 1,
                    );
                    $this->db->where('id', 1);
                    $queryResultUpdate = $this->db->update('default_values', $updateTalentCodeValue);

                    //send email to system admin to generate email id and process the essl operations 
                    //for the new talent

                    $data['content'] = "Please create email id(" . $email . ") and essl access "
                            . "for the newly joined talent " . $firstName . " " . $middleName . " " . $lastName;
                    $from = $this->defaultValues->getHrmEmail();
                    $to_mails = $this->defaultValues->getTalentAdminEmail();
                    $subject = 'Please Create Email,essl access for the talent';
                    $html = $this->load->view('talentManagement/mail_template', $data, TRUE);
                    $ccMails = $this->talent->getManagersEmails();
                    $hrmName = $this->defaultValues->getValue(19);
                    $this->load->library('PHPMailer');
                    $this->phpmailer->ClearAllRecipients();
                    $this->phpmailer->AddAddress($to_mails);
                    $this->phpmailer->AddCc($ccMails);
                    $this->phpmailer->IsMail();
                    $this->phpmailer->From = $from;
                    $this->phpmailer->FromName = $hrmName;
                    $this->phpmailer->IsHTML(true);
                    $this->phpmailer->Subject = $subject;
                    $this->phpmailer->Body = $html;
                    $result = $this->phpmailer->Send();

                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'New Talent is added successfully');
                    redirect('talents', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                }
            }

            $this->load->view('talentManagement/addNewTalent', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function editTalent() {
        $this->load->helper('date');

        $this->load->helper(array('form'));
        //using url for get id
        $this->load->helper('url');
        $talentId = $this->uri->segment(4);
        $editData['talentId'] = $talentId;
        $roles = $this->roles->getRole();
        $rolesArray = array(
            '' => '[SELECT]'
        );
        foreach ($roles as $role) {
            $rolesArray[$role['id']] = $role['role_name'] . '-' . $role['description'];
        }
        $editData['roles'] = $rolesArray;
        //for get edit details
        $editData['talentDetailsById'] = $this->talent->getTalentById($talentId);
        foreach ($editData['talentDetailsById'] as $talentDetails) {
            $userId = $talentDetails['user'];
            $role = $talentDetails['role'];
        }
        $roleDetails = $this->roles->getRoleById($role);
        foreach ($roleDetails as $roleDetail) {
            $hierarchy = $roleDetail['hierarchy'];
        }
        $reportingManagers = $this->talent->getRolesAboveAndEqualRole($role, $hierarchy, false);
        $reportingManagersArray = array(
            '' => '[SELECT]'
        );
        foreach ($reportingManagers as $reportingManager) {
            $reportingManagersArray[$reportingManager['id']] = $reportingManager['name'];
        }
        $editData['reportingManagers'] = $reportingManagersArray;
        $timeSheetManagers = $this->talent->getRolesAboveAndEqualRole($role, $hierarchy, false);
        $timeSheetManagersArray = array(
            '' => '[SELECT]'
        );
        foreach ($timeSheetManagers as $timeSheetManager) {
            $timeSheetManagersArray[$timeSheetManager['id']] = $timeSheetManager['name'];
        }
        $editData['timeSheetManagers'] = $timeSheetManagersArray;


        if ($this->input->post()) {

            $this->load->library('form_validation');

            $this->form_validation->set_rules('firstName', 'First Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('middleName', 'Middle Name', 'trim|xss_clean');
            $this->form_validation->set_rules('lastName', 'Last Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('role', 'Role', 'trim|required|xss_clean');
            $this->form_validation->set_rules('address', 'Permanent Address', 'trim|required|xss_clean');
            $this->form_validation->set_rules('present_address', 'Present Address', 'trim|required|xss_clean');
            $this->form_validation->set_rules('qualification', 'Qualification', 'trim|required|xss_clean');
            $this->form_validation->set_rules('dob', 'Date of Birth', 'trim|required|xss_clean');
            $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|callback_check_email_valid');
            $this->form_validation->set_rules('dateOfJoining', 'Date of Joining', 'trim|required|xss_clean');
            $this->form_validation->set_rules('emergencyContactName', 'Emergency Contact Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('emergencyContactRelationship', 'Emergency Contact Relationship', 'trim|required|xss_clean');
            $this->form_validation->set_rules('emergencyContactNumber', 'Emergency Contact Number', 'trim|required|xss_clean');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|xss_clean|callback_check_database_username');

            $role = $this->input->post('role');
            if ($role) {
                $roleDetails = $this->roles->getRoleDetailsById($role);
                $managementRoleStream = $this->defaultValues->getValue(28);
                if ($roleDetails->role_stream != $managementRoleStream) {
                    $this->form_validation->set_rules('reportingManager', 'Reporting Manager', 'trim|required|xss_clean');
                }
            }

            $data['selected_role'] = $this->input->post('role');
            $data['selected_reportingManager'] = $this->input->post('reportingManager');
            $data['selected_timeSheetManager'] = $this->input->post('reportingManager');
            $data['selected_isEnabled'] = $this->input->post('isEnabled');

            $firstName = $this->input->post('firstName');
            $middleName = $this->input->post('middleName');
            $lastName = $this->input->post('lastName');
            $role = $this->input->post('role');
            $address = $this->input->post('address');
            $present_address = $this->input->post('present_address');
            $qualification = $this->input->post('qualification');
            $gender = $this->input->post('gender');
            $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dob'));
            $dob = $date->format('Y-m-d');
            $mobile = $this->input->post('mobile');
            $email = $this->input->post('email');
            $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateOfJoining'));
            $dateOfJoining = $date->format('Y-m-d');
            $emergencyContactName = $this->input->post('emergencyContactName');
            $emergencyContactRelationship = $this->input->post('emergencyContactRelationship');
            $emergencyContactNumber = $this->input->post('emergencyContactNumber');
            $reportingManager = $this->input->post('reportingManager');
            $timeSheetManager = $this->input->post('reportingManager');
            $username = $this->input->post('username');
            $track = $this->input->post('track');
            $profile_image = $this->input->post('profile_image');
//                $password = MD5($this->input->post('password'));
            $isEnabled = $this->input->post('isEnabled');
            $autoOdApproval = $this->input->post('autoOdApproval');
            $autoTimeApproval = $this->input->post('autoTimeApproval');
            $isResigned = $this->input->post('isResigned');
            $dateOfResignation = date('Y-m-d');
            if (1 == $this->input->post('isResigned')) {
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateOfResignation'));
                $dateOfResignation = $date->format('Y-m-d');
                $startDate = $this->talent->getStartAndEndDateToFindReport($talentId, $dateOfResignation);
                $this->form_validation->set_rules('dateOfResignation', 'Date Of Resignation', 'trim|required|xss_clean');
            }

            $fromDate = $this->defaultValues->getValue(3);
            if (1 == $this->input->post('isResigned') && (strtotime($dateOfResignation) < strtotime($fromDate))) {
                $this->session->set_flashdata('flashError', 'Already monthly process done for that date');
            } else if ($this->form_validation->run() == TRUE) {
                $currentWorkingDirectory = getcwd();
                if (1 == $this->input->post('isResigned')) {
                    $this->deleteAttendendanceDetailsAfterResignDate($talentId, $dateOfResignation);
//                    $this->AttendanceManagementModel->doResignProcess($talentId, $startDate, $dateOfResignation);
                    $this->runProcessTalent($talentId, $dateOfResignation, $isResigned);
//                    die('sdfsdfdsfdsf');
                }
                //$generateReport = $this->talent->resignedReport($talentId,$dateOfResignation);
                //die();
                //Validation Success.  
                // Do update operation here
                $updateTalentData = array(
                    'id' => $talentId,
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'role' => $role,
                    'address' => $address,
                    'present_address' => $present_address,
                    'qualification' => $qualification,
                    'gender' => $gender,
                    'dob' => $dob,
                    'mobile' => $mobile,
                    'email' => $email,
                    'date_of_joining' => $dateOfJoining,
                    'emergency_contact_name' => $emergencyContactName,
                    'emergency_contact_relationship' => $emergencyContactRelationship,
                    'emergency_contact_number' => $emergencyContactNumber,
                    'reporting_manager' => $reportingManager,
                    'timesheet_manager' => $timeSheetManager,
                    'is_resigned' => $isResigned,
                    'od_approval_auto' => $autoOdApproval,
                    'autoTimeApproval' => $autoTimeApproval,
                    'track' => $track,
                );
                if (1 == $this->input->post('isResigned')) {
                    $updateTalentData['date_of_resignation'] = $dateOfResignation;
                } else {
                    $updateTalentData['date_of_resignation'] = '0000-00-00';
                }


                $config['upload_path'] = './uploads/';
                $config['allowed_types'] = 'gif|jpg|png';
//                $config['max_size'] = 300;
//                $config['max_width'] = 500;
//                $config['max_height'] = 500;
                $this->load->library('upload', $config);

                $upload_data = $this->upload->data();

                $upload_error = false;

                if ($_FILES['profile_image']['error'] != 4) {
                    if (!$this->upload->do_upload('profile_image')) {
                        $upload_error = true;
                        $this->session->set_flashdata('flashError', $this->upload->display_errors());
                    } else {
                        $upload_data = $this->upload->data();

                        $updateTalentData['profile_image'] = $upload_data['file_name'];
                    }
                }
                if (false === $upload_error) {


                    $this->db->trans_begin();
                    $this->db->where('id', $talentId);
                    $queryResultTalentUpdate = $this->db->update('talents', $updateTalentData);
                    if ($queryResultTalentUpdate) {
                        //for get user id
                        $talentData = $this->talent->getTalentById($talentId);
                        foreach ($talentData as $talentDetails) {
                            $userId = $talentDetails['user'];
                        }
                        $updateUserData = array(
                            'username' => $username,
                            'email' => $email,
                            'is_enabled' => $isEnabled,
                        );
                        $this->db->where('id', $userId);
                        $queryResultUpdate = $this->db->update('users', $updateUserData);
                        $this->db->trans_commit();
                        $this->session->set_flashdata('flashSuccess', 'Talent Details Updated successfully');
                        redirect('talents', 'refresh');
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('flashError', 'Some error found');
                    }
                }
            }

            $editData["userDetailsByTalentId"] = [0 => array('username' => $username, 'is_enabled' => $isEnabled)];
            $editData["talentDetailsById"] = [0 => array(
                    "first_name" => $firstName,
                    "middle_name" => $middleName,
                    "last_name" => $lastName,
                    "role" => $role,
                    "address" => $address,
                    "present_address" => $present_address,
                    "qualification" => $qualification,
                    "gender" => $gender,
                    "dob" => $dob,
                    "mobile" => $mobile,
                    "email" => $email,
                    "date_of_joining" => $dateOfJoining,
                    "emergency_contact_name" => $emergencyContactName,
                    "emergency_contact_relationship" => $emergencyContactRelationship,
                    "emergency_contact_number" => $emergencyContactNumber,
                    "date_of_resignation" => $dateOfResignation,
                    "reporting_manager" => $reportingManager,
                    "timesheet_manager" => $timeSheetManager,
                    "is_resigned" => $isResigned,
                    'od_approval_auto' => $autoOdApproval,
                    'autoTimeApproval' => $autoTimeApproval,
                    'track' => $track,
            )];
        } else {
            $editData['userDetailsByTalentId'] = $this->user->getUserByUserId($userId);
        }

        $profile_image_path = $this->talent->getProfileImage($talentId);
        $editData['profile_image'] = $profile_image_path;

        $this->load->view('talentManagement/editTalent', $editData);
    }

    function check_database($username) {
        //Field validation succeeded.  Validate against database
        $username = $this->input->post('username');

        //query the database
        $result = $this->user->getUsername($username);

        if ($result) {
            $this->form_validation->set_message('check_database', 'Username already exists');
            return false;
        } else {
            return TRUE;
        }
    }

    function check_email_valid($username) {
        //Field validation succeeded.  Validate against database
        $email = $this->input->post('email');

        //query the database
        $mailMatch = $this->defaultValues->getValue(16);
        $emailSplit = explode("@", $email);
        $mailMatchArray = explode(",", $mailMatch);
        foreach ($mailMatchArray as $mail) {
            if ($emailSplit[1] == $mail) {
                return TRUE;
            }
        }
        $this->form_validation->set_message('check_email_valid', 'Email is not in the format ' . $mailMatch);
        return false;
    }

    function check_database_username($username) {
        //Field validation succeeded.  Validate against database
        $username = $this->input->post('username');
        $talentId = $this->uri->segment(4);
        //for get user id
        $talentData = $this->talent->getTalentById($talentId);
        foreach ($talentData as $talentDetails) {
            $userId = $talentDetails['user'];
        }
        //query the database
        $result = $this->user->getUsernameUserId($username, $userId);

        if ($result) {
            $this->form_validation->set_message('check_database_username', 'Username already exists');
            return false;
        } else {
            return TRUE;
        }
    }

    function reportingManagerChange() {
        $this->load->helper(array('form'));
        $role = $this->input->post('role');
        $dataRportingManager['role'] = $role;
        $roleDetails = $this->roles->getRoleById($role);
        foreach ($roleDetails as $roleDetail) {
            $hierarchy = $roleDetail['hierarchy'];
        }
        $reportingManagers = $this->talent->getRolesAboveRole($role, $hierarchy);
        $reportingManagersArray = array(
            '' => '[SELECT]'
        );
        foreach ($reportingManagers as $reportingManager) {
            $reportingManagersArray[$reportingManager['id']] = $reportingManager['name'];
        }
        $reportingData['reportingManagers'] = $reportingManagersArray;
        $this->load->view('talentManagement/reportingManagerChange', $reportingData);
    }

    public function viewTalentDetails() {
        $this->load->helper('date');

        $this->load->helper(array('form'));
        $session_data = $this->session->userdata('logged_in');
        //using url for get id
        $this->load->helper('url');
        $talentId = $session_data['talentId'];
        $editData['talentId'] = $session_data['talentId'];

        //for get edit details
        $editData['talentDetailsById'] = $this->talent->getTalentById($talentId);
        $reportingManagerId = '';
        $userId = '';
        foreach ($editData['talentDetailsById'] as $talentDetails) {
            $userId = $talentDetails['user'];
            $reportingManagerId = $talentDetails['reporting_manager'];
        }
        //ger reporting manager
        $reportingManagerDetails = $this->talent->getTalentById($reportingManagerId);
        $reportingManager = '';
        foreach ($reportingManagerDetails as $reportingManagerDetail) {
            $reportingManager = $reportingManagerDetail['name'];
        }
        $editData['reportingManagers'] = $reportingManager;
        $editData['userDetailsByTalentId'] = $this->user->getUserByUserId($userId);

        $profile_image_path = $this->talent->getProfileImage($talentId);
        $editData['profile_image'] = $profile_image_path;

        $this->load->view('talentManagement/viewTalentDetails', $editData);
    }

    public function checkResignIssues() {
        $this->load->helper('url');
        $this->load->helper('date');
        $dateOfResignation = $this->input->get('resignDate');
        $date = DateTime::createFromFormat('d/m/Y', $this->input->get('resignDate'));
        $dateOfResignation = $date->format('Y-m-d');
        $talentId = $this->input->get('talentId');
        //find start date
        $startDate = $this->talent->getStartAndEndDateToFindReport($talentId, $dateOfResignation);
        //find attendance uploaded for the resigned date
//        $status = $this->talentAttendenceSheet->dateUploadCheck($dateOfResignation);
//        if ($status == false) {
//            echo 'Please upload attendance for the resignation date';
//            return;
//        }
        $talentDetails = $this->talent->getTalentDetailsByTalentId($talentId);
        if (($talentDetails->role_stream) == $this->defaultValues->getValue(28)) {
            echo true;
            return;
        }
        //find approval pending
        $status = $this->myOd->getCountOdWaitingTalent($talentId);
        if ($status == false) {
            echo 'Please process od before setting resign date';
            return false;
        }
        $status = $this->leaves->getCountLeaveWaitingTalent($talentId);
        if ($status == false) {
            echo 'Please process Leave before setting resign date';
            return false;
        }
        $status = $this->talentAttendenceSheet->getCountCompWaitingTalent($talentId);
        if ($status == false) {
            echo 'Please process Comp off before setting resign date';
            return false;
        }
        $is_encashment = ($this->defaultValues->getValue(26) == 1) ? true : false;
        if ($is_encashment) {
            $status = $this->talentAttendenceSheet->getCountencashmentWaitingTalent($talentId);
            if ($status == false) {
                echo 'Please process encashment off before setting resign date';
                return false;
            }
        }
        echo true;
    }

    public function deleteAttendendanceDetailsAfterResignDate($talent, $date) {
        $this->db->where('attendance_date >', $date);
        $this->db->where('talent_id', $talent);
        $queryResult = $this->db->delete('talent_attendance');
        return;
    }

    public function removeTimesheetRestriction() {
        $talent = $this->input->post('talent');
        $checked = $this->input->post('checked');
        $updateValue = array(
            'timesheet_restriction' => $checked,
        );
        $this->db->where('id', $talent);
        $this->db->update('talents', $updateValue);
        echo 1;
    }

}
