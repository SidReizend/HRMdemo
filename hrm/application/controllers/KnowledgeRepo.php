<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class KnowledgeRepo extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('KnowledgeRepository', '', TRUE);
        $this->load->model('Tag', '', TRUE);
        //$this->load->library('pagination');
        $this->load->library('Ajax_pagination');
        $this->perPage = 4;
    }

    public function index() {
        //$this->output->enable_profiler(TRUE);

        $tags = $this->Tag->viewAllTag();
        $tagArray = array();

        foreach ($tags as $tag) {
            $tagArray[$tag['id']] = $tag['tag_name'];
        }

        $data['tag'] = $tagArray;
        $page = $this->input->post('page');
        $search = $this->input->post('search');
        $tag = $this->input->post('tag') != '' ? $this->input->post('tag') : array();
        if (!$page) {
            $offset = 0;
        } else {
            $offset = $page;
        }

        $this->load->helper(array('form'));
        $session_data = $this->session->userdata('logged_in');
        if ($session_data['ip'] == $session_data['localIp']) {
            $roleStream = $session_data['role_stream_id'];
            //for pagination
            $config = array();
            $config['target'] = '#searchedData';
            $config['anchor_class'] = 'btn btn-primary';
            $config["base_url"] = base_url() . "index.php/knowledgeRepo/index/";
            $tagPass = array();
            foreach ($tag as $x) {
                $tagPass[] = intval($x);
            }
            $totalRec = $this->KnowledgeRepository->getKnowledgeCountSearch($roleStream, $search, $tagPass);
            $config["total_rows"] = $totalRec;
            $config["per_page"] = $this->perPage;
            $this->ajax_pagination->initialize($config);
            $data['knowledgeRepo'] = $this->KnowledgeRepository->getKnowledgeRepo($roleStream, $config["per_page"], $offset, $search, $tagPass);

            // is ajax
            if ($this->input->is_ajax_request()) {
                $this->load->view('talentsKnowledge/knowledgeRepoAjax', $data);
            } else {
                $this->load->view('talentsKnowledge/knowledgeRepo', $data);
            }
        } else {
            redirect('home', 'refresh');
        }
    }

    public function readMore() {
        $session_data = $this->session->userdata('logged_in');
        if ($session_data['ip'] == $session_data['localIp']) {
            $roleStream = $session_data['role_stream_id'];

            //using url for get id
            $this->load->helper('url');
            $id = $this->uri->segment(3);
            $data['knowledgeRepo'] = $this->KnowledgeRepository->getKnowledgeDetailsById($id);
            $data['knowledgeRepo'] = $this->KnowledgeRepository->getKnowledgeDetailsById($id);
            $tags = $this->Tag->getSelectedTagByKnowledgeId($id);
            $tagName = "";
            foreach ($tags as $tag) {
                if ($tagName != '') {
                    $tagName .= ', ' . $tag['tag_name'];
                } else {
                    $tagName = $tag['tag_name'];
                }
            }
            
            $data['tagName'] = $tagName;
            $this->load->view('talentsKnowledge/readMore', $data);
        } else {
            redirect('home', 'refresh');
        }
    }

}

?>
