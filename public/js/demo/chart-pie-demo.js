// Set new default font family and font color to mimic Bootstrap's default styling
(Chart.defaults.global.defaultFontFamily = "Nunito"),
    '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = "#858796";

// Fetch data from Laravel API
fetch("/transactions/status-chart")
    .then((response) => response.json())
    .then((data) => {
        let labels = data.map((item) => item.status); // Status: pending, success, failed
        let values = data.map((item) => item.total); // Jumlah transaksi tiap status

        // Warna berdasarkan status
        let statusColors = {
            pending: "#f6c23e", // Kuning untuk pending
            success: "#1cc88a", // Hijau untuk success
            failed: "#e74a3b", // Merah untuk failed
        };

        let backgroundColors = labels.map(
            (status) => statusColors[status] || "#858796"
        ); // Default abu-abu jika tidak cocok

        // Pie Chart
        var ctx = document.getElementById("myPieChart");
        var myPieChart = new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: labels,
                datasets: [
                    {
                        data: values,
                        backgroundColor: backgroundColors,
                        hoverBackgroundColor: backgroundColors.map(
                            (color) => color
                        ), // Warna tetap sama saat hover
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    },
                ],
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: "#dddfeb",
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: "bottom",
                },
                cutoutPercentage: 80,
            },
        });
    })
    .catch((error) => console.error("Error fetching data:", error));
