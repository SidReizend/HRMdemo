<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class PayRoll extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('SalaryManagement', '', TRUE);
        $this->load->model('talent', '', TRUE);
    }

    public function index() {
        $this->load->helper(array('form'));
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $talents = $this->talent->getTalents();
            $talentsDropdown = array("" => "[SELECT Talent]");
            foreach ($talents as $talent) {
                $id = $talent['id'];
                $talentsDropdown[$id] = $talent['name'];
            }
            $data['talentsDropdown'] = $talentsDropdown;
            $this->load->view('salaryManagement/talentSalaryComponant', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function salaryProcessing() {
        $this->load->helper(array('form'));
        if ($this->session->userdata('logged_in')) {
            //check the user is loged in
            $session_data = $this->session->userdata('logged_in');
            $talents = $this->talent->getTalents();
            $talentsDropdown = array("all" => "Select Talent");
            foreach ($talents as $talent) {
                $id = $talent['id'];
                $talentsDropdown[$id] = $talent['name'];
            }
            $data['talentsDropdown'] = $talentsDropdown;
            $this->load->view('salaryManagement/salaryProcessing', $data);
        } else {
            //If no session, redirect to login page
            redirect('login', 'refresh');
        }
    }

    public function ajaxTalantSalaryComponants() {
        //using url for get project
        $talent = $this->input->get('talent');
        $this->load->helper('form');

        //get the sub project exist status
//        $data['additionSalaryComponats'] = $this->SalaryManagement->getAdditionSalaryComponats($talent);
        $row = array();
        $row['1'] = $this->SalaryManagement->getSalaryComponat($talent, 1);
        $row['2'] = $this->SalaryManagement->getSalaryComponat($talent, 2);
        $row['3'] = $this->SalaryManagement->getSalaryComponat($talent, 3);
        $row['4'] = $this->SalaryManagement->getSalaryComponat($talent, 4);
        $row['5'] = $this->SalaryManagement->getSalaryComponat($talent, 5);
        $row['6'] = $this->SalaryManagement->getSalaryComponat($talent, 6);
        $row['7'] = $this->SalaryManagement->getSalaryComponat($talent, 7);
        $row['8'] = $this->SalaryManagement->getSalaryComponat($talent, 8);
        $row['9'] = $this->SalaryManagement->getSalaryComponat($talent, 9);
        $row['10'] = $this->SalaryManagement->getSalaryComponat($talent, 10);
        $data['row'] = $row;
//        $data['deductionSalaryComponats'] = $this->SalaryManagement->getDeductionSalaryComponats($talent);
//        $data['basicComponats'] = $this->SalaryManagement->basicComponats($talent);
        $this->load->view('salaryManagement/salaryComponants', $data);
        //if sub project exists then shows the sub projects else not
    }

    public function ajaxProcessSalary() {
        //using url for get project
        $talent = $this->input->get('talent');
        $this->load->helper('form');
        //get the sub project exist status
        if ($talent != 'all') {
            $data['additionSalaryComponats'] = $this->SalaryManagement->getAdditionSalaryComponats($talent);
            $data['deductionSalaryComponats'] = $this->SalaryManagement->getDeductionSalaryComponats($talent);
            $salary = 0;
            $salary += $this->SalaryManagement->getSalaryComponat($talent, 1);
            $salary += $this->SalaryManagement->getSalaryComponat($talent, 2);
            $salary += $this->SalaryManagement->getSalaryComponat($talent, 3);
            $salary -= $this->SalaryManagement->getSalaryComponat($talent, 4);
            $salary -= $this->SalaryManagement->getSalaryComponat($talent, 5);
            $salary -= $this->SalaryManagement->getSalaryComponat($talent, 6);
            $salary -= $this->SalaryManagement->getSalaryComponat($talent, 7);
            $salary -= $this->SalaryManagement->getSalaryComponat($talent, 8);
            if ($salary) {
                $this->db->where('talent', $talent);
                $queryResult = $this->db->delete('talent_salary');
                $insertData = array(
                    'salary' => $salary,
                    'talent' => $talent,
                );
                $queryResult = $this->db->insert('talent_salary', $insertData);
            }
        }
        $data['basicComponats'] = $this->SalaryManagement->basicComponats($talent);
        $this->load->view('salaryManagement/ajaxProcessSalary', $data);
        //if sub project exists then shows the sub projects else not
    }

    public function ajaxExcel() {
        //using url for get project
        $talent = $this->uri->segment(3);
        $this->load->helper('form');
        $this->load->helper('url');
        $heading = array(
            0 => 'SL. No.',
            1 => 'Talent',
            2 => 'Account Number',
            3 => 'Salary',
        );
        $slno = 0;
        $content = array();
        $talents = $this->talent->getTalents();
        foreach ($talents as $talent) {
            $content[$slno] = array(
                0 => $slno + 1,
                1 => $talent['name'],
                2 => $this->SalaryManagement->getSalaryComponat($talent['id'], 10),
                3 => $this->SalaryManagement->getSalary($talent['id']),
            );
            $slno++;
        }
        $data['heading'] = $heading;
        $data['tableName'] = 'Salary';
        $data['content'] = $content;
        $data['slno'] = $slno;
//        echo '<pre>';
//        var_dump($content);
//        echo '</pre>';
        $this->load->view('excel/excel', $data);
    }

    public function ajaxUpdateCompanant() {
        $talent = $this->input->get('talent');
        $id = $this->input->get('id');
        $value = $this->input->get('value');
        $this->db->where('talent', $talent);
        $this->db->where('component', $id);
        $queryResult = $this->db->delete('talant_salary_components');
        $insertData = array(
            'component' => $id,
            'talent' => $talent,
            'value' => $value
        );
        //inserting
        $queryResult = $this->db->insert('talant_salary_components', $insertData);
        if (!$queryResult) {
            echo "0";
        } else {
            echo "1";
        }
    }

}
