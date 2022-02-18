<?php
 defined('BASEPATH') or exit ('No direct script access allowed');

 class Word_model extends CI_Model
 {
     public $table ='kce_category_dictionary';
     function __construct()
     {
         parent::__construct();
         $this->load->database();
         $this->load->admin_model('word_model');
     }

    //  Insert into database

    public function addWordCategory($data= [])
    {
        if ($this->db->insert($this->table, $data)) {
            return true;
        }
        return false;
    }
    // Delete field Table Word category
    public function deleteWordCategory($id=null)
    {
        if ($this->db->delete($this->table, ['id' => $id])) {
            return true;
        }
        return false;
    }
    //Edit Word Category
    public function updateWord($id,$data= [])
    {
        $this->db->where('id', $id);
        if ($this->db->update($this->table, $data)) {
            return true;
        }
        return false;
    } 
    // Get ID from table word category
    public function getwordCategoryByID($id)
    {
        $q = $this->db->get_where($this->table, ['id' => $id]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    // Delete word Category via Checkbox
    public function deleteWordAction($id)
    {
        if ($this->db->delete($this->table, ['id' => $id,])) {
            return true;
        }
        return false;
    }
 }

?>