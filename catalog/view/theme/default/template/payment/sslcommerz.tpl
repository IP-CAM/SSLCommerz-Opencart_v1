<form action="<?php echo $action; ?>" method="post">
  <input type="hidden" name="store_id" value="<?php echo $store_id; ?>" /> 
  <input type="hidden" name="total_amount" value="<?php echo $total_amount; ?>" />
  <input type="hidden" name="tran_id" value="<?php echo $tran_id; ?>" />
  <input type="hidden" name="success_url" value="<?php echo $success_url; ?>" />
  <input type="hidden" name="fail_url" value="<?php echo $fail_url; ?>" />
  <input type="hidden" name="cancel_url" value="<?php echo $cancel_url; ?>" />
  
  <div class="buttons">
    <div class="right">
      <input type="submit" value="<?php echo $button_confirm; ?>" class="button" />
    </div>
  </div>
</form>