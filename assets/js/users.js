// assets/js/users.js

document.addEventListener("DOMContentLoaded", () => {
  const baseUrl = document.body.dataset.baseUrl || "";

  const alertBox = document.getElementById("alert");

  const userModal = document.getElementById("userModal");
  const userForm = document.getElementById("userForm");
  const userModalTitle = document.getElementById("userModalTitle");
  const userSubmitBtn = document.getElementById("userSubmitBtn");
  const userSubmitLbl = document.getElementById("userSubmitLabel");
  const openCreateBtn = document.getElementById("openCreateBtn");
  const passwordWrap = document.getElementById("passwordFieldWrapper");

  const pwModal = document.getElementById("pwModal");
  const pwForm = document.getElementById("pwForm");
  const pwUserName = document.getElementById("pwUserName");
  const pwSubmitBtn = document.getElementById("pwSubmitBtn");
  const pwSubmitLbl = document.getElementById("pwSubmitLabel");

  const confirmModal = document.getElementById("confirmModal");
  const confirmUserName = document.getElementById("confirmUserName");
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
    el.addEventListener("click", () => closeModal(userModal));
  });
  document.querySelectorAll("[data-close-pw]").forEach((el) => {
    el.addEventListener("click", () => closeModal(pwModal));
  });
  document.querySelectorAll("[data-close-confirm]").forEach((el) => {
    el.addEventListener("click", () => closeModal(confirmModal));
  });
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeModal(userModal);
      closeModal(pwModal);
      closeModal(confirmModal);
    }
  });

  // =========================================================
  // CREATE
  // =========================================================
  openCreateBtn.addEventListener("click", () => {
    userForm.reset();
    clearErrors(userForm);
    document.getElementById("user_id").value = "";
    userModalTitle.textContent = "New User";
    userSubmitLbl.textContent = "Create User";
    passwordWrap.classList.remove("hidden");
    document.getElementById("password").required = true;
    openModal(userModal);
  });

  // =========================================================
  // EDIT
  // =========================================================
  document.querySelectorAll(".edit-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const u = JSON.parse(btn.dataset.user);

      userForm.reset();
      clearErrors(userForm);

      userModalTitle.textContent = "Edit User";
      userSubmitLbl.textContent = "Save Changes";

      document.getElementById("user_id").value = u.user_id;
      document.getElementById("first_name").value = u.first_name;
      document.getElementById("last_name").value = u.last_name;
      document.getElementById("username").value = u.username;
      document.getElementById("email").value = u.email;
      document.getElementById("contact_number").value = u.contact_number || "";
      document.getElementById("role_id").value = u.role_id;

      passwordWrap.classList.add("hidden");
      document.getElementById("password").required = false;
      document.getElementById("password").value = "";

      openModal(userModal);
    });
  });

  // ---------- CREATE / UPDATE submit ----------
  userForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    hideAlert();
    clearErrors(userForm);

    const id = document.getElementById("user_id").value;
    const isEdit = id !== "";

    const payload = {
      first_name: document.getElementById("first_name").value.trim(),
      last_name: document.getElementById("last_name").value.trim(),
      username: document.getElementById("username").value.trim(),
      email: document.getElementById("email").value.trim(),
      contact_number: document.getElementById("contact_number").value.trim(),
      role_id: parseInt(document.getElementById("role_id").value, 10) || 0,
    };
    if (!isEdit) payload.password = document.getElementById("password").value;

    const url = isEdit
      ? `${baseUrl}/api/users/update.php?id=${id}`
      : `${baseUrl}/api/users/create.php`;

    userSubmitBtn.disabled = true;
    userSubmitLbl.textContent = isEdit ? "Saving…" : "Creating…";

    try {
      const { data } = await axios.post(url, payload, {
        headers: { "Content-Type": "application/json" },
        withCredentials: true,
      });

      if (data.success) {
        closeModal(userModal);
        sessionStorage.setItem("users_flash", data.message || "Saved.");
        window.location.reload();
      } else {
        if (data.errors) {
          Object.entries(data.errors).forEach(([f, m]) =>
            setError(userForm, f, m),
          );
        }
        showAlert(data.message || "Save failed.", "error");
      }
    } catch (err) {
      const res = err.response?.data;
      if (res?.errors) {
        Object.entries(res.errors).forEach(([f, m]) =>
          setError(userForm, f, m),
        );
      }
      showAlert(res?.message || "Save failed.", "error");
    } finally {
      userSubmitBtn.disabled = false;
      userSubmitLbl.textContent = isEdit ? "Save Changes" : "Create User";
    }
  });

  // =========================================================
  // CHANGE PASSWORD
  // =========================================================
  document.querySelectorAll(".pw-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      pwForm.reset();
      clearErrors(pwForm);

      document.getElementById("pw_password").value = "";
      document.getElementById("pw_password_confirm").value = "";

      pwUserName.textContent = btn.dataset.userName;
      pwForm.dataset.userId = btn.dataset.userId;

      openModal(pwModal);
    });
  });

  pwForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    hideAlert();
    clearErrors(pwForm);

    const userId = pwForm.dataset.userId;
    const password = document.getElementById("pw_password").value;
    const confirm = document.getElementById("pw_password_confirm").value;

    pwSubmitBtn.disabled = true;
    pwSubmitLbl.textContent = "Updating…";

    try {
      const { data } = await axios.post(
        `${baseUrl}/api/users/change-password.php?id=${userId}`,
        { password, password_confirm: confirm },
        {
          headers: { "Content-Type": "application/json" },
          withCredentials: true,
        },
      );

      if (data.success) {
        closeModal(pwModal);
        sessionStorage.setItem(
          "users_flash",
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
  // DEACTIVATE (soft delete)
  // =========================================================
  document.querySelectorAll(".deactivate-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      confirmUserName.textContent = btn.dataset.userName;
      confirmDeactivateBtn.dataset.userId = btn.dataset.userId;
      openModal(confirmModal);
    });
  });

  confirmDeactivateBtn.addEventListener("click", async () => {
    const userId = confirmDeactivateBtn.dataset.userId;

    confirmDeactivateBtn.disabled = true;
    confirmDeactivateLbl.textContent = "Archiving…";

    try {
      const { data } = await axios.post(
        `${baseUrl}/api/users/toggle-active.php?id=${userId}`,
        {},
        {
          headers: { "Content-Type": "application/json" },
          withCredentials: true,
        },
      );

      if (data.success) {
        closeModal(confirmModal);
        sessionStorage.setItem("users_flash", data.message || "User archived.");
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
      const userId = btn.dataset.userId;
      btn.disabled = true;

      try {
        const { data } = await axios.post(
          `${baseUrl}/api/users/toggle-active.php?id=${userId}`,
          {},
          {
            headers: { "Content-Type": "application/json" },
            withCredentials: true,
          },
        );

        if (data.success) {
          sessionStorage.setItem(
            "users_flash",
            data.message || "User reactivated.",
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
  // Flash message after reload
  // =========================================================
  const flash = sessionStorage.getItem("users_flash");
  if (flash) {
    sessionStorage.removeItem("users_flash");
    showAlert(flash, "success");
  }
});
