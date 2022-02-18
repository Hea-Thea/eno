<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Languages extends MY_Controller
{
    public function __construct()
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
      $this->load->admin_model('language_model');
        
    }

    public function view($action=[]){
        $this->data['action'] = $action;
        $bc           = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('language')] ];
        $meta         = ['page_title' => lang('language'), 'bc' => $bc];
        $this->page_construct('languages/index', $meta, $this->data);
    }

    // show form add language
    public function add(){
        $this->data['modal_js']  = $this->site->modal_js();
        $this->load->view($this->theme. "languages/add");
        // $name = $this->input->post('language');
        // $param['name'] = $name;
        // $this->language_model->insertLanguang($param);
        // $bc   = [['link' => base_url(), 'page' => lang('home')],  ['link' => '#', 'page' => lang('')]];
        // $meta = ['page_title' => lang('language'), 'bc' => $bc];
        // $this->page_construct('languages/add', $meta, $this->data);
    }
    // add language
    public function add_language($action = null){
        $this->sma->checkPermissions(false, true);
        $this->data['error']  = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $this->data['action'] = $action;

        $name = $this->input->post('language');
        $param['name'] = $name;
         $this->session->set_flashdata('message', lang('language added successfully'));
        $this->language_model->addLanguang($param);
          admin_redirect('languages/view');
    //     $bc   = [['link' => base_url(), 'page' => lang('home')],  ['link' => '#', 'page' => lang('')]];
    //     $meta = ['page_title' => lang('language'), 'bc' => $bc];
    //     // $this->page_construct('languages/index', $meta, $this->data);
     }

    // show database
    public function getLanguage()
    {
        
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
        ->select('id,name')
        ->from('kce_language')
        // show field data (not use model)
        ->add_column('Actions', 
            "<div class=\"text-center\" style=\"display:flex; gap:5px; justify-content:center; align-items:center;\">
                <a class=\"tip btn btn-primary btn-sm\"  style=\"border-radius: 6px !important;\" title='<b> " . lang('edit_language') . "</b>' href='" . admin_url('languages/edit/$1') . "' data-toggle='modal'  data-target='#myModal'>
                 <span class=\"badge btn-sm\"> <i class=\"fa fa-edit\"></i> </span>
                </a>

                 <a href='#' class='tip po btn btn-danger btn-sm' title='<b>".lang('delete_language')."</b>' style=\"border-radius: 6px !important;\" data-content=\"<p>" . lang('r_u_sure') . "</p>
                    <a class='btn btn-danger po-delete' href='" . admin_url('languages/delete/$1') . "'>" . lang('i_m_sure') . "</a> 
                
                    <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'>
                    <span class=\"badge btn-sm\"><i class=\"fa fa-trash-o\"></i> </span>
                </a>
            </div>", 'id');
        echo $this->datatables->generate();
    }

    //language delete  check box
    public function language_actions()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
         $this->session->set_flashdata('warning', lang('access_denied'));
         redirect($_SERVER['HTTP_REFERER']);
        }

        $this->form_validation->set_rules('form_action', lang('form_action'), 'required');

        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('form_action') == 'delete') {
                    $this->sma->checkPermissions('delete');
                    $error = false;
                    foreach ($_POST['val'] as $id) {
                        if (!$this->language_model->deleteLanguage($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('language_x_deleted'));
                    } else {
                        $this->session->set_flashdata('message', $this->lang->line('language_deleted_successfully'));
                    }
                    redirect($_SERVER['HTTP_REFERER']);
            }  
        } 
            else {
                $this->session->set_flashdata('error', lang('no_language_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
   }

    // Delete field  language on actione
    public function delete($id= null)
    {
        $this->sma->checkPermissions(null, true);
        if ($this->input->get('id')) {
            $id = $this->input->get('id');    
        }
        if (!$id) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('id_not_found')]);      
        }

        // if ($this->input->get('id') == 1 || $id == 1) {
        //     $this->sma->send_json(['error' => 1, 'msg' => lang('language_deleted')]);
        // }

        if ($this->language_model->deleteLanguage($id)) { 
            $this->sma->send_json(['error' => 0, 'msg' => lang('language_deleted successfully')]); 
        } else {
            $this->sma->send_json(['error' => 1, 'msg' => lang('language_deleted_have_sales')]);
        }
    }

    // edit and update data
    public function edit($id = null){
        $this->sma->checkPermissions(false, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
    
        $language_detail = $this->language_model->getLanguageByID($id);
        if($this->input->post('update_language')){
            $data = [
                        'name' => $this->input->post('typelanguage')
                    ];
            $this->language_model->updateLanguage($id, $data);
            $this->session->set_flashdata('message', lang('language_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        else{
            $this->data['language']      = $language_detail;
            $this->data['modal_js']        = $this->site->modal_js();
            $this->load->view($this->theme . 'languages/edit', $this->data);
        }
    
    }

}
?>