<?php


defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Api extends REST_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->api_model('main_api');
        $this->load->api_model('products_api');
        $this->load->api_model('categories_api');
        $this->load->shop_model('shop_model');
        $this->load->library('sma');
        $this->load->library('ion_auth');
        $this->load->library('Tec_cart', '', 'cart');
        $this->load->library('form_validation');
        $this->load->admin_model('auth_model');
        $this->load->admin_model('companies_model');

        $this->shop_settings = $this->shop_model->getShopSettings();

        $this->customer = $this->warehouse = $this->customer_group = false;
        $id = $this->input->get_request_header('User-ID', TRUE);
        if($id){
            $user = $this->db->select('*')->from('sma_users')->where('id',$id)->get()->row();
            $this->customer       = $this->site->getCompanyByID($user->company_id);
            $this->customer_group = $this->shop_model->getCustomerGroup($this->customer->customer_group_id);
        }

        if ($selected_currency = get_cookie('shop_currency', true)) {
            $this->Settings->selected_currency = $selected_currency;
        } else {
            $this->Settings->selected_currency = $this->Settings->default_currency;
        }
        $this->default_currency          = $this->shop_model->getCurrencyByCode($this->Settings->default_currency);
        $this->data['default_currency']  = $this->default_currency;
        $this->selected_currency         = $this->shop_model->getCurrencyByCode($this->Settings->selected_currency);
        $this->data['selected_currency'] = $this->selected_currency;
    }

    // public function index_get(){
    //     $latestProducts = $this->main_api->getLatestProducts();
    //     $featureProducts = $this->main_api->getFeaturedProducts();
    //     $promoProducts = $this->main_api->getPromoProducts();
    //     $categories = $this->main_api->getCategories();
    //     $banners = $this->main_api->getBanners();

    //     $category_data = [];
    //     foreach ($categories as $category) {

    //         $category->sub_category = $this->categories_api->countSubCategories($category->id);
    //         $category_data[] = $category;
    //     }

    //     $data = [
    //         'banners' => $banners,
    //         'categories' => $category_data,
    //         'latest_products'  => $latestProducts,
    //         'feature_products' => $featureProducts,
    //         'promotion_products' => $promoProducts
    //     ];

    //     if($data){
    //         $this->response($data, REST_Controller::HTTP_OK);
    //     }else{
    //         $this->response(NULL, 404);
    //     }
    // } 

    // public function search_get(){
        
    //     $categories = $this->main_api->getCategories();
    //     $brands = $this->main_api->getBrands();

    //     $category_data = [];
    //     foreach ($categories as $category) {

    //         $category->sub_category = $this->categories_api->countSubCategories($category->id);
    //         $category_data[] = $category;
    //     }

    //     $data = [
    //         'brands' => $brands,
    //         'categories' => $category_data
    //     ];

    //     if($data){
    //         $this->response($data, REST_Controller::HTTP_OK);
    //     }else{
    //         $this->response(NULL, 404);
    //     }
    // }

    public function login_post(){  
        $remember = false;     
        if ($this->ion_auth->login($this->input->post('identity'), $this->input->post('password'), $remember)) {

            $this->load->helper('email');
            $identity = $this->input->post('identity');
            $password = $this->input->post('password');

            $identity_column = valid_email($identity) ? 'email' : 'phone';
            
            $query = $this->db->select($identity_column . ', username, email, id, password, active, last_login, last_ip_address, avatar, gender, group_id, warehouse_id, biller_id, company_id, view_right, edit_right, allow_discount, show_cost, show_price,first_name,last_name,company,avatar')
                ->where($identity_column, $this->db->escape_str($identity))
                ->limit(1)
                ->get('users');

            if ($query->num_rows() === 1) {
                $user = $query->row();

                $password = $this->auth_model->hash_password_db($user->id, $password);

                if ($password === true) {
                    
                    if ($user->active != 1) {
                        $registerStatus = array('message'=> 'login_unsuccessful_not_active','status'=> "error");
                        $this->response($registerStatus, REST_Controller::HTTP_OK);
                        exit;
                    }

                    $this->auth_model->update_last_login($user->id);
                    $this->auth_model->update_last_login_ip($user->id);
                    $ldata = ['user_id' => $user->id, 'ip_address' => $this->input->ip_address(), 'login' => $identity, 'time' => date('Y-m-d H:i:s')];
                    $this->db->insert('user_logins', $ldata);
                    $this->auth_model->clear_login_attempts($identity);


                    $loginStatus = array('message'=> 'login successful','status'=> "success",'data'=>$user);
                    $this->response($loginStatus, REST_Controller::HTTP_OK);
                }
            }else{

            }
          
        } else {
            $registerStatus = array('message'=> $this->ion_auth->errors(),'status'=> "error");
            $this->response($registerStatus, REST_Controller::HTTP_OK);
        }
    }

    public function register_post(){  
        $email    = strtolower($this->input->post('email'));
        $username = strtolower($this->input->post('username'));
        $password = $this->input->post('password');
        $phone    = $this->input->post('phone');
       
        $this->form_validation->set_rules('first_name', lang('first_name'), 'required');
        $this->form_validation->set_rules('last_name', lang('last_name'), 'required');
        // $this->form_validation->set_rules('email', lang('email_address'), 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('phone',lang('phone'),'required');
        $this->form_validation->set_rules('username', lang('username'), 'required|is_unique[users.username]');
        $this->form_validation->set_rules('password', lang('password'), 'required|min_length[8]|max_length[25]|matches[password_confirm]');
        $this->form_validation->set_rules('password_confirm', lang('confirm_password'), 'required');
        

        if ($this->form_validation->run() == false) {
            
            $errors = $this->form_validation->error_array();
            $registerStatus = array('message'=> 'Register Fail','status'=> 'error','errors'=> $errors);
                $this->response($registerStatus, REST_Controller::HTTP_OK);
        }else{
            $customer_group = $this->shop_model->getCustomerGroup($this->Settings->customer_group);
            $price_group    = $this->shop_model->getPriceGroup($this->Settings->price_group);

            $company_data = [
                'company'             => $this->input->post('company') ? $this->input->post('company') : '-',
                'name'                => $this->input->post('first_name') . ' ' . $this->input->post('last_name'),
                'email'               => $this->input->post('email'),
                'phone'               => $this->input->post('phone'),
                'group_id'            => 8,
                'group_name'          => 'student',
                'customer_group_id'   => (!empty($customer_group)) ? $customer_group->id : null,
                'customer_group_name' => (!empty($customer_group)) ? $customer_group->name : null,
                'price_group_id'      => (!empty($price_group)) ? $price_group->id : null,
                'price_group_name'    => (!empty($price_group)) ? $price_group->name : null,
                // 'customer_group_id'   => null,
                // 'customer_group_name' => null,
                // 'price_group_id'      => null,
                // 'price_group_name'    => null,
            ];

            $company_id = $this->shop_model->addCustomer($company_data);

            $additional_data = [
                'first_name' => $this->input->post('first_name'),
                'last_name'  => $this->input->post('last_name'),
                'phone'      => $this->input->post('phone'),
                'company'    => '',
                'gender'     => 'male',
                'company_id' => $company_id,
                'group_id'   => 8,
                //'active'     => 1
                // 'player_id'    => $this->input->post('player_id'),
            ];
            
            if ($this->ion_auth->register($username, $password, $email,$additional_data,true)) {
                $registerStatus = array('message'=> 'Register Success','status'=> "success");
                $this->response($registerStatus, REST_Controller::HTTP_OK);
            }else{
                $registerStatus = array('message'=> 'Register Fail','status'=> "error");
                $this->response($registerStatus, REST_Controller::HTTP_OK);
            }
        }

        

    }

    public function forgot_password_post(){  
        $this->form_validation->set_rules('email', lang('email_address'), 'required|valid_email');

        if ($this->form_validation->run() == false) {
            $errors = $this->form_validation->error_array();
            $registerStatus = array('message'=> 'Forgot Password Fail','status'=> 'error','errors'=> $errors);
            $this->response($registerStatus, REST_Controller::HTTP_OK);
        }else{
            $identity = $this->ion_auth->where('email', strtolower($this->input->post('email')))->users()->row();
            if (empty($identity) || $identity == NULL) {
                $responseStatus = array('message'=> "forgot_password_email_not_found",'status'=> "error");
                $this->response($responseStatus, REST_Controller::HTTP_OK);
            }else{
                $forgotten = $this->ion_auth->forgotten_password($identity->email);
                if ($forgotten) {
                    $responseStatus = array('message'=> $this->ion_auth->messages(),'status'=> "success");
                    $this->response($responseStatus, REST_Controller::HTTP_OK);
                } else {

                    $responseStatus = array('message'=> $this->ion_auth->errors(),'status'=> "error");
                    $this->response($responseStatus, REST_Controller::HTTP_OK);
                }
            }

        }     
        
    }

    public function verify_code_post(){       
        $phone = $this->post("phone");
        $password = $this->post("password");

        $data = array('returned post: '. $this->post('email'));
        $this->response($data);
    }

    public function reset_password_post(){ 
        $this->form_validation->set_rules('new', lang('password'), 'required|min_length[8]|max_length[25]|matches[new_confirm]');
        $this->form_validation->set_rules('new_confirm', lang('confirm_password'), 'required');

        if ($this->form_validation->run() == false) {
            $errors = $this->form_validation->error_array();
            $resetStatus = array('message'=> 'Reset Pasword Fail','status'=> 'error','errors'=> $errors);
                $this->response($resetStatus, REST_Controller::HTTP_OK);
        }else{
            $id = $this->input->get_request_header('User-ID', TRUE);
            $user = $this->db->select('*')->from('sma_users')->where('id',$id)->get()->row();      
            $identity = $user->email;

            $change = $this->ion_auth->reset_password($identity, $this->input->post('new'));
            if ($change) {
                    //if the password was successfully changed
                $responseStatus = array('message'=>  $this->ion_auth->messages(),'status'=> "error");
                $this->response($responseStatus, REST_Controller::HTTP_OK);
            } else {
                $responseStatus = array('message'=>  $this->ion_auth->errors(),'status'=> "error");
                $this->response($responseStatus, REST_Controller::HTTP_OK);
            }
        }
        
    }



    public function change_password_post(){

        $this->form_validation->set_rules('old_password', lang('old_password'), 'required');
        $this->form_validation->set_rules('new_password', lang('new_password'), 'required|min_length[8]|max_length[25]');
        $this->form_validation->set_rules('new_password_confirm', lang('confirm_password'), 'required|matches[new_password]');

        //$user = $this->ion_auth->user()->row();

        if ($this->form_validation->run() == false) {
            $errors = $this->form_validation->error_array();
            $errorResponse = array('message'=> 'Change Pasword Fail','status'=> 'error','errors'=> $errors);
                $this->response($errorResponse, REST_Controller::HTTP_OK);
        }else{
            $id = $this->input->get_request_header('User-ID', TRUE);
            $user = $this->db->select('*')->from('sma_users')->where('id',$id)->get()->row();      
            $identity = $user->email;

            $change   = $this->ion_auth->change_password($identity, $this->input->post('old_password'), $this->input->post('new_password'));

            if ($change) {

                $responseStatus = array('message'=>  $this->ion_auth->messages(),'status'=> "success");
                $this->response($responseStatus, REST_Controller::HTTP_OK);
            } else {

                $responseStatus = array('message'=>  $this->ion_auth->errors(),'status'=> "error");
                $this->response($responseStatus, REST_Controller::HTTP_OK);
            }
        }    
    }

    public function update_profile_post(){       
        $this->form_validation->set_rules('first_name', lang('first_name'), 'required');
        $this->form_validation->set_rules('last_name', lang('last_name'), 'required');
        $this->form_validation->set_rules('phone', lang('phone'), 'required');
        $this->form_validation->set_rules('email', lang('email'), 'required|valid_email');
        $this->form_validation->set_rules('company', lang('company'), 'trim');
        //$this->form_validation->set_rules('vat_no', lang('vat_no'), 'trim');
        //$this->form_validation->set_rules('address', lang('billing_address'), 'required');
        // $this->form_validation->set_rules('city', lang('city'), 'required');
        // $this->form_validation->set_rules('state', lang('state'), 'required');
        // $this->form_validation->set_rules('postal_code', lang('postal_code'), 'required');
        // $this->form_validation->set_rules('country', lang('country'), 'required');
    
        if ($this->form_validation->run() == false) {
            $errors = $this->form_validation->error_array();
            $errorResponse = array('message'=> 'Update Profile Fail','status'=> 'error','errors'=> $errors);
            $this->response($errorResponse, REST_Controller::HTTP_OK);
        }else{
            
            $user_id = $this->input->post('user_id');
            $data = [
                'first_name'     => $this->input->post('first_name'),
                'last_name'      => $this->input->post('last_name'),
                'company'        => $this->input->post('company'),
                'email'          => $this->input->post('email'),
                'username'          => $this->input->post('username'),
                'phone'          => $this->input->post('phone'),
                'gender'         => $this->input->post('gender'),
                'biller_id'      => "0",
                'warehouse_id'   => "0",
                'view_right'     => "0",
                'edit_right'     => "0",
                'allow_discount' => "0",
            ];


           if ($this->ion_auth->update($user_id, $data)) {
                $messageResponse = array('message'=> 'User Update','status'=> 'success');
                $this->response($messageResponse, REST_Controller::HTTP_OK);
           }else{
                $messageResponse = array('message'=> 'User Not Update','status'=> 'error');
                $this->response($messageResponse, REST_Controller::HTTP_OK);
           }
        }
        
        
    }

    public function change_profile_picture_post(){  
        $id = $this->input->get_request_header('User-ID', TRUE);
        $user = $this->db->select('*')->from('sma_users')->where('id',$id)->get()->row();           
        //validate form input
        $this->form_validation->set_rules('avatar', lang('avatar'), 'trim');

        if ($this->form_validation->run() == true) {
            if ($_FILES['avatar']['size'] > 0) {
                $this->load->library('upload');

                $config['upload_path']   = 'assets/uploads/avatars';
                $config['allowed_types'] = 'gif|jpg|png';
                //$config['max_size'] = '500';
                $config['max_width']    = $this->Settings->iwidth;
                $config['max_height']   = $this->Settings->iheight;
                $config['overwrite']    = false;
                $config['encrypt_name'] = true;
                $config['max_filename'] = 25;

                $this->upload->initialize($config);

                if (!$this->upload->do_upload('avatar')) {
                    $error = $this->upload->display_errors();
                    $this->session->set_flashdata('error', $error);
                    redirect($_SERVER['HTTP_REFERER']);
                }

                $photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/avatars/' . $photo;
                $config['new_image']      = 'assets/uploads/avatars/thumbs/' . $photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
                
                if($this->auth_model->updateAvatar($id, $photo)){
                    unlink('assets/uploads/avatars/' . $user->avatar);
                    unlink('assets/uploads/avatars/thumbs/' . $user->avatar);

                    $responseStatus = array('message'=> lang('avatar_updated'),'status'=> "success");
                    $this->response($responseStatus, REST_Controller::HTTP_OK);
                }else{
                    $responseStatus = array('message'=> lang('avatar_not_updated'),'status'=> "error");
                    $this->response($responseStatus, REST_Controller::HTTP_OK);
                }

               
                
            } else {
                
                $errorStatus = array('message'=> 'Avatar required','status'=> 'error','errors'=> $errors);
                $this->response($errorStatus, REST_Controller::HTTP_OK);
            }
        }else{
            $errors = $this->form_validation->error_array();
            $errorStatus = array('message'=> 'Avatar required','status'=> 'error','errors'=> $errors);
            $this->response($errorStatus, REST_Controller::HTTP_OK);
        }
    }

    // public function add_wishlist_post(){       
    //     $product_id = $this->post("product_id");
    //     $id = $this->input->get_request_header('User-ID', TRUE);

    //     if ($this->main_api->addWishlist($product_id)) {
    //         $total = $this->main_api->getWishlist(true);
            

    //         $responseStatus = array('message'=> lang('added_wishlist'),'status'=> "success",'total' => $total);
    //         $this->response($responseStatus, REST_Controller::HTTP_OK);
    //     } else {
    //         $responseStatus = array('message'=> lang('product_exists_in_wishlist'),'status'=> lang('info'),'level' => 'info');
    //         $this->response($responseStatus, REST_Controller::HTTP_OK);
    //     }
    // }

    // public function remove_wishlist_post(){       
    //     $product_id = $this->post("product_id");
    //     $id = $this->input->get_request_header('User-ID', TRUE);

    //     if ($this->main_api->removeWishlist($product_id)) {
    //         $total = $this->main_api->getWishlist(true);
            

    //         $responseStatus = array('message'=> lang('removed_wishlist'),'status'=> "success",'total' => $total);
    //         $this->response($responseStatus, REST_Controller::HTTP_OK);
    //     } else {
    //         $responseStatus = array('message'=> lang('error_occured'),'status'=> lang('error'),'level' => 'error');
    //         $this->response($responseStatus, REST_Controller::HTTP_OK);
    //     }
    // }

    // public function wishlist_get()
    // {
    //     $id = $this->input->get_request_header('User-ID', TRUE);
    //     $this->db->where('user_id',$id);
    //     $products = $this->db->get('wishlist')->result();
    //     $this->load->helper('text');
    //     foreach ($products as $product) {
    //         $item          = $this->main_api->getWishlistProductByID($product->product_id);
    //         $item->details = character_limiter(strip_tags($item->details), 140);
    //         $items[]       = $item;
    //     }
    //     $data['items']      = $products ? $items : null;

    //     if($data){
    //         $this->response($data, REST_Controller::HTTP_OK);
    //     }else{
    //         $this->response(NULL, 404);
    //     }
    // }

    public function carts_get()
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $queryCart = $this->db->select('id')->from('cart')->where('user_id', $user_id)->get()->row();
        if($queryCart){
            $cart_id = $queryCart->id;
            //$data = $this->cart->cart_data_app(true,$cart_id);
            //$data = $this->cart->app_contents(true,$cart_id);

            // $this->sma->send_json(['cart' => $this->cart->cart_data(true), 'status' => lang('success'), 'message' => lang('cart_updated')]);
           
            $cartData = $this->cart->app_contents(true,$cart_id);
            $data = array();
            foreach ($cartData as $row) {
                $row['image_url'] = $uploads_url = base_url('assets/uploads/').$row['image'];
                unset($row['image']);
                array_push($data, $row);
            }
            $this->sma->send_json(['cart'=> $data]);

            // if($data){
            //     $this->response($data, REST_Controller::HTTP_OK);
            // }else{
            //     $this->response(NULL, 404);
            // }
        }else{
            $this->response([
                    'message' => 'No product were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
        }
        
    }

    public function cart_add_post()
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $product_id = $this->post("product_id");

        if ($this->input->post('quantity')) {


            $product = $this->shop_model->getProductForCart($product_id);
            $options = $this->shop_model->getProductVariants($product_id);
            $price   = $this->sma->setCustomerGroupPrice((isset($product->special_price) && !empty($product->special_price) ? $product->special_price : $product->price), $this->customer_group);
            $price   = $this->sma->isPromo($product) ? $product->promo_price : $price;
            $option  = false;
            if (!empty($options)) {
                if ($this->input->post('option')) {
                    foreach ($options as $op) {
                        if ($op['id'] == $this->input->post('option')) {
                            $option = $op;
                        }
                    }
                } else {
                    $option = array_values($options)[0];
                }
                $price = $option['price'] + $price;
            }
            $selected = $option ? $option['id'] : false;
            if (!$this->Settings->overselling && $this->checkProductStock($product, 1, $selected)) {
                if ($this->input->is_ajax_request()) {
                    $this->sma->send_json(['error' => 1, 'message' => lang('item_out_of_stock')]);
                } else {
                    $this->session->set_flashdata('error', lang('item_out_of_stock'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }
            $tax_rate   = $this->site->getTaxRateByID($product->tax_rate);
            $ctax       = $this->site->calculateTax($product, $tax_rate, $price);
            $tax        = $this->sma->formatDecimal($ctax['amount']);
            $price      = $this->sma->formatDecimal($price);
            $unit_price = $this->sma->formatDecimal($product->tax_method ? $price + $tax : $price);
            $id         = $this->Settings->item_addition ? md5($product->id) : md5(microtime());

            $data = [
                'id'         => $id,
                'product_id' => $product->id,
                'qty'        => ($this->input->get('qty') ? $this->input->get('qty') : ($this->input->post('quantity') ? $this->input->post('quantity') : 1)),
                'name'       => $product->name,
                'slug'       => $product->slug,
                'code'       => $product->code,
                'price'      => $unit_price,
                'tax'        => $tax,
                'image'      => $product->image,
                'option'     => $selected,
                'options'    => !empty($options) ? $options : null,
            ];
            if ($this->cart->insert_app($data, $user_id)) {
                if ($this->input->post('quantity')) {
                  
                    $responseStatus = array('message'=> lang('item_added_to_cart'),'status'=> 'success');
                    $this->response($responseStatus, REST_Controller::HTTP_OK);
                } else {
                    $this->cart->cart_data();
                }
            }else{
                $responseStatus = array('message'=> lang('unable_to_add_item_to_cart'),'status'=> 'error');
                $this->response($responseStatus, REST_Controller::HTTP_OK);
            }
           
            
        }
    }

    public function cart_destroy_get()
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $queryCart = $this->db->select('id')->from('cart')->where('user_id', $user_id)->get()->row();
        $cart_id = $queryCart->id;

        if ($this->cart->destroyFromApp($cart_id)) {
           
            $this->sma->send_json(['status' => lang('success'), 'message' => lang('cart_items_deleted')]);
        } else {
            $this->sma->send_json(['status' => lang('error'), 'message' => lang('error_occured')]);
        }
        
    }

    public function cart_remove_get($rowid = null)
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $queryCart = $this->db->select('id')->from('cart')->where('user_id', $user_id)->get()->row();
        $cart_id = $queryCart->id;
        if ($this->cart->removeFromApp($rowid, $cart_id, $user_id)) {
            $cartData = $this->cart->app_contents(true,$cart_id);
            $data = array();
            foreach ($cartData as $row) {
                $row['image_url'] = $uploads_url = base_url('assets/uploads/').$row['image'];
                unset($row['image']);
                array_push($data, $row);
            }
            $this->sma->send_json(['cart'=> $data, 'status' => lang('success'), 'message' => lang('cart_item_deleted')]);

            //$this->sma->send_json(['cart' => $this->cart->cart_data(true), 'status' => lang('success'), 'message' => lang('cart_item_deleted')]);
        }
        
    }

    public function cart_update_post($data = null)
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $queryCart = $this->db->select('id')->from('cart')->where('user_id', $user_id)->get()->row();
        $cart_id = $queryCart->id;

        if ($rowid = $this->input->post('rowid', true)) {
            $item = $this->cart->get_item($rowid);
            // $product = $this->site->getProductByID($item['product_id']);
            $product = $this->shop_model->getProductForCart($item['product_id']);
            $options = $this->shop_model->getProductVariants($product->id);
            $price   = $this->sma->setCustomerGroupPrice(($product->special_price ?? $product->price), $this->customer_group);
            $price   = $this->sma->isPromo($product) ? $product->promo_price : $price;
            // $price = $this->sma->isPromo($product) ? $product->promo_price : $product->price;
            if ($option = $this->input->post('option')) {
                foreach ($options as $op) {
                    if ($op['id'] == $option) {
                        $price = $price + $op['price'];
                    }
                }
            }
            $selected = $this->input->post('option') ? $this->input->post('option', true) : false;
            if ($this->checkProductStock($product, $this->input->post('qty', true), $selected)) {
                if ($this->input->is_ajax_request()) {
                    $this->sma->send_json(['error' => 1, 'message' => lang('item_stock_is_less_then_order_qty')]);
                } else {
                    $this->session->set_flashdata('error', lang('item_stock_is_less_then_order_qty'));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            }

            $tax_rate   = $this->site->getTaxRateByID($product->tax_rate);
            $ctax       = $this->site->calculateTax($product, $tax_rate, $price);
            $tax        = $this->sma->formatDecimal($ctax['amount']);
            $price      = $this->sma->formatDecimal($price);
            $unit_price = $this->sma->formatDecimal($product->tax_method ? $price + $tax : $price);

            $data = [
                'rowid'  => $rowid,
                'price'  => $unit_price,
                'tax'    => $tax,
                'qty'    => $this->input->post('quantity', true),
                'option' => $selected,
            ];
            if ($this->cart->update_app($data,$cart_id,$user_id)) {
                //$this->sma->send_json(['cart' => $this->cart->cart_data(true), 'status' => lang('success'), 'message' => lang('cart_updated')]);
                $cartData = $this->cart->app_contents(true,$cart_id);
                $data = array();
                foreach ($cartData as $row) {
                    $row['image_url'] = $uploads_url = base_url('assets/uploads/').$row['image'];
                    unset($row['image']);
                    array_push($data, $row);
                }
                $this->sma->send_json(['cart'=> $data, 'status' => lang('success'), 'message' => lang('cart_updated')]);
                
            }
        }
    }

    // Add/edit customer address
    public function address_post($id = null)
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $user = $this->db->select('*')->from('sma_users')->where('id',$user_id)->get()->row();  

        $data = [
            'line1'  => $this->input->post('line1'),
            'line2'       => $this->input->post('line2'),
            'phone'       => $this->input->post('phone'),
            'city'        => $this->input->post('city'),
            'state'       => $this->input->post('state'),
            'postal_code' => $this->input->post('postal_code'),
            'country'     => $this->input->post('country'),
            'company_id'  => $user->company_id,
            'lat'  => $this->input->post('lat'),
            'lng'  => $this->input->post('lng'),
             ];

        if ($id) {
            $this->db->update('addresses', $data, ['id' => $id]);
            $responseStatus = array('message'=> lang('address_updated'),'status'=> 'success');
            $this->response($responseStatus, REST_Controller::HTTP_OK);
        } else {
            $this->db->insert('addresses', $data);
            $responseStatus = array('message'=> lang('address_added'),'status'=> 'success');
            $this->response($responseStatus, REST_Controller::HTTP_OK);
        }
        
    }

    public function delete_address_get()
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $id = $this->get('id');
        $this->db->delete('addresses',['id' => $id]);
        if($this->db->affected_rows() > 0) {
            $responseStatus = array('message'=> lang('address_deleted'),'status'=> 'success');
            $this->response($responseStatus, REST_Controller::HTTP_OK);
        } else {
            $responseStatus = array('message'=> lang('address_not_deleted'),'status'=> 'error');
            $this->response($responseStatus, REST_Controller::HTTP_OK);
        }
        
    }

    // Customer address list
    public function addresses_get()
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
       
        $user = $this->db->select('*')->from('sma_users')->where('id',$user_id)->get()->row();  
        
        $addresses = $this->db->get_where('addresses', ['company_id' => $user->company_id])->result();
        $data = ['addresses' => $addresses];

        if($data){
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
        
    }



    // Customer address list
    public function coupons_get()
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        
        $user = $this->db->select('*')->from('sma_users')->where('id', $user_id)->get()->row();  
        
        $coupons = $this->db->where('customer_id',$user->id)->or_where('customer_id',0)->where('status',1)->get('app_coupons')->result();
        $data = [
            'coupons' => $coupons,
        ];

        if($data){
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
        
    }

    public function check_coupon_post()
    {
        $code = $this->post('code');
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $user = $this->db->select('*')->from('sma_users')->where('id', $user_id)->get()->row();  
        
        $this->db->where('status',1);
        $this->db->where('code', $code);
        $this->db->where('customer_id', $user->id);
       
        $coupon = $this->db->get('app_coupons')->row();
        if($coupon){
            $data = [
                'coupon' => $coupon,
            ];
            if($data){
                $this->response($data, REST_Controller::HTTP_OK);
            }else{
                $this->response(NULL, 404);
            }
        }else{
            $today = date('Y-m-d');
            $this->db->where('code', $code);
            $this->db->where('start_date <=', $today)->where('end_date >=', $today);
            $coupon = $this->db->get('app_coupons')->row();

            $data = [
                'coupon' => $coupon,
            ];
            if($data){
                $this->response($data, REST_Controller::HTTP_OK);
            }else{
                $this->response(NULL, 404);
            }
        }
        
        
    }

    // Customer address list
    public function distance_prices_get()
    {
        
        $distance_prices = $this->db->select('*')->from('app_distance_prices')->order_by('id','desc')->get()->result();
        $data = [
            'distance_prices' => $distance_prices,
        ];

        if($data){
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
        
    }

    public function estimate_distance_price_get(){
        
        $distance = $this->get('distance');
        $query = $this->db->query("SELECT * FROM `sma_app_distance_prices` WHERE $distance BETWEEN `from_km` AND `to_km` LIMIT 1");
        $row = $query->row();
        $price = $this->shop_settings->shipping;
        if (isset($row)){
            $price = $row->price;
        }
        
        $data = array('status'=> 'success','message'=>'Success get distance price estimate','price'=> $price);
        $this->response($data, REST_Controller::HTTP_OK);
    }

    public function checkout_post()
    {   
        $this->form_validation->set_rules('address', lang('address'), 'trim|required');
        $this->form_validation->set_rules('comment', lang('comment'), 'trim');
        $this->form_validation->set_rules('payment_method', lang('payment_method'), 'required');
        

        if ($this->form_validation->run() == true) {
            if ($address = $this->shop_model->getAddressByID($this->input->post('address'))) {
                
                $biller      = $this->site->getCompanyByID($this->shop_settings->biller);
                $note        = $this->db->escape_str($this->input->post('comment'));
                $product_tax = 0;
                $total       = 0;
                $gst_data    = [];
                $total_cgst  = $total_sgst  = $total_igst  = 0;

                $user_id = $this->input->get_request_header('User-ID', TRUE);
                $queryCart = $this->db->select('id')->from('cart')->where('user_id', $user_id)->get()->row();
                if($queryCart){
                    $cart_id = $queryCart->id;
                    foreach ($this->cart->app_contents(false, $cart_id) as $item) {
                        $item_option = null;
                        if ($product_details = $this->shop_model->getProductForCart($item['product_id'])) {
                            $price = $this->sma->setCustomerGroupPrice((isset($product_details->special_price) ? $product_details->special_price : $product_details->price), $this->customer_group);
                            $price = $this->sma->isPromo($product_details) ? $product_details->promo_price : $price;
                            if ($item['option']) {
                                if ($product_variant = $this->shop_model->getProductVariantByID($item['option'])) {
                                    $item_option = $product_variant->id;
                                    $price       = $product_variant->price + $price;
                                }
                            }

                            $item_net_price = $unit_price = $price;
                            $item_quantity  = $item_unit_quantity  = $item['qty'];
                            $pr_item_tax    = $item_tax    = 0;
                            $tax            = '';

                            if (!empty($product_details->tax_rate)) {
                                $tax_details = $this->site->getTaxRateByID($product_details->tax_rate);
                                $ctax        = $this->site->calculateTax($product_details, $tax_details, $unit_price);
                                $item_tax    = $ctax['amount'];
                                $tax         = $ctax['tax'];
                                if ($product_details->tax_method != 1) {
                                    $item_net_price = $unit_price - $item_tax;
                                }
                                $pr_item_tax = $this->sma->formatDecimal(($item_tax * $item_unit_quantity), 4);
                                // if ($this->Settings->indian_gst && $gst_data = $this->gst->calculateIndianGST($pr_item_tax, ($biller->state == $customer->state), $tax_details)) {
                                //     $total_cgst += $gst_data['cgst'];
                                //     $total_sgst += $gst_data['sgst'];
                                //     $total_igst += $gst_data['igst'];
                                // }
                            }

                            $product_tax += $pr_item_tax;
                            $subtotal = (($item_net_price * $item_unit_quantity) + $pr_item_tax);

                            $unit = $this->site->getUnitByID($product_details->unit);

                            $product = [
                                'product_id'        => $product_details->id,
                                'product_code'      => $product_details->code,
                                'product_name'      => $product_details->name,
                                'product_type'      => $product_details->type,
                                'option_id'         => $item_option,
                                'net_unit_price'    => $item_net_price,
                                'unit_price'        => $this->sma->formatDecimal($item_net_price + $item_tax),
                                'quantity'          => $item_quantity,
                                'product_unit_id'   => $unit ? $unit->id : null,
                                'product_unit_code' => $unit ? $unit->code : null,
                                'unit_quantity'     => $item_unit_quantity,
                                'warehouse_id'      => $this->shop_settings->warehouse,
                                'item_tax'          => $pr_item_tax,
                                'tax_rate_id'       => $product_details->tax_rate,
                                'tax'               => $tax,
                                'discount'          => null,
                                'item_discount'     => 0,
                                'subtotal'          => $this->sma->formatDecimal($subtotal),
                                'serial_no'         => null,
                                'real_unit_price'   => $price,
                            ];

                            $products[] = ($product + $gst_data);
                            $total += $this->sma->formatDecimal(($item_net_price * $item_unit_quantity), 4);
                        } else {
                            $this->session->set_flashdata('error', lang('product_x_found') . ' (' . $item['name'] . ')');
                            redirect($_SERVER['HTTP_REFERER'] ?? 'cart');
                        }
                    }

                    $shipping_fee = $this->input->post('shipping');
                    if($shipping_fee && $shipping_fee != ""){
                        $shipping = $shipping_fee;
                    }else{
                        $shipping    = $this->shop_settings->shipping;
                    }
                    $product_discount = 0;
                    $order_discount = $this->site->calculateDiscount($this->input->post('order_discount'), ($total + $product_tax), true);
                    $total_discount = $this->sma->formatDecimal(($order_discount + $product_discount), 4);

                    
                    $order_tax   = $this->site->calculateOrderTax($this->Settings->default_tax_rate2, ($total + $product_tax));
                    $total_tax   = $this->sma->formatDecimal(($product_tax + $order_tax), 4);
                    $grand_total = $this->sma->formatDecimal(($total + $total_tax + $shipping)-$this->sma->formatDecimal($order_discount), 4);

                    $data = [
                        'date'              => date('Y-m-d H:i:s'),
                        'reference_no'      => $this->site->getReference('so'),
                        'customer_id'       => $this->customer->id ?? '',
                        'customer'          => ($this->customer->company && $this->customer->company != '-' ? $this->customer->company : $this->customer->name),
                        'biller_id'         => $biller->id,
                        'biller'            => ($biller->company && $biller->company != '-' ? $biller->company : $biller->name),
                        'warehouse_id'      => $this->shop_settings->warehouse,
                        'note'              => $note,
                        'staff_note'        => null,
                        'total'             => $total,
                        'product_discount'  => $product_discount,
                        'order_discount_id' => $this->input->post('order_discount'),
                        'order_discount'    => $order_discount,
                        'total_discount'    => $total_discount,
                        'product_tax'       => $product_tax,
                        'order_tax_id'      => $this->Settings->default_tax_rate2,
                        'order_tax'         => $order_tax,
                        'total_tax'         => $total_tax,
                        'shipping'          => $shipping,
                        'grand_total'       => $grand_total,
                        'total_items'       => $this->cart->total_items(),
                        'sale_status'       => 'pending',
                        'payment_status'    => 'pending',
                        'payment_term'      => null,
                        'due_date'          => null,
                        'paid'              => 0,
                        'created_by'        => $this->session->userdata('user_id') ? $this->session->userdata('user_id') : null,
                        'shop'              => 1,
                        'address_id'        => ($this->input->post('address') == 'new') ? '' : $address->id,
                        'hash'              => hash('sha256', microtime() . mt_rand()),
                        'payment_method'    => $this->input->post('payment_method'),
                    ];
                    if ($this->Settings->invoice_view == 2) {
                        $data['cgst'] = $total_cgst;
                        $data['sgst'] = $total_sgst;
                        $data['igst'] = $total_igst;
                    }
                   

                    if ($sale_id = $this->shop_model->addSale($data, $products, $this->customer, $address)) {
                        // $email = $this->order_received($sale_id, $data['hash']);
                        // if (!$email['sent']) {
                        //     $this->session->set_flashdata('error', $email['error']);
                        // }
                        // $this->load->library('sms');
                        // $this->sms->newSale($sale_id);
                        $this->cart->destroyFromApp($cart_id);

                        $responseStatus = array('message'=> lang('order_added_success'),'status'=> 'success');
                        $this->response($responseStatus, REST_Controller::HTTP_OK);
                        
                    }else{
                        $responseStatus = array('message'=> lang('order_added_fail'),'status'=> 'error');
                        $this->response($responseStatus, REST_Controller::HTTP_OK);
                    }
                }
                
            } else {
                $responseStatus = array('message'=> lang('address_x_found'),'status'=> 'success');
                $this->response($responseStatus, REST_Controller::HTTP_OK);
            }
        }else{
            $errors = $this->form_validation->error_array();
            $respStatus = array('message'=> 'Checkout Fail','status'=> 'error','errors'=> $errors);
            $this->response($respStatus, REST_Controller::HTTP_OK);
        }
    }

    // Customer order/orders page
    // public function order_detail_get($id = null)
    // {
    //     if ($order = $this->main_api->getOrder(['id' => $id])) {
    //         $this->data['inv']         = $order;
    //         $this->data['rows']        = $this->main_api->getOrderItems($id);
    //         $this->data['customer']    = $this->site->getCompanyByID($order->customer_id);
    //         $this->data['biller']      = $this->site->getCompanyByID($order->biller_id);
    //         $this->data['address']     = $this->shop_model->getAddressByID($order->address_id);
    //         $this->data['return_sale'] = $order->return_id ? $this->main_api->getOrder(['id' => $id]) : null;
    //         $this->data['return_rows'] = $order->return_id ? $this->main_api->getOrderItems($order->return_id) : null;
            
    //         if($this->data){
    //             $this->response($this->data, REST_Controller::HTTP_OK);
    //         }else{
    //             $this->response(NULL, 404);
    //         }
    //     } 

    // }

    // Customer order/orders page
    // public function orders_get()
    // {
        
    //     $filters = [
    //         'start'       => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
    //         'limit'       => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : 10,
    //         'start_date'  => $this->get('start_date') ? $this->get('start_date') : null,
    //         'end_date'    => $this->get('end_date') ? $this->get('end_date') : null,
    //         'order_by'    => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'decs'],
    //     ];

    //     $sl_data     = $this->main_api->getOrders($filters);
    //     $data = [
    //         'data'  => $sl_data,
    //         'limit' => (int) $filters['limit'],
    //         'start' => (int) $filters['start'],
    //         'total' => $this->main_api->getOrdersCount($filters),
    //     ];

    //     if($data){
    //         $this->response($data, REST_Controller::HTTP_OK);
    //     }else{
    //         $this->response(NULL, 404);

    //     }
        
    // }

    // Display Page
    // public function product_detail_get($code)
    // {
    //     $product = $this->shop_model->getProductByCode($code);
    //     if (!$product) {
    //         $responseStatus = array('message'=> lang('product_not_found'),'status'=> 'success');
    //         $this->response($responseStatus, REST_Controller::HTTP_OK);
    //     }else{
    //         if ($product->type == 'combo') {
    //             $this->data['combo_items'] = $this->shop_model->getProductComboItems($product->id);
    //         }
    //         $this->shop_model->updateProductViews($product->id, $product->views);
    //         $this->data['product']        = $product;
    //         $this->data['other_products'] = $this->main_api->getOtherProducts($product->id, $product->category_id, $product->brand);
    //         $this->data['unit']           = $this->main_api->getUnitByID($product->unit);
    //         $this->data['brand']          = $this->main_api->getBrandByID($product->brand);
    //         $this->data['images']         = $this->products_api->getProductPhotos($product->id);
    //         $this->data['category']       = $this->main_api->getCategoryByID($product->category_id);
    //         $this->data['subcategory']    = $product->subcategory_id ? $this->site->getCategoryByID($product->subcategory_id) : null;
    //         $this->data['tax_rate']       = $product->tax_rate ? $this->site->getTaxRateByID($product->tax_rate) : null;
    //         $this->data['warehouse']      = $this->shop_model->getAllWarehouseWithPQ($product->id);
    //         $this->data['options']        = $this->shop_model->getProductOptionsWithWH($product->id);
    //         $this->data['variants']       = $this->shop_model->getProductOptions($product->id);
    //         //$this->data['variants']       = $this->shop_model->getProductOptions($product->id);
    //         $this->data['variant_options'] = $this->products_api->getProductVariantOptionValues($product->id);
                                  
    //         $this->data['range_prices']  = $this->products_api->getProductRangePrices($product->id);
    //         $this->data['variant_combinations']  = $this->products_api->getProductVariantCombination($product->id);

    //         $this->load->helper('text');
    //         $this->data['page_title'] = $product->code . ' - ' . $product->name;
    //         $this->data['page_desc']  = character_limiter(strip_tags($product->product_details), 160);

    //         if($this->data){
    //             $this->response($this->data, REST_Controller::HTTP_OK);
    //         }else{
    //             $this->response(NULL, 404);
    //         }
    //     }

        
    // }

    // public function search_product_post()
    // {
    //     $category_id = $this->post('category_id');
    //     $subcategory_id = $this->post('subcategory_id');
    //     $brand_id = $this->post('brand_id');
    //     $promo = null;
    //     $featured = null;
    //     if ($this->input->post('promo') && $this->input->post('promo') == '1') {
    //         $promo = true;
    //     }
    //     if ($this->input->post('featured') && $this->input->post('featured') == '1') {
    //         $featured = true;
    //     }
    

    //     $filters = [
    //         'query'       => $this->input->post('query'),
    //         'category'    => $category_id ? $category_id : null,
    //         'subcategory' => $subcategory_id ? $subcategory_id : null,
    //         'brand'       => $brand_id ? $brand_id : null,
    //         'promo'       => $promo,
    //         'order_by'    => $this->input->post('order_by'),
    //         'min_price'   => $this->input->post('min_price'),
    //         'max_price'   => $this->input->post('max_price'),
    //         'in_stock'    => $this->input->post('in_stock'),
    //         'featured'    => $featured,
    //     ];

    //     $filters['limit']  = $this->post('limit') ? $this->post('limit') : 10;
    //     $filters['offset'] = $this->post('offset') ? $this->post('offset') : 0;

    //     $products = $this->main_api->getProducts($filters);


    //     if($products){
    //         $this->response($products, REST_Controller::HTTP_OK);
    //     }else{
    //         $this->response([
    //             'message' => 'No product were found.',
    //             'status'  => false,
    //         ], REST_Controller::HTTP_NOT_FOUND);
    //     }
    // }













    /**
     * Users Registration with Google
    */
    function google_register_post(){
        $method = $_SERVER['REQUEST_METHOD'];
        if($method != 'POST'){
            json_output(400,array('status' => 400,'success'=> false,'message' => 'Bad request.'));
        } else {
            $company = "N/A";
            $phone = "";
            $password = "";
            $username = $this->input->post("username");
            $first_name = $this->input->post("first_name");
            $last_name = $this->input->post("last_name");
            $email = $this->input->post("email");
            $google_id = $this->input->post("google_id");
            $url = $this->input->post("image");
            $player_id = $this->input->post("player_id");

            if(empty($google_id)){
                //response field required
            }else{
                $existUser = $this->db->get_where('users', array('google_id'=> $google_id));
                if (!$existUser->num_rows() > 0){
                    if ($url !="") {
                        $data = file_get_contents($url);
                        $dir = "assets/uploads/avatars/";
                        $img = md5(time()).'.jpg';
                        $ch = curl_init($url);
                        $fp = fopen( 'assets/uploads/avatars/'. $img, 'wb' );
                        curl_setopt($ch, CURLOPT_FILE, $fp);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_exec($ch);
                        curl_close($ch);
                        fclose($fp);



                        $customer_group = $this->shop_model->getCustomerGroup($this->Settings->customer_group);
                        $price_group    = $this->shop_model->getPriceGroup($this->Settings->price_group);

                        $company_data = [
                            'company'             => $company,
                            'name'                => $username,
                            'email'               => $email,
                            'phone'               => $phone,
                            'group_id'            => 3,
                            'group_name'          => 'customer',
                            'customer_group_id'   => (!empty($customer_group)) ? $customer_group->id : null,
                            'customer_group_name' => (!empty($customer_group)) ? $customer_group->name : null,
                            'price_group_id'      => (!empty($price_group)) ? $price_group->id : null,
                            'price_group_name'    => (!empty($price_group)) ? $price_group->name : null
                        ];

                        $company_id = $this->shop_model->addCustomer($company_data);

                        $additional_data = [
                            'first_name' => $first_name,
                            'last_name'  => $last_name,
                            'phone'      => $phone,
                            'company'    => $company,
                            'gender'     => 'male',
                            'company_id' => $company_id,
                            'group_id'   => 3,
                            'player_id'    => $player_id,
                            'avatar' => $img,
                            'google_id' => $google_id,
                            'google_verify' => 1
                        ];
                        
                        if ($this->ion_auth->register($username, $password, $email, $additional_data)) {
                            $data = $this->db->select('*')->from('users')->where('company_id',$company_id)->get()->row();
                            
                            $registerStatus = array('message'=> 'Register Success','status'=> 'success','data'=>$data);
                            $this->response($registerStatus, REST_Controller::HTTP_OK);

                        }else{
                           
                            $registerStatus = array('message'=> 'Register Fail','status'=> 'error','data'=>$data);
                            $this->response($registerStatus, REST_Controller::HTTP_OK);
                        }
                        
                    }else{
                        $customer_group = $this->shop_model->getCustomerGroup($this->Settings->customer_group);
                        $price_group    = $this->shop_model->getPriceGroup($this->Settings->price_group);

                        $company_data = [
                            'company'             => $company,
                            'name'                => $username,
                            'email'               => $email,
                            'phone'               => $phone,
                            'group_id'            => 3,
                            'group_name'          => 'customer',
                            'customer_group_id'   => (!empty($customer_group)) ? $customer_group->id : null,
                            'customer_group_name' => (!empty($customer_group)) ? $customer_group->name : null,
                            'price_group_id'      => (!empty($price_group)) ? $price_group->id : null,
                            'price_group_name'    => (!empty($price_group)) ? $price_group->name : null
                        ];

                        $company_id = $this->shop_model->addCustomer($company_data);

                        $additional_data = [
                            'first_name' => $first_name,
                            'last_name'  => $last_name,
                            'phone'      => $phone,
                            'company'    => $company,
                            'gender'     => 'male',
                            'company_id' => $company_id,
                            'group_id'   => 3,
                            'player_id'    => $player_id,
                            'google_id' => $google_id,
                            'google_verify' => 1
                        ];
                        
                        if ($this->ion_auth->register($username, $password, $email, $additional_data)) {
                            $data = $this->db->select('*')->from('users')->where('company_id',$company_id)->get()->row();
                            $registerStatus = array('message'=> 'Register Success','status'=> 'success','data'=>$data);
                            $this->response($registerStatus, REST_Controller::HTTP_OK);

                        }else{
                           
                            $registerStatus = array('message'=> 'Register Fail','status'=> 'error');
                            $this->response($registerStatus, REST_Controller::HTTP_OK);
                        }
                    }

                }else{
                    $userInfo = $existUser->row();
                    //User already exist in DB
                    
                    $user_profile_photo = $this->db->get_where('users', array('google_id'=> $google_id))->row()->avatar;
                    
                    //Download again
                    
                    if ($url !="") {
                        //Delete existing image 
                        @unlink('./assets/img/users/'.$user_profile_photo);

                        $data = file_get_contents($url);
                        $dir = "assets/uploads/avatars/";
                        $img = md5(time()).'.jpg';
                        $ch = curl_init($url);
                        $fp = fopen( 'assets/uploads/avatars/'. $img, 'wb' );
                        curl_setopt($ch, CURLOPT_FILE, $fp);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_exec($ch);
                        curl_close($ch);
                        fclose($fp);

                        $user_data = array(
                            'username'      => $username, 
                            'email'    => $email,
                            'avatar' => $img,    
                            "player_id" => $player_id
                        );
                        $this->db->update('users',$user_data, array('id'=>$userInfo->id));

                        $data = $this->db->select('*')->from('users')->where('id',$userInfo->id)->get()->row();
                        $registerStatus = array('message'=> 'Login Success','status'=> 'success','data'=>$data);
                            $this->response($registerStatus, REST_Controller::HTTP_OK);


                    } else {
                        $user_data = array(
                            'username'      => $username, 
                            'email'    => $email,
                            "player_id" => $player_id
                        );
                        $this->db->update('users',$user_data, array('id'=>$userInfo->id));
                        $data = $this->db->select('*')->from('users')->where('id',$userInfo->id)->get()->row();
                        $registerStatus = array('message'=> 'Login Success','status'=> 'success','data'=>$data);
                            $this->response($registerStatus, REST_Controller::HTTP_OK);
                    }
                    
                    
                }
            }

        }

    }


    /**
     * Users Registration with Facebook
    */
    function facebook_register_post(){
        $method = $_SERVER['REQUEST_METHOD'];
        if($method != 'POST'){
            json_output(400,array('status' => 400,'success'=> false,'message' => 'Bad request.'));
        } else {
            $company = "N/A";
            $phone = "";
            $password = "";
            $username = $this->input->post("username");
            $first_name = $this->input->post("first_name");
            $last_name = $this->input->post("last_name");
            $email = $this->input->post("email");
            $facebook_id = $this->input->post("facebook_id");
            $url = $this->input->post("image");
            $player_id = $this->input->post("player_id");

            if(empty($facebook_id)){
                //response field required
            }else{
                $existUser = $this->db->get_where('users', array('facebook_id'=> $facebook_id));
                if (!$existUser->num_rows() > 0){
                     //User not yet exist 
                    //$fb_id = $facebook_id;
                    //$url = "https://graph.facebook.com/$fb_id/picture?width=350&height=500";
                    $data = file_get_contents($url);
                    $dir = "assets/uploads/avatars/";
                    $img = md5(time()).'.jpg';
                    $ch = curl_init($url);
                    $fp = fopen( 'assets/uploads/avatars/'. $img, 'wb' );
                    curl_setopt($ch, CURLOPT_FILE, $fp);
                    curl_setopt($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                    curl_exec($ch);
                    curl_close($ch);
                    fclose($fp);



                    $customer_group = $this->shop_model->getCustomerGroup($this->Settings->customer_group);
                    $price_group    = $this->shop_model->getPriceGroup($this->Settings->price_group);

                    $company_data = [
                        'company'             => $company,
                        'name'                => $username,
                        'email'               => $email,
                        'phone'               => $phone,
                        'group_id'            => 3,
                        'group_name'          => 'customer',
                        'customer_group_id'   => (!empty($customer_group)) ? $customer_group->id : null,
                        'customer_group_name' => (!empty($customer_group)) ? $customer_group->name : null,
                        'price_group_id'      => (!empty($price_group)) ? $price_group->id : null,
                        'price_group_name'    => (!empty($price_group)) ? $price_group->name : null
                    ];

                    $company_id = $this->shop_model->addCustomer($company_data);

                    $additional_data = [
                        'first_name' => $first_name,
                        'last_name'  => $last_name,
                        'phone'      => $phone,
                        'company'    => $company,
                        'gender'     => 'male',
                        'company_id' => $company_id,
                        'group_id'   => 3,
                        'player_id'    => $player_id,
                        'avatar' => $img,
                        'facebook_id' => $facebook_id,
                        'facebook_verify' => 1
                    ];
                    
                    if ($this->ion_auth->register($username, $password, $email, $additional_data)) {
                        $data = $this->db->select('*')->from('users')->where('company_id',$company_id)->get()->row();
                        
                        $registerStatus = array('message'=> 'Register Success','status'=> 'success','data'=>$data);
                        $this->response($registerStatus, REST_Controller::HTTP_OK);

                    }else{
                       
                        $registerStatus = array('message'=> 'Register Fail','status'=> 'error','data'=>$data);
                        $this->response($registerStatus, REST_Controller::HTTP_OK);
                    }


                }else{
                    $userInfo = $existUser->row();
                    //User already exist in DB
                    
                    $user_profile_photo = $this->db->get_where('users', array('facebook_id'=> $facebook_id))->row()->avatar;
                    
                    //Download again
                    
                    if ($url !="") {
                        //Delete existing image 
                        @unlink('./assets/img/users/'.$user_profile_photo);

                        $data = file_get_contents($url);
                        $dir = "assets/uploads/avatars/";
                        $img = md5(time()).'.jpg';
                        $ch = curl_init($url);
                        $fp = fopen( 'assets/uploads/avatars/'. $img, 'wb' );
                        curl_setopt($ch, CURLOPT_FILE, $fp);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_exec($ch);
                        curl_close($ch);
                        fclose($fp);

                        $user_data = array(
                            'username'      => $username, 
                            'email'    => $email,
                            'avatar' => $img,    
                            "player_id" => $player_id
                        );
                        $this->db->update('users',$user_data, array('id'=>$userInfo->id));

                        $data = $this->db->select('*')->from('users')->where('id',$userInfo->id)->get()->row();
                        $registerStatus = array('message'=> 'Login Success','status'=> 'success','data'=>$data);
                            $this->response($registerStatus, REST_Controller::HTTP_OK);


                    }
                    
                }
            }

        }

    }


    /**
     * Users Registration with Apple
    */

    function apple_register_post(){

        $method = $_SERVER['REQUEST_METHOD'];
        if($method != 'POST'){
            json_output(400,array('status' => 400,'success'=> false,'message' => 'Bad request.'));
        } else {

            $company = "N/A";
            $phone = "";
            $password = "";
            $username = $this->input->post("username");
            $first_name = $this->input->post("first_name");
            $last_name = $this->input->post("last_name");
            $email = $this->input->post("email");
            $apple_id = $this->input->post("apple_id");
            $player_id = $this->input->post("player_id");


            if(empty($apple_id)){
                //response field required
            }else{
                $existUser = $this->db->get_where('users', array('apple_id'=> $apple_id));
                if (!$existUser->num_rows() > 0){

                    $customer_group = $this->shop_model->getCustomerGroup($this->Settings->customer_group);
                    $price_group    = $this->shop_model->getPriceGroup($this->Settings->price_group);

                    $company_data = [
                        'company'             => $company,
                        'name'                => $username,
                        'email'               => $email,
                        'phone'               => $phone,
                        'group_id'            => 3,
                        'group_name'          => 'customer',
                        'customer_group_id'   => (!empty($customer_group)) ? $customer_group->id : null,
                        'customer_group_name' => (!empty($customer_group)) ? $customer_group->name : null,
                        'price_group_id'      => (!empty($price_group)) ? $price_group->id : null,
                        'price_group_name'    => (!empty($price_group)) ? $price_group->name : null
                    ];

                    $company_id = $this->shop_model->addCustomer($company_data);

                    $additional_data = [
                        'first_name' => $first_name,
                        'last_name'  => $last_name,
                        'phone'      => $phone,
                        'company'    => $company,
                        'gender'     => 'male',
                        'company_id' => $company_id,
                        'group_id'   => 3,
                        'player_id'    => $player_id,
                        'avatar' => "",
                        'apple_id' => $apple_id,
                        'apple_verify' => 1
                    ];
                    
                    if ($this->ion_auth->register($username, $password, $email, $additional_data)) {
                        $data = $this->db->select('*')->from('users')->where('company_id',$company_id)->get()->row();
                        
                        $registerStatus = array('message'=> 'Register Success','status'=> 'success','data'=>$data);
                        $this->response($registerStatus, REST_Controller::HTTP_OK);

                    }else{
                       
                        $registerStatus = array('message'=> 'Register Fail','status'=> 'error','data'=>$data);
                        $this->response($registerStatus, REST_Controller::HTTP_OK);
                    }


                }else{
                    $userInfo = $existUser->row();
                    //User already exist in DB
                    
                    $user_profile_photo = $this->db->get_where('users', array('apple_id'=> $apple_id))->row()->avatar;
                    
                    
                    $user_data = array(
                        'username'      => $username, 
                        'email'    => $email,
                        'avatar' => '',    
                        "player_id" => $player_id
                    );
                    $this->db->update('users',$user_data, array('id'=>$userInfo->id));

                    $data = $this->db->select('*')->from('users')->where('id',$userInfo->id)->get()->row();
                    $registerStatus = array('message'=> 'Login Success','status'=> 'success','data'=>$data);
                        $this->response($registerStatus, REST_Controller::HTTP_OK);

                }
            }
        }

    }

    private function checkProductStock($product, $qty, $option_id = null)
    {
        if ($product->type == 'service' || $product->type == 'digital') {
            return false;
        }
        $chcek = [];
        if ($product->type == 'standard') {
            $quantity = 0;
            if ($pis = $this->site->getPurchasedItems($product->id, $this->shop_settings->warehouse, $option_id)) {
                foreach ($pis as $pi) {
                    $quantity += $pi->quantity_balance;
                }
            }
            $chcek[] = ($qty <= $quantity);
        } elseif ($product->type == 'combo') {
            $combo_items = $this->site->getProductComboItems($product->id, $this->shop_settings->warehouse);
            foreach ($combo_items as $combo_item) {
                if ($combo_item->type == 'standard') {
                    $quantity = 0;
                    if ($pis = $this->site->getPurchasedItems($combo_item->id, $this->shop_settings->warehouse, $option_id)) {
                        foreach ($pis as $pi) {
                            $quantity += $pi->quantity_balance;
                        }
                    }
                    $chcek[] = (($combo_item->qty * $qty) <= $quantity);
                }
            }
        }
        return empty($chcek) || in_array(false, $chcek);
    }


    public function payment_methods_get()
    {
        
        $banks = $this->db->get('banks')->result();
        $data = ['banks' => $banks];

        if($data){
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
        
    }

    public function find_product_variant_id_post(){

        $product_id = $this->input->post('product_id');
        $variant_value = $this->input->post('variant_value');
        $array_value_id = explode(",",$variant_value);
        //$array_value_id = explode(",",$variant_value);
        $total = count($array_value_id);
        $query = $this->db->query("
            SELECT
                opt.*
            FROM
                sma_z_product_variant_options as opt
                INNER JOIN sma_product_variants as pv ON pv.id = opt.product_variant_id 
            WHERE
                opt.product_id = $product_id
                AND option_value_id IN ($variant_value) 
            GROUP BY
                opt.product_variant_id 
            HAVING
                COUNT( DISTINCT option_value_id ) > ($total-1);");

        $row = $query->row();

        $data = ['product_variant_id' => $row->product_variant_id];

        if($data){
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }

            
    }


































    //=======================MONI Protect=========================//

    public function page_get()
    {
        $slug = $this->get('slug');
        $data = $this->db->select('*')->from('pages')->where('slug',$slug)->get()->row();
        if($data){
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
        
    }

    public function notifications_get()
    {
        $id = $this->input->get_request_header('User-ID', TRUE);
        
        $uploads_url = base_url('assets/uploads/');
        $this->db->select("{$this->db->dbprefix('app_notifications')}.id, {$this->db->dbprefix('app_notifications')}.description, {$this->db->dbprefix('app_notifications')}.name, {$this->db->dbprefix('app_notifications')}.link,  CONCAT('{$uploads_url}', {$this->db->dbprefix('app_notifications')}.image) as image_url, ");

        $notifications = $this->db->get('app_notifications')->result();
        if($notifications){
            $this->response($notifications, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    }

    public function users_get(){
        
        $query = $this->db
            ->select($this->db->dbprefix('users') . '.id as id, first_name, last_name, email, company, award_points, ' . $this->db->dbprefix('groups') . '.name, active,phone,username,gender,group_id')
            ->from('users')
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->group_by('users.id')
            ->where('company_id', null)
            ->where('groups.id !=', 6)
            ->where('groups.id !=', 7)
            ->get();

        $users = $query->result();

        if($users){
            $this->response($users, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    }

    public function agencies_get(){
        
        $query = $this->db
            ->select($this->db->dbprefix('users') . '.id as id, first_name, last_name, email, company, award_points, ' . $this->db->dbprefix('groups') . '.name, active,phone,username,gender,group_id')
            ->from('users')
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->group_by('users.id')
            ->where('groups.id', 6)
            ->get();

        $users = $query->result();

        if($users){
            $this->response($users, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    }

    public function dealers_get(){
        
        $query = $this->db
            ->select($this->db->dbprefix('users') . '.id as id, first_name, last_name, email, company, award_points, ' . $this->db->dbprefix('groups') . '.name, active,phone,username,gender,group_id')
            ->from('users')
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->group_by('users.id')
            ->where('groups.id', 7)
            ->get();

        $users = $query->result();

        if($users){
            $this->response($users, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    }

    public function user_profile_get(){
        $user_id = $this->get('id');
        $profile = $this->db
            ->select($this->db->dbprefix('users') . '.id as id, first_name, last_name, email, company, award_points, ' . $this->db->dbprefix('groups') . '.name, active,phone,username,gender,group_id,first_name,last_name,company,avatar')
            ->from('users')
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->group_by('users.id')
            ->where('company_id', null)
            ->where('users.id', $user_id)
            ->get()->row();

        if($profile){
            $this->response($profile, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    }

    public function customer_profile_get(){
        $user_id = $this->get('id');
        $profile = $this->db
            ->select($this->db->dbprefix('users') . '.id as id, first_name, last_name, email, company, award_points, ' . $this->db->dbprefix('groups') . '.name, active,phone')
            ->from('users')
            ->join('groups', 'users.group_id=groups.id', 'left')
            ->group_by('users.id')
            ->where('company_id', null)
            ->where('users.id', $user_id)
            ->get()->row();

        if($profile){
            $this->response($profile, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    }

    public function create_user_post(){

        $username = strtolower($this->input->post('username'));
        $email    = strtolower($this->input->post('email'));
        $password = $this->input->post('password');
        $notify   = true;

        $additional_data = [
            'first_name'     => $this->input->post('first_name'),
            'last_name'      => $this->input->post('last_name'),
            'company'        => $this->input->post('company'),
            'phone'          => $this->input->post('phone'),
            'gender'         => $this->input->post('gender'),
            'group_id'       => $this->input->post('group') ? $this->input->post('group') : '3',
            'biller_id'      => "0",
            'warehouse_id'   => "0",
            'view_right'     => "0",
            'edit_right'     => "0",
            'allow_discount' => "0",
        ];
        $active = $this->input->post('status');
        
        if ($this->ion_auth->register($username, $password, $email, $additional_data, $active, $notify)) {
            $messageResponse = array('message'=> 'Create Success','status'=> 'success');
            $this->response($messageResponse, REST_Controller::HTTP_OK);

        }else{
            $messageResponse = array('message'=> 'Create Fail','status'=> 'error');
            $this->response($messageResponse, REST_Controller::HTTP_OK);
        }
    }

    public function edit_user_post(){
        $user_id = $this->input->post('user_id');
        $data = [
            'first_name'     => $this->input->post('first_name'),
            'last_name'      => $this->input->post('last_name'),
            'company'        => $this->input->post('company'),
            'email'          => $this->input->post('email'),
            'phone'          => $this->input->post('phone'),
            'gender'         => $this->input->post('gender'),
            'active'         => $this->input->post('status'),
            'group_id'       => $this->input->post('group') ? $this->input->post('group') : '3',
            'biller_id'      => "0",
            'warehouse_id'   => "0",
            'view_right'     => "0",
            'edit_right'     => "0",
            'allow_discount' => "0",
        ];

       if ($this->ion_auth->update($user_id, $data)) {
            $messageResponse = array('message'=> 'User Update','status'=> 'success');
            $this->response($messageResponse, REST_Controller::HTTP_OK);
       }else{
            $messageResponse = array('message'=> 'User Not Update','status'=> 'error');
            $this->response($messageResponse, REST_Controller::HTTP_OK);
       }
    }

    public function delete_user_get()
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $id = $this->get('id');
        $this->db->delete('users',['id' => $id]);
        if($this->db->affected_rows() > 0) {
            $responseStatus = array('message'=> lang('user_deleted'),'status'=> 'success');
            $this->response($responseStatus, REST_Controller::HTTP_OK);
        } else {
            $responseStatus = array('message'=> lang('user_not_deleted'),'status'=> 'error');
            $this->response($responseStatus, REST_Controller::HTTP_OK);
        }
        
    }

    // public function dashboard_get(){
    //     //$user_id = $this->input->get_request_header('User-ID', TRUE);
    //     $filters = [
    //         'group'    => $this->get('group') ? $this->get('group') : 'customer',
    //     ];

    //     $data = [
    //         'total_customer' => $this->main_api->countCompanies($filters),
    //         'total_user' => $this->main_api->countUsers(),
    //         'total_agency' => $this->main_api->countAgencies(),
    //         'total_dealer' => $this->main_api->countDealers(),
    //         'latest_customers' => $this->main_api->getLatestCustomer(5),
    //         'total_insurance_success' => $this->main_api->countInsuranceByStatus("success"),
    //         'total_insurance_process' => $this->main_api->countInsuranceByStatus("progress"),
    //         'total_insurance_cancel' => $this->main_api->countInsuranceByStatus("cancel"),
    //         'latest_insurances' => $this->main_api->getLatestInsurance(10),
    //     ];

    //     if($data){
    //         $this->response($data, REST_Controller::HTTP_OK);
    //     }else{
    //         $this->response(NULL, 404);
    //     }
    // }

    public function groups_get(){
        $groups = $this->ion_auth->groups()->result();
        if($groups){
            $this->response($groups, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
        
    }

    public function customer_groups_get(){
        $customer_groups = $this->companies_model->getAllCustomerGroups();
        if($customer_groups){
            $this->response($customer_groups, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    }

    public function create_customer_post(){

        $this->load->library('upload');

        $config['upload_path']   = 'assets/uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        //$config['max_size'] = '500';
        $config['max_width']    = $this->Settings->iwidth;
        $config['max_height']   = $this->Settings->iheight;
        $config['overwrite']    = false;
        $config['encrypt_name'] = true;
        $config['max_filename'] = 25;

        $this->upload->initialize($config);

        $identify_card_photo = "";
        $driving_license_photo = "";
        $vihecle_id_photo = "";
        if (isset( $_FILES["image_identify_card"] ) && !empty( $_FILES["image_identify_card"]["name"] ) ) {
            if ($_FILES['image_identify_card']['size'] > 0) {
                
                if (!$this->upload->do_upload('image_identify_card')) {
                    $error = $this->upload->display_errors();
                }

                $identify_card_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $identify_card_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $identify_card_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }
        if (isset( $_FILES["image_driving_license"] ) && !empty( $_FILES["image_driving_license"]["name"] ) ) {
            if ($_FILES['image_driving_license']['size'] > 0) {

                if (!$this->upload->do_upload('image_driving_license')) {
                    $error = $this->upload->display_errors();
                }

                $driving_license_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $driving_license_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $driving_license_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }
        if (isset( $_FILES["image_vihecle_id"] ) && !empty( $_FILES["image_vihecle_id"]["name"] ) ) {
            if ($_FILES['image_vihecle_id']['size'] > 0) {
                
                if (!$this->upload->do_upload('image_vihecle_id')) {
                    $error = $this->upload->display_errors();
                }

                $vihecle_id_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $vihecle_id_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $vihecle_id_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }

        $cg   = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
       
        $data = [
            'name'                => $this->input->post('name'),
            'email'               => $this->input->post('email'),
            'group_id'            => '3',
            'group_name'          => 'customer',
            'customer_group_id'   => $this->input->post('customer_group'),
            'customer_group_name' => $cg->name,
            'price_group_id'      => null,
            'price_group_name'    => null,
            'company'             => $this->input->post('company'),
            'address'             => $this->input->post('address'),
            'vat_no'              => "",
            'city'                => "",
            'state'               => "",
            'postal_code'         => "",
            'country'             => "",
            'phone'               => $this->input->post('phone'),
            'cf1'                 => $this->input->post('secondary_phone'),
            'cf2'                 => $this->input->post('saleman_group'),
            'cf3'                 => $this->input->post('saleman_id'),
            'cf4'                 => $this->input->post('cf4'),
            'cf5'                 => $this->input->post('cf5'),
            'cf6'                 => $this->input->post('cf6'),
            'gst_no'              => "",
        ];
        
        if ($cid = $this->companies_model->addCompany($data)) {
            $additional_data = 
            [
                'type' => "car",
                'plate_number' => $this->input->post('plate_number'),
                'start_date' => $this->input->post('start_date'),
                'identify_card' => $identify_card_photo,
                'driving_license' => $driving_license_photo,
                'vihecle_id' => $vihecle_id_photo,
                'customer_id' => $cid,
            ];

            $this->db->insert('insurance_items', $additional_data);

            $response = array('message'=> 'customer_added','status'=> 'success');
            $this->response($response, REST_Controller::HTTP_OK);
        }else{
            $response = array('message'=> 'customer_not_added','status'=> 'error');
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function edit_customer_post(){

        $this->load->library('upload');

        $config['upload_path']   = 'assets/uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        //$config['max_size'] = '500';
        $config['max_width']    = $this->Settings->iwidth;
        $config['max_height']   = $this->Settings->iheight;
        $config['overwrite']    = false;
        $config['encrypt_name'] = true;
        $config['max_filename'] = 25;

        $this->upload->initialize($config);

        $identify_card_photo = "";
        $driving_license_photo = "";
        $vihecle_id_photo = "";
        if (isset( $_FILES["image_identify_card"] ) && !empty( $_FILES["image_identify_card"]["name"] ) ) {
            if ($_FILES['image_identify_card']['size'] > 0) {
                
                if (!$this->upload->do_upload('image_identify_card')) {
                    $error = $this->upload->display_errors();
                }

                $identify_card_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $identify_card_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $identify_card_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }
        if (isset( $_FILES["image_driving_license"] ) && !empty( $_FILES["image_driving_license"]["name"] ) ) {
            if ($_FILES['image_driving_license']['size'] > 0) {

                if (!$this->upload->do_upload('image_driving_license')) {
                    $error = $this->upload->display_errors();
                }

                $driving_license_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $driving_license_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $driving_license_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }
        if (isset( $_FILES["image_vihecle_id"] ) && !empty( $_FILES["image_vihecle_id"]["name"] ) ) {
            if ($_FILES['image_vihecle_id']['size'] > 0) {
                
                if (!$this->upload->do_upload('image_vihecle_id')) {
                    $error = $this->upload->display_errors();
                }

                $vihecle_id_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $vihecle_id_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $vihecle_id_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }

        $cg   = $this->site->getCustomerGroupByID($this->input->post('customer_group'));
        $user_id = $this->input->post('user_id');
       
        $data = [
            'name'                => $this->input->post('name'),
            'email'               => $this->input->post('email'),
            'group_id'            => '3',
            'group_name'          => 'customer',
            'customer_group_id'   => $this->input->post('customer_group'),
            'customer_group_name' => $cg->name,
            'price_group_id'      => null,
            'price_group_name'    => null,
            'company'             => $this->input->post('company'),
            'address'             => $this->input->post('address'),
            'vat_no'              => "",
            'city'                => "",
            'state'               => "",
            'postal_code'         => "",
            'country'             => "",
            'phone'               => $this->input->post('phone'),
            'cf1'                 => $this->input->post('secondary_phone'),
            'cf2'                 => $this->input->post('saleman_group'),
            'cf3'                 => $this->input->post('saleman_id'),
            'cf4'                 => $this->input->post('cf4'),
            'cf5'                 => $this->input->post('cf5'),
            'cf6'                 => $this->input->post('cf6'),
            'gst_no'              => "",
        ];

        $additional_data = 
            [
                'type' => "car",
                'plate_number' => $this->input->post('plate_number'),
                'start_date' => $this->input->post('start_date'),
            ];
        if($identify_card_photo != ""){
            $additional_data['identify_card'] = $identify_card_photo;
        }
        if($driving_license_photo != ""){
            $additional_data['driving_license'] = $driving_license_photo;
        }
        if($vihecle_id_photo != ""){
            $additional_data['vihecle_id'] = $vihecle_id_photo;
        }
        
        if ($this->db->update('companies',$data, array('id'=> $user_id)) || 
            $this->db->update('insurance_items',$additional_data, array('customer_id'=> $user_id))
            ) {

            $response = array('message'=> 'customer_updated','status'=> 'success');
            $this->response($response, REST_Controller::HTTP_OK);
        }else{
            $response = array('message'=> 'customer_not_updated','status'=> 'error');
            $this->response($response, REST_Controller::HTTP_OK);
        }
    }

    public function delete_customer_get()
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $id = $this->get('id');
        $this->db->delete('users',['id' => $id]);
        if($this->db->affected_rows() > 0) {
            $responseStatus = array('message'=> lang('user_deleted'),'status'=> 'success');
            $this->response($responseStatus, REST_Controller::HTTP_OK);
        } else {
            $responseStatus = array('message'=> lang('user_not_deleted'),'status'=> 'error');
            $this->response($responseStatus, REST_Controller::HTTP_OK);
        }
        
    }

    public function shop_setting_get(){
        $shop_setting = $this->db
            ->select('*')
            ->from('shop_settings')
            ->where('shop_id', 1)
            ->get()->row();

        if($shop_setting){
            $this->response($shop_setting, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    }



































    public function car_insurances_get(){
        $user_id = $this->input->get_request_header('user_id', TRUE);
        $customer_id = $this->get('id');
        $query = $this->db
            ->select("insurance_items.*, CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as created_by", false)
            ->from('insurance_items')
            ->join('users', 'users.id=insurance_items.created_by', 'left')
            ->where($this->db->dbprefix('insurance_items') . '.customer_id', $customer_id)->get();

        $insurances = $query->result();

        if($insurances){
            $data = [
                    'data'  => $insurances,
            ];
            $this->response($data, REST_Controller::HTTP_OK);

        }else{
            $this->response(NULL, 404);
        }
    }

    public function create_car_insurance_post(){
        $user_id = $this->input->get_request_header('user_id', TRUE);

        $this->load->library('upload');

        $config['upload_path']   = 'assets/uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        //$config['max_size'] = '500';
        $config['max_width']    = $this->Settings->iwidth;
        $config['max_height']   = $this->Settings->iheight;
        $config['overwrite']    = false;
        $config['encrypt_name'] = true;
        $config['max_filename'] = 25;

        $this->upload->initialize($config);

        $identify_card_photo = "";
        $driving_license_photo = "";
        $vihecle_id_photo = "";
        if (isset( $_FILES["image_identify_card"] ) && !empty( $_FILES["image_identify_card"]["name"] ) ) {
            if ($_FILES['image_identify_card']['size'] > 0) {
                
                if (!$this->upload->do_upload('image_identify_card')) {
                    $error = $this->upload->display_errors();
                }

                $identify_card_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $identify_card_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $identify_card_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }
        if (isset( $_FILES["image_driving_license"] ) && !empty( $_FILES["image_driving_license"]["name"] ) ) {
            if ($_FILES['image_driving_license']['size'] > 0) {

                if (!$this->upload->do_upload('image_driving_license')) {
                    $error = $this->upload->display_errors();
                }

                $driving_license_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $driving_license_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $driving_license_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }
        if (isset( $_FILES["image_vihecle_id"] ) && !empty( $_FILES["image_vihecle_id"]["name"] ) ) {
            if ($_FILES['image_vihecle_id']['size'] > 0) {
                
                if (!$this->upload->do_upload('image_vihecle_id')) {
                    $error = $this->upload->display_errors();
                }

                $vihecle_id_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $vihecle_id_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $vihecle_id_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }
       
        
        $date = date('Y-m-d H:i:s');
        $start_date = $this->input->post('start_date');
        $convStartDate = strtotime($start_date);
        $effective_date = date('Y-m-d',$convStartDate);
           

        $customer_id = $this->input->post('customer_id');
        $reference_no =  $this->input->post('reference_no') ? $this->input->post('reference_no') : $this->site->getReference('ins');

        //$conver_eff_date = strtotime($effective_date);
        $expire_date = date('Y-m-d',strtotime("+1 years",$convStartDate));
        $price = 0;

        $data = [
            'date'       => $date,
            'reference_no'     => $reference_no,
            'plate_number'    => $this->input->post('plate_number'),
            'start_date' => $effective_date,
            'expire_date' => $expire_date,
            'type'       => "car",
            'membership_type'       => $this->input->post('type'),
            'note'       => "N/A",
            'customer_id' => $customer_id,
            'created_by' => $user_id,
            'price'       => $price,
            'quote_status'       => 'process',
        ];

        if($identify_card_photo != ""){
            $data['identify_card'] = $identify_card_photo;
        }
        if($driving_license_photo != ""){
            $data['driving_license'] = $driving_license_photo;
        }
        if($vihecle_id_photo != ""){
            $data['vihecle_id'] = $vihecle_id_photo;
        }
        
        $this->db->insert('insurance_items', $data);
        if($this->db->affected_rows() > 0) {
            $ins_id = $this->db->insert_id();
            if ($this->site->getReference('ins') == $data['reference_no']) {
                $this->site->updateReference('ins');
            }
            $response = array('message'=> 'car_insurance_added','status'=> 'success');
            $this->response($response, REST_Controller::HTTP_OK);
        }else{
            $response = array('message'=> 'car_insurance_not_added','status'=> 'error');
            $this->response($response, REST_Controller::HTTP_OK);
        }

        
    }

    public function edit_car_insurance_post(){
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $this->load->library('upload');

        $config['upload_path']   = 'assets/uploads';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        //$config['max_size'] = '500';
        $config['max_width']    = $this->Settings->iwidth;
        $config['max_height']   = $this->Settings->iheight;
        $config['overwrite']    = false;
        $config['encrypt_name'] = true;
        $config['max_filename'] = 25;

        $this->upload->initialize($config);

        $identify_card_photo = "";
        $driving_license_photo = "";
        $vihecle_id_photo = "";
        if (isset( $_FILES["image_identify_card"] ) && !empty( $_FILES["image_identify_card"]["name"] ) ) {
            if ($_FILES['image_identify_card']['size'] > 0) {
                
                if (!$this->upload->do_upload('image_identify_card')) {
                    $error = $this->upload->display_errors();
                }

                $identify_card_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $identify_card_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $identify_card_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }
        if (isset( $_FILES["image_driving_license"] ) && !empty( $_FILES["image_driving_license"]["name"] ) ) {
            if ($_FILES['image_driving_license']['size'] > 0) {

                if (!$this->upload->do_upload('image_driving_license')) {
                    $error = $this->upload->display_errors();
                }

                $driving_license_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $driving_license_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $driving_license_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }
        if (isset( $_FILES["image_vihecle_id"] ) && !empty( $_FILES["image_vihecle_id"]["name"] ) ) {
            if ($_FILES['image_vihecle_id']['size'] > 0) {
                
                if (!$this->upload->do_upload('image_vihecle_id')) {
                    $error = $this->upload->display_errors();
                }

                $vihecle_id_photo = $this->upload->file_name;

                $this->load->helper('file');
                $this->load->library('image_lib');
                $config['image_library']  = 'gd2';
                $config['source_image']   = 'assets/uploads/' . $vihecle_id_photo;
                $config['new_image']      = 'assets/uploads/thumbs/' . $vihecle_id_photo;
                $config['maintain_ratio'] = true;
                $config['width']          = 150;
                $config['height']         = 150;

                $this->image_lib->clear();
                $this->image_lib->initialize($config);

                if (!$this->image_lib->resize()) {
                    echo $this->image_lib->display_errors();
                }
            }
        }

        $date = date('Y-m-d H:i:s');
        $start_date = $this->input->post('start_date');
        $convStartDate = strtotime($start_date);
        $effective_date = date('Y-m-d',$convStartDate);

        $customer_id = $this->input->post('customer_id');
        $reference_no =  $this->input->post('reference_no');

        //$conver_eff_date = strtotime($effective_date);
        $expire_date = date('Y-m-d',strtotime("+1 years",$start_date));
        $price = 0;

        $data = [
            'date'       => $date,
            'reference_no'     => $reference_no,
            'plate_number'    => $this->input->post('plate_number'),
            'start_date' => $effective_date,
            'expire_date' => $expire_date,
            'type'       => "car",
            'membership_type'       => $this->input->post('type'),
            'note'       => "N/A",
            'customer_id' => $customer_id,
            'created_by' => $user_id,
            'price'       => $price,
            'quote_status'       => 'process',
        ];

        if($identify_card_photo != ""){
            $data['identify_card'] = $identify_card_photo;
        }
        if($driving_license_photo != ""){
            $data['driving_license'] = $driving_license_photo;
        }
        if($vihecle_id_photo != ""){
            $data['vihecle_id'] = $vihecle_id_photo;
        }

    
        
        $this->db->update('insurance_items',$data, array('id'=> $insurance_id));
        if($this->db->affected_rows() > 0) {
            $response = array('message'=> 'car_insurance_updated','status'=> 'success');
            $this->response($response, REST_Controller::HTTP_OK);
        }else{
            $response = array('message'=> 'car_insurance_not_updated','status'=> 'error');
            $this->response($response, REST_Controller::HTTP_OK);
        }

        
    }

    public function delete_car_insurance_get()
    {
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $id = $this->get('id');
        $this->db->delete('insurance_items',['id' => $id]);
        if($this->db->affected_rows() > 0) {
            $responseStatus = array('message'=> lang('car_insurance_deleted'),'status'=> 'success');
            $this->response($responseStatus, REST_Controller::HTTP_OK);
        } else {
            $responseStatus = array('message'=> lang('car_insurance_not_deleted'),'status'=> 'error');
            $this->response($responseStatus, REST_Controller::HTTP_OK);
        }
        
    }

    public function view_car_insurance_get(){
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $id = $this->get('id');
        $insurance = $this->companies_model->getCarInsuranceByID($id);

        if($insurance){
            $this->response($insurance, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    }

    public function insurances_get(){
        $user         = $this->input->get('user') ? $this->input->get('user') : null;
        $customer     = $this->input->get('customer') ? $this->input->get('customer') : null;
    
        $reference_no = $this->input->get('reference_no') ? $this->input->get('reference_no') : null;
        $start_date   = $this->input->get('start_date') ? $this->input->get('start_date') : null;
        $end_date     = $this->input->get('end_date') ? $this->input->get('end_date') : null;
        $status = $this->input->get('status') ? $this->input->get('status') : null;

        if ($start_date) {
            $convStartDate = strtotime($start_date);
            $convEndDate = strtotime($end_date);
           
            $start_date = date('Y-m-d',$convStartDate);
            $end_date   = date('Y-m-d',$convEndDate);
        }

        $uploads_url = base_url('assets/uploads/');
        $this->db
        ->select("insurance_items.id,date,companies.id as customer_id,companies.name as customer_name,
            companies.phone as customer_phone, reference_no, plate_number,quote_status, identify_card", false)
        ->from('insurance_items')
        ->join('companies', 'companies.id=insurance_items.customer_id', 'inner')
        ->join('users', 'users.id=insurance_items.created_by', 'inner');

        if ($user) {
            $this->db->where('insurance_items.created_by', $user);
        }
        
        if ($customer) {
            $this->db->where('insurance_items.customer_id', $customer);
        }
       
        if ($reference_no) {
            $this->db->like('insurance_items.reference_no', $reference_no, 'both');
        }
        if ($start_date) {
            $this->db->where($this->db->dbprefix('insurance_items') . '.date BETWEEN "' . $start_date . '" and "' . $end_date . '"');
        }

        if ($status) {
            $this->db->where('insurance_items.quote_status', $status);
        }

        $this->db->order_by('insurance_items.id','desc');
        //$this->db->limit($limit);

        $query = $this->db->get();
        $insurances = $query->result();
        $data = [
                    'data'  => $insurances,
            ];
        $this->response($data, REST_Controller::HTTP_OK);

        // if($insurances){
            

        // }else{
        //     $this->response(NULL, 404);
        // }
           
    }

    public function users_and_customers_get(){
        $customer_groups = $this->companies_model->getCompanyByGroup('customer');
        $staff = $this->companies_model->getStaff();
        if($customer_groups || $start_date){
            $data = [
                    'customers'  => $customer_groups,
                    'staffs'  => $staff,
            ];
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    }




























































    // kce_dictionary



    public function general_get($category_id){
        $x = $this->main_api->get_general($category_id);
        if($x)
        {
            $data = array();
            foreach($x->result() as $key => $get)
            {
                $data[] = [
                            'id' => $get->id,
                            'category_id' => $get->category_general_id,
                            'category_name' => $get->category_general_name,
                            'title' => $get->title,
                            'description' => $get->description,
                            'img_url'   => $get->img_url,
                            'video_url' => $get->video_url,
                            'audio_url' => $get->audio_url
                          ];
            }
            $fields['general'] = $data;
            $this->response($fields, REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response(NULL, 404);
        } 
       
    }

}
