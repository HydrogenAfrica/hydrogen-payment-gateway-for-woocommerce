jQuery( function( $ ) {
	'use strict';

	/**
	 * Object to handle hydrogen admin functions.
	 */
	var wc_hydrogen_admin = {
		/**
		 * Initialize.
		 */
		init: function() {

			// Toggle api key settings.
			$( document.body ).on( 'change', '#woocommerce_hydrogen_testmode', function() {
				var test_secret_key = $( '#woocommerce_hydrogen_test_secret_key' ).parents( 'tr' ).eq( 0 ),
					// test_public_key = $( '#woocommerce_hydrogen_test_public_key' ).parents( 'tr' ).eq( 0 ),
					live_secret_key = $( '#woocommerce_hydrogen_live_secret_key' ).parents( 'tr' ).eq( 0 );
					// live_public_key = $( '#woocommerce_hydrogen_live_public_key' ).parents( 'tr' ).eq( 0 );

				if ( $( this ).is( ':checked' ) ) {
					test_secret_key.show();
					// test_public_key.show();
					live_secret_key.hide();
					// live_public_key.hide();
				} else {
					test_secret_key.hide();
					// test_public_key.hide();
					live_secret_key.show();
					// live_public_key.show();
				}
			} );

			$( '#woocommerce_hydrogen_testmode' ).change();

			$( document.body ).on( 'change', '.woocommerce_hydrogen_split_payment', function() {
				var subaccount_code = $( '.woocommerce_hydrogen_subaccount_code' ).parents( 'tr' ).eq( 0 ),
					subaccount_charge = $( '.woocommerce_hydrogen_split_payment_charge_account' ).parents( 'tr' ).eq( 0 ),
					transaction_charge = $( '.woocommerce_hydrogen_split_payment_transaction_charge' ).parents( 'tr' ).eq( 0 );

				if ( $( this ).is( ':checked' ) ) {
					subaccount_code.show();
					subaccount_charge.show();
					transaction_charge.show();
				} else {
					subaccount_code.hide();
					subaccount_charge.hide();
					transaction_charge.hide();
				}
			} );

			$( '#woocommerce_hydrogen_split_payment' ).change();

			// Toggle Custom Metadata settings.
			$( '.wc-hydrogen-metadata' ).change( function() {
				if ( $( this ).is( ':checked' ) ) {
					$( '.wc-hydrogen-meta-order-id, .wc-hydrogen-meta-name, .wc-hydrogen-meta-email, .wc-hydrogen-meta-phone, .wc-hydrogen-meta-billing-address, .wc-hydrogen-meta-shipping-address, .wc-hydrogen-meta-products' ).closest( 'tr' ).show();
				} else {
					$( '.wc-hydrogen-meta-order-id, .wc-hydrogen-meta-name, .wc-hydrogen-meta-email, .wc-hydrogen-meta-phone, .wc-hydrogen-meta-billing-address, .wc-hydrogen-meta-shipping-address, .wc-hydrogen-meta-products' ).closest( 'tr' ).hide();
				}
			} ).change();

			// Toggle Bank filters settings.
			$( '.wc-hydrogen-payment-channels' ).on( 'change', function() {

				var channels = $( ".wc-hydrogen-payment-channels" ).val();

				if ( $.inArray( 'card', channels ) != '-1' ) {
					$( '.wc-hydrogen-cards-allowed' ).closest( 'tr' ).show();
					$( '.wc-hydrogen-banks-allowed' ).closest( 'tr' ).show();
				}
				else {
					$( '.wc-hydrogen-cards-allowed' ).closest( 'tr' ).hide();
					$( '.wc-hydrogen-banks-allowed' ).closest( 'tr' ).hide();
				}

			} ).change();

			$( ".wc-hydrogen-payment-icons" ).select2( {
				templateResult: formatHydrogenPaymentIcons,
				templateSelection: formatHydrogenPaymentIconDisplay
			} );

			$( '#woocommerce_hydrogen_test_secret_key, #woocommerce_hydrogen_live_secret_key' ).after(
				'<button class="wc-hydrogen-toggle-secret" style="height: 30px; margin-left: 2px; cursor: pointer"><span class="dashicons dashicons-visibility"></span></button>'
			);

			$( '.wc-hydrogen-toggle-secret' ).on( 'click', function( event ) {
				event.preventDefault();

				let $dashicon = $( this ).closest( 'button' ).find( '.dashicons' );
				let $input = $( this ).closest( 'tr' ).find( '.input-text' );
				let inputType = $input.attr( 'type' );

				if ( 'text' == inputType ) {
					$input.attr( 'type', 'password' );
					$dashicon.removeClass( 'dashicons-hidden' );
					$dashicon.addClass( 'dashicons-visibility' );
				} else {
					$input.attr( 'type', 'text' );
					$dashicon.removeClass( 'dashicons-visibility' );
					$dashicon.addClass( 'dashicons-hidden' );
				}
			} );
		}
	};

	function formatHydrogenPaymentIcons( payment_method ) {
		if ( !payment_method.id ) {
			return payment_method.text;
		}

		var $payment_method = $(
			'<span><img src=" ' + wc_hydrogen_admin_params.plugin_url + '/assets/images/' + payment_method.element.value.toLowerCase() + '.png" class="img-flag" style="height: 15px; weight:18px;" /> ' + payment_method.text + '</span>'
		);

		return $payment_method;
	};

	function formatHydrogenPaymentIconDisplay( payment_method ) {
		return payment_method.text;
	};

	wc_hydrogen_admin.init();

} );
