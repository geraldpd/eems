// Set new default font family and font color to mimic Bootstrap's default styling
(Chart.defaults.global.defaultFontFamily = "Nunito"),
    '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
Chart.defaults.global.defaultFontColor = "#858796";

// Calculate the sum of all data values
const totalCategory = config.categoryPie.reduce(
    (sum, item) => sum + item.data,
    0
);

// Pie Chart Example
var ctx = document.getElementById("categoryChart");
var myPieChart = new Chart(ctx, {
    type: "doughnut",
    data: {
        labels: config.categoryPie.map((obj) => obj.label),
        datasets: [
            {
                data: config.categoryPie.map((obj) => obj.data),
                backgroundColor: config.categoryPie.map((obj) => obj.color),
                hoverBackgroundColor: config.categoryPie.map(
                    (obj) => obj.color
                ),
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            },
        ],
    },
    options: {
        maintainAspectRatio: true,
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
            display: false,
        },
        cutoutPercentage: 80,
    },
});

var ctx = document.getElementById("typeChart");
var myPieChart = new Chart(ctx, {
    type: "doughnut",
    data: {
        labels: config.typePie.map((obj) => obj.label),
        datasets: [
            {
                data: config.typePie.map((obj) => obj.data),
                backgroundColor: config.typePie.map((obj) => obj.color),
                hoverBackgroundColor: config.typePie.map((obj) => obj.color),
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            },
        ],
    },
    options: {
        maintainAspectRatio: true,
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
            display: false,
        },
        cutoutPercentage: 80,
    },
});
