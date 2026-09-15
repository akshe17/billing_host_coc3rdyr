// assets/js/master/room.js

document.addEventListener("DOMContentLoaded", () => {
  const baseUrl = document.body.dataset.baseUrl || "";
  const alertBox = document.getElementById("alert");

  // Modals / forms
  const modal = document.getElementById("roomModal");
  const form = document.getElementById("roomForm");
  const modalTitle = document.getElementById("roomModalTitle");
  const submitBtn = document.getElementById("roomSubmitBtn");
  const submitLbl = document.getElementById("roomSubmitLabel");
  const openCreate = document.getElementById("openCreateBtn");

  // Delete confirm modal
  const deleteModal = document.getElementById("deleteModal");
  const deleteRoomNumber = document.getElementById("deleteRoomNumber");
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
  const confirmDeleteLbl = document.getElementById("confirmDeleteLabel");

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
    el.addEventListener("click", () => closeModal(modal));
  });
  document.querySelectorAll("[data-close-delete]").forEach((el) => {
    el.addEventListener("click", () => closeModal(deleteModal));
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal(modal);
      closeModal(deleteModal);
    }
  });

  // =========================================================
  // CREATE
  // =========================================================
  if (openCreate) {
    openCreate.addEventListener("click", () => {
      form.reset();
      clearErrors(form);
      document.getElementById("room_id").value = "";
      modalTitle.textContent = "New Room";
      submitLbl.textContent = "Create Room";
      openModal(modal);
    });
  }

  // =========================================================
  // EDIT
  // =========================================================
  document.querySelectorAll(".edit-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const r = JSON.parse(btn.dataset.room);

      form.reset();
      clearErrors(form);

      modalTitle.textContent = "Edit Room";
      submitLbl.textContent = "Save Changes";

      document.getElementById("room_id").value = r.room_id;
      document.getElementById("room_number").value = r.room_number;
      document.getElementById("room_type_id").value = r.room_type_id;
      document.getElementById("status_id").value = r.status_id;
      document.getElementById("floor_level").value = r.floor_level ?? "";
      document.getElementById("building").value = r.building ?? "";

      openModal(modal);
    });
  });

  // ---------- CREATE / UPDATE submit ----------
  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    hideAlert();
    clearErrors(form);

    const id = document.getElementById("room_id").value;
    const isEdit = id !== "";

    const payload = {
      room_type_id: document.getElementById("room_type_id").value,
      status_id: document.getElementById("status_id").value,
      room_number: document.getElementById("room_number").value.trim(),
      floor_level: document.getElementById("floor_level").value,
      building: document.getElementById("building").value.trim(),
    };

    const url = isEdit
      ? `${baseUrl}/api/rooms/update.php?id=${id}`
      : `${baseUrl}/api/rooms/create.php`;

    submitBtn.disabled = true;
    submitLbl.textContent = isEdit ? "Saving…" : "Creating…";

    try {
      const { data } = await axios.post(url, payload, {
        headers: { "Content-Type": "application/json" },
        withCredentials: true,
      });

      if (data.success) {
        closeModal(modal);
        sessionStorage.setItem("room_flash", data.message || "Saved.");
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
      submitLbl.textContent = isEdit ? "Save Changes" : "Create Room";
    }
  });

  // =========================================================
  // DELETE
  // =========================================================
  let pendingDeleteId = null;

  document.querySelectorAll(".delete-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      pendingDeleteId = btn.getAttribute("data-room-id");
      deleteRoomNumber.textContent =
        btn.getAttribute("data-room-number") || "this room";
      openModal(deleteModal);
    });
  });

  if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener("click", async () => {
      if (!pendingDeleteId) return;

      confirmDeleteBtn.disabled = true;
      confirmDeleteLbl.textContent = "Deleting…";

      try {
        const { data } = await axios.post(
          `${baseUrl}/api/rooms/delete.php?id=${pendingDeleteId}`,
          {},
          {
            headers: { "Content-Type": "application/json" },
            withCredentials: true,
          },
        );

        if (data.success) {
          closeModal(deleteModal);
          sessionStorage.setItem("room_flash", data.message || "Room deleted.");
          window.location.reload();
        } else {
          closeModal(deleteModal);
          showAlert(data.message || "Delete failed.", "error");
        }
      } catch (err) {
        closeModal(deleteModal);
        showAlert(err.response?.data?.message || "Delete failed.", "error");
      } finally {
        confirmDeleteBtn.disabled = false;
        confirmDeleteLbl.textContent = "Yes, delete";
        pendingDeleteId = null;
      }
    });
  }

  // =========================================================
  // SEARCH
  // =========================================================
  const searchInput = document.getElementById("filterSearch");
  const filterClear = document.getElementById("filterClear");
  const filterSummary = document.getElementById("filterSummary");
  const filteredCount = document.getElementById("filteredCount");
  const totalCount = document.getElementById("totalCount");
  const emptyState = document.getElementById("emptyState");

  const rows = Array.from(document.querySelectorAll(".room-row"));
  if (totalCount) totalCount.textContent = rows.length;

  function applyFilters() {
    const q = (searchInput?.value || "").toLowerCase().trim();
    let visible = 0;

    rows.forEach((row) => {
      const match = q === "" || (row.dataset.search || "").includes(q);
      row.classList.toggle("hidden", !match);
      if (match) visible++;
    });

    if (filteredCount) filteredCount.textContent = visible;

    const isFiltering = q !== "";
    if (filterSummary) filterSummary.classList.toggle("hidden", !isFiltering);
    if (emptyState) emptyState.classList.toggle("hidden", visible > 0);

    if (filterClear) {
      filterClear.classList.toggle("hidden", !isFiltering);
      filterClear.classList.toggle("inline-flex", isFiltering);
    }
  }

  if (searchInput) searchInput.addEventListener("input", applyFilters);
  if (filterClear) {
    filterClear.addEventListener("click", () => {
      searchInput.value = "";
      applyFilters();
    });
  }

  applyFilters();

  // =========================================================
  // Flash message
  // =========================================================
  const flash = sessionStorage.getItem("room_flash");
  if (flash) {
    sessionStorage.removeItem("room_flash");
    showAlert(flash, "success");
  }
});
