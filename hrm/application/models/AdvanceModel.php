<?php

Class advanceModel extends CI_Model {

    function getAdvanceByResignedStatus($year, $resigned,$status,$talent) {
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'concat(p.project_code,"-",p.name) as project,'
                . 'ad.amount,ad.date_of_approval,'
                . 'concat(a.first_name," ",a.middle_name," ",a.last_name) as approved_by,'
                . 'concat(cr.first_name," ",cr.middle_name," ",cr.last_name) as created_by,'
                . 'ad.status,ad.id,ad.note');
        $this->db->from('advance ad');
        $this->db->join('talents t', 'ad.talent=t.id', 'left');
        $this->db->join('talents a', 'ad.approved_by=a.id', 'left');
        $this->db->join('talents cr', 'ad.created_by=cr.id', 'left');
        $this->db->join('project p', 'ad.project=p.id', 'left');
        if ($year) {
            $this->db->where('((YEAR(date_of_approval) = ' . $year . ' AND MONTH(date_of_approval) > 3) '
                    . 'OR (YEAR(date_of_approval) = ' . ($year + 1) . ' AND MONTH(date_of_approval) < 4)) ');
        }
        if ($status) {
            if(1 == $status)
            $this->db->where('ad.status', 0);
            if(2 == $status)
            $this->db->where('ad.status', 1);
        }
        if ($talent != '') {
            $this->db->where('t.id', $talent);
        }
        $this->db->where('t.is_resigned', $resigned);
        $this->db->order_by('ad.date_of_approval', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }
    function getAdvanceById($id) {
        $this->db->select('ad.*');
        $this->db->from('advance ad');
        $this->db->where('ad.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

}
