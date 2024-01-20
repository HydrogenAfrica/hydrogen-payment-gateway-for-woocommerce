<?php

if (!defined('ABSPATH')) {
	exit;
}

class WC_Gateway_Hydrogen extends WC_Payment_Gateway_CC
{

	/**
	 * Is test mode active?
	 *
	 * @var bool
	 */
	public $testmode;

	/**
	 * Should orders be marked as complete after payment?
	 * 
	 * @var bool
	 */
	public $autocomplete_order;

	/**
	 * Hydrogen payment page type.
	 *
	 * @var string
	 */
	public $payment_page;

	/**
	 * Hydrogen test public key.
	 *
	 * @var string
	 */
	public $test_public_key;

	/**
	 * Hydrogen test secret key.
	 *
	 * @var string
	 */
	public $test_secret_key;

	/**
	 * Hydrogen live public key.
	 *
	 * @var string
	 */
	public $live_public_key;

	/**
	 * Hydrogen live secret key.
	 *
	 * @var string
	 */
	public $live_secret_key;

	/**
	 * Should we save customer cards?
	 *
	 * @var bool
	 */
	public $saved_cards;

	/**
	 * Should Hydrogen split payment be enabled.
	 *
	 * @var bool
	 */
	public $split_payment;

	/**
	 * Should the cancel & remove order button be removed on the pay for order page.
	 *
	 * @var bool
	 */
	public $remove_cancel_order_button;

	/**
	 * Hydrogen sub account code.
	 *
	 * @var string
	 */
	public $subaccount_code;

	/**
	 * Who bears Hydrogen charges?
	 *
	 * @var string
	 */
	public $charges_account;

	/**
	 * A flat fee to charge the sub account for each transaction.
	 *
	 * @var string
	 */
	public $transaction_charges;

	/**
	 * Should custom metadata be enabled?
	 *
	 * @var bool
	 */
	public $custom_metadata;

	/**
	 * Should the order id be sent as a custom metadata to Hydrogen?
	 *
	 * @var bool
	 */
	public $meta_order_id;

	/**
	 * Should the customer name be sent as a custom metadata to Hydrogen?
	 *
	 * @var bool
	 */
	public $meta_name;

	/**
	 * Should the billing email be sent as a custom metadata to Hydrogen?
	 *
	 * @var bool
	 */
	public $meta_email;

	/**
	 * Should the billing phone be sent as a custom metadata to Hydrogen?
	 *
	 * @var bool
	 */
	public $meta_phone;

	/**
	 * Should the billing address be sent as a custom metadata to Hydrogen?
	 *
	 * @var bool
	 */
	public $meta_billing_address;

	/**
	 * Should the shipping address be sent as a custom metadata to Hydrogen?
	 *
	 * @var bool
	 */
	public $meta_shipping_address;

	/**
	 * Should the order items be sent as a custom metadata to Hydrogen?
	 *
	 * @var bool
	 */
	public $meta_products;

	/**
	 * API public key
	 *
	 * @var string
	 */
	public $public_key;

	/**
	 * API secret key
	 *
	 * @var string
	 */
	public $secret_key;

	/**
	 * Gateway disabled message
	 *
	 * @var string
	 */
	public $msg;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->id                 = 'hydrogen';
		$this->method_title       = __('Hydrogen Payment Gateway', 'woo-hydrogen');
		$this->method_description = sprintf(__('Hydrogen Payment Gateway helps you process payments using cards and account transfers for faster delivery of goods and services.. <a href="%1$s" target="_blank">Sign up</a> for a Hydrogen account, and <a href="%2$s" target="_blank">get your authentication token</a>.', 'hydrogen-woocommerce'), 'https://dashboard.hydrogenpay.com/', 'https://dashboard.hydrogenpay.com/merchant/profile/api-integration');
		$this->has_fields         = true;

		$this->payment_page = $this->get_option('payment_page');

		$this->supports = array(
			'products',
			'refunds',
			'tokenization',
			'subscriptions',
			'multiple_subscriptions',
			'subscription_cancellation',
			'subscription_suspension',
			'subscription_reactivation',
			'subscription_amount_changes',
			'subscription_date_changes',
			'subscription_payment_method_change',
			'subscription_payment_method_change_customer',
		);

		// Load the form fields
		$this->init_form_fields();

		// Load the settings
		$this->init_settings();

		// Get setting values

		$this->title              = $this->get_option('title');
		$this->description        = $this->get_option('description');
		$this->enabled            = $this->get_option('enabled');
		$this->testmode           = $this->get_option('testmode') === 'yes' ? true : false;
		$this->autocomplete_order = $this->get_option('autocomplete_order') === 'yes' ? true : false;

		$this->test_public_key = $this->get_option('test_public_key');
		$this->test_secret_key = $this->get_option('test_secret_key');

		$this->live_public_key = $this->get_option('live_public_key');
		$this->live_secret_key = $this->get_option('live_secret_key');

		$this->saved_cards = $this->get_option('saved_cards') === 'yes' ? true : false;

		$this->split_payment              = $this->get_option('split_payment') === 'yes' ? true : false;
		$this->remove_cancel_order_button = $this->get_option('remove_cancel_order_button') === 'yes' ? true : false;
		$this->subaccount_code            = $this->get_option('subaccount_code');
		$this->charges_account            = $this->get_option('split_payment_charge_account');
		$this->transaction_charges        = $this->get_option('split_payment_transaction_charge');

		$this->custom_metadata = $this->get_option('custom_metadata') === 'yes' ? true : false;

		$this->meta_order_id         = $this->get_option('meta_order_id') === 'yes' ? true : false;
		$this->meta_name             = $this->get_option('meta_name') === 'yes' ? true : false;
		$this->meta_email            = $this->get_option('meta_email') === 'yes' ? true : false;
		$this->meta_phone            = $this->get_option('meta_phone') === 'yes' ? true : false;
		$this->meta_billing_address  = $this->get_option('meta_billing_address') === 'yes' ? true : false;
		$this->meta_shipping_address = $this->get_option('meta_shipping_address') === 'yes' ? true : false;
		$this->meta_products         = $this->get_option('meta_products') === 'yes' ? true : false;

		$this->public_key = $this->testmode ? $this->test_public_key : $this->live_public_key;
		$this->secret_key = $this->testmode ? $this->test_secret_key : $this->live_secret_key;

		// Hooks
		add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));

		add_action('admin_notices', array($this, 'admin_notices'));
		add_action(
			'woocommerce_update_options_payment_gateways_' . $this->id,
			array(
				$this,
				'process_admin_options',
			)
		);

		add_action('woocommerce_receipt_' . $this->id, array($this, 'receipt_page'));

		// Payment listener/API hook.
		// add_action('woocommerce_api_wc_gateway_hydrogen', array($this, 'verify_hydrogen_transaction'));

		// Webhook listener/API hook.
		add_action('woocommerce_api_tbz_wc_hydrogen_webhook', array($this, 'process_webhooks'));

		add_action('woocommerce_api_hydrogen_wc_payment', array($this, 'hydrogen_wc_payment_popup_action'));


		// Hydrogen Payment confirmation listener/API hook  .
		add_action('woocommerce_api_wc_gateway_hydrogen', array($this, 'verify_hydrogen_wc_transaction'));

		//Hydrogen Popup confirmation test
		add_action('woocommerce_api_wc_gateway_hydrogen_popup', array($this, 'verify_hydrogen_wc_transaction_popup'));

		// Check if the gateway can be used.
		if (!$this->is_valid_for_use()) {
			$this->enabled = false;
		}
	}

	/**
	 * Check if this gateway is enabled and available in the user's country.
	 */
	public function is_valid_for_use()
	{

		if (!in_array(get_woocommerce_currency(), apply_filters('woocommerce_hydrogen_supported_currencies', array('NGN', 'USD', 'ZAR', 'GHS', 'KES', 'XOF', 'EGP')))) {

			$this->msg = sprintf(__('Hydrogen does not support your store currency. Kindly set it to either NGN (&#8358), GHS (&#x20b5;), USD (&#36;), KES (KSh), ZAR (R), XOF (CFA), or EGP (E£) <a href="%s">here</a>', 'woo-hydrogen'), admin_url('admin.php?page=wc-settings&tab=general'));

			return false;
		}

		return true;
	}

	/**
	 * Display hydrogen payment icon.
	 */
	public function get_icon()
	{

		$base_location = wc_get_base_location();

		if ('GH' === $base_location['country']) {
			$icon = '<img src="' . WC_HTTPS::force_https_url(plugins_url('assets/images/hydrogen-gh.png', WC_HYDROGEN_MAIN_FILE)) . '" alt="Hydrogen Payment Options" />';
		} elseif ('ZA' === $base_location['country']) {
			$icon = '<img src="' . WC_HTTPS::force_https_url(plugins_url('assets/images/hydrogen-za.png', WC_HYDROGEN_MAIN_FILE)) . '" alt="Hydrogen Payment Options" />';
		} elseif ('KE' === $base_location['country']) {
			$icon = '<img src="' . WC_HTTPS::force_https_url(plugins_url('assets/images/hydrogen-ke.png', WC_HYDROGEN_MAIN_FILE)) . '" alt="Hydrogen Payment Options" />';
		} else {
			$icon = '<img src="' . WC_HTTPS::force_https_url(plugins_url('assets/images/hydrogen-wc.png', WC_HYDROGEN_MAIN_FILE)) . '" alt="Hydrogen Payment Options" />';
		}

		return apply_filters('woocommerce_gateway_icon', $icon, $this->id);
	}


	/**
	 * Check if Hydrogen merchant details is filled.
	 */
	public function admin_notices()
	{

		if ($this->enabled == 'no') {
			return;
		}

		// Check required fields.
		if (!($this->public_key || $this->secret_key)) {
			echo '<div class="error"><p>' . sprintf(__('Please enter your Hydrogen merchant details <a href="%s">here</a> to be able to use the Hydrogen WooCommerce plugin.', 'woo-hydrogen'), admin_url('admin.php?page=wc-settings&tab=checkout&section=hydrogen')) . '</p></div>';
			return;
		}
	}

	/**
	 * Check if Hydrogen gateway is enabled.
	 *
	 * @return bool
	 */
	public function is_available()
	{

		if ('yes' == $this->enabled) {

			// if ( ! ( $this->public_key && $this->secret_key ) ) {

			if (!($this->secret_key)) {


				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * Admin Panel Options.
	 */
	public function admin_options()
	{

?>

		<h2><?php _e('Hydrogen Payment Gateway', 'woo-hydrogen'); ?>
			<?php
			if (function_exists('wc_back_link')) {
				wc_back_link(__('Return to payments', 'woo-hydrogen'), admin_url('admin.php?page=wc-settings&tab=checkout'));
			}
			?>
		</h2>

		<h4>
			<strong><?php printf(__('Optional: To avoid situations where bad network makes it impossible to verify transactions, set your webhook URL <a href="%1$s" target="_blank" rel="noopener noreferrer">here</a> to the URL below<span style="color: red"><pre><code>%2$s</code></pre></span>', 'hydrogen-wc'), '#', WC()->api_request_url('hydrogen-wc_webhook')); ?></strong>
		</h4>

		<?php

		if ($this->is_valid_for_use()) {

			echo '<table class="form-table">';
			$this->generate_settings_html();
			echo '</table>';
		} else {
		?>
			<div class="inline error">
				<p><strong><?php _e('Hydrogen Payment Gateway Disabled', 'woo-hydrogen'); ?></strong>: <?php echo $this->msg; ?></p>
			</div>

<?php
		}
	}

	/**
	 * Initialise Gateway Settings Form Fields.
	 */
	public function init_form_fields()
	{

		$form_fields = array(
			'enabled'                          => array(
				'title'       => __('Enable/Disable', 'woo-hydrogen'),
				'label'       => __('Enable hydrogen', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'description' => __('Enable Hydrogen as a payment option on the checkout page.', 'hydrogen-woocommerce-payment'),
				'default'     => 'no',
				'desc_tip'    => true,
			),
			'title'                            => array(
				'title'       => __('Title', 'hydrogen-woocommerce-payment'),
				'type'        => 'text',
				'description' => __('This controls the payment method title which the user sees during checkout.', 'hydrogen-woocommerce-payment'),
				'default'     => __('Debit/Credit Cards', 'hydrogen-woocommerce-payment'),
				'desc_tip'    => true,
			),
			'description'                      => array(
				'title'       => __('Description', 'hydrogen-wc'),
				'type'        => 'textarea',
				'description' => __('This controls the payment method description which the user sees during checkout.', 'hydrogen-wc'),
				'default'     => __('Make payment using your debit and credit cards', 'hydrogen-wc'),
				'desc_tip'    => true,
			),
			'testmode'                         => array(
				'title'       => __('Test mode', 'woo-hydrogen'),
				'label'       => __('Enable Test Mode', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'description' => __('Test mode enables you to test payments before going live. <br />Once the LIVE MODE is enabled on your Hydrogen account uncheck this.', 'woo-hydrogen'),
				'default'     => 'yes',
				'desc_tip'    => true,
			),
			'payment_page'                     => array(
				'title'       => __('Payment Option', 'hydrogen-wc'),
				'type'        => 'select',
				'description' => __('Popup shows the payment popup on the page while Redirect will redirect the customer to Hydrogen to make payment.', 'woo-hydrogen'),
				'default'     => '',
				'desc_tip'    => false,
				'options'     => array(
					''          => __('Select One', 'woo-hydrogen'),
					'inline'    => __('Popup', 'woo-hydrogen'),
					'redirect'  => __('Redirect', 'woo-hydrogen'),
				),
			),
			'test_secret_key'                  => array(
				'title'       => __('Test Authentication Token', 'woo-hydrogen'),
				'type'        => 'password',
				'description' => __('Enter your Test Authentication Token here', 'woo-hydrogen'),
				'default'     => '',
			),
			'live_secret_key'                  => array(
				'title'       => __('Live Authentication Token', 'woo-hydrogen'),
				'type'        => 'password',
				'description' => __('Enter your Live Authentication here.', 'woo-hydrogen'),
				'default'     => '',
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

			'saved_cards'                      => array(
				'title'       => __('Saved Cards', 'woo-hydrogen'),
				'label'       => __('Enable Payment via Saved Cards', 'woo-hydrogen'),
				'type'        => 'checkbox',
				'description' => __('If enabled, users will be able to pay with a saved card during checkout. Card details are saved on Hydrogen servers, not on your store.<br>Note that you need to have a valid SSL certificate installed.', 'woo-hydrogen'),
				'default'     => 'no',
				'desc_tip'    => true,
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

		if ('NGN' !== get_woocommerce_currency()) {
			unset($form_fields['custom_gateways']);
		}

		$this->form_fields = $form_fields;
	}

	/**
	 * Payment form on checkout page
	 */
	public function payment_fields()
	{

		if ($this->description) {
			echo wpautop(wptexturize($this->description));
		}

		if (!is_ssl()) {
			return;
		}

		if ($this->supports('tokenization') && is_checkout() && $this->saved_cards && is_user_logged_in()) {
			$this->tokenization_script();
			$this->saved_payment_methods();
			$this->save_payment_method_checkbox();
		}
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

		$script_src = $this->testmode ?
			'https://hydrogenshared.blob.core.windows.net/paymentgateway/paymentGatewayInegration.js' :
			'https://hydrogenshared.blob.core.windows.net/paymentgateway/HydrogenPGIntegration.js';

		$secret_key = $this->testmode ? $this->test_secret_key : $this->live_secret_key;

		$suffix = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		wp_enqueue_script('jquery');

		wp_enqueue_script('hydrogen', $script_src, array('jquery'), WC_HYDROGEN_VERSION, false);

		// Add 'module' attribute to the script tag
		wp_script_add_data('hydrogen', 'module', true);

		wp_enqueue_script('wc_hydrogen', plugins_url('assets/js/hydrogen' . $suffix . '.js', WC_HYDROGEN_MAIN_FILE), array('jquery', 'hydrogen'), WC_HYDROGEN_VERSION, false);

		$hydrogen_params = array(
			// 'key' => $this->secret_key,
			'key' => $secret_key,
		);

		if (is_checkout_pay_page() && get_query_var('order-pay')) {

			$email         = $order->get_billing_email();
			$amount        = $order->get_total(); // hydrogen payment amount
			$txnref        = $order_id . '_' . time();
			$the_order_id  = $order->get_id();
			$the_order_key = $order->get_order_key();
			$currency      = $order->get_currency();

			if ($the_order_id == $order_id && $the_order_key == $order_key) {

				$hydrogen_params['email']    = $email;
				$hydrogen_params['amount']   = $amount;
				$hydrogen_params['txnref']   = $txnref;
				$hydrogen_params['currency'] = $currency;

				// Get the "My Account" URL
				$hydrogen_wc_redirect_url = wc_get_page_permalink('myaccount');

				// Pass the "My Account" URL to your JavaScript
				$hydrogen_params['hydrogen_wc_redirect_url'] = $hydrogen_wc_redirect_url;
			}

			if ($this->split_payment) {

				$hydrogen_params['subaccount_code'] = $this->subaccount_code;
				$hydrogen_params['charges_account'] = $this->charges_account;

				if (empty($this->transaction_charges)) {
					$hydrogen_params['transaction_charges'] = '';
				} else {
					$hydrogen_params['transaction_charges'] = $this->transaction_charges * 100;
				}
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
	 * Load admin scripts.
	 */
	public function admin_scripts()
	{

		if ('woocommerce_page_wc-settings' !== get_current_screen()->id) {
			return;
		}

		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

		$hydrogen_admin_params = array(
			'plugin_url' => WC_HYDROGEN_URL,
		);

		wp_enqueue_script('wc_hydrogen_admin', plugins_url('assets/js/hydrogen-admin' . $suffix . '.js', WC_HYDROGEN_MAIN_FILE), array(), WC_HYDROGEN_VERSION, true);

		wp_localize_script('wc_hydrogen_admin', 'wc_hydrogen_admin_params', $hydrogen_admin_params);
	}

	/**
	 * Process the payment for Hydrogen.
	 *
	 * @param int $order_id
	 *
	 * @return array|void
	 */
	public function process_payment($order_id)
	{

		if ('redirect' === $this->payment_page) {

			// For the 'redirect' payment method, initiate the redirect payment option

			return $this->process_redirect_payment_option($order_id);
		} elseif (isset($_POST['wc-' . $this->id . '-payment-token']) && 'new' !== $_POST['wc-' . $this->id . '-payment-token']) {

			// Hydrogen Payment with token

			$token_id = wc_clean($_POST['wc-' . $this->id . '-payment-token']);

			$token    = \WC_Payment_Tokens::get($token_id);

			if ($token->get_user_id() !== get_current_user_id()) {

				wc_add_notice('Invalid token ID', 'error');

				return;
			} else {

				$status = $this->process_token_payment($token->get_token(), $order_id);

				if ($status) {

					$order = wc_get_order($order_id);

					return array(
						'result'   => 'success',
						'redirect' => $this->get_return_url($order),
					);
				}
			}
		} else {

			// Handle other payment scenarios for Hydrogen

			$order = wc_get_order($order_id);

			if (is_user_logged_in() && isset($_POST['wc-' . $this->id . '-new-payment-method']) && true === (bool) $_POST['wc-' . $this->id . '-new-payment-method'] && $this->saved_cards) {

				$order->update_meta_data('_wc_hydrogen_save_card', true);

				$order->save();
			}

			return array(
				'result'   => 'success',
				'redirect' => $order->get_checkout_payment_url(true),
			);
		}
	}

	/**
	 * Process a redirect payment option payment.
	 *
	 * @since 5.7
	 * @param int $order_id
	 * @return array|void
	 */

	public function process_redirect_payment_option($order_id)
	{
		$order = wc_get_order($order_id);
		$amount = $order->get_total(); // Hydrogen payment amount
		$txnref = $order_id . '_' . time();

		if ($this->testmode) {

			$secret_key = $this->test_secret_key;

			$hydrogen_url = 'https://qa-dev.hydrogenpay.com/qa/bepay/api/v1/merchant/initiate-payment';

			// Your code for test mode
		} else {

			$secret_key = $this->live_secret_key;

			$hydrogen_url = 'https://api.hydrogenpay.com/bepay/api/v1/merchant/initiate-payment';

			// Your code for live mode
		}

		$callback_url = $order->get_checkout_payment_url(true);

		$payment_channels = $this->get_gateway_payment_channels($order);

		$hydrogen_params1 = array(
			'amount' => $amount,
			'email' => $order->get_billing_email(),
			'currency' => $order->get_currency(),
			'description' => 'Payment for items ordered with ID  ' . $order->get_id(),
			'meta' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
			'callback' => $callback_url,
		);

		if (!empty($payment_channels)) {
			$hydrogen_params['channels'] = $payment_channels;
		}

		if ($this->split_payment) {
			$hydrogen_params['subaccount'] = $this->subaccount_code;
			$hydrogen_params['bearer'] = $this->charges_account;

			if (empty($this->transaction_charges)) {
				$hydrogen_params['transaction_charge'] = '';
			} else {
				$hydrogen_params['transaction_charge'] = $this->transaction_charges * 100;
			}
		}

		$hydrogen_params['metadata']['custom_fields'] = $this->get_custom_fields($order_id);
		$hydrogen_params['metadata']['cancel_action'] = wc_get_cart_url();

		$order->update_meta_data('_hydrogen_txn_ref', $txnref);
		$order->save();

		$headers = array(
			'Authorization' => $secret_key,
			'Content-Type' => 'application/json',
			'Cache-Control' => 'no-cache',
		);

		$args = array(
			'headers' => $headers,
			'timeout' => 60,
			'body' => json_encode($hydrogen_params1),
		);

		$request = wp_remote_post($hydrogen_url, $args);

		if (is_wp_error($request)) {
			wc_add_notice(__('Unable to redirect now to Hydrogen payment, try again, or use a popup', 'woo-hydrogen'), 'error');
			return;
		}

		$response_code = wp_remote_retrieve_response_code($request);
		$response_body = json_decode(wp_remote_retrieve_body($request));

		if (200 === $response_code) {
			return array(
				'result' => 'success',
				'redirect' => $response_body->data->url,
			);
		} else {
			wc_add_notice(__('Unable to process payment, please try again', 'woo-hydrogen'), 'error');

			// return array(
			//     'result' => 'success',
			//     'redirect' => $response_body,
			// );
			return;
		}
	}

	/**
	 * Process a token payment.
	 *
	 * @param $token
	 * @param $order_id
	 *
	 * @return bool
	 */


	/**
	 * Show new card can only be added when placing an order notice.
	 */
	public function add_payment_method()
	{

		wc_add_notice(__('You can only add a new card when placing an order.', 'woo-hydrogen'), 'error');

		return;
	}

	/**
	 * Displays the payment page.
	 *
	 * @param $order_id
	 */
	public function receipt_page($order_id)
	{

		$order = wc_get_order($order_id);

		echo '<div id="wc-hydrogen-form">';

		echo '<p>' . __('Thank you for your order, please click the button below to pay with Hydrogen Payment Gateway.', 'hydrogen-wc') . '</p>';

		echo '<div id="hydrogen_form"><form id="order_review" method="post" action="' . WC()->api_request_url('WC_Gateway_Hydrogen') . '"></form><button class="button" id="hydrogen-payment-button">' . __('Pay Now', 'woo-hydrogen') . '</button>';

		if (!$this->remove_cancel_order_button) {
			echo '  <a class="button cancel" id="hydrogen-cancel-payment-button" href="' . esc_url($order->get_cancel_order_url()) . '">' . __('Cancel order &amp; restore cart', 'woo-hydrogen') . '</a></div>';
		}

		echo '</div>';
	}

	/**
	 * Verify Hydrogen Woo Payment Transaction.
	 */

	function verify_hydrogen_wc_transaction()
	{
		// Ensure 'transactionRef' is set in the POST data
		if (isset($_POST['transactionRef'])) {

			$hydrogenTransactionRef = $_POST['transactionRef'];

			$hydrogenRansOderId = $_POST['hydrogenOderId'];

			// Get the JSON data from the POST request and decode it
			$hydrogenPostData = json_decode(stripslashes($hydrogenTransactionRef), true);

			// Check if the JSON data can be decoded successfully
			if ($hydrogenPostData === null) {
				wp_send_json(array('result' => 'error', 'message' => 'Invalid JSON data in transactionRef.'));
				return;
			}

			if ($this->testmode) {

				$secret_key = $this->test_secret_key;

				$urlHp = 'https://qa-api.hydrogenpay.com/bepayment/api/v1/Merchant/confirm-payment';

				// Your code for test mode
			} else {

				$secret_key = $this->live_secret_key;

				$urlHp = 'https://api.hydrogenpay.com/bepay/api/v1/Merchant/confirm-payment';

				// Your code for live mode
			}

			// Prepare the request data
			$request_data = array(
				'body'    => json_encode(array('transactionRef' => $hydrogenPostData)),
				'headers' => array(
					'Authorization' => $secret_key,
					'Content-Type'  => 'application/json',
					'Cache-Control' => 'no-cache',
				),
			);

			$response = wp_remote_post($urlHp, $request_data);

			if (is_wp_error($response)) {
				// Handle request error
				wp_send_json(array('result' => 'error', 'message' => $response->get_error_message()));
			} else {
				$response_code = wp_remote_retrieve_response_code($response);

				$response_body = wp_remote_retrieve_body($response);

				if ($response_code == 200) {
					// Request was successful
					$response_data = json_decode($response_body);

					$hydrogen_transaction_status = $response_data->data->status;

					if ($response_data->data->status == "Paid") {

						$order_id = $hydrogenRansOderId;

						$order = wc_get_order($order_id);

						// if (in_array($order->get_status(), array('processing', 'completed', 'on-hold'))) {

						// 	// wp_send_json(array('result' => 'error', 'message' => 'In array will have completed ' . $response_body));

						// 	// wp_redirect($this->get_return_url($order));

						// 	// exit;
						// }

						if (in_array($order->get_status(), array('processing', 'completed', 'on-hold'))) {
							// Handle success case here if needed

							// wp_send_json(array('result' => 'error', 'message' => $order->get_status()));

							WC()->cart->empty_cart();

							http_response_code(200); // Set an HTTP 200 status code for a successful response

							wp_send_json(array('statusCode' => '90000', 'message' => 'Success message, order in array processing, completed or on-hold' . $order->get_status()));

							wp_redirect($this->get_return_url($order));
						}


						$order_total = $order->get_total();
						$order_currency = $order->get_currency();
						$currency_symbol = get_woocommerce_currency_symbol($order_currency);
						$amount_paid = $response_data->data->amount;
						$hydrogen_transaction_ref = $response_data->data->transactionRef;
						$payment_currency = strtoupper($response_data->data->currency);
						$gateway_symbol = get_woocommerce_currency_symbol($payment_currency);

						// Checking with hydrogen payment details

						if ($amount_paid < $order_total) {

							$order->update_status('on-hold', '');

							$order->add_meta_data('_transaction_id', $hydrogen_transaction_ref, true);

							$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment transaction was successful, but the amount paid is not the same as the total order amount.%2$sYour order is currently on hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-hydrogen'), '<br />', '<br />', '<br />');
							$notice_type = 'notice';

							// Add Customer Order Note
							$order->add_order_note($notice, 1);

							// Add Admin Order Note
							$admin_order_note = sprintf(__('<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Amount paid is less than the total order amount.%3$sAmount Paid was <strong>%4$s (%5$s)</strong> while the total order amount is <strong>%6$s (%7$s)</strong>%8$s<strong>Hydrogen Transaction Reference:</strong> %9$s', 'woo-hydrogen'), '<br />', '<br />', '<br />', $currency_symbol, $amount_paid, $currency_symbol, $order_total, '<br />', $hydrogen_transaction_ref);
							$order->add_order_note($admin_order_note);

							function_exists('wc_reduce_stock_levels') ? wc_reduce_stock_levels($order_id) : $order->reduce_order_stock();

							wc_add_notice($notice, $notice_type);

							WC()->cart->empty_cart();

							http_response_code(200); // Set an HTTP 200 status code for a successful response

							wp_send_json(array('statusCode' => '90000', 'message' => 'Success message, amount paid less than order total and order on-hold' . $order->get_status()));
						} else {

							if ($payment_currency !== $order_currency) {

								$order->update_status('on-hold', '');

								$order->update_meta_data('_transaction_id', $hydrogen_transaction_ref);

								$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment was successful, but the payment currency is different from the order currency.%2$sYour order is currently on-hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-hydrogen'), '<br />', '<br />', '<br />');
								$notice_type = 'notice';

								// Add Customer Order Note
								$order->add_order_note($notice, 1);

								// Add Admin Order Note
								$admin_order_note = sprintf(__('<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Order currency is different from the payment currency.%3$sOrder Currency is <strong>%4$s (%5$s)</strong> while the payment currency is <strong>%6$s (%7$s)</strong>%8$s<strong>Hydrogen Transaction Reference:</strong> %9$s', 'woo-hydrogen'), '<br />', '<br />', '<br />', $order_currency, $currency_symbol, $payment_currency, $gateway_symbol, '<br />', $hydrogen_transaction_ref);
								$order->add_order_note($admin_order_note);

								function_exists('wc_reduce_stock_levels') ? wc_reduce_stock_levels($order_id) : $order->reduce_order_stock();

								wc_add_notice($notice, $notice_type);

								WC()->cart->empty_cart();

								http_response_code(200); // Set an HTTP 200 status code for a successful response

								wp_send_json(array('statusCode' => '90000', 'message' => 'Success message, order currency not same and order on-hold' . $order->get_status()));
							} else {

								$order->payment_complete($hydrogen_transaction_ref);

								$order->add_order_note(sprintf(__('Payment via HYDROGEN GATEWAY Successful (Transaction Reference: %s)', 'woo-hydrogen'), $hydrogen_transaction_ref));

								if ($this->is_autocomplete_order_enabled($order)) {

									$order->update_status('completed', '');
								}


								//	
								$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment transaction was successful.', 'woo-hydrogen'), '<br />', '<br />', '<br />');
								$notice_type = 'notice';

								WC()->cart->empty_cart();
								//

								http_response_code(200); // Set an HTTP 200 status code for a successful response

								wp_send_json(array('statusCode' => '90000', 'message' => 'Success message, order already paid for ' . $order->get_status()));

								wc_add_notice($notice, $notice_type);

								// Redirect to cart after successful payment

								wp_redirect(wc_get_page_permalink('cart'));

								exit;
							}
						}

						$order->save();

						$this->save_card_details($response_data, $order->get_user_id(), $order_id);

						WC()->cart->empty_cart();

						http_response_code(200); // Set an HTTP 200 status code for a successful response

						wp_send_json(array('statusCode' => '90000', 'message' => 'Success message, order already paid for ' . $order->get_status()));

						// wp_send_json(array('result' => 'error', 'message' => 'Save successfully and also empty cart ' . $response_body));

						wp_redirect(wc_get_page_permalink('cart'));

						// wp_send_json($order->get_total());

						exit;
					} else {

						$order_id = $hydrogenRansOderId;

						$order = wc_get_order($order_id);

						$order->update_status("failed", __('Payment was declined by Hydrogen.', 'woo-hydrogen'));

						wp_send_json(array('result' => 'error', 'message' => 'Payment was declined by Hydrogen. :' . $hydrogen_transaction_status));
					}
				} else {
					// Handle non-200 HTTP response codes
					wp_send_json(array('result' => 'error', 'message' => 'Request failed with HTTP code ' . $response_body));
				}

				// Redirect to cart page

				http_response_code(200); // Set an HTTP 200 status code for a successful response

				wp_send_json(array('statusCode' => '90000', 'message' => 'Success message' . $response_body));

				// wp_send_json(array('result' => 'error', 'message' => 'Redirect to cat page 2 ' . $response_body));
				wp_redirect(wc_get_page_permalink('cart'));

				exit;
			}
		} else {

			// Handle the case where 'transactionRef' is not provided in the POST data
			wp_send_json(array('result' => 'error', 'message' => 'TransactionRef not found in POST data.'));

			wp_redirect(wc_get_page_permalink('cart'));

			exit;
		}
	}

	function verify_hydrogen_wc_transaction_popup()
	{
		// Ensure 'transactionRef' is set in the POST data
		if (isset($_POST['transactionRef'])) {

			$hydrogenTransactionRef = $_POST['transactionRef'];

			$hydrogenRansOderId = $_POST['hydrogenOderId'];

			// Get the JSON data from the POST request and decode it
			$hydrogenPostData = json_decode(stripslashes($hydrogenTransactionRef), true);

			// Check if the JSON data can be decoded successfully
			if ($hydrogenPostData === null) {
				wp_send_json(array('result' => 'error', 'message' => 'Invalid JSON data in transactionRef.'));
				return;
			}

			if ($this->testmode) {

				$secret_key = $this->test_secret_key;

				$urlHp = 'https://qa-api.hydrogenpay.com/bepayment/api/v1/Merchant/confirm-payment';

				// Your code for test mode
			} else {

				$secret_key = $this->live_secret_key;

				$urlHp = 'https://api.hydrogenpay.com/bepay/api/v1/Merchant/confirm-payment';

				// Your code for live mode
			}

			// Prepare the request data
			$request_data = array(
				'body'    => json_encode(array('transactionRef' => $hydrogenPostData)),
				'headers' => array(
					'Authorization' => $secret_key,
					'Content-Type'  => 'application/json',
					'Cache-Control' => 'no-cache',
				),
			);

			// Send a POST request to the specified endpoint

			$response = wp_remote_post($urlHp, $request_data);

			if (is_wp_error($response)) {
				// Handle request error
				wp_send_json(array('result' => 'error', 'message' => $response->get_error_message()));
			} else {
				$response_code = wp_remote_retrieve_response_code($response);

				$response_body = wp_remote_retrieve_body($response);

				if ($response_code == 200) {
					// Request was successful
					$response_data = json_decode($response_body);

					$hydrogen_transaction_status = $response_data->data->status;

					if ($response_data->data->status == "Paid") {

						$order_id = $hydrogenRansOderId;

						$order = wc_get_order($order_id);

						// if (in_array($order->get_status(), array('processing', 'completed', 'on-hold'))) {

						// 	// wp_send_json(array('result' => 'error', 'message' => 'In array will have completed ' . $response_body));

						// 	// wp_redirect($this->get_return_url($order));

						// 	// exit;
						// }

						if (in_array($order->get_status(), array('processing', 'completed', 'on-hold'))) {
							// Handle success case here if needed

							// wp_send_json(array('result' => 'error', 'message' => $order->get_status()));

							WC()->cart->empty_cart();

							http_response_code(200); // Set an HTTP 200 status code for a successful response

							wp_send_json(array('statusCode' => '90000', 'message' => 'Success message, order in array processing, completed or on-hold' . $order->get_status()));

							wp_redirect($this->get_return_url($order));
						}


						$order_total = $order->get_total();
						$order_currency = $order->get_currency();
						$currency_symbol = get_woocommerce_currency_symbol($order_currency);
						$amount_paid = $response_data->data->amount;
						$hydrogen_transaction_ref = $response_data->data->transactionRef;
						$payment_currency = strtoupper($response_data->data->currency);
						$gateway_symbol = get_woocommerce_currency_symbol($payment_currency);

						// Checking with hydrogen payment details

						if ($amount_paid < $order_total) {

							$order->update_status('on-hold', '');

							$order->add_meta_data('_transaction_id', $hydrogen_transaction_ref, true);

							$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment transaction was successful, but the amount paid is not the same as the total order amount.%2$sYour order is currently on hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-hydrogen'), '<br />', '<br />', '<br />');
							$notice_type = 'notice';

							// Add Customer Order Note
							$order->add_order_note($notice, 1);

							// Add Admin Order Note
							$admin_order_note = sprintf(__('<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Amount paid is less than the total order amount.%3$sAmount Paid was <strong>%4$s (%5$s)</strong> while the total order amount is <strong>%6$s (%7$s)</strong>%8$s<strong>Hydrogen Transaction Reference:</strong> %9$s', 'woo-hydrogen'), '<br />', '<br />', '<br />', $currency_symbol, $amount_paid, $currency_symbol, $order_total, '<br />', $hydrogen_transaction_ref);
							$order->add_order_note($admin_order_note);

							function_exists('wc_reduce_stock_levels') ? wc_reduce_stock_levels($order_id) : $order->reduce_order_stock();

							wc_add_notice($notice, $notice_type);

							WC()->cart->empty_cart();

							http_response_code(200); // Set an HTTP 200 status code for a successful response

							wp_send_json(array('statusCode' => '90000', 'message' => 'Success message, amount paid less than order total and order on-hold' . $order->get_status()));
						} else {

							if ($payment_currency !== $order_currency) {

								$order->update_status('on-hold', '');

								$order->update_meta_data('_transaction_id', $hydrogen_transaction_ref);

								$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment was successful, but the payment currency is different from the order currency.%2$sYour order is currently on-hold.%3$sKindly contact us for more information regarding your order and payment status.', 'woo-hydrogen'), '<br />', '<br />', '<br />');
								$notice_type = 'notice';

								// Add Customer Order Note
								$order->add_order_note($notice, 1);

								// Add Admin Order Note
								$admin_order_note = sprintf(__('<strong>Look into this order</strong>%1$sThis order is currently on hold.%2$sReason: Order currency is different from the payment currency.%3$sOrder Currency is <strong>%4$s (%5$s)</strong> while the payment currency is <strong>%6$s (%7$s)</strong>%8$s<strong>Hydrogen Transaction Reference:</strong> %9$s', 'woo-hydrogen'), '<br />', '<br />', '<br />', $order_currency, $currency_symbol, $payment_currency, $gateway_symbol, '<br />', $hydrogen_transaction_ref);
								$order->add_order_note($admin_order_note);

								function_exists('wc_reduce_stock_levels') ? wc_reduce_stock_levels($order_id) : $order->reduce_order_stock();

								wc_add_notice($notice, $notice_type);

								WC()->cart->empty_cart();

								http_response_code(200); // Set an HTTP 200 status code for a successful response

								wp_send_json(array('statusCode' => '90000', 'message' => 'Success message, order currency not same and order on-hold' . $order->get_status()));
							} else {

								$order->payment_complete($hydrogen_transaction_ref);

								$order->add_order_note(sprintf(__('Payment via HYDROGEN GATEWAY Successful (Transaction Reference: %s)', 'woo-hydrogen'), $hydrogen_transaction_ref));

								if ($this->is_autocomplete_order_enabled($order)) {

									$order->update_status('completed', '');
								}

								//	
								$notice      = sprintf(__('Thank you for shopping with us.%1$sYour payment transaction was successful.', 'woo-hydrogen'), '<br />', '<br />', '<br />');
								$notice_type = 'notice';

								WC()->cart->empty_cart();
								//

								http_response_code(200); // Set an HTTP 200 status code for a successful response

								wp_send_json(array('statusCode' => '90000', 'message' => 'Success message, order already paid for ' . $order->get_status()));

								// wc_add_notice($notice, $notice_type);  // to-doLater

								// Redirect to cart after successful payment

								wp_redirect(wc_get_page_permalink('cart'));

								exit;
							}
						}

						$order->save();

						$this->save_card_details($response_data, $order->get_user_id(), $order_id);

						WC()->cart->empty_cart();

						http_response_code(200); // Set an HTTP 200 status code for a successful response

						wp_send_json(array('statusCode' => '90000', 'message' => 'Success message, order already paid for ' . $order->get_status()));

						// wp_send_json(array('result' => 'error', 'message' => 'Save successfully and also empty cart ' . $response_body));

						wp_redirect(wc_get_page_permalink('cart'));

						// wp_send_json($order->get_total());

						exit;
					} else {

						$order_id = $hydrogenRansOderId;

						$order = wc_get_order($order_id);

						$order->update_status("failed", __('Payment was declined by Hydrogen.', 'woo-hydrogen'));

						wp_send_json(array('result' => 'error', 'message' => 'Payment was declined by Hydrogen. :' . $hydrogen_transaction_status));
					}
				} else {
					// Handle non-200 HTTP response codes
					wp_send_json(array('result' => 'error', 'message' => 'Request failed with HTTP code ' . $response_body));
				}

				// Redirect to cart page

				http_response_code(200); // Set an HTTP 200 status code for a successful response

				wp_send_json(array('statusCode' => '90000', 'message' => 'Success message' . $response_body));

				// wp_send_json(array('result' => 'error', 'message' => 'Redirect to cat page 2 ' . $response_body));
				wp_redirect(wc_get_page_permalink('cart'));

				exit;
			}
		} else {

			// Handle the case where 'transactionRef' is not provided in the POST data
			wp_send_json(array('result' => 'error', 'message' => 'TransactionRef not found in POST data.'));

			wp_redirect(wc_get_page_permalink('cart'));

			exit;
		}
	}


	/**
	 * Save Customer Card Details.
	 *
	 * @param $hydrogen_response
	 * @param $user_id
	 * @param $order_id
	 */
	public function save_card_details($hydrogen_response, $user_id, $order_id)
	{

		$this->save_subscription_payment_token($order_id, $hydrogen_response);

		$order = wc_get_order($order_id);

		$save_card = $order->get_meta('_wc_hydrogen_save_card');

		if ($user_id && $this->saved_cards && $save_card && $hydrogen_response->data->authorization->reusable && 'card' == $hydrogen_response->data->authorization->channel) {

			$gateway_id = $order->get_payment_method();

			$last4          = $hydrogen_response->data->authorization->last4;
			$exp_year       = $hydrogen_response->data->authorization->exp_year;
			$brand          = $hydrogen_response->data->authorization->card_type;
			$exp_month      = $hydrogen_response->data->authorization->exp_month;
			$auth_code      = $hydrogen_response->data->authorization->authorization_code;
			$customer_email = $hydrogen_response->data->customer->email;

			$payment_token = "$auth_code###$customer_email";

			$token = new WC_Payment_Token_CC();
			$token->set_token($payment_token);
			$token->set_gateway_id($gateway_id);
			$token->set_card_type(strtolower($brand));
			$token->set_last4($last4);
			$token->set_expiry_month($exp_month);
			$token->set_expiry_year($exp_year);
			$token->set_user_id($user_id);
			$token->save();

			$order->delete_meta_data('_wc_hydrogen_save_card');
			$order->save();
		}
	}

	/**
	 * Save payment token to the order for automatic renewal for further subscription payment.
	 *
	 * @param $order_id
	 * @param $hydrogen_response
	 */
	public function save_subscription_payment_token($order_id, $hydrogen_response)
	{

		if (!function_exists('wcs_order_contains_subscription')) {
			return;
		}

		if ($this->order_contains_subscription($order_id) && $hydrogen_response->data->authorization->reusable && 'card' == $hydrogen_response->data->authorization->channel) {

			$auth_code      = $hydrogen_response->data->authorization->authorization_code;
			$customer_email = $hydrogen_response->data->customer->email;

			$payment_token = "$auth_code###$customer_email";

			// Also store it on the subscriptions being purchased or paid for in the order
			if (function_exists('wcs_order_contains_subscription') && wcs_order_contains_subscription($order_id)) {

				$subscriptions = wcs_get_subscriptions_for_order($order_id);
			} elseif (function_exists('wcs_order_contains_renewal') && wcs_order_contains_renewal($order_id)) {

				$subscriptions = wcs_get_subscriptions_for_renewal_order($order_id);
			} else {

				$subscriptions = array();
			}

			if (empty($subscriptions)) {
				return;
			}

			foreach ($subscriptions as $subscription) {
				$subscription->update_meta_data('_hydrogen_token', $payment_token);
				$subscription->save();
			}
		}
	}

	/**
	 * Get custom fields to pass to Hydrogen.
	 *
	 * @param int $order_id WC Order ID
	 *
	 * @return array
	 */
	public function get_custom_fields($order_id)
	{

		$order = wc_get_order($order_id);

		$custom_fields = array();

		$custom_fields[] = array(
			'display_name'  => 'Plugin',
			'variable_name' => 'plugin',
			'value'         => 'woo-hydrogen',
		);

		if ($this->custom_metadata) {

			if ($this->meta_order_id) {

				$custom_fields[] = array(
					'display_name'  => 'Order ID',
					'variable_name' => 'order_id',
					'value'         => $order_id,
				);
			}

			if ($this->meta_name) {

				$custom_fields[] = array(
					'display_name'  => 'Customer Name',
					'variable_name' => 'customer_name',
					'value'         => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
				);
			}

			if ($this->meta_email) {

				$custom_fields[] = array(
					'display_name'  => 'Customer Email',
					'variable_name' => 'customer_email',
					'value'         => $order->get_billing_email(),
				);
			}

			if ($this->meta_phone) {

				$custom_fields[] = array(
					'display_name'  => 'Customer Phone',
					'variable_name' => 'customer_phone',
					'value'         => $order->get_billing_phone(),
				);
			}

			if ($this->meta_products) {

				$line_items = $order->get_items();

				$products = '';

				foreach ($line_items as $item_id => $item) {
					$name     = $item['name'];
					$quantity = $item['qty'];
					$products .= $name . ' (Qty: ' . $quantity . ')';
					$products .= ' | ';
				}

				$products = rtrim($products, ' | ');

				$custom_fields[] = array(
					'display_name'  => 'Products',
					'variable_name' => 'products',
					'value'         => $products,
				);
			}

			if ($this->meta_billing_address) {

				$billing_address = $order->get_formatted_billing_address();
				$billing_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $billing_address));

				$hydrogen_params['meta_billing_address'] = $billing_address;

				$custom_fields[] = array(
					'display_name'  => 'Billing Address',
					'variable_name' => 'billing_address',
					'value'         => $billing_address,
				);
			}

			if ($this->meta_shipping_address) {

				$shipping_address = $order->get_formatted_shipping_address();
				$shipping_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $shipping_address));

				if (empty($shipping_address)) {

					$billing_address = $order->get_formatted_billing_address();
					$billing_address = esc_html(preg_replace('#<br\s*/?>#i', ', ', $billing_address));

					$shipping_address = $billing_address;
				}
				$custom_fields[] = array(
					'display_name'  => 'Shipping Address',
					'variable_name' => 'shipping_address',
					'value'         => $shipping_address,
				);
			}
		}

		return $custom_fields;
	}

	/**
	 * Checks if WC version is less than passed in version.
	 *
	 * @param string $version Version to check against.
	 *
	 * @return bool
	 */
	public function is_wc_lt($version)
	{
		return version_compare(WC_VERSION, $version, '<');
	}

	/**
	 * Checks if autocomplete order is enabled for the payment method.
	 *
	 * @since 5.7
	 * @param WC_Order $order Order object.
	 * @return bool
	 */
	protected function is_autocomplete_order_enabled($order)
	{
		$autocomplete_order = false;

		$payment_method = $order->get_payment_method();

		$hydrogen_settings = get_option('woocommerce_' . $payment_method . '_settings');

		if (isset($hydrogen_settings['autocomplete_order']) && 'yes' === $hydrogen_settings['autocomplete_order']) {
			$autocomplete_order = true;
		}

		return $autocomplete_order;
	}

	/**
	 * Retrieve the payment channels configured for the gateway
	 *
	 * @since 5.7
	 * @param WC_Order $order Order object.
	 * @return array
	 */
	protected function get_gateway_payment_channels($order)
	{

		$payment_method = $order->get_payment_method();

		if ('hydrogen' === $payment_method) {
			return array();
		}

		$payment_channels = $this->payment_channels;

		if (empty($payment_channels)) {
			$payment_channels = array('card');
		}

		return $payment_channels;
	}

	/**
	 * Retrieve a transaction from Hydrogen.
	 *
	 * @since 5.7.5
	 * @param $hydrogen_txn_ref
	 * @return false|mixed
	 */


	private function get_hydrogen_transaction($hydrogen_txn_ref)
	{

		$hydrogen_url = 'https://api.hydrogenpay/transaction/verify/' . $hydrogen_txn_ref;

		$headers = array(
			'Authorization' => 'Bearer ' . $this->secret_key,
		);

		$args = array(
			'headers' => $headers,
			'timeout' => 60,
		);

		$request = wp_remote_get($hydrogen_url, $args);

		if (!is_wp_error($request) && 200 === wp_remote_retrieve_response_code($request)) {
			return json_decode(wp_remote_retrieve_body($request));
		}

		return false;
	}

	/**
	 * Get Hydrogen payment icon URL.
	 */
	public function get_logo_url()
	{

		$base_location = wc_get_base_location();

		if ('GH' === $base_location['country']) {
			$url = WC_HTTPS::force_https_url(plugins_url('assets/images/hydrogen-gh.png', WC_HYDROGEN_MAIN_FILE));
		} elseif ('ZA' === $base_location['country']) {
			$url = WC_HTTPS::force_https_url(plugins_url('assets/images/hydrogen-za.png', WC_HYDROGEN_MAIN_FILE));
		} elseif ('KE' === $base_location['country']) {
			$url = WC_HTTPS::force_https_url(plugins_url('assets/images/hydrogen-ke.png', WC_HYDROGEN_MAIN_FILE));
		} else {
			$url = WC_HTTPS::force_https_url(plugins_url('assets/images/hydrogen-wc.png', WC_HYDROGEN_MAIN_FILE));
		}

		return apply_filters('wc_hydrogen_gateway_icon_url', $url, $this->id);
	}

	/**
	 * Check if an order contains a subscription.
	 *
	 * @param int $order_id WC Order ID.
	 *
	 * @return bool
	 */
	public function order_contains_subscription($order_id)
	{

		return function_exists('wcs_order_contains_subscription') && (wcs_order_contains_subscription($order_id) || wcs_order_contains_renewal($order_id));
	}
}
