== Hydrogen Payment Gateway for WooCommerce ==
Contributors: Hydrogenpay
Donate link: https://hydrogenpay.com
Tags: hydrogen, hydrogenpay, woocommerce, payment gateway, payments
Requires at least: 3.0.0
Tested up to: 8.2.5
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Short Description ==
Fast, secure payments through card transactions and account transfers for WooCommerce.

== Description ==
Hydrogen Payment Gateway enables fast, secure payments through card transactions and account transfers, optimizing the delivery of goods and services.

== Introduction ==
This document outlines the scope of work for developing plugins that integrate the Hydrogen Payment Gateway API into a merchant’s website or product.

The project involves creating WooCommerce plugins to enable seamless integration of Hydrogen’s Payment Gateway API, ensuring smooth payment processing within the merchant’s platform.

Hydrogen Payment Gateway enables fast, secure payments through card transactions and account transfers, optimizing the delivery of goods and services.

== Getting Started ==
1.	Installation process

    1.  Download the plugin zip file using the link https://github.com/HydrogenAfrica/woocommerce-hydrogen-payment-gateway-plugin/archive/refs/heads/main.zip
    2.  Login to your WordPress Admin. Click on "Plugins > Add New" from the left hand menu.
    3.  Click on the "Upload" option, then click "Choose File" to select the zip file from your computer. Once selected, press "OK" and press the "Install Now" button.
    4.  Activate the plugin.
    5.  Open the settings page for WooCommerce and click the "Payment" tab.
    6.  Configure your Hydrogen Payment Gateway settings. See below for details.

        Configure the plugin:

        To configure the plugin, go to WooCommerce > Settings from the left hand menu, then click Checkout from the top tab. You will see Hydrogen Payment Gateway as part of the available Checkout Options. Click on it to Manage to configure the Hudrogen payment gateway.

        * Enable/Disable - check the box to enable Hydrogen Payment Gateway.
        * Title - allows you to determine what your customers will see this payment option as on the checkout page.
        * Description - controls the message that appears under the payment fields on the checkout page. Here you can list the types of cards you accept.
        * Test Mode - Check to enable test mode. Test mode enables you to test payments before going live. If you ready to start receving real payment on your site, kindly uncheck this.
        * Sandbox - Enter your sandbox API Keys here. Get your API Keys from your Hydrogen account under Settings > [Developer/API]([url](https://dashboard.hydrogenpay.com/merchant/profile/api-integration))
        * Live API Keys - Enter your Live API Keys here. Get your Authorization token from your Hydrogen account under Settings > [Developer/API]([url](https://dashboard.hydrogenpay.com/merchant/profile/api-integration))
        * Payment Option - Popup shows the Hydrogen payment popup on the page while Redirect will redirect the customer to Hydrogen Payment Site to make payment.
        * Click on Save Changes for the changes you made to be effected.

2.	Software dependencies

    1. You need to have WooCommerce plugin installed and activated on your WordPress site.
    2. You need to open a Hydrogen merchant account on Hydrogen
    3. works with WooCommerce v2.6 and above

3.	Latest releases

    1. 1.0.0 - September 26, 2024.


4.	API references

    * https://dashboard.hydrogenpay.com/

    * https://docs.hydrogenpay.com/docs/authentication


== Contribute ==

Explain how other users and developers can contribute to make your code better. 

If you discover a bug or have a solution to improve the Hydrogen Payment Gateway for the WooCommerce plugin,
we welcome your contributions to enhance the code.

 * Visit our GitHub repository: [https://dev.azure.com/HydrogenPay/Payment%20Gateway/_git/be_pg_wc_plugin]

 * Create a detailed bug report or feature request in the "Issues" section.

 * If you have a code improvement or bug fix, feel free to submit a pull request.

        * Fork the repository on GitHub

        * Clone the repository into your local system and create a branch that describes what you are working on by pre-fixing with feature-name.

        * Make the changes to your forked repository's branch. Ensure you are using PHP Coding Standards (PHPCS).

        * Make commits that are descriptive and breaks down the process for better understanding.

        * Push your fix to the remote version of your branch and create a PR that aims to merge that branch into master.
        
        * After you follow the step above, the next stage will be waiting on us to merge your Pull Request.

 Your contributions help us make the PG plugin even better for the community. Thank you!

== Screenshots ==

1. Hydrogen Payment Gateway for WooCommerce Setting Page

2. Hydrogen Payment Gateway for WooCommer checkout page

3. Hydrogen Popup/inline payment page

