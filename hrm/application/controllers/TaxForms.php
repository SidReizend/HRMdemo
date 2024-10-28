<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class TaxForms extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('IncomeTax', '', TRUE);
        $this->load->model('defaultValues', '', TRUE);
    }

    public function index() {
        //$this->output->enable_profiler(TRUE);
        if ($this->session->userdata('logged_in')) {
            $this->load->helper(array('form'));
            $yearArray = array(
                '' => '[SELECT FINANCIAL YEAR]'
            );
            $earliest_year = $this->defaultValues->getStartYear();
            foreach (range(date('Y'), $earliest_year) as $year) {
                $yearArray[$year] = ($year) . '-' . ($year + 1);
            }
            $yearArray[($year - 1)] = ($year - 1) . '-' . ($year);
            $data['selectedYear'] = $this->input->post('year');
            $data['years'] = $yearArray;
            $yearForms = array();
            if ($this->input->post()) {
                $year = $this->input->post('year');
                $yearForms = $this->IncomeTax->getIncomeTaxFinancialYear($year);
            }
            $data['yearForms'] = $yearForms;
            $this->load->view('incomeTaxManagement/taxForms', $data);
        } else {
//If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function incomeTaxForm() {
        $this->load->helper(array('form'));
        $id = $this->uri->segment(3);
        $data['taxDetails'] = $this->IncomeTax->getIncomeTaxById($id);
        $data['taxOtherIncome'] = $this->IncomeTax->getOtherIncome($id);
        $data['taxOtherInvestments'] = $this->IncomeTax->getOtherInvestments($id);
        $data['taxOtherPermitted'] = $this->IncomeTax->getOtherPermitted($id);
//            $this->load->view('incomeTaxManagement/incomeTaxForm', $data);
        $this->load->view('incomeTaxManagement/incomeTaxFormView', $data);
    }

}
