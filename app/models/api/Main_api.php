<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Main_api extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function getLatestProducts()
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("{$this->db->dbprefix('products')}.id, {$this->db->dbprefix('products')}.code, {$this->db->dbprefix('products')}.name, {$this->db->dbprefix('products')}.type, {$this->db->dbprefix('products')}.slug, price, CONCAT('{$uploads_url}', {$this->db->dbprefix('products')}.image) as image_url, tax_method, tax_rate, unit");

        $this->db->order_by('id','asc');
        $this->db->limit(10);
        
        return $this->db->get('products')->result();
    }

    public function getPopularProducts()
    {
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("{$this->db->dbprefix('products')}.id, {$this->db->dbprefix('products')}.code, {$this->db->dbprefix('products')}.name, {$this->db->dbprefix('products')}.type, {$this->db->dbprefix('products')}.slug, price, CONCAT('{$uploads_url}', {$this->db->dbprefix('products')}.image) as image_url, tax_method, tax_rate, unit");

        $this->db->order_by('id','asc');
        $this->db->limit(10);
        
        return $this->db->get('products')->result();
    }

    public function getCategories()
    {
        $uploads_url = base_url('assets/uploads/');
         $this->db->select("{$this->db->dbprefix('categories')}.id, {$this->db->dbprefix('categories')}.code, {$this->db->dbprefix('categories')}.name, {$this->db->dbprefix('categories')}.slug, parent_id, CONCAT('{$uploads_url}', {$this->db->dbprefix('categories')}.image) as image_url");
        $this->db->where('parent_id', 0);
        $this->db->order_by('id','asc');
        $this->db->limit(10);
        
        return $this->db->get('categories')->result();
    }

    public function getBanners()
    {
        $uploads_url = base_url('assets/uploads/');
         $this->db->select("{$this->db->dbprefix('app_banners')}.id, {$this->db->dbprefix('app_banners')}.code, {$this->db->dbprefix('app_banners')}.name, CONCAT('{$uploads_url}', {$this->db->dbprefix('app_banners')}.image) as image_url");
        $this->db->order_by('id','asc');
        return $this->db->get('app_banners')->result();
    }

    public function registerCustomer($data)
    {
        $this->db->insert('companies', $data);
        if($this->db->affected_rows()){
            $response = array('message'=> lang('account_created'),'status'=> true);
            return $response;
        }else{
            $response = array('message'=> 'Register fail','status'=> false);
            return $response;
        }
    }































































    // kce_dictionary


    public function get_general($category_id){
        $this->db->select ('kce_general_detail.id as id,
                            kce_category_general.id as category_general_id,
                            kce_category_general.name as category_general_name,
                            title,
                            description,
                            img_url,
                            video_url,
                            audio_url');
        $this->db->from('kce_general_detail');
        $this->db->where('kce_general_detail.sma_kce_category_general_id',$category_id);
        $this->db->join('kce_category_general',
                        'kce_general_detail.sma_kce_category_general_id = kce_category_general.id',
                        'inner');
        $query = $this->db->get(); 

        if($query->num_rows() != 0)
        {
            return $query;
        }
        else
        {
            return false;
        }
    }
    
}
