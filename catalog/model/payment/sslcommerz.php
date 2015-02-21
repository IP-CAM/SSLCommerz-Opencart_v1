<?php
class ModelPaymentSslcommerz extends Model 
{
  public function getMethod($address, $total) {
    $this->load->language('payment/sslcommerz');
  	
  	$method_data = array();

    $method_data = array(
      'code'     => 'sslcommerz',
      'title'    => $this->language->get('text_title'),
      'sort_order' => '1'
    );
  	
    return $method_data;
  }
}
?>