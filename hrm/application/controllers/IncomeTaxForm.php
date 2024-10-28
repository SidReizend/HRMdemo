<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class IncomeTaxForm extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('IncomeTax', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        $session_data = $this->session->userdata('logged_in');
        $talentId = $session_data['talentId'];
        $this->load->helper(array('form'));
        $currentFinancialYear = $this->IncomeTax->getCurrentFinancialYear();
        $data['financial_year'] = $currentFinancialYear->financial_year;
        $data['forms'] = $this->IncomeTax->getIncomeTax($talentId);
        $data['formsCurrentFinancialYear'] = $this->IncomeTax->getIncomeTaxFinancial($talentId, $currentFinancialYear->financial_year);
        $this->load->view('incomeTaxManagement/formList', $data);
    }

    public function addNewTaxForm() {
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];
        $this->load->helper(array('form'));
        $currentFinancialYear = $this->IncomeTax->getCurrentFinancialYear();
        $data['currentFinancialYear'] = $currentFinancialYear;
        // Do insert operation here
        if ($this->input->post()) {
            $countNewIncome = $this->input->post('countNewIncome');
            $countNewPermitted = $this->input->post('countNewPermitted');
            $countNewInvestments = $this->input->post('countNewInvestments');
            $address = $this->input->post('address');
            $selfOccupied = $this->input->post('selfOccupied');
            $netAnualIncome = $this->input->post('netAnualIncome');
            $houseLoanInterest = $this->input->post('houseLoanInterest');
            $preConstructionInterest = $this->input->post('preConstructionInterest');
            $hraRent = $this->input->post('hraRent');
            $providentFund = $this->input->post('providentFund');
            $pensionFund = $this->input->post('pensionFund');
            $principalRepayment = $this->input->post('principalRepayment');
            $inserancePremium = $this->input->post('inserancePremium');
            $termDeposit = $this->input->post('termDeposit');
            $savings = $this->input->post('savings');
            $mutualFund = $this->input->post('mutualFund');
            $childrenTutionFee = $this->input->post('childrenTutionFee');
            $totalInvestments = $this->input->post('totalInvestments');
            $pensionScheme = $this->input->post('pensionScheme');
            $insurancePremiumDeduction = $this->input->post('insurancePremiumDeduction');
            $donations = $this->input->post('donations');

            $insertData = array(
                'talent' => $session_data['talentId'],
                'address' => $address,
                'self_occupied' => $selfOccupied,
                'net_anual_income' => $netAnualIncome,
                'house_loan_interest' => $houseLoanInterest,
                'pre_construction_interest' => $preConstructionInterest,
                'hra_rent' => $hraRent,
                'provident_fund' => $providentFund,
                'pension_fund' => $pensionFund,
                'principal_repayment' => $principalRepayment,
                'insurance_premium' => $inserancePremium,
                'term_deposit' => $termDeposit,
                'savings' => $savings,
                'mutual_fund' => $mutualFund,
                'children_tution_fee' => $childrenTutionFee,
                'total_investments' => $totalInvestments,
                'pension_scheme' => $pensionScheme,
                'insurance_premium_deduction' => $insurancePremiumDeduction,
                'donations' => $donations,
                'financial_year' => $currentFinancialYear->id,
            );
            $this->db->trans_begin();
            $queryResult = $this->db->insert('income_tax_form', $insertData);
            $insert_id = $this->db->insert_id();
            if ($queryResult) {
                for ($i = 1; $i <= $countNewIncome; $i++) {
                    if ($this->input->post('otherIncomesName' . $i)) {
                        $insertData = array(
                            'tax_form_id' => $insert_id,
                            'income_name' => $this->input->post('otherIncomesName' . $i),
                            'income' => $this->input->post('otherIncome' . $i),
                        );
                        $queryResult = $this->db->insert('income_tax_other_income', $insertData);
                    }
                }
                for ($i = 1; $i <= $countNewPermitted; $i++) {
                    if ($this->input->post('otherPermittedName' . $i)) {
                        $insertData = array(
                            'tax_form_id' => $insert_id,
                            'permitted_name' => $this->input->post('otherPermittedName' . $i),
                            'income' => $this->input->post('otherPermittedIncome' . $i),
                        );
                        $queryResult = $this->db->insert('income_tax_other_permitted', $insertData);
                    }
                }
                for ($i = 1; $i <= $countNewInvestments; $i++) {
                    if ($this->input->post('otherInvestmentsName' . $i)) {
                        $insertData = array(
                            'tax_form_id' => $insert_id,
                            'investment_name' => $this->input->post('otherInvestmentsName' . $i),
                            'income' => $this->input->post('otherInvestmentsIncome' . $i),
                        );
                        $queryResult = $this->db->insert('income_tax_other_investments', $insertData);
                    }
                }
                //creating notification
                $insertData = array(
                    'user' => 12,
                    'type' => 'yellow',
                    'message' => $session_data['firstName'] . ' ' . $session_data['middleName'] . ' ' . $session_data['lastName'] . ' Filled Tax Declaration Form',
                    'message_time' => date('Y-m-d H:i:s')
                );
                $queryResult = $this->db->insert('notifications', $insertData);
                $this->db->trans_commit();
                $this->session->set_flashdata('flashSuccess', 'New form is added successfully');
                redirect('incomeTaxForm', 'refresh');
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flashError', 'Some error found');
            }
        }
        // set flash message


        $data['x'] = '';
        $this->load->view('incomeTaxManagement/incomeTaxForm', $data);
    }

    public function incomeTaxFormUpdate() {
        $session_data = $this->session->userdata('logged_in');
        $data['username'] = $session_data['username'];
        $this->load->helper(array('form'));
        $currentFinancialYear = $this->IncomeTax->getCurrentFinancialYear();
        $id = $this->uri->segment(3);
        // Do insert operation here
        if ($this->input->post()) {
            $formId = $this->input->post('formId');
            $countNewIncome = $this->input->post('countNewIncome');
            $countNewPermitted = $this->input->post('countNewPermitted');
            $countNewInvestments = $this->input->post('countNewInvestments');
            $address = $this->input->post('address');
            $selfOccupied = $this->input->post('selfOccupied');
            $netAnualIncome = $this->input->post('netAnualIncome');
            $houseLoanInterest = $this->input->post('houseLoanInterest');
            $preConstructionInterest = $this->input->post('preConstructionInterest');
            $hraRent = $this->input->post('hraRent');
            $providentFund = $this->input->post('providentFund');
            $pensionFund = $this->input->post('pensionFund');
            $principalRepayment = $this->input->post('principalRepayment');
            $inserancePremium = $this->input->post('inserancePremium');
            $termDeposit = $this->input->post('termDeposit');
            $savings = $this->input->post('savings');
            $mutualFund = $this->input->post('mutualFund');
            $childrenTutionFee = $this->input->post('childrenTutionFee');
            $totalInvestments = $this->input->post('totalInvestments');
            $pensionScheme = $this->input->post('pensionScheme');
            $insurancePremiumDeduction = $this->input->post('insurancePremiumDeduction');
            $donations = $this->input->post('donations');

            $updateData = array(
                'talent' => $session_data['talentId'],
                'address' => $address,
                'self_occupied' => $selfOccupied,
                'net_anual_income' => $netAnualIncome,
                'house_loan_interest' => $houseLoanInterest,
                'pre_construction_interest' => $preConstructionInterest,
                'hra_rent' => $hraRent,
                'provident_fund' => $providentFund,
                'pension_fund' => $pensionFund,
                'principal_repayment' => $principalRepayment,
                'insurance_premium' => $inserancePremium,
                'term_deposit' => $termDeposit,
                'savings' => $savings,
                'mutual_fund' => $mutualFund,
                'children_tution_fee' => $childrenTutionFee,
                'total_investments' => $totalInvestments,
                'pension_scheme' => $pensionScheme,
                'insurance_premium_deduction' => $insurancePremiumDeduction,
                'donations' => $donations,
            );
            $this->db->trans_begin();
            $this->db->where('id', $formId);
            $queryResultRoleUpdate = $this->db->update('income_tax_form', $updateData);
            if ($queryResultRoleUpdate) {
                $this->db->where('tax_form_id', $formId);
                $this->db->delete('income_tax_other_income');
                $this->db->where('tax_form_id', $formId);
                $this->db->delete('income_tax_other_permitted');
                $this->db->where('tax_form_id', $formId);
                $this->db->delete('income_tax_other_investments');
                for ($i = 1; $i <= $countNewIncome; $i++) {
                    if ($this->input->post('otherIncomesName' . $i)) {
                        $insertData = array(
                            'tax_form_id' => $formId,
                            'income_name' => $this->input->post('otherIncomesName' . $i),
                            'income' => $this->input->post('otherIncome' . $i),
                        );
                        $queryResult = $this->db->insert('income_tax_other_income', $insertData);
                    }
                }
                for ($i = 1; $i <= $countNewPermitted; $i++) {
                    if ($this->input->post('otherPermittedName' . $i)) {
                        $insertData = array(
                            'tax_form_id' => $formId,
                            'permitted_name' => $this->input->post('otherPermittedName' . $i),
                            'income' => $this->input->post('otherPermittedIncome' . $i),
                        );
                        $queryResult = $this->db->insert('income_tax_other_permitted', $insertData);
                    }
                }
                for ($i = 1; $i <= $countNewInvestments; $i++) {
                    if ($this->input->post('otherInvestmentsName' . $i)) {
                        $insertData = array(
                            'tax_form_id' => $formId,
                            'investment_name' => $this->input->post('otherInvestmentsName' . $i),
                            'income' => $this->input->post('otherInvestmentsIncome' . $i),
                        );
                        $queryResult = $this->db->insert('income_tax_other_investments', $insertData);
                    }
                }
                //creating notification
                $insertData = array(
                    'user' => 12,
                    'type' => 'yellow',
                    'message' => $session_data['firstName'] .' updated Tax Declaration Form',
                    'message_time' => date('Y-m-d H:i:s')
                );
                $queryResult = $this->db->insert('notifications', $insertData);
                $this->db->trans_commit();
                $this->session->set_flashdata('flashSuccess', 'Form Updated successfully');
                redirect('incomeTaxForm', 'refresh');
            } else {
                $this->db->trans_rollback();
                $this->session->set_flashdata('flashError', 'Some error found');
            }
        }
        $data['taxDetails'] = $this->IncomeTax->getIncomeTaxById($id);
        $data['taxOtherIncome'] = $this->IncomeTax->getOtherIncome($id);
        $data['taxOtherInvestments'] = $this->IncomeTax->getOtherInvestments($id);
        $data['taxOtherPermitted'] = $this->IncomeTax->getOtherPermitted($id);
//            $this->load->view('incomeTaxManagement/incomeTaxForm', $data);
        $this->load->view('incomeTaxManagement/incomeTaxFormEdit', $data);
    }

}
