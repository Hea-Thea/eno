<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Typeofpayment extends MY_Controller
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
        $this->lang->admin_load('customers', $this->Settings->user_language);
        $this->load->library('form_validation');
        $this->load->admin_model('typeofpayment_model');
    }
 
    public function index($action = null) 
    {    
       $this->sma->checkPermissions();

       $this->data['error']  = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       $this->data['action'] = $action;
       
       $bc   = [['link' => base_url(), 'page' => lang('home')],  ['link' => '#', 'page' => lang('Type_of_payment')]];
       $meta = ['page_title' => lang('typeofspeech'), 'bc' => $bc];
       $this->page_construct('typeofpayment/view', $meta, $this->data);
       
      
    }

    public function gettypeofpayment()
    {
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select('id,name')
            ->from('kce_type_payment')
            ->add_column('Actions', 
            "<div class=\"text-center\" style=\"display:flex; gap:5px; justify-content:center; align-items:center;\">
                <a class=\"tip btn btn-primary btn-sm\"  style=\"border-radius: 6px !important;\" title='<b> " . lang('edit_type_of_payment') . "</b>' href='" . admin_url('typeofpayment/edit/$1') . "' data-toggle='modal'  data-target='#myModal'>
                 <span class=\"badge btn-sm\"> <i class=\"fa fa-edit\"></i> </span>
                </a>

                 <a href='#' class='tip po btn btn-danger btn-sm' title='<b>".lang('delete_type_of_payment')."</b>' style=\"border-radius: 6px !important;\" data-content=\"<p>" . lang('r_u_sure') . "</p>
                    <a class='btn btn-danger po-delete' href='" . admin_url('typeofpayment/delete/$1') . "'>" . lang('i_m_sure') . "</a> 
                
                    <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'>
                    <span class=\"badge btn-sm\"><i class=\"fa fa-trash-o\"></i> </span>
                </a>
            </div>", 'id');
            echo $this->datatables->generate();
    }

    public function action_user(){
        $this->data['modal_js']        = $this->site->modal_js();
        $this->load->view($this->theme . 'typeofpayment/add', $this->data);
    }
    
    public function insert(){
        
        $this->sma->checkPermissions(null, true);

        $param['name'] = $this->input->post('typeofpayment');

        $this->typeofpayment_model->insert($param);
        $this->session->set_flashdata('message', lang('type_of_payment_added'));
        admin_redirect('typeofpayment/index');

    }

    public function delete($id = null)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if ($this->typeofpayment_model->deletetypeofpayment($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('type_of_payment_deleted')]);
        } else {
            $this->sma->send_json(['error' => 1, 'msg' => lang('type_of_pamyment_x_deleted_have_customer_selected')]);
        }
    }

    public function edit($id=null){ 
        $this->sma->checkPermissions(null, true);

        $typeofpayment_details = $this->typeofpayment_model->getTypeofpaymentByID($id);

        if($this->input->post('update_typeofpayment')){
            $data = [
                        'name' => $this->input->post('typeofpayment')
                    ];
            $this->typeofpayment_model->updatetypeofpayment($id, $data);
            $this->session->set_flashdata('message', lang('Type_of_payment_updated'));
            redirect($_SERVER['HTTP_REFERER']);
        }
        else{
            $this->data['typeofpayment']      = $typeofpayment_details;
            $this->data['modal_js']        = $this->site->modal_js();
            $this->load->view($this->theme . 'typeofpayment/edit', $this->data);
        }
    }

    public function typeofpayment_actions(){
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
                        if (!$this->typeofpayment_model->deletetypeofpayment($id)) {
                            $error = true;
                        }
                    }
                    // redirect($_SERVER['HTTP_REFERER']);
                    $this->session->set_flashdata('message', lang('type_of_payment_selected_delete_success'));
                    admin_redirect('typeofpayment/index');
                }
            } else {
                $this->session->set_flashdata('error', lang('no_typeofpayment_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    
}
