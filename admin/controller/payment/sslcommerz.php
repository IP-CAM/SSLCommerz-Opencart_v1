<?php
class ControllerPaymentSslcommerz extends Controller 
{
  private $error = array();
 
  public function index() {
    $this->language->load('payment/sslcommerz');
    $this->document->setTitle('SslCommerz Payment Method Configuration');
    $this->load->model('setting/setting');
   

    if (($this->request->server['REQUEST_METHOD'] == 'POST')  && $this->validate()) 
    {
      $this->model_setting_setting->editSetting('sslcommerz', $this->request->post);
      $this->session->data['success'] = 'Saved.';
      $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
    }

 
    // language variables
    $this->data['heading_title'] =          $this->language->get('heading_title');
    $this->data['button_save'] =            $this->language->get('text_button_save');
    $this->data['button_cancel'] =          $this->language->get('text_button_cancel');
    $this->data['text_enabled'] =           $this->language->get('text_enabled');
    $this->data['text_disabled'] =          $this->language->get('text_disabled');
    $this->data['text_test_transaction'] =  $this->language->get('text_test_transaction');
    $this->data['text_live_transaction'] =  $this->language->get('text_live_transaction');
    $this->data['entry_storeId'] =          $this->language->get('entry_storeId');
    $this->data['entry_order_status'] =     $this->language->get('entry_order_status');
    $this->data['entry_status'] =           $this->language->get('entry_status');
    $this->data['entry_transaction_mode'] = $this->language->get('entry_transaction_mode');
    $this->data['entry_sort_order'] =       $this->language->get('entry_sort_order');

    // load errors
    if (isset($this->error['warning'])) {
      $this->data['error_warning'] = $this->error['warning'];
    } else {
      $this->data['error_warning'] = '';
    }

    if (isset($this->error['storeId'])) {
      $this->data['error_storeId'] = $this->error['storeId'];
    } else {
      $this->data['error_storeId'] = '';
    }

    // breadcrumbs
    $this->data['breadcrumbs'] = array();

    $this->data['breadcrumbs'][] = array(
      'text'      => 'Home',
      'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      'separator' => false
    );

    $this->data['breadcrumbs'][] = array(
      'text'      => 'Payment',
      'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),          
      'separator' => ' :: '
    );

    $this->data['breadcrumbs'][] = array(
      'text'      => $this->language->get('heading_title'),
      'href'      => $this->url->link('payment/sslcommerz', 'token=' . $this->session->data['token'], 'SSL'),
      'separator' => ' :: '
    );
  

    $this->data['action'] = $this->url->link('payment/sslcommerz', 'token=' . $this->session->data['token'], 'SSL');
    $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
    
    
    // StoreId : should be provided by the payment gateway company
    if (isset($this->request->post['sslcommerz_storeId'])) {
      $this->data['sslcommerz_storeId'] = $this->request->post['sslcommerz_storeId'];
    } else {
      $this->data['sslcommerz_storeId'] = $this->config->get('sslcommerz_storeId');
    }

    // transactions mode: Live/Test
    if (isset($this->request->post['sslcommerz_transaction_mode'])) {
      $this->data['sslcommerz_transaction_mode'] = $this->request->post['sslcommerz_transaction_mode'];
    } else {
      $this->data['sslcommerz_transaction_mode'] = $this->config->get('sslcommerz_transaction_mode');
    }

    // total : to be added later
    // if (isset($this->request->post['sslcommerz_total'])) {
    //   $this->data['sslcommerz_total'] = $this->request->post['sslcommerz_total'];
    // } else {
    //   $this->data['sslcommerz_total'] = $this->config->get('sslcommerz_total'); 
    // } 

    // Order Status
    if (isset($this->request->post['sslcommerz_order_status_id'])) {
      $this->data['sslcommerz_order_status_id'] = $this->request->post['sslcommerz_order_status_id'];
    } else {
      $this->data['sslcommerz_order_status_id'] = $this->config->get('sslcommerz_order_status_id');
    }


    // Module status: Enabled/Disabled
    if (isset($this->request->post['sslcommerz_status'])) {
      $this->data['sslcommerz_status'] = $this->request->post['sslcommerz_status'];
    } else {
      $this->data['sslcommerz_status'] = $this->config->get('sslcommerz_status');
    }
        
    // sort order
    if (isset($this->request->post['sslcommerz_sort_order'])) {
      $this->data['sslcommerz_sort_order'] = $this->request->post['sslcommerz_sort_order'];
    } else {
      $this->data['sslcommerz_sort_order'] = $this->config->get('sslcommerz_sort_order');
    }


    //populate Order Status field
    $this->load->model('localisation/order_status');
    $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

    //template
    $this->template = 'payment/sslcommerz.tpl';
            
    $this->children = array(
      'common/header',
      'common/footer'
    );
 
    $this->response->setOutput($this->render());
  }


  protected function validate() 
  {
    if (!$this->user->hasPermission('modify', 'payment/sslcommerz')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    if (!$this->request->post['sslcommerz_storeId']) {
      $this->error['storeId'] = $this->language->get('error_storeId');
    }

    if (!$this->error) {
      return true;
    } else {
      return false;
    } 
  }
}