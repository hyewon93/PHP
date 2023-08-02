<!DOCTYPE html>
<html>
<head>
	<title>Test Checkout</title>
	<link rel="stylesheet" type="text/css" href="./public_html/css/style.css">
    <link rel="stylesheet" type="text/css" href="./public_html/css/bootstrap.css">
</head>
<body>
    <form id="checkoutForm" onsubmit="paymentForm.submit(); return false;">
        <div class="container">
            <div class="row">
                <div class="col-sm-8">
                    <div class="container">
                        <div class="row form-group">
                            <label for="name" class="form-label mt-4">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" autocomplete="off" required>
                        </div>
                        <div class="row form-group">
                            <label for="email" class="form-label mt-4">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" autocomplete="off" required>
                        </div>
                        <div class="row form-group">
                            <label for="address" class="form-label mt-4">Address</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="Address" autocomplete="off" required>
                        </div>
                        <div class="row form-group">
                            <label for="city" class="form-label mt-4">City</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="City" autocomplete="off" required>
                        </div>
                        <div class="row">
                            <dis class="col form-group">
                                <label for="country" class="form-label mt-4">Country</label>
                                <select class="form-select" id="country" name="country" required>
                                    <option value="CA">Country</option>
                                </select>
                            </dis>
                            <dis class="col form-group">
                                <label for="province" class="form-label mt-4">State / Province</label>
                                <select class="form-select" id="province" name="province" required>
                                    <option value="BC">British Columbia</option>
                                </select>
                            </dis>
                            <dis class="col form-group">
                                <label for="postalCode" class="form-label mt-4">Zip / Postal Code</label>
                                <input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="Zip / Postal Code" autocomplete="off" required>
                            </dis>
                        </div>
                        <div class="row payment-error-container" id="payment-error-container" style="display:none;">
                            <div class="bs-component">
                                <div class="alert alert-dismissible alert-danger">
                                    <strong>Oh snap!</strong> <p id="payment-error"></p>
                                </div>
                            </div>
                        </div>
                        <div class="row payment-card-container">
                            <div id="card-element">
                                <!--Stripe.js injects the Card Element-->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="container">
                        <div class="row">
                            <div class="col"><img class="product-image" src="./public_html/images/cup.png" /></div>
                            <div class="col product-title">
                                <h4>Red Cup</h4>
                                $ 20.00
                            </div>
                        </div>
                        <div class="row">
                            <button id="submit-button" type="submit" class="btn btn-primary submit-button">
                                <span class="submit-button-text">Submit</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <form id="completedForm" onsubmit="location.replace(location.href); return false;" style="display: none;">
        <div class="completed-form">
            <div class="bs-component">
                <div class="alert alert-dismissible alert-success">
                    <strong>Your order was successful!</strong><br/> 
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Go back to checkout</button>
        </div>
    </form>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var paymentForm = new Object();
        var stripePublicKey = "%% Your Stripe Public Key %%";
        var stripe = Stripe(stripePublicKey);
        var card = "";

        paymentForm.__construct = function() {
            paymentForm.__init();
        }

        paymentForm.__init = function() {

            // Import Stripe Card
            var elements = stripe.elements();
            var style = {
                base: {
                    fontWeight: '500',
                    fontSize: '16px',
                    fontSmoothing: 'antialiased',
                }
            };
            card = elements.create("card", {
                hidePostalCode: true,
                style: style
            });
            // Stripe injects an iframe into the DOM
            card.mount("#card-element");
        }

        paymentForm.loading = function(isLoading) {
            if(isLoading) {
                document.querySelector('#submit-button').disabled = true;
                document.querySelector('#submit-button').classList.add('button-loading');
                document.querySelector('.submit-button-text').textContent = "";
            } else {
                document.querySelector('#submit-button').disabled = false;
                document.querySelector('#submit-button').classList.remove('button-loading');
                document.querySelector('.submit-button-text').textContent = "Submit";
            }
            
        }

        paymentForm.submit = async function() {
            
            // TODO: form validation

            paymentForm.loading(true);

            const {error, paymentMethod} = await stripe.createPaymentMethod({
                type: 'card',
                card: card
            });

            if(error) {
                let errorEl = document.querySelector('#payment-error');
                errorEl.textContent = '';

                errorEl.textContent = error.message;

                document.querySelector('#payment-error-container').style.display = "block";

                paymentForm.loading(false);

            } else {

                var request = {
                    "f": "submitForm",
                    "amount": 20.00,
                    "name": document.querySelector('#name').value,
                    "email": document.querySelector('#email').value,
                    "address": document.querySelector('#address').value,
                    "city": document.querySelector('#city').value,
                    "country": document.querySelector('#country').value,
                    "province": document.querySelector('#province').value,
                    "postalCode": document.querySelector('#postalCode').value,
                    "paymentMethodID": paymentMethod.id,
                };

                var xhr = new XMLHttpRequest();
                xhr.open('POST', "./function.php", true);
                xhr.setRequestHeader('Content-type', 'application/json');
                xhr.responseType = "json";
                xhr.send(JSON.stringify(request));

                xhr.onload = async function () {

                    if(xhr.response.status == "OK") {

                        document.querySelector("#checkoutForm").style.display = "none";
                        document.querySelector("#completedForm").style.display = "block";

                    } else if(xhr.response.status == "required_action") {

                        const handleCardAction = await stripe.handleCardAction(xhr.response.data.client_secret);

                        if(handleCardAction.error) {
                            let errorEl = document.querySelector('#payment-error');
                            errorEl.textContent = '';

                            errorEl.textContent = handleCardAction.error.message;

                            document.querySelector('#payment-error-container').style.display = "block";

                            paymentForm.loading(false);

                        } else {

                            var request = {
                                "f": "confirm3DSecure",
                                "paymentIntentID" : xhr.response.data.paymentIntentID
                            };

                            var xhr2 = new XMLHttpRequest();
                            xhr2.open('POST', "./function.php", true);
                            xhr2.setRequestHeader('Content-type', 'application/json');
                            xhr2.responseType = "json";
                            xhr2.send(JSON.stringify(request));

                            xhr2.onload = function () {
                                
                                if(xhr2.response.status == "OK") {

                                    document.querySelector("#checkoutForm").style.display = "none";
                                    document.querySelector("#completedForm").style.display = "block";

                                } else {
                                    let errorEl = document.querySelector('#payment-error');
                                    errorEl.textContent = '';

                                    errorEl.textContent = xhr.response.message;

                                    document.querySelector('#payment-error-container').style.display = "block";

                                    paymentForm.loading(false);
                                }
                            }
                        }

                    } else {
                        let errorEl = document.querySelector('#payment-error');
                        errorEl.textContent = '';

                        errorEl.textContent = xhr.response.message;

                        document.querySelector('#payment-error-container').style.display = "block";

                        paymentForm.loading(false);
                    }
                }
            }
        }

        paymentForm.__construct();

    </script>
</body>
</html>