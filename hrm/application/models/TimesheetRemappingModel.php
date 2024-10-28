<?php

Class TimesheetRemappingModel extends CI_Model {
    /*
     * controller:TimesheetRemapping
     */

    function gettimesheetRemaps() {
        $this->db->select('tpm.*,pp.project_code as previous_project_code, pp.name as previous_project, '
                . 'ap.project_code as actual_project_code, ap.name as actual_project , ts.task_code, tgt.task_code as target_task_code, '
                . 'ts.task_desc, t.first_name as created_by, ta.first_name as talent_name');
        $this->db->from('timesheet_project_remapping tpm');
        $this->db->join('project pp ', 'pp.id=tpm.previous_project', 'inner');
        $this->db->join('project ap ', 'ap.id=tpm.actual_project', 'inner');
        $this->db->join('task_code ts ', 'ts.id=tpm.task_code', 'inner');
        $this->db->join('task_code tgt ', 'tgt.id=tpm.target_task_code', 'left');
        $this->db->join('talents t ', 't.id=tpm.created_by', 'left');
        $this->db->join('talents ta ', 'tpm.talents=ta.id', 'left');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*
     * controller:TimesheetRemapping
     */

    function updateTimePrediction($selectedactualProject, $selectedProject, $selectedTask, $fromdate, $todate, $talent, $targetTask) {
        $this->db->set('Project', $selectedactualProject);
        $this->db->set('task', $targetTask);
        $this->db->where('Project', $selectedProject);
        $this->db->where('task', $selectedTask);
        if ($talent) {
            $this->db->where('talent', $talent);
        }
        if ($fromdate) {
            $this->db->where("date>=", $fromdate);
        }
        if ($todate) {
            $this->db->where("date<=", $todate);
        }
        $queryResult = $this->db->update('time_prediction');
        return $queryResult;
    }

}
