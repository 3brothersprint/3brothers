<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    body {
        background: #f5f6f8;
    }

    .pos-input {
        font-size: 22px;
        height: 60px;
    }

    .cart-table td {
        vertical-align: middle;
    }
    </style>
</head>

<body>

    <div class="container-fluid p-4">
        <div class="row">

            <!-- LEFT -->
            <div class="col-md-7">
                <h4>🧾 POS</h4>

                <!-- BARCODE INPUT -->
                <input type="text" id="barcodeInput" class="form-control pos-input mb-3" placeholder="Scan barcode here"
                    autofocus>

                <!-- CART -->
                <table class="table table-bordered cart-table bg-white">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th width="120">Price</th>
                            <th width="120">Qty</th>
                            <th width="120">Total</th>
                            <th width="60"></th>
                        </tr>
                    </thead>
                    <tbody id="cartBody"></tbody>
                </table>
            </div>

            <!-- RIGHT -->
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Summary</h5>
                        <h2 class="fw-bold">₱ <span id="grandTotal">0.00</span></h2>

                        <button class="btn btn-success w-100 mt-3" onclick="checkout()">
                            Checkout
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="cameraModal">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Scan Barcode</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="camera" style="width:100%;height:300px"></div>
                </div>
            </div>
        </div>
    </div>
    <script>
    function startCamera() {
        Quagga.init({
            inputStream: {
                type: "LiveStream",
                target: document.querySelector('#camera')
            },
            decoder: {
                readers: ["code_128_reader"]
            }
        }, () => Quagga.start());

        Quagga.onDetected(data => {
            scanBarcode(data.codeResult.code);
            Quagga.stop();
            bootstrap.Modal.getInstance(
                document.getElementById("cameraModal")
            ).hide();
        });
    }
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@ericblade/quagga2/dist/quagga.min.js"></script>

    <script>
    let cart = {};

    document.getElementById("barcodeInput").addEventListener("change", function() {
        scanBarcode(this.value);
        this.value = "";
    });

    function scanBarcode(code) {
        fetch("scan_barcode.php?code=" + code)
            .then(res => res.json())
            .then(item => {
                if (!item) return alert("Item not found");

                if (!cart[code]) {
                    cart[code] = {
                        ...item,
                        qty: 1
                    };
                } else {
                    cart[code].qty++;
                }

                renderCart();
            });
    }

    function renderCart() {
        let body = document.getElementById("cartBody");
        let total = 0;
        body.innerHTML = "";

        Object.keys(cart).forEach(code => {
            let item = cart[code];
            let rowTotal = item.qty * item.price;
            total += rowTotal;

            body.innerHTML += `
            <tr>
                <td>${item.name}</td>
                <td>₱${item.price}</td>
                <td>
                    <input type="number" value="${item.qty}" min="1"
                        onchange="cart['${code}'].qty=this.value;renderCart()"
                        class="form-control">
                </td>
                <td>₱${rowTotal.toFixed(2)}</td>
                <td>
                    <button class="btn btn-sm btn-danger"
                        onclick="delete cart['${code}'];renderCart()">✕</button>
                </td>
            </tr>
        `;
        });

        document.getElementById("grandTotal").innerText = total.toFixed(2);
    }

    function checkout() {
        fetch("checkout.php", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(cart)
        }).then(() => {
            alert("Payment successful");
            cart = {};
            renderCart();
        });
    }
    </script>

</body>

</html>