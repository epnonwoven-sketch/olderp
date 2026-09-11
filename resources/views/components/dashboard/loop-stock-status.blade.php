<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div>
  <canvas id="looStockStatus" height="300"></canvas>
</div>

<script>
  $(document).ready(function() {
    // Initial call to fetch data
    getTheLoopStatus();

    // Set interval to update chart every 1 minutes (60000 milliseconds)
    setInterval(getTheLoopStatus, 60000); // 1 minutes = 60000ms
  });

  function getTheLoopStatus() {
    $.ajax({
      url: "{{route('dashboard.loop.status')}}",
      type: "get",
      success: function(response) {
        if (response?.status && response?.data) {
          const items = response.data; // Directly access data
          console.log(items);
          // Extract values for the chart
          const values = items.map(item => item.balance);
          const labels = items.map(item => item.loop_color);
          const minLimits = items.map(item => item.min_limit);

          // Determine bar colors based on the condition
          const barColors = values.map((value, index) => {
            const minLimit = parseFloat(minLimits[index]); // Get the specific min limit for each value
            value = parseFloat(value);
            if (value < minLimit) {
              return 'rgb(235, 17, 17)'; // Red for danger
            } else if (value < (minLimit + (minLimit / 2))) {
              return 'rgb(255, 160, 0)'; // Yellow for warning
            } else {
              return 'rgb(47, 222, 61)'; // Green for success
            }
          });

          // Chart configuration
          const data = {
            labels: labels,
            datasets: [{
              label: 'Loop Stock',
              data: values,
              backgroundColor: barColors, // Dynamic bar colors
              borderColor: barColors.map(color => color.replace('0.8', '1')), // Border colors matching the bar
              borderWidth: 1,
            }]
          };

          const config = {
            type: 'bar',
            data: data,
            options: {
              scales: {
                y: {
                  beginAtZero: true,
                  title: {
                    display: true,
                    text: 'Stock'
                  }
                }
              },
              plugins: {
                legend: {
                  display: true
                },
                tooltip: {
                  callbacks: {
                    label: function(tooltipItem) {
                      const value = tooltipItem.raw;
                      const index = tooltipItem.dataIndex;
                      const minLimit = minLimits[index]; // Get specific min limit for this bar
                      return [
                        `Value: ${value}`,
                        `Min Limit: ${minLimit}`,
                        value < minLimit ? 'Status: Below Limit' : 
                          (value < (minLimit + (minLimit / 2)) ? "Status: Near Limit" : 'Status: Above Limit')
                      ];
                    }
                  }
                }
              }
            }
          };

          // If chart already exists, update it, else create a new chart
          if (window.conditionalBarChart) {
            window.conditionalBarChart.data = data; // Update data
            window.conditionalBarChart.update(); // Update the chart
          } else {
            // Render the chart for the first time
            window.conditionalBarChart = new Chart(
              document.getElementById('looStockStatus'),
              config
            );
          }
        }
      }
    });
  }
</script>
