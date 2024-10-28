<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Project extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('projects', '', TRUE);
        $this->load->model('ProjectMappingModel', '', TRUE);
        $this->load->model('TaskManagementModel', '', TRUE);
        $this->load->model('talentAttendenceSheet', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('TimePredictionModel', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //select the project details
            $projectDetails = $this->projects->getProjects();

            $data['projectDetails'] = $projectDetails;

            $this->load->view('projectManagement/project', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addNewProject() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $is_management = $session_data['is_management'];
            $talentId = $session_data['talentId'];
            $is_pmo = $session_data['is_pmo'];
            $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo);
            $talentArray = array();
            //$talentArray[''] = "Select";
            foreach ($talents as $talent) {
                $talentArray[$talent['id']] = $talent['name'];
            }
            $data['talents'] = $talentArray;

            $statuses = $this->projects->getStatuses();
            $statusArray = array();
            foreach ($statuses as $status) {
                $statusArray[$status['id']] = $status['status_name'];
            }
            $data['statuses'] = $statusArray;

            //get project heads
            $head = $this->projects->getProjectHeads();
            $headArray = array();
            foreach ($head as $heads) {

                $headArray[$heads['id']] = $heads['head_name'];
            }
            $data['heads'] = $headArray;
            //billable option
            $billableArray = array(
                '0' => 'No',
                '1' => 'Yes',
            );
            $data['billables'] = $billableArray;
            //allowance selcting option
            $allowanceArray = array(
                '0' => 'No',
                '1' => 'Yes',
            );
            $data['allowance'] = $allowanceArray;

            $types = $this->projects->getTypes(False);
            $typeArray = array(
                '' => '[SELECT TYPE]'
            );
            foreach ($types as $type) {
                $typeArray[$type['id']] = $type['type_name'];
            }
            $data['types'] = $typeArray;

            $this->load->helper(array('form'));

            $this->load->library('form_validation');

            $data['selectedType'] = $this->input->post('type');
            $data['selectedTypeid'] = '';
            $data['selectedStatus'] = $this->input->post('status');
            $data['selectedAllowance'] = $this->input->post('allowance');
            $data['selectedBillable'] = $this->input->post('billable');
            $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
            if ($this->form_validation->run() == TRUE) {
                //Validation Success.  
                // Do insert operation here
                $projectCode = $this->input->post('projectCode');
                $fromDate = "0000-00-00";
                if ($this->input->post('fromDate') != "") {
                    $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromDate'));
                    $fromDate = $date->format('Y-m-d');
                }
                $toDate = "0000-00-00";
                if ($this->input->post('toDate') != "") {
                    $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
                    $toDate = $date->format('Y-m-d');
                }
                $head = $this->input->post('project_head');
                $type = $this->input->post('type');
                $status = $this->input->post('status');
                $allowance = $this->input->post('allowance');
                $billable = $this->input->post('billable');
                $name = $this->input->post('name');
                $description = $this->input->post('description');
                $estimated_hours = $this->input->post('estimatedHours');

                $stable_talents = $this->input->post('stable_talents');
                $talents = "";
                if (count($stable_talents) > 0) {
                    foreach ($stable_talents as $talent) {
                        if ($talent != "") {
                            $talents .= $talent . ",";
                        }
                    }
                }


                $sub_project = 0;
                if ($this->input->post('sub_project')) {
                    $sub_project = 1;
                    $billable = 0;
                }

                $insertData = array(
                    'project_code' => $projectCode,
                    'description' => $description,
                    'name' => $name,
                    'head' => $head,
                    'type' => $type,
                    'status' => $status,
                    'from_date' => $fromDate,
                    'estimated_hours' => $estimated_hours,
                    'to_date' => $toDate,
                    'stable_talents' => $talents,
                    'sub_project' => $sub_project,
                    'billable' => $billable,
                    'allowance' => $allowance
                );
                $this->db->trans_begin();
                $queryResult = $this->db->insert('project', $insertData);

                if ($queryResult) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'New Project is added successfully');
                    redirect('project', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                }

                // set flash message
            }

            $this->load->view('projectManagement/addNewProject', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function projectTaskMapping() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //select the project details
            $projects = $this->projects->getProjects();
            $projectArray = array();
            $projectArray[''] = 'Select';
            foreach ($projects as $project) {
                $projectArray[$project['id']] = $project['project_code'];
            }
            $data['projects'] = $projectArray;

            $tasks = $this->TaskManagementModel->getAllTask();
            $taskArray = array();
            $taskArray[''] = 'Select';
            //$taskArray[''] = "--Task--";
            foreach ($tasks as $task) {
                $taskArray[$task['id']] = $task['task_code'];
            }
            $data['tasks'] = $taskArray;
            $this->load->helper(array('form'));

            $this->load->view('projectManagement/projectTaskMapping', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function mapNewTask() {

        $projectCode = $this->input->post('projectCode');
        $assigned_tasks = $this->input->post('assigned_tasks');

        //check duplication
        $numb_task = $this->ProjectMappingModel->checkProjectTaskMappingExist($projectCode, $assigned_tasks);
        if ($numb_task > 0) {
            echo 2;
        } else {
            $insertData = array(
                'project_id' => $projectCode,
                'task_id' => $assigned_tasks,
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

    public function taskTable() {
        $start = $this->input->get('start') ? $this->input->get('start') : 0;
        $length = $this->input->get('length') ? $this->input->get('length') : 10;
        $draw = $this->input->get('draw') ? $this->input->get('draw') : 10;
        $search = $this->input->get('search') ? $this->input->get('search')['value'] : '';
        $resultArray = [];
        $taskList = $this->ProjectMappingModel->getProjectTaskMapping($start, $length, $search);
        $serialNumber = 0;
        foreach ($taskList as $task) {
            $resultArray[] = [
                ++$serialNumber,
//                $task['project_id'],
                $task['project_code'],
//                $task['task_id'],
                $task['task_code'],
                anchor('Project/DeleteTaskMapping/taskid/' . $task['id'], 'Delete', array('class' => 'btn btn-danger'))
            ];
        }
        $filteredCount = $this->ProjectMappingModel->getProjectTaskMappingCount($start, $length, $search);
        $output = [
            'data' => $resultArray,
            'draw' => (int) $draw,
            'recordsTotal' => $filteredCount,
            'recordsFiltered' => $filteredCount,
        ];
        echo json_encode($output);
    }

    public function DeleteTaskMapping() {
        $this->load->helper('url');
        $id = $this->uri->segment(4);
        $deletestatus = $this->ProjectMappingModel->deleteTaskMapping($id);
        if ($deletestatus) {
            $this->session->set_flashdata('flashSuccess', 'Mapping removed successfully');
            redirect('project/projectTaskMapping', 'refresh');
        }
    }

    public function editProject() {

        $session_data = $this->session->userdata('logged_in');
        $is_management = $session_data['is_management'];
        $talentId = $session_data['talentId'];
        $is_pmo = $session_data['is_pmo'];
        $talents = $this->ProjectMappingModel->getTalents($talentId, $is_management, $is_pmo, true);
        $talentArray = array();
        //$talentArray[''] = "Select";
        foreach ($talents as $talent) {
            $talentArray[$talent['id']] = $talent['name'];
        }
        $editData['talents'] = $talentArray;

        $statuses = $this->projects->getStatuses();
        $statusArray = array();
        foreach ($statuses as $status) {
            $statusArray[$status['id']] = $status['status_name'];
        }
        $editData['statuses'] = $statusArray;

        //billable option
        $billableArray = array(
            '0' => 'No',
            '1' => 'Yes',
        );
        $editData['billables'] = $billableArray;
        //allowance selcting option
        $allowanceArray = array(
            '0' => 'No',
            '1' => 'Yes',
        );
        $editData['allowance'] = $allowanceArray;

        $types = $this->projects->getTypes(False);
        $typeArray = array(
            '' => '[SELECT TYPE]'
        );
        foreach ($types as $type) {
            $typeArray[$type['id']] = $type['type_name'];
        }
        $editData['types'] = $typeArray;
        $editData['selectedTypeid'] = '';

        //get project heads
        $head = $this->projects->getProjectHeads();

        $headArray = array();
        foreach ($head as $heads) {

            $headArray[$heads['id']] = $heads['head_name'];
        }

        $editData['heads'] = $headArray;
        //using url for get id
        $this->load->helper('url');
        $ptojectId = $this->uri->segment(3);
        $editData['ptojectId'] = $ptojectId;
        //for get edit details
        $editData['projectDetailsById'] = $this->projects->getProjectById($ptojectId);
        $this->load->helper(array('form'));

        $this->load->library('form_validation');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
        $this->form_validation->set_rules('toDate', 'To Date', 'trim|required|xss_clean|callback_check_allocation_exist');

        $editData['selectedBillable'] = $this->input->post('billable');

        if ($this->form_validation->run() == TRUE) {
            //Validation Success.  
            $projectCode = $this->input->post('projectCode');
            $fromDate = "0000-00-00";
            if ($this->input->post('fromDate') != "") {
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('fromDate'));
                $fromDate = $date->format('Y-m-d');
            }
            $toDate = "0000-00-00";
            if ($this->input->post('toDate') != "") {
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
                $toDate = $date->format('Y-m-d');
            }
            $type = $this->input->post('type');
            $head = $this->input->post('project_head');
            $status = $this->input->post('status');
            $allowance = $this->input->post('allowance');
            $billable = $this->input->post('billable');
            $name = $this->input->post('name');
            $description = $this->input->post('description');
            $url = $this->input->post('url');
            $estimatedHours = $this->input->post('estimatedHours');
            $stable_talents = $this->input->post('stable_talents');
            $talents = "";
            if (count($stable_talents) > 0) {
                foreach ($stable_talents as $talent) {
                    if ($talent != "") {
                        $talents .= $talent . ",";
                    }
                }
            }



            $sub_project = 0;
            if ($this->input->post('sub_project')) {
                $sub_project = 1;
                $billable = 0;
            }

            $updateData = array(
                'project_code' => $projectCode,
                'description' => $description,
                'name' => $name,
                'type' => $type,
                'head' => $head,
                'status' => $status,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'url' => $url,
                'stable_talents' => $talents,
                'estimated_hours' => $estimatedHours,
                'billable' => $billable,
                'sub_project' => $sub_project,
                'allowance' => $allowance
            );
            $this->db->trans_begin();
            $this->db->where('id', $ptojectId);
            $queryResultRoleUpdate = $this->db->update('project', $updateData);

            if ($queryResultRoleUpdate) {
                $this->db->trans_commit();
                //die();
                $this->session->set_flashdata('flashSuccess', 'Project Updated successfully');
                redirect('project', 'refresh');
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flashError', 'Some error found');
            }
            // set flash message
        }

        $this->load->view('projectManagement/editProject', $editData);
    }

//    public function deleteRole() {
//        //using url for get id
//        $this->load->helper('url');
//        $roleId = $this->uri->segment(4);
//        $editData['roleId'] = $roleId;
//        $this->roles->deleteRoleById($roleId);
//        $this->session->set_flashdata('flashSuccess', 'Role deleted successfully');
//        redirect('role', 'refresh');
//    }

    function numeric_wcomma($str) {
        $this->form_validation->set_message('numeric_wcomma', 'Should be a number');
        return preg_match('/^[0-9,]+$/', $str) ? TRUE : FALSE;
    }

    function check_allocation_exist($project) {
        //Field validation succeeded.  Validate against database
        $date = DateTime::createFromFormat('d/m/Y', $this->input->post('toDate'));
        $toDate = $date->format('Y-m-d');

        //using url for get id
        $this->load->helper('url');
        $ptojectId = $this->uri->segment(3);
        //check is any timehseet is filled up 
        $timesheetIsCreatedUsersAfterTheDate = $this->ProjectMappingModel->timesheetIsCreatedUsersAfterTheDate($ptojectId, $toDate);
        $timesheetCreatedUsers = '';
        foreach ($timesheetIsCreatedUsersAfterTheDate as $user) {
            $timesheetCreatedUsers = $timesheetCreatedUsers == '' ? $user['name'] : $timesheetCreatedUsers . ',' . $user['name'];
        }
        if ($timesheetCreatedUsers) {
            $this->form_validation->set_message('check_allocation_exist', 'Timesheet is created the talents ' . $timesheetCreatedUsers . ' after the date ' . $this->input->post('toDate') . ', please refer the timesheets');
            return false;
        } else {
            //find the users for which project allocated after the date
            $projectMappedUsersAfterTheDate = $this->ProjectMappingModel->projectMappedUserAfterTheDate($ptojectId, $toDate);
            $projectMappedUsers = '';
            foreach ($projectMappedUsersAfterTheDate as $user) {
                $projectMappedUsers = $projectMappedUsers == '' ? $user['name'] : $projectMappedUsers . ',' . $user['name'];
            }
            if ($projectMappedUsers) {
                $this->form_validation->set_message('check_allocation_exist', 'Project is mapped for the talents ' . $projectMappedUsers . ' after the date ' . $this->input->post('toDate') . ', please refer the mapping');
                return false;
            } else {
                return TRUE;
            }
        }
    }

    /**
     * List Project Types
     */
    public function listTypes() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //select the project type
            $projectTypes = $this->projects->getProjectTypes();
            $data['projectTypes'] = $projectTypes;
            $this->load->view('projectManagement/projectType', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    /**
     * Add Project Type
     */
    public function addProjectType() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            $billTypes = $this->projects->getProjectBillTypes();
            $billTypesDropdown = array("" => " --Select-- ");
            foreach ($billTypes as $type) {
                $id = $type['id'];
                $billTypesDropdown[$id] = $type['type_name'];
            }
            $data['selectedbillTypes'] = '';
            $data['billTypes'] = $billTypesDropdown;

            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->library('form_validation');
            //for form validayion, setting rules
            $config = array(
                array(
                    'field' => 'project_type',
                    'label' => 'Name',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'bill_type',
                    'label' => 'Type',
                    'rules' => 'trim|required|xss_clean'
                )
            );
            $this->form_validation->set_rules('project_type', 'Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('bill_type', 'Type', 'trim|required|xss_clean');
            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() == TRUE) {
                $error_flag = 0;
                //creating notification for reporting manager
                $projectType = $this->input->post('project_type');
                $type_id = $this->input->post('bill_type');
                $insertData = array(
                    'type_name' => $projectType,
                    'type_id' => $type_id
                );
                //inserting
                $queryResult = $this->db->insert('master_project_type', $insertData);
                if (!$queryResult) {
                    $error_flag = 1;
                }
                if ($error_flag == 0) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'Project Type Created successfully');
                    redirect('project/listTypes', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                    $this->load->view('projectManagement/projectTypeCreate', $data);
                }
            }
            $this->load->view('projectManagement/projectTypeCreate', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function editProjectType() {
        $session_data = $this->session->userdata('logged_in');
        $this->load->helper(array('form'));
        //using url for get id
        $this->load->helper('url');
        $id = $this->uri->segment(3);
        $editData['id'] = $id;
        $billTypes = $this->projects->getProjectBillTypes();
        $billTypesDropdown = array("" => " --Select-- ");
        foreach ($billTypes as $type) {
            $gid = $type['id'];
            $billTypesDropdown[$gid] = $type['type_name'];
        }
        $editData['typeDetails'] = $this->projects->getProjectTypeData($id);
        $editData['billTypes'] = $billTypesDropdown;
        $editData['selectedbillTypes'] = $editData['typeDetails'][0]['type_id'];

        if ($this->input->post()) {
            $this->db->trans_begin();
            $this->load->library('form_validation');
            $config = array(
                array(
                    'field' => 'project_type',
                    'label' => 'Name',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'bill_type',
                    'label' => 'Type',
                    'rules' => 'trim|required|xss_clean'
                )
            );
            $this->form_validation->set_rules('project_type', 'Name', 'trim|required|xss_clean');
            $this->form_validation->set_rules('bill_type', 'Type', 'trim|required|xss_clean');
            $this->form_validation->set_rules($config);

            if ($this->form_validation->run() == TRUE) {
                $projectType = $this->input->post('project_type');
                $type_id = $this->input->post('bill_type');

                $updateSpecificData = array(
                    'type_name' => $projectType,
                    'type_id' => $type_id
                );
                $this->db->where('id', $id);
                $queryResult = $this->db->update('master_project_type', $updateSpecificData);
                if ($queryResult) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'Project Type Updated successfully');
                    redirect('project/listTypes', 'refresh');
                } else {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('flashError', 'Some error found');
                    $this->load->view('projectManagement/projectTypeEdit', $editData);
                }
            }
        }
        $this->load->view('projectManagement/projectTypeEdit', $editData);
    }

    public function projectTypewiseReport() {
        $this->load->helper(array('form'));
        $revenueType = $this->input->post('revenue_type');
        $projectType = $this->input->post('project_type');
        $project = $this->input->post('project');
        $fromDateNotStandered = $this->input->post('fromDate') ? $this->input->post('fromDate') : date('d/m/Y');
        $toDateNotStandered = $this->input->post('toDate') ? $this->input->post('toDate') : date('d/m/Y');
        $date = DateTime::createFromFormat('d/m/Y', $fromDateNotStandered);
        $fromDate = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $toDateNotStandered);
        $toDate = $date->format('Y-m-d');

        $projectTypes = $this->projects->getTypes();
        $projectTypesDropdown = array("" => " --Select Project Type--");
        $projectTypesArr = [];
        foreach ($projectTypes as $type) {
            $id = $type['id'];
            $projectTypesDropdown[$id] = $type['type_name'];
            $projectTypesArr[$id] = [];
        }

        $projectRevenueTypes = $this->projects->getProjectBillTypes();
        $projectRevenueTypesDropdown = array("" => " --Select Revenue Type--");
        $projectRevenueTypesArr = [];
        foreach ($projectRevenueTypes as $type) {
            $id = $type['id'];
            $projectRevenueTypesDropdown[$id] = $type['type_name'];
            $projectRevenueTypesArr[$id] = [];
        }


        $projects = $this->projects->getProjectsList();
        $projectDropdown = array("" => " --Select Project--");
        foreach ($projects as $type) {
            $id = $type['id'];
            $projectDropdown[$id] = $type['project_code'];
            array_push($projectTypesArr[$type['type']], $id);
        }
        $data['fromDate'] = $fromDateNotStandered;
        $data['toDate'] = $toDateNotStandered;
        $data['projectTypesDropdown'] = $projectTypesDropdown;
        $data['projectRevenueTypesDropdown'] = $projectRevenueTypesDropdown;
        $data['selectedprojectType'] = $projectType;
        $data['selectedrevenueType'] = $revenueType;
        $data['projectDropdown'] = $projectDropdown;
        $data['selectedproject'] = $project;
        $data['projectTypesArr'] = json_encode($projectTypesArr);

        $project_id = '';
        $type_id = '';
        $revenue_id = '';
        if ($project) {
            $project_id = $project;
            $type_id = $this->projects->getProjectById($project_id)->type;
        } elseif ($projectType) {
            $type_id = $projectType;
        }
        if($revenueType){
            $revenue_id = $revenueType;
        }

        $results = [];
        $revenueTypes = $this->projects->getProjectBillTypes($revenue_id);
        $i = 0;
        foreach ($revenueTypes as $revenuetype) {
            $results[$i] = array(
                'id' => $revenuetype['id'],
                'name' => $revenuetype['type_name'],
                'types' => array(),
                'time' => $this->projects->secondsToMinitues($this->projects->getRevTypeTotalTimeTask($revenuetype['id'], '', $fromDate, $toDate))
            );
            $j = 0;
            $projectTypes = $this->projects->getTypesSorted($type_id, $revenuetype['id']);
            foreach ($projectTypes as $type) {
                $projectTypesDropdown[$id] = $type['type_name'];
                $results[$i]['types'][$j] = array(
                    'id' => $type['id'],
                    'name' => $type['type_name'],
                    'projects' => array(),
                    'time' => $this->projects->secondsToMinitues($this->projects->getTypeTotalTimeTask($type['id'], '', $fromDate, $toDate))
                );
                $projects = $this->projects->getProjectsByType($type['id'], $project_id);
                $projects_add = FALSE;
                foreach ($projects as $project) {
                    $time = $this->projects->getTotalTimeTask($project['id'], '', $fromDate, $toDate);
                    $projectarr = array(
                        'id' => $project['id'],
                        'name' => $project['project_code'],
                        'time' => $this->projects->secondsToMinitues($time)
                    );
                    if($time){
                        $projects_add = TRUE;
                        array_push($results[$i]['types'][$j]['projects'], $projectarr);
                    }
                }
                if (!$projects_add) {
                    $projectarr = array(
                        'id' => '',
                        'name' => '',
                        'time' => ''
                    );
                    array_push($results[$i]['types'][$j]['projects'], $projectarr);
                }
                $j = $j + 1;
            }
            $i = $i + 1;
        }    
        $data['results'] = $results;
        $this->load->view('projectManagement/projectTypeWiseReport', $data);
    }

    public function projectTypewiseExcel() {
        $projectType = $this->input->get('project_type');
        $revenueType = $this->input->get('revenue_type');
        $project = $this->input->get('project');
        $fromDateNotStandered = $this->input->get('fromdate') ? $this->input->get('fromdate') : date('d/m/Y');
        $toDateNotStandered = $this->input->get('todate') ? $this->input->get('todate') : date('d/m/Y');
        $date = DateTime::createFromFormat('d/m/Y', $fromDateNotStandered);
        $fromDate = $date->format('Y-m-d');
        $date = DateTime::createFromFormat('d/m/Y', $toDateNotStandered);
        $toDate = $date->format('Y-m-d');
        $project_id = '';
        $type_id = '';
        $revenue_id = '';
        if ($project) {
            $project_id = $project;
            $type_id = $this->projects->getProjectById($project_id)->type;
        } elseif ($projectType) {
            $type_id = $projectType;
        }
        if($revenueType){
            $revenue_id = $revenueType;
        }

        $results = [];
        $revenueTypes = $this->projects->getProjectBillTypes($revenue_id);
        $i = 0;
        foreach ($revenueTypes as $revenuetype) {
            $results[$i] = array(
                'id' => $revenuetype['id'],
                'name' => $revenuetype['type_name'],
                'types' => array()
            );
            $j = 0;
            $projectTypes = $this->projects->getTypesSorted($type_id, $revenuetype['id']);
            foreach ($projectTypes as $type) {
                $projectTypesDropdown[$id] = $type['type_name'];
                $results[$i]['types'][$j] = array(
                    'id' => $type['id'],
                    'name' => $type['type_name'],
                    'projects' => array(),
                    'time' => $this->projects->secondsToMinitues($this->projects->getTypeTotalTimeTask($type['id'], '', $fromDate, $toDate))
                );
                // $projects = $this->projects->getProjectsByType($type['id'], $project_id);
                // foreach ($projects as $project) {
                //     $projectarr = array(
                //         'id' => $project['id'],
                //         'name' => $project['project_code'],
                //         'time' => $this->projects->secondsToMinitues($this->projects->getTotalTimeTask($project['id'], '', $fromDate, $toDate))
                //     );
                //     array_push($results[$i]['types'][$j]['projects'], $projectarr);
                // }
                // if (!$projects) {
                //     $projectarr = array(
                //         'id' => '',
                //         'name' => '',
                //         'time' => ''
                //     );
                //     array_push($results[$i]['types'][$j]['projects'], $projectarr);
                // }
                $j = $j + 1;
            }
            $i = $i + 1;
        }
        $data['fromdate'] = $fromDateNotStandered;
        $data['todate'] = $toDateNotStandered;
        $data['results'] = $results;
        $this->load->view('projectManagement/projectTypeWiseExcel', $data);
    }

    public function mappingMigrate() {
        //select the project details
        $projectDetails = $this->projects->getMapping();
        foreach ($projectDetails as $project) {
            $project_id = $project['id'];
            $assigned_tasks = explode(",", $project['assigned_tasks']);
            foreach ($assigned_tasks as $task) {
                $numb_task = $this->ProjectMappingModel->checkProjectTaskMappingExist($project_id, $task);
                if ($numb_task > 0) {
                    continue;
                }
                $insertData = array(
                    'project_id' => $project_id,
                    'task_id' => $task,
                );
                $this->db->trans_begin();
                $queryResult = $this->db->insert('project_task_mapping', $insertData);

                if ($queryResult) {
                    $this->db->trans_commit();
                    //echo 1;
                } else {
                    $this->db->trans_rollback();
                    //echo 0;
                }
            }
        }
    }

    /*
     * mothly plan generator page load
     */

    public function monthlyPlanGenerator() {

        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        //get  all projects
        $project = $this->projects->getAllProjects();

        $projectArray = array(
            '' => '[SELECT PROJECT]'
        );
        foreach ($project as $projects) {
            $projectArray[$projects['id']] = $projects['project_code'] . " - " . $projects['name'];
        }
        $data['project'] = $projectArray;
        //get all months
        $monthArray = array(
            '' => '[SELECT MONTH]'
        );
        $months = $this->talentAttendenceSheet->getMonths();
        foreach ($months as $month) {
            $monthArray[$month['id']] = $month['month'];
        }
        $data['months'] = $monthArray;
        //for form validayion, setting rules
        $this->form_validation->set_rules('taskcode', 'Task Code', 'trim|required|xss_clean');
        $this->form_validation->set_rules('taskdesc', 'Task Description', 'trim|required|xss_clean');

        $this->load->view('projectManagement/monthlyPlanGenerator', $data);
    }

    /*
     * save monthly plan
     */

    public function saveMonthlyPlan() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $projectId = $this->input->get('project_name');

        $hours = $this->input->get('hours');
        // check current project already monthly plan added
        $numb_task = $this->projects->checkMonthlyPlanMappingExist($projectId, $year, $month);
        if ($numb_task > 0) {
            echo 2;
        } else {
            $insertData = array(
                'year' => $year,
                'month' => $month,
                'project' => $projectId,
                'hours' => $hours
            );
            //inserting
            $queryResult = $this->db->insert('monthly_plan', $insertData);
            if (!$queryResult) {
                echo 0;
            } else {
                echo 1;
            }
        }
    }

    /*
     * get all the monthly plan against the selected year and month
     */

    public function getMonthlyPlan() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $dateFrom = date('Y-m-d', strtotime(date($year . '-' . $month . '-' . '01')));
        $dateTo = date('Y-m-t', strtotime($dateFrom));
        $monthlyplan = $this->projects->getMonthlyPlan($year, $month);
        //get not grouped project first
        usort($monthlyplan, function ($a, $b) {
            if ($a['group_name'] == '' && $b['group_name'] != '') {
                return -1; // empty places first
            } elseif ($a['group_name'] != '' && $b['group_name'] == '') {
                return 1; // non-empty places after empty places
            } else {
                return 0; // no change if both places are empty or non-empty
            }
        });
        $this->load->helper('form');
        //get status
        $statuses = $this->projects->getStatuses();
        $statusArray = array();
        foreach ($statuses as $status) {
            $statusArray[$status['id']] = $status['status_name'];
        }
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;
        $data['monthlyplan'] = $monthlyplan;
        $data['year'] = $year;
        $data['month'] = $month;
        $data['statusArray'] = $statusArray;
        $this->load->view('projectManagement/monthlyPlanAjax', $data);
    }

    /*
     * delete monthly plan 
     */

    public function deleteMonthlyPlan() {
        $id = $this->input->get('id');
        $monthlyplan = array(
            'is_deleted' => 1
        );
        $this->db->where('id', $id);
        //updating is deleted field
        $queryResult = $this->db->update('monthly_plan', $monthlyplan);
        if ($queryResult) {
            $this->db->trans_commit();
            echo 1;
        } else {
            $this->db->trans_rollback();
            echo 2;
        }
    }

    /*
     * show the popup model on clicking on add task
     */

    public function showTashAddModel() {
        $id = $this->input->get('id');
        $projectId = $this->input->get('projectId');
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        $projectById = $this->projects->getMonthlyPlan($year, $month, $id);
        $years = '';
        $month = '';
        foreach ($projectById as $ids) {
            $year = $ids['year'];
            $month = $ids['month'];
        }
        // get all talents
        $talents = $this->talent->getTalents();
        $talentsArray = array(
            '' => '[SELECT TALENT]'
        );
        foreach ($talents as $talent) {
            $talentsArray[$talent['id']] = $talent['name'];
        }
        $data['talent'] = $talentsArray;
        //get all task mapped the  selected project
        $alltasks = $this->projects->getTaskOfProject($projectId);
        $taskArray = array(
            '' => '[SELECT Task]'
        );
        foreach ($alltasks as $task) {
            $taskArray[$task['id']] = $task['task'];
        }
        $data['project'] = $id;
        $data['projectId'] = $projectId;
        $data['task'] = $taskArray;
        $data['projectById'] = $projectById;
        $this->load->view('projectManagement/monthlyPlanAjax', $data);
    }

    /**
     * add monthly task plan 
     */
    public function addTaskMonthlyPlan() {
        $task = $this->input->get('task');
        $talent = $this->input->get('talent');
        $time = $this->input->get('time');
        $monthlyplan = $this->input->get('monthlyplan');
        $insertData = array(
            'task' => $task,
            'talent' => $talent,
            'time' => $time,
            'monthlyplan' => $monthlyplan
        );
        //checking is already added
        $numb_task = $this->projects->checkMonthlyPlanTaskMappingExist($task, $monthlyplan, $talent);

        if ($numb_task > 0) {
            $this->db->where('monthlyplan', $monthlyplan);
            $this->db->where('task', $task);
            $this->db->where('talent', $talent);
            //task plan already added against the same updated
            $queryResult = $this->db->update('monthly_task_plan', $insertData);
            if ($queryResult) {
                $this->db->trans_commit();
                echo 2;
            } else {
                $this->db->trans_rollback();
                echo 0;
            }
        } else {
            //fid the duplicate of task plan
            $task_mapping = $this->projects->checkMonthlyPlanTaskMappingExist($task, $monthlyplan);
            if ($task_mapping > 0) {
                echo 3;
            } else {
                //inserting
                $queryResult = $this->db->insert('monthly_task_plan', $insertData);
                if (!$queryResult) {
                    echo 0;
                } else {
                    echo 1;
                }
            }
        }
    }

    /*
     * get  all the monthly task plan against the project
     */

    public function getMonthlyTaskPlan() {
        $monthlyplan = $this->input->get('monthlyplan');
        $projectId = $this->input->get('projectId');
        $monthlytaskplan = $this->projects->getMonthlyTaskPlan($monthlyplan);
        $projectByIds = $this->projects->getMonthlyPlan($year, $month, $monthlyplan);
        $hoursArray = array();
        foreach ($projectByIds as $hours) {
            $hoursArray['hours'] = $hours['hours'];
            $hoursArray['project'] = $hours['project'];
            $hoursArray['year'] = $hours['year'];
            $hoursArray['month'] = $hours['month'];
        }

        $this->load->helper('form');
        $statuses = $this->projects->getTaskStatuses();
        $statusArray = array();
        foreach ($statuses as $status) {
            $statusArray[$status['status_id']] = $status['status'];
        }
        $data['statusArray'] = $statusArray;
        $data['hoursArray'] = $hoursArray;
        $data['projectId'] = $projectId;
        $data['monthlyplan'] = $monthlyplan;
        $data['monthlytaskplan'] = $monthlytaskplan;
        $this->load->view('projectManagement/monthlyTaskPlanAjax', $data);
    }

    /*
     * delete monthly task plan
     */

    public function deleteMonthlyTaskPlan() {
        $id = $this->input->get('id');
        $monthlytaskplan = array(
            'is_deleted' => 1
        );
        $this->db->where('id', $id);
        //update the field isdeleted
        $queryResult = $this->db->update('monthly_task_plan', $monthlytaskplan);
        if ($queryResult) {
            $this->db->trans_commit();
            echo 1;
        } else {
            $this->db->trans_rollback();
            echo 2;
        }
    }

    /*
     * show unassaigned task corresponding thime and talents
     */

    public function showUnassignedTask() {
        $id = $this->input->get('id');
        $projectId = $this->input->get('projectId');
        $projectById = $this->projects->getMonthlyPlan($year, $month, $id);
        $years = '';
        $month = '';
        foreach ($projectById as $ids) {
            $year = $ids['year'];
            $month = $ids['month'];
        }
        $dateFrom = date('Y-m-01', strtotime(date($year . '-' . $month)));
        $dateTo = date('Y-m-t', strtotime($dateFrom));
        //get added task in monthly task plan
        $selectedTask = $this->projects->getAddedTaskInMonthlyPlan($id);
        if ($selectedTask) {
            $selectedtaskArray = array();
            foreach ($selectedTask as $task) {
                $selectedtaskArray[] = $task['task'];
            }
        } else {
            $selectedtaskArray = '';
        }
        if ($selectedtaskArray) {
            $selectedtasktime = $this->TimePredictionModel->projectTaskAmountCalProjects($projectId, $selectedtaskArray, $talent = '', $dateFrom, $dateTo);
        }
        //get the not added task in the same project in monthly task plan
        $notaddedtask = $this->projects->getNotAddedTask($dateFrom, $dateTo, $projectId, $selectedtaskArray);
        $taskcode = array();
        foreach ($notaddedtask as $notadded) {
            $taskcode[] = $notadded['task'];
        }
        //avoid the duplicatie
        $uniqueTaskCode = array_unique($taskcode);
        $this->load->helper('form');
        //get the  all status of the task
        $statuses = $this->projects->getTaskStatuses();
        $statusArray = array();
        foreach ($statuses as $status) {
            $statusArray[$status['status_id']] = $status['status'];
        }
        $data['statusArray'] = $statusArray;
        $data['project'] = $id;
        $data['projectId'] = $projectId;
        $data['taskcode'] = $uniqueTaskCode;
        $data['selectedtasktime'] = $selectedtasktime;
        $data['year'] = $year;
        $data['month'] = $month;
        $this->load->view('projectManagement/monthlyTaskPlanAjax', $data);
    }

    /*
     *  task status updating
     */

    public function taskStatusUpdated() {
        $status = $this->input->get('status');
        $taskid = $this->input->get('taskid');
        $task = array(
            'status' => $status
        );
        $this->db->where('id', $taskid);
        $queryResult = $this->db->update('task_code', $task);
        if ($queryResult) {
            $this->db->trans_commit();
            echo 1;
        } else {
            $this->db->trans_rollback();
            echo 2;
        }
    }

    /*
     *  show unassigned project for selected year and month
     */

    public function showUnAssignedProjects() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $dateFrom = date('Y-m-d', strtotime(date($year . '-' . $month . '-' . '01')));
        $dateTo = date('Y-m-t', strtotime($dateFrom));
        $monthlyplan = $this->projects->getMonthlyPlan($year, $month);
        if ($monthlyplan) {
            $selectedProjectArray = array();
            foreach ($monthlyplan as $plan) {
                $selectedProjectArray[] = $plan['projectid'];
            }
        } else {
            $selectedProjectArray = '';
        }
        $unSelectedProject = $this->projects->getNotAddedProject($dateFrom, $dateTo, $selectedProjectArray);
        if (empty($unSelectedProject)) {
            echo 1;
        } else {

            $data['dateFrom'] = $dateFrom;
            $data['dateTo'] = $dateTo;
            $data['year'] = $year;
            $data['month'] = $month;
            $data['unSelectedProject'] = $unSelectedProject;
            $this->load->view('projectManagement/monthlyPlanAjax', $data);
        }
    }

    /*
     * showuUnassigned Projects  name only year and month change in monthlyplan
     */

    public function projectDropdownUnassignedProject() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $monthlyplan = $this->projects->getMonthlyPlan($year, $month);
        if ($monthlyplan) {
            $selectedProjectArray = array();
            foreach ($monthlyplan as $plan) {
                $selectedProjectArray[] = $plan['projectid'];
            }
        } else {
            $selectedProjectArray = '';
        }
        //get  all  unselected projects
        $project = $this->projects->getAllProjects($selectedProjectArray);
        $unSelectedArray[''] = "--Project--";
        foreach ($project as $key => $unselectedProject) {
            $unSelectedArray[$unselectedProject['id']] = $unselectedProject['project_code'] . "-" . $unselectedProject['name'];
        }
        $data['project'] = $unSelectedArray;
        $data['ChangeProject'] = 1;
        $this->load->helper(array('form'));
        $this->load->view('projectManagement/monthlyPlanAjax', $data);
    }

    /*
     * show chart in popup  on cick  show chart button
     */

    public function showChart() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $dateFrom = date('Y-m-d', strtotime(date($year . '-' . $month . '-' . '01')));
        $dateTo = date('Y-m-t', strtotime($dateFrom));
        $monthlyplan = $this->projects->getMonthlyPlan($year, $month);
        $ProjectArray = array();
        $EstimateTimeArray = array();
        $ActualTimeArray = array();
        //get group time group name  group hours and not grouped project name and hours
        $grouped = array();
        foreach ($monthlyplan as $plan) {
            $grouped[$plan['group_name']][] = $plan;
        }
        foreach ($grouped as $place => $rows) {
            $totalEstimateProjectHour = "00:00:00";
            //get group name and group hours if group name is not empty 
            if ($rows[0]['group_name'] != '') {
                $ProjectArray[] = $rows[0]['group_name'];
                $EstimateTimeArray[] = $rows[0]['group_hours'];
                $actualtime = [];
                foreach ($rows as $row) {
                    $monthlyplans = $this->projects->getMonthlyhoursSum($dateFrom, $dateTo, $row['projectid']);
                    $totalEstimateProjectHour = $this->talentAttendenceSheet->secondsToMinitues($monthlyplans[0]['time']);
                    $actualtime[] = $totalEstimateProjectHour;
                }
                $ActualTimeArray[] = $this->TimePredictionModel->sum_time($actualtime);
            } else {
                //get project code and project time if group is empty
                foreach ($rows as $row) {
                    $ProjectArray[] = $row['project_code'];
                    $EstimateTimeArray[] = $row['hours'];
                    $monthlyplans = $this->projects->getMonthlyhoursSum($dateFrom, $dateTo, $row['projectid']);
                    $totalEstimateProjectHour = $this->talentAttendenceSheet->secondsToMinitues($monthlyplans[0]['time']);
                    $ActualTimeArray[] = $totalEstimateProjectHour;
                }
            }
        }
        $data['project'] = $ProjectArray;
        $data['hours'] = $EstimateTimeArray;
        $data['actualhours'] = $ActualTimeArray;
        $this->load->view('projectManagement/showChart', $data);
    }

    /*
     * mothly plan generator group adding popup load
     */

    public function monthlyPlanGroupGenerator() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $this->load->helper(array('form'));
        $this->load->library('form_validation');
        //get  all projects
        $project = $this->projects->getAllProjects();

        $projectArray = array(
            
            '' => '[SELECT PROJECT]'
        );
        foreach ($project as $projects) {
            $projectArray[$projects['id']] = $projects['project_code'] . " - " . $projects['name'];
        }
        $data['project'] = $projectArray;
        $data['year'] = $year;
        $data['month'] = $month;
        $data['groupadding'] = 1;
        $this->load->view('projectManagement/monthlyPlanAjax', $data);
    }

    /*
     * save  group monthly plan
     */

    public function saveGroupMonthlyPlan() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $projectgroup = $this->input->get('groupproject');
        $project = json_decode($projectgroup, true);
        $hours = $this->input->get('time');
        if (is_array($project) && count($project) > 0) {
            foreach ($project as $projectId) {
                
                $numb_plan = $this->projects->checkMonthlyPlanMappingExist($projectId['project'], $year, $month);

                if ($numb_plan > 0) {
                    echo 2;
                } else {
                    $insertData = array(
                        'year' => $year,
                        'month' => $month,
                        'project' => $projectId['project'],
                        'group_name' => $projectId['group'],
                        'group_hours' => $hours,
                        'hours' => $projectId['task_time'],
                    );
                    $queryResult = $this->db->insert('monthly_plan', $insertData);
                    if (!$queryResult) {
                        $error = true;
                    }
                }
                $insertData = array();
            }
            if ($error) {
                echo 0;
            } else {
                echo 1;
            }
        }
    }

    /*
     * showuUnassigned Projects  name only year and month change in monthlyplan group adding popup page
     */

    public function projectGroupDropdownUnassignedProject() {
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $dateFrom = date('Y-m-d', strtotime(date($year . '-' . $month . '-' . '01')));
        $dateTo = date('Y-m-t', strtotime($dateFrom));
        $monthlyplan = $this->projects->getMonthlyPlan($year, $month);
        if ($monthlyplan) {
            $selectedProjectArray = array();
            foreach ($monthlyplan as $plan) {
                $selectedProjectArray[] = $plan['projectid'];
            }
        } else {
            $selectedProjectArray = '';
        }
        //get  all  unselected projects
        $project = $this->projects->getAllProjects($selectedProjectArray);
        $unSelectedArray = array(
            '' => '[SELECT PROJECT]'
        );
        foreach ($project as $key => $unselectedProject) {
            $unSelectedArray[$unselectedProject['id']] = $unselectedProject['project_code'] . "-" . $unselectedProject['name'];
        }
        $data['dateFrom'] = $dateFrom;
        $data['dateTo'] = $dateTo;
        $data['project'] = $unSelectedArray;
        $data['ChangegroupProject'] = 1;
        $this->load->helper(array('form'));
        $this->load->view('projectManagement/monthlyPlanAjax', $data);
    }

    public function projectTypeMapping() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //select the project details
            $projects = $this->projects->getProjects();
            $projectArray = array();
            $projectArray[''] = 'Select';
            foreach ($projects as $project) {
                $projectArray[$project['id']] = $project['project_code'];
            }
            $data['projects'] = $projectArray;

            $types = $this->projects->getTypes(False);
            $typeArray = array();
            foreach ($types as $type) {
                $typeArray[$type['id']] = $type['type_name'];
            }
            $data['types'] = $typeArray;

            $this->load->helper(array('form'));

            $this->load->view('projectManagement/projectTypeMapping', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function typeTable() {
        $start = $this->input->get('start') ? $this->input->get('start') : 0;
        $length = $this->input->get('length') ? $this->input->get('length') : 10;
        $draw = $this->input->get('draw') ? $this->input->get('draw') : 10;
        $search = $this->input->get('search') ? $this->input->get('search')['value'] : '';
        $resultArray = [];
        $typeList = $this->ProjectMappingModel->getProjectTypeMapping($start, $length, $search);
        $serialNumber = 0;
        foreach ($typeList as $type) {
            $resultArray[] = [
                ++$serialNumber,
                $type['project_code'],
                $type['type_name'],
                $type['status_name'],
                anchor('Project/DeleteTypeMapping/taskid/' . $type['id'], 'Delete', array('class' => 'btn btn-danger'))
            ];
        }
        $filteredCount = $this->ProjectMappingModel->getProjectTypeMappingCount($start, $length, $search);
        $output = [
            'data' => $resultArray,
            'draw' => (int) $draw,
            'recordsTotal' => $filteredCount,
            'recordsFiltered' => $filteredCount,
        ];
        echo json_encode($output);
    }

    public function DeleteTypeMapping() {
        $this->load->helper('url');
        $id = $this->uri->segment(4);
        $deletestatus = $this->ProjectMappingModel->deleteTypeMapping($id);
        if ($deletestatus) {
            $this->session->set_flashdata('flashSuccess', 'Mapping removed successfully');
            redirect('project/projectTypeMapping', 'refresh');
        }
    }

    public function mapNewType() {
        $projectCode = $this->input->post('projectCode');
        $assigned_types = $this->input->post('assigned_types');
        //check duplication
        $numb_task = $this->ProjectMappingModel->checkProjectTypeMappingExist($projectCode, $assigned_types);
        if ($numb_task > 0) {
            echo 2;
        } else {
            $updateData = array(
                'type' => $assigned_types
            );
            $this->db->trans_begin();
            $this->db->where('id', $projectCode);
            $queryResult = $this->db->update('project', $updateData);
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
