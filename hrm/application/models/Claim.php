<?php

Class Claim extends CI_Model {

    function getClaims($settlement, $year) {
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'c.settlement,concat(p.project_code,"-",p.name) as project,'
                . 'c.amount,c.date_of_approval,'
                . 'concat(a.first_name," ",a.middle_name," ",a.last_name) as approved_by,'
                . 'concat(cr.first_name," ",cr.middle_name," ",cr.last_name) as created_by,'
                . 'c.status,c.id,c.note');
        $this->db->from('claims c');
        $this->db->join('talents t', 'c.talent=t.id', 'left');
        $this->db->join('talents a', 'c.approved_by=a.id', 'left');
        $this->db->join('talents cr', 'c.created_by=cr.id', 'left');
        $this->db->join('project p', 'c.project=p.id', 'left');
        if ($settlement) {
            $this->db->where('c.settlement', $settlement);
        }
        if ($year) {
            $this->db->where('((YEAR(date_of_approval) = ' . $year . ' AND MONTH(date_of_approval) > 3) '
                    . 'OR (YEAR(date_of_approval) = ' . ($year + 1) . ' AND MONTH(date_of_approval) < 4)) ');
        }
        $this->db->order_by('c.date_of_approval', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getClaimsByResignedStatus($settlement, $year, $resigned, $status, $talent) {
        $this->db->select('concat(t.first_name," ",t.middle_name," ",t.last_name) as name,c.claim_id,'
                . 'c.settlement,concat(p.project_code,"-",p.name) as project,c.category,'
                . 'c.amount,c.date_of_approval,mc.value,'
                . 'concat(a.first_name," ",a.middle_name," ",a.last_name) as approved_by,'
                . 'concat(cr.first_name," ",cr.middle_name," ",cr.last_name) as created_by,'
                . 'c.status,c.id,c.note,tc.task_code');
        $this->db->from('claims c');
        $this->db->join('talents t', 'c.talent=t.id', 'left');
        $this->db->join('master_claim_category mc', 'c.category=mc.id', 'left');
        $this->db->join('talents a', 'c.approved_by=a.id', 'left');
        $this->db->join('talents cr', 'c.created_by=cr.id', 'left');
        $this->db->join('project p', 'c.project=p.id', 'left');
        $this->db->join('task_code tc', 'c.task_code=tc.id', 'left');
        if ($status) {
            if (1 == $status)
                $this->db->where('c.status', 0);
            if (2 == $status)
                $this->db->where('c.status', 1);
        }
        if ($talent != '') {
            $this->db->where('t.id', $talent);
        }
        if ($settlement) {
            $this->db->where('c.settlement', $settlement);
        }
        if ($year) {
            $this->db->where('((YEAR(date_of_approval) = ' . $year . ' AND MONTH(date_of_approval) > 3) '
                    . 'OR (YEAR(date_of_approval) = ' . ($year + 1) . ' AND MONTH(date_of_approval) < 4)) ');
        }
        $this->db->where('t.is_resigned', $resigned);
        $this->db->order_by('c.date_of_approval', 'desc');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getClaimById($id) {
        $this->db->select('c.*');
        $this->db->from('claims c');
        $this->db->where('c.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function getClaimCategory() {
        $this->db->select('c.*');
        $this->db->from('master_claim_category c');
        $query = $this->db->get();
        return $query->result_array();
    }

    function commaSeperated($amount) {
        setlocale(LC_MONETARY, 'en_IN');
        $amount = money_format('%!i', $amount);
        return $amount;
    }

    function getTransactions($year, $company, $project, $orderBy, $month) {
        $this->db->select('company_name,concat(p.project_code,"-",p.name) as project,'
                . 'c.credit_amount,c.debit_amount,c.date_of_approval,'
                . 'concat(cr.first_name," ",cr.middle_name," ",cr.last_name) as created_by,'
                . 'c.id,c.note,c.remarks,t.task_code,c.id as tranId');
        $this->db->from('transaction_tracking c');
        $this->db->join('company cy', 'c.company=cy.id', 'left');
        $this->db->join('talents cr', 'c.created_by=cr.id', 'left');
        $this->db->join('project p', 'c.project=p.id', 'left');
        $this->db->join('task_code t', 'c.task_code=t.id', 'left');
        if ($year) {
            $this->db->where('((YEAR(date_of_approval) = ' . $year . ' AND MONTH(date_of_approval) > 3) '
                    . 'OR (YEAR(date_of_approval) = ' . ($year + 1) . ' AND MONTH(date_of_approval) < 4)) ');
        }
        if ($company != '') {
            $this->db->where('c.company', $company);
        }
        if ($project != '') {
            $this->db->where('p.id', $project);
        }
        if ($month != '') {
            $this->db->where('MONTH(date_of_approval)', $month);
        }
        $this->db->order_by('c.date_of_approval', $orderBy);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTransactionsTemp() {
        $this->db->select('company_name,concat(p.project_code,"-",p.name) as project,'
                . 'c.credit_amount,c.debit_amount,c.date_of_approval,'
                . 'cr.username as created_by,'
                . 'c.id,c.note,c.remarks,t.task_code');
        $this->db->from('transaction_tracking_temp c');
        $this->db->join('company cy', 'c.company=cy.id', 'left');
        $this->db->join('users cr', 'c.created_by=cr.id', 'left');
        $this->db->join('project p', 'c.project=p.id', 'left');
        $this->db->join('task_code t', 'c.task_code=t.id', 'left');
        $this->db->order_by('c.date_of_approval', "asc");
        $query = $this->db->get();
        return $query->result_array();
    }

    function getCreditTranByCompanyYearStatus($year, $company, $project, $month) {
        $this->db->select('sum(c.credit_amount) as credit_sum');
        $this->db->from('transaction_tracking c');
        $this->db->join('company cy', 'c.company=cy.id', 'left');
        $this->db->join('talents cr', 'c.created_by=cr.id', 'left');
        $this->db->join('project p', 'c.project=p.id', 'left');
        if ($year) {
            $this->db->where('((YEAR(date_of_approval) = ' . $year . ' AND MONTH(date_of_approval) > 3) '
                    . 'OR (YEAR(date_of_approval) = ' . ($year + 1) . ' AND MONTH(date_of_approval) < 4)) ');
        }
        if ($company != '') {
            $this->db->where('c.company', $company);
        }
        if ($project != '') {
            $this->db->where('p.id', $project);
        }
        if ($month != '') {
            $this->db->where('MONTH(date_of_approval)', $month);
        }
        $this->db->order_by('c.date_of_approval', 'desc');
        $query = $this->db->get();
        $row = $query->row();
        return $row->credit_sum;
    }

    function getDebitTranByCompanyYearStatus($year, $company, $project, $month) {
        $this->db->select('sum(c.debit_amount) as debit_sum');
        $this->db->from('transaction_tracking c');
        $this->db->join('company cy', 'c.company=cy.id', 'left');
        $this->db->join('talents cr', 'c.created_by=cr.id', 'left');
        $this->db->join('project p', 'c.project=p.id', 'left');
        if ($year) {
            $this->db->where('((YEAR(date_of_approval) = ' . $year . ' AND MONTH(date_of_approval) > 3) '
                    . 'OR (YEAR(date_of_approval) = ' . ($year + 1) . ' AND MONTH(date_of_approval) < 4)) ');
        }
        if ($company != '') {
            $this->db->where('c.company', $company);
        }
        if ($project != '') {
            $this->db->where('p.id', $project);
        }
        if ($month != '') {
            $this->db->where('MONTH(date_of_approval)', $month);
        }
        $this->db->order_by('c.date_of_approval', 'desc');
        $query = $this->db->get();
        $row = $query->row();
        return $row->debit_sum;
    }

    function getTransById($id) {
        $this->db->select('c.*');
        $this->db->from('transaction_tracking c');
        $this->db->where('c.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function getTransTempById($id) {
        $this->db->select('c.*');
        $this->db->from('transaction_tracking_temp c');
        $this->db->where('c.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function getCompanyByCompanyName($companyName) {
        $this->db->select('c.*');
        $this->db->from('company c');
        $this->db->where('c.company_name', $companyName);
        $query = $this->db->get();
        return $query->row()->id;
    }

    function getAccountSettledClaims() {
        $this->db->select('c.id,c.claim_id,t.first_name');
        $this->db->from('claims c');
        $this->db->join('talents t', 'c.talent=t.id', 'left');
        $this->db->where('c.claim_id !=', '');
        $this->db->where('c.settlement =', 'Account');
        $this->db->where('c.status =', '1');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getClaimTranAmountFactored($claimId) {
        $this->db->select('sum(c.transaction_amount) as sum_amount');
        $this->db->from('transaction_claim_mapping c');
        $this->db->where('c.claim_id', $claimId);
        $query = $this->db->get();
        return $query->row()->sum_amount;
    }

    function getMappedClaimsToTransaction($transId) {
        $this->db->select('t.*,c.claim_id,c.id as claim,p.project_code');
        $this->db->from('transaction_claim_mapping t');
        $this->db->join('claims c', 't.claim_id=c.id', 'left');
        $this->db->join('project p', 'c.project=p.id', 'left');
        $this->db->where('t.trasaction', $transId);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getMappedClaimsToTransactionGroupConcat($transId) {
        $this->db->select('GROUP_CONCAT(c.claim_id SEPARATOR ",") as claimId');
        $this->db->from('transaction_claim_mapping t');
        $this->db->join('claims c', 't.claim_id=c.id', 'left');
        $this->db->join('project p', 'c.project=p.id', 'left');
        $this->db->where('t.trasaction', $transId);
        $query = $this->db->get();
        return $query->row()->claimId;
    }

    function getCountClaimMappedToTrans($claimId) {
        $this->db->select("id");
        $this->db->from('transaction_claim_mapping t');
        $this->db->where('t.claim_id', $claimId);
        $query = $this->db->get();
        return $query->num_rows();
    }

}
