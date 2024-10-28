<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TaskManagementCntrl extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('TaskManagementModel', '', TRUE);
        $this->load->model('projects', '', TRUE);
        $this->load->model('TimePredictionModel', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];

            $data['allTaskCodes'] = $this->TaskManagementModel->getAllTask();
            $this->load->view('TaskManagement/TaskView', $data);
        } else {
            //If no session, redirect to login pageF
            redirect('login', 'refresh');
        }
    }

    /**
     * for server side pagination
     */
    public function getTaskByPagination() {
        $start = $this->input->get('start') ? $this->input->get('start') : 0;
        $length = $this->input->get('length') ? $this->input->get('length') : 10;
        $draw = $this->input->get('draw') ? $this->input->get('draw') : 10;
        $search = $this->input->get('search') ? $this->input->get('search')['value'] : '';
        $resultArray = [];
        $taskList = $this->TaskManagementModel->getTaskByLimit($start, $length, $search);
        $serialNumber = 0;
        foreach ($taskList as $task) {
            $resultArray[] = [
                ++$serialNumber,
                $task['task_code'],
                $task['task_desc'],
                $task['billable'] == 1 ? 'Yes' : 'No',
                $task['is_travel'] == 1 ? 'Yes' : 'No',
                $task['project_code'],
                $task['parent_task_code'],
                anchor('TaskManagementCntrl/EditTask/taskid/' . $task['id'], 'Edit', array('class' => 'btn btn-default'))
            ];
        }
        $filteredCount = $this->TaskManagementModel->getTotalTask($search);
        $output = [
            'data' => $resultArray,
            'draw' => (int) $draw,
            'recordsTotal' => $filteredCount,
            'recordsFiltered' => $filteredCount,
        ];
        echo json_encode($output);
    }

    public function AddTask() {
        if ($this->session->userdata('logged_in')) {
            $this->db->trans_begin();

            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $data['username'] = $session_data['username'];
            //helper for form and date
            $this->load->helper(array('form'));

            $this->load->library('form_validation');
            //for form validayion, setting rules
            $config = array(
                array(
                    'field' => 'taskcode',
                    'label' => 'Task code',
                    'rules' => 'trim|required|xss_clean'
                ),
                array(
                    'field' => 'taskdesc',
                    'label' => 'Task Description',
                    'rules' => 'trim|required|xss_clean'
                )
            );
            $this->form_validation->set_rules($config);
            //billable option
            $billableArray = array(
                '0' => 'No',
                '1' => 'Yes',
            );
            $data['billables'] = $billableArray;
            $data['selectedBillable'] = $this->input->post('billable');
            //is travel option
            $isTravelArray = array(
                '0' => 'No',
                '1' => 'Yes',
            );
            $data['isTravel'] = $isTravelArray;
            $data['selectedIsTravel'] = $this->input->post('isTravel');
            $data['selectedProject'] = $this->input->post('parent_project');
            $data['selectedTask'] = $this->input->post('parent_task');
            //status option
            $statuses = $this->projects->getTaskStatuses();
            $statusArray = array();
            foreach ($statuses as $status) {
                $statusArray[$status['status_id']] = $status['status'];
            }
            $data['status'] = $statusArray;
            $invoiceStatus = $this->projects->getInvoiceStatuses();
            $invoiceStatusArray = array();
            foreach ($invoiceStatus as $status) {
                $invoiceStatusArray[$status['status_id']] = $status['status'];
            }
            $data['invoicestatus'] = $invoiceStatusArray;
            $data['selectedStatus'] = $this->input->post('status');
            $data['selectedinvoicestatus'] = $this->input->post('invoice_status');
            //Is recurring billable option
            $isRecurringArray = array(
                '0' => 'No',
                '1' => 'Yes',
            );
            $data['isRecurring'] = $isRecurringArray;
            $data['selectedRecurring'] = $this->input->post('isrecurring');
            //dropdown for previous project
            $projectDropdown = array("" => "[SELECT Previous Project]");
            $projects = $this->projects->getProjects();
            foreach ($projects as $project) {
                $id = $project['id'];
                $projectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
            }
            $data['projectDropdown'] = $projectDropdown;
            $data['taskDropdown'] = array("" => "[SELECT Task]");
            $this->form_validation->set_rules('invoice_status', 'Invoice Status', 'trim|required|xss_clean');
            $this->form_validation->set_rules('advanceinvoicenumber', 'Advance Invoice Number', 'trim|xss_clean');
            $this->form_validation->set_rules('advanceinvoicedate', 'Advance Invoice Date', 'trim|xss_clean');
            $this->form_validation->set_rules('invoicenumber', 'Advance Invoice Number', 'trim|xss_clean');
            $this->form_validation->set_rules('invoicedate', 'Advance Invoice Date', 'trim|xss_clean');
            $invoice_status = $this->input->post('invoice_status');
            // Check the invoice status value and apply additional validation rules
            if ($invoice_status == "1") {
                $this->form_validation->set_rules('advanceinvoicenumber', 'Advance Invoice Number', 'trim|required|xss_clean');
                $this->form_validation->set_rules('advanceinvoicedate', 'Advance Invoice Date', 'trim|required|xss_clean');
            } elseif ($invoice_status == "2") {
                $this->form_validation->set_rules('invoicenumber', 'Advance Invoice Number', 'trim|required|xss_clean');
                $this->form_validation->set_rules('invoicedate', 'Advance Invoice Date', 'trim|required|xss_clean');
            }
            if ($this->form_validation->run() == TRUE) {
                $error_flag = 0;
                //creating notification for reporting manager
                $taskcode = $this->input->post('taskcode');
                $taskdesc = $this->input->post('taskdesc');
                $billable = $this->input->post('billable');
                $is_travel = $this->input->post('is_travel');
                $parent_project = $this->input->post('parent_project');
                $parent_task = $this->input->post('parent_task');
                $status = $this->input->post('status');
                $invoice_number = $this->input->post('invoicenumber');
                $invoice_date = $this->input->post('invoicedate');
                $invoice_status = $this->input->post('invoice_status');
                $advance_invoice_number = $this->input->post('advanceinvoicenumber');
                $advance_invoice_date = $this->input->post('advanceinvoicedate');
                $deploy_date = $this->input->post('deployDate');
                $is_recurring = $this->input->post('isrecurring');
                $is_crossbillable = $this->input->post('iscrossbillable');
                if ($invoice_date) {
                    $date = DateTime::createFromFormat('d/m/Y', $invoice_date);
                    $invoice_date = $date->format('Y-m-d');
                } else {
                    $invoice_date = null;
                }
                if ($advance_invoice_date) {
                    $date = DateTime::createFromFormat('d/m/Y', $advance_invoice_date);
                    $advance_invoice_date = $date->format('Y-m-d');
                } else {
                    $advance_invoice_date = null;
                }
                if ($deploy_date) {
                    $date = DateTime::createFromFormat('d/m/Y', $deploy_date);
                    $deploy_date = $date->format('Y-m-d');
                } else {
                    $deploy_date = null;
                }
                $numb_task = $this->TaskManagementModel->checkTask($taskcode);
                if ($numb_task == 0) {
                    $insertData = array(
                        'task_code' => $taskcode,
                        'billable' => $billable,
                        'is_travel' => $is_travel,
                        'parent_project' => $parent_project,
                        'parent_task' => $parent_task,
                        'task_desc' => $taskdesc,
                        'status' => $status,
                        'invoice_number' => $invoice_number,
                        'invoice_date' => $invoice_date,
                        'is_recurring' => $is_recurring,
                        'invoice_status' => $invoice_status,
                        'advance_invoice_date' => $advance_invoice_date,
                        'advance_invoice_number' => $advance_invoice_number,
                        'is_crossbillable' => $is_crossbillable
                    );
                    //inserting
                    $queryResult = $this->db->insert('task_code', $insertData);
                    if (!$queryResult) {
                        $error_flag = 1;
                    }
                    if ($error_flag == 0) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('flashSuccess', 'Task Created successfully');
                        redirect('TaskManagementCntrl', 'refresh');
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('flashError', 'Some error found');
                        $this->load->view('TaskManagement/TaskCreation', $data);
                    }
                } else {
                    $this->session->set_flashdata('flashError', 'Already Have Same Task Code');
                    $this->load->view('TaskManagement/TaskCreation', $data);
                }
            }
        $this->load->view('TaskManagement/TaskCreation', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function EditTask() {
        $session_data = $this->session->userdata('logged_in');
        $this->load->helper(array('form'));

        //using url for get id
        $this->load->helper('url');
        $taskid = $this->uri->segment(4);
        $editData['taskid'] = $taskid;

        $editData['TaskDetails'] = $this->TaskManagementModel->gettaskData($taskid);
        //billable option
        $billableArray = array(
            '0' => 'No',
            '1' => 'Yes',
        );
        //is trave option
        $isTravelArray = array(
            '0' => 'No',
            '1' => 'Yes',
        );
        //status option
        $statuses = $this->projects->getTaskStatuses();
        $statusArray = array();
        foreach ($statuses as $status) {
            $statusArray[$status['status_id']] = $status['status'];
        }
        //Is recurring billable option
        $isRecurringArray = array(
            '0' => 'No',
            '1' => 'Yes',
        );
        $editData['isTravelArray'] = $isTravelArray;

        $editData['billables'] = $billableArray;

        $editData['statusArray'] = $statusArray;

        $editData['isRecurring'] = $isRecurringArray;
        //dropdown for previous project
        $projectDropdown = array("" => "[SELECT Previous Project]");
        $projects = $this->projects->getProjects();
        foreach ($projects as $project) {
            $id = $project['id'];
            $projectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
        }
        $editData['projectDropdown'] = $projectDropdown;
        $invoiceStatus = $this->projects->getInvoiceStatuses();
        $invoiceStatusArray = array();
        foreach ($invoiceStatus as $status) {
            $invoiceStatusArray[$status['status_id']] = $status['status'];
        }
        $editData['invoicestatus'] = $invoiceStatusArray;
        if ($this->input->post()) {
            $this->db->trans_begin();
            $this->load->library('form_validation');

            //for form validayion, setting rules
            $this->form_validation->set_rules('taskcode', 'Task Code', 'trim|required|xss_clean');
            $this->form_validation->set_rules('taskdesc', 'Task Description', 'trim|required|xss_clean');
            $this->form_validation->set_rules('invoice_status', 'Invoice Status', 'trim|required|xss_clean');
            $this->form_validation->set_rules('advanceinvoicenumber', 'Advance Invoice Number', 'trim|xss_clean');
            $this->form_validation->set_rules('advanceinvoicedate', 'Advance Invoice Date', 'trim|xss_clean');
            $this->form_validation->set_rules('invoicenumber', ' Invoice Number', 'trim|xss_clean');
            $this->form_validation->set_rules('invoicedate', ' Invoice Date', 'trim|xss_clean');
            $invoice_status = $this->input->post('invoice_status');
            // Check the invoice status value and apply additional validation rules
            if ($invoice_status == "1") {
                $this->form_validation->set_rules('advanceinvoicenumber', 'Advance Invoice Number', 'trim|required|xss_clean');
                $this->form_validation->set_rules('advanceinvoicedate', 'Advance Invoice Date', 'trim|required|xss_clean');
            } elseif ($invoice_status == "2") {
                $this->form_validation->set_rules('invoicenumber', 'Advance Invoice Number', 'trim|required|xss_clean');
                $this->form_validation->set_rules('invoicedate', 'Advance Invoice Date', 'trim|required|xss_clean');
            }

            if ($this->form_validation->run() == TRUE) {

                $taskcode = $this->input->post('taskcode');
                $taskdesc = $this->input->post('taskdesc');
                $billable = $this->input->post('billable');
                $is_travel = $this->input->post('is_travel');
                $parent_project = $this->input->post('parent_project');
                $parent_task = $this->input->post('parent_task');
                $status = $this->input->post('status');
                $invoice_number = $this->input->post('invoicenumber');
                $invoice_date = $this->input->post('invoicedate');
                $deploy_date = $this->input->post('deployDate');
                $is_recurring = $this->input->post('isrecurring');
                $invoice_status = $this->input->post('invoice_status');
                $advance_invoice_number = $this->input->post('advanceinvoicenumber');
                $advance_invoice_date = $this->input->post('advanceinvoicedate');
                $is_crossbillable = $this->input->post('iscrossbillable');
                if ($invoice_date) {
                    $date = DateTime::createFromFormat('d/m/Y', $invoice_date);
                    $invoice_date = $date->format('Y-m-d');
                } else {
                    $invoice_date = null;
                }
                if ($advance_invoice_date) {
                    $date = DateTime::createFromFormat('d/m/Y', $advance_invoice_date);
                    $advance_invoice_date = $date->format('Y-m-d');
                } else {
                    $advance_invoice_date = null;
                }
                if ($deploy_date) {
                    $date = DateTime::createFromFormat('d/m/Y', $deploy_date);
                    $deploy_date = $date->format('Y-m-d');
                } else {
                    $deploy_date = null;
                }
                $numb_task = $this->TaskManagementModel->checkTask($taskcode, $taskid);
                if ($numb_task == 0) {
                    $updateSpecificData = array(
                        'task_code' => $taskcode,
                        'billable' => $billable,
                        'is_travel' => $is_travel,
                        'parent_project' => $parent_project,
                        'parent_task' => $parent_task,
                        'task_desc' => $taskdesc,
                        'status' => $status,
                        'invoice_number' => $invoice_number,
                        'invoice_date' => $invoice_date,
                        'is_recurring' => $is_recurring,
                        'deploy_date' => $deploy_date,
                        'invoice_status' => $invoice_status,
                        'advance_invoice_date' => $advance_invoice_date,
                        'advance_invoice_number' => $advance_invoice_number,
                        'is_crossbillable' => $is_crossbillable
                    );
                    $this->db->where('id', $taskid);
                    $queryResult = $this->db->update('task_code', $updateSpecificData);
                    if ($queryResult) {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('flashSuccess', 'Task Updated successfully');
                        redirect('TaskManagementCntrl', 'refresh');
                    } else {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('flashError', 'Some error found');
                        $this->load->view('TaskManagement/TaskEdit', $editData);
                    }
                } else {
                    $this->session->set_flashdata('flashError', 'Already Have Same Task Code');
                    $this->load->view('TaskManagement/TaskEdit', $editData);
                }
            }
        }
        $this->load->view('TaskManagement/TaskEdit', $editData);
    }

}
