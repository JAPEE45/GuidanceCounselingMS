// Sample notification data
const notifications = [
  {
    id: 1,
    date: "Monday, August 29 at 09:00 AM",
    counselor: "Dr. Maria Santos",
    type: "Academic",
    status: "Pending",
    unread: true,
  },
  {
    id: 2,
    date: "Tuesday, August 30 at 10:30 AM",
    counselor: "Dr. Juan Dela Cruz",
    type: "Personal",
    status: "Confirmed",
    unread: true,
  },
  {
    id: 3,
    date: "Wednesday, August 31 at 02:00 PM",
    counselor: "Dr. Maria Santos",
    type: "Career",
    status: "Completed",
    unread: false,
  },
  {
    id: 4,
    date: "Thursday, September 1 at 11:00 AM",
    counselor: "Dr. Ana Reyes",
    type: "Academic",
    status: "Pending",
    unread: true,
  },
  {
    id: 5,
    date: "Friday, September 2 at 03:30 PM",
    counselor: "Dr. Juan Dela Cruz",
    type: "Personal",
    status: "Cancelled",
    unread: false,
  },
  {
    id: 6,
    date: "Monday, September 5 at 09:00 AM",
    counselor: "Dr. Maria Santos",
    type: "Career",
    status: "Confirmed",
    unread: true,
  },
];

// Function to get icon based on status
function getStatusIcon(status) {
  const icons = {
    Pending: "fa-clock",
    Confirmed: "fa-calendar-check",
    Completed: "fa-check-circle",
    Cancelled: "fa-times-circle",
  };
  return icons[status] || "fa-bell";
}

// Function to render notifications
function renderNotifications(filter = "all") {
  const container = document.getElementById("notificationsList");
  let filteredNotifications = notifications;

  if (filter === "unread") {
    filteredNotifications = notifications.filter((n) => n.unread);
  } else if (filter === "pending") {
    filteredNotifications = notifications.filter((n) => n.status === "Pending");
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
                <div class="notification-item ${
                  notification.unread ? "unread" : ""
                }" onclick="markAsRead(${notification.id})">
                    <div class="notification-icon ${notification.status.toLowerCase()}">
                        <i class="fas ${getStatusIcon(
                          notification.status
                        )}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">
                            ${notification.date} - ${notification.counselor} (${
        notification.type
      })
                        </div>
                        <div class="notification-details">
                            Appointment scheduled with ${notification.counselor}
                        </div>
                        <div class="notification-time">
                            <i class="far fa-clock"></i> ${notification.date}
                        </div>
                    </div>
                    <span class="notification-status status-${notification.status.toLowerCase()}">
                        ${notification.status}
                    </span>
                </div>
            `
    )
    .join("");
}

// Function to mark notification as read
function markAsRead(id) {
  const notification = notifications.find((n) => n.id === id);
  if (notification) {
    notification.unread = false;
    renderNotifications();
  }
}

// Function to mark all as read
function markAllAsRead() {
  notifications.forEach((n) => (n.unread = false));
  renderNotifications();
  alert("All notifications marked as read!");
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
