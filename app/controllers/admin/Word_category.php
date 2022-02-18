<?php 
  defined('BASEPATH') or exit('No direct script access allowed');

  class Word_category extends MY_Controller
  {
      function __construct()
      {
          parent::__construct();
          if (!$this->loggedIn) {
            $this->session->set_userdata('requested_page', $this->uri->uri_string());
            $this->sma->md('login');
         }

         $this->load->library('form_validation');
         $this->load->admin_model('word_model');
      }
    //   Show Datatable

      public function index($action=null)
      {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }
            $this->data['error']  = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
            $this->data['action'] = $action;
            $bc   = [['link' => base_url(), 'page' => lang('home')],['link' => '#', 'page' => lang('Word Category')]];
            $meta = ['page_title' => lang('Word Category'), 'bc' => $bc];
            $this->page_construct('word_category/index', $meta, $this->data);
      }
    //   Add Datatable in word category 
      
      public function addword()
      {
        $this->sma->checkPermissions(false, true);

        $data = [
            'name' => $this->input->post('word'),
        ];
        $this->session->set_flashdata('message', lang('Add_Part_of_speech successfully'));
        $this->word_model->addWordCategory($data);
        $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
        admin_redirect('word_category/index');
      }
     
    //   Select Datatable in database

    public function getAllWord()
    {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select('id,name')
            ->from('kce_category_dictionary')
            ->add_column('Actions', 
            "<div class=\"text-center\" style=\"display:flex; gap:5px; justify-content:center; align-items:center;\">
                <a class=\"tip btn btn-primary btn-sm\"  style=\"border-radius: 6px !important;\" title='<b> " . lang('edit_word_category') . "</b>' href='" . admin_url('word_category/editWord/$1') . "' data-toggle='modal'  data-target='#myModal'>
                 <span class=\"badge btn-sm\"> <i class=\"fa fa-edit\"></i> </span>
                </a>

                 <a href='#' class='tip po btn btn-danger btn-sm' title='<b>".lang('delete_word_category')."</b>' style=\"border-radius: 6px !important;\" data-content=\"<p>" . lang('r_u_sure') . "</p>
                    <a class='btn btn-danger po-delete' href='" . admin_url('word_category/deleteWord/$1') . "'>" . lang('i_m_sure') . "</a> 
                
                    <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'>
                    <span class=\"badge btn-sm\"><i class=\"fa fa-trash-o\"></i> </span>
                </a>
            </div>", 'id');
        echo $this->datatables->generate();
    }

    // Modal form word Category
    
    public function action_addWord()
    {
        $this->data['modal_js']        = $this->site->modal_js();
        $this->load->view($this->theme . 'word_category/addword', $this->data);
    }
    //   Delete datatable

    public function deleteWord($id=null)
    {
        $this->sma->checkPermissions(null, true);
            if ($this->input->get('id')) {
                $id = $this->input->get('id');
            }
            if (!$id) {
                $this->sma->send_json(['error' => 1, 'msg' => lang('id_not_found')]);
            }
            if ($this->word_model->deleteWordCategory($id)) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('Word_deleted successfully')]);
            } else {
                $this->sma->send_json(['error' => 1, 'msg' => lang('word_deleted_have_sales')]);
            }
    }
    // Edit Word Category
    public function editWord($id = null)
    {
           $word_details = $this->word_model->getwordCategoryByID($id);

            if ($this->input->post('word_submit')) {
                $data = ['name' => $this->input->post('wordname')];

                $this->word_model->updateWord($id, $data);
                $this->session->set_flashdata('message', lang('Word_updated'));
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                $this->data['wordcategory']      = $word_details;
                $this->data['modal_js']        = $this->site->modal_js();
                $this->load->view($this->theme . 'word_category/editword', $this->data);
            }
    }
    // Delete field via Checkbox 
    public function delete_action()
    {
        if (!$this->Owner && !$this->GP['bulk_actions']) {
            $this->session->set_flashdata('warning', lang('access_denied'));
            redirect($_SERVER['HTTP_REFERER']);
        }
   
        $this->form_validation->set_rules('word_action', lang('form_action'), 'required');
   
        if ($this->form_validation->run() == true) {
            if (!empty($_POST['val'])) {
                if ($this->input->post('word_action') == 'delete') {
                    $this->sma->checkPermissions('delete');
                    $error = false;
                    foreach ($_POST['val'] as $id) {
                        if (!$this->word_model->deleteWordAction($id)) {
                            $error = true;
                        }
                    }
                    if ($error) {
                        $this->session->set_flashdata('warning', lang('Word_x_deleted_have_sales'));
                    } else {
                        $this->session->set_flashdata('message', $this->lang->line('Word_deleted'));
                    }
                    redirect($_SERVER['HTTP_REFERER']);
                }
   
   
                }
                else {
                   $this->session->set_flashdata('error', $this->lang->line('no_word_selected'));
                   redirect($_SERVER['HTTP_REFERER']);
            }
        
      }
    }
}
  

    
?>