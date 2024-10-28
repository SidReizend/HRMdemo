<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MySkill extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('MySkillModel', '', TRUE);
    }

    /*
     * index page of skill will display all skills and year of experience
     */

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $this->load->helper(array('form'));
            $session_data = $this->session->userdata('logged_in');
            /* Get skills for listing */
            $talentId = $session_data['id'];
            $getUserSkills = $this->MySkillModel->getUserSkills($talentId);
            $data['userSkills'] = $getUserSkills;
            $skillIdArray = [];
            if (isset($getUserSkills)) {
                foreach ($getUserSkills as $skillId) {
                    $skillIdArray[] = $skillId['skills'];
                }
            }
            $skills = $this->MySkillModel->getAllSkills($skillIdArray);
            $skillArray = array('' => '[Select Skill]');
            foreach ($skills as $skill) {
                $skillArray[$skill['id']] = $skill['skill_name'];
            }
            $skillArray['others'] = 'Others';
            $data['skills'] = $skillArray;
            /* Number of years */
            $yearsArray = array('' => '[Select Year]');
            for ($year = 0; $year <= 25; $year++) {
                $yearsArray[$year] = $year;
            }
            $data['years'] = $yearsArray;
            /* Number of years end */
            /* Number of Months */
            $monthArray = array('' => '[Select month]');
            for ($month = 0; $month <= 11; $month++) {
                $monthArray[$month] = $month;
            }
            $data['month'] = $monthArray;
            /* Number of months end */
            $this->load->view('MySkills/addSkills', $data);
        } else {
            redirect('login', 'refresh');
        }
    }

    /*
     * Talent add new skills and its view
     */

    public function addNewSkill() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $talentId = $session_data['id'];
            $skill = $this->input->post('skills');
            $yearOfExperience = $this->input->post('yearOfExperience');
            $monthOfExperience = $this->input->post('monthOfExperience');
            $monthOfExperience = ($monthOfExperience == 0) ? ' ': $monthOfExperience;
            $skillLevel = $this->input->post('skillLevel');
            $this->db->trans_begin();
            /* Insert data into skills if not in skill table */
            $getApprovedSkills = '';
            if ($skill == 'others') {
                $skillName = $this->input->post('otherSkills');
                /* Not approved skills with same user add restrict */
                $getNotApprovedSkills = $this->MySkillModel->getNotApprovedSkills($skillName, $talentId);
                if (!empty($getNotApprovedSkills)) {
                    $this->session->set_flashdata('flashError', 'You already added the same skill!');
                    redirect('MySkill', 'refresh');
                }
                /* approved skills again add restrict */
                $getNotApprovedSkills = $this->MySkillModel->getApprovedSkills($skillName);

                if (!empty($getApprovedSkills)) {
                    $this->session->set_flashdata('flashError', 'Skills already exist');
                    redirect('MySkill', 'refresh');
                }
                $insertSkill = array(
                    'skill_name' => $skillName,
                    'is_approved' => 0,
                );
                $this->db->insert('skills', $insertSkill);
                $insertId = $this->db->insert_id();
                $skill = $insertId;
            }
            $this->load->helper(array('form'));
            if (!empty($skill) && $yearOfExperience != '') {
                $insertData = array(
                    'skills' => $skill,
                    'user_id' => $talentId,
                    'year_of_experience' => $yearOfExperience.'.'.$monthOfExperience,
                    'skill_level' => $skillLevel,
                );

                $queryResult = $this->db->insert('my_skills', $insertData);

                if ($queryResult) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'New Skill added');
                    redirect('MySkill', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found!');
                }
            } else {
                $this->session->set_flashdata('flashError', 'Some error found');
                redirect('MySkill', 'refresh');
            }
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    /*
     * Display all skills for admin view
     */

    public function viewSkills() {
        if ($this->session->userdata('logged_in')) {
            $this->load->helper(array('form'));
            $session_data = $this->session->userdata('logged_in');
            $skills = $this->MySkillModel->getAllSkills();
            $skillArray = array('' => '[Select Skill]');
            foreach ($skills as $skill) {
                $skillArray[$skill['id']] = $skill['skill_name'];
            }
            $data['skills'] = $skillArray;
            /* Number of years */
            $yearsArray = array('' => '[Select Year]');
            for ($year = 1; $year <= 25; $year++) {
                $yearsArray[$year] = $year;
            }
            $data['years'] = $yearsArray;
            // return print_r(json_encode($data));
            $this->load->view('MySkills/skillsView', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function skillsSearch() {
        if ($this->session->userdata('logged_in')) {
            $this->load->helper(array('form'));
            $skill = $this->input->post('skills');
            $yearOfExperience = explode(',', $this->input->post('yearOfExperience'));
            $yearOfExperienceStart = $yearOfExperience[0];
            $yearOfExperienceEnd = $yearOfExperience[1];
            if (empty($skill)) {
                $userSkills = array();
            } else {
                $skillName = $this->MySkillModel->getSkillName($skill);
                /* Get all talents with skills */
                $userId = $this->MySkillModel->getAllApprovedUserId($skill, $yearOfExperienceStart, $yearOfExperienceEnd, $approved = 1);
                $userSkills = $this->MySkillModel->getAllApprovedUserSkills($userId['userId']);
                $data['skills'] = $skillName['skill_name'];
            }
            $userArray = array();
            if (isset($userSkills)) {
                foreach ($userSkills as $userSkill) {
                    $skills = explode(',', $userSkill['skillName']);
                    $year = explode(',', $userSkill['yearExp']);
                    $skillLevel = explode(',', $userSkill['skillLevel']);
                    $userArray[$userSkill['user_id']]['skill'] = $skills;
                    $userArray[$userSkill['user_id']]['year'] = $year;
                    $userArray[$userSkill['user_id']]['skill_level'] = $skillLevel;
                    $userArray[$userSkill['user_id']]['name'] = $userSkill['first_name'] . ' ' . $userSkill['middle_name'] . ' ' . $userSkill['last_name'];
                }
            }
            $data['userArray'] = $userArray;
            $dataView['userTable'] = $this->load->view('MySkills/ajaxTableView', $data, TRUE);
            return print_r(json_encode($dataView));
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    /*
     * Shows newly added skills for approval
     */

    public function manageSkill() {
        if ($this->session->userdata('logged_in')) {
            $this->load->helper(array('form'));
            /* Get all talents with skills */
            $data['userSkills'] = $this->MySkillModel->getAllUserSkills($approved = 0);
            $this->load->view('MySkills/manageNewSkills', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    /* My skills remove */

    public function removeSkill() {
        if ($this->session->userdata('logged_in')) {
            $this->load->helper(array('form'));
            $session_data = $this->session->userdata('logged_in');
            $talentId = $session_data['id'];
            $skill = $this->input->post('skillId');
            $getNotApprovedSkills = $this->MySkillModel->getNotApprovedSkillsById($skill, $talentId);
            if (!empty($getNotApprovedSkills)) {
                $status = $this->MySkillModel->deleteSkill($skill);
                $status = $this->MySkillModel->deleteUserUnApprovedSkills($getNotApprovedSkills['skills']);
            } else {
                $status = $this->MySkillModel->deleteSkill($skill);
            }
            if ($status == TRUE) {
                $this->session->set_flashdata('flashSuccess', 'Skill Removed');
            }
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    /*
     *  Approve other skill added by user 
     */

    public function approveSkills() {
        if ($this->session->userdata('logged_in')) {
            $skillId = $this->input->post('skillId');
            $skillName = $this->input->post('skillName');
            $status = 'error';
            $skillArray = $this->MySkillModel->getApprovedSkills($skillName);
            if (!empty($skillArray)) {
                $status = "error";
                return print_r(json_encode(['status' => $status]));
                die;
            }
            $updateData = [
                'is_approved' => 1,
                'skill_name' => $skillName
            ];
            $status = $this->MySkillModel->approveSkills($skillId, $updateData);
            $status = 'success';
            $this->session->set_flashdata('flashSuccess', 'Skill approved');
            return print_r(json_encode(['status' => $status]));
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    /*
     * Replace skill model view with skill name like current name.
     * Here is a option to replace a skill with similar one.
     */

    public function replcaeSkillModel() {
        if ($this->session->userdata('logged_in')) {
            $skillName = $this->input->post('skillName');
            $user = $this->input->post('user');
            $skillByUser = $this->MySkillModel->getSkillByUserId($user);
            $skills = $this->MySkillModel->getLikeSkills($skillByUser['skills']);
            $skillArray = array();
            if (isset($skillArray)) {
                foreach ($skills as $skill) {
                    $skillArray[$skill['id']] = $skill['skill_name'];
                }
            }
            return print_r(json_encode($skillArray));
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    /*
     * If same skills added by different user replace one with other
     */

    public function replaceSkill() {
        if ($this->session->userdata('logged_in')) {
            $replacedWithSkill = $this->input->post('replacedWithSkill');
            $replaceSkill = $this->input->post('replaceSkill');
            $updateSkill = [
                'skills' => $replacedWithSkill
            ];
            $this->db->trans_begin();
            $status = $this->MySkillModel->updateSkill($replaceSkill, $updateSkill);
            if ($status == TRUE) {
                $status = $this->MySkillModel->deleteUserUnApprovedSkills($replaceSkill);
                $this->db->trans_commit();
                $this->session->set_flashdata('flashSuccess', 'Skill replaced');
                return print_r(json_encode($status));
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flashError', 'Some error found');
                return print_r(json_encode($status));
            }
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    /*
     * Delete newly added skill. if it is not needed by admin
     */

    public function deleteSkills() {
        if ($this->session->userdata('logged_in')) {
            $skillId = $this->input->post('skill');
            $mySkillId = $this->input->post('myskill');
            $this->db->trans_begin();
            /* delete mapped skill */
            $status = $this->MySkillModel->deleteSkill($mySkillId);
            if($status==TRUE){
                /* delete skills */
                $status = $this->MySkillModel->deleteUserUnApprovedSkills($skillId);
                $this->db->trans_commit();
                $this->session->set_flashdata('flashSuccess', 'Skill Successfully Deleted');
                return print_r(json_encode($status));
            }
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

}