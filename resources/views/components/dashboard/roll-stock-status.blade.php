<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <canvas id="rollStockStatus"></canvas>

<script>
  $(document).ready(function () {
    getTheRollStatus(); // Initial fetch
    setInterval(getTheRollStatus, 60000); // Refresh every minute
  });

  function getTheRollStatus() {
    $.ajax({
      url: "{{route('dashboard.roll.status')}}",
      type: "get",
      success: function (response) {
        if (response?.status && response?.data) {
          const items = response.data;
          console.log(items);

          // Labels with details
          const labels = items.map(item => `Size: ${item.size}, Color: ${item.roll_color}, GSM: ${item.gsm}, Q: ${item.quality}`);

          // Extracting values
          const stockValues = items.map(item => parseFloat(item.total_net_weight) || 0);
          const inTransitValues = items.map(item => parseFloat(item.transit_total_net_weight) || 0);
          const minLimits = items.map(item => parseFloat(item.min_limit) || 0);

          // Determine dynamic colors based on stock levels
          const barColors = stockValues.map((stock, index) => {
            const minLimit = minLimits[index];
            if (stock < minLimit) return 'rgb(235, 17, 17)'; // Red - Below limit
            else if (stock < (minLimit + minLimit / 2)) return 'rgb(255, 160, 0)'; // Orange - Near limit
            return 'rgb(47, 222, 61)'; // Green - Above limit
          });

          // Chart data
          const data = {
            labels: labels,
            datasets: [
              {
                label: "Stock",
                data: stockValues,
                backgroundColor: barColors, // Dynamic colors
                borderColor: barColors.map(color => color.replace('0.8', '1')), 
                borderWidth: 1
              },
              {
                label: "In Transit",
                data: inTransitValues,
                backgroundColor: "rgba(54, 162, 235, 0.7)", // Blue
                borderColor: "rgba(54, 162, 235, 1)",
                borderWidth: 1
              }
            ]
          };

          const config = {
            type: 'bar',
            data: data,
            options: {
              responsive: true,
              scales: {
                x: { stacked: false },
                y: { beginAtZero: true, title: { display: true, text: 'Quantity (kg)' } }
              },
              plugins: {
                legend: { display: true },
                tooltip: {
                  callbacks: {
                    label: function (tooltipItem) {
                      const index = tooltipItem.dataIndex;
                      const minLimit = minLimits[index];
                      const value = tooltipItem.raw;
                      return [
                        `${tooltipItem.dataset.label}: ${value}`,
                        `Min Limit: ${minLimit}`,
                        value < minLimit ? 'Status: Below Limit' : 
                        (value < (minLimit + minLimit / 2) ? "Status: Near Limit" : 'Status: Above Limit')
                      ];
                    }
                  }
                }
              }
            }
          };

          // Update or create the chart
          if (window.rollStockBar) {
            window.rollStockBar.data = data;
            window.rollStockBar.update();
          } else {
            window.rollStockBar = new Chart(
              document.getElementById('rollStockStatus'),
              config
            );
          }
        }
      }
    });
  }
</script>
