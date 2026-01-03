/* ===== USER STATUS CHART ===== */
const userCtx = document.getElementById("userChart");

new Chart(userCtx, {
  type: "doughnut",
  data: {
    labels: ["User Aktif", "User Nonaktif"],
    datasets: [
      {
        data: [USER_AKTIF, USER_NONAKTIF],
        backgroundColor: ["#198754", "#dc3545"],
      },
    ],
  },
  options: {
    plugins: {
      legend: {
        position: "bottom",
      },
    },
  },
});

/* ===== OUTSTANDING CHART ===== */
const outCtx = document.getElementById("outstandingChart");

new Chart(outCtx, {
  type: "bar",
  data: {
    labels: ["Outstanding"],
    datasets: [
      {
        label: "Total Outstanding (Rp)",
        data: [OUTSTANDING_VALUE],
        backgroundColor: "#6f42c1",
      },
    ],
  },
  options: {
    plugins: {
      legend: {
        position: "bottom",
      },
    },
    scales: {
      y: {
        ticks: {
          callback: (value) => "Rp " + value.toLocaleString("id-ID"),
        },
      },
    },
  },
});
