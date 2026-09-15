// assets/js/doctors.js

document.addEventListener("DOMContentLoaded", () => {
  const baseUrl = document.body.dataset.baseUrl || "";

  const alertBox = document.getElementById("alert");

  const doctorModal = document.getElementById("doctorModal");
  const doctorForm = document.getElementById("doctorForm");
  const doctorModalTitle = document.getElementById("doctorModalTitle");
  const doctorSubmitBtn = document.getElementById("doctorSubmitBtn");
  const doctorSubmitLbl = document.getElementById("doctorSubmitLabel");
  const openCreateBtn = document.getElementById("openCreateBtn");
  const passwordWrap = document.getElementById("passwordFieldWrapper");

  const pwModal = document.getElementById("pwModal");
  const pwForm = document.getElementById("pwForm");
  const pwDoctorName = document.getElementById("pwDoctorName");
  const pwSubmitBtn = document.getElementById("pwSubmitBtn");
  const pwSubmitLbl = document.getElementById("pwSubmitLabel");

  const confirmModal = document.getElementById("confirmModal");
  const confirmDoctorName = document.getElementById("confirmDoctorName");
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

  // ---------- Modal close handlers ----------
  document.querySelectorAll("[data-close-modal]").forEach((el) => {
    el.addEventListener("click", () => closeModal(doctorModal));
  });
  document.querySelectorAll("[data-close-pw]").forEach((el) => {
    el.addEventListener("click", () => closeModal(pwModal));
  });
  document.querySelectorAll("[data-close-confirm]").forEach((el) => {
    el.addEventListener("click", () => closeModal(confirmModal));
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal(doctorModal);
      closeModal(pwModal);
      closeModal(confirmModal);
    }
  });

  // =========================================================
  // CREATE
  // =========================================================
  openCreateBtn.addEventListener("click", () => {
    doctorForm.reset();
    clearErrors(doctorForm);
    document.getElementById("doctor_id").value = "";
    doctorModalTitle.textContent = "New Doctor";
    doctorSubmitLbl.textContent = "Create Doctor";
    passwordWrap.classList.remove("hidden");
    document.getElementById("password").required = true;
    openModal(doctorModal);
  });

  // =========================================================
  // EDIT
  // =========================================================
  document.querySelectorAll(".edit-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const d = JSON.parse(btn.dataset.doctor);

      doctorForm.reset();
      clearErrors(doctorForm);

      doctorModalTitle.textContent = "Edit Doctor";
      doctorSubmitLbl.textContent = "Save Changes";

      document.getElementById("doctor_id").value = d.doctor_id;
      document.getElementById("first_name").value = d.first_name;
      document.getElementById("last_name").value = d.last_name;
      document.getElementById("username").value = d.username;
      document.getElementById("email").value = d.email;
      document.getElementById("contact_number").value = d.contact_number || "";
      document.getElementById("license_number").value = d.license_number;
      document.getElementById("consultation_fee").value = d.consultation_fee;

      passwordWrap.classList.add("hidden");
      document.getElementById("password").required = false;
      document.getElementById("password").value = "";

      openModal(doctorModal);
    });
  });

  // ---------- CREATE / UPDATE submit ----------
  doctorForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    hideAlert();
    clearErrors(doctorForm);

    const id = document.getElementById("doctor_id").value;
    const isEdit = id !== "";

    const payload = {
      first_name: document.getElementById("first_name").value.trim(),
      last_name: document.getElementById("last_name").value.trim(),
      username: document.getElementById("username").value.trim(),
      email: document.getElementById("email").value.trim(),
      contact_number: document.getElementById("contact_number").value.trim(),
      license_number: document.getElementById("license_number").value.trim(),
      consultation_fee: document.getElementById("consultation_fee").value,
    };
    if (!isEdit) payload.password = document.getElementById("password").value;

    const url = isEdit
      ? `${baseUrl}/api/doctors/update.php?id=${id}`
      : `${baseUrl}/api/doctors/create.php`;

    doctorSubmitBtn.disabled = true;
    doctorSubmitLbl.textContent = isEdit ? "Saving…" : "Creating…";

    try {
      const { data } = await axios.post(url, payload, {
        headers: { "Content-Type": "application/json" },
        withCredentials: true,
      });

      if (data.success) {
        closeModal(doctorModal);
        sessionStorage.setItem("doctors_flash", data.message || "Saved.");
        window.location.reload();
      } else {
        if (data.errors) {
          Object.entries(data.errors).forEach(([f, m]) =>
            setError(doctorForm, f, m),
          );
        }
        showAlert(data.message || "Save failed.", "error");
      }
    } catch (err) {
      const res = err.response?.data;
      if (res?.errors) {
        Object.entries(res.errors).forEach(([f, m]) =>
          setError(doctorForm, f, m),
        );
      }
      showAlert(res?.message || "Save failed.", "error");
    } finally {
      doctorSubmitBtn.disabled = false;
      doctorSubmitLbl.textContent = isEdit ? "Save Changes" : "Create Doctor";
    }
  });

  // =========================================================
  // CHANGE PASSWORD
  // =========================================================
  document.querySelectorAll(".pw-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      pwForm.reset();
      clearErrors(pwForm);
      pwDoctorName.textContent = btn.dataset.doctorName;
      pwForm.dataset.doctorId = btn.dataset.doctorId;
      openModal(pwModal);
    });
  });

  pwForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    hideAlert();
    clearErrors(pwForm);

    const doctorId = pwForm.dataset.doctorId;
    const password = document.getElementById("pw_password").value;
    const confirm = document.getElementById("pw_password_confirm").value;

    pwSubmitBtn.disabled = true;
    pwSubmitLbl.textContent = "Updating…";

    try {
      const { data } = await axios.post(
        `${baseUrl}/api/doctors/change-password.php?id=${doctorId}`,
        { password, password_confirm: confirm },
        {
          headers: { "Content-Type": "application/json" },
          withCredentials: true,
        },
      );

      if (data.success) {
        closeModal(pwModal);
        sessionStorage.setItem(
          "doctors_flash",
          data.message || "Password updated.",
        );
        window.location.reload();
      } else {
        if (data.errors) {
          Object.entries(data.errors).forEach(([f, m]) =>
            setError(pwForm, f, m),
          );
        }
        showAlert(data.message || "Update failed.", "error");
      }
    } catch (err) {
      const res = err.response?.data;
      if (res?.errors) {
        Object.entries(res.errors).forEach(([f, m]) => setError(pwForm, f, m));
      }
      showAlert(res?.message || "Update failed.", "error");
    } finally {
      pwSubmitBtn.disabled = false;
      pwSubmitLbl.textContent = "Update Password";
    }
  });

  // =========================================================
  // DEACTIVATE
  // =========================================================
  document.querySelectorAll(".deactivate-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      confirmDoctorName.textContent = btn.dataset.doctorName;
      confirmDeactivateBtn.dataset.doctorId = btn.dataset.doctorId;
      openModal(confirmModal);
    });
  });

  confirmDeactivateBtn.addEventListener("click", async () => {
    const doctorId = confirmDeactivateBtn.dataset.doctorId;

    confirmDeactivateBtn.disabled = true;
    confirmDeactivateLbl.textContent = "Archiving…";

    try {
      const { data } = await axios.post(
        `${baseUrl}/api/doctors/toggle-active.php?id=${doctorId}`,
        {},
        {
          headers: { "Content-Type": "application/json" },
          withCredentials: true,
        },
      );

      if (data.success) {
        closeModal(confirmModal);
        sessionStorage.setItem(
          "doctors_flash",
          data.message || "Doctor archived.",
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
      confirmDeactivateLbl.textContent = "Yes, archive";
    }
  });

  // =========================================================
  // REACTIVATE
  // =========================================================
  document.querySelectorAll(".reactivate-btn").forEach((btn) => {
    btn.addEventListener("click", async () => {
      const doctorId = btn.dataset.doctorId;
      btn.disabled = true;

      try {
        const { data } = await axios.post(
          `${baseUrl}/api/doctors/toggle-active.php?id=${doctorId}`,
          {},
          {
            headers: { "Content-Type": "application/json" },
            withCredentials: true,
          },
        );

        if (data.success) {
          sessionStorage.setItem(
            "doctors_flash",
            data.message || "Doctor reactivated.",
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

  // =========================================================
  // SEARCH + FILTER
  // =========================================================
  const searchInput = document.getElementById("filterSearch");
  const statusFilter = document.getElementById("filterStatus");
  const filterClear = document.getElementById("filterClear");
  const filterSummary = document.getElementById("filterSummary");
  const filteredCount = document.getElementById("filteredCount");
  const totalCount = document.getElementById("totalCount");
  const emptyState = document.getElementById("emptyState");

  const rows = Array.from(document.querySelectorAll(".doctor-row"));
  if (totalCount) totalCount.textContent = rows.length;

  function applyFilters() {
    const q = (searchInput?.value || "").toLowerCase().trim();
    const status = statusFilter?.value || "";

    let visible = 0;

    rows.forEach((row) => {
      const matchesSearch = q === "" || (row.dataset.search || "").includes(q);
      const matchesStatus = status === "" || row.dataset.status === status;

      const show = matchesSearch && matchesStatus;
      row.classList.toggle("hidden", !show);
      if (show) visible++;
    });

    if (filteredCount) filteredCount.textContent = visible;

    const isFiltering = q !== "" || status !== "";
    if (filterSummary) filterSummary.classList.toggle("hidden", !isFiltering);
    if (emptyState) emptyState.classList.toggle("hidden", visible > 0);
  }

  if (searchInput) searchInput.addEventListener("input", applyFilters);
  if (statusFilter) statusFilter.addEventListener("change", applyFilters);
  if (filterClear) {
    filterClear.addEventListener("click", () => {
      searchInput.value = "";
      statusFilter.value = "";
      applyFilters();
    });
  }

  // =========================================================
  // Flash message
  // =========================================================
  const flash = sessionStorage.getItem("doctors_flash");
  if (flash) {
    sessionStorage.removeItem("doctors_flash");
    showAlert(flash, "success");
  }
});
