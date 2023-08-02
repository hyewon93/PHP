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
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" autocomplete="off">
                        </div>
                        <div class="row form-group">
                            <label for="email" class="form-label mt-4">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" autocomplete="off">
                        </div>
                        <div class="row form-group">
                            <label for="address" class="form-label mt-4">Address</label>
                            <input type="text" class="form-control" id="address" name="address" placeholder="Address" autocomplete="off">
                        </div>
                        <div class="row form-group">
                            <label for="city" class="form-label mt-4">City</label>
                            <input type="text" class="form-control" id="city" name="city" placeholder="City" autocomplete="off">
                        </div>
                        <div class="row">
                            <dis class="col form-group">
                                <label for="country" class="form-label mt-4">Country</label>
                                <select class="form-select" id="country" name="country">
                                    <option value="CA">Country</option>
                                </select>
                            </dis>
                            <dis class="col form-group">
                                <label for="province" class="form-label mt-4">State / Province</label>
                                <select class="form-select" id="province" name="province">
                                    <option value="BC">British Columbia</option>
                                </select>
                            </dis>
                            <dis class="col form-group">
                                <label for="postalCode" class="form-label mt-4">Zip / Postal Code</label>
                                <input type="text" class="form-control" id="postalCode" name="postalCode" placeholder="Zip / Postal Code" autocomplete="off">
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
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        var paymentForm = new Object();
        var stripeKey = "pk_test_51NW1czAgQo6v5Vl63ElXnrRxBiGspcLCR2Bba4FZX1EC0bDqL18w1XN9kiy7DiiCrzUFUZ6MZ0mx1xEcOiaOfapy00XMKQvFtH";
        var stripe = Stripe(stripeKey);
        var card, token, clientSecret = "";

        paymentForm.__construct = function() {
            paymentForm.__init();
        }

        paymentForm.__init = function() {
/*
            // Create Payment Intent
            var request = {
                "f": "createPaymentIntent",
            }
            var xhr = new XMLHttpRequest();
            xhr.open('POST', "./function.php", true);
            xhr.setRequestHeader('Content-type', 'application/json');
            xhr.responseType = "json";
            xhr.send(JSON.stringify(request));

            xhr.onload = function () {
                console.log(xhr.response);

                if(xhr.response.status == "OK") {
                    token = xhr.response.data.token;
                    clientSecret = xhr.response.data.client_secret;
                }
            }
            */

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

        paymentForm.submit = async function() {
            
            // TODO: form validation

            const {error, paymentMethod} = await stripe.createPaymentMethod({
                type: 'card',
                card: card
            });

            if(error) {
                let errorEl = document.querySelector('#payment-error');
                errorEl.textContent = '';

                errorEl.textContent = error.message;

                document.querySelector('#payment-error-container').style.display = "block";

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

                xhr.onload = function () {
                    console.log(xhr.response);
                }
            }
        }

        paymentForm.__construct();

    </script>
</body>
</html>