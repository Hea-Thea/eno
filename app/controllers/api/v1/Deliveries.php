<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Deliveries extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->methods['index_get']['limit'] = 500;
        $this->load->api_model('deliveries_api');
        $this->load->model('site');
    }


    public function index_get()
    {
        $id = $this->get('id');

        $filters = [
            'id'     => $id,

            'start'    => $this->get('start') && is_numeric($this->get('start')) ? $this->get('start') : 1,
            'limit'    => $this->get('limit') && is_numeric($this->get('limit')) ? $this->get('limit') : 10,
            'order_by' => $this->get('order_by') ? explode(',', $this->get('order_by')) : ['id', 'asc'],
            'delivery_boy_id'    => $this->get('delivery_boy_id') ? $this->get('delivery_boy_id') : null,
            'status'    => $this->get('status') ? $this->get('status') : null,
        
        ];

        if ($id === null) {
            if ($deliveries = $this->deliveries_api->getDeliveries($filters)) {
                $pr_data = [];
                foreach ($deliveries as $delivery) {
                   
                    $pr_data[] = $delivery;

                }

                $data = [
                    'data'  => $pr_data,
                    'limit' => $filters['limit'],
                    'start' => $filters['start'],
                    'total' => $this->deliveries_api->countDeliveries($filters),
                ];
                $this->response($data, REST_Controller::HTTP_OK);
            } else {
                $this->response([
                    'message' => 'No delivery were found.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        } else {
            if ($delivery = $this->deliveries_api->getDelivery($filters)) {
           
                $this->set_response($delivery, REST_Controller::HTTP_OK);
            } else {
                $this->set_response([
                    'message' => 'Delivery could not be found for id ' . $id . '.',
                    'status'  => false,
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    public function detail_get(){
        $id = $this->get('id');
        $deli                = $this->deliveries_api->getDeliveryByID($id);
        $sale                = $this->deliveries_api->getInvoiceByID($deli->sale_id);
        if (!$sale) {
            $errResponse = array('message'=> 'sale_not_found','status'=> "error");
            $this->response($errResponse, REST_Controller::HTTP_OK);
            exit;
        }
        $data['delivery']   = $deli;
        //$data['biller']     = $this->site->getCompanyByID($sale->biller_id);
        $data['rows']       = $this->deliveries_api->getAllInvoiceItemsWithDetails($deli->sale_id);
        $data['payments']       = $this->deliveries_api->getPaymentsForSale($deli->sale_id);

        if($data){
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    } 

    public function home_get(){
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $filterPacking = array('delivery_boy_id'=> $user_id,'status'=>'packing');
        $filterDelivering = array('delivery_boy_id'=> $user_id,'status'=>'delivering');
        $filterDelivered = array('delivery_boy_id'=> $user_id,'status'=>'delivered');
        $data = [
            'total_assign' => $this->deliveries_api->countDeliveries($filterPacking),
            'total_pending' => $this->deliveries_api->countDeliveries($filterPacking),
            'total_process' => $this->deliveries_api->countDeliveries($filterDelivering),
            'total_complete' => $this->deliveries_api->countDeliveries($filterDelivered),
            'total_cancel' => $this->deliveries_api->countDeliveries($filterPacking),
        ];

        if($data){
            $this->response($data, REST_Controller::HTTP_OK);
        }else{
            $this->response(NULL, 404);
        }
    } 

    public function update_delivery_status_post(){  
        $user_id = $this->input->get_request_header('User-ID', TRUE);
        $delivery_id = $this->input->post('delivery_id');
        $status = $this->input->post('status');

        $data = array('status'=> $status);
        $condition = array('id'=> $delivery_id, 'delivery_boy_id'=> $user_id);
        $this->db->update('deliveries1',$data, $condition);
        if($this->db->affected_rows() > 0) {

            $responseStatus = array('message'=> lang('status_updated'),'status'=> "success");
            $this->response($responseStatus, REST_Controller::HTTP_OK);

        }else{
             $responseStatus = array('message'=> lang('status_not_updated'),'status'=> "error");
            $this->response($responseStatus, REST_Controller::HTTP_OK);
        }
    }
    
}
