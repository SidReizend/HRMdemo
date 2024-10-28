<?php

Class IncomeTax extends CI_Model {

    function getIncomeTax($talent) {
        $this->db->select('itf.*,mfy.financial_year as fin_year');
        $this->db->from('income_tax_form itf');
        $this->db->join('master_financial_year mfy', 'mfy.id=itf.financial_year', 'left');
        $this->db->where('talent', $talent);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }
    function getIncomeTaxFinancial($talent,$currentFinancialYear) {
        $this->db->select('itf.*,mfy.financial_year as fin_year');
        $this->db->from('income_tax_form itf');
        $this->db->join('master_financial_year mfy', 'mfy.id=itf.financial_year', 'left');
        $this->db->where('talent', $talent);
        $this->db->where('mfy.financial_year', $currentFinancialYear);
        $query = $this->db->get();
        return $query->row();
    }
    function getIncomeTaxFinancialYear($currentFinancialYear) {
        $this->db->select('itf.*,mfy.financial_year as fin_year,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('income_tax_form itf');
        $this->db->join('master_financial_year mfy', 'mfy.id=itf.financial_year', 'left');
        $this->db->join('talents t', 't.id=itf.talent', 'left');
        $this->db->where('mfy.financial_year', $currentFinancialYear);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getIncomeTaxById($id) {
        $this->db->select('itf.*,mfy.financial_year as fin_year,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('income_tax_form itf');
        $this->db->join('talents t', 't.id=itf.talent', 'left');
        $this->db->join('master_financial_year mfy', 'mfy.id=itf.financial_year', 'left');
        $this->db->where('itf.id', $id);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return $query->row();
    }

    function getOtherIncome($tax_form_id) {
        $this->db->select('*');
        $this->db->from('income_tax_other_income');
        $this->db->where('tax_form_id', $tax_form_id);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getOtherInvestments($tax_form_id) {
        $this->db->select('*');
        $this->db->from('income_tax_other_investments');
        $this->db->where('tax_form_id', $tax_form_id);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getOtherPermitted($tax_form_id) {
        $this->db->select('*');
        $this->db->from('income_tax_other_permitted');
        $this->db->where('tax_form_id', $tax_form_id);
        $this->db->order_by('id', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getCurrentFinancialYear() {
        $this->db->select('m.financial_year,m.id');
        $this->db->from('default_values d');
        $this->db->join('master_financial_year m','m.id=d.value','left');
        $this->db->where('d.id', 5);
        $query = $this->db->get();
        return $query->row();
    }

}
