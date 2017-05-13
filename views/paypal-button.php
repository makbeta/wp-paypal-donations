<!-- Begin PayPal Donations by https://www.tipsandtricks-hq.com/paypal-donations-widgets-plugin -->
<?php
$url = isset($pd_options['sandbox']) ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

//print_r($data);
?>

<form action="<?php echo apply_filters('paypal_donations_url', $url); ?>" method="post"<?php
if (isset($pd_options['new_tab'])) {
        echo ' target="_blank"';
}
?>>
    <div class="paypal-donations">
        <input type="hidden" name="cmd" value="_donations" />
        <input type="hidden" name="bn" value="TipsandTricks_SP" />
        <input type="hidden" name="business" value="<?php echo $pd_options['paypal_account']; ?>" />
<?php
        # Build the button
        $paypal_btn = '';
        $indent = str_repeat(" ", 8);

        // Optional Settings
        if ($pd_options['page_style'])
            $paypal_btn .=  $indent.'<input type="hidden" name="page_style" value="' .esc_attr($pd_options['page_style']). '" />'.PHP_EOL;
        if ($return_page)
            $paypal_btn .=  $indent.'<input type="hidden" name="return" value="' .esc_url($return_page). '" />'.PHP_EOL; // Return Page
        if ($purpose)
            $paypal_btn .=  apply_filters('paypal_donations_purpose_html', $indent.'<input type="hidden" name="item_name" value="' .esc_attr($purpose). '" />'.PHP_EOL);  // Purpose
        if ($reference)
            $paypal_btn .=  $indent.'<input type="hidden" name="item_number" value="' .esc_attr($reference). '" />'.PHP_EOL;  // Any reference for this donation
        if ($amount){
            if ($show_amount){
                $paypal_btn .= renderVisibleAmount($amount, $pd_options, $indent);
            }
            else {
              if(!is_numeric($amount)){
                  wp_die('Error! Donation amount must be a numeric value.');
              }
              $paypal_btn .=  $indent.'<input type="hidden" name="amount" value="' . apply_filters( 'paypal_donations_amount', $amount ) . '" />'.PHP_EOL;
            }
        }

        if (!empty($validate_ipn)){
            $notify_url = site_url() . '/?ppd_paypal_ipn=process';
            $paypal_btn .=  $indent.'<input type="hidden" name="notify_url" value="' .esc_url($notify_url). '" />'.PHP_EOL; // Notify URL
        }

        // More Settings
        if (isset($pd_options['return_method']))
            $paypal_btn .= $indent.'<input type="hidden" name="rm" value="' .esc_attr($pd_options['return_method']). '" />'.PHP_EOL;
        if (isset($pd_options['currency_code']))
            $paypal_btn .= $indent.'<input type="hidden" name="currency_code" value="' .esc_attr($pd_options['currency_code']). '" />'.PHP_EOL;
        if (isset($pd_options['button_localized']))
            { $button_localized = $pd_options['button_localized']; } else { $button_localized = 'en_US'; }
        if (isset($pd_options['set_checkout_language']) and $pd_options['set_checkout_language'] == true)
            $paypal_btn .= $indent.'<input type="hidden" name="lc" value="' .esc_attr($pd_options['checkout_language']). '" />'.PHP_EOL;

        // Settings not implemented yet
        //      $paypal_btn .=     '<input type="hidden" name="amount" value="20" />';

        // Get the button URL
        // custom image button
        if ($pd_options['button'] == "custom" && !empty($button_url)){
            $paypal_btn .=  $indent.'<input type="image" src="' .esc_url($button_url). '" name="submit" alt="PayPal - The safer, easier way to pay online." />'.PHP_EOL;
        }
        // custom text button
        else if ($pd_options['button'] == "custom" && !empty($button_text)){
            $paypal_btn .=  $indent.'<button type="submit">'.apply_filters('paypal_donations_button_text', $button_text).' </button>'.PHP_EOL;
        }
        // standard button
        else {
            $button_localized = apply_filters('pd_button_localized_value', $button_localized);
            $button_url = str_replace('en_US', $button_localized, $donate_buttons[$pd_options['button']]);
        }

        // PayPal stats tracking
        if (!isset($pd_options['disable_stats']) or $pd_options['disable_stats'] != true)
            $paypal_btn .=  $indent.'<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />'.PHP_EOL;
        echo $paypal_btn;
?>
    </div>
</form>
<!-- End PayPal Donations -->

<?php
function renderVisibleAmount($amount, $pd_options, $indent) {
    $paypal_btn = '';
    $amounts = explode(',', $amount);

  if (is_array($amounts)) {
      $paypal_btn .=  $indent. '<div class="paypal-donations__levels">'.PHP_EOL;
      foreach ($amounts as $amt) {
          $amt = trim($amt);
          if (is_numeric($amt)) {
              $label = $amt;
              // TODO: define full set of mappings for all currencies
              if (isset($pd_options['currency_code']) && $pd_options['currency_code'] == 'USD') {
                  $label = '$' . $amt;
              }
              $paypal_btn .=  $indent.$indent.'<span class="paypal-donations__level"><input type="radio" name="amount" value="' . apply_filters( 'paypal_donations_amount', $amt ) . '" id="paypal-donations__level--' . apply_filters( 'paypal_donations_amount', $amt ) . '"/> <label for="paypal-donations__level--' . apply_filters( 'paypal_donations_amount', $amt ) . '">'.$label.'</label></span>'.PHP_EOL;
          }
          else {
              wp_die('Error! Donation amount must be a numeric value.');
          }
      }
  }
  // show one amount input amount
  else {
      $paypal_btn .=  $indent.$indent.'<span class="paypal-donations__level"><input type="radio" name="amount" value="' . apply_filters( 'paypal_donations_amount', $amount ) . '" /> <label>'.$amount.'</label></span>'.PHP_EOL;
  }
  $paypal_btn .=  $indent. '</div>'.PHP_EOL;

  return $paypal_btn;
}
 ?>
