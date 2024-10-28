<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of TalentsKnowledgeRepo
 *
 */
require 'MailHandler.php';

class TalentKnowledge extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('KnowledgeRepository', '', TRUE);
        $this->load->model('roles', '', TRUE);
        $this->load->model('Tag', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {

        $sess_data = $this->session->userdata('logged_in');
        if ($sess_data['ip'] == $sess_data['localIp']) {
            $talent = $sess_data['talentId'];
            $data['knowledge'] = $this->KnowledgeRepository->getKnowledgeByTalent($talent);


            $this->load->view('talentsKnowledge/knowledges', $data);
        } else {
            redirect('home', 'refresh');
        }
    }

    public function addKnowledge() {

        if ($this->session->userdata('logged_in')) {

            $this->load->helper(array('form'));
            $this->load->library('form_validation');
            $sess_data = $this->session->userdata('logged_in');
            if ($sess_data['ip'] == $sess_data['localIp']) {
                $talent = $sess_data['talentId'];

                $roleStreams = $this->roles->getRoleStreams();
                $roleStreamArray = array();

                foreach ($roleStreams as $roleStream) {
                    $roleStreamArray[$roleStream['id']] = $roleStream['role_stream'];
                }

                $data['role_stream'] = $roleStreamArray;

                $tags = $this->Tag->viewAllTag();
                $tagArray = array();

                foreach ($tags as $tag) {
                    $tagArray[$tag['id']] = $tag['tag_name'];
                }

                $data['tag'] = $tagArray;

                if ($this->input->post()) {


                    $this->form_validation->set_rules('heading', 'Heading', 'trim|required|xss_clean');
                    $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
                    $this->form_validation->set_rules('role_stream[]', 'Role', 'trim|required|xss_clean');
                    $this->form_validation->set_rules('tag_array[]', 'Tag', 'trim|required|xss_clean');
                    //  $this->form_validation->set_rules('file_name', 'Document', 'required');

                    if ($this->form_validation->run() == TRUE) {


                        $config['upload_path'] = './uploads/';
                        $config['allowed_types'] = 'txt|xls|xlsx|doc|docx|pdf|ppt|pptx';
                        $this->load->library('upload', $config);
                        $upload_data = $this->upload->data();
                        //            var_dump($upload_data);

                        $upload_error = false;

                        if ($_FILES['file_name']['error'] != 4) {

                            if (!$this->upload->do_upload('file_name')) {

                                $upload_error = true;
                                $this->session->set_flashdata('flashError', $this->upload->display_errors());
                            } else {
                                $upload_data = $this->upload->data();
                                $fileName = $upload_data['file_name'];
                            }
                        }
                        if (false === $upload_error) {
                            $heading = $this->input->post('heading');
                            $description = $this->input->post('description');

                            $user_note = $sess_data['firstName'] . " " . $sess_data['middleName'] . " " .
                                    $sess_data['lastName'] . " created the Knowledge Repo on " . date('Y-m-d H:i:s');
                            if ($sess_data['is_management'] == 1) {
                                $verified = 1;
                                $verifiedBy = $sess_data['talentId'];
                            } else {
                                $verified = 0;
                                $verifiedBy = 0;
                            }
                            $talentsKnowledge = array(
                                'heading' => $heading,
                                'description' => $description,
                                'file_name' => $fileName,
                                'created_by' => $talent,
                                'created_on' => date('Y-m-d H:i'),
                                'note' => $user_note,
                                'verified' => $verified,
                                'verified_by' => $verifiedBy,
                            );


                            $role_stream = $this->input->post('role_stream[]');
                            $tags = $this->input->post('tag_array[]');
                            $this->db->trans_begin();
                            $queryResult = $this->db->insert('knowledge_repo', $talentsKnowledge);

                            if ($queryResult) {
                                $insert_id = $this->db->insert_id();

                                foreach ($role_stream as $role_stream) {
                                    $insertRoleKnowledge = array(
                                        'knowledge_repo' => $insert_id,
                                        'role_stream' => $role_stream,
                                    );
                                    $queryResult = $this->db->insert('knowledge_repo_stream', $insertRoleKnowledge);
                                }
                                $this->db->trans_commit();

                                foreach ($tags as $tag) {
                                    $insertTag = array(
                                        'knowledge_repo' => $insert_id,
                                        'tag' => $tag,
                                    );
                                    $queryResult = $this->db->insert('knowledge_repo_tags', $insertTag);
                                }

                                $this->db->trans_commit();


                                $data['name'] = $sess_data['firstName'] . " " . $sess_data['middleName'] . " " . $sess_data['lastName'];
                                $data['heading'] = $heading;
                                $from = $this->defaultValues->getHrmEmail();
                                $hrmName = $this->defaultValues->getValue(19);
                                $subject = $hrmName . ': New KSHOP created';
                                $data['subject'] = $subject;
                                $html = $this->load->view('talentsKnowledge/mail_template', $data, TRUE);

                                $roles = $this->roles->getRoleIdByStramId($this->defaultValues->getValue(28));
                                $role_id = array();

                                foreach ($roles as $role) {
                                    array_push($role_id, $role['id']);
                                }
                                //var_dump($role_id);
                                $to_mails = $this->roles->getEmailIdByRole($role_id);
                                // var_dump($to_mails);
                                $handler = new MailHandler();
                                $handler->sendEmail('html', $from, $to_mails, $subject, '', '', $html);
                                //die();
                                //notification creation
                                //ger reporting manager
                                $talentDetails = $this->talent->getTalentById($sess_data['talentId']);
                                foreach ($talentDetails as $talet) {
                                    $reportingManager = $talet['reporting_manager'];
                                }
                                $talentDetails = $this->talent->getTalentById($reportingManager);
                                foreach ($talentDetails as $talet) {
                                    $reportingManagerUserId = $talet['user'];
                                }
                                if ($sess_data['is_management'] === TRUE) {
                                    $reportingManagerUserId = $sess_data['id'];
                                }
                                //creating notification for reporting manager
                                $insertNotificationData = array(
                                    'user' => $reportingManagerUserId,
                                    'type' => 'Yellow',
                                    'message' => $sess_data['firstName'] . ' created a knowledge repository',
                                    'message_time' => date('Y-m-d H:i:s'),
                                );
                                //inserting
                                $queryResult = $this->db->insert('notifications', $insertNotificationData);

                                $this->session->set_flashdata('flashSuccess', 'Talent Knowledge saved successfully');
                                redirect('TalentKnowledge/index', 'refresh');
                            } else {
                                $this->db->trans_rollback();
                                $this->session->set_flashdata('flashError', 'Some error found');
                            }
                        }
                    }
                }

                $this->load->view('talentsKnowledge/addKnowledge', $data);
            } else {
                redirect('home', 'refresh');
            }
        } else {

            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function editKnowledge() {


        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'txt|xls|xlsx|doc|docx|pdf|ppt|pptx';
        $this->load->library('upload', $config);

        $this->load->library('form_validation');

        $sess_data = $this->session->userdata('logged_in');
        if ($sess_data['ip'] == $sess_data['localIp']) {
            $talent = $sess_data['talentId'];
            $this->load->helper(array('form'));
            //using url for get id
            $this->load->helper('url');
            $talentId = $this->uri->segment(3);

            $editData['talentId'] = $talentId;
            $roleStreams = $this->roles->getRoleStreams();
            $roleStreamArray = array();
            foreach ($roleStreams as $roleStream) {
                $roleStreamArray[$roleStream['id']] = $roleStream['role_stream'];
            }
            $tags = $this->Tag->viewAllTag();
            $tagArray = array();

            foreach ($tags as $tag) {
                $tagArray[$tag['id']] = $tag['tag_name'];
            }


            $editData['tag'] = $tagArray;
            $editData['role_stream'] = $roleStreamArray;

            $editData['knowledge'] = $this->KnowledgeRepository->getKnowledgeById($talentId);
            $editData['knowledgeRoleStream'] = $this->KnowledgeRepository->getSelectedRoleStreamByKnowledgeId($talentId);
            $editData['selectedTagArray'] = $this->Tag->getSelectedTagByKnowledgeId($talentId);

            if ($this->input->post()) {



                $this->form_validation->set_rules('heading', 'Heading', 'trim|required|xss_clean');
                $this->form_validation->set_rules('description', 'Description', 'trim|required|xss_clean');
                $this->form_validation->set_rules('role_stream[]', 'Role', 'trim|required|xss_clean');
                //  $this->form_validation->set_rules('file_name', 'Document', 'required');

                if ($this->form_validation->run() == TRUE) {


                    $config['upload_path'] = './uploads/';
                    $config['allowed_types'] = 'txt|xls|xlsx|doc|docx|pdf|ppt|pptx';
                    $this->load->library('upload', $config);
                    $upload_data = $this->upload->data();

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
                    $user_note = $note . ".  " . $sess_data['firstName'] . " " . $sess_data['middleName'] . " " .
                            $sess_data['lastName'] . " updated the Knowledge Repo on " . date('Y-m-d H:i:s');

                    $talentsKnowledge['heading'] = $heading;
                    $talentsKnowledge['description'] = $description;
                    $talentsKnowledge['modified_on'] = date('d/m/Y H:i A');
                    $talentsKnowledge['note'] = $user_note;
                    $talentsKnowledge['updated_by'] = $talent;

                    $role_stream = $this->input->post('role_stream[]');
                    $tags = $this->input->post('tag_array[]');
                    $this->db->trans_begin();
                    $this->db->where('id', $knowledgeId);
                    $KnowledgeUpdate = $this->db->update('knowledge_repo', $talentsKnowledge);

                    if ($KnowledgeUpdate) {

                        $old_file = base_url() . "upload/" . $this->input->post('old_file');
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
                        foreach ($tags as $tag) {
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
                        $data['heading'] = $heading;
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
//                        // var_dump($role_mails);
//                        $this->email->to($role_mails);
//                        $this->email->subject($subject);
//                        $this->email->message($html);
//                        $this->email->send();
                        //die();
                        $this->session->set_flashdata('flashSuccess', 'Talent Knowledge updated successfully');

                        redirect('TalentKnowledge/index', 'refresh');
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('flashError', 'Some error found');
                    }
                }
            }
            $this->load->view('talentsKnowledge/editKnowledge', $editData);
        } else {
            redirect('home', 'refresh');
        }
    }

}
