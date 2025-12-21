/* ===============================
   CUSTOM SELECT
================================ */
document.querySelectorAll(".custom-select").forEach((select) => {
  const button = select.querySelector(".custom-select-btn");
  const options = select.querySelector(".custom-select-options");
  const hiddenInput = document.getElementById(select.dataset.target);

  button.addEventListener("click", (e) => {
    e.stopPropagation();

    document.querySelectorAll(".custom-select").forEach((s) => {
      if (s !== select) s.classList.remove("open");
    });

    select.classList.toggle("open");
  });

  options.querySelectorAll("li").forEach((option) => {
    option.addEventListener("click", () => {
      button.childNodes[0].textContent = option.textContent;
      hiddenInput.value = option.dataset.value;
      select.classList.remove("open");
    });
  });
});

// Close custom select on outside click
document.addEventListener("click", () => {
  document
    .querySelectorAll(".custom-select")
    .forEach((s) => s.classList.remove("open"));
});

/* ===============================
   WIZARD LOGIC
================================ */
let currentStep = 0;
const steps = document.querySelectorAll(".wizard-step");
const progressSteps = document.querySelectorAll(".wizard-steps .step");
const nextBtn = document.getElementById("nextBtn");
const prevBtn = document.getElementById("prevBtn");
const form = document.getElementById("printWizardForm");

function validateStep(stepIndex) {
  const step = steps[stepIndex];
  const requiredFields = step.querySelectorAll("[required]");

  for (let field of requiredFields) {
    if (!field.value) {
      field.classList.add("is-invalid");
      return false;
    }
    field.classList.remove("is-invalid");
  }
  return true;
}

function updateSummary() {
  document.getElementById("summaryService").textContent =
    document.getElementById("printType").value || "—";

  document.getElementById("summaryPaper").textContent =
    form.querySelector('select[name="paper"]')?.value || "—";

  document.getElementById("summaryQty").textContent =
    form.querySelector('input[type="number"]')?.value || "—";

  document.getElementById("summaryColor").textContent =
    form.querySelector('select[name="color"]')?.value || "—";

  document.getElementById("summaryNotes").textContent =
    form.querySelector("textarea")?.value || "—";
}

function updateWizard() {
  steps.forEach((step, index) => {
    step.classList.toggle("active", index === currentStep);
  });

  progressSteps.forEach((step, index) => {
    step.classList.remove("active", "completed", "locked");

    if (index < currentStep) step.classList.add("completed");
    if (index === currentStep) step.classList.add("active");
    if (index > currentStep) step.classList.add("locked");
  });

  prevBtn.style.display = currentStep === 0 ? "none" : "inline-block";
  nextBtn.textContent = currentStep === steps.length - 1 ? "Submit" : "Next";

  if (currentStep === 3) updateSummary();
}

nextBtn.addEventListener("click", () => {
  if (!validateStep(currentStep)) return;

  if (currentStep < steps.length - 1) {
    currentStep++;
    updateWizard();
  } else {
    form.submit();
  }
});

prevBtn.addEventListener("click", () => {
  if (currentStep > 0) {
    currentStep--;
    updateWizard();
  }
});

updateWizard();
const uploadZone = document.getElementById("uploadZone");
const fileInput = document.getElementById("fileInput");
const filePreview = document.getElementById("filePreview");
const fileName = document.getElementById("fileName");
const fileSize = document.getElementById("fileSize");
const removeFile = document.getElementById("removeFile");

// Drag events
["dragenter", "dragover"].forEach((event) => {
  uploadZone.addEventListener(event, (e) => {
    e.preventDefault();
    uploadZone.classList.add("dragover");
  });
});

["dragleave", "drop"].forEach((event) => {
  uploadZone.addEventListener(event, (e) => {
    e.preventDefault();
    uploadZone.classList.remove("dragover");
  });
});

// Drop file
uploadZone.addEventListener("drop", (e) => {
  const file = e.dataTransfer.files[0];
  if (file) handleFile(file);
});

// Click upload
fileInput.addEventListener("change", () => {
  if (fileInput.files[0]) handleFile(fileInput.files[0]);
});

// Handle file
function handleFile(file) {
  fileInput.files = new DataTransfer().files;
  const dt = new DataTransfer();
  dt.items.add(file);
  fileInput.files = dt.files;

  fileName.textContent = file.name;
  fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + " MB";
  filePreview.classList.remove("d-none");
}

// Remove file
removeFile.addEventListener("click", () => {
  fileInput.value = "";
  filePreview.classList.add("d-none");
});

document.querySelectorAll(".variant-group").forEach((group) => {
  const input = group.nextElementSibling;

  group.querySelectorAll(".variant-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      group
        .querySelectorAll(".variant-btn")
        .forEach((b) => b.classList.remove("active"));

      btn.classList.add("active");
      input.value = btn.textContent.trim();
    });
  });
});
