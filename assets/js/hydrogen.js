jQuery(function ($) {

    let hydrogen_submit = false;

    $("#wc-hydrogen-form").hide();
    wcHydrogenFormHandler();

    $("#hydrogen-payment-button").click(function () {
        wcHydrogenFormHandler();
        // location.reload(); // Refresh the page to reopen the modal
    });

    $("#hydrogen_form form#order_review").submit(function () {
        wcHydrogenFormHandler();
    });

    function wcHydrogenCustomFields() {
        let custom_fields = [
            {
                display_name: "Plugin",
                variable_name: "plugin",
                value: "woo-hydrogen"
            }
        ];

        if (wc_hydrogen_params.meta_order_id) {
            custom_fields.push({
                display_name: "Order ID",
                variable_name: "order_id",
                value: wc_hydrogen_params.meta_order_id
            });
        }

        if (wc_hydrogen_params.meta_name) {
            custom_fields.push({
                display_name: "Customer Name",
                variable_name: "customer_name",
                value: wc_hydrogen_params.meta_name
            });
        }

        if (wc_hydrogen_params.meta_email) {
            custom_fields.push({
                display_name: "Customer Email",
                variable_name: "customer_email",
                value: wc_hydrogen_params.meta_email
            });
        }

        if (wc_hydrogen_params.meta_phone) {
            custom_fields.push({
                display_name: "Customer Phone",
                variable_name: "customer_phone",
                value: wc_hydrogen_params.meta_phone
            });
        }

        if (wc_hydrogen_params.meta_billing_address) {
            custom_fields.push({
                display_name: "Billing Address",
                variable_name: "billing_address",
                value: wc_hydrogen_params.meta_billing_address
            });
        }

        if (wc_hydrogen_params.meta_shipping_address) {
            custom_fields.push({
                display_name: "Shipping Address",
                variable_name: "shipping_address",
                value: wc_hydrogen_params.meta_shipping_address
            });
        }

        if (wc_hydrogen_params.meta_products) {
            custom_fields.push({
                display_name: "Products",
                variable_name: "products",
                value: wc_hydrogen_params.meta_products
            });
        }

        return custom_fields;
    }

    function wcHydrogenCustomFilters() {
        let custom_filters = {};

        if (wc_hydrogen_params.card_channel) {
            if (wc_hydrogen_params.banks_allowed) {
                custom_filters["banks"] = wc_hydrogen_params.banks_allowed;
            }

            if (wc_hydrogen_params.cards_allowed) {
                custom_filters["card_brands"] = wc_hydrogen_params.cards_allowed;
            }
        }

        return custom_filters;
    }

    function showSpinner() {
        // Create the spinner HTML
        let spinnerHTML = `
            <style>
                /* Style for the spinner overlay */
                .spinner-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8); /* Semi-transparent white background */
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999; /* Ensure it's above other elements */
                }
    
                /* Style for the spinner */
                .spinner {
                    border: 4px solid rgba(0, 0, 0, 0.1);
                    border-top: 4px solid #3498db; /* Blue color for spinner */
                    border-radius: 50%;
                    width: 40px;
                    height: 40px;
                    animation: spin 1s linear infinite; /* Animation for rotation */
                }
    
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
            <div id="loading-spinner" class="spinner-overlay">
                <div class="spinner"></div>
            </div>
        `;

        // Append the spinner to the body
        $('body').append(spinnerHTML);
    }

    function hideSpinner() {
        // Hide the spinner by removing it from the DOM
        $('#loading-spinner').remove();
    }

    function hydrogenShowSuccessModal(message, newURL) {
        // Create the modal HTML with CSS styling

        let hydrogenModalSuccessHTML = `
        <style>
        /* Style the modal overlay to cover the entire screen */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Style the modal container */
        .hydrogen-modal-container {
            background: #fff; /* White background color */
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); /* Drop shadow */
        position: fixed;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          max-width: 1000px!important;
          z-index: 1001;
        }

        /* Style the modal content */
        .modal-content {
            padding: 20px;
        }
        </style>
        <div class="modal-overlay">
        <div class="hydrogen-modal-container">
            <div class="modal fade" id="customModalSuccess" tabindex="-1" aria-labelledby="customModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="hydrogen-modal-body">
                            <p>${message}</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-dismiss="modal" id="okButton">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        `;

        // Append the modal HTML to the body
        $('body').append(hydrogenModalSuccessHTML);

        // Add an event listener for the "OK" button
        $('#okButton').on('click', function () {
            // Redirect to the new URL when the "OK" button is clicked
            window.location.href = newURL;
        });
    }


    function wcPaymentChannels() {
        let payment_channels = [];

        if (wc_hydrogen_params.bank_channel) {
            payment_channels.push("bank");
        }

        if (wc_hydrogen_params.card_channel) {
            payment_channels.push("card");
        }

        if (wc_hydrogen_params.ussd_channel) {
            payment_channels.push("ussd");
        }

        if (wc_hydrogen_params.qr_channel) {
            payment_channels.push("qr");
        }

        if (wc_hydrogen_params.bank_transfer_channel) {
            payment_channels.push("bank_transfer");
        }

        return payment_channels;
    }

    async function wcHydrogenFormHandler() {

        $("#wc-hydrogen-form").hide();

        let $form = $("form#payment-form, form#order_review");
        let hydrogen_txnref = $form.find("input.hydrogen_txnref");
        let subaccount_code = "";
        let charges_account = "";
        let transaction_charges = "";

        hydrogen_txnref.val("");

        let amount = Number(wc_hydrogen_params.amount);

        // Success payment Modal

        let orderRedirect = wc_hydrogen_params.meta_order_id;

        let currentRedirectURL = window.location.href;

        let callback_url = currentRedirectURL;

        window.obj = {
            amount: amount,
            email: wc_hydrogen_params.email,
            currency: wc_hydrogen_params.currency,
            description: "Payment for items ordered with ID " + wc_hydrogen_params.meta_order_id,
            meta: wc_hydrogen_params.meta_name,
            callback: callback_url,
            isAPI: false
        };

        window.token = wc_hydrogen_params.key;

        function handlePaymentCallback() {
            // Trigger the close button's click for the iframe
            closeButton.click();

            // Redirect to the cart page
            window.location.href = '/cart'; // Adjust the URL as needed
        }

        if (Array.isArray(wcPaymentChannels()) && wcPaymentChannels().length) {
            paymentData["channels"] = wcPaymentChannels();

            if (!$.isEmptyObject(wcHydrogenCustomFilters())) {
                paymentData["metadata"]["custom_filters"] = wcHydrogenCustomFilters();
            }
        }

        function adjustModalHeight() {
            const modalContent = document.getElementById('modal');
            // Remove the 'height' style property from the div by setting it to auto
            modalContent.style.height = 'auto';
        }

        // function adjustModalHeightForMobile() {
        //     const modalContent = document.getElementById('modal');
        //     // Add specific styling for mobile view if needed
        //     modalContent.style.height = 'auto';
        // }

        var urlParams = new URLSearchParams(window.location.search);

        // Get the value of the "TransactionRef" parameter
        var TransactionRef = urlParams.get('TransactionRef');

        // Get the value of the "TransactionRef" parameter
        var orderKey = urlParams.get('key');

        // Get the current URL
        var currentURL = window.location.href;

        // Find the index of the "key=" parameter in the URL
        var startIndex = currentURL.indexOf('key=');

        if (startIndex !== -1) {
            // Extract the portion of the URL starting from "key=" to the end
            var keyParam = currentURL.substring(startIndex);

            // Check if there's a second "?" in the keyParam (indicating additional parameters)
            var secondQuestionMarkIndex = keyParam.indexOf('?');

            if (secondQuestionMarkIndex !== -1) {
                // If there's a second "?", remove everything from that point
                keyParam = keyParam.substring(0, secondQuestionMarkIndex);
            }

            // Extract the value of the "key" parameter
            var keyValue = keyParam.split('=')[1];

        }

        if (!TransactionRef) {

            // If "TransactionRef" is not found, check for a second "?" in the URL
            var fullUrl = window.location.href;
            var index = fullUrl.lastIndexOf('?');

            if (index !== -1) {
                // Extract everything after the last "?" character
                var paramsAfterLastQuestionMark = fullUrl.substring(index + 1);

                // Split the remaining string by "&" to get individual parameters
                var remainingParams = paramsAfterLastQuestionMark.split('&');

                // Look for "TransactionRef" in the remaining parameters
                for (var i = 0; i < remainingParams.length; i++) {
                    var param = remainingParams[i].split('=');
                    if (param[0] === 'TransactionRef') {
                        TransactionRef = param[1];
                        break;
                    }
                }
            }
        }

        if (TransactionRef) {

            if (window !== window.parent) {
                //Additional logic specifically for TransactionRef within an iframe inlineJs

                window.parent.postMessage({ TransactionRef: TransactionRef }, '*');

            } else {
                // Code execution if TransactionRef exists outside the iframe

                var hydrogenOderId = wc_hydrogen_params.meta_order_id;

                // console.log('TransactionRef found:', TransactionRef, hydrogenOderId);

                // sendHydrogenPaymentConfirmationRequest(TransactionRef, hydrogenOderId);

                var redirectionUrl = wc_hydrogen_params.hydrogen_wc_redirect_url;

                // Remove "checkout/order-pay" from the URL
                var newURL = window.location.href.replace(/\/checkout\/order-pay\/\d+\/.*/, '');

                // Concatenate "my-account/orders/" to the URL
                newURL += '/cart/';

                // sendHydrogenPaymentConfirmationRequest(TransactionRef, hydrogenOderId);

                // hideModal();

                transactionRef = TransactionRef

                hideModal()
            }

        } else {

            // Refactor hydrogenPopup to not take obj and token as parameters
            async function hydrogenPopup() {
                try {

                    // Access obj directly from the global scope (window)
                    let hydrogen = await handlePgData(window.obj, window.token); // Use obj and token directly from the outer scope

                    console.log("return transaction ref", hydrogen);

                    transactionRef = hydrogen;

                    // Remove the 'height' style property from the div
                    // adjustModalHeight();

                    if (window.innerWidth > 768) {
                        // Remove the 'height' style property from the div only for larger screens
                        adjustModalHeight();

                    }

                    // Handle success or further processing here
                } catch (error) {
                    console.error("Error occurred:", error);
                    // Handle errors or failure cases here
                }
            }

            hydrogenPopup();

        }

        // Get a reference to the close button in the modal
        var closeButton = document.querySelector('.modal .close');

        // Get a reference to the "Close" button in the footer
        //   var closeFooterButton = document.querySelector('.modal-footer button');

        // Function to hide the modal and overlay
        function hideModal() {

            showSpinner();

            $("#wc-hydrogen-form").show();

            // Send ajax request for hydrogen payment confirmation endpoint

            var hydrogenOderId = wc_hydrogen_params.meta_order_id;

            // sendHydrogenPaymentConfirmationRequest(transactionRef, hydrogenOderId); Commented out for test

            $.ajax({

                url: '/wc-api/wc_gateway_hydrogen_popup', // calling the payment confirmation endpoint function

                method: 'POST',

                data: {
                    transactionRef: JSON.stringify(transactionRef),

                    hydrogenOderId: hydrogenOderId

                }, // Set the data property as an object

                dataType: 'json',

                beforeSend: function () {

                    // showSpinner();

                    // console.log('Curldata before send:', currentURL); //for deburging
                    // console.log('Curldata before send Oder Id:', hydrogenOderId); //for deburging

                },

                // success: handleHydrogenPaymentConfirmation,

                success: function (response) {

                    var hydrogenOderId = wc_hydrogen_params.meta_order_id;

                    // Remove "checkout/order-pay" from the URL
                    var newURL = window.location.href.replace(/\/checkout\/order-pay\/\d+\/.*/, '');

                    newURL += '/cart/';

                    // console.log(response);

                    if (response.statusCode == "90000") {

                        // console.log('Success Response Data: ', response);

                        // Hydrogen Modal For Successful Payment

                        var alertMessage = 'Your payment for order #' + hydrogenOderId + ' is successful and confirmed! Check your email or account for order details.';

                        hydrogenShowSuccessModal(alertMessage, newURL);

                    } else {

                        var alertMessage = 'Your payment for order #' + hydrogenOderId + ' was declined with status: Failed ! Click Ok.';

                        hydrogenShowSuccessModal(alertMessage, newURL);

                    }

                },

                error: function (xhr, status, error) {

                    console.log('Error:', status, error);

                    // Hide the loading spinner for hydrogen conformation
                    // $('#loading-spinner').hide();

                },

                complete: function () {
                    // Hide spinner when the request is complete
                    hideSpinner();
                }

            });

        }

        //******** Add a click event listener to the close button to hide the modal *********
        closeButton.addEventListener('click', hideModal);

        //Get a message and Close Iframe after successful payment status
        window.addEventListener('message', function (event) {
            // Check the origin of the message if required

            // Check if the message contains TransactionRef
            if (event.data && event.data.TransactionRef) {

                // hideModal();
                closeButton.click();

            }
        });

    }


});
