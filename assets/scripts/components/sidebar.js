// Toggle Sidebar for Mobile
function toggleSidebar() {
  document.getElementById("sidebar").classList.toggle("active");
}

// Close sidebar when clicking outside on mobile
document.addEventListener("click", function (e) {
  const sidebar = document.getElementById("sidebar");
  const toggle = document.querySelector(".sidebar-toggle");

  if (window.innerWidth <= 768) {
    if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
      sidebar.classList.remove("active");
    }
  }
});
