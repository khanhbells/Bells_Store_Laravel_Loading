(function ($) {
    "use strict";
    var HT = {};

    HT.createChart = (label, data) => {
        let canvas = document.getElementById('barChart')
        let ctx = canvas.getContext('2d')
        if (window.myBarChart) {
            window.myBarChart.destroy()
        }
        let chartData = {
            labels: label,
            datasets: [
                {
                    label: "Doanh thu",
                    backgroundColor: "rgba(26,179,148,0.5)",
                    borderColor: "rgba(26,179,148,0.7)",
                    pointBackgroundColor: "rgba(26,179,148,1)",
                    pointBorderColor: "#fff",
                    data: data
                }
            ]
        }
        let chartOption = {
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, chartData) {
                        let value = tooltipItem.yLabel; // Sử dụng yLabel để lấy dữ liệu
                        value = value.toString();
                        value = value.split(/(?=(?:...)*$)/); // Tách chuỗi thành nhóm 3 chữ số
                        value = value.join('.'); // Nối lại các nhóm bằng dấu chấm
                        return 'Doanh thu: ' + value;
                    }
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        userCallback: function (value, index, values) {
                            // Convert giá trị thành chuỗi và tách chuỗi thành nhóm 3 chữ số
                            value = value.toString();
                            value = value.split(/(?=(?:...)*$)/); // Tách thành nhóm 3 chữ số
                            value = value.join('.'); // Nối lại các nhóm bằng dấu chấm
                            return value;
                        }
                    }
                }],
                xAxes: [{
                    ticks: {
                    }
                }]
            }
        }
        window.myBarChart = new Chart(ctx, { type: 'bar', data: chartData, options: chartOption });
    }

    HT.changeChart = () => {
        $(document).on('click', '.chartButton', function (e) {
            e.preventDefault()
            let button = $(this)
            let chartType = $(this).attr('data-chart')

            $('.chartButton').removeClass('active')
            button.addClass('active')

            HT.callChart(chartType)

        })
    }

    HT.callChart = (chartType) => {
        $.ajax({
            type: 'GET',
            url: 'ajax/order/chart',
            data: {
                chartType: chartType
            },
            dataType: 'json',
            success: function (res) {
                HT.createChart(res.label, res.data)
            }
        });
    }

    $(document).ready(function () {
        HT.createChart(label, data)
        HT.changeChart();
    });

})(jQuery);