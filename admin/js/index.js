function addVariant() {
  document.getElementById("variantWrapper").insertAdjacentHTML(
    "beforeend",
    `
        <div class="row g-2 align-items-center mb-2">
            <div class="col-md-3">
                <input name="variant_type[]" class="form-control" placeholder="Type (e.g. Size)" required>
            </div>
            <div class="col-md-3">
                <input name="variant_value[]" class="form-control" placeholder="Variant (e.g. A4)" required>
            </div>
            <div class="col-md-2">
                <input name="variant_price[]" type="number" class="form-control" placeholder="Price" required>
            </div>
            <div class="col-md-2">
                <input name="variant_stock[]" type="number" class="form-control" placeholder="Stock" required>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger"
                    onclick="this.closest('.row').remove()">✕</button>
            </div>
        </div>
    `
  );
}

function addSpec() {
  document.getElementById("specWrapper").insertAdjacentHTML(
    "beforeend",
    `
        <div class="row g-2 align-items-center mb-2">
            <div class="col-md-5">
                <input name="spec_name[]" class="form-control" placeholder="Specification" required>
            </div>
            <div class="col-md-5">
                <input name="spec_value[]" class="form-control" placeholder="Value" required>
            </div>
            <div class="col-md-2 text-end">
                <button type="button" class="btn btn-sm btn-outline-danger"
                    onclick="this.closest('.row').remove()">✕</button>
            </div>
        </div>
    `
  );
}
// AUTO SKU GENERATION
document.getElementById("productName").addEventListener("input", () => {
  const rand = Math.floor(100000 + Math.random() * 900000);
  document.getElementById("sku").value = "SKU-" + rand;
});
