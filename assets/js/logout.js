// assets/js/logout.js

document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("logoutBtn");
  if (!btn) return; // no sign-out button on this page — nothing to do

  btn.addEventListener("click", async (e) => {
    e.preventDefault();

    const baseUrl = document.body.dataset.baseUrl || "";

    try {
      await axios.post(
        `${baseUrl}/api/logout.php`,
        {},
        {
          headers: { "Content-Type": "application/json" },
          withCredentials: true,
        },
      );
    } catch (err) {
      console.warn("[logout] request failed, redirecting anyway", err);
    }

    // Always redirect, even if the request errored
    window.location.href = `${baseUrl}/index.php`;
  });
});
