function viewOrder(id) {
  const modalEl = document.getElementById("viewProductModal");
  const modal = new bootstrap.Modal(modalEl);
  modal.show();

  const content = document.getElementById("viewProductContent");
  content.innerHTML = `
        <div class="text-center py-5 text-muted">
            Loading order details...
        </div>
    `;

  fetch("/print/admin/get-product/view_order.php?id=" + id)
    .then((res) => res.text())
    .then((html) => (content.innerHTML = html))
    .catch(() => {
      content.innerHTML = `
                <div class="alert alert-danger">
                    Failed to load order.
                </div>
            `;
    });
}
function updateOrderStatus(orderId) {
  const status = document.getElementById("orderStatus").value;
  const remark = document.getElementById("orderRemark").value || "";

  if (!status) {
    Swal.fire("Required", "Select a status", "warning");
    return;
  }

  Swal.fire({
    title: "Confirm update?",
    text: "This will update the order status",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, update",
  }).then((result) => {
    if (!result.isConfirmed) return;

    fetch("/print/admin/get-product/update_order_status.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ id: orderId, status, remark }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          Swal.fire("Updated", data.message, "success").then(() =>
            location.reload()
          );
        } else {
          Swal.fire("Error", data.message, "error");
        }
      });
  });
}
