<?php

Class KnowledgeRepository extends CI_Model {

    function getKnowledgeRepo($roleStream, $perPage, $page, $search, $tag) {


        //find the tagged knowledge repo


        if (!empty($tag)) {
            $this->db->select('knowledge_repo');
            $this->db->from('knowledge_repo_tags');
            $this->db->where_in('tag', $tag);
            $queryTag = $this->db->get();
            $tag_arr = $queryTag->result_array();

            $knowledgeRepo = array();
            foreach ($tag_arr as $knowledgeRepos) {
                $knowledgeRepo[] = $knowledgeRepos['knowledge_repo'];
            }
        }


        $start = ($page - 1) * $perPage;
        $this->db->select('kr.heading,kr.id,kr.description,kr.created_on,concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'kr.file_name,');
        $this->db->from('knowledge_repo kr');
        $this->db->join('knowledge_repo_stream krs', 'krs.knowledge_repo=kr.id', 'left');
        $this->db->join('talents t', 'kr.created_by=t.id', 'left');
        $this->db->where('krs.role_stream', $roleStream);
        $this->db->where('kr.verified', 1);
        if (!empty($tag) && !empty($knowledgeRepo)) {
            $this->db->where_in('kr.id', $knowledgeRepo);
        }
        $this->db->where("(kr.heading LIKE '%$search%' OR kr.description LIKE '%$search%')");
        $this->db->limit($perPage, $page);
        $this->db->order_by('kr.created_on DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getKnowledgeCount($roleStream) {
        $this->db->select('kr.heading,kr.description,kr.file_name,');
        $this->db->from('knowledge_repo kr');
        $this->db->join('knowledge_repo_stream krs', 'krs.knowledge_repo=kr.id', 'left');
        $this->db->where('krs.role_stream', $roleStream);
        $this->db->where('kr.verified', 1);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getMyKnowledgeCount($talent) {
        $this->db->select('kr.id');
        $this->db->from('knowledge_repo kr');
        $this->db->where('kr.created_by', $talent);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getMyApprovedKnowledgeCount($talent) {
        $this->db->select('kr.id');
        $this->db->from('knowledge_repo kr');
        $this->db->where('kr.created_by', $talent);
        $this->db->where('kr.verified', 1);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function lastKshop($roleStream) {
        $this->db->select('kr.heading,kr.description,kr.file_name,');
        $this->db->from('knowledge_repo kr');
        $this->db->join('knowledge_repo_stream krs', 'krs.knowledge_repo=kr.id', 'left');
        $this->db->where('krs.role_stream', $roleStream);
        $this->db->where('kr.verified', 1);
        $this->db->order_by('kr.id DESC');
        $this->db->limit('1');
        $query = $this->db->get();
        return $query->row();
    }

    function getKnowledgeCountSearch($roleStream, $search, $tag) {
        //find the tagged knowledge repo


        if (!empty($tag)) {
            $this->db->select('knowledge_repo');
            $this->db->from('knowledge_repo_tags');
            $this->db->where_in('tag', $tag);
            $queryTag = $this->db->get();
            $tag_arr = $queryTag->result_array();

            $knowledgeRepo = array();
            foreach ($tag_arr as $knowledgeRepos) {
                $knowledgeRepo[] = $knowledgeRepos['knowledge_repo'];
            }
        }

        $this->db->select('kr.heading,kr.description,kr.file_name,');
        $this->db->from('knowledge_repo kr');
        $this->db->join('knowledge_repo_stream krs', 'krs.knowledge_repo=kr.id', 'left');
        $this->db->where('krs.role_stream', $roleStream);
        $this->db->where('kr.verified', 1);
        if (!empty($tag) && !empty($knowledgeRepo)) {
            $this->db->where_in('kr.id', $knowledgeRepo);
        }
        $this->db->where("(kr.heading LIKE '%$search%' OR kr.description LIKE '%$search%')");
        $query = $this->db->get();
        return $query->num_rows();
    }

    function getKnowledgeRepository() {
        $this->db->select('kr.id,kr.heading,kr.description,kr.file_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'kr.verified_by,kr.created_on,kr.verified,kr.note,'
                . 'concat(ta.first_name," ",ta.middle_name," ",ta.last_name) as verifier_name');
        $this->db->join('talents t', 't.id=kr.created_by', 'left');
        $this->db->join('talents ta', 'ta.id=kr.verified_by', 'left');
        $this->db->from('knowledge_repo kr');
        $this->db->order_by('id DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getKnowledgeforVerification() {
        $this->db->select('kr.id,kr.heading,kr.description,kr.file_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'kr.verified_by,kr.created_on,kr.verified,kr.note,'
                . 'concat(ta.first_name," ",ta.middle_name," ",ta.last_name) as verifier_name');
        $this->db->join('talents t', 't.id=kr.created_by', 'left');
        $this->db->join('talents ta', 'ta.id=kr.verified_by', 'left');
        $this->db->from('knowledge_repo kr');
        $this->db->where('kr.verified', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getKnowledgeById($id) {
        $this->db->select('kr.id,kr.heading,kr.description,kr.file_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'kr.verified_by,kr.updated_by,kr.assigned_to,kr.created_on,kr.verified,kr.note,kr.created_by,modified_on,'
                . 'concat(ta.first_name," ",ta.middle_name," ",ta.last_name) as updated_by,'
                . 'concat(tb.first_name," ",tb.middle_name," ",tb.last_name) as verified_by,'
        );
        $this->db->join('talents t', 't.id=kr.created_by', 'left');
        $this->db->join('talents ta', 'ta.id=kr.updated_by', 'left');
        $this->db->join('talents tb', 'tb.id=kr.verified_by', 'left');
        $this->db->join('talents tc', 'tc.id=kr.assigned_to', 'left');
        $this->db->from('knowledge_repo kr');
        $this->db->where('kr.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getKnowledgeByTalent($talent_id) {
        $this->db->select('kr.id,kr.heading,kr.description,kr.file_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'kr.verified_by,kr.updated_by,kr.assigned_to,kr.created_on,kr.verified,kr.note,kr.created_by,modified_on,'
                . 'concat(ta.first_name," ",ta.middle_name," ",ta.last_name) as updated_by,'
                . 'concat(tb.first_name," ",tb.middle_name," ",tb.last_name) as verified_by,'
                . 'concat(tc.first_name," ",tc.middle_name," ",tc.last_name) as assigned_to,');
        $this->db->join('talents t', 't.id=kr.created_by', 'left');
        $this->db->join('talents ta', 'ta.id=kr.updated_by', 'left');
        $this->db->join('talents tb', 'tb.id=kr.verified_by', 'left');
        $this->db->join('talents tc', 'tc.id=kr.assigned_to', 'left');
        $this->db->from('knowledge_repo kr');
        $this->db->where('t.id', $talent_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getKnowledgeDetailsById($id) {
        $this->db->select('kr.id,kr.heading,kr.description,kr.file_name,'
                . 'concat(t.first_name," ",t.middle_name," ",t.last_name) as name,'
                . 'kr.verified_by,kr.created_on,kr.verified,kr.note,kr.created_by,'
                . 'concat(ta.first_name," ",ta.middle_name," ",ta.last_name) as verifier_name');
        $this->db->join('talents t', 't.id=kr.created_by', 'left');
        $this->db->join('talents ta', 'ta.id=kr.verified_by', 'left');
        $this->db->from('knowledge_repo kr');
        $this->db->where('kr.id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function getSelectedRoleStreamByKnowledgeId($id) {
        $this->db->select('m.role_stream,m.id');
        $this->db->from('knowledge_repo_stream r');
        $this->db->Join('role_stream m', 'm.id=r.role_stream', 'left');
        $this->db->where('r.knowledge_repo', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function tags() {
        $this->db->select('*');
        $this->db->from('tag');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getTagId($id) {
        $this->db->select('*');
        $this->db->from('tag');
        $this->db->where('id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function reduceLength($text, $variable) {
        $text = strip_tags($text);
        if (strlen($text) >= $variable) {
            return substr($text, 0, ($variable - 1)) . '...';
        } else {
            return $text;
        }
    }

    function getKnowledgePortals($startDate, $endDate, $talent,$status) {
        $this->db->select('k.*,concat(t.first_name," ",t.middle_name," ",t.last_name) as name');
        $this->db->from('knowledge_repo k');
        $this->db->join('talents t', 't.id=k.created_by', 'left');
        $this->db->where("k.created_on >=", $startDate);
        $this->db->where("k.created_on <=", $endDate);
        if ($talent) {
            $this->db->where("k.created_by", $talent);
        }
        if ($status) {
            if($status == 1)
            $this->db->where("k.verified", 0);
            if($status == 2)
            $this->db->where("k.verified", 1);
        }
        $this->db->order_by("k.created_on ASC");
        $query = $this->db->get();
        return $query->result_array();
    }

}
