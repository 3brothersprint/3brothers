document.addEventListener("DOMContentLoaded", () => {
  let currentStep = 0;

  const steps = document.querySelectorAll(".wizard-step");
  const nextBtn = document.getElementById("nextBtn");
  const prevBtn = document.getElementById("prevBtn");

  /* ================= STEP CONTROL ================= */
  const indicators = document.querySelectorAll(".wizard-steps .step");
  const progress = document.querySelector(".wizard-progress");
  const progressColors = [
    "#f9a36b", // Step 1 → Step 2
    "#4e8cff", // Step 2 → Step 3
    "#9b6dff", // Step 3 → Step 4
  ];

  // Build progress segments ONCE
  progress.innerHTML = "";
  for (let i = 0; i < indicators.length - 1; i++) {
    const seg = document.createElement("span");
    progress.appendChild(seg);
  }
  const segments = progress.querySelectorAll("span");
  function showStep(index) {
    /* ================= STEP CONTENT ================= */
    steps.forEach((step, i) => {
      step.classList.toggle("active", i === index);
    });

    /* ================= STEP INDICATORS ================= */
    indicators.forEach((step, i) => {
      step.classList.remove("active", "completed");

      const circle = step.querySelector(".step-circle");

      if (i < index) {
        step.classList.add("completed");
        circle.innerHTML = "✓";
      } else if (i === index) {
        step.classList.add("active");
        circle.innerHTML = i + 1;
      } else {
        circle.innerHTML = i + 1;
      }
    });

    /* ================= PROGRESS BAR ================= */
    segments.forEach((seg, i) => {
      if (i < index) {
        seg.classList.add("active");
        seg.style.setProperty(
          "--seg-color",
          progressColors[i] || progressColors[0]
        );
      } else {
        seg.classList.remove("active");
        seg.style.removeProperty("--seg-color");
      }
    });

    /* ================= BUTTON VISIBILITY ================= */
    prevBtn.style.display = index === 0 ? "none" : "inline-block";
    nextBtn.innerText = index === steps.length - 1 ? "Submit" : "Next";
  }

  function validateStep(stepIndex) {
    const inputs = steps[stepIndex].querySelectorAll("input, select, textarea");
    for (let input of inputs) {
      if (input.hasAttribute("required") && !input.value) {
        input.classList.add("is-invalid");
        return false;
      }
      input.classList.remove("is-invalid");
    }
    return true;
  }

  nextBtn.addEventListener("click", () => {
    if (!validateStep(currentStep)) return;

    if (currentStep === steps.length - 1) {
      document.getElementById("printWizardForm").submit();
      return;
    }

    currentStep++;
    fillSummary();
    showStep(currentStep);
  });

  prevBtn.addEventListener("click", () => {
    if (currentStep > 0) {
      currentStep--;
      showStep(currentStep);
    }
  });

  showStep(currentStep);

  /* ================= CUSTOM SELECT ================= */
  document.querySelectorAll(".custom-select").forEach((select) => {
    const btn = select.querySelector(".custom-select-btn");
    const options = select.querySelector(".custom-select-options");
    const hiddenInput = document.getElementById(select.dataset.target);

    btn.addEventListener("click", () => {
      options.classList.toggle("show");
    });

    options.querySelectorAll("li").forEach((option) => {
      option.addEventListener("click", () => {
        btn.innerText = option.innerText;
        hiddenInput.value = option.dataset.value;
        options.classList.remove("show");
      });
    });

    document.addEventListener("click", (e) => {
      if (!select.contains(e.target)) options.classList.remove("show");
    });
  });

  /* ================= FILE UPLOAD ================= */
  const fileInput = document.getElementById("fileInput");
  const filePreview = document.getElementById("filePreview");
  const uploadZone = document.getElementById("uploadZone");

  let selectedFiles = [];

  /* ================= FILE SELECT ================= */
  fileInput.addEventListener("change", () => {
    const files = Array.from(fileInput.files);

    // Limit to 5 files total
    if (selectedFiles.length + files.length > 5) {
      alert("You can upload a maximum of 5 files only.");
      fileInput.value = "";
      return;
    }

    files.forEach((file) => {
      selectedFiles.push(file);
    });

    renderPreview();
    syncFileInput();
  });

  /* ================= PREVIEW ================= */
  function renderPreview() {
    filePreview.innerHTML = "";
    filePreview.classList.toggle("d-none", selectedFiles.length === 0);

    selectedFiles.forEach((file, index) => {
      const item = document.createElement("div");
      item.className =
        "d-flex align-items-center justify-content-between border rounded p-2 mb-2";

      item.innerHTML = `
      <div>
        <strong>${file.name}</strong>
        <div class="text-muted small">${(file.size / 1024).toFixed(1)} KB</div>
      </div>
      <button type="button" class="btn btn-sm btn-outline-danger">Remove</button>
    `;

      item.querySelector("button").addEventListener("click", () => {
        selectedFiles.splice(index, 1);
        renderPreview();
        syncFileInput();
      });

      filePreview.appendChild(item);
    });
  }

  /* ================= KEEP INPUT IN SYNC ================= */
  function syncFileInput() {
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach((file) => dataTransfer.items.add(file));
    fileInput.files = dataTransfer.files;
  }

  /* ================= CLICK ZONE ================= */
  uploadZone.addEventListener("click", () => fileInput.click());

  /* ================= SUMMARY ================= */
  function fillSummary() {
    document.getElementById("summaryService").innerText =
      document.getElementById("printType").value || "—";

    const selects = steps[1].querySelectorAll("select");
    const copies = steps[1].querySelector("input[type='number']");
    const notes = steps[2].querySelector("textarea");

    document.getElementById("summaryPaper").innerText =
      selects[0]?.value || "—";
    document.getElementById("summaryQty").innerText = copies?.value || "—";
    document.getElementById("summaryColor").innerText =
      selects[1]?.value || "—";
    document.getElementById("summaryNotes").innerText = notes?.value || "—";
  }
});
