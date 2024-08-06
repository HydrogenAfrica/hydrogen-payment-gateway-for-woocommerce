jQuery(function ($) {
  // Function to generate metadata for the payment request
  function generateMetadata() {
    let metadata = [
      {
        display_name: "Plugin",
        variable_name: "plugin",
        value: "woo-hydrogen",
      },
    ];

    // Add Order ID to metadata if available
    if (wc_hydrogen_params.meta_order_id) {
      metadata.push({
        display_name: "Order ID",
        variable_name: "order_id",
        value: wc_hydrogen_params.meta_order_id,
      });
    }

    // Add Customer Name to metadata if available
    if (wc_hydrogen_params.meta_name) {
      metadata.push({
        display_name: "Customer Name",
        variable_name: "customer_name",
        value: wc_hydrogen_params.meta_name,
      });
    }

    // Add Customer Email to metadata if available
    if (wc_hydrogen_params.meta_email) {
      metadata.push({
        display_name: "Customer Email",
        variable_name: "customer_email",
        value: wc_hydrogen_params.meta_email,
      });
    }

    // Add Customer Phone to metadata if available
    if (wc_hydrogen_params.meta_phone) {
      metadata.push({
        display_name: "Customer Phone",
        variable_name: "customer_phone",
        value: wc_hydrogen_params.meta_phone,
      });
    }

    // Add Billing Address to metadata if available
    if (wc_hydrogen_params.meta_billing_address) {
      metadata.push({
        display_name: "Billing Address",
        variable_name: "billing_address",
        value: wc_hydrogen_params.meta_billing_address,
      });
    }

    // Add Shipping Address to metadata if available
    if (wc_hydrogen_params.meta_shipping_address) {
      metadata.push({
        display_name: "Shipping Address",
        variable_name: "shipping_address",
        value: wc_hydrogen_params.meta_shipping_address,
      });
    }

    // Add Products to metadata if available
    if (wc_hydrogen_params.meta_products) {
      metadata.push({
        display_name: "Products",
        variable_name: "products",
        value: wc_hydrogen_params.meta_products,
      });
    }

    return metadata;
  }

  // Function to get payment filters
  function getPaymentFilters() {
    let filters = {};

    // Add allowed banks and card brands if available
    if (wc_hydrogen_params.card_channel) {
      if (wc_hydrogen_params.banks_allowed) {
        filters.banks = wc_hydrogen_params.banks_allowed;
      }
      if (wc_hydrogen_params.cards_allowed) {
        filters.card_brands = wc_hydrogen_params.cards_allowed;
      }
    }

    return filters;
  }

  // Function to display a modal with a message and redirect URL
  function showModal(message, redirectUrl) {
    let modalHTML = `
        <style>
          .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
          }
  
          .hydrogen-modal-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 1000px !important;
            z-index: 1001;
          }
  
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

    $("body").append(modalHTML);
    $("#okButton").on("click", function () {
      window.location.href = redirectUrl;
    });
  }

  // Function to get available payment channels
  function getPaymentChannels() {
    let channels = [];

    if (wc_hydrogen_params.bank_channel) {
      channels.push("bank");
    }

    if (wc_hydrogen_params.card_channel) {
      channels.push("card");
    }

    if (wc_hydrogen_params.ussd_channel) {
      channels.push("ussd");
    }

    if (wc_hydrogen_params.qr_channel) {
      channels.push("qr");
    }

    if (wc_hydrogen_params.bank_transfer_channel) {
      channels.push("bank_transfer");
    }

    return channels;
  }

  // Function to handle the payment process
  async function handlePayment() {
    $("#wc-hydrogen-form").hide();

    $("form#payment-form, form#order_review")
      .find("input.hydrogen_txnref")
      .val("");

    let amount = Number(wc_hydrogen_params.amount);
    let currentUrl = window.location.href;

    // Set up payment object
    window.obj = {
      amount: amount,
      email: wc_hydrogen_params.email,
      currency: wc_hydrogen_params.currency,
      description:
        "Payment for items ordered with ID " + wc_hydrogen_params.meta_order_id,
      customerName: wc_hydrogen_params.meta_name,
      meta: wc_hydrogen_params.meta_name,
      callback: currentUrl,
      isAPI: true,
      returnRef: 2
    };

    window.token = wc_hydrogen_params.key;

    // Check if there are any available payment channels and filters
    if (Array.isArray(getPaymentChannels()) && getPaymentChannels().length) {
      paymentData.channels = getPaymentChannels();
      if (!$.isEmptyObject(getPaymentFilters())) {
        paymentData.metadata.custom_filters = getPaymentFilters();
      }
    }

    function adjustModalHeight() {
      const modalContent = document.getElementById("hydrogenPay_modal");
      if (modalContent) {
        //modalContent.style.height = "73.5%";
        modalContent.style.marginTop = "0px";
      }

      const modalClose = document.querySelector("#hydrogenPay_modal .close");
      if (modalClose) {
        //modalContent.style.height = "73.5%";
        modalClose.style.color = "white";
      }

      const modal = document.getElementById("hydrogenPay_myModal");
      if (modal) {
        modal.style.zIndex = "9999";
      }
    }

    function adjustModalHeightMobile() {
      const modalContent = document.getElementById("hydrogenPay_modal");
      if (modalContent) {
        //modalContent.style.height = "73.5%";
        modalContent.style.marginTop = "-20px";
      }

      const modalClose = document.querySelector("#hydrogenPay_modal .close");
      if (modalClose) {
        //modalContent.style.height = "73.5%";
        modalClose.style.color = "white";
      }

      const modal = document.getElementById("hydrogenPay_myModal");
      if (modal) {
        modal.style.zIndex = "9999";
      }
    }

    let urlParams = new URLSearchParams(window.location.search);
    let transactionRef = urlParams.get("TransactionRef");
    let key = urlParams.get("key");

    let newUrl = window.location.href;
    let keyIndex = newUrl.indexOf("key=");
    if (keyIndex !== -1) {
      let keyString = newUrl.substring(keyIndex);
      let queryIndex = keyString.indexOf("?");
      if (queryIndex !== -1) {
        keyString = keyString.substring(0, queryIndex);
      }
      keyString.split("=")[1];
    }

    // If transactionRef is not found, parse the URL to get it
    if (!transactionRef) {
      let urlString = window.location.href;
      let questionMarkIndex = urlString.lastIndexOf("?");
      if (questionMarkIndex !== -1) {
        let params = urlString.substring(questionMarkIndex + 1).split("&");
        for (let i = 0; i < params.length; i++) {
          let param = params[i].split("=");
          if (param[0] === "TransactionRef") {
            transactionRef = param[1];
            break;
          }
        }
      }
    }

    // If transactionRef is found, handle the callback and confirm payment
    if (transactionRef) {
      if (window !== window.parent) {
        var response = { event: "callback", transactionRef: transactionRef };
        window.parent.postMessage(JSON.stringify(response), "*");
      } else {
        let orderId = wc_hydrogen_params.meta_order_id;
        let redirectUrl = wc_hydrogen_params.hydrogen_wc_redirect_url;
        let baseUrl = window.location.href.replace(
          /\/checkout\/order-pay\/\d+\/.*/,
          ""
        );
        baseUrl += "/cart/";
        transactionRef = transactionRef;
        confirmPayment(transactionRef);
      }
    } else {
      // Fetch transaction reference if not found
      async function fetchTransactionRef() {
        try {
          let response = await handlePgData(window.obj, window.token, onClose);
          console.log("return transaction ref", response);
          transactionRef = response;
          if (window.innerWidth > 768) {
            // Remove the 'height' style property from the div only for larger screens
            adjustModalHeight();
          } else {
            adjustModalHeightMobile();
          }
        } catch (error) {
          console.error("Error occurred:", error);
        }
      }

      fetchTransactionRef();
    }

    // Function to handle the callback URL
    function callbackURL(transactionRef) {
      const modalContainer = document.getElementById("hydrogenPay_myModal");
      if (modalContainer) {
        modalContainer.remove();
        confirmPayment(transactionRef);
      }
    }

    // Function to handle the close event
    function onClose(transactionRef) {
      var response = { event: "close", transactionRef: transactionRef };
      window.parent.postMessage(JSON.stringify(response), "*");
    }

    // Function to confirm the payment
    function confirmPayment(transactionRef) {
      let spinnerHTML = `
          <style>
            .spinner-overlay {
              position: fixed;
              top: 0;
              left: 0;
              width: 100%;
              height: 100%;
              background: rgba(255, 255, 255, 0.8);
              display: flex;
              justify-content: center;
              align-items: center;
              z-index: 9999;
            }
  
            .spinner {
              border: 4px solid rgba(0, 0, 0, 0.1);
              border-top: 4px solid #3498db;
              border-radius: 50%;
              width: 40px;
              height: 40px;
              animation: spin 1s linear infinite;
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

      $("body").append(spinnerHTML);
      $("#wc-hydrogen-form").show();

      let orderId = wc_hydrogen_params.meta_order_id;

      $.ajax({
        url: "/wc-api/wc_gateway_hydrogen_popup",
        method: "POST",
        data: {
          transactionRef: JSON.stringify(transactionRef),
          hydrogenOderId: orderId,
        },
        dataType: "json",
        beforeSend: function () {},
        success: function (response) {
          let baseUrl = window.location.href.replace(
            /\/checkout\/order-pay\/\d+\/.*/,
            ""
          );
          baseUrl += "/cart/";

          if (response.statusCode === "90000") {
            let successMessage = `Your payment for order #${orderId} is successful and confirmed! Check your email or account for order details.`;
            showModal(successMessage, baseUrl);
          } else {
            let failureMessage = `Your payment for order #${orderId} was declined with status: Failed! Click Ok.`;
            showModal(failureMessage, baseUrl);
          }
        },
        error: function (xhr, status, error) {
          console.log("Error:", status, error);
        },
        complete: function () {
          $("#loading-spinner").remove();
        },
      });
    }

    // Event listener for messages from the parent window
    window.addEventListener(
      "message",
      function (event) {
        var messageResponse = JSON.parse(event.data);
        switch (messageResponse.event) {
          case "callback":
            console.log("Callback successful:", messageResponse.transactionRef);
            callbackURL(messageResponse.transactionRef);
            break;
          case "close":
            console.log(
              "Payment Modal closed and execute payment confirmation:",
              messageResponse.transactionRef
            );
            confirmPayment(messageResponse.transactionRef);
            break;
          default:
            console.log("Unknown event:", messageResponse);
            break;
        }
      },
      false
    );
  }

  $("#wc-hydrogen-form").hide();
  handlePayment();

  $("#hydrogen-payment-button").click(function () {
    handlePayment();
  });

  $("#hydrogen_form form#order_review").submit(function () {
    handlePayment();
  });
});
