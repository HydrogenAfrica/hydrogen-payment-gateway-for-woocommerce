jQuery(function ($) {
    let hydrogen_submit = false;
    $("#wc-hydrogen-form").hide();
    wcHydrogenFormHandler();

    $("#hydrogen-payment-button").click(function () {
        wcHydrogenkFormHandler();
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

    function wcHydrogenFormHandler() {
        $("#wc-hydrogen-form").hide();

        let $form = $("form#payment-form, form#order_review");
        let hydrogen_txnref = $form.find("input.hydrogen_txnref");
        let subaccount_code = "";
        let charges_account = "";
        let transaction_charges = "";

        hydrogen_txnref.val("");

        let amount = Number(wc_hydrogen_params.amount);

        let hydrogen_callback = function (transaction) {
            $form.append('<input type="hidden" class="hydrogen_txnref" name="hydrogen_txnref" value="' + transaction.reference + '"/>');
            hydrogen_submit = true;
            $form.submit();

            $("body").block({
                message: null,
                overlayCSS: {
                    background: "#fff",
                    opacity: 0.6
                },
                css: {
                    cursor: "wait"
                }
            });
        };

        // Success payment Modal

        let paymentData = {
            key: wc_hydrogen_params.key,
            email: wc_hydrogen_params.email,
            amount: amount,
            ref: wc_hydrogen_params.txnref,
            currency: wc_hydrogen_params.currency,
            subaccount: subaccount_code,
            bearer: charges_account,
            transaction_charge: transaction_charges,
            metadata: {
                custom_fields: wcHydrogenCustomFields()
            },

            onSuccess: hydrogen_callback,
            onCancel: function () {

                $("#wc-hydrogen-form").show();
                $(this.el).unblock();

            }
        };

        let orderRedirect = wc_hydrogen_params.meta_order_id;

        // Get the current URL and encode it
        // let currentRedirectURL = encodeURIComponent(window.location.href);
        let currentRedirectURL = window.location.href;

        let callback_url = currentRedirectURL;

        var hydrogen_pay = {

            "amount": amount,
            "email": wc_hydrogen_params.email,
            "currency": wc_hydrogen_params.currency,
            "description": "Payment for items ordered with ID " + wc_hydrogen_params.meta_order_id,
            "meta": wc_hydrogen_params.meta_name,
            "callback": callback_url,

        };

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
                //Additional logic specifically for TransactionRef within an iframe

                window.parent.postMessage({ TransactionRef: TransactionRef }, '*');

            } else {
                // Code execution if TransactionRef exists outside the iframe

                //Hydrogen Custom Modal Added for payment

                function showSuccessModal(message, newURL) {
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
                    border-radius: 5px;
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

                // This is a referral window with a TransactionRef

                // console.log('TransactionRef found:', TransactionRef);

                // console.log('Key found:', keyValue);

                var hydrogenOderId = wc_hydrogen_params.meta_order_id;

                // console.log('TransactionRef found:', TransactionRef, hydrogenOderId);

                // sendHydrogenPaymentConfirmationRequest(TransactionRef, hydrogenOderId);

                var redirectionUrl = wc_hydrogen_params.hydrogen_wc_redirect_url;

                // Remove "checkout/order-pay" from the URL
                var newURL = window.location.href.replace(/\/checkout\/order-pay\/\d+\/.*/, '');

                // Concatenate "my-account/orders/" to the URL
                newURL += '/cart/';

                // sendHydrogenPaymentConfirmationRequest(TransactionRef, hydrogenOderId);

                $.ajax({

                    url: '/wc-api/wc_gateway_hydrogen', // calling the payment confirmation endpoint function

                    method: 'POST',

                    data: {
                        transactionRef: JSON.stringify(TransactionRef),

                        hydrogenOderId: hydrogenOderId

                    }, // Set the data property as an object

                    dataType: 'json',

                    beforeSend: function () {

                        // console.log('Curldata before send:', TransactionRef); //for deburging
                        // console.log('Curldata before send Oder Id:', hydrogenOderId); //for deburging
                        // console.log('Curldata before send Redirection Link:', redirectionUrl); //for deburging
                        // console.log('Curldata before send Redirection Link:', newURL); //for deburging
                    },

                    // success: handleHydrogenPaymentConfirmation,

                    success: function (response) {

                        // console.log(response);

                        if (response.statusCode == "90000") {

                            // console.log('Success Response Data: ', response);

                            // Hydrogen Modal For Successful Payment

                            linkModal.style.display = 'none';

                            var alertMessage = 'Your payment for order #' + hydrogenOderId + ' is successful and confirmed! Check your email or account for order details.';

                            showSuccessModal(alertMessage, newURL);

                        } else {

                            // console.log('Error Message: ', response.message);

                            // console.log('Error Message: ', response);


                            // Hydrogen Modal For Declined Payment
                            linkModal.style.display = 'none';

                            var alertMessage = 'Your payment for order #' + hydrogenOderId + ' was declined with status: Failed ! Click Ok.';

                            // showSuccessModal(alertMessage);

                            // window.location.href = newURL;

                            showSuccessModal(alertMessage, newURL);

                        }

                    },

                    error: function (xhr, status, error) {

                        console.log('Error:', status, error);

                        // Hide the loading spinner for hydrogen conformation
                        $('#loading-spinner').hide();

                    }

                });

            }


        } else {

            $.ajax({
                url: '/wc-api/hydrogen_wc_payment', // URL endpoint

                method: 'POST',
                // hydrogen_pay: JSON.stringify(hydrogen_pay),

                data: { hydrogen_pay: JSON.stringify(hydrogen_pay) }, // Set the data property as an object
                // data: hydrogen_pay,
                // contentType: 'application/json',
                dataType: 'json',

                success: function (response) {

                    // console.log(response);

                    if (response.statusCode == "90000") {

                        // console.log('Success Response Data: ', response);

                        transactionRef = response.data.transactionRef; // Transaction Ref for payment confirmation on sucess

                        openLinkModal(response.data.url);

                        // console.log('Success Response Data 1: ', response.data.url);
                        // console.log('Success Response Data Ref: ', response.data.transactionRef);

                    } else {

                        console.log('Error Message: ', response.message);

                        console.log('Error Message: ', response);


                        // Display a user-friendly error message
                        // alert('Network error. Please refresh the page and try again later.');

                        // Display a confirm dialog with a custom message
                        var userConfirmation = confirm('Network error. Would you like to refresh the page ?');

                        if (userConfirmation) {

                            // If the user clicks "OK," refresh the page
                            location.reload();
                        }

                    }
                },
                error: function (xhr, status, error) {

                    console.log('Error:', status, error);

                    // Display a user-friendly error message

                    alert('Network error. Please refresh the page and try again later.');

                }
            });


        }// end of referal check


        // Define the CSS style
        var customCSS = `
          <style>
              .modal-overlay {
                  position: fixed;
                  top: 0;
                  left: 0;
                  width: 100%;
                  height: 100%;
                  background-color: rgba(0, 0, 0, 0.5);
                  z-index: 1000;
              }
          
              .modal-container {
                  position: fixed;
                  top: 50%;
                  left: 50%;
                  transform: translate(-50%, -50%);
                  max-width: 1000px!important;
                  z-index: 1001;
              }
          
              .custom-modal-body {
                  padding: 0px;
              }
          
              .modal-logo {
                  width: auto;
                  height: 150px;
                  margin-right: 10px;
              }

              .close {
                position: absolute;
                right: -1px;
                top: 29px;
                padding: 5px 7px!important;
              }

              #loading-spinner {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.8);
                display: none;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }
            
            .spinner {
                border: 4px solid rgba(255, 255, 255, 0.3);
                border-top: 4px solid #0073aa; /* Change the color of the spinner */
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 2s linear infinite;
            }
            
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
            
          </style>
          `;

        // Append the custom CSS to the head of the document
        $('head').append(customCSS);

        // Define the HTML for the modal overlay
        var overlayHTML = `
          <div class="modal-overlay"></div>
          `;

        // Append the modal overlay to the body
        $('body').append(overlayHTML);

        // Define the HTML for the modal
        var modalHTML = `
          <div class="modal-container">
              <div class="modal fade" id="linkModal" tabindex="-1" aria-labelledby="linkModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                      <div class="modal-content max-w-650px">
                          <div class="modal-header">
                              <img src="https://qa-gateway.hydrogenpay.com/_next/static/media/Logo.9a9207a9.svg" alt="Logo" class="modal-logo">
                                    
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                              </button>
                          </div>
                          <div class="modal-body custom-modal-body">
                              <iframe id="linkIframe" style="width: 150%; height: 400px; border: none;"></iframe>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          
          `;

        // Append the modal HTML to the body
        $('body').append(modalHTML);

        // Get a reference to the close button in the modal
        var closeButton = document.querySelector('.modal .close');

        // Get a reference to the "Close" button in the footer
        //   var closeFooterButton = document.querySelector('.modal-footer button');

        // Get a reference to the overlay element
        var overlay = document.querySelector('.modal-overlay');

        // Function to hide the modal and overlay
        function hideModal() {

            linkModal.style.display = 'none';

            overlay.style.display = 'none';

            //   location.reload(); // Refresh the page when the modal is closed

            $("#wc-hydrogen-form").show();

            $(this.el).unblock();

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

                    // Concatenate "my-account/orders/" to the URL
                    newURL += '/cart/';

                    // console.log(response);

                    if (response.statusCode == "90000") {

                        // console.log('Success Response Data: ', response);

                        // Hydrogen Modal For Successful Payment

                        linkModal.style.display = 'none';

                        var alertMessage = 'Your payment for order #' + hydrogenOderId + ' is successful and confirmed! Check your email or account for order details.';

                        hydrogenShowSuccessModal(alertMessage, newURL);

                    } else {

                        // console.log('Error Message: ', response.message);

                        // console.log('Error Message: ', response);


                        // Hydrogen Modal For Declined Payment
                        linkModal.style.display = 'none';

                        var alertMessage = 'Your payment for order #' + hydrogenOderId + ' was declined with status: Failed ! Click Ok.';

                        // showSuccessModal(alertMessage);

                        // window.location.href = newURL;

                        hydrogenShowSuccessModal(alertMessage, newURL);

                    }

                },

                error: function (xhr, status, error) {

                    console.log('Error:', status, error);

                    // Hide the loading spinner for hydrogen conformation
                    // $('#loading-spinner').hide();

                },

            });

        }

        // Function to show the modal and overlay
        function showModal() {

            linkModal.style.display = 'block';

            overlay.style.display = 'block';

        }

        // Define a flag to track whether a modal is currently open
        var isModalOpen = false;

        //********* Function to open the modal and load the link *********

        function openLinkModal(linkUrl) {
            // Check if a modal is already open
            if (isModalOpen) {
                // Close the current modal
                hideModal();
            }

            // Set the flag to indicate that a modal is open
            isModalOpen = true;

            linkIframe.src = linkUrl;
            showModal(); // Show the modal and overlay
        }

        //******** Add a click event listener to the close button to hide the modal *********
        closeButton.addEventListener('click', hideModal);

        // Add a click event listener to the "Close" button in the footer to hide the modal
        //   closeFooterButton.addEventListener('click', hideModal);

        // Add a click event listener to the overlay to hide the modal
        overlay.addEventListener('click', hideModal);

        // Get a reference to the button that triggers opening the modal
        // var openLinkButton = document.getElementById('openLinkButton');

        // Get a reference to the modal and iframe
        var linkModal = document.getElementById('linkModal');

        var linkIframe = document.getElementById('linkIframe');

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
            border-radius: 5px;
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

});
