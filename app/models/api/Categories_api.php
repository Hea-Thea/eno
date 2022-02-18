<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Categories_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function countCategories($filters = [])
    {
    	if ($filters['parent_id']) {
            $this->db->where('parent_id', $filters['parent_id']);
        }else{
        	$this->db->where('parent_id', 0);
        }
        
        $this->db->from('categories');
        return $this->db->count_all_results();
    }

    public function getCategoryByCode($code)
    {
        return $this->db->get_where('categories', ['code' => $code], 1)->row();
    }

    public function getCategoryByID($id)
    {
        return $this->db->get_where('categories', ['id' => $id], 1)->row();
    }

    public function getCategory($filters)
    {
        if (!empty($categories = $this->getCategories($filters))) {
            return array_values($categories)[0];
        }
        return false;
    }


    public function getCategories($filters = [])
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("{$this->db->dbprefix('categories')}.id, {$this->db->dbprefix('categories')}.code, {$this->db->dbprefix('categories')}.name, {$this->db->dbprefix('categories')}.slug, parent_id, CONCAT('{$uploads_url}', {$this->db->dbprefix('categories')}.image) as image_url");


        if ($filters['parent_id']) {
            $this->db->where('parent_id', $filters['parent_id']);
        }else{
        	$this->db->where('parent_id', 0);
        }
        
        if ($filters['code']) {
            $this->db->where('code', $filters['code']);
        } else {
            $this->db->order_by($filters['order_by'][0], $filters['order_by'][1] ? $filters['order_by'][1] : 'asc');
            $this->db->limit($filters['limit'], ($filters['start'] - 1));
        }

        return $this->db->get('categories')->result();
    }

   
}
