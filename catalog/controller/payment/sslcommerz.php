<?php
class ControllerPaymentSslcommerz extends Controller{
  public function index() 
  {
    //set BD time
    date_default_timezone_set('Asia/Dacca');

    $this->language->load('payment/sslcommerz');
    
    $this->data['button_confirm'] = $this->language->get('button_confirm');

    // transaction mode: live/test
    if ($this->config->get('sslcommerz_transaction_mode') == 'live'){
      $this->data['action'] = 'https://www.sslcommerz.com.bd/process/index.php';
    }elseif ($this->config->get('sslcommerz_transaction_mode') == 'test'){
      $this->data['action'] = 'https://www.sslcommerz.com.bd/testbox/process/index.php';    
    } 

  
    $this->load->model('checkout/order');
    $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
  
    if ($order_info) {
      $this->data['tran_id'] = $this->session->data['order_id'];
      
      $this->data['store_id'] = trim($this->config->get('sslcommerz_storeId')); 
      
      $this->data['total_amount'] =  $order_info['total'];
      
      $this->data['success_url'] = $this->url->link('payment/sslcommerz/callback');
      $this->data['fail_url'] = str_replace('&amp;', '&', $this->url->link('checkout/checkout', 'token=' . 
        $this->session->data['token'], 'SSL'));
      $this->data['cancel_url'] = $this->url->link('common/home');

      if(file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/sslcommerz.tpl')){
        $this->template = $this->config->get('config_template') . '/template/payment/sslcommerz.tpl';
      } else {
        $this->template = 'default/template/payment/sslcommerz.tpl';
      }
  
      $this->render();
    }
  }
  
  public function callback()
  {
      $tran_id = (int)$_POST['tran_id'];
      $val_id = $_POST['val_id']; 
      $amount = $_POST['amount']; 
      $card_type = $_POST['card_type']; 
      $store_amount = $_POST['store_amount']; 
    
        if ($this->config->get('sslcommerz_transaction_mode') == 'live'){
         $client = 'https://www.sslcommerz.com.bd/validator/validationserver.php?wsdl';
        }elseif($this->config->get('sslcommerz_transaction_mode') == 'test'){
         $client = 'https://www.sslcommerz.com.bd/testbox/validator/validationserver.php?wsdl';    
        } 

       if ( isset($tran_id) && isset($val_id) ){ 
          try{
              $c = new soapclient($client); 
          } 
          catch (Exception $e){
            echo 'Caught exception: ',  $e->getMessage(), "\n"; 
          }

          $res = $c->checkValidation($val_id); 

          if( strcmp ($res, "VALID") == 0 && $this->checkOrder($amount) ):
           $this->model_checkout_order->confirm($tran_id, $this->config->get('config_order_status_id'));
           $message = ' approved (ID: ' . $val_id . ')';
           $this->model_checkout_order->update($tran_id, $this->config->get('sslcommerz_order_status_id'), $message, false);
           $this->redirect($this->url->link('checkout/success'));
          else:
            //canceled order_status_id => 7 ;  (not mandatory)
            // $message = "Incomplete payment";
            // $this->model_checkout_order->update($tran_id, '7', $message, false);
          endif;
      } 
    }
        
    protected function checkOrder($amount)
    {
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        
        return ($amount == $order_info['total']) ? true: false;
    }
}
?>
