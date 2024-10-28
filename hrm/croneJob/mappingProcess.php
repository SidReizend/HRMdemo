<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller {

    function __construct() {
        parent::__construct();
       
    }

    public function index() {
       //select the project details
            $insertData = array(
                    'menu_name' => 'Project Task Mapping',
                    'link' => 'project/projectTaskMapping',
                    'is_enabled' => 1,
                    'parent' => 45,
                    'priority' => 457,
                );
        $this->db->trans_begin();
                $queryResult = $this->db->insert('master_menu', $insertData);

                if ($queryResult) {
                    $this->db->trans_commit();
                    //echo 1;
                } else {
                    $this->db->trans_rollback();
                    //echo 0;
                }
    }
        }
        
  
    