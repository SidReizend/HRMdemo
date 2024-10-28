<?php

Class ProjectTypeModel extends CI_Model {

    public function getAllProjectTypes() {
        $this->db->select('project_type.*,schedule.schedule');
        $this->db->join('schedule', 'project_type.schedule_id=schedule.id', 'left');
        $this->db->from('project_type');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProjectTypeData($id) {
        $this->db->select('project_type.*');
        $this->db->from('project_type');
        $this->db->where('project_type.id', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProjectDashboardData($scheduleId = 0) {
        $this->db->select('project_type.*,sum(pipe_line.value) as totalProposed,sum(pipe_line.approved_value) as totalApproved');
        $this->db->join('project_type', 'project_type.id = pipe_line.project_type', 'left');
        $this->db->from('pipe_line');
        $this->db->where('project_type.schedule_id', $scheduleId);
        $this->db->group_by('project_type.id');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProjectDashboard($scheduleId = 0) {
        $this->db->select('sum(project_type.targeted_value) as totalTarget ,sum(pipe_line.value) as totalProposed,sum(pipe_line.approved_value) as totalApproved');
        $this->db->join('project_type', 'project_type.id = pipe_line.project_type', 'left');
        $this->db->from('pipe_line');
        $this->db->where('project_type.schedule_id', $scheduleId);
        $this->db->group_by('project_type.organization_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProjectTotalTarget($scheduleId = 0) {
        $this->db->select('sum(project_type.targeted_value) as totalTarget');
        $this->db->from('project_type');
        $this->db->where('project_type.schedule_id', $scheduleId);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getProjectTotalApproved($scheduleId = 0) {
        $this->db->select('sum(pipe_line.approved_value) as totalApproved');
        $this->db->join('project_type', 'project_type.id = pipe_line.project_type', 'left');
        $this->db->from('pipe_line');
        $this->db->where('project_type.schedule_id', $scheduleId);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getProposedById($id) {
        $this->db->select('*');
        $this->db->from('pipe_line');
        $this->db->where('pipe_line.project_type', $id);
        $this->db->where('pipe_line.stage', 'Proposed');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getApprovedById($id) {
        $this->db->select('*');
        $this->db->from('pipe_line');
        $this->db->where('pipe_line.project_type', $id);
        $this->db->where('pipe_line.stage', 'Approved');
        $query = $this->db->get();
        return $query->result_array();
    }

}
