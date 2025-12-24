function viewOrder(id) {
  const modalEl = document.getElementById("viewProductModal");

  if (!modalEl) {
    alert("Modal not found in DOM");
    return;
  }

  const modal = new bootstrap.Modal(modalEl);
  modal.show();

  const content = document.getElementById("viewProductContent");
  content.innerHTML = `
        <div class="text-center py-5 text-muted">
            Loading request...
        </div>
    `;

  fetch("/print/admin/print/view_order.php?id=" + id)
    .then((res) => res.text())
    .then((html) => (content.innerHTML = html))
    .catch((err) => {
      console.error(err);
      content.innerHTML = `
                <div class="alert alert-danger">
                    Failed to load request.
                </div>
            `;
    });
}
function updateOrderStatus(orderId) {
  const status = document.getElementById("orderStatus").value;
  const price = document.getElementById("orderPrice").value || null;
  const remark = document.getElementById("orderRemark").value || "";

  if (!status) {
    Swal.fire("Required", "Please select an action", "warning");
    return;
  }

  if (status === "Approved" && (!price || price <= 0)) {
    Swal.fire(
      "Invalid Price",
      "Please set a valid price before approving",
      "warning"
    );
    return;
  }

  Swal.fire({
    title: "Confirm action?",
    html: `
        <strong>Status:</strong> ${status}<br>
        <strong>Price:</strong> ₱${price ?? "—"}
    `,
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Yes, proceed",
    confirmButtonColor: status === "Rejected" ? "#dc3545" : "#0d6efd",
  }).then((result) => {
    if (!result.isConfirmed) return;

    fetch("/print/admin/print/update_order_status.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        id: orderId,
        status: status,
        price: price,
        remark: remark,
      }),
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          Swal.fire("Success", data.message, "success").then(() =>
            location.reload()
          );
        } else {
          Swal.fire("Error", data.message, "error");
        }
      });
  });
}
