<?php

defined('BASEPATH') or exit('No direct script access allowed');

class General_model extends CI_Model
{
    public $table_general_detail = "sma_kce_general_detail";

    public $table_general = "sma_kce_category_general";

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function index(){

    }

    public function get_tbl_general(){
        return $this->db->get($this->table_general);
    }

    public function add_general($param){
        $fields = array(
            'sma_kce_category_general_id'=> $param['category'],
            'title'                      => $param['title'],
            'description'                => $param['description'],
            'img_url'                    => $param['attachments'],
            'video_url'                  => $param['video_url'],
            'audio_url'                  => $param['audio_url'],
        );

        $this->db->insert($this->table_general_detail,$fields);
    }

    public function getGeneralByID($id)
    {
        $q = $this->db->get_where($this->table_general_detail, ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function update_general($id, $data = []){
        $this->db->where('id', $id);
        if ($this->db->update($this->table_general_detail, $data)) {
            return true;
        }
        return false;
    }
    public function deleteGeneral($id){
        if ($this->db->delete($this->table_general_detail, ['id' => $id])) { 
            return true;
        }
        return false;
    }
    public function insert_csv($data = []){
        if ($this->db->insert_batch($this->table_general_detail, $data)) {
            return true; 
        }
        return false;
    }
}