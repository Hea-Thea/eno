<?php
defined('BASEPATH') or exit('No direct script access allowed');

 class Speech_model extends CI_Model
 {
     public function __construct()
     {
         parent::__construct();
         $this->load->database();
         $this->load->admin_model('speech_model');
       
     }
    //  Insert data in table part of speech
     public function addspeech($data=[])
     {
        if ($this->db->insert('kce_part_speech', $data)) {
            return true;
        }
        return false;
     }
    //  Delete field Table  part of speech 
     public function deleteSpeech($id) 
     {
        if ($this->db->delete('kce_part_speech', ['id' => $id])) {
            return true;
        }
        return false;
        
 }

    //  Update field Table part of speech
    public function updateSpeech($id,$data= []) {
        $this->db->where('id', $id);
        if ($this->db->update('kce_part_speech', $data)) {
            return true;
        }
        return false;
    }
    // Get id form table part of speech
    public function getPartOfSpeechByID($id)
    {
        $q = $this->db->get_where('kce_part_speech', ['id' => $id]);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }
   // delete all field in table part of speech
   public function deleteSpeechAction($id)
   {
    if ($this->db->delete('kce_part_speech', ['id' => $id,])) {
        return true;
    }
    return false;

   }   



}
