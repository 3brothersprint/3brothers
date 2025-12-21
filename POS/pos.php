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
        background: #f4f6f9;
    }

    .pos-input {
        font-size: 22px;
        height: 60px;
        border-radius: 12px;
    }

    .table td {
        vertical-align: middle;
    }

    .card {
        border-radius: 16px;
    }

    button {
        border-radius: 10px;
    }
    </style>
</head>

<body>

    <div class="container-fluid p-3">
        <div class="row g-3">

            <!-- LEFT PANEL -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="fw-bold mb-0">🧾 POS</h4>

                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cameraModal"
                                onclick="startCamera()">
                                📱 Scan with Camera
                            </button>
                        </div>

                        <!-- BARCODE INPUT -->
                        <input type="text" id="barcodeInput" class="form-control pos-input mb-3"
                            placeholder="Scan barcode / type barcode" autofocus>

                        <!-- CART -->
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th width="120">Price</th>
                                        <th width="120">Qty</th>
                                        <th width="120">Total</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody id="cartBody"></tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top:20px">
                    <div class="card-body">

                        <h5 class="fw-semibold">Summary</h5>
                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Total</span>
                            <strong>₱ <span id="grandTotal">0.00</span></strong>
                        </div>

                        <button class="btn btn-success w-100 mt-3 py-3 fs-5" onclick="checkout()">
                            💳 Checkout
                        </button>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade" id="variantModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="variantTitle"></h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div id="variantList" class="list-group"></div>
                </div>
            </div>
        </div>
    </div>

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

    function scanBarcode(code) {
        fetch("scan_barcode.php?code=" + code)
            .then(res => res.json())
            .then(item => {

                if (!item) return alert("Item not found");

                if (item.type === 'variants') {
                    loadVariants(item.product_id, item.name);
                    return;
                }

                addToCart(item.barcode, item);
            });
    }

    function loadVariants(productId, name) {
        fetch("fetch_variants.php?id=" + productId)
            .then(res => res.json())
            .then(list => {
                document.getElementById("variantTitle").innerText = name;
                let html = '';

                list.forEach(v => {
                    html += `
                <button class="list-group-item list-group-item-action"
                    onclick="addToCart('${v.barcode}', ${JSON.stringify(v).replace(/"/g,'&quot;')})">
                    ${v.value} — ₱${v.price}
                    <span class="badge bg-secondary float-end">Stock: ${v.stock}</span>
                </button>`;
                });

                document.getElementById("variantList").innerHTML = html;
                new bootstrap.Modal('#variantModal').show();
            });
    }
    const barcodeInput = document.getElementById("barcodeInput");

    barcodeInput.addEventListener("keydown", function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            scanBarcode(this.value.trim());
            this.value = "";
        }
    });

    function addToCart(code, item) {

        if (item.stock <= 0) {
            alert("Out of stock ❌");
            return;
        }

        if (!cart[code]) {
            cart[code] = {
                ...item,
                qty: 1
            };
        } else {
            if (cart[code].qty + 1 > item.stock) {
                alert("Not enough stock");
                return;
            }
            cart[code].qty++;
        }

        renderCart();
    }
    </script>

</body>

</html>