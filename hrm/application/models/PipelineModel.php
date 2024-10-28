<?php

Class PipelineModel extends CI_Model {

    function getAllProjects() {
        $this->db->select('pipe_line.*,project_type.project_type');
        $this->db->join('project_type', 'pipe_line.project_type=project_type.id', 'left');
        $this->db->from('pipe_line');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProjectData($id) {
        $this->db->select('pipe_line.*,project_type.project_type,project_type.id as project_id');
        $this->db->from('pipe_line');
        $this->db->join('project_type', 'pipe_line.project_type=project_type.id', 'left');
        $this->db->where('pipe_line.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

}
