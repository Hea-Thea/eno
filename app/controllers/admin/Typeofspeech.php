<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Typeofspeech extends MY_Controller
{
   function __construct()
   {
      parent::__construct();

      if (!$this->loggedIn) {
         $this->session->set_userdata('requested_page', $this->uri->uri_string());
         $this->sma->md('login');
      }
      if ($this->Customer || $this->Supplier) {
         $this->session->set_flashdata('warning', lang('access_denied'));
         redirect($_SERVER['HTTP_REFERER']);
      }
      $this->load->library('form_validation');
      $this->load->admin_model('speech_model');
   }
   // Add Part of speech
   public function add_part_of_speech()
   {
        $this->sma->checkPermissions(false, true);

        $data = [
            'name' => $this->input->post('partofspeech'),
        ];
        $this->session->set_flashdata('message', lang('Add_Part_of_speech success'));
        $this->speech_model->addspeech($data);
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        admin_redirect('typeofspeech/add');
   }


   function add($action = null)
   {
            if (!$this->Owner && !$this->GP['bulk_actions']) {
                $this->session->set_flashdata('warning', lang('access_denied'));
                redirect($_SERVER['HTTP_REFERER']);
            }
                $this->data['error']  = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
                $this->data['action'] = $action;
                $bc   = [['link' => base_url(), 'page' => lang('home')],['link' => '#', 'page' => lang('Type of speech')]];
                $meta = ['page_title' => lang('typeofspeech'), 'bc' => $bc];
                $this->page_construct('typeofspeech/index', $meta, $this->data);
   }
   function action_user()
   {
      $this->data['modal_js']        = $this->site->modal_js();
      $this->load->view($this->theme . 'typeofspeech/add', $this->data);
   }
   public function getspeech()
   {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select('id,name')
            ->from('kce_part_speech')
            ->add_column('Actions', 
            "<div class=\"text-center\" style=\"display:flex; gap:5px; justify-content:center; align-items:center;\">
                <a class=\"tip btn btn-primary btn-sm\"  style=\"border-radius: 6px !important;\" title='<b> " . lang('Edit_part_of_speech') . "</b>' href='" . admin_url('typeofspeech/edit/$1') . "' data-toggle='modal'  data-target='#myModal'>
                 <span class=\"badge btn-sm\"> <i class=\"fa fa-edit\"></i> </span>
                </a>

                 <a href='#' class='tip po btn btn-danger btn-sm' title='<b>".lang('Delete_part_of_speech')."</b>' style=\"border-radius: 6px !important;\" data-content=\"<p>" . lang('r_u_sure') . "</p>
                    <a class='btn btn-danger po-delete' href='" . admin_url('typeofspeech/delete/$1') . "'>" . lang('i_m_sure') . "</a> 
                
                    <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'>
                    <span class=\"badge btn-sm\"><i class=\"fa fa-trash-o\"></i> </span>
                </a>
            </div>", 'id');
        echo $this->datatables->generate();
   }
   //Modal_Edit open form 
   public function edit($id = null)
   {
          
            $partspeech_details = $this->speech_model->getPartOfSpeechByID($id);

            if ($this->input->post('speech_submit')) {
                $data = ['name' => $this->input->post('typeofspeech')];

                $this->speech_model->updateSpeech($id, $data);
                $this->session->set_flashdata('message', lang('Type_of_speech_updated'));
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->data['partspeech']      = $partspeech_details;
                $this->data['modal_js']        = $this->site->modal_js();
                $this->load->view($this->theme . 'typeofspeech/edit', $this->data);
            }
   }
   // Delete field  part of speech 
   public function delete($id = null)
   {
            $this->sma->checkPermissions(null, true);
            if ($this->input->get('id')) {
                $id = $this->input->get('id');
            }
            if (!$id) {
                $this->sma->send_json(['error' => 1, 'msg' => lang('id_not_found')]);
            }
            if ($this->speech_model->deleteSpeech($id)) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Part of speech_deleted successfully')]);
            } else {
                $this->sma->send_json(['error' => 1, 'msg' => lang('Part of speech_deleted_have_sales')]);
            }
 
   }
   // Delete  fields in checkbox

   public function delete_action()
   {
      if (!$this->Owner && !$this->GP['bulk_actions']) {
         $this->session->set_flashdata('warning', lang('access_denied'));
         redirect($_SERVER['HTTP_REFERER']);
     }

     $this->form_validation->set_rules('speech_action', lang('form_action'), 'required');

     if ($this->form_validation->run() == true) {
         if (!empty($_POST['val'])) {
             if ($this->input->post('speech_action') == 'delete') {
                 $this->sma->checkPermissions('delete');
                 $error = false;
                 foreach ($_POST['val'] as $id) {
                     if (!$this->speech_model->deleteSpeechAction($id)) {
                         $error = true;
                     }
                 }
                 if ($error) {
                     $this->session->set_flashdata('warning', lang('billers_x_deleted_have_sales'));
                 } else {
                     $this->session->set_flashdata('message', $this->lang->line('Type_of_speech_deleted'));
                 }
                 redirect($_SERVER['HTTP_REFERER']);
             }


             }
             else {
                $this->session->set_flashdata('error', $this->lang->line('no_type_of_speech_selected'));
                redirect($_SERVER['HTTP_REFERER']);
         }
     
   }
}
}
?>
