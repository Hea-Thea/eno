<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Loans_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAllLoanTerms()
    {
        $q = $this->db->get('loan_terms');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllLoanTypes()
    {
        $q = $this->db->get('loan_types');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllInterestTypes()
    {
        $q = $this->db->get('loan_interest_types');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getAllCurrencies()
    {
        $q = $this->db->get('currencies');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function addLoan($data = [])
    {
        if ($this->db->insert('loans', $data)) {
            $loan_id = $this->db->insert_id();
            $holidays = $this->db->get('loan_holiday_detail');
            $this->make_repayment_schedule($loan_id, $data['term'], $data['loan_term_id'], $data['first_payment_date'],$data['amount'],$data['interest_rate'],$data['interest_type_id'],$holidays); 
           
            if ($this->site->getReference('ln') == $data['reference_no']) {
                $this->site->updateReference('ln');
            }
            return true;
        }
        return false;
    }

    public function updateLoan($id, $data = [])
    {
        if ($this->db->update('loans', $data, ['id' => $id])) {
            return true;
        }
        return false;
    }

    public function getLoanByID($id)
    {
        $q = $this->db->get_where('loans', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function deleteLoan($id)
    {
      
        $this->db->trans_start();
        //$sale_items = $this->resetSaleActions($id);
        // $this->site->log('Sale', ['model' => $this->getInvoiceByID($id), 'items' => $sale_items]);
        $this->site->log('Loan', ['model' => $this->getLoanByID($id)]);
        if ($this->db->delete('loan_repayment_schedule_detail', ['loan_id' => $id]) && $this->db->delete('loans', ['id' => $id])) {
            $this->db->delete('payments', ['loan_id' => $id]);
        }
        $this->db->delete('attachments', ['subject_id' => $id, 'subject_type' => 'loan']);
        $this->db->trans_complete();
        if ($this->db->trans_status() === false) {
            log_message('error', 'An errors has been occurred while adding the loan (Delete:Loans_model.php)');
        } else {
            return true;
        }
        return false;
    }

    public function make_repayment_schedule($loan_id, $number_payment, $loan_term, $deduction_start,$amount,$interest,$using_interest_id,$holiday_arr) {
        $holidays=array();

        foreach($holiday_arr->result() as $r){
            $holidays[]=$r->holiday_date;
        }

        $rest_day=array();
        if($this->config->item('monday') == "yes"){
            $rest_day[]='monday';
        }
        if($this->config->item('tuesday') == "yes"){
            $rest_day[]='tuesday';
        }
        if($this->config->item('wednesday') == "yes"){
            $rest_day[]='wednesday';
        }
        if($this->config->item('thursday') == "yes"){
            $rest_day[]='thursday';
        }
        if($this->config->item('friday') == "yes"){
            $rest_day[]='friday';
        }
        if($this->config->item('saturday') == "yes"){
            $rest_day[]='saturday';
        }
        if($this->config->item('sunday') == "yes"){
            $rest_day[]='sunday';
        }

        $week_term=0;
        if ($loan_term == 1) {
            //TERMS (Daily)
        } else if ($loan_term == 2) {
            //TERMS (Weekly)
            $week_term = 1;
        } else if ($loan_term == 3) {
            //TERMS (Two weekly)
            $week_term = 2;
        } else if ($loan_term == 4) {
            //TERMS (Three Weekly)
            $week_term = 3;
        } else if ($loan_term == 5) {
            //TERMS(Monthly)
        }else if ($loan_term == 6) {
            //TERMS(Monthly)
        }

        if ($week_term == 1 || $week_term == 2 || $week_term == 3) { //loan term by week
            $amount_process = 0;
            $increment = 1;
            //  # of weeks to increment by
            //  First day of the first week of the year
            $startdate = strtotime($deduction_start);
            //  $all_weeks[1] is the first partial week of the year
            //  $all_weeks[53] is the last partial week of the year
            $all_weeks = array();
            $total = 0;
            $day_offset = array();
            for ($week = 0; $week < $number_payment; $week += $increment) {
                $week_cycle = $week_term;
                $nw = ($week * $week_cycle) + 1;
                //+ 1 to be in the next range
                $week_data = array();
                //$week_data['start'] = strtotime("+$nw weeks", $startdate);
                //$week_data['end'] = strtotime("+6 days", $week_data['start']);
                $date_t=date('Y-m-d',strtotime("+$nw weeks", $startdate));

                while ($this->isExist($date_t,$day_offset,$holidays,$rest_day)) {
                    $date_t = date('Y-m-d',strtotime("+1 day", strtotime($date_t)));

                }
                $day_offset[$week]=$date_t;
                $week_data['start']=strtotime($date_t);
                $all_weeks[$week + 1] = $week_data;
            }

            //echo "Week No.    Start Date  End Date\r\n";
            $i = 1;
            foreach ($all_weeks as $week => $week_data) {
                $amount_paid = $amount - $amount_process;
                if ($using_interest_id == 1 || $using_interest_id == 2) {
                    $payment = ((($amount_paid * ($interest / 100)) * $number_payment) + $amount) / $number_payment;
                    
                } else {
                    if ($i == $number_payment) {
                        $payment = (($amount_paid * $interest) / 100) + $amount;
                        
                    } else {
                        $payment = (($amount_paid * $interest) / 100);
                        
                    }

                }

                $timestamp = strtotime($week_data['start']);
                $day = date('l', $timestamp);
                
                $data_detail=array(
                    'loan_id'=>$loan_id,
                    'times'=>$i,
                    'payment_amount'=>round($payment,2),
                    'payment_date'=>date("Y-m-d", $week_data['start']),
                    'is_paid'=>0,
                    'status'=>1,
                    'created_date'=>null
                );
                $this->db->insert('loan_repayment_schedule_detail',$data_detail);
                $increment++;
                $total += $payment;
                if ($using_interest_id == 2) {
                    $amount_process += $amount / $number_payment;
                }
                $i++;
            }
            
        } else if ($loan_term == 1) {//loan term by day
            $amount_process = 0;
            $increment = 1;
            //  # of weeks to increment by
            //  First day of the first week of the year
            $startdate = strtotime($deduction_start);
            //  $all_weeks[1] is the first partial week of the year
            //  $all_weeks[53] is the last partial week of the year
            $all_days = array();
            $total = 0;
            $day_offset = array();

            for ($day = 0; $day < $number_payment; $day += $increment) {
                $day_cycle = 1;
                $nw = ($day * $day_cycle) + 1;
                //+ 1 to be in the next range
                $day_data = array();
                //$day_data['start'] = strtotime("+$nw days", $startdate);
                //$day_data['end'] = strtotime("+1 days", $day_data['start']);
                $date_t=date('Y-m-d',strtotime("+$nw days", $startdate));

                while ($this->isExist($date_t,$day_offset,$holidays,$rest_day)) {
                    $date_t = date('Y-m-d',strtotime("+1 day", strtotime($date_t)));

                }
                $day_offset[$day]=$date_t;
                $day_data['start']=strtotime($date_t);
                $all_days[$day + 1] = $day_data;
            }

            //echo "Week No.    Start Date  End Date\r\n";
            $i = 1;
            foreach ($all_days as $day => $day_data) {
                $amount_paid = $amount - $amount_process;
                if ($using_interest_id == 1 || $using_interest_id == 2) {
                    $payment = ((($amount_paid * ($interest / 100)) * $number_payment) + $amount) / $number_payment;
                } else {
                    if ($i == $number_payment) {
                        $payment = (($amount_paid * $interest) / 100) + $amount;
                    } else {
                        $payment = (($amount_paid * $interest) / 100);
                    }

                }

                $timestamp = strtotime(date("Y-m-d", $day_data['start']));
                $day_name = date('l', $timestamp);
                $data_detail=array(
                    'loan_id'=>$loan_id,
                    'times'=>$i,
                    'payment_amount'=>round($payment,2),
                    'payment_date'=>date("Y-m-d", $day_data['start']),
                    'is_paid'=>0,
                    'status'=>1,
                    'created_date'=>null
                );
                $this->db->insert('loan_repayment_schedule_detail',$data_detail);
                $increment++;
                $total += $payment;
                if ($using_interest_id == 2) {
                    $amount_process += $amount / $number_payment;
                }
                $i++;
            }

        } else if ($loan_term == 5) { // loan term by month
            $amount_process = 0;
            $increment = 1;
            //  # of weeks to increment by
            //  First day of the first week of the year
            $startdate = strtotime($deduction_start);
            //  $all_weeks[1] is the first partial week of the year
            //  $all_weeks[53] is the last partial week of the year
            $all_days = array();
            $total = 0;
            $day_offset = array();

            for ($day = 0; $day < $number_payment; $day += $increment) {
                $day_cycle = 1;
                $nw = ($day * $day_cycle) + 1;
                //+ 1 to be in the next range
                $day_data = array();
                //$day_data['start'] = strtotime("+$nw month", $startdate);
                //$day_data['end'] = strtotime("+1 month", $day_data['start']);
                
                $date_t=date('Y-m-d',strtotime("+$nw month", $startdate));

                while ($this->isExist($date_t,$day_offset,$holidays,$rest_day)) {
                    $date_t = date('Y-m-d',strtotime("+1 day", strtotime($date_t)));

                }
                $day_offset[$day]=$date_t;
                $day_data['start']=strtotime($date_t);
                $all_days[$day + 1] = $day_data;
            }

            //echo "Week No.    Start Date  End Date\r\n";
            $i = 1;
            foreach ($all_days as $day => $day_data) {
                $amount_paid = $amount - $amount_process;
                if ($using_interest_id == 1 || $using_interest_id == 2) {
                    $payment = ((($amount_paid * ($interest / 100)) * $number_payment) + $amount) / $number_payment;
                } else {
                    if ($i == $number_payment) {
                        $payment = (($amount_paid * $interest) / 100) + $amount;
                    } else {
                        $payment = (($amount_paid * $interest) / 100);
                    }

                }

                $timestamp = strtotime(date("Y-m-d", $day_data['start']));
                $day_name = date('l', $timestamp);
                
                $data_detail=array(
                    'loan_id'=>$loan_id,
                    'times'=>$i,
                    'payment_amount'=>round($payment,2),
                    'payment_date'=>date("Y-m-d", $day_data['start']),
                    'is_paid'=>0,
                    'status'=>1,
                    'created_date'=>null
                );
                $this->db->insert('loan_repayment_schedule_detail',$data_detail);
                
                $increment++;
                $total += $payment;
                if ($using_interest_id == 2) {
                    $amount_process += $amount / $number_payment;
                }
                $i++;
            }
        }
    }
    
    function isExist($date_t,$day_offset,$holidays,$rest_day) {

        //$rest_day=array('Saturday','Sunday');
        $timestamp = strtotime(date("Y-m-d", strtotime($date_t)));
        $day_name = date('l', $timestamp);
        
        //$day_name = date('l', $timestamp);
        if(in_array($day_name, $rest_day) || in_array($date_t, $day_offset) || in_array($date_t, $holidays)){
            return true;
        }else{
            return false;
        }
    }


    public function addPayment($data = [], $customer_id = null)
    {
        if ($this->db->insert('payments', $data)) {
            if ($this->site->getReference('pay') == $data['reference_no']) {
                $this->site->updateReference('pay');
            }
            $this->site->syncLoanPayments($data['loan_id']);
            if ($data['paid_by'] == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($data['cc_no']);
                $this->db->update('gift_cards', ['balance' => ($gc->balance - $data['amount'])], ['card_no' => $data['cc_no']]);
            } elseif ($customer_id && $data['paid_by'] == 'deposit') {
                $customer = $this->site->getCompanyByID($customer_id);
                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $data['amount'])], ['id' => $customer_id]);
            }
            return true;
        }
        return false;
    }

    public function updatePayment($id, $data = [], $customer_id = null)
    {
        $opay = $this->getPaymentByID($id);
        if ($this->db->update('payments', $data, ['id' => $id])) {
            $this->site->syncLoanPayments($data['loan_id']);
            if ($opay->paid_by == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($opay->cc_no);
                $this->db->update('gift_cards', ['balance' => ($gc->balance + $opay->amount)], ['card_no' => $opay->cc_no]);
            } elseif ($opay->paid_by == 'deposit') {
                if (!$customer_id) {
                    $sale        = $this->getLoanByID($opay->loan_id);
                    $customer_id = $sale->customer_id;
                }
                $customer = $this->site->getCompanyByID($customer_id);
                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount + $opay->amount)], ['id' => $customer->id]);
            }
            if ($data['paid_by'] == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($data['cc_no']);
                $this->db->update('gift_cards', ['balance' => ($gc->balance - $data['amount'])], ['card_no' => $data['cc_no']]);
            } elseif ($customer_id && $data['paid_by'] == 'deposit') {
                $customer = $this->site->getCompanyByID($customer_id);
                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount - $data['amount'])], ['id' => $customer_id]);
            }
            return true;
        }
        return false;
    }

    public function deletePayment($id)
    {
        $opay = $this->getPaymentByID($id);
        $this->site->log('Payment', ['model' => $opay]);
        if ($this->db->delete('payments', ['id' => $id])) {
            $this->site->syncLoanPayments($opay->loan_id);
            if ($opay->paid_by == 'gift_card') {
                $gc = $this->site->getGiftCardByNO($opay->cc_no);
                $this->db->update('gift_cards', ['balance' => ($gc->balance + $opay->amount)], ['card_no' => $opay->cc_no]);
            } elseif ($opay->paid_by == 'deposit') {
                $loan     = $this->getLoanByID($opay->loan_id);
                $customer = $this->site->getCompanyByID($loan->customer_id);
                $this->db->update('companies', ['deposit_amount' => ($customer->deposit_amount + $opay->amount)], ['id' => $customer->id]);
            }
            return true;
        }
        return false;
    }

    public function getLoanPayments($loan_id)
    {
        $this->db->order_by('id', 'asc');
        $q = $this->db->get_where('payments', ['loan_id' => $loan_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function getPaymentByID($id)
    {
        $q = $this->db->get_where('payments', ['id' => $id], 1);
        if ($q->num_rows() > 0) {
            return $q->row();
        }
        return false;
    }

    public function getPaymentsForLoan($loan_id)
    {
        $this->db->select('payments.date, payments.paid_by, payments.amount, payments.cc_no, payments.cheque_no, payments.reference_no, users.first_name, users.last_name, type')
            ->join('users', 'users.id=payments.created_by', 'left');
        $q = $this->db->get_where('payments', ['loan_id' => $loan_id]);
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }

    public function getPaymentSchedule($loan_id)
    {
        $this->db->select('loan_repayment_schedule_detail.*');
        $this->db->order_by('id', 'asc');
        $this->db->where('loan_id', $loan_id);
        $q = $this->db->get('loan_repayment_schedule_detail');
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        return false;
    }



}
