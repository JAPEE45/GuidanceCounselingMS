// Sample requests data - Change hasRequests to false to see "No requests" message
const hasRequests = true;
const requests = [
  {
    title: "New Appointment Request",
    description:
      "John Doe requested an appointment for career counseling - 2 hours ago",
  },
  {
    title: "Student Registration",
    description:
      "Jane Smith completed registration and awaiting approval - 3 hours ago",
  },
  {
    title: "Counselor Availability Update",
    description: "Dr. Martinez updated schedule availability - 5 hours ago",
  },
];

// Populate requests
function populateRequests() {
  const container = document.getElementById("requestsContainer");

  if (!hasRequests || requests.length === 0) {
    container.innerHTML =
      '<div class="no-requests">No requests as of today</div>';
  } else {
    container.innerHTML = requests
      .map(
        (request) => `
                    <div class="request-item">
                        <h6>${request.title}</h6>
                        <p>${request.description}</p>
                    </div>
                `
      )
      .join("");
  }
}

// Initialize
populateRequests();
