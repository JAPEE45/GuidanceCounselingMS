// Sample notification data
var notifications = window.notifications || [];

// Function to get icon based on title/type
function getStatusIcon(title) {
  if (title.includes("Pending")) return "fa-clock";
  if (title.includes("Confirmed")) return "fa-calendar-check";
  if (title.includes("Completed")) return "fa-check-circle";
  if (title.includes("Cancelled")) return "fa-times-circle";
  return "fa-bell";
}

// Function to render notifications
function renderNotifications(filter = "all") {
  const container = document.getElementById("notificationsList");
  let filteredNotifications = notifications;

  if (filter === "unread") {
    filteredNotifications = notifications.filter((n) => n.unread);
  } else if (filter === "pending") {
    // Assuming 'Pending' is in the title for pending appointments
    filteredNotifications = notifications.filter((n) => n.title.includes("Pending"));
  }

  if (filteredNotifications.length === 0) {
    container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h4>No notifications found</h4>
                        <p>You're all caught up!</p>
                    </div>
                `;
    return;
  }

  container.innerHTML = filteredNotifications
    .map(
      (notification) => `
                <div class="notification-item ${notification.unread ? "unread" : ""
        }" onclick="markAsRead(${notification.id})">
                    <div class="notification-icon">
                        <i class="fas ${getStatusIcon(notification.title)}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">
                            ${notification.title}
                        </div>
                        <div class="notification-details">
                            ${notification.message}
                        </div>
                        <div class="notification-time">
                            <i class="far fa-clock"></i> ${notification.date}
                        </div>
                    </div>
                    ${notification.unread ? '<span class="badge bg-primary rounded-pill">New</span>' : ''}
                </div>
            `
    )
    .join("");
}

// Function to mark notification as read
function markAsRead(id) {
  const notification = notifications.find((n) => n.id === id);
  if (notification && notification.unread) {

    // AJAX call to mark as read
    const formData = new FormData();
    formData.append('id', id);

    fetch('mark_notification_read.php', {
      method: 'POST',
      body: formData
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          notification.unread = false;
          renderNotifications();
        }
      });
  }
}

// Function to mark all as read
function markAllAsRead() {
  fetch('mark_notification_read.php', {
    method: 'POST',
    body: new FormData() // No ID means mark all
  })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        notifications.forEach((n) => (n.unread = false));
        renderNotifications();
        alert("All notifications marked as read!");
      }
    });
}

// Function to filter notifications
function filterNotifications(filter) {
  // Update active button
  document.querySelectorAll(".filter-btn").forEach((btn) => {
    btn.classList.remove("active");
  });
  event.target.classList.add("active");

  renderNotifications(filter);
}

// Initialize notifications on page load
document.addEventListener("DOMContentLoaded", function () {
  renderNotifications();
});
