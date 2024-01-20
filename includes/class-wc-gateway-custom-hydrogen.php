<?php

/**
 * Class Tbz_WC_Hydrogen_Custom_Gateway.
 */
class WC_Gateway_Custom_Hydrogen extends WC_Gateway_Hydrogen_Subscriptions
{

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields()
	{

		$this->form_fields = array(
			'enabled'                          => array(
				'title'       => __('Enable/Disable', 'woo-hydrogen'),
				/* translators: payment method title */
				'label'       => sprintf(__('Enable Hydrogen - %s', 'woo-hydrogen'), $this->title),
				'type'        => 'checkbox',
				'description' => __('Enable this gateway as a payment option on the checkout page.', 'woo-hydrogen'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'title'                            => array(
				'title'       => __('Title', 'woo-hydrogen'),
				'type'        => 'text',
				'description' => __('This controls the payment method title which the user sees during checkout.', 'hydrogen-wc'),
				'desc_tip'    => true,
				'default'     => __('Hydrogen', 'hydrogen-wc'),
			),
			'description'                      => array(
				'title'       => __('Description', 'woo-hydrogen'),
				'type'        => 'textarea',
				'description' => __('This controls the payment method description which the user sees during checkout.', 'woo-hydrogen'),
				'desc_tip'    => true,
				'default'     => '',
			),
			'payment_page'                     => array(
				'title'       => __('Payment Option', 'woo-hydrogen'),
				'type'        => 'select',
				'description' => __('Popup shows the payment popup on the page while Redirect will redirect the customer to Hydrogen to make payment.', 'hydrogen-wc'),
				'default'     => '',
				'desc_tip'    => false,
				'options'     => array(
					''         => __('Select One', 'hydrogen-wc'),
					'inline'   => __('Popup', 'woo-hydrogen'),
					'redirect' => __('Redirect', 'woo-hydrogen'),
				),
			),
			'autocomplete_order'               => array(
				'title'       => __('Autocomplete Order After Payment', 'woo-hydrogen'),
				'label'       => __('Autocomplete Order', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'class'       => 'wc-hydrogen-autocomplete-order',
				'description' => __('If enabled, the order will be marked as complete after successful payment', 'woo-hydrogen'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'remove_cancel_order_button'       => array(
				'title'       => __('Remove Cancel Order & Restore Cart Button', 'woo-hydrogen'),
				'label'       => __('Remove the cancel order & restore cart button on the pay for order page', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no',
			),
			'subaccount_code'                  => array(
				'title'       => __('Subaccount Code', 'woo-hydrogen'),
				'type'        => 'text',
				'description' => __('Enter the subaccount code here.', 'woo-hydrogen'),
				'class'       => __('woocommerce_hydrogen_subaccount_code', 'woo-hydrogen'),
				'default'     => '',
			),
			'payment_channels'                 => array(
				'title'             => __('Payment Channels', 'woo-hydrogen'),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select wc-hydrogen-payment-channels',
				'description'       => __('The payment channels enabled for this gateway', 'woo-hydrogen'),
				'default'           => '',
				'desc_tip'          => true,
				'select_buttons'    => true,
				'options'           => $this->channels(),
				'custom_attributes' => array(
					'data-placeholder' => __('Select payment channels', 'woo-hydrogen'),
				),
			),
			'cards_allowed'                    => array(
				'title'             => __('Allowed Card Brands', 'woo-hydrogen'),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select wc-hydrogen-cards-allowed',
				'description'       => __('The card brands allowed for this gateway. This filter only works with the card payment channel.', 'woo-hydrogen'),
				'default'           => '',
				'desc_tip'          => true,
				'select_buttons'    => true,
				'options'           => $this->card_types(),
				'custom_attributes' => array(
					'data-placeholder' => __('Select card brands', 'woo-hydrogen'),
				),
			),
			'banks_allowed'                    => array(
				'title'             => __('Allowed Banks Card', 'woo-hydrogen'),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select wc-hydrogen-banks-allowed',
				'description'       => __('The banks whose card should be allowed for this gateway. This filter only works with the card payment channel.', 'woo-hydrogen'),
				'default'           => '',
				'desc_tip'          => true,
				'select_buttons'    => true,
				'options'           => $this->banks(),
				'custom_attributes' => array(
					'data-placeholder' => __('Select banks', 'woo-hydrogen'),
				),
			),
			'payment_icons'                    => array(
				'title'             => __('Payment Icons', 'woo-hydrogen'),
				'type'              => 'multiselect',
				'class'             => 'wc-enhanced-select wc-hydrogen-payment-icons',
				'description'       => __('The payment icons to be displayed on the checkout page.', 'woo-hydrogen'),
				'default'           => '',
				'desc_tip'          => true,
				'select_buttons'    => true,
				'options'           => $this->payment_icons(),
				'custom_attributes' => array(
					'data-placeholder' => __('Select payment icons', 'woo-hydrogen'),
				),
			),
			'custom_metadata'                  => array(
				'title'       => __('Custom Metadata', 'woo-hydrogen'),
				'label'       => __('Enable Custom Metadata', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'class'       => 'wc-hydrogen-metadata',
				'description' => __('If enabled, you will be able to send more information about the order to Hydrogen.', 'woo-hydrogen'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_order_id'                    => array(
				'title'       => __('Order ID', 'woo-hydrogen'),
				'label'       => __('Send Order ID', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'class'       => 'wc-hydrogen-meta-order-id',
				'description' => __('If checked, the Order ID will be sent to Hydrogen', 'woo-hydrogen'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_name'                        => array(
				'title'       => __('Customer Name', 'woo-hydrogen'),
				'label'       => __('Send Customer Name', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'class'       => 'wc-hydrogen-meta-name',
				'description' => __('If checked, the customer full name will be sent to Hydrogen', 'woo-hydrogen'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_email'                       => array(
				'title'       => __('Customer Email', 'woo-hydrogen'),
				'label'       => __('Send Customer Email', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'class'       => 'wc-hydrogen-meta-email',
				'description' => __('If checked, the customer email address will be sent to Hydrogen', 'woo-hydrogen'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_phone'                       => array(
				'title'       => __('Customer Phone', 'woo-hydrogen'),
				'label'       => __('Send Customer Phone', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'class'       => 'wc-hydrogen-meta-phone',
				'description' => __('If checked, the customer phone will be sent to Hydrogen', 'woo-hydrogen'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_billing_address'             => array(
				'title'       => __('Order Billing Address', 'woo-hydrogen'),
				'label'       => __('Send Order Billing Address', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'class'       => 'wc-hydrogen-meta-billing-address',
				'description' => __('If checked, the order billing address will be sent to Hydrogen', 'woo-hydrogen'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_shipping_address'            => array(
				'title'       => __('Order Shipping Address', 'woo-hydrogen'),
				'label'       => __('Send Order Shipping Address', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'class'       => 'wc-hydrogen-meta-shipping-address',
				'description' => __('If checked, the order shipping address will be sent to Hydrogen', 'woo-hydrogen'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'meta_products'                    => array(
				'title'       => __('Product(s) Purchased', 'woo-hydrogen'),
				'label'       => __('Send Product(s) Purchased', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'class'       => 'wc-hydrogen-meta-products',
				'description' => __('If checked, the product(s) purchased will be sent to Hydrogen', 'woo-hydrogen'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
		);
	}

	/**
	 * Admin Panel Options.
	 */
	public function admin_options()
	{

		$hydrogen_settings_url = admin_url('admin.php?page=wc-settings&tab=checkout&section=hydrogen');
		$checkout_settings_url = admin_url('admin.php?page=wc-settings&tab=checkout');
?>

		<h2>
			<?php
			/* translators: payment method title */
			printf(__('Hydrogen - %s', 'woo-hydrogen'), esc_attr($this->title));
			?>
			<?php
			if (function_exists('wc_back_link')) {
				wc_back_link(__('Return to payments', 'woo-hydrogen'), $checkout_settings_url);
			}
			?>
		</h2>

		<h4>
			<?php
			/* translators: link to Hydrogen developers settings page */
			printf(__('Important: To avoid situations where bad network makes it impossible to verify transactions, set your webhook URL <a href="%s" target="_blank" rel="noopener noreferrer">here</a> to the URL below', 'hydrogen-woocommerce'), '#');
			?>
		</h4>

		<p style="color: red">
			<code><?php echo esc_url(WC()->api_request_url('hydrogen-wc_webhook')); ?></code>
		</p>

		<p>
			<?php
			/* translators: link to hydrogen general settings page */
			printf(__('To configure your Hydrogen Authentication Token and enable/disable test mode, do that <a href="%s">here</a>', 'hydrogen-woocommerce'), esc_url($hydrogen_settings_url));
			?>
		</p>

<?php

		if ($this->is_valid_for_use()) {

			echo '<table class="form-table">';
			$this->generate_settings_html();
			echo '</table>';
		} else {

			/* translators: disabled message */
			echo '<div class="inline error"><p><strong>' . sprintf(__('Hydrogen Payment Gateway Disabled: %s', 'hydrogen-woocommerce'), esc_attr($this->msg)) . '</strong></p></div>';
		}
	}

	/**
	 * Payment Channels.
	 */
	public function channels()
	{

		return array(
			'card'          => __('Cards', 'woo-hydrogen'),
			'bank'          => __('Pay with Bank', 'woo-hydrogen'),
			'ussd'          => __('USSD', 'woo-hydrogen'),
			'qr'            => __('QR', 'woo-hydrogen'),
			'bank_transfer' => __('Bank Transfer', 'woo-hydrogen'),
		);
	}

	/**
	 * Card Types.
	 */
	public function card_types()
	{

		return array(
			'visa'       => __('Visa', 'woo-hydrogen'),
			'verve'      => __('Verve', 'woo-hydrogen'),
			'mastercard' => __('Mastercard', 'woo-hydrogen'),
		);
	}

	/**
	 * Banks.
	 */
	public function banks()
	{

		return array(
			'044'  => __('Access Bank', 'woo-hydrogen'),
			'035A' => __('ALAT by WEMA', 'woo-hydrogen'),
			'401'  => __('ASO Savings and Loans', 'woo-hydrogen'),
			'023'  => __('Citibank Nigeria', 'woo-hydrogen'),
			'063'  => __('Access Bank (Diamond)', 'woo-hydrogen'),
			'050'  => __('Ecobank Nigeria', 'woo-hydrogen'),
			'562'  => __('Ekondo Microfinance Bank', 'woo-hydrogen'),
			'084'  => __('Enterprise Bank', 'woo-hydrogen'),
			'070'  => __('Fidelity Bank', 'woo-hydrogen'),
			'011'  => __('First Bank of Nigeria', 'woo-hydrogen'),
			'214'  => __('First City Monument Bank', 'woo-hydrogen'),
			'058'  => __('Guaranty Trust Bank', 'woo-hydrogen'),
			'030'  => __('Heritage Bank', 'woo-hydrogen'),
			'301'  => __('Jaiz Bank', 'woo-hydrogen'),
			'082'  => __('Keystone Bank', 'woo-hydrogen'),
			'014'  => __('MainStreet Bank', 'woo-hydrogen'),
			'526'  => __('Parallex Bank', 'woo-hydrogen'),
			'076'  => __('Polaris Bank Limited', 'woo-hydrogen'),
			'101'  => __('Providus Bank', 'woo-hydrogen'),
			'221'  => __('Stanbic IBTC Bank', 'woo-hydrogen'),
			'068'  => __('Standard Chartered Bank', 'woo-hydrogen'),
			'232'  => __('Sterling Bank', 'woo-hydrogen'),
			'100'  => __('Suntrust Bank', 'woo-hydrogen'),
			'032'  => __('Union Bank of Nigeria', 'woo-hydrogen'),
			'033'  => __('United Bank For Africa', 'woo-hydrogen'),
			'215'  => __('Unity Bank', 'woo-hydrogen'),
			'035'  => __('Wema Bank', 'woo-hydrogen'),
			'057'  => __('Zenith Bank', 'woo-hydrogen'),
		);
	}

	/**
	 * Payment Icons.
	 */
	public function payment_icons()
	{

		return array(
			'verve'         => __('Verve', 'woo-hydrogen'),
			'visa'          => __('Visa', 'woo-hydrogen'),
			'mastercard'    => __('Mastercard', 'woo-hydrogen'),
			'hydrogenwhite' => __('Secured by Hydrogen White', 'woo-hydrogen'),
			'hydrogenblue'  => __('Secured by Hydrogen Blue', 'woo-hydrogen'),
			'hydrogen-wc'   => __('Hydrogen Nigeria', 'woo-hydrogen'),
			'hydrogen-gh'   => __('Hydrogen Ghana', 'woo-hydrogen'),
			'access'        => __('Access Bank', 'woo-hydrogen'),
			'alat'          => __('ALAT by WEMA', 'woo-hydrogen'),
			'aso'           => __('ASO Savings and Loans', 'woo-hydrogen'),
			'citibank'      => __('Citibank Nigeria', 'woo-hydrogen'),
			'diamond'       => __('Access Bank (Diamond)', 'woo-hydrogen'),
			'ecobank'       => __('Ecobank Nigeria', 'woo-hydrogen'),
			'ekondo'        => __('Ekondo Microfinance Bank', 'woo-hydrogen'),
			'enterprise'    => __('Enterprise Bank', 'woo-hydrogen'),
			'fidelity'      => __('Fidelity Bank', 'woo-hydrogen'),
			'firstbank'     => __('First Bank of Nigeria', 'woo-hydrogen'),
			'fcmb'          => __('First City Monument Bank', 'woo-hydrogen'),
			'gtbank'        => __('Guaranty Trust Bank', 'woo-hydrogen'),
			'heritage'      => __('Heritage Bank', 'woo-hydrogen'),
			'jaiz'          => __('Jaiz Bank', 'woo-hydrogen'),
			'keystone'      => __('Keystone Bank', 'woo-hydrogen'),
			'mainstreet'    => __('MainStreet Bank', 'woo-hydrogen'),
			'parallex'      => __('Parallex Bank', 'woo-hydrogen'),
			'polaris'       => __('Polaris Bank Limited', 'woo-hydrogen'),
			'providus'      => __('Providus Bank', 'woo-hydrogen'),
			'stanbic'       => __('Stanbic IBTC Bank', 'woo-hydrogen'),
			'standard'      => __('Standard Chartered Bank', 'woo-hydrogen'),
			'sterling'      => __('Sterling Bank', 'woo-hydrogen'),
			'suntrust'      => __('Suntrust Bank', 'woo-hydrogen'),
			'union'         => __('Union Bank of Nigeria', 'woo-hydrogen'),
			'uba'           => __('United Bank For Africa', 'woo-hydrogen'),
			'unity'         => __('Unity Bank', 'woo-hydrogen'),
			'wema'          => __('Wema Bank', 'woo-hydrogen'),
			'zenith'        => __('Zenith Bank', 'woo-hydrogen'),
		);
	}

	/**
	 * Display the selected payment icon.
	 */
	public function get_icon()
	{
		$icon_html = '<img src="' . WC_HTTPS::force_https_url(WC_HYDROGEN_URL . '/assets/images/hydrogen.png') . '" alt="hydrogen" style="height: 40px; margin-right: 0.4em;margin-bottom: 0.6em;" />';
		$icon      = $this->payment_icons;

		if (is_array($icon)) {

			$additional_icon = '';

			foreach ($icon as $i) {
				$additional_icon .= '<img src="' . WC_HTTPS::force_https_url(WC_HYDROGEN_URL . '/assets/images/' . $i . '.png') . '" alt="' . $i . '" style="height: 40px; margin-right: 0.4em;margin-bottom: 0.6em;" />';
			}

			$icon_html .= $additional_icon;
		}

		return apply_filters('woocommerce_gateway_icon', $icon_html, $this->id);
	}

	/**
	 * Outputs scripts used for hydrogen payment.
	 */
	public function payment_scripts()
	{

		if (isset($_GET['pay_for_order']) || !is_checkout_pay_page()) {
			return;
		}

		if ($this->enabled === 'no') {
			return;
		}

		$order_key = urldecode($_GET['key']);
		$order_id  = absint(get_query_var('order-pay'));

		$order = wc_get_order($order_id);

		if ($this->id !== $order->get_payment_method()) {
			return;
		}

		$suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		wp_enqueue_script('jquery');


		$hydrogen_params = array(
			'key' => $this->public_key,
		);

		if (is_checkout_pay_page() && get_query_var('order-pay')) {

			$email = $order->get_billing_email();

			$amount = $order->get_total();

			$txnref = $order_id . '_' . time();

			$the_order_id  = $order->get_id();
			$the_order_key = $order->get_order_key();
			$currency      = $order->get_currency();

			if ($the_order_id == $order_id && $the_order_key == $order_key) {

				$hydrogen_params['email']    = $email;
				$hydrogen_params['amount']   = $amount;
				$hydrogen_params['txnref']   = $txnref;
				$hydrogen_params['currency'] = $currency;
			}

			if ($this->split_payment) {

				$hydrogen_params['subaccount_code']     = $this->subaccount_code;
				$hydrogen_params['charges_account']     = $this->charges_account;
				$hydrogen_params['transaction_charges'] = $this->transaction_charges * 100;
			}

			if (in_array('bank', $this->payment_channels)) {
				$hydrogen_params['bank_channel'] = 'true';
			}

			if (in_array('card', $this->payment_channels)) {
				$hydrogen_params['card_channel'] = 'true';
			}

			if (in_array('ussd', $this->payment_channels)) {
				$hydrogen_params['ussd_channel'] = 'true';
			}

			if (in_array('qr', $this->payment_channels)) {
				$hydrogen_params['qr_channel'] = 'true';
			}

			if (in_array('bank_transfer', $this->payment_channels)) {
				$hydrogen_params['bank_transfer_channel'] = 'true';
			}

			if ($this->banks) {

				$hydrogen_params['banks_allowed'] = $this->banks;
			}

			if ($this->cards) {

				$hydrogen_params['cards_allowed'] = $this->cards;
			}

			if ($this->custom_metadata) {

				if ($this->meta_order_id) {

					$hydrogen_params['meta_order_id'] = $order_id;
				}

				if ($this->meta_name) {

					$hydrogen_params['meta_name'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
				}

				if ($this->meta_email) {

					$hydrogen_params['meta_email'] = $email;
				}

				if ($this->meta_phone) {

					$hydrogen_params['meta_phone'] = $order->get_billing_phone();
				}

				if ($this->meta_products) {

					$line_items = $order->get_items();

					$products = '';

					foreach ($line_items as $item_id => $item) {
						$name      = $item['name'];
						$quantity  = $item['qty'];
						$products .= $name . ' (Qty: ' . $quantity . ')';
						$products .= ' | ';
					}

					$products = rtrim($products, ' | ');

					$hydrogen_params['meta_products'] = $products;
				}

				if ($this->meta_billing_address) {

					$billing_address = $order->get_formatted_billing_address();
					$billing_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $billing_address));

					$hydrogen_params['meta_billing_address'] = $billing_address;
				}

				if ($this->meta_shipping_address) {

					$shipping_address = $order->get_formatted_shipping_address();
					$shipping_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $shipping_address));

					if (empty($shipping_address)) {

						$billing_address = $order->get_formatted_billing_address();
						$billing_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $billing_address));

						$shipping_address = $billing_address;
					}

					$hydrogen_params['meta_shipping_address'] = $shipping_address;
				}
			}

			$order->update_meta_data('_hydrogen_txn_ref', $txnref);
			$order->save();
		}

		wp_localize_script('wc_hydrogen', 'wc_hydrogen_params', $hydrogen_params);
	}

	/**
	 * Add custom gateways to the checkout page.
	 *
	 * @param $available_gateways
	 *
	 * @return mixed
	 */
	public function add_gateway_to_checkout($available_gateways)
	{

		if ($this->enabled == 'no') {
			unset($available_gateways[$this->id]);
		}

		return $available_gateways;
	}
}
