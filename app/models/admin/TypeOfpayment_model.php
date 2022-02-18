<?php

defined('BASEPATH') or exit('No direct script access allowed');

class TypeOfpayment_model extends CI_Model
{
    public $table = "sma_kce_type_payment";

    public function __construct()
    {
        parent::__construct();
    }
    public function index(){
        return $this->db->get($this->table);
    }
    public function insert($param)
    {
        $field = array(
            'name'=>$param['name']
        );  
        $this->db->insert($this->table, $field);
    }

    public function deletetypeofpayment($id){
        if ($this->db->delete($this->table, ['id' => $id])) { 
            return true;
        }
        return false;
    }
    public function getTypeofpaymentByID($id)
    {
        $q = $this->db->get_where($this->table, ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    public function updatetypeofpayment($id, $data = []){
        $this->db->where('id', $id);
        if ($this->db->update($this->table, $data)) {
            return true;
        }
        return false;
    }
}   