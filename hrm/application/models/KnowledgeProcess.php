<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of KnowledgeProcess
 *
 */
class KnowledgeProcess extends CI_Model {
    //put your code here
    
    
    function getKnowledgeforVerification($talent_id) {
            $this->db->select('kr.id,kr.heading,kr.description,kr.file_name,
               kr.verified_by,kr.updated_by,kr.assigned_to,kr.created_on,kr.verified,kr.note,kr.created_by,modified_on,'
                . 'concat(ta.first_name," ",ta.middle_name," ",ta.last_name) as updated_by,'
                     . 'concat(tb.first_name," ",tb.middle_name," ",tb.last_name) as verified_by,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
       );
        $this->db->join('talents t','t.id=kr.created_by','left');
        $this->db->join('talents ta','ta.id=kr.assigned_to','left');
        $this->db->join('talents tb','tb.id=kr.verified_by','left');
        $this->db->from('knowledge_repo kr');
      //  $this->db->where('kr.verified',0);
          //$where = '(kr.assigned_to='.$talent_id.' OR kr.verified_by = '.$talent_id.')';
        //$this->db->where($where);
       $this->db->where('kr.assigned_to',$talent_id );
       $this->db->or_where('kr.verified_by',$talent_id);
       //$this->db->distinct();
        $query = $this->db->get();
        return $query->result_array();
    }
   
     function getTalentsKnowledgeUnderReportingManager($talent){
         /*
            $this->db->select('kr.id,kr.heading,kr.description,kr.file_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'kr.verified_by,kr.created_on,kr.verified,kr.note,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as verifier_name');
        $this->db->join('talents t','t.id=kr.created_by','left');
        
        $this->db->from('knowledge_repo kr');
          
          */
         $this->db->select('kr.id,kr.heading,kr.description,kr.file_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'kr.verified_by,kr.updated_by,kr.assigned_to,kr.created_on,kr.verified,kr.note,kr.created_by,modified_on,'
                . 'concat(ta.first_name," ",ta.middle_name," ",ta.last_name) as updated_by,'
          . 'concat(tb.first_name," ",tb.middle_name," ",tb.last_name) as verified_by,'
          . 'concat(tc.first_name," ",tc.middle_name," ",tc.last_name) as assigned_to,');
        $this->db->join('talents t','t.id=kr.created_by','left');
        $this->db->join('talents ta','ta.id=kr.updated_by','left');
        $this->db->join('talents tb','tb.id=kr.verified_by','left');
        $this->db->join('talents tc','tc.id=kr.assigned_to','left');
        $this->db->from('knowledge_repo kr');
        $this->db->where('t.reporting_manager',$talent);
        $this->db->order_by('kr.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
        
    }
    function getAllTalentKnowledge()
    {
        

    $this->db->select('kr.id,kr.heading,kr.description,kr.file_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'kr.verified_by,kr.updated_by,kr.assigned_to,kr.created_on,kr.verified,kr.note,kr.created_by,modified_on,'
                . 'concat(ta.first_name," ",ta.middle_name," ",ta.last_name) as updated_by,'
          . 'concat(tb.first_name," ",tb.middle_name," ",tb.last_name) as verified_by,'
          . 'concat(tc.first_name," ",tc.middle_name," ",tc.last_name) as assigned_to,');
        $this->db->join('talents t','t.id=kr.created_by','left');
        $this->db->join('talents ta','ta.id=kr.updated_by','left');
        $this->db->join('talents tb','tb.id=kr.verified_by','left');
        $this->db->join('talents tc','tc.id=kr.assigned_to','left');
        $this->db->from('knowledge_repo kr');
        $this->db->order_by('kr.id', 'desc');
        $query = $this->db->get();
        return $query->result_array();
        
    }
}
  
