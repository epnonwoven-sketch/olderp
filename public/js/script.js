const toggler = document.querySelector(".toggler-btn");
toggler.addEventListener("click", function () {
  document.querySelector("#sidebar").classList.toggle("collapsed");
});
  
document.addEventListener('DOMContentLoaded', function () {

    // Get all the sidebar links
    const sidebarLinks = document.querySelectorAll('.sidebar-link');

    // Event listener for when a sidebar link is clicked
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            // Only prevent default action if it is a collapsible item
            const targetId = e.target.closest('a').getAttribute('data-bs-target').slice(1); // Remove the '#' from the data-bs-target attribute

            // Store the active menu and submenu names in sessionStorage
            const parentMenu = e.target.closest('.sidebar-item');
            if (parentMenu) {
                const menuId = parentMenu.querySelector('a').getAttribute('id').slice(1); // Get the menu id without "p" prefix
                const subMenuId = targetId;

                // Call the function to store active menu and submenu state
                navBarMenuActive(menuId, subMenuId);
            }

            // Get all collapsible items (menus)
            const collapsibleItems = document.querySelectorAll('.sidebar-dropdown');

            collapsibleItems.forEach(item => {
                const itemId = item.id;
                const collapse = new bootstrap.Collapse(item, {
                    toggle: false // Don't toggle automatically
                });

                // Check if the clicked item is a child of the current item
                if (!item.contains(e.target)) {
                    if (itemId !== targetId) {
                        collapse.hide(); // Hide other items
                    } else {
                        // collapse.toggle(); // Toggle the clicked item
                    }
                }
            });

            // Only prevent the default action for links that are collapsible
            if (e.target.closest('a').hasAttribute('data-bs-target')) {
                e.preventDefault(); // Prevent default action for collapsible links
            }
        });
    });

    if (typeof(Storage) !== "undefined") {
        const activeMenuName = sessionStorage.getItem('activeMenuName');
        const activeSubMenuName = sessionStorage.getItem('activeSubMenuName');
        const activeSubSubMenuName = sessionStorage.getItem('activeSubSubMenuName');

        if (activeMenuName && activeSubMenuName) {
            // Set the menu and submenu as active
            const activeMenu = document.getElementById('p' + activeMenuName);
            const activeSubMenu = document.getElementById("p"+activeSubMenuName);
            const activeSubSubMenu = document.getElementById(activeSubSubMenuName);

            if (activeMenu) {
                activeMenu.setAttribute("aria-expanded", "true"); // Ensure the parent is expanded
                const parentDropdown = document.getElementById(activeMenuName);
                if (parentDropdown) {
                    parentDropdown.classList.add("show"); // Ensure the parent dropdown is visible
                }
            }

            // Set the second-level submenu as active
            if (activeSubMenu) {
                activeSubMenu.classList.add("show");
                activeMenu.setAttribute("aria-expanded", "true"); 
                const secondDropdown = document.getElementById(activeSubMenuName);
                if (secondDropdown) {
                    secondDropdown.classList.add("show");
                }
            }

            // Set the third-level submenu as active
            if (activeSubSubMenu) {
                activeSubSubMenu.setAttribute("aria-expanded", "true"); 
                const thirdDropdown = document.getElementById(activeSubSubMenuName);
                if (thirdDropdown) {
                    thirdDropdown.classList.add("show");
                }
            }

            if(activeSubMenu){
                activeSubMenu.classList.add("active-link");
            }
        }
    }
});

function toggleNave(element){
    if(!element.classList.toggle("collapsed")){
        element.classList.add("show");
    }
}

// Function to store the active menu and submenu in sessionStorage
function navBarMenuActive(menuName, subMenuName,subSubMenuName) {
    if (typeof(Storage) !== "undefined") {
        sessionStorage.setItem("activeMenuName", menuName);
        sessionStorage.setItem("activeSubMenuName", subMenuName);
        sessionStorage.setItem("activeSubSubMenuName", subSubMenuName);
        const activeSubMenu = document.getElementById("p"+subMenuName);
        if(activeSubMenu){
            document.querySelectorAll(".active-link").forEach((element) => {
                element.classList.remove("active-link");
            });
            
            activeSubMenu.classList.add("active-link");
        }

    }
}


function addFilter(tableName,indexNo=[]){
  // Define the table and first row headers
  var table = $('#'+tableName);

  // Dynamically create the second row in <thead> for dropdown filters
  var filterRow = $('<tr></tr>'); // Create a new <tr> for filters
  $("#" + tableName + " tr").has("th.filter-header").remove();

  table.find('thead tr:nth-child(1) th').each(function (index) {
      // Create <th> and <select> dynamically for each header
      if (indexNo.includes(index)) {
          filterRow.append('<th></th>'); // Empty header for non-filterable columns
      } else {
          var filterCell = $(`
              <th class="filter-header">
                  <select class="filter-select" data-column="${index}" style="width: 100%" multiple="multiple" style="font-size:xx-small; min-width: 100px;">
                      <option value="">All</option>
                  </select>
              </th>
          `);

          // Append <th> with dropdown to the filter row
          filterRow.append(filterCell);

      }
  });

  // Append the filter row to the <thead>
  table.find('thead').append(filterRow);

  // Initialize DataTable
  var dataTable = table.DataTable();

  // Populate dropdowns with unique values for each column
  dataTable.columns().every(function () {
      var column = this;
      var select = $('.filter-select[data-column="' + column.index() + '"]');

      // Get unique values for the column and add them as options
      column.data().unique().sort().each(function (d) {
          select.append('<option value="' + d + '">' + d + '</option>');
      });

      // Initialize Select2 for the dropdown
      select.select2({
          placeholder: 'Select one or more values',
          allowClear: false,
          width: '100%'
      }).on('select2:open', function () {
            // Apply min-width styling dynamically when dropdown opens
            $('.select2-container--open .select2-dropdown').css('min-width', '100px').css('z-index', '9999');
            $('.select2-container--open .select2-selection').css('min-width', '100px').css('z-index', '9999');
        }).on('select2:close', function () {
            // Apply min-width styling dynamically when dropdown opens
            $('.select2-container .select2-dropdown').css('min-width', '0px').css('z-index', '0');
            $('.select2-container .select2-selection').css('min-width', '0px').css('z-index', '0');
        });
  });

  dataTable.on('responsive-resize', function (e, datatable, columns) {
        datatable.columns().every(function (index) {
            if (columns[index]) {
                $('.filter-header').eq(index).show();  // Show filter if column is visible
            } else {
                $('.filter-header').eq(index).hide();  // Hide filter if column is collapsed
            }
        });
    });
  // Add filtering functionality for multi-select
  $('.filter-select').on('change', function () {
      var columnIndex = $(this).data('column'); // Get column index
      var selectedValues = $(this).val(); // Get selected values (array)

      // Build regex to match any of the selected values
      var regex = selectedValues && selectedValues.length > 0
          ? selectedValues.map((value) => `^${value}$`).join('|') // Join selected values with "|" for OR regex
          : '';

      // Apply the filter using regex
      dataTable.column(columnIndex).search(regex, true, false).draw(); // Regex-based search
  });

}


// ==============================================


function addFilter(tableName,indexNo=[], callback = null){
    // Define the table and first row headers
    var table = $('#'+tableName);

    // Dynamically create the second row in <thead> for dropdown filters
    var filterRow = $('<tr></tr>'); // Create a new <tr> for filters
    $("#" + tableName + " tr").has("th.filter-header").remove();

    table.find('thead tr:nth-child(1) th').each(function (index) {
        // Create <th> and <select> dynamically for each header
        if (indexNo.includes(index)) {
            filterRow.append('<th></th>'); // Empty header for non-filterable columns
        } else {
            var filterCell = $(`
                <th class="filter-header">
                    <select class="filter-select" data-column="${index}" style="width: 100%" multiple="multiple" style="font-size:xx-small; min-width: 100px;">
                        <option value="">All</option>
                    </select>
                </th>
            `);

            // Append <th> with dropdown to the filter row
            filterRow.append(filterCell);

        }
    });

    // Append the filter row to the <thead>
    table.find('thead').append(filterRow);

    // Initialize DataTable
    var dataTable = table.DataTable();

    // Populate dropdowns with unique values for each column
    dataTable.columns().every(function () {
        var column = this;
        var select = $('.filter-select[data-column="' + column.index() + '"]');

        // Get unique values for the column and add them as options
        column.data().unique().sort().each(function (d) {
            select.append('<option value="' + d + '">' + d + '</option>');
        });

        // Initialize Select2 for the dropdown
        select.select2({
            placeholder: 'Select one or more values',
            allowClear: false,
            width: '100%'
        }).on('select2:open', function () {
                // Apply min-width styling dynamically when dropdown opens
                $('.select2-container--open .select2-dropdown').css('min-width', '100px').css('z-index', '9999');
                $('.select2-container--open .select2-selection').css('min-width', '100px').css('z-index', '9999');
            }).on('select2:close', function () {
                // Apply min-width styling dynamically when dropdown opens
                $('.select2-container .select2-dropdown').css('min-width', '0px').css('z-index', '0');
                $('.select2-container .select2-selection').css('min-width', '0px').css('z-index', '0');
            });
    });

    function reformatFilter(excludeIndex) {

        // Store all filter values
        let allFilters = {};

        $('.filter-select').each(function () {

            let col = $(this).data('column');

            allFilters[col] = $(this).val() || [];
        });

        dataTable.columns().every(function () {

            let column = this;
            let idx = column.index();

            // Skip changed filter
            if (idx === excludeIndex) return;

            let select = $('.filter-select[data-column="' + idx + '"]');

            if (!select.length) return;

            let currentValues = select.val() || [];

            let uniqueValues = [];

            // Loop all rows
            dataTable.rows().every(function () {

                let rowIndex = this.index();

                let matched = true;

                // Check all filters EXCEPT current column
                for (let filterCol in allFilters) {

                    filterCol = parseInt(filterCol);

                    // Ignore current dropdown filter
                    if (filterCol === idx) continue;

                    let filterValues = allFilters[filterCol];

                    if (filterValues.length) {

                        let cellValue = dataTable
                            .cell(rowIndex, filterCol)
                            .data();

                        // Remove HTML
                        if (typeof cellValue === 'string') {
                            cellValue = $('<div>')
                                .html(cellValue)
                                .text()
                                .trim();
                        }

                        if (!filterValues.includes(String(cellValue))) {
                            matched = false;
                            break;
                        }
                    }
                }

                // If row matches all other filters
                if (matched) {

                    let cellData = dataTable
                        .cell(rowIndex, idx)
                        .data();

                    // Remove HTML
                    if (typeof cellData === 'string') {
                        cellData = $('<div>')
                            .html(cellData)
                            .text()
                            .trim();
                    }

                    if (
                        cellData !== undefined &&
                        cellData !== null &&
                        cellData !== '' &&
                        !uniqueValues.includes(cellData)
                    ) {
                        uniqueValues.push(cellData);
                    }
                }
            });

            uniqueValues.sort();

            // Rebuild dropdown
            select.empty();

            select.append('<option value="">All</option>');

            uniqueValues.forEach(function (item) {

                let isSelected =
                    currentValues.includes(String(item))
                        ? 'selected'
                        : '';

                select.append(
                    `<option value="${item}" ${isSelected}>${item}</option>`
                );
            });

            select.trigger('change.select2');
        });
    }


    dataTable.on('responsive-resize', function (e, datatable, columns) {
        datatable.columns().every(function (index) {
            if (columns[index]) {
                $('.filter-header').eq(index).show();  // Show filter if column is visible
            } else {
                $('.filter-header').eq(index).hide();  // Hide filter if column is collapsed
            }
        });
    });
    // Add filtering functionality for multi-select
    $('.filter-select').on('change', function () {
        var columnIndex = $(this).data('column'); // Get column index
        var selectedValues = $(this).val(); // Get selected values (array)

        // Build regex to match any of the selected values
        var regex = selectedValues && selectedValues.length > 0
            ? selectedValues.map((value) => `^${value}$`).join('|') // Join selected values with "|" for OR regex
            : '';

        // Apply the filter using regex
        dataTable.column(columnIndex).search(regex, true, false).draw(); // Regex-based search
        reformatFilter(columnIndex);

        if (typeof callback === 'function') {

            callback(dataTable);
        }

    });

}



// ===========================================










/*
    function addFilter(tableName, indexNo = []) {
        var table = $('#' + tableName);
        var filterRow = $('<tr></tr>');
        $("#" + tableName + " tr").has("th.filter-header").remove();

        table.find('thead tr:nth-child(1) th').each(function (index) {
            if (indexNo.includes(index)) {
                filterRow.append('<th></th>');
            } else {
                var filterCell = $(`  
                    <th class="filter-header">
                        <select class="filter-select" data-column="${index}" style="width: 100%" multiple="multiple">
                            <option value="">All</option>
                        </select>
                    </th>
                `);
                filterRow.append(filterCell);
            }
        });

        table.find('thead').append(filterRow);
        var dataTable = table.DataTable();

        // Store column data for dependency filtering
        var columnDataMap = {};

        dataTable.columns().every(function () {
            var column = this;
            var index = column.index();
            var select = $('.filter-select[data-column="' + index + '"]');

            var uniqueValues = column.data().unique().sort();
            columnDataMap[index] = uniqueValues;

            uniqueValues.each(function (d) {
                select.append('<option value="' + d + '">' + d + '</option>');
            });

            select.select2({
                placeholder: 'Select one or more values',
                allowClear: false,
                width: '100%'
            }).on('change', function () {
                var columnIndex = $(this).data('column');
                var selectedValues = $(this).val() || [];

                // Apply filtering logic
                var regex = selectedValues.length ? selectedValues.map(val => `^${val}$`).join('|') : '';
                dataTable.column(columnIndex).search(regex, true, false).draw();

                // Update dependent dropdowns
                updateDependentFilters(dataTable, columnIndex, selectedValues, columnDataMap);
            });
        });

        function updateDependentFilters(dataTable, changedIndex, selectedValues, columnDataMap) {
            console.log(columnDataMap);
        
            dataTable.columns().every(function () {
                var index = this.index();
                if (index !== changedIndex) {
                    var select = $('.filter-select[data-column="' + index + '"]');
        
                    // Get unique values from the filtered table data
                    var visibleData = dataTable.rows({ search: "applied" }).data().toArray();
                    var uniqueFilteredValues = [...new Set(visibleData.map(row => row[index] || ''))]
                        .filter(v => v !== '') // Remove empty/undefined values
                        .sort();
        
                    select.empty().append('<option value="">All</option>');
        
                    uniqueFilteredValues.forEach(value => {
                        select.append(`<option value="${value}">${value}</option>`);
                    });
        
                    select.trigger('change.select2');
                }
            });
        }
        
    }
*/

function base64Encode(str) {
    return btoa(unescape(encodeURIComponent(str)));
}

function base64Decode(str) {
    return decodeURIComponent(escape(atob(str)));
}

function cssColorToRgb(color) {
    const temp = document.createElement("div");
    temp.style.color = color;
    document.body.appendChild(temp);

    const rgb = getComputedStyle(temp).color;
    document.body.removeChild(temp);

    const match = rgb.match(/\d+/g);
    return match ? match.map(Number) : null;
}

function getContrastTextColor(bgColor) {
    const rgb = cssColorToRgb(bgColor);
    if (!rgb) return "#000000";

    // Perceived brightness formula
    const brightness = (rgb[0] * 299 + rgb[1] * 587 + rgb[2] * 114) / 1000;

    return brightness > 160 ? "#000000" : "#ffffff";
}


function isValidCssColor(color) {
    if (!color) return false;

    const s = new Option().style;
    s.color = '';
    s.color = color;
    return s.color !== '';
}

function normalizeColor(color) {
    return color.replace(/\s+/g, '');
}


function formatOption(option) {
    if (!option.id) {
        return option.text;
    }

    let rawColor = $(option.element).data('color');
    let color = normalizeColor(rawColor);

    let isValid = isValidCssColor(color);

    let bgColor = isValid ? color : "#ffffff";
    let textColor = isValid ? getContrastTextColor(bgColor) : "#000000";
    let border = isValid ? "none" : "1px solid #ccc";

    return $(`
        <span style="
            background-color:${bgColor};
            color:${textColor};
            padding:4px 10px;
            border-radius:4px;
            display:inline-block;
            border:${border};
        ">
            ${option.text}
        </span>
    `);
}
function destroySelect2(selector) {
    $(selector).each(function () {
        if ($(this).hasClass('select2-hidden-accessible')) {
            $(this).select2('destroy');
        }
    });
}

