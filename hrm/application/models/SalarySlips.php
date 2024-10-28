<?php

Class SalarySlips extends CI_Model {
    public function getSlips() {
        $this->db->select('ss.id,ss.year,mm.month,ss.date_of_upload,ss.file_name,note,ss.date_of_upload,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,u.username');
        $this->db->from('salary_slip ss');
        $this->db->join('talents t','t.id=ss.talent','left');
        $this->db->join('users u','u.id=ss.uploaded_by','left');
        $this->db->join('master_months mm','mm.id=ss.month','left');
        $this->db->where('ss.is_deleted', 0);
        $this->db->order_by('ss.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getSlipsByResignedStatus($isResigned,$month,$year) {
        $this->db->select('ss.id,ss.year,mm.month,ss.date_of_upload,ss.file_name,note,ss.date_of_upload,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,u.username');
        $this->db->from('salary_slip ss');
        $this->db->join('talents t','t.id=ss.talent','left');
        $this->db->join('users u','u.id=ss.uploaded_by','left');
        $this->db->join('master_months mm','mm.id=ss.month','left');
        $this->db->where('ss.is_deleted', 0);
        $this->db->where('t.is_resigned', $isResigned);
        $this->db->where('ss.month', $month);
        $this->db->where('ss.year', $year);
        $this->db->order_by('ss.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function getMySlips($talent) {
        $this->db->select('ss.id,ss.year,mm.month,ss.date_of_upload,ss.file_name,note,ss.date_of_upload,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,u.username');
        $this->db->from('salary_slip ss');
        $this->db->join('talents t','t.id=ss.talent','left');
        $this->db->join('users u','u.id=ss.uploaded_by','left');
        $this->db->join('master_months mm','mm.id=ss.month','left');
        $this->db->where('ss.is_deleted', 0);
        $this->db->where('ss.talent', $talent);
        $this->db->order_by('ss.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }
    public function deleteSlipById($id){
        $updateData = array(
            'is_deleted' => '1'
        );
        $this->db->where('id', $id);
        $this->db->update('salary_slip', $updateData);
        return true;
    }
    public function checkExistSalarySlip($talent,$month,$year){
        $this->db->select('id');
        $this->db->from('salary_slip ss');
        $this->db->where('ss.is_deleted', 0);
        $this->db->where('ss.talent', $talent);
        $this->db->where('ss.month', $month);
        $this->db->where('ss.year', $year);
        $query = $this->db->get();
        $rowsCount = $query->num_rows();
        if($rowsCount){
            return true;
        } else {
            return false;
        }
    }
}
