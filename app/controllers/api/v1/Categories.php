<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Categories extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('categories_api');
    }


    public function index_get()
    {
        $code = $this->get('code');

        $filters = [
            'code'     => $code,

            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : 10,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['code', 'asc'],
            'parent_id'    => $this->get('parent_id') ? $this->get('parent_id') : null,
        
        ];

        if ($code === null) {
            if ($categories = $this->categories_api->getCategories($filters)) {
                $pr_data = [];
                foreach ($categories as $category) {
                    $category->sub_category = $this->categories_api->countSubCategories($category->id);
                    $pr_data[] = $category;

                }

                $data = [
                    'data'  => $pr_data,
                    'limit' => $filters['limit'],
                    'start' => $filters['start'],
                    'total' => $this->categories_api->countCategories($filters),
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No category were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            if ($category = $this->categories_api->getCategory($filters)) {
           
                $this->set_response($category, REST_Controller::HTTP_OK);
            } else {
                $this->set_response([
                    'message' => 'Category could not be found for code ' . $code . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    
}
