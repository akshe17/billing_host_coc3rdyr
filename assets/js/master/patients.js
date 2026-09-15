// assets/js/master/patients.js

document.addEventListener("DOMContentLoaded", () => {
  const baseUrl = document.body.dataset.baseUrl || "";
  const alertBox = document.getElementById("alert");

  // Modal
  const modal = document.getElementById("patientModal");
  const form = document.getElementById("patientForm");
  const modalTitle = document.getElementById("patientModalTitle");
  const submitBtn = document.getElementById("patientSubmitBtn");
  const submitLbl = document.getElementById("patientSubmitLabel");
  const openCreate = document.getElementById("openCreateBtn");

  // Tabs
  const tabButtons = document.querySelectorAll(".tab-btn");
  const tabPanels = document.querySelectorAll(".tab-panel");
  const nextBtn = document.getElementById("nextTab");
  const prevBtn = document.getElementById("prevTab");
  const tabOrder = ["info", "admission", "room", "doctors", "diagnoses"];
  let currentTab = "info";

  // Admission toggle
  const createAdmission = document.getElementById("create_admission");
  const admissionFields = document.getElementById("admissionFields");
  const roomNotice = document.getElementById("roomNotice");
  const roomFields = document.getElementById("roomFields");
  const doctorsNotice = document.getElementById("doctorsNotice");
  const doctorsFields = document.getElementById("doctorsFields");
  const diagnosesNotice = document.getElementById("diagnosesNotice");
  const diagnosesFields = document.getElementById("diagnosesFields");

  // Doctor / diagnosis rows
  const doctorsList = document.getElementById("doctorsList");
  const diagnosesList = document.getElementById("diagnosesList");
  const addDoctorRow = document.getElementById("addDoctorRow");
  const addDiagnosisRow = document.getElementById("addDiagnosisRow");

  // ---------- Read doctors + diagnoses from the hidden #patientsData div ----------
  const dataHolder = document.getElementById("patientsData");

  let DOCTORS = [];
  let DIAGNOSES = [];

  if (dataHolder) {
    try {
      DOCTORS = JSON.parse(dataHolder.dataset.doctors || "[]");
    } catch (e) {
      console.error("[patients] failed to parse doctors data:", e);
    }
    try {
      DIAGNOSES = JSON.parse(dataHolder.dataset.diagnoses || "[]");
    } catch (e) {
      console.error("[patients] failed to parse diagnoses data:", e);
    }
  }

  // Confirm modal
  const confirmModal = document.getElementById("confirmModal");
  const confirmPatientName = document.getElementById("confirmPatientName");
  const confirmDeactivateBtn = document.getElementById("confirmDeactivateBtn");
  const confirmDeactivateLbl = document.getElementById(
    "confirmDeactivateLabel",
  );

  // ---------- Helpers ----------
  const showAlert = (msg, type = "error") => {
    const styles = {
      error: "bg-red-50 border-red-200 text-red-700",
      success: "bg-emerald-50 border-emerald-200 text-emerald-700",
    };
    alertBox.className = `mb-5 rounded-lg px-4 py-3 text-sm border ${styles[type] || styles.error}`;
    alertBox.textContent = msg;
    alertBox.classList.remove("hidden");
    alertBox.scrollIntoView({ behavior: "smooth", block: "nearest" });
  };
  const hideAlert = () => alertBox.classList.add("hidden");

  const clearErrors = (scope) => {
    scope.querySelectorAll("[data-error-for]").forEach((p) => {
      p.textContent = "";
      p.classList.add("hidden");
    });
  };
  const setError = (scope, field, msg) => {
    const p = scope.querySelector(`[data-error-for="${field}"]`);
    if (!p) return;
    p.textContent = msg || "";
    p.classList.toggle("hidden", !msg);
  };

  const openModal = (el) => el.classList.remove("hidden");
  const closeModal = (el) => el.classList.add("hidden");

  // ---------- Tab switching ----------
  function goToTab(name) {
    currentTab = name;
    tabButtons.forEach((btn) => {
      const active = btn.dataset.tab === name;
      btn.className = `tab-btn px-4 py-3 text-sm font-medium border-b-2 whitespace-nowrap ${
        active
          ? "border-blue-600 text-blue-600"
          : "border-transparent text-slate-500 hover:text-slate-700"
      }`;
    });
    tabPanels.forEach((panel) =>
      panel.classList.toggle("hidden", panel.dataset.panel !== name),
    );

    const idx = tabOrder.indexOf(name);
    prevBtn.classList.toggle("hidden", idx === 0);
    prevBtn.classList.toggle("inline-flex", idx > 0);

    const isLast = idx === tabOrder.length - 1;
    nextBtn.classList.toggle("hidden", isLast);
    nextBtn.classList.toggle("inline-flex", !isLast);

    submitBtn.classList.toggle("hidden", !isLast);
    submitBtn.classList.toggle("inline-flex", isLast);
  }

  tabButtons.forEach((btn) =>
    btn.addEventListener("click", () => goToTab(btn.dataset.tab)),
  );
  nextBtn.addEventListener("click", () => {
    const idx = tabOrder.indexOf(currentTab);
    if (idx < tabOrder.length - 1) goToTab(tabOrder[idx + 1]);
  });
  prevBtn.addEventListener("click", () => {
    const idx = tabOrder.indexOf(currentTab);
    if (idx > 0) goToTab(tabOrder[idx - 1]);
  });

  // ---------- Admission toggle ----------
  function syncAdmissionState() {
    const on = createAdmission.checked;
    [admissionFields, roomFields, doctorsFields, diagnosesFields].forEach(
      (el) => {
        if (!el) return;
        el.classList.toggle("opacity-50", !on);
        el.classList.toggle("pointer-events-none", !on);
      },
    );
    [roomNotice, doctorsNotice, diagnosesNotice].forEach((el) => {
      if (el) el.classList.toggle("hidden", on);
    });
  }

  createAdmission.addEventListener("change", syncAdmissionState);

  // ---------- Doctors rows ----------
  function makeDoctorRow(selectedId = "", role = "Attending", fee = "") {
    const row = document.createElement("div");
    row.className =
      "doctor-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-start p-3 rounded-lg border border-slate-200 bg-white";

    const options = DOCTORS.map(
      (d) =>
        `<option value="${d.doctor_id}" ${String(d.doctor_id) === String(selectedId) ? "selected" : ""}>${d.name}</option>`,
    ).join("");

    row.innerHTML = `
            <div class="sm:col-span-5">
                <select class="doctor-select w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select doctor —</option>
                    ${options}
                </select>
            </div>
            <div class="sm:col-span-3">
                <select class="doctor-role w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    ${["Attending", "Consulting", "Referring", "Primary"]
                      .map(
                        (r) =>
                          `<option value="${r}" ${r === role ? "selected" : ""}>${r}</option>`,
                      )
                      .join("")}
                </select>
            </div>
            <div class="sm:col-span-3">
                <input type="number" class="doctor-fee w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Fee" min="0" step="0.01" value="${fee}">
            </div>
            <div class="sm:col-span-1 flex justify-end">
                <button type="button" class="remove-row p-2 text-rose-500 hover:bg-rose-50 rounded-lg">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        `;

    const select = row.querySelector(".doctor-select");
    const feeInput = row.querySelector(".doctor-fee");
    select.addEventListener("change", () => {
      const doc = DOCTORS.find((d) => String(d.doctor_id) === select.value);
      if (doc && !feeInput.value) feeInput.value = doc.fee.toFixed(2);
    });

    row
      .querySelector(".remove-row")
      .addEventListener("click", () => row.remove());
    return row;
  }

  addDoctorRow.addEventListener("click", () =>
    doctorsList.appendChild(makeDoctorRow()),
  );

  // ---------- Diagnosis rows ----------
  function makeDiagnosisRow(
    selectedId = "",
    type = "Primary",
    diagnosedBy = "",
  ) {
    const row = document.createElement("div");
    row.className =
      "diagnosis-row grid grid-cols-1 sm:grid-cols-12 gap-2 items-start p-3 rounded-lg border border-slate-200 bg-white";

    const options = DIAGNOSES.map(
      (d) =>
        `<option value="${d.diagnosis_id}" ${String(d.diagnosis_id) === String(selectedId) ? "selected" : ""}>${d.icd_code ? "[" + d.icd_code + "] " : ""}${d.diagnosis_name}</option>`,
    ).join("");

    const docOptions = DOCTORS.map(
      (d) =>
        `<option value="${d.doctor_id}" ${String(d.doctor_id) === String(diagnosedBy) ? "selected" : ""}>${d.name}</option>`,
    ).join("");

    row.innerHTML = `
            <div class="sm:col-span-6">
                <select class="diagnosis-select w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select diagnosis —</option>
                    ${options}
                </select>
            </div>
            <div class="sm:col-span-3">
                <select class="diagnosis-type w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    ${["Primary", "Secondary", "Differential", "Comorbidity"]
                      .map(
                        (t) =>
                          `<option value="${t}" ${t === type ? "selected" : ""}>${t}</option>`,
                      )
                      .join("")}
                </select>
            </div>
            <div class="sm:col-span-2">
                <select class="diagnosed-by w-full rounded-lg border border-slate-300 px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Doctor</option>
                    ${docOptions}
                </select>
            </div>
            <div class="sm:col-span-1 flex justify-end">
                <button type="button" class="remove-row p-2 text-rose-500 hover:bg-rose-50 rounded-lg">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
        `;

    row
      .querySelector(".remove-row")
      .addEventListener("click", () => row.remove());
    return row;
  }

  addDiagnosisRow.addEventListener("click", () =>
    diagnosesList.appendChild(makeDiagnosisRow()),
  );

  // ---------- Collect form data ----------
  function collectDoctors() {
    return Array.from(doctorsList.querySelectorAll(".doctor-row"))
      .map((row) => ({
        doctor_id: row.querySelector(".doctor-select")?.value || "",
        doctor_role: row.querySelector(".doctor-role")?.value || "Attending",
        consultation_fee_charged: row.querySelector(".doctor-fee")?.value || "",
      }))
      .filter((x) => x.doctor_id);
  }

  function collectDiagnoses() {
    return Array.from(diagnosesList.querySelectorAll(".diagnosis-row"))
      .map((row) => ({
        diagnosis_id: row.querySelector(".diagnosis-select")?.value || "",
        diagnosis_type:
          row.querySelector(".diagnosis-type")?.value || "Primary",
        diagnosed_by_doctor_id: row.querySelector(".diagnosed-by")?.value || "",
      }))
      .filter((x) => x.diagnosis_id);
  }

  // ---------- Reset form ----------
  function resetForm() {
    form.reset();
    clearErrors(form);
    document.getElementById("patient_id").value = "";
    document.getElementById("admission_id").value = "";
    doctorsList.innerHTML = "";
    diagnosesList.innerHTML = "";
    createAdmission.checked = false;
    syncAdmissionState();
    goToTab("info");
  }

  // ---------- CREATE ----------
  if (openCreate) {
    openCreate.addEventListener("click", () => {
      resetForm();
      modalTitle.textContent = "New Patient";
      submitLbl.textContent = "Save Patient";
      openModal(modal);
    });
  }

  // ---------- EDIT (fetch from API) ----------
  document.querySelectorAll(".edit-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = btn.dataset.patientId;
      resetForm();
      modalTitle.textContent = "Edit Patient";
      submitLbl.textContent = "Save Changes";
      openModal(modal);

      try {
        const { data } = await axios.get(
          `${baseUrl}/api/patients/get-details.php?id=${id}`,
          { withCredentials: true },
        );

        if (!data.success) {
          showAlert(data.message || "Could not load patient.", "error");
          return;
        }

        const p = data.data;

        document.getElementById("patient_id").value = p.patient_id;
        document.getElementById("first_name").value = p.first_name || "";
        document.getElementById("last_name").value = p.last_name || "";
        document.getElementById("gender_id").value = p.gender_id || "";
        document.getElementById("birth_date").value = p.birth_date || "";
        document.getElementById("email").value = p.email || "";
        document.getElementById("contact_number").value =
          p.contact_number || "";
        document.getElementById("address").value = p.address || "";
        document.getElementById("emergency_contact").value =
          p.emergency_contact || "";
        document.getElementById("emergency_contact_number").value =
          p.emergency_contact_number || "";
        document.getElementById("medical_history").value =
          p.medical_history || "";

        if (p.admission_id) {
          document.getElementById("admission_id").value = p.admission_id;
          createAdmission.checked = true;
          document.getElementById("admission_status_id").value =
            p.admission_status_id || "1";
          document.getElementById("admission_type").value =
            p.admission_type || "Emergency";
          document.getElementById("chief_complaint").value =
            p.chief_complaint || "";
          document.getElementById("admission_notes").value =
            p.admission_notes || "";

          if (p.room_assignment) {
            document.getElementById("room_id").value =
              p.room_assignment.room_id;
          }

          (p.doctors || []).forEach((d) => {
            doctorsList.appendChild(
              makeDoctorRow(
                d.doctor_id,
                d.doctor_role,
                d.consultation_fee_charged,
              ),
            );
          });

          (p.diagnoses || []).forEach((d) => {
            diagnosesList.appendChild(
              makeDiagnosisRow(
                d.diagnosis_id,
                d.diagnosis_type,
                d.diagnosed_by_doctor_id,
              ),
            );
          });
        }

        syncAdmissionState();
      } catch (err) {
        console.error("[patients] load failed", err);
        showAlert("Could not load patient details.", "error");
      }
    });
  });

  // ---------- SUBMIT ----------
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    hideAlert();
    clearErrors(form);

    const id = document.getElementById("patient_id").value;
    const isEdit = id !== "";

    const payload = {
      first_name: document.getElementById("first_name").value.trim(),
      last_name: document.getElementById("last_name").value.trim(),
      gender_id: document.getElementById("gender_id").value,
      birth_date: document.getElementById("birth_date").value,
      email: document.getElementById("email").value.trim(),
      contact_number: document.getElementById("contact_number").value.trim(),
      address: document.getElementById("address").value.trim(),
      emergency_contact: document
        .getElementById("emergency_contact")
        .value.trim(),
      emergency_contact_number: document
        .getElementById("emergency_contact_number")
        .value.trim(),
      medical_history: document.getElementById("medical_history").value.trim(),

      create_admission: createAdmission.checked ? 1 : 0,
      admission_id: document.getElementById("admission_id").value || 0,
      admission_status_id: document.getElementById("admission_status_id").value,
      admission_type: document.getElementById("admission_type").value,
      chief_complaint: document.getElementById("chief_complaint").value.trim(),
      admission_notes: document.getElementById("admission_notes").value.trim(),

      room_id: document.getElementById("room_id").value || 0,
      doctors: collectDoctors(),
      diagnoses: collectDiagnoses(),
    };

    const url = isEdit
      ? `${baseUrl}/api/patients/update.php?id=${id}`
      : `${baseUrl}/api/patients/create.php`;

    submitBtn.disabled = true;
    submitLbl.textContent = isEdit ? "Saving…" : "Creating…";

    try {
      const { data } = await axios.post(url, payload, {
        headers: { "Content-Type": "application/json" },
        withCredentials: true,
      });

      if (data.success) {
        closeModal(modal);
        sessionStorage.setItem("patient_flash", data.message || "Saved.");
        window.location.reload();
      } else {
        if (data.errors) {
          Object.entries(data.errors).forEach(([f, m]) => setError(form, f, m));
        }
        showAlert(data.message || "Save failed.", "error");
      }
    } catch (err) {
      const res = err.response?.data;
      if (res?.errors) {
        Object.entries(res.errors).forEach(([f, m]) => setError(form, f, m));
      }
      showAlert(res?.message || "Save failed.", "error");
    } finally {
      submitBtn.disabled = false;
      submitLbl.textContent = isEdit ? "Save Changes" : "Save Patient";
    }
  });

  // ---------- ARCHIVE / REACTIVATE ----------
  document.querySelectorAll(".deactivate-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      confirmPatientName.textContent = btn.dataset.patientName;
      confirmDeactivateBtn.dataset.patientId = btn.dataset.patientId;
      openModal(confirmModal);
    });
  });

  if (confirmDeactivateBtn) {
    confirmDeactivateBtn.addEventListener("click", async () => {
      const id = confirmDeactivateBtn.dataset.patientId;
      if (!id) return;

      confirmDeactivateBtn.disabled = true;
      confirmDeactivateLbl.textContent = "Archiving…";

      try {
        const { data } = await axios.post(
          `${baseUrl}/api/patients/toggle-active.php?id=${id}`,
          {},
          {
            headers: { "Content-Type": "application/json" },
            withCredentials: true,
          },
        );

        if (data.success) {
          closeModal(confirmModal);
          sessionStorage.setItem(
            "patient_flash",
            data.message || "Patient archived.",
          );
          window.location.reload();
        } else {
          closeModal(confirmModal);
          showAlert(data.message || "Action failed.", "error");
        }
      } catch (err) {
        closeModal(confirmModal);
        showAlert(err.response?.data?.message || "Action failed.", "error");
      } finally {
        confirmDeactivateBtn.disabled = false;
        confirmDeactivateLbl.textContent = "Archive Patient";
      }
    });
  }

  document.querySelectorAll(".reactivate-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const id = btn.dataset.patientId;
      btn.disabled = true;
      try {
        const { data } = await axios.post(
          `${baseUrl}/api/patients/toggle-active.php?id=${id}`,
          {},
          {
            headers: { "Content-Type": "application/json" },
            withCredentials: true,
          },
        );
        if (data.success) {
          sessionStorage.setItem(
            "patient_flash",
            data.message || "Patient reactivated.",
          );
          window.location.reload();
        } else {
          showAlert(data.message || "Action failed.", "error");
        }
      } catch (err) {
        showAlert(err.response?.data?.message || "Action failed.", "error");
      } finally {
        btn.disabled = false;
      }
    });
  });

  // ---------- Modal close handlers ----------
  document.querySelectorAll("[data-close-modal]").forEach((el) => {
    el.addEventListener("click", () => closeModal(modal));
  });
  document.querySelectorAll("[data-close-confirm]").forEach((el) => {
    el.addEventListener("click", () => closeModal(confirmModal));
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal(modal);
      closeModal(confirmModal);
    }
  });

  // ---------- Search + filter ----------
  const searchInput = document.getElementById("filterSearch");
  const showArchived = document.getElementById("showArchived");
  const filterClear = document.getElementById("filterClear");
  const filterSummary = document.getElementById("filterSummary");
  const filteredCount = document.getElementById("filteredCount");
  const totalCount = document.getElementById("totalCount");
  const emptyState = document.getElementById("emptyState");

  const rows = Array.from(document.querySelectorAll(".patient-row"));
  if (totalCount) totalCount.textContent = rows.length;

  function applyFilters() {
    const q = (searchInput?.value || "").toLowerCase().trim();
    const showInactiveOnly = showArchived?.checked || false;

    let visible = 0;
    rows.forEach((row) => {
      const matchesSearch = q === "" || (row.dataset.search || "").includes(q);
      const isArchived = row.dataset.status === "0";
      const matchesArchiveFilter = showInactiveOnly ? isArchived : !isArchived;
      const show = matchesSearch && matchesArchiveFilter;
      row.classList.toggle("hidden", !show);
      if (show) visible++;
    });

    if (filteredCount) filteredCount.textContent = visible;
    const isFiltering = q !== "" || showInactiveOnly;
    if (filterSummary) filterSummary.classList.toggle("hidden", !isFiltering);
    if (emptyState) emptyState.classList.toggle("hidden", visible > 0);
    if (filterClear) {
      filterClear.classList.toggle("hidden", !isFiltering);
      filterClear.classList.toggle("inline-flex", isFiltering);
    }
  }

  if (searchInput) searchInput.addEventListener("input", applyFilters);
  if (showArchived) showArchived.addEventListener("change", applyFilters);
  if (filterClear) {
    filterClear.addEventListener("click", () => {
      searchInput.value = "";
      showArchived.checked = false;
      applyFilters();
    });
  }
  applyFilters();

  // ---------- Flash ----------
  const flash = sessionStorage.getItem("patient_flash");
  if (flash) {
    sessionStorage.removeItem("patient_flash");
    showAlert(flash, "success");
  }
});
