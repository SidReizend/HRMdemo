<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TranTracking extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('claim', '', TRUE);
        $this->load->model('talent', '', TRUE);
        $this->load->model('projects', '', TRUE);
        $this->load->model('timePredictionModel', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
        $this->load->model('talentAttendenceSheet', '', TRUE);
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            $session_data = $this->session->userdata('logged_in');
            $selectedCompany = '';
            $selectedMonth = '';
            $selectedSettlement = '';
            $selectedYear = '';
            $selectedProject = '';
            if ($this->input->post()) {
                $selectedMonth = $this->input->post('month');
                $selectedSettlement = $this->input->post('settlement');
                $selectedCompany = $this->input->post('company');
                $selectedYear = $this->input->post('year');
                $selectedProject = $this->input->post('project');
            } else {
                if ($this->session->flashdata('flashError') || $this->session->flashdata('flashSuccess')) {
                    if ($this->session->userdata('transaction')) {
                        $sessionTransaction = $this->session->userdata('transaction');
                        $selectedMonth = $sessionTransaction['selectedMonth'];
                        $selectedSettlement = $sessionTransaction['selectedSettlement'];
                        $selectedCompany = $sessionTransaction['selectedCompany'];
                        $selectedYear = $sessionTransaction['selectedYear'];
                        $selectedProject = $sessionTransaction['selectedProject'];
                    }
                }
            }

            $data['username'] = $session_data['username'];
            $this->load->helper(array('form'));
            $settlementDropdown = array(
                '' => '[SEL. Settlement]',
                'Account' => 'Account',
                'Salary' => 'Salary'
            );
            $statusDropdown = array(
                '0' => 'Planned',
                '1' => 'Settled'
            );
            $companies = $this->talent->getCompanies();
            $companyDropdown = array("" => "[SELECT Company]");
            foreach ($companies as $company) {
                $id = $company['id'];
                $companyDropdown[$id] = $company['company_name'];
            }
            $projectDropdown = array("" => "[SELECT Project]");
            $projects = $this->projects->getProjectsAndHeads();
            foreach ($projects as $project) {
                $id = $project['id'];
                $projectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
            }
            $currentMonth = date('m');
            if ($currentMonth <= 3) {
                $SelectBoxyear = date('Y') - 1;
            } else {
                $SelectBoxyear = date('Y');
            }
            $data['selectedYear'] = $selectedYear == '' ? $SelectBoxyear : $selectedYear;
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);

            $monthArray = array(
                '' => '[SELECT MONTH]'
            );
            $months = $this->talentAttendenceSheet->getMonths();
            foreach ($months as $month) {
                $monthArray[$month['id']] = $month['month'];
            }


            $data['selectedMonth'] = $selectedMonth;
            $data['selectedSettlement'] = $selectedSettlement;
            $data['selectedStatusSearch'] = $this->input->post('statusSearch');
            $data['selectedCompany'] = $selectedCompany;
            $data['companyDropdown'] = $companyDropdown;
            $data['companies'] = $companies;
            $data['years'] = $yearArray;
            $data['selectedProject'] = $selectedProject;
            $data['projectDropdown'] = $projectDropdown;
            $data['monthDropdown'] = $monthArray;
            $data['claims'] = $this->claim->getTransactions($data['selectedYear'], $data['selectedCompany'], $data['selectedProject'], 'desc', $data['selectedMonth']);

            //set new sessions
            $sess_array = array(
                'selectedMonth' => $data['selectedMonth'],
                'selectedSettlement' => $data['selectedSettlement'],
                'selectedYear' => $data['selectedYear'],
                'selectedProject' => $data['selectedProject'],
                'selectedCompany' => $data['selectedCompany'],
            );
            $this->session->set_userdata('transaction', $sess_array);

            $this->load->view('claimManagement/tranTracks', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function addNewTransaction() {
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $companies = $this->talent->getCompanies();
            $companyDropdown = array("" => "[SELECT Company]");
            foreach ($companies as $company) {
                $id = $company['id'];
                $companyDropdown[$id] = $company['company_name'];
            }
            $projectDropdown = array("" => "[SELECT Project]");
            $projects = $this->projects->getProjectsAndHeads();
            foreach ($projects as $project) {
                $id = $project['id'];
                $projectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
            }

            $claims = $this->claim->getAccountSettledClaims();
            $claimDropdown = array("" => "[SELECT Claim]");
            foreach ($claims as $claim) {
                $id = $claim['id'];
                $claimDropdown[$id] = $claim['claim_id'] . '-' . $claim['first_name'];
            }

            $optionDropdown = array(
                "1" => "Full",
                "2" => "Partial"
            );
            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->library('form_validation');

            //for form validayion, setting rules
            $selectedCompany = $this->input->post('company');
            $selectedProject = $this->input->post('project');
            $selectedApprovedBy = $this->input->post('approvedBy');
            $subProject = $this->input->post('subProject');
            $note = $this->input->post('note');
            $remarks = $this->input->post('remarks');
            $claims = $this->input->post('claims');
            $credit_amount = $this->input->post('credit_amount');
            $debit_amount = $this->input->post('debit_amount');



            if ($this->input->post()) {
                if (($debit_amount == 0 && $credit_amount == 0) || ($debit_amount != 0 && $credit_amount != 0)) {
                    $this->session->set_flashdata('flashError', 'Credit and debit amount are invalid');
                } else {
                    //Validation Success.  
                    $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateOfApproval'));
                    $dateOfApproval = $date->format('Y-m-d');
                    $createdBy = $session_data['talentId'];
                    //die();
                    // Do insert operation here
                    $this->db->trans_begin();
                    $queryError = FALSE;
                    $insertUserData = array(
                        'company' => $selectedCompany,
                        'project' => $selectedProject,
                        'credit_amount' => $credit_amount,
                        'debit_amount' => $debit_amount,
                        'date_of_approval' => $dateOfApproval,
                        'created_by' => $createdBy,
                        'task_code' => $subProject,
                        'note' => $note,
                        'remarks' => $remarks,
                    );
                    $queryResult = $this->db->insert('transaction_tracking', $insertUserData);
                    if ($queryResult) {
                        $trasaction = $this->db->insert_id();

                        $slNo = $this->input->post('slNo');
                        if ($slNo > 1) {
                            for ($i = 1; $i < $slNo; $i++) {
                                $claimId = $this->input->post('claimId' . $i);
                                $claimAmount = $this->input->post('claimAmount' . $i);
                                $claimOption = $this->input->post('option' . $i);
                                if ($claimId) {
                                    $prevAmount = $this->claim->getClaimTranAmountFactored($claimId);
                                    $claimDetails = $this->claim->getClaimById($claimId);
                                    $amountFactored = $this->claim->getClaimTranAmountFactored($claimId);
                                    if (($amountFactored + $claimAmount) > $claimDetails->amount) {
                                        $queryError = TRUE;
                                        $errorMessage = "Failed due to claim amount exceeded transaction amount";
                                    }
                                    $insertUserData = array(
                                        'claim_id' => $claimId,
                                        'trasaction' => $trasaction,
                                        'option' => $claimOption,
                                        'transaction_amount' => $claimAmount,
                                    );
                                    $queryResult = $this->db->insert('transaction_claim_mapping', $insertUserData);
                                    if (!$queryResult) {
                                        $queryError = TRUE;
                                        $errorMessage = "Some error found";
                                    }
                                }
                            }
                        }
                        //if success
                        if (FALSE === $queryError) {
                            $this->db->trans_commit();
                            //if success
                            $this->session->set_flashdata('flashSuccess', 'New Transaction is added successfully');
                            redirect('tranTracking', 'refresh');
                        } else {
                            $this->db->trans_rollback();
                            $this->session->set_flashdata('flashError', $errorMessage);
                        }
                    } else {
                        //failure
                        $this->session->set_flashdata('flashError', 'Some error found');
                    }
                }
            }

            $data['projectDropdown'] = $projectDropdown;
            $data['talentId'] = $session_data['talentId'];
            $data['companyDropdown'] = $companyDropdown;
            $data['selectedCompany'] = $selectedCompany;
            $data['selectedProject'] = $selectedProject;
            $data['selectedApprovedBy'] = $selectedApprovedBy;
            $data['projectDropdown'] = $projectDropdown;
            $data['claimDropdown'] = $claimDropdown;
            $data['optionDropdown'] = $optionDropdown;
            $this->load->view('claimManagement/addNewTransaction', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function editTransaction() {
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $this->load->helper('url');
            $transId = $this->uri->segment(3);
            $transaction = $this->claim->getTransById($transId);
            $companies = $this->talent->getCompanies();
            $companyDropdown = array("" => "[SELECT Company]");
            foreach ($companies as $company) {
                $id = $company['id'];
                $companyDropdown[$id] = $company['company_name'];
            }
            $projectDropdown = array("" => "[SELECT Project]");
            $projects = $this->projects->getProjectsAndHeads();
            foreach ($projects as $project) {
                $id = $project['id'];
                $projectDropdown[$id] = $project['project_code'] . '-' . $project['name'];
            }
            //find the claims mapped to the transaction
            $claimsMapped = $this->claim->getMappedClaimsToTransaction($transId);

            $claims = $this->claim->getAccountSettledClaims();
            $claimDropdown = array("" => "[SELECT Claim]");
            foreach ($claims as $claim) {
                $id = $claim['id'];
                $claimDropdown[$id] = $claim['claim_id'] . '-' . $claim['first_name'];
            }

            $optionDropdown = array(
                "1" => "Full",
                "2" => "Partial"
            );

            //helper for form and date
            $this->load->helper(array('form'));
            $this->load->library('form_validation');
            //for form validayion, setting rules
            $this->form_validation->set_rules('company', 'company', 'trim|required|xss_clean');
            if ($this->input->post()) {
                $date = DateTime::createFromFormat('d/m/Y', $this->input->post('dateOfApproval'));
                $dateOfApproval = $date->format('Y-m-d');
                $selectedCompany = $this->input->post('company');
                $selectedProject = $this->input->post('project');
                $selectedSubProject = $this->input->post('subProject');
                $note = $this->input->post('note');
                $remarks = $this->input->post('remarks');
                $credit_amount = $this->input->post('credit_amount');
                $debit_amount = $this->input->post('debit_amount');
                $subProject = $this->input->post('subProject');
                if ($this->form_validation->run() == TRUE) {
                    if (($debit_amount == 0 && $credit_amount == 0) || ($debit_amount != 0 && $credit_amount != 0)) {
                        $this->session->set_flashdata('flashError', 'Credit and debit amount are invalid');
                    } else {

                        $this->db->trans_begin();
                        $queryError = FALSE;
                        //Validation Success.  
                        // Do insert operation here
                        $updateData = array(
                            'company' => $selectedCompany,
                            'project' => $selectedProject,
                            'date_of_approval' => $dateOfApproval,
                            'note' => $note,
                            'credit_amount' => $credit_amount,
                            'debit_amount' => $debit_amount,
                            'task_code' => $subProject,
                            'remarks' => $remarks,
                        );
                        $this->db->where('id', $transId);
                        $queryResult = $this->db->update('transaction_tracking', $updateData);
                        if ($queryResult) {
                            //deleting alredy existing claims
                            $this->db->where('trasaction', $transId);
                            $this->db->delete('transaction_claim_mapping');
                            //adding as new claims
                            $slNo = $this->input->post('slNo');
                            if ($slNo > 1) {
                                for ($i = 1; $i < $slNo; $i++) {
                                    $claimId = $this->input->post('claimId' . $i);
                                    $claimAmount = $this->input->post('claimAmount' . $i);
                                    $claimOption = $this->input->post('option' . $i);
                                    if ($claimId) {
                                        $prevAmount = $this->claim->getClaimTranAmountFactored($claimId);
                                        $claimDetails = $this->claim->getClaimById($claimId);
                                        $amountFactored = $this->claim->getClaimTranAmountFactored($claimId);
                                        if (($amountFactored + $claimAmount) > $claimDetails->amount) {
                                            $queryError = TRUE;
                                            $errorMessage = "Failed due to claim amount exceeded transaction amount";
                                        }
                                        $insertUserData = array(
                                            'claim_id' => $claimId,
                                            'trasaction' => $transId,
                                            'option' => $claimOption,
                                            'transaction_amount' => $claimAmount,
                                        );
                                        $queryResult = $this->db->insert('transaction_claim_mapping', $insertUserData);
                                        if (!$queryResult) {
                                            $queryError = TRUE;
                                            $errorMessage = "Some error found";
                                        }
                                    }
                                }
                            }
                            if (FALSE === $queryError) {
                                $this->db->trans_commit();
                                //if success
                                $this->session->set_flashdata('flashSuccess', 'Transaction updated successfully');
                                redirect('tranTracking', 'refresh');
                            } else {
                                $this->db->trans_rollback();
                                $this->session->set_flashdata('flashError', $errorMessage);
                            }
                        } else {
                            $this->db->trans_rollback();
                            //failure
                            $this->session->set_flashdata('flashError', 'Some error found');
                        }
                    }
                }
                $dateOfApproval = date('d/m/Y', strtotime($dateOfApproval));
            } else {
                $selectedCompany = $transaction->company;
                $selectedProject = $transaction->project;
                $selectedSubProject = $transaction->task_code;
                $credit_amount = $transaction->credit_amount;
                $debit_amount = $transaction->debit_amount;
                $note = $transaction->note;
                $remarks = $transaction->remarks;
                $dateOfApproval = date('d/m/Y', strtotime($transaction->date_of_approval));
            }
            $subProjects = array();
            $getSubProjectStatus = FALSE;
            if ($selectedProject) {
                $getSubProjectStatus = $this->projects->getSubProjectStatus($selectedProject);
            }
            if ($getSubProjectStatus) {
                $project_details = $this->projects->getProjectById($selectedProject);
                $assigned_tasks = explode(",", $project_details->assigned_tasks);
                $subProjects = $this->timePredictionModel->getProjectTask($assigned_tasks);
            }
            $subProjectDropdown = array("" => "[SELECT sub project]");
            foreach ($subProjects as $project) {
                $id = $project['id'];
                $subProjectDropdown[$id] = $project['task_code'];
            }
            $data['subProjectDropdown'] = $subProjectDropdown;
            $data['projectDropdown'] = $projectDropdown;
            $data['companyDropdown'] = $companyDropdown;
            $data['selectedSubProject'] = $selectedSubProject;
            $data['subProjects'] = $subProjects;
            $data['selectedCompany'] = $selectedCompany;
            $data['selectedProject'] = $selectedProject;
            $data['credit_amount'] = $credit_amount;
            $data['debit_amount'] = $debit_amount;
            $data['dateOfApproval'] = $dateOfApproval;
            $data['projectDropdown'] = $projectDropdown;
            $data['note'] = $note;
            $data['remarks'] = $remarks;
            $data['claimsMapped'] = $claimsMapped;
            $data['claimDropdown'] = $claimDropdown;
            $data['optionDropdown'] = $optionDropdown;
            $this->load->view('claimManagement/editTransaction', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function statusUpdate() {
        //using url for get id
        $this->load->helper('url');
        $id = $this->input->get('id');
        $status = $this->input->get('status');
        $updateData = array(
            'status' => $status
        );
        $this->db->where('id', $id);
        $this->db->update('claims', $updateData);
        echo 1;
    }

    public function subProjectMapping() {
        //using url for get project
        $project = $this->input->get('project');
        $this->load->helper('form');

        //get the sub project exist status

        $getSubProjectStatus = $this->projects->getSubProjectStatus($project);
        if ($getSubProjectStatus) {
            $project_details = $this->projects->getProjectById($project);
            $assigned_tasks = explode(",", $project_details->assigned_tasks);
            $subProjects = $this->timePredictionModel->getProjectTask($assigned_tasks);
            //var_dump($subProjects);
            $subProjectDropdown = array("" => "[SELECT sub project]");
            foreach ($subProjects as $project) {
                $id = $project['id'];
                $subProjectDropdown[$id] = $project['task_code'];
            }
            $data['subProjectDropdown'] = $subProjectDropdown;
            $this->load->view('claimManagement/subProjects', $data);
        }
        //if sub project exists then shows the sub projects else not
    }

    public function uploadTransaction() {
        $this->load->helper(array('form'));
        $sess_data = $this->session->userdata('logged_in');
        $user_id = $sess_data['id'];
        if ($this->input->post()) {
            //Validation Success.  
            // Do insert operation here
            $config['upload_path'] = './uploads/';
            $config['allowed_types'] = 'xls|xlsx|csv';
            $this->load->library('upload', $config);
            $upload_data = $this->upload->data();
            $upload_error = false;
            if ($_FILES['uploadFile']['error'] != 4) {
                if (!$this->upload->do_upload('uploadFile')) {
                    $upload_error = true;
                    $this->session->set_flashdata('flashError', $this->upload->display_errors());
                } else {
                    $note = $this->input->post('note');
                    $upload_data = $this->upload->data();
                    $fileName = $upload_data['file_name'];
                }
            }
            if (false === $upload_error) {
                $file = './uploads/' . $fileName;

                $handle = fopen($file, "r");
                $i = 0;
                $error = FALSE;
                while (($row = fgetcsv($handle)) !== FALSE) {
                    if (0 != $i) {
                        $companyName = $row[0];
                        $transactionDate = $row[1];
                        $transactionDate = str_replace("-", "/", $transactionDate);
                        $date = DateTime::createFromFormat('d/m/Y', $transactionDate);
                        $dateOfApproval = $date->format('Y-m-d');
                        $particulars = $row[2];
                        $debitAmount = $row[3] ? str_replace(",", "", $row[3]) : 0;
                        $creditAmount = $row[4] ? str_replace(",", "", $row[4]) : 0;
                        if (($debitAmount == 0 && $creditAmount == 0) || ($debitAmount != 0 && $creditAmount != 0)) {
                            $error = TRUE;
                            $this->session->set_flashdata('flashError', 'Credit and debit amount are invalid');
                        }
                        $company = $this->claim->getCompanyByCompanyName($companyName);
                        $insertUserData = array(
                            'company' => $company,
                            'credit_amount' => $creditAmount,
                            'debit_amount' => $debitAmount,
                            'date_of_approval' => $dateOfApproval,
                            'created_by' => $user_id,
                            'note' => $particulars,
                        );
                        $queryResult = $this->db->insert('transaction_tracking_temp', $insertUserData);
                        if (!$queryResult) {
                            $error = TRUE;
                            $this->session->set_flashdata('flashError', 'Some error found');
                        }
                    }
                    $i++;
                }
                if ($error === false) {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('flashSuccess', 'Transaction uploaded successfully');
                    redirect('TranTracking/uploadTransaction', 'refresh');
                } else {
                    $this->db->trans_rollback();
                }
            } else {
                $this->session->set_flashdata('flashError', 'File Upload Failed');
            }
        }
        $data['claims'] = $this->claim->getTransactionsTemp();
        $this->load->view('claimManagement/transactionUpload', $data);
    }

    public function excelDownload() {

        $this->load->helper('url');
        $company = $this->input->get('company');
        $year = $this->input->get('year');
        $project = $this->input->get('project');
        $month = $this->input->get('month');
        $claims = $this->claim->getTransactions($year, $company, $project, 'asc', $month);
        $heading = array(
            0 => 'SL. No.',
            1 => 'Company',
            2 => 'Project',
            3 => 'Task Code',
            4 => 'Credit Amount',
            5 => 'Debit Amount',
            6 => 'Date of transaction',
            7 => 'Particulars',
            8 => 'Remarks',
            10 => 'Claim Id',
        );
        $slno = 0;
        $content = array();
        foreach ($claims as $claim) {
            $content[$slno] = array(
                0 => $slno + 1,
                1 => $claim['company_name'],
                2 => $claim['project'],
                3 => $claim['task_code'],
                4 => $this->claim->commaSeperated($claim['credit_amount']),
                5 => $this->claim->commaSeperated($claim['debit_amount']),
                6 => date('d/m/Y', strtotime($claim['date_of_approval'])),
                7 => $claim['note'],
                8 => $claim['remarks'],
                9 => $this->claim->getMappedClaimsToTransactionGroupConcat($claim['tranId']),
            );
            $slno++;
        }
        $data['heading'] = $heading;
        $data['tableName'] = 'Transaction Tracks';
        $data['content'] = $content;
        $data['slno'] = $slno;
        $this->load->view('excel/excel', $data);
    }

    public function findClaimAmount() {
        $claimId = $this->input->get('claimId');
        $claimDetails = $this->claim->getClaimById($claimId);
        $response = array(
            'status' => "success",
            'message' => $claimDetails->amount
        );
        echo json_encode($response);
    }

    public function findClaimHeads() {
        $heads = $this->claimHeads();
        $response = array(
            'status' => "success",
            'message' => $heads
        );
        echo json_encode($response);
    }

    public function claimHeads() {
        $claimHeads = $this->defaultValues->getValue(37);
        $heads = (explode(",", $claimHeads));
        return $heads;
    }

    public function claimAmountFactoredValidation() {
        $claimId = $this->input->get('claimId');
        $claimAmount = $this->input->get('claimAmount');
        $claimDetails = $this->claim->getClaimById($claimId);
        $amountFactored = $this->claim->getClaimTranAmountFactored($claimId);
        if (($amountFactored + $claimAmount) > $claimDetails->amount) {
            $response = array(
                'status' => "failure",
                'message' => "Sorry alredy " . $amountFactored . " is mapped to some other transactions"
            );
        } else {
            $response = array(
                'status' => "success",
                'message' => "No issue found"
            );
        }
        echo json_encode($response);
    }

    public function approveTransaction() {

        $this->load->helper('url');
        $transTempId = $this->uri->segment(3);
        $transaction = $this->claim->getTransTempById($transTempId);
        $insertUserData = array(
            'company' => $transaction->company,
            'credit_amount' => $transaction->credit_amount,
            'debit_amount' => $transaction->debit_amount,
            'date_of_approval' => $transaction->date_of_approval,
            'created_by' => $transaction->created_by,
            'note' => $transaction->note,
            'is_uploaded' => 1,
        );
        $queryResult = $this->db->insert('transaction_tracking', $insertUserData);
        if ($queryResult) {
            $this->db->where('id', $transTempId);
            $deleteResult = $this->db->delete('transaction_tracking_temp');
            if ($deleteResult) {
                $this->session->set_flashdata('flashSuccess', 'Transaction verified successfully');
                redirect('TranTracking/uploadTransaction', 'refresh');
            } else {
                $this->session->set_flashdata('flashError', 'Some error found');
                redirect('TranTracking/uploadTransaction', 'refresh');
            }
        } else {
            $this->session->set_flashdata('flashError', 'Some error found');
            redirect('TranTracking/uploadTransaction', 'refresh');
        }
    }

    public function approveSelectedTransaction() {
        $query_error = FALSE;
        $this->db->trans_begin();
        $selectedTransactions = $this->input->post('checkedTransactions');
        $transactionsArray = (explode(",", $selectedTransactions));
        foreach ($transactionsArray as $transTempId) {
            $transaction = $this->claim->getTransTempById($transTempId);
            $insertUserData = array(
                'company' => $transaction->company,
                'credit_amount' => $transaction->credit_amount,
                'debit_amount' => $transaction->debit_amount,
                'date_of_approval' => $transaction->date_of_approval,
                'created_by' => $transaction->created_by,
                'note' => $transaction->note,
                'is_uploaded' => 1,
            );
            $queryResult = $this->db->insert('transaction_tracking', $insertUserData);
            if (!$queryResult) {
                $query_error = TRUE;
            }
            $this->db->where('id', $transTempId);
            $deleteQuery = $this->db->delete('transaction_tracking_temp');
            if (!$deleteQuery) {
                $query_error = TRUE;
            }
        }
        if ($query_error == TRUE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('flashError', 'Some error found');
            redirect('TranTracking/uploadTransaction', 'refresh');
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('flashSuccess', 'Transaction verified successfully');
            redirect('TranTracking/uploadTransaction', 'refresh');
        }
    }

    public function deleteSelectedTransaction() {
        $query_error = FALSE;
        $this->db->trans_begin();
        $selectedTransactions = $this->input->post('checkedDeleteTransactions');
        $transactionsArray = (explode(",", $selectedTransactions));
        foreach ($transactionsArray as $transTempId) {
            $this->db->where('id', $transTempId);
            $deleteQuery = $this->db->delete('transaction_tracking_temp');
            if (!$deleteQuery) {
                $query_error = TRUE;
            }
        }
        if ($query_error == TRUE) {
            $this->db->trans_rollback();
            $this->session->set_flashdata('flashError', 'Some error found');
            redirect('TranTracking/uploadTransaction', 'refresh');
        } else {
            $this->db->trans_commit();
            $this->session->set_flashdata('flashSuccess', 'Transaction verified successfully');
            redirect('TranTracking/uploadTransaction', 'refresh');
        }
    }

    public function deleteTransaction() {

        $this->load->helper('url');
        $transTempId = $this->uri->segment(3);
        $transaction = $this->claim->getTransTempById($transTempId);
        $this->db->where('id', $transTempId);
        $result = $this->db->delete('transaction_tracking_temp');
        if ($result) {
            $this->session->set_flashdata('flashSuccess', 'Transaction deleted successfully');
            redirect('TranTracking/uploadTransaction', 'refresh');
        } else {
            $this->session->set_flashdata('flashError', 'Some error found');
            redirect('TranTracking/uploadTransaction', 'refresh');
        }
    }

}
