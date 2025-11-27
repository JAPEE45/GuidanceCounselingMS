<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'counselor') {
    header("Location: ../../index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT notification_id, title, message, is_read, created_at 
        FROM notifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $notifications_db = $stmt->fetchAll();

    $notifications = [];
    foreach ($notifications_db as $n) {
        $notifications[] = [
            'id' => $n['notification_id'],
            'title' => $n['title'],
            'message' => $n['message'],
            'unread' => !$n['is_read'],
            'date' => date('F d, Y h:i A', strtotime($n['created_at']))
        ];
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notifications - Guidance Counseling System</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />

    <link rel="stylesheet" href="../../assets/styles/components/sidebar.css" />
    <style>
        .main-content {
            margin-left: 250px;
            padding: 2rem;
            transition: all 0.3s ease;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .notifications-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            padding: 1.5rem;
        }
        .notification-item {
            display: flex;
            align-items: flex-start;
            padding: 1rem;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.2s;
        }
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        .notification-item.unread {
            background-color: #e8f4ff;
        }
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: #495057;
        }
        .notification-content {
            flex: 1;
        }
        .notification-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
        }
        .notification-time {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }
    </style>
  </head>
  <body>
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <h4><i class="fas fa-graduation-cap"></i> Guidance Counseling</h4>
      </div>
      <ul class="sidebar-menu">
        <li>
          <a href="./dashboard.php" class=""
            ><i class="fas fa-home"></i> Dashboard</a
          >
        </li>
        <li>
          <a href="./my-schedule.php" class=""
            ><i class="fas fa-calendar-alt"></i> My Schedule</a
          >
        </li>
        <li>
          <a href="./session-management.php"
            ><i class="fas fa-clipboard-list"></i> Session Management</a
          >
        </li>
        <li>
          <a href="./student-record.php" class="notif-referrals"
            ><i class="fas fa-user-graduate"></i> Student Record</a
          >
        </li>
        <li>
          <a href="#" class="active"><i class="fas fa-bell"></i> Notifications</a>
        </li>
        <li>
          <a href="./profile.php"><i class="fa-solid fa-user-circle"></i> Profile</a>
        </li>
        <li>
          <a href="../../logout.php"
            ><i class="fas fa-sign-out-alt"></i> Logout</a
          >
        </li>
      </ul>
    </aside>

    <button class="sidebar-toggle" onclick="toggleSidebar()">
      <i class="fas fa-bars"></i>
    </button>

    <div class="main-content">
      <div class="page-header">
        <h2><i class="fas fa-bell"></i> Notifications</h2>
        <button class="btn btn-primary" onclick="markAllAsRead()">
            <i class="fas fa-check-double me-2"></i> Mark All as Read
        </button>
      </div>

      <div class="notifications-container">
        <div id="notificationsList">
          <!-- Notifications will be loaded here -->
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/scripts/components/sidebar.js"></script>
    <script>
        var notifications = <?php echo json_encode($notifications); ?>;

        function getStatusIcon(title) {
            if (title.includes("Request")) return "fa-user-plus";
            if (title.includes("Appointment")) return "fa-calendar";
            return "fa-bell";
        }

        function renderNotifications() {
            const container = document.getElementById("notificationsList");
            
            if (notifications.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h4>No notifications found</h4>
                    </div>
                `;
                return;
            }

            container.innerHTML = notifications.map(n => `
                <div class="notification-item ${n.unread ? 'unread' : ''}" onclick="markAsRead(${n.id})">
                    <div class="notification-icon">
                        <i class="fas ${getStatusIcon(n.title)}"></i>
                    </div>
                    <div class="notification-content">
                        <div class="notification-title">${n.title}</div>
                        <div class="text-muted">${n.message}</div>
                        <div class="notification-time">
                            <i class="far fa-clock me-1"></i> ${n.date}
                        </div>
                    </div>
                    ${n.unread ? '<span class="badge bg-primary rounded-pill">New</span>' : ''}
                </div>
            `).join("");
        }

        function markAsRead(id) {
            const n = notifications.find(n => n.id === id);
            if (n && n.unread) {
                const formData = new FormData();
                formData.append('id', id);

                fetch('mark_notification_read.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        n.unread = false;
                        renderNotifications();
                    }
                });
            }
        }

        function markAllAsRead() {
            fetch('mark_notification_read.php', {
                method: 'POST',
                body: new FormData()
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    notifications.forEach(n => n.unread = false);
                    renderNotifications();
                    alert("All notifications marked as read!");
                }
            });
        }

        renderNotifications();
    </script>
  </body>
</html>
