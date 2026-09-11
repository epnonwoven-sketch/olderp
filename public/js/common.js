
function isNum(e){
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
        return false;
    }
}
function isAlpha(e){
    var keyCode = (e.which) ? e.which : e.keyCode
    if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)
        return false;

    return true;
}
function isNumDot(e) {
    var charCode = (e.which) ? e.which : e.keyCode;
    if (charCode == 46) {
        var txt = e.target.value;
        if ((txt.indexOf(".") > -1) || txt.length == 0) {
            return false;
        }
    } else {
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            return false;
        }
    }
}

function gsmJson(e) {
    var keyCode = e.which || e.keyCode;
    var value = e.target.value;
    
    // Ensure the input starts with a number (only on first character entry)
    if (value.length === 0 && (keyCode < 48 || keyCode > 57)) {
        popupAlert("Input must start with a number!");
        e.preventDefault();
        return false;
    }

    // Allow only numbers and slashes (no dots allowed)
    if ((keyCode < 48 || keyCode > 57) && keyCode !== 47) {
        popupAlert("Only numbers and '/' are allowed!");
        e.preventDefault();
        return false;
    }

    // Prevent consecutive slashes
    if (value.slice(-1) === '/' && keyCode === 47) {
        popupAlert("Consecutive '/' are not allowed!");
        e.preventDefault();
        return false;
    }

    // Count the number of slashes and restrict to a maximum of 3
    var slashCount = (value.match(/\//g) || []).length;
    if (slashCount >= 2 && keyCode === 47) {
        popupAlert("Only three '/' are allowed!");
        e.preventDefault();
        return false;
    }

    return true;
}


jQuery.validator.addMethod("dateFormatYYYMMDD", function(value, element) {
    return this.optional(element) || /^([12]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))+$/i.test(value);
}, "Invalid format (YYYY-MM-DD)"); 

jQuery.validator.addMethod("alphaSpace", function(value, element) {
    return this.optional(element) || /^[a-zA-Z ]+$/i.test(value);
}, "Letters only please (a-z, A-Z )");

$(".toggler-btn").on("click", function() {
    document.querySelector("#sidebar").classList.toggle("collapsed");
});
toastr.options = {
    "closeButton": true, // Show close (dismiss) button
    "debug": false, 
    "newestOnTop": true,
    "progressBar": true, // Display a progress bar
    "positionClass": "toast-top-right", // Top right corner
    "preventDuplicates": true, // Avoid duplicate toasts
    "onclick": null,
    "showDuration": "300", // How long the show animation lasts
    "hideDuration": "1000", // How long the hide animation lasts
    "timeOut": "3000", // Auto-hide delay (5 seconds)
    "extendedTimeOut": "1000", // Extra time for mouse hover
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};
function modelInfo(msg,className="success") {
    

    if(className=="error") {
        toastr.error(msg);
    }
    else if(className=="info") {
        toastr.info(msg);
    }
    else if(className=="warning") {
        toastr.warning(msg);
    }else{
        toastr.success(msg);
    }

}


function addEventListenersToForm(id) {
    const form = document.getElementById(id);
    // Loop through all elements in the form
    Array.from(form.elements).forEach((element) => {
        // Add event listeners based on input type
        if (element.tagName === "INPUT" || element.tagName === "SELECT" || element.tagName === "TEXTAREA") {
            element.addEventListener("input", hideErrorMessage);
            element.addEventListener("change", hideErrorMessage);
        }
    });
}

function hideErrorMessage(event) {
    const element = event.target;
    // Find and hide the error message associated with the element
    const errorMessage = document.getElementById(`${element.id}-error`);
    if (errorMessage) {
        errorMessage.innerHTML = "";
    }
}



// add search option 

//=============================================

function addSearch(id) {
    const select = $("#" + id);
    const options = getOptions(select);

    const searchInput = $('<input type="text" class="search-input form-control" placeholder="Search...">');
    const dropdown = $('<div class="dropdownSearch"></div>');
    const div = $("<div id='div"+id+"' >");
    div.append(searchInput);
    div.append(dropdown);
    select.before(div);
    // select.before(dropdown);

    searchInput.css({
        width: select.outerWidth() + "px",
        height: select.outerHeight() + "px",
        padding: select.css("padding"),
        fontSize: select.css("font-size")
    });

    dropdown.css({
        width: select.outerWidth() + "px",
    });

    searchInput.hide();
    dropdown.hide();

    select.on("click", function () {
        toggleSelectAndSearch(select, searchInput, dropdown);
    });

    dropdown.on("mousedown", function (e) {
        if (!div.is(e.relatedTarget) && !$(e.relatedTarget).closest(div).length) {
            dropdown.hide();
            searchInput.hide();
            select.show();
        }
    });


    populateDropdown('', options, dropdown, searchInput, select);

    searchInput.on("input", function () {
        const filter = $(this).val().toLowerCase();
        populateDropdown(filter, options, dropdown, searchInput, select);
    });

    observeSelectChanges(select, options, dropdown, searchInput); // Observe changes in the <select>
}

function getOptions(select) {
    return Array.from(select.find("option")).map(option => ({
        text: option.textContent || option.innerText,
        value: option.value
    }));
}

function populateDropdown(filter = '', options, dropdown, searchInput, select) {
    dropdown.empty();
    const filteredOptions = options.filter(option =>
        option.text.toLowerCase().includes(filter.toLowerCase())
    );

    if (filteredOptions.length === 0) {
        dropdown.append('<div>No results found</div>');
        return;
    }

    filteredOptions.forEach(option => {
        const div = $("<div>")
            .text(option.text)
            .attr("data-value", option.value)
            .css("cursor", "pointer")
            .on("click", function () {
                setSelect(option.value, option.text, dropdown, searchInput, select);
            });
        dropdown.append(div);
    });

    const offset = select.offset();
    
}

function setSelect(value, text, dropdown, searchInput, select) {
    select.val(value).trigger('change');
    dropdown.hide();
    searchInput.val(text);
    searchInput.hide();
    select.show();
}

function toggleSelectAndSearch(select, searchInput, dropdown) {
    if (searchInput.is(":visible")) {
        searchInput.hide();
        dropdown.hide();
        select.show();
    } else {        
        searchInput.show();
        searchInput.css("display","block");
        searchInput.focus();
        dropdown.show();
        select.hide();
    }
}

function observeSelectChanges(select, options, dropdown, searchInput) {
    const observer = new MutationObserver(() => {
        // Update options array and redraw dropdown
        options.length = 0;
        options.push(...getOptions(select));
        populateDropdown('', options, dropdown, searchInput, select);
    });

    observer.observe(select[0], { childList: true });
}

//end

function resetFormById(id){
    $("#"+id+" input[type='hidden'][name*='id']").val("");
}



// drag and drop

// Functionality for dragging
$(document).ready(function(){
        // Functionality for dragging
        document.addEventListener('mousedown', (e) => {
            const target = e.target.closest('.movable');
            if (!target || e.target.closest('.header .icons')) return; // Ignore dragging when clicking the collapse icon
    
            let isDragging = true;
            const rect = target.getBoundingClientRect();
            const offsetX = e.clientX - rect.left;
            const offsetY = e.clientY - rect.top;
    
            target.style.cursor = 'grabbing';
    
            const moveHandler = (e) => {
            if (!isDragging) return;
            target.style.left = `${e.clientX - offsetX}px`;
            target.style.top = `${e.clientY - offsetY}px`;
            };
    
            const stopDragging = () => {
            isDragging = false;
            target.style.cursor = 'grab';
            document.removeEventListener('mousemove', moveHandler);
            document.removeEventListener('mouseup', stopDragging);
            };
    
            document.addEventListener('mousemove', moveHandler);
            document.addEventListener('mouseup', stopDragging);
        });
  
      // Functionality for collapsing the entire container
      document.querySelectorAll('.movable .header .icons').forEach((icon) => {
        icon.addEventListener('click', (e) => {
          const container = e.target.closest('.movable');
          const isCollapsed = container.classList.toggle('collapsed');
          const content = container.querySelector('.content');
          
          if (isCollapsed) {
            content.style.display = 'none'; // Hide the content
            e.target.textContent = '▶';    // Update icon to collapsed state
          } else {
            content.style.display = 'block'; // Show the content
            e.target.textContent = '▼';     // Update icon to expanded state
          }
        });
      });
});

function setHintDefault(id="") {
    document.querySelectorAll(((id ? "#"+id : "")+" ")+'.movable').forEach((container) => {
        // Reset position to default (top-left corner or a defined position)
        container.style.left = '';
        container.style.top = '';

        // Expand all containers
        const isCollapsed = container.classList.contains('collapsed');
        if (isCollapsed) {
            const icon = container.querySelector('.header .icons');
            const content = container.querySelector('.content');
            container.classList.remove('collapsed');
            if (content) content.style.display = 'block';
            if (icon) icon.textContent = '▼'; // Update icon to expanded state
        }
    });
}

function setHintCollapse(id=""){
    document.querySelectorAll(((id ? "#"+id : "")+" ")+'.movable').forEach((container) => {
        // Reset position to default (top-left corner or a defined position)
        container.style.left = '';
        container.style.top = '';

        // Expand all containers
        const isCollapsed = container.classList.contains('collapsed');
        if (!isCollapsed) {
            const icon = container.querySelector('.header .icons');
            const content = container.querySelector('.content');
            container.classList.add('collapsed');
            if (content) content.style.display = 'block';
            if (icon) icon.textContent = '▼'; // Update icon to expanded state
        }
    });
}

//   end

function evaluateFormula(formula, variables) {
    return new Function(...Object.keys(variables), "return " + formula)(...Object.values(variables));
}

let formula = "a + b * c"; // Dynamic formula
let values = { a: 10, b: 5, c: 2 }; // Variables

console.log(evaluateFormula(formula, values)); // Output: 20



