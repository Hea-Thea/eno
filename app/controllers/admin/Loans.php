<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Loans extends MY_Controller
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
        $this->load->admin_model('companies_model');
        $this->load->admin_model('loans_model');

        $this->digital_upload_path = 'files/';
        $this->upload_path         = 'assets/uploads/';
        $this->thumbs_path         = 'assets/uploads/thumbs/';
        $this->image_types         = 'gif|jpg|jpeg|png|tif';
        $this->digital_file_types  = 'zip|psd|ai|rar|pdf|doc|docx|xls|xlsx|ppt|pptx|gif|jpg|jpeg|png|tif|txt';
        $this->allowed_file_size   = '1024';
        $this->data['logo']        = true;
        $this->load->library('attachments', [
            'path'     => $this->digital_upload_path,
            'types'    => $this->digital_file_types,
            'max_size' => $this->allowed_file_size,
        ]);
    }

    public function index($id = null)
    {
        $this->sma->checkPermissions();

        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $bc                  = [['link' => base_url(), 'page' => lang('home')], ['link' => '#', 'page' => lang('loans')]];
        $meta                = ['page_title' => lang('loans'), 'bc' => $bc];
        $this->page_construct('loans/index', $meta, $this->data);
    }

    public function getLoans()
    {
        $this->sma->checkPermissions('loans');

        $payment_schedule_link = anchor('admin/loans/payment_schedule/$1', '<i class="fa fa-file-text-o"></i> ' . lang('payment_schedule'));
        $edit_link   = anchor('admin/loans/edit/$1', '<i class="fa fa-edit"></i> ' . lang('edit'));
        //$attachment_link = '<a href="'.base_url('assets/uploads/$1').'" target="_blank"><i class="fa fa-chain"></i></a>';
        $detail_link       = anchor('admin/loans/view/$1', '<i class="fa fa-file-text-o"></i> ' . lang('loan_details'));
        $payments_link     = anchor('admin/loans/payments/$1', '<i class="fa fa-money"></i> ' . lang('view_payments'), 'data-toggle="modal" data-target="#myModal"');
        $add_payment_link  = anchor('admin/loans/add_payment/$1', '<i class="fa fa-money"></i> ' . lang('add_payment'), 'data-toggle="modal" data-target="#myModal"');

        $delete_link = "<a href='#' class='po' title='<b>" . $this->lang->line('delete') . "</b>' data-content=\"<p>"
        . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . admin_url('loans/delete/$1') . "'>"
        . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i> "
        . lang('delete') . '</a>';
        $action = '<div class="text-center"><div class="btn-group text-left">'
        . '<button type="button" class="btn btn-default btn-xs btn-primary dropdown-toggle" data-toggle="dropdown">'
        . lang('actions') . ' <span class="caret"></span></button>
        <ul class="dropdown-menu pull-right" role="menu">
            <li>' . $payments_link . '</li>
            <li>' . $add_payment_link . '</li>
            <li>' . $payment_schedule_link . '</li>
            <li>' . $detail_link . '</li>
            <li>' . $edit_link . '</li>
            <li>' . $delete_link . '</li>
        </ul>
    </div></div>';

        $this->load->library('datatables');

        $this->datatables
            ->select($this->db->dbprefix('loans') . ".id as id, date, reference_no, {$this->db->dbprefix('loan_types')}.name as loan_type, amount,interest_rate,amount_interest,status, grand_total, paid, (grand_total-paid) as balance, payment_status, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user", false)
            ->from('loans')
            ->join('users', 'users.id=loans.created_by', 'left')
            ->join('loan_types', 'loan_types.id=loans.loan_type_id', 'left')
            ->group_by('loans.id');

        if (!$this->Owner && !$this->Admin && !$this->session->userdata('view_right')) {
            $this->datatables->where('created_by', $this->session->userdata('user_id'));
        }
        //$this->datatables->edit_column("attachment", $attachment_link, "attachment");
        $this->datatables->add_column('Actions', $action, 'id');
        echo $this->datatables->generate();
    }

    public function add()
    {
        $this->sma->checkPermissions(false, true);

        $this->form_validation->set_rules('loan_type', lang('loan_type'), 'required');
        $this->form_validation->set_rules('gccustomer', lang('customer'), 'required');
        $this->form_validation->set_rules('apply_date', lang('apply_date'), 'required');
        $this->form_validation->set_rules('agency', lang('agency'), 'required');
        $this->form_validation->set_rules('status', lang('status'), 'required');
        $this->form_validation->set_rules('currency', lang('currency'), 'required');
        $this->form_validation->set_rules('apply_amount', lang('amount'), 'required');
        $this->form_validation->set_rules('interest_rate', lang('interest_rate'), 'required');
        $this->form_validation->set_rules('interest_type', lang('interest_type'), 'required');
        $this->form_validation->set_rules('term', lang('term'), 'required');
        $this->form_validation->set_rules('loan_term', lang('loan_term'), 'required');
        $this->form_validation->set_rules('first_payment_date', lang('first_payment_date'), 'required');

        $this->form_validation->set_rules('guarantor', lang('guarantor'), 'required');


        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }

            $apply_date = $this->sma->fsd(trim($this->input->post('apply_date')));
            $first_payment_date = $this->sma->fsd(trim($this->input->post('first_payment_date')));

            
            $data = [
                'date'=> $date,
                'reference_no'    => $this->input->post('reference') ? $this->input->post('reference') : $this->site->getReference('ln'),
                'loan_type_id'  => $this->input->post('loan_type', true),
                'customer_id'  => $this->input->post('gccustomer', true),
                'description'         => $this->input->post('description', true),
                'apply_date'       => $apply_date,
                'agency_id'  => $this->input->post('agency', true),
                'remark'  => $this->input->post('remark', true),
                'staff_note'  => $this->input->post('remark', true),
                'status'  => $this->input->post('status', true),
                'currency_id'  => $this->input->post('currency', true),
                'amount'       => $this->input->post('apply_amount'),
                'interest_rate'       => $this->input->post('interest_rate'),
                'interest_type_id'       => $this->input->post('interest_type'),
                'term'  => $this->input->post('term', true),
                'loan_term_id'  => $this->input->post('loan_term', true),
                'first_payment_date'       => $first_payment_date,
                'guarantor_id'       => $this->input->post('guarantor',true),
                'customer_colleteral'       => $this->input->post('customer_colleteral'),
                'purpose'       => $this->input->post('purpose'),
                'created_by'   => $this->session->userdata('user_id'),
                'payment_status'=> 'pending'
            ];
            
            
        } elseif ($this->input->post('add_loan')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->loans_model->addLoan($data)) {  
            $this->session->set_flashdata('message', lang('loan_added'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error']           = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js']        = $this->site->modal_js();
    
            $this->data['loan_terms']    = $this->loans_model->getAllLoanTerms();
            $this->data['loan_types']    = $this->loans_model->getAllLoanTypes();
            $this->data['interest_types']    = $this->loans_model->getAllInterestTypes();
            $this->data['currencies']    = $this->loans_model->getAllCurrencies();


            $this->data['billers']    = $this->site->getAllCompanies('biller');
            $this->data['customers']    = $this->site->getAllCompanies('customer');
            $this->data['agencies']    = $this->site->getAllCompanies('agency');
            $this->data['guarantors']    = $this->site->getAllCompanies('guarantor');

            $bc                       = [['link' => admin_url('home'), 'page' => lang('home')], ['link' => admin_url('loans'), 'page' => lang('loans')], ['link' => '#', 'page' => lang('create_loan')]];
            $meta                     = ['page_title' => lang('loans'), 'bc' => $bc];
            $this->page_construct('loans/add', $meta, $this->data);
        }
    }

    public function edit($id = null)
    {
        $this->sma->checkPermissions('edit', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
       
        $this->form_validation->set_rules('loan_type', lang('loan_type'), 'required');
        $this->form_validation->set_rules('gccustomer', lang('customer'), 'required');
        $this->form_validation->set_rules('apply_date', lang('apply_date'), 'required');
        $this->form_validation->set_rules('agency', lang('agency'), 'required');
        $this->form_validation->set_rules('status', lang('status'), 'required');
        $this->form_validation->set_rules('currency', lang('currency'), 'required');
        $this->form_validation->set_rules('apply_amount', lang('amount'), 'required');
        $this->form_validation->set_rules('interest_rate', lang('interest_rate'), 'required');
        $this->form_validation->set_rules('interest_type', lang('interest_type'), 'required');
        $this->form_validation->set_rules('term', lang('term'), 'required');
        $this->form_validation->set_rules('loan_term', lang('loan_term'), 'required');
        $this->form_validation->set_rules('first_payment_date', lang('first_payment_date'), 'required');

        $this->form_validation->set_rules('guarantor', lang('guarantor'), 'required');


        if ($this->form_validation->run() == true) {
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }

            $apply_date = $this->sma->fsd(trim($this->input->post('apply_date')));
            $first_payment_date = $this->sma->fsd(trim($this->input->post('first_payment_date')));

            
            $data = [
                'date'=> $date,
                'reference_no'    => $this->input->post('reference') ? $this->input->post('reference') : $this->site->getReference('ln'),
                'loan_type_id'  => $this->input->post('loan_type', true),
                'customer_id'  => $this->input->post('gccustomer', true),
                'description'         => $this->input->post('description', true),
                'apply_date'       => $apply_date,
                'agency_id'  => $this->input->post('agency', true),
                'remark'  => $this->input->post('remark', true),
                'staff_note'  => $this->input->post('remark', true),
                'status'  => $this->input->post('status', true),
                'currency_id'  => $this->input->post('currency', true),
                'amount'       => $this->input->post('apply_amount'),
                'interest_rate'       => $this->input->post('interest_rate'),
                'interest_type_id'       => $this->input->post('interest_type'),
                'term'  => $this->input->post('term', true),
                'loan_term_id'  => $this->input->post('loan_term', true),
                'first_payment_date'       => $first_payment_date,
                'guarantor_id'       => $this->input->post('guarantor',true),
                'customer_colleteral'       => $this->input->post('customer_colleteral'),
                'purpose'       => $this->input->post('purpose'),
                'created_by'   => $this->session->userdata('user_id')
            ];
            
        } elseif ($this->input->post('edit_loan')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->loans_model->updateLoan($id,$data)) {
            $this->session->set_flashdata('message', lang('loan_updated'));
            admin_redirect('loans');
        } else {
            $this->data['error']           = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['modal_js']        = $this->site->modal_js();
    
            $this->data['loan_terms']    = $this->loans_model->getAllLoanTerms();
            $this->data['loan_types']    = $this->loans_model->getAllLoanTypes();
            $this->data['interest_types']    = $this->loans_model->getAllInterestTypes();
            $this->data['currencies']    = $this->loans_model->getAllCurrencies();


            $this->data['billers']    = $this->site->getAllCompanies('biller');
            $this->data['customers']    = $this->site->getAllCompanies('customer');
            $this->data['agencies']    = $this->site->getAllCompanies('agency');
            $this->data['guarantors']    = $this->site->getAllCompanies('guarantor');
            $this->data['loan']    = $this->loans_model->getLoanByID($id);

            $bc                       = [['link' => admin_url('home'), 'page' => lang('home')], ['link' => admin_url('loans'), 'page' => lang('loans')], ['link' => '#', 'page' => lang('create_loan')]];
            $meta                     = ['page_title' => lang('loans'), 'bc' => $bc];
            $this->page_construct('loans/edit', $meta, $this->data);
        }
    }




















    public function payment_note($id = null)
    {
        $this->sma->checkPermissions('payments', true);
        $payment                  = $this->loans_model->getPaymentByID($id);
        $inv                      = $this->loans_model->getLoanByID($payment->loan_id);
        $this->data['biller']     = $this->site->getCompanyByID($inv->biller_id);
        $this->data['customer']   = $this->site->getCompanyByID($inv->customer_id);
        $this->data['inv']        = $inv;
        $this->data['payment']    = $payment;
        $this->data['page_title'] = lang('payment_note');

        $this->load->view($this->theme . 'loans/payment_note', $this->data);
    }

    /* -------------------------------------------------------------------------------- */

    public function payments($id = null)
    {
        $this->sma->checkPermissions(false, true);
        $this->data['payments'] = $this->loans_model->getLoanPayments($id);
        $this->data['inv']      = $this->loans_model->getLoanByID($id);
        $this->load->view($this->theme . 'loans/payments', $this->data);
    }

    public function add_payment($id = null)
    {
        $this->sma->checkPermissions('payments', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $loan = $this->loans_model->getLoanByID($id);
        if ($loan->status == 'completed' && $loan->grand_total == $loan->paid) {
            $this->session->set_flashdata('error', lang('loan_already_paid'));
            $this->sma->md();
        }

        //$this->form_validation->set_rules('reference_no', lang("reference_no"), 'required');
        $this->form_validation->set_rules('amount-paid', lang('amount'), 'required');
        $this->form_validation->set_rules('paid_by', lang('paid_by'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            $loan = $this->loans_model->getLoanByID($this->input->post('loan_id'));
            if ($this->input->post('paid_by') == 'deposit') {
                $customer_id = $loans_model->customer_id;
                if (!$this->site->check_customer_deposit($customer_id, $this->input->post('amount-paid'))) {
                    $this->session->set_flashdata('error', lang('amount_greater_than_deposit'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                $customer_id = null;
            }
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = date('Y-m-d H:i:s');
            }
            $payment = [
                'date'         => $date,
                'loan_id'      => $this->input->post('loan_id'),
                'reference_no' => $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('pay'),
                'amount'       => $this->input->post('amount-paid'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('paid_by') == 'gift_card' ? $this->input->post('gift_card_no') : $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'note'         => $this->input->post('note'),
                'created_by'   => $this->session->userdata('user_id'),
                'type'         => 'received',
            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo                 = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('add_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->loans_model->addPayment($payment, $customer_id)) {
            
            $this->session->set_flashdata('message', lang('payment_added'));
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            $this->data['error'] = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            if ($loan->status == 'completed' && $loan->paid == $loan->grand_total) {
                $this->session->set_flashdata('warning', lang('payment_was_returned'));
                $this->sma->md();
            }
            $this->data['inv']         = $loan;
            $this->data['payment_ref'] = ''; //$this->site->getReference('pay');
            $this->data['modal_js']    = $this->site->modal_js();

            $this->load->view($this->theme . 'loans/add_payment', $this->data);
        }
    }

    public function edit_payment($id = null)
    {
        $this->sma->checkPermissions('edit', true);
        $this->load->helper('security');
        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $payment = $this->loans_model->getPaymentByID($id);
        if ($payment->paid_by == 'ppp' || $payment->paid_by == 'stripe' || $payment->paid_by == 'paypal' || $payment->paid_by == 'skrill') {
            $this->session->set_flashdata('error', lang('x_edit_payment'));
            $this->sma->md();
        }
        $this->form_validation->set_rules('reference_no', lang('reference_no'), 'required');
        $this->form_validation->set_rules('amount-paid', lang('amount'), 'required');
        $this->form_validation->set_rules('paid_by', lang('paid_by'), 'required');
        $this->form_validation->set_rules('userfile', lang('attachment'), 'xss_clean');
        if ($this->form_validation->run() == true) {
            if ($this->input->post('paid_by') == 'deposit') {
                $loan        = $this->loans_model->getLoanByID($this->input->post('loan_id'));
                $customer_id = $loan->customer_id;
                $amount      = $this->input->post('amount-paid') - $payment->amount;
                if (!$this->site->check_customer_deposit($customer_id, $amount)) {
                    $this->session->set_flashdata('error', lang('amount_greater_than_deposit'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                $customer_id = null;
            }
            if ($this->Owner || $this->Admin) {
                $date = $this->sma->fld(trim($this->input->post('date')));
            } else {
                $date = $payment->date;
            }
            $payment = [
                'date'         => $date,
                'loan_id'      => $this->input->post('loan_id'),
                'reference_no' => $this->input->post('reference_no'),
                'amount'       => $this->input->post('amount-paid'),
                'paid_by'      => $this->input->post('paid_by'),
                'cheque_no'    => $this->input->post('cheque_no'),
                'cc_no'        => $this->input->post('pcc_no'),
                'cc_holder'    => $this->input->post('pcc_holder'),
                'cc_month'     => $this->input->post('pcc_month'),
                'cc_year'      => $this->input->post('pcc_year'),
                'cc_type'      => $this->input->post('pcc_type'),
                'note'         => $this->input->post('note'),
                'created_by'   => $this->session->userdata('user_id'),
            ];

            if ($_FILES['userfile']['size'] > 0) {
                $this->load->library('upload');
                $config['upload_path']   = $this->digital_upload_path;
                $config['allowed_types'] = $this->digital_file_types;
                $config['max_size']      = $this->allowed_file_size;
                $config['overwrite']     = false;
                $config['encrypt_name']  = true;
                $this->upload->initialize($config);
                if (!$this->upload->do_upload()) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }
                $photo                 = $this->upload->file_name;
                $payment['attachment'] = $photo;
            }

            //$this->sma->print_arrays($payment);
        } elseif ($this->input->post('edit_payment')) {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }

        if ($this->form_validation->run() == true && $this->loans_model->updatePayment($id, $payment, $customer_id)) {
            $this->session->set_flashdata('message', lang('payment_updated'));
            admin_redirect('loans');
        } else {
            $this->data['error']    = (validation_errors() ? validation_errors() : $this->session->flashdata('error'));
            $this->data['payment']  = $payment;
            $this->data['modal_js'] = $this->site->modal_js();
            $this->load->view($this->theme . 'loans/edit_payment', $this->data);
        }
    }

    public function loan_actions()
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
                    foreach ($_POST['val'] as $id) {
                        $this->loans_model->deleteLoan($id);
                    }
                    $this->session->set_flashdata('message', $this->lang->line('loan_deleted'));
                    redirect($_SERVER['HTTP_REFERER']);
                }

                if ($this->input->post('form_action') == 'export_excel') {
                    $this->load->library('excel');
                    $this->excel->setActiveSheetIndex(0);
                    $this->excel->getActiveSheet()->setTitle(lang('loans'));
                    $this->excel->getActiveSheet()->SetCellValue('A1', lang('date'));
                    $this->excel->getActiveSheet()->SetCellValue('B1', lang('reference'));
                    $this->excel->getActiveSheet()->SetCellValue('C1', lang('amount'));
                    $this->excel->getActiveSheet()->SetCellValue('D1', lang('note'));
                    $this->excel->getActiveSheet()->SetCellValue('E1', lang('created_by'));

                    $row = 2;
                    foreach ($_POST['val'] as $id) {
                        $loan = $this->loans_model->getLoanByID($id);
                        $user    = $this->site->getUser($loan->created_by);
                        $this->excel->getActiveSheet()->SetCellValue('A' . $row, $this->sma->hrld($loan->date));
                        $this->excel->getActiveSheet()->SetCellValue('B' . $row, $loan->reference_no);
                        $this->excel->getActiveSheet()->SetCellValue('C' . $row, $this->sma->formatMoney($loan->amount));
                        $this->excel->getActiveSheet()->SetCellValue('D' . $row, $loan->description);
                        $this->excel->getActiveSheet()->SetCellValue('E' . $row, $user->first_name . ' ' . $user->last_name);
                        $row++;
                    }

                    $this->excel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                    $this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excel->getDefaultStyle()->getAlignment()->setVertical('center');
                    $filename = 'loans_' . date('Y_m_d_H_i_s');
                    $this->load->helper('excel');
                    create_excel($this->excel, $filename);
                }
            } else {
                $this->session->set_flashdata('error', $this->lang->line('no_loan_selected'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        } else {
            $this->session->set_flashdata('error', validation_errors());
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete($id = null)
    {
        $this->sma->checkPermissions(null, true);

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if (!$id) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('id_not_found')]);
        }

        if ($this->loans_model->deleteLoan($id)) {
            if ($this->input->is_ajax_request()) {
                $this->sma->send_json(['error' => 0, 'msg' => lang('loan_deleted')]);
            }
            $this->session->set_flashdata('message', lang('loan_deleted'));
            admin_redirect('loans');
        }
    }

    public function delete_payment($id = null)
    {
        $this->sma->checkPermissions('delete');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        if (!$id) {
            $this->sma->send_json(['error' => 1, 'msg' => lang('id_not_found')]);
        }

        if ($this->loans_model->deletePayment($id)) {
            //echo lang("payment_deleted");
            $this->session->set_flashdata('message', lang('payment_deleted'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function view($id = null)
    {
        $this->sma->checkPermissions('index');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->loans_model->getLoanByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode']     = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer']    = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments']    = $this->loans_model->getPaymentsForLoan($id);
        $this->data['biller']      = $this->site->getCompanyByID($inv->biller_id);
        $this->data['created_by']  = $this->site->getUser($inv->created_by);
        $this->data['updated_by']  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        //$this->data['warehouse']   = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']         = $inv;
        $this->data['rows']        = $this->loans_model->getPaymentSchedule($id);
        // $this->data['return_sale'] = $inv->return_id ? $this->loans_model->getInvoiceByID($inv->return_id) : null;
        // $this->data['return_rows'] = $inv->return_id ? $this->loans_model->getAllInvoiceItems($inv->return_id) : null;
        //$this->data['paypal']      = $this->loans_model->getPaypalSettings();
        //$this->data['skrill']      = $this->loans_model->getSkrillSettings();
        $this->data['attachments'] = $this->site->getAttachments($id, 'loan');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('loans'), 'page' => lang('loans')], ['link' => '#', 'page' => lang('view')]];
        $meta = ['page_title' => lang('view_loans_details'), 'bc' => $bc];
        $this->page_construct('loans/view', $meta, $this->data);
    }

    public function payment_schedule($id = null)
    {
        $this->sma->checkPermissions('index');

        if ($this->input->get('id')) {
            $id = $this->input->get('id');
        }
        $this->data['error'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('error');
        $inv = $this->loans_model->getLoanByID($id);
        if (!$this->session->userdata('view_right')) {
            $this->sma->view_rights($inv->created_by);
        }
        $this->data['barcode']     = "<img src='" . admin_url('products/gen_barcode/' . $inv->reference_no) . "' alt='" . $inv->reference_no . "' class='pull-left' />";
        $this->data['customer']    = $this->site->getCompanyByID($inv->customer_id);
        $this->data['payments']    = $this->loans_model->getPaymentsForLoan($id);
        $this->data['biller']      = $this->site->getCompanyByID($inv->biller_id);
        $this->data['created_by']  = $this->site->getUser($inv->created_by);
        $this->data['updated_by']  = $inv->updated_by ? $this->site->getUser($inv->updated_by) : null;
        //$this->data['warehouse']   = $this->site->getWarehouseByID($inv->warehouse_id);
        $this->data['inv']         = $inv;
        $this->data['holiday_arr'] = $this->db->get('loan_holiday_detail');
        //$this->data['rows']        = $this->loans_model->getPaymentSchedule($id);
        // $this->data['return_sale'] = $inv->return_id ? $this->loans_model->getInvoiceByID($inv->return_id) : null;
        // $this->data['return_rows'] = $inv->return_id ? $this->loans_model->getAllInvoiceItems($inv->return_id) : null;
        //$this->data['paypal']      = $this->loans_model->getPaypalSettings();
        //$this->data['skrill']      = $this->loans_model->getSkrillSettings();
        $this->data['attachments'] = $this->site->getAttachments($id, 'loan');

        $bc   = [['link' => base_url(), 'page' => lang('home')], ['link' => admin_url('loans'), 'page' => lang('loans')], ['link' => '#', 'page' => lang('view')]];
        $meta = ['page_title' => lang('view_loans_details'), 'bc' => $bc];
        $this->page_construct('loans/payment_schedule', $meta, $this->data);
    }

    
}
