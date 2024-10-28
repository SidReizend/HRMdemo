<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('projects', '', TRUE);
        
    }

    public function index() {
      
            //select the project details
            $projectDetails = $this->projects->getMapping();
             foreach ($projectDetails as $project) {
                $project_id=$project['id'];
                $assigned_tasks = explode(",", $project['assigned_tasks']);
                foreach ($assigned_tasks as $task) {
                    $insertData = array(
                    'project_id' => $project_id,
                    'task_id' => $task,
                );
                $this->db->trans_begin();
                $queryResult = $this->db->insert('project_task_mapping', $insertData);

                if ($queryResult) {
                    $this->db->trans_commit();
                    echo 1;
                } else {
                    $this->db->trans_rollback();
                    echo 0;
                }
                }
        }
       
    }


}
