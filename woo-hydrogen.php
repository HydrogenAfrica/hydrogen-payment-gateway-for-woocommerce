<?php

/**
 * Plugin Name: Hydrogen WooCommerce Payment Gateway
 * Plugin URI: 
 * Description: Hydrogen Woocommerce Payment Gateway helps you process payments using cards and account transfers for faster delivery of goods and services.
 * Version: 1.0.0
 * Author: Hydrogen
 * WC requires at least: 7.0
 * WC tested up to: 8.1
 * Text Domain: hydrogen-woocommerce
 */

use Automattic\WooCommerce\Admin\Notes\Note;
use Automattic\WooCommerce\Admin\Notes\Notes;

if (!defined('ABSPATH')) {
	exit;
}

define('WC_HYDROGEN_MAIN_FILE', __FILE__);
define('WC_HYDROGEN_URL', untrailingslashit(plugins_url('/', __FILE__)));

define('WC_HYDROGEN_VERSION', '1.0.0');

/**
 * Initialize Hydrogen WooCommerce payment gateway.
 */
function tbz_wc_hydrogen_init()
{

	load_plugin_textdomain('woo-hydrogen', false, plugin_basename(dirname(__FILE__)) . '/languages');

	if (!class_exists('WC_Payment_Gateway')) {
		add_action('admin_notices', 'tbz_wc_hydrogen_wc_missing_notice');
		return;
	}

	add_action('admin_init', 'tbz_wc_hydrogen_testmode_notice');

	require_once dirname(__FILE__) . '/includes/class-wc-gateway-hydrogen.php';

	require_once dirname(__FILE__) . '/includes/class-wc-gateway-hydrogen-subscriptions.php';

	require_once dirname(__FILE__) . '/includes/class-wc-gateway-custom-hydrogen.php';

	add_filter('woocommerce_payment_gateways', 'tbz_wc_add_hydrogen_gateway', 99);

	add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'tbz_woo_hydrogen_plugin_action_links');
}
add_action('plugins_loaded', 'tbz_wc_hydrogen_init', 99);

/**
 * Add Settings link to the plugin entry in the plugins menu.
 *
 * @param array $links Plugin action links.
 *
 * @return array
 **/
function tbz_woo_hydrogen_plugin_action_links($links)
{

	$settings_link = array(
		'settings' => '<a href="' . admin_url('admin.php?page=wc-settings&tab=checkout&section=hydrogen') . '" title="' . __('View Hydrogen WooCommerce Settings', 'hydrogen-wc') . '">' . __('Settings', 'hydrogen-wc') . '</a>',
	);

	return array_merge($settings_link, $links);
}

/**
 * Add Hydrogen Gateway to WooCommerce.
 *
 * @param array $methods WooCommerce payment gateways methods.
 *
 * @return array
 */
function tbz_wc_add_hydrogen_gateway($methods)
{

	if (class_exists('WC_Subscriptions_Order') && class_exists('WC_Payment_Gateway_CC')) {
		$methods[] = 'WC_Gateway_Hydrogen_Subscriptions';
	} else {
		$methods[] = 'WC_Gateway_Hydrogen';
	}

	if ('NGN' === get_woocommerce_currency()) {

		$settings        = get_option('woocommerce_hydrogen_settings', '');
		$custom_gateways = isset($settings['custom_gateways']) ? $settings['custom_gateways'] : '';

		switch ($custom_gateways) {
			case '5':
				$methods[] = 'WC_Gateway_Hydrogen_One';
				$methods[] = 'WC_Gateway_Hydrogen_Two';
				$methods[] = 'WC_Gateway_Hydrogenk_Three';
				$methods[] = 'WC_Gateway_Hydrogen_Four';
				$methods[] = 'WC_Gateway_Hydrogen_Five';
				break;

			case '4':
				$methods[] = 'WC_Gateway_Hydrogen_One';
				$methods[] = 'WC_Gateway_Hydrogenk_Two';
				$methods[] = 'WC_Gateway_Hydrogen_Three';
				$methods[] = 'WC_Gateway_Hydrogen_Four';
				break;

			case '3':
				$methods[] = 'WC_Gateway_Hydrogen_One';
				$methods[] = 'WC_Gateway_Hydrogen_Two';
				$methods[] = 'WC_Gateway_Hydrogen_Three';
				break;

			case '2':
				$methods[] = 'WC_Gateway_Hydrogen_One';
				$methods[] = 'WC_Gateway_Hydrogen_Two';
				break;

			case '1':
				$methods[] = 'WC_Gateway_Hydrogen_One';
				break;

			default:
				break;
		}
	}

	return $methods;
}

/**
 * Display a notice if WooCommerce is not installed
 */
function tbz_wc_hydrogen_wc_missing_notice()
{
	echo '<div class="error"><p><strong>' . sprintf(__('Hydrogen payment Gateway wc requires WooCommerce to be installed and active. Click %s to install WooCommerce.', 'woo-hydrogen'), '<a href="' . admin_url('plugin-install.php?tab=plugin-information&plugin=woocommerce&TB_iframe=true&width=772&height=539') . '" class="thickbox open-plugin-details-modal">here</a>') . '</strong></p></div>';
}

/**
 * Display the test mode notice.
 **/
function tbz_wc_hydrogen_testmode_notice()
{

	if (!class_exists(Notes::class)) {
		return;
	}

	if (!class_exists(WC_Data_Store::class)) {
		return;
	}

	if (!method_exists(Notes::class, 'get_note_by_name')) {
		return;
	}

	$test_mode_note = Notes::get_note_by_name('hydrogen-test-mode');

	if (false !== $test_mode_note) {
		return;
	}

	$hydrogen_settings = get_option('woocommerce_hydrogen_settings');
	$test_mode         = $hydrogen_settings['testmode'] ?? '';

	if ('yes' !== $test_mode) {
		Notes::delete_notes_with_name('hydrogen-test-mode');

		return;
	}

	$note = new Note();
	$note->set_title(__('Hydrogen test mode enabled', 'woo-hydrogen'));
	$note->set_content(__('Hydrogen test mode is currently enabled. Remember to disable it when you want to start accepting live payment on your site.', 'woo-hydrogen'));
	$note->set_type(Note::E_WC_ADMIN_NOTE_INFORMATIONAL);
	$note->set_layout('plain');
	$note->set_is_snoozable(false);
	$note->set_name('hydrogen-test-mode');
	$note->set_source('woo-hydrogen');
	$note->add_action('disable-hydrogen-test-mode', __('Disable Hydrogen test mode', 'woo-hydrogen'), admin_url('admin.php?page=wc-settings&tab=checkout&section=hydrogen'));
	$note->save();
}

add_action(
	'before_woocommerce_init',
	function () {
		if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
		}
	}
);

/**
 * Registers WooCommerce Blocks integration.
 */
function tbz_wc_gateway_hydrogen_woocommerce_block_support()
{
	if (class_exists('Automattic\WooCommerce\Blocks\Payments\Integrations\AbstractPaymentMethodType')) {
		require_once __DIR__ . '/includes/class-wc-gateway-hydrogen-blocks-support.php';
		add_action(
			'woocommerce_blocks_payment_method_type_registration',
			static function (Automattic\WooCommerce\Blocks\Payments\PaymentMethodRegistry $payment_method_registry) {
				$payment_method_registry->register(new WC_Gateway_Hydrogen_Blocks_Support());
			}
		);
	}
}
add_action('woocommerce_blocks_loaded', 'tbz_wc_gateway_hydrogen_woocommerce_block_support');
