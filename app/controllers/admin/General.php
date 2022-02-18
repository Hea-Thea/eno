<?php

defined('BASEPATH') or exit('No direct script access allowed');

class General extends MY_Controller
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
        $this->load->admin_model('general_model');

        $config['upload_path']     = 'assets/uploads/generals/';
        $config ['allowed_types']    = 'gif|jpg|jpeg|png';
        $config ['max_size']   = '10240';
        $config['overwrite']   = TRUE;

        $this->load->library("upload",$config);
        
    }

    public function index($action = null) 
    {    
       $this->sma->checkPermissions();

       $this->data['error']  = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
       $this->data['action'] = $action;

       
       $bc   = [['link' => base_url(), 'page' => lang('home')],  ['link' => '#', 'page' => lang('general')],['link' => admin_url('general/action_user'), 'page' => lang('add_items')]];
       $meta = ['page_title' => lang('typeofspeech'), 'bc' => $bc];
       $this->page_construct('general/view', $meta, $this->data);
    }
    public function getgeneral(){
        $this->sma->checkPermissions('index');
        $this->load->library('datatables');
        $this->datatables
            ->select(
                        'kce_general_detail.id as id,
                        kce_category_general.name,
                        title,
                        description,
                        img_url,video_url,
                        audio_url')

            ->from('kce_general_detail')

            ->join(
                    'kce_category_general',
                    'kce_category_general.id  =  kce_general_detail.sma_kce_category_general_id',
                    'inner')
            ->add_column('Actions', 
            "<div class=\"text-center\" style=\"display:flex; gap:5px; justify-content:center; align-items:center;\">
                <a class=\"tip btn btn-primary btn-sm\"  style=\"border-radius: 6px !important;\" title='<b> " . lang('edit_payment') . "</b>' href='" . admin_url('general/edit/$1') . "' data-toggle=''  data-target=''>
                    <span class=\"badge btn-sm\"> <i class=\"fa fa-edit\"></i> </span>
                </a>

                    <a href='#' class='tip po btn btn-danger btn-sm' title='<b>".lang('delete_payment')."</b>' style=\"border-radius: 6px !important;\" data-content=\"<p>" . lang('r_u_sure') . "</p>
                    <a class='btn btn-danger po-delete' href='" . admin_url('general/delete/$1') . "'>" . lang('i_m_sure') . "</a> 
                
                    <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'>
                    <span class=\"badge btn-sm\"><i class=\"fa fa-trash-o\"></i> </span>
                </a>
            </div>", 'id');
            
            echo $this->datatables->generate();            
            
    }

    public function action_user(){
        $this->sma->checkPermissions();
        $this->data['error']  = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');

        $tbl_general = $this->general_model->get_tbl_general();

        $this->data['get_tbl_general'] = $tbl_general;


        $bc   = [['link' => base_url(), 'page' => lang('home')],  ['link' => admin_url('general/index'), 'page' => lang('general')],['link' => '#', 'page' => lang('add items')]];
        $meta = ['page_title' => lang('add'), 'bc' => $bc];
        $this->page_construct('general/add', $meta, $this->data); 
    }

    public function resize_img($fileName,$error_location){
        //resize_image
        $this->load->library('image_lib');
        $config['image_library']  = 'gd2';
        $config['source_image']   = 'assets/uploads/generals/'.$fileName;
        $config['new_image']      = 'assets/uploads/generals/thumbs/'.$fileName;
        $config['maintain_ratio'] = true;
        $config['width']          = 900;
        $config['height']         = 675;

        $this->image_lib->clear();
        $this->image_lib->initialize($config);

        if (!$this->image_lib->resize()) {
            $error = $this->image_lib->display_errors();
            $this->session->set_flashdata('error', lang($error));
            admin_redirect("general/$error_location");
        }
        $this->image_lib->clear();
    }
    public function add(){
        if($this->input->post('category') != 0){

            if($this->upload->do_upload('general_img')){
                $img = $this->upload->data();
                $fileName = $img['file_name'];

                $this->resize_img($fileName,'action_user');
            }
            else{
                $fileName = '';
            }
            $fields = array(
                "category"      => $this->input->post('category'),
                "title"         => $this->input->post('title'),
                "attachments"   => $fileName,
                "video_url"     => $this->input->post('video_url'),
                "audio_url"     => $this->input->post('audio_url'),
                "description"   => $this->input->post('description_general')
            );
            $this->general_model->add_general($fields);
            $this->session->set_flashdata('message', lang('item_add'));
            admin_redirect('general/action_user');
        }
        else{
            $this->session->set_flashdata('error', lang('please select one of category'));
            admin_redirect('general/action_user');
        }
    }

    public function edit($id){
        $this->sma->checkPermissions(null, true);

        if($this->input->post('update_items'))
        {
            if($this->upload->do_upload('general_img')){
                $img = $this->upload->data();
                $fileName = $img['file_name'];
                $this->resize_img($fileName,'edit');

                $data = [
                    'sma_kce_category_general_id'      => $this->input->post('category'),
                    'title'         => $this->input->post('title'),
                    'img_url'       => $fileName,
                    'video_url'     => $this->input->post('video_url'),
                    'audio_url'     => $this->input->post('audio_url'),
                    'description'   => $this->input->post('description_general')
                ];
            }
            else{
                $data = [
                    'sma_kce_category_general_id'      => $this->input->post('category'),
                    'title'         => $this->input->post('title'),
                    'video_url'     => $this->input->post('video_url'),
                    'audio_url'     => $this->input->post('audio_url'),
                    'description'   => $this->input->post('description_general')
                ];
            }
            
            $this->general_model->update_general($id, $data);
            $this->session->set_flashdata('message', lang('Item_updated'));
            admin_redirect('general/index');
        }
        else
        {
            $general_details = $this->general_model->getGeneralByID($id);
            $tbl_general = $this->general_model->get_tbl_general();
            $this->data['get_tbl_general']  = $tbl_general;
            $this->data['general_detail']   = $general_details;
            

            $bc   = [['link' => base_url(), 'page' => lang('home')],  ['link' => admin_url('general/index'), 'page' => lang('general')],['link' => '#', 'page' => lang('edit_items')]];
            $meta = ['page_title' => lang('edit'), 'bc' => $bc];
            $this->page_construct('general/edit', $meta, $this->data); 
        }
    }
    public function delete($id = null)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if ($this->general_model->deleteGeneral($id)) {
            $this->sma->send_json(['error' => 0, 'msg' => lang('Item_deleted')]);
        } else {
            $this->sma->send_json(['error' => 1, 'msg' => lang('Item_x_deleted_have_customer_selected')]);
        }
    }
    public function general_actions(){
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
                        if (!$this->general_model->deleteGeneral($id)) {
                            $error = true;
                        }
                    }
                    $this->session->set_flashdata('message', lang('Item_selected_delete_success'));
                    admin_redirect('general/index');
                }
            } else {
                $this->session->set_flashdata('error', lang('no_items_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    public function import_csv(){
        if($this->input->post('import')){
            $this->sma->checkPermissions('add', true);
            $this->load->helper('security');
            $this->form_validation->set_rules('csv_file', lang('upload_file'), 'xss_clean');

            if ($this->form_validation->run() == true) {
                if (DEMO) {
                    $this->session->set_flashdata('warning', lang('disabled_in_demo'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
    
                if (isset($_FILES['csv_file'])) {
                    $this->load->library('upload');
    
                    $config['upload_path']   = 'files/general_files/';
                    $config['allowed_types'] = 'csv';
                    $config['max_size']      = '200000';
                    $config['overwrite']     = true;
                    $config['encrypt_name']  = true;
    
                    $this->upload->initialize($config);

                    if (!$this->upload->do_upload('csv_file')) {
                        $error = $this->upload->display_errors();
                        $this->session->set_flashdata('error', $error);
                        admin_redirect('general/index'); 
                    }
                    else
                    {
                        $csv = $this->upload->file_name;

                        $arrResult = [];
                        $handle    = fopen('files/general_files/' . $csv, 'r');
                        if ($handle) {
                            while (($row = fgetcsv($handle, 5001, ',')) !== false) {
                                $arrResult[] = $row;
                            }
                            fclose($handle);

                            $general_fields = array();

                            foreach ($arrResult as $key => $value) {
                                if($key !=0){
                                    $general_fields[] = [
                                        'sma_kce_category_general_id'    => isset($value[0]) ? trim($value[0]) : '',
                                        'title'                          => isset($value[1]) ? trim($value[1]) : '',
                                        'description'                    => isset($value[2]) ? trim($value[2]) : '',
                                    ];
                                }
                            }
                            if($this->general_model->insert_csv($general_fields)){
                                $this->session->set_flashdata('message', lang('Import Success'));
                                admin_redirect('general/index');
                            }
                            else{
                                $this->session->set_flashdata('error', lang($this->upload->display_errors()));
                                admin_redirect('general/index');
                            }
                        }
                    }
                }
            }
        }
        else{
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'general/import', $this->data);
        }
    }
}

