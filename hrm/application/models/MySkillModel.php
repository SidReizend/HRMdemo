<?php

Class MySkillModel extends CI_Model {

    function getAllSkills($userSkills = '') {
        $this->db->select('*');
        $this->db->from('skills');
        if (!empty($userSkills)) {
            $this->db->where_not_in('id', $userSkills);
        }
        $this->db->where('is_approved', 1);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getUserSkills($talentId = '') {
        $this->db->select('*,ms.id as mySkillId');
        $this->db->from('my_skills as ms');
        $this->db->join('skills as s', 'ms.skills = s.id', 'left');
        $this->db->where('user_id', $talentId);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getApprovedSkills($skill = '') {
        $this->db->select('*');
        $this->db->from('skills');
        $this->db->where('is_approved', 1);
        $this->db->where('skill_name', $skill);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getNotApprovedSkills($skillName = '', $talentId = '') {
        $this->db->select('*');
        $this->db->from('skills as s');
        $this->db->join('my_skills as ms', 's.id = ms.skills', 'inner');
        $this->db->where('s.is_approved', 0);
        $this->db->where('ms.user_id', $talentId);
        $this->db->where('s.skill_name', $skillName);
        $query = $this->db->get();
        return $query->result_array();
    }

    function getAllUserSkills($approved = '') {
        $this->db->select('*, s.id as skillId,ms.id as mySkillId');
        $this->db->from('my_skills as ms');
        $this->db->join('skills as s', 'ms.skills = s.id', 'left');
        $this->db->join('talents as t', 'ms.user_id = t.user', 'left');
        $this->db->where('s.is_approved', $approved);
        $query = $this->db->get();
        return $query->result_array();
    }

    function deleteSkill($skillId = '') {
        $this->db->where('id', $skillId);
        $this->db->delete('my_skills');
        return TRUE;
    }

    function deleteUserUnApprovedSkills($skillId = '') {
        $this->db->where('id', $skillId);
        $this->db->delete('skills');
        return TRUE;
    }

    function getNotApprovedSkillsById($skillId = '', $talentId = '') {
        $this->db->select('*');
        $this->db->from('skills as s');
        $this->db->join('my_skills as ms', 's.id = ms.skills', 'inner');
        $this->db->where('s.is_approved', 0);
        $this->db->where('ms.user_id', $talentId);
        $this->db->where('ms.id', $skillId);
        $query = $this->db->get();
        return $query->row_array();
    }

    function approveSkills($skillId = '', $updateData = '') {
        $this->db->where('id', $skillId);
        $this->db->update('skills', $updateData);
        return TRUE;
    }

    function getLikeSkills($userSkills = '') {
        $this->db->select('*');
        $this->db->from('skills');
        $this->db->where('is_approved', 1);
        $this->db->where_not_in('id', explode(',', $userSkills));
        $query = $this->db->get();
        return $query->result_array();
    }

    function updateSkill($replaceSkill = '', $updateData = '') {
        $this->db->where('skills', $replaceSkill);
        $this->db->update('my_skills', $updateData);
        return TRUE;
    }

    function getAllApprovedUserId($skill = '', $yearOfExperienceStart = '', $yearOfExperienceEnd = '', $approved = '') {
        $this->db->select('GROUP_CONCAT(DISTINCT ms.user_id) as userId');
        $this->db->from('my_skills as ms');
        $this->db->join('skills as s', 'ms.skills = s.id', 'left');
        if (!empty($skill) && !empty($yearOfExperienceStart) && !empty($yearOfExperienceEnd)) {
            $this->db->where('ms.skills', $skill);
            $this->db->where("ms.year_of_experience BETWEEN  $yearOfExperienceStart AND $yearOfExperienceEnd ");
        } else if (!empty($skill)) {
            $this->db->where('ms.skills', $skill);
        }
        $this->db->where('s.is_approved', $approved);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getAllApprovedUserSkills($userId = '') {
        $this->db->select('s.*,ms.*,t.first_name,t.middle_name,t.last_name,s.id as skillId,GROUP_CONCAT(s.skill_name) as skillName, GROUP_CONCAT(ms.year_of_experience) as yearExp, GROUP_CONCAT(ms.skill_level) as skillLevel');
        $this->db->from('my_skills as ms');
        $this->db->join('skills as s', 'ms.skills = s.id', 'left');
        $this->db->join('talents as t', 'ms.user_id = t.user', 'left');
        $this->db->where_in('ms.user_id', explode(',', $userId));
        $this->db->group_by('ms.user_id');
        $query = $this->db->get();
        return $query->result_array();
    }

    function getSkillName($skill = '') {
        $this->db->select('skill_name');
        $this->db->from('skills');
        $this->db->where('id', $skill);
        $query = $this->db->get();
        return $query->row_array();
    }

    function getSkillByUserId($useId = '') {
        $this->db->select('GROUP_CONCAT(skills) as skills');
        $this->db->from('my_skills');
        $this->db->where('user_id', $useId);
        $query = $this->db->get();
        return $query->row_array();
    }

}
