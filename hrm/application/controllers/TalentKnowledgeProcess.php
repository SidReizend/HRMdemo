<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TalentKnowledgeProcess
 *
 */
class TalentKnowledgeProcess extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('KnowledgeRepository', '', TRUE);
        $this->load->model('KnowledgeProcess', '', TRUE);
        $this->load->model('roles', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
        $this->load->model('Tag', '', TRUE);
    }

    public function index() {
        //list all knowledge repo with verifiled=0
        $sess_data = $this->session->userdata('logged_in');

        if ($sess_data['ip'] == $sess_data['localIp']) {
            $talent = $sess_data['talentId'];
            //condition for checking the talent is either reporting manager or managerial position
            if ($sess_data['role_stream_id'] == $this->defaultValues->getValue(28)) {
                $data['knowledge'] = $this->KnowledgeProcess->getAllTalentKnowledge();
            } else {
                $data['knowledge'] = $this->KnowledgeProcess->getTalentsKnowledgeUnderReportingManager($talent);
            }


            $this->load->view('talentKnowledgeProcess/knowledge', $data);
        } else {
            redirect('home', 'refresh');
        }
    }

    public function viewPendingVerification() {
        $sess_data = $this->session->userdata('logged_in');
        if ($sess_data['ip'] == $sess_data['localIp']) {
            $talent = $sess_data['talentId'];


            $data['knowledge'] = $this->KnowledgeProcess->getKnowledgeforVerification($talent);

            $data['redirect_to'] = 'TalentKnowledgeProcess/viewPendingVerification';
            $this->load->view('talentKnowledgeProcess/viewPendingVerification', $data);
        } else {
            redirect('home', 'refresh');
        }
    }

    public function editPendingKnowledge() {
        //getting user detatils from session
        $sess_data = $this->session->userdata('logged_in');
        if ($sess_data['ip'] == $sess_data['localIp']) {
            $talent = $sess_data['talentId'];
            $this->load->helper(array('form'));

            //using url for get id
            $this->load->helper('url');
            $knowledge_id = $this->uri->segment(3);
            $editData['id'] = $knowledge_id;

            //assign role for the view
            $roleStreams = $this->roles->getRoleStreams();
            $roleStreamArray = array();

            foreach ($roleStreams as $roleStream) {
                $roleStreamArray[$roleStream['id']] = $roleStream['role_stream'];
            }

            $editData['role_stream'] = $roleStreamArray;

            $tags = $this->Tag->viewAllTag();
            $tagArray = array();

            foreach ($tags as $tag) {
                $tagArray[$tag['id']] = $tag['tag_name'];
            }

            $editData['tag'] = $tagArray;
            $editData['knowledge'] = $this->KnowledgeRepository->getKnowledgeById($knowledge_id);
            $editData['knowledgeRoleStream'] = $this->KnowledgeRepository->getSelectedRoleStreamByKnowledgeId($knowledge_id);
            $editData['selectedTagArray'] = $this->Tag->getSelectedTagByKnowledgeId($knowledge_id);
            $login_details = $this->session->userdata('logged_in');

            if ($this->input->post()) {



                $talentsKnowledge = array();
                $fileName = '';
                $this->load->library('form_validation');
                $this->form_validation->set_rules('heading', 'Heading', 'trim|required|xss_clean');
                $this->form_validation->set_rules('note', 'Note', 'trim|required|xss_clean');
                $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');

                $this->form_validation->set_rules('role_stream[]', 'Role', 'trim|required|xss_clean');


                if ($this->form_validation->run() == TRUE) {


                    $config['upload_path'] = './uploads/';
                    $config['allowed_types'] = 'txt|xls|xlsx|doc|docx|pdf';
                    $this->load->library('upload', $config);
                    $upload_data = $this->upload->data();


                    //check whether a new file is uploaded
                    $talentsKnowledge = array();
                    $upload_error = false;
                    if (isset($upload_data['file_name'])) {

                        if ($_FILES['file_name']['error'] != 4) {

                            if (!$this->upload->do_upload('file_name')) {

                                $upload_error = true;
                                $this->session->set_flashdata('flashError', $this->upload->display_errors());
                            } else {


                                $upload_data = $this->upload->data();
                                $fileName = $upload_data['file_name'];

                                if (false === $upload_error) {

                                    $talentsKnowledge['file_name'] = $fileName;
                                }
                            }
                        }
                    }
                    $heading = $this->input->post('heading');
                    $description = $this->input->post('description');
                    $knowledgeId = $this->input->post('knowledgeId');
                    $note = $this->input->post('note');
                    $user_file = $this->input->post('old_file');
                    $old_note = $this->input->post('old_note');

                    $note = $this->input->post('note');

                    $user_note = $old_note . ".  " . $sess_data['firstName'] . " " . $sess_data['middleName'] . " " .
                            $sess_data['lastName'] . " Verified and Updated on " . date('Y-m-d H:i:s') . " commented with " . $note;


                    $talentsKnowledge['heading'] = $heading;
                    $talentsKnowledge['description'] = $description;
                    $talentsKnowledge['modified_on'] = date('d/m/Y H:i A');
                    $talentsKnowledge['note'] = $user_note;
                    $talentsKnowledge['updated_by'] = $talent;

                    $role_stream = $this->input->post('role_stream[]');
                    $tag_array = $this->input->post('tag_array[]');
                    $this->db->trans_begin();
                    $this->db->where('id', $knowledgeId);
                    $KnowledgeUpdate = $this->db->update('knowledge_repo', $talentsKnowledge);

                    if ($KnowledgeUpdate) {

                        // $old_file= base_url()."upload/".$this->input->post('old_file');

                        $this->db->where('knowledge_repo', $knowledgeId);
                        $this->db->delete('knowledge_repo_stream');
                        foreach ($role_stream as $role_stream) {
                            $insertRoleKnowledge = array(
                                'knowledge_repo' => $knowledgeId,
                                'role_stream' => $role_stream,
                            );
                            $queryResult = $this->db->insert('knowledge_repo_stream', $insertRoleKnowledge);
                        }
                        $this->db->where('knowledge_repo', $knowledgeId);
                        $this->db->delete('knowledge_repo_tags');

                        foreach ($tag_array as $tag) {
                            $insertTags = array(
                                'knowledge_repo' => $knowledgeId,
                                'tag' => $tag,
                            );
                            $queryResult = $this->db->insert('knowledge_repo_tags', $insertTags);
                        }
                        $this->db->trans_commit();

                        $config = Array(
                            'mailtype' => 'html',
                        );

                        $this->load->library('email', $config);
                        $data['name'] = $sess_data['firstName'] . " " . $sess_data['middleName'] . " " . $sess_data['lastName'];
                        $data['heading'] = $user_note;
                        $from = $this->defaultValues->getHrmEmail();
                        $hrmName = $this->defaultValues->getValue(19);
                        $subject = $hrmName . ': KSHOP Content Updated ';
                        $html = $this->load->view('talentsKnowledge/mail_template', $data, TRUE);
//                        $this->email->from($from, $hrmName);
                        $roles = $this->roles->getRoleIdByStramId($this->defaultValues->getValue(28));
                        $role_id = array();

                        foreach ($roles as $role) {
                            array_push($role_id, $role['id']);
                        }
                        //var_dump($role_id);
//                        $role_mails = $this->roles->getEmailIdByRole($role_id);
                        // var_dump($role_mails);
//                        $this->email->to($role_mails);
//                        $this->email->subject($subject);
//                        $this->email->message($html);
//                        $this->email->send();
                        //die();
                        $this->session->set_flashdata('flashSuccess', 'Talent Knowledge Updated successfully');

                        redirect('TalentKnowledgeProcess/viewPendingVerification', 'refresh');
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('flashError', 'Some error found');
                    }
                }
            }
            $this->load->view('talentKnowledgeProcess', $editData);
        } else {
            redirect('home', 'refresh');
        }
    }

    public function editKnowledge() {
        //config upload path
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'txt|xls|xlsx|doc|docx|pdf';

        //loading libraries
        $this->load->library('upload', $config);
        $this->load->library('form_validation');

        //getting user detatils from session
        $sess_data = $this->session->userdata('logged_in');

        if ($sess_data['ip'] == $sess_data['localIp']) {
            $talent = $sess_data['talentId'];
            $this->load->helper(array('form'));

            //using url for get id
            $this->load->helper('url');
            $knowledge_id = $this->uri->segment(3);
            $editData['id'] = $knowledge_id;

            //assign role for the view
            $roleStreams = $this->roles->getRoleStreams();
            $roleStreamArray = array();

            foreach ($roleStreams as $roleStream) {
                $roleStreamArray[$roleStream['id']] = $roleStream['role_stream'];
            }

            $editData['role_stream'] = $roleStreamArray;

            $tags = $this->Tag->viewAllTag();
            $tagArray = array();

            foreach ($tags as $tag) {
                $tagArray[$tag['id']] = $tag['tag_name'];
            }

            $editData['tag'] = $tagArray;

            $editData['knowledge'] = $this->KnowledgeRepository->getKnowledgeById($knowledge_id);
            $editData['knowledgeRoleStream'] = $this->KnowledgeRepository->getSelectedRoleStreamByKnowledgeId($knowledge_id);

            $login_details = $this->session->userdata('logged_in');
            $is_management = $login_details['is_management'];
            $talentId_user = $login_details['talentId'];
            $subordinates = $this->talent->getSubordinates($talentId_user, $is_management);
            $talentsArray = array(
                '' => 'SELECT'
            );
            foreach ($subordinates as $subord) {
                $talentsArray[$subord['id']] = $subord['name'];
            }
            $editData['talentsUnderReportManager'] = $talentsArray;

            $editData['selectedTagArray'] = $this->Tag->getSelectedTagByKnowledgeId($knowledge_id);






            $talentsKnowledge = array();
            $fileName = '';


            if ($this->input->post()) {


                $config['upload_path'] = './uploads/';
                $config['allowed_types'] = 'txt|xls|xlsx|doc|docx|pdf';
                $this->load->library('upload', $config);
                $upload_data = $this->upload->data();


                //check whether a new file is uploaded
                $talentsKnowledge = array();
                $upload_error = false;
                if (isset($upload_data['file_name'])) {

                    if ($_FILES['file_name']['error'] != 4) {

                        if (!$this->upload->do_upload('file_name')) {

                            $upload_error = true;
                            $this->session->set_flashdata('flashError', $this->upload->display_errors());
                        } else {


                            $upload_data = $this->upload->data();
                            $fileName = $upload_data['file_name'];

                            if (false === $upload_error) {

                                $talentsKnowledge['file_name'] = $fileName;
                            }
                        }
                    }
                }
                if (false === $upload_error) {
                    $heading = $this->input->post('heading');
                    $description = $this->input->post('description');
                    $knowledgeId = $this->input->post('knowledgeId');
                    $note = $this->input->post('note');

                    $user_note = $this->input->post('old_note');
                    $assigned_to = $this->input->post('assigned_to');
                    $note = $this->input->post('note');
                    $talent_email = '';
                    foreach ($subordinates as $subordinates) {
                        if ($subordinates['id'] == $assigned_to) {
                            $talent_name = $subordinates['name'];
                            $talent_email = $subordinates['email'];
                        }
                    }
                    if ($note) {
                        $user_note .= " || " . $sess_data['firstName'] . " " . $sess_data['middleName'] . " " .
                                $sess_data['lastName'] . " commented: " . $note;
                    }
                    $talentsKnowledge['heading'] = $heading;
                    $talentsKnowledge['description'] = $description;
                    $talentsKnowledge['modified_on'] = date('d/m/Y H:i A');
                    $talentsKnowledge['note'] = $user_note;
                    $talentsKnowledge['updated_by'] = $talent;
                    $talentsKnowledge['assigned_to'] = $assigned_to;


                    $role_stream = $this->input->post('role_stream[]');
                    $tag_array = $this->input->post('tag_array[]');
                    $this->db->trans_begin();
                    $this->db->where('id', $knowledgeId);
                    $KnowledgeUpdate = $this->db->update('knowledge_repo', $talentsKnowledge);

                    if ($KnowledgeUpdate) {


                        $this->db->where('knowledge_repo', $knowledgeId);
                        $this->db->delete('knowledge_repo_stream');
                        foreach ($role_stream as $role_stream) {
                            $insertRoleKnowledge = array(
                                'knowledge_repo' => $knowledgeId,
                                'role_stream' => $role_stream,
                            );
                            $queryResult = $this->db->insert('knowledge_repo_stream', $insertRoleKnowledge);
                        }
                        $this->db->where('knowledge_repo', $knowledgeId);
                        $this->db->delete('knowledge_repo_tags');

                        foreach ($tag_array as $tag) {
                            $insertTags = array(
                                'knowledge_repo' => $knowledgeId,
                                'tag' => $tag,
                            );
                            $queryResult = $this->db->insert('knowledge_repo_tags', $insertTags);
                        }

                        $this->db->trans_commit();

                        $config = Array(
                            'mailtype' => 'html',
                        );

                        $this->load->library('email', $config);
                        $data['name'] = $sess_data['firstName'] . " " . $sess_data['middleName'] . " " . $sess_data['lastName'];
                        $data['heading'] = $user_note;
                        $from = $this->defaultValues->getHrmEmail();
                        $hrmName = $this->defaultValues->getValue(19);
                        $subject = $hrmName . ': KSHOP Content Updated ';
                        $html = $this->load->view('talentsKnowledge/mail_template', $data, TRUE);
                        $this->email->from($from, $hrmName);
                        $roles = $this->roles->getRoleIdByStramId($this->defaultValues->getValue(28));
                        $role_id = array();

                        foreach ($roles as $role) {
                            array_push($role_id, $role['id']);
                        }
                        //var_dump($role_id);
//                        $role_mails = $this->roles->getEmailIdByRole($role_id);
//                        array_push($role_mails, $talent_email);
//                        $this->email->to($role_mails);
//                        $this->email->subject($subject);
//                        $this->email->message($html);
//                        $this->email->send();


                        //die();
                        $this->session->set_flashdata('flashSuccess', 'Talent Knowledge Updated successfully');
                        redirect('TalentKnowledgeProcess/index', 'refresh');
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('flashError', 'Some error found');
                    }
                }
            }
            $this->load->view('talentKnowledgeProcess/editKnowledge', $editData);
        } else {
            redirect('home', 'refresh');
        }
    }

    public function verifyKnowledge() {
        $sess_data = $this->session->userdata('logged_in');
        if ($sess_data['ip'] == $sess_data['localIp']) {
            //using url for get id
            $this->load->helper('url');
            $knowledge = $this->uri->segment(3);

            if (null !== $this->uri->segment(4)) {
                $redirect = $this->uri->segment(4) . '/' . $this->uri->segment(5);
                ;
            }
            $note_update = " Approved by " . $sess_data['firstName'] . " " . $sess_data['middleName'] . " " .
                    $sess_data['lastName'] . " on " . date('Y-m-d H:i:s');
            $this->db->trans_begin();
            $updateData = array(
                'verified' => '1',
                'verified_by' => $sess_data['talentId'],
            );
            $this->db->set('note', "CONCAT( note, '," . $note_update . "' )", false);
            $this->db->where('id', $knowledge);

            $queryResult = $this->db->update('knowledge_repo', $updateData);
            if ($queryResult) {
                $this->db->trans_commit();
                $getKnowledge = $this->KnowledgeRepository->getKnowledgeById($knowledge);

                foreach ($getKnowledge as $data) {
                    $created_by = $data['created_by'];
                }
                $creator_email = $this->talent->getTalentEmail($created_by);

                $config = Array(
                    'mailtype' => 'html',
                );

                $this->load->library('email', $config);
                $data['name'] = $sess_data['firstName'] . " " . $sess_data['middleName'] . " " . $sess_data['lastName'];
                $data['heading'] = $note_update;
                $data['subject'] = $subject;
                $from = $this->defaultValues->getHrmEmail();
                $hrmName = $this->defaultValues->getValue(19);
                $subject = $hrmName . ': KSHOP Content Verified ';
                $html = $this->load->view('talentsKnowledge/mail_template', $data, TRUE);
                $this->email->from($from, $hrmName);
                $roles = $this->roles->getRoleIdByStramId($this->defaultValues->getValue(28));
                $role_id = array();

                foreach ($roles as $role) {
                    array_push($role_id, $role['id']);
                }

//                $role_mails = $this->roles->getEmailIdByRole($role_id);
//
//                array_push($role_mails, $creator_email);
//                $this->email->to($role_mails);
//                $this->email->subject($subject);
//                $this->email->message($html);
//                $this->email->send();

                $this->session->set_flashdata('flashSuccess', 'Talent Knowledge Verified successfully');
                if (isset($redirect)) {

                    redirect($redirect, 'refresh');
                }
                redirect('TalentKnowledgeProcess/index', 'refresh');
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flashError', 'Some error found');
            }
        } else {
            redirect('home', 'refresh');
        }
    }

}
