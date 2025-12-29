    
      // Step elements
      const step1 = document.getElementById("step1");
      const studentForm = document.getElementById("studentForm");
      const employeeForm = document.getElementById("employeeForm");
      const vehicleFormStep = document.getElementById("vehicleFormStep");
      const documentsFormStep = document.getElementById("documentsFormStep");
      const vehicleToDocuments = document.createElement("button");
      vehicleToDocuments.type = "button";
      vehicleToDocuments.id = "vehicleToDocuments";
      vehicleToDocuments.className = "register-btn";
      vehicleToDocuments.setAttribute("data-step", "vehicle");
      // This button is already added in the HTML above

      const backToVehicle = document.getElementById("backToVehicle");

      // Get references to back button and classification select
      const backToPersonal = document.getElementById("backToPersonal");
      const classification = document.getElementById("classification");

      // Back button for vehicle step
      backToPersonal.addEventListener("click", function () {
        vehicleFormStep.classList.add("hidden");
        if (classification.value === "student") {
          studentForm.classList.remove("hidden");
        } else if (classification.value === "employee") {
          employeeForm.classList.remove("hidden");
        }
        setStepIndicator(1);
      });

      // Back button for student form (step 1b)
      document
        .getElementById("backToStep1_student")
        .addEventListener("click", function () {
          studentForm.classList.add("hidden");
          step1.classList.remove("hidden");
          setStepIndicator(1);
        });

      // Back button for employee form (step 1b)
      document
        .getElementById("backToStep1_employee")
        .addEventListener("click", function () {
          employeeForm.classList.add("hidden");
          step1.classList.remove("hidden");
          setStepIndicator(1);
        });

      // Extend the handler for step 2 and 3
      function handleContinue(e) {
        const step = e.target.getAttribute("data-step");
        switch (step) {
          case "personal":
            if (!classification.value) {
              alert("Please select a classification.");
              return;
            }
            step1.classList.add("hidden");
            if (classification.value === "student") {
              studentForm.classList.remove("hidden");
            } else if (classification.value === "employee") {
              employeeForm.classList.remove("hidden");
            }
            // Step indicator stays at 1
            break;
          case "student":
            studentForm.classList.add("hidden");
            vehicleFormStep.classList.remove("hidden");
            setStepIndicator(2);
            break;
          case "employee":
            employeeForm.classList.add("hidden");
            vehicleFormStep.classList.remove("hidden");
            setStepIndicator(2);
            break;
          case "vehicle":
            vehicleFormStep.classList.add("hidden");
            documentsFormStep.classList.remove("hidden");
            setStepIndicator(3);
            break;
          // Add more cases if you add more steps
        }
      }

      // Attach handler to all Continue buttons (including vehicle step)
      document.querySelectorAll(".register-btn[data-step]").forEach((btn) => {
        btn.addEventListener("click", handleContinue);
      });

      // Back button for documents step
      document
        .getElementById("backToVehicle")
        .addEventListener("click", function () {
          documentsFormStep.classList.add("hidden");
          vehicleFormStep.classList.remove("hidden");
          setStepIndicator(2);
        });

      // Step indicator elements
      const indicator1 = document.getElementById("step-indicator-1");
      const indicator2 = document.getElementById("step-indicator-2");
      const indicator3 = document.getElementById("step-indicator-3");

      // Step indicator logic
      function setStepIndicator(step) {
        // Remove all active/completed classes first
        [indicator1, indicator2, indicator3].forEach((el) => {
          el.classList.remove("active", "completed");
        });

        // Activate steps up to the current step
        if (step >= 1) indicator1.classList.add("active");
        if (step >= 2) indicator2.classList.add("active");
        if (step >= 3) indicator3.classList.add("active");
      }

      // Drag and drop for file inputs in documentsFormStep
      document
        .querySelectorAll("#documentsFormStep .dropzone")
        .forEach((dropzone) => {
          const inputId = dropzone.getAttribute("data-input");
          const fileInput = dropzone.querySelector('input[type="file"]');
          const previewImg = dropzone.querySelector(".file-preview");
          const removeBtn = dropzone.querySelector(".remove-preview");

          // Highlight dropzone on dragover
          dropzone.addEventListener("dragover", function (e) {
            e.preventDefault();
            dropzone.classList.add("dragover");
          });

          dropzone.addEventListener("dragleave", function (e) {
            e.preventDefault();
            dropzone.classList.remove("dragover");
          });

          dropzone.addEventListener("drop", function (e) {
            e.preventDefault();
            dropzone.classList.remove("dragover");
            if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
              fileInput.files = e.dataTransfer.files;
              const label = dropzone.querySelector(".browse-files");
              label.textContent = e.dataTransfer.files[0].name;
              showPreview(
                e.dataTransfer.files[0],
                previewImg,
                removeBtn,
                fileInput,
                label
              );
            }
          });

          // Also update label and preview when file selected via click
          fileInput.addEventListener("change", function (e) {
            const label = dropzone.querySelector(".browse-files");
            if (fileInput.files.length > 0) {
              label.textContent = fileInput.files[0].name;
              showPreview(
                fileInput.files[0],
                previewImg,
                removeBtn,
                fileInput,
                label
              );
            } else {
              label.textContent = "Browse Files";
              previewImg.style.display = "none";
              previewImg.src = "";
              if (removeBtn) removeBtn.style.display = "none";
            }
          });

          // Remove/replace file logic
          if (removeBtn) {
            removeBtn.addEventListener("click", function () {
              fileInput.value = "";
              previewImg.src = "";
              previewImg.style.display = "none";
              removeBtn.style.display = "none";
              const label = dropzone.querySelector(".browse-files");
              label.textContent = "Browse Files";
            });
          }

          function showPreview(file, imgElem, removeBtn, fileInput, label) {
            if (file && file.type.startsWith("image/")) {
              const reader = new FileReader();
              reader.onload = function (e) {
                imgElem.src = e.target.result;
                imgElem.style.display = "block";
                if (removeBtn) removeBtn.style.display = "block";
              };
              reader.readAsDataURL(file);
            } else {
              imgElem.style.display = "none";
              imgElem.src = "";
              if (removeBtn) removeBtn.style.display = "block";
            }
          }
        });

      // Continue button for Step 3 (Documents) to show Step 4 (Submit Section)
      document
        .getElementById("documentsToSubmit")
        .addEventListener("click", function () {
          documentsFormStep.classList.add("hidden");
          document.querySelector(".submit-section").classList.remove("hidden");
          setStepIndicator(3); // Keep the indicator at step 3
          document.querySelector(".step-indicator").style.display = "none"; // Hide step indicator
        });

      // Show submit-section and hide documentsFormStep on submit
      document
        .getElementById("registrationForm")
        .addEventListener("submit", function (e) {
          e.preventDefault(); // Prevent actual form submission
          documentsFormStep.classList.add("hidden");
          document.querySelector(".submit-section").classList.remove("hidden");
          setStepIndicator(3); // Optionally keep the indicator at step 3
          document.querySelector(".step-indicator").style.display = "none"; // Hide step indicator
        });
