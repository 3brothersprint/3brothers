function loadProducts() {
  fetch(
    "/print/admin/products/fetch_products.php?search=" +
      search.value +
      "&category=" +
      categoryFilter.value
  )
    .then((res) => res.text())
    .then((data) => (document.querySelector("tbody").innerHTML = data));
}

document.getElementById("search").addEventListener("keyup", loadProducts);
document
  .getElementById("categoryFilter")
  .addEventListener("change", loadProducts);

function printBarcode(productId) {
  window.open(
    "./products/print_barcode.php?id=" + productId,
    "_blank",
    "width=400,height=600"
  );
}
function printVariantBarcode(variantId) {
  window.open(
    "./products/print_variant_barcode.php?id=" + variantId,
    "_blank",
    "width=400,height=600"
  );
}
function viewProduct(productId) {
  fetch("./products/view_product.php?id=" + productId)
    .then((res) => res.text())
    .then((html) => {
      document.getElementById("viewProductContent").innerHTML = html;
      new bootstrap.Modal(document.getElementById("viewProductModal")).show();
    });
}

function printVariantBarcode(id) {
  window.open(
    "./products/print_variant_barcode.php?id=" + id,
    "_blank",
    "width=400,height=600"
  );
}
function editProduct(id) {
  fetch("./products/edit_product.php?id=" + id)
    .then((res) => res.text())
    .then((html) => {
      document.getElementById("editProductContent").innerHTML = html;
      new bootstrap.Modal(document.getElementById("editProductModal")).show();
    });
}
function deleteProduct(id) {
  if (!confirm("Delete this product? This cannot be undone.")) return;

  window.location.href = "./products/delete_product.php?id=" + id;
}
function deleteProduct(id) {
  Swal.fire({
    title: "Delete Product?",
    text: "This action cannot be undone!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#dc3545",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Yes, delete it",
    cancelButtonText: "Cancel",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch("products/delete_product.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "id=" + id,
      })
        .then((res) => res.text())
        .then((response) => {
          if (response === "success") {
            Swal.fire({
              icon: "success",
              title: "Deleted!",
              text: "Product has been deleted.",
              timer: 1500,
              showConfirmButton: false,
            }).then(() => location.reload());
          } else {
            Swal.fire("Error", response, "error");
          }
        });
    }
  });
}
