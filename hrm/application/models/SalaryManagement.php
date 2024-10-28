<?php

Class SalaryManagement extends CI_Model {

    function getSalaryComponat($talent, $comp) {
        $this->db->select('t.*,s.component,type');
        $this->db->from('talant_salary_components t');
        $this->db->join('salary_components s', 't.component=s.id', 'left');
        $this->db->where('t.talent', $talent);
        $this->db->where('s.id', $comp);
        $this->db->order_by('id asc');
        $query = $this->db->get();
        if ($query->num_rows()) {
            $temp = $query->row();
            return $temp->value;
        } else {
            return;
        }
    }
    function getSalary($talent) {
        $this->db->select('');
        $this->db->from('talent_salary t');
        $this->db->where('t.talent', $talent);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $temp = $query->row();
            return $temp->salary;
        } else {
            return;
        }
    }

    function getAdditionSalaryComponats($talent) {
        $this->db->select('t.*,s.component,type');
        $this->db->from('talant_salary_components t');
        $this->db->join('salary_components s', 't.component=s.id', 'left');
        $this->db->where('t.talent', $talent);
        $this->db->where('s.type', 1);
        $this->db->order_by('id asc');
        $query = $this->db->get();
        $this->db->last_query();
        return $query->result_array();
    }

    function getDeductionSalaryComponats($talent) {
        $this->db->select('t.*,s.component,type');
        $this->db->from('talant_salary_components t');
        $this->db->join('salary_components s', 't.component=s.id', 'left');
        $this->db->where('t.talent', $talent);
        $this->db->where('s.type', 2);
        $this->db->order_by('id asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function basicComponats($talent) {
        $this->db->select('t.*,s.component,type');
        $this->db->from('talant_salary_components t');
        $this->db->join('salary_components s', 't.component=s.id', 'left');
        $this->db->where('t.talent', $talent);
        $this->db->where('s.type', 3);
        $this->db->order_by('id asc');
        $query = $this->db->get();
        return $query->result_array();
    }

}
