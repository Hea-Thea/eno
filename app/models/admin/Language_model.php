<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Language_model extends CI_Model
{
   
    public function __construct()
    {
        parent::__construct();
        
    }
    // insert data
    public function addLanguang($param){
        
            $field = array(
                'name'=>$param['name'],
            );
            //process insert
            $this->db->insert('kce_language', $field);
    }

    // delete data
    public function deleteLanguage($id=null) 
        {
            if ($this->db->delete('kce_language', ['id' => $id])) {
                return true;
            }
            return false;
        }

    // show data edit
    public function getLanguageByID($id)
    {
        $q = $this->db->get_where('kce_language', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
    // Update data
    public function updateLanguage($id,$data=[])
    {
        if ($this->db->update('kce_language', $data, ['id' => $id])) {
            return true;
        }
        return false;

    }
  
}
?>