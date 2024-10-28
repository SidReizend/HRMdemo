<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Tag
 *
 * @author sibin_francis
 */
class Tag extends CI_Model  {
    
    public function viewAllTag(){
        $this->db->select('t.id,t.tag_name');
        $this->db->from('tag t');
        
        $query = $this->db->get();
        return $query->result_array();
    }
    
    function getSelectedTagByKnowledgeId($id) {
        $this->db->select('m.tag_name,m.id');
        $this->db->from('knowledge_repo_tags r');
        $this->db->Join('tag m','m.id=r.tag','left');
        $this->db->where('r.knowledge_repo', $id);
        $query = $this->db->get();
        return $query->result_array();
    }
    
}
