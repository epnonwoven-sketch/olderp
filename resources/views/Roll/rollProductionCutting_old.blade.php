@include("layout.header")
<!-- Main Component -->

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Roll</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">List</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">  
        <div class="panel-heading">
            <h5 class="panel-title">Roll List</h5> 
            <div class="panel-control">
                <button id="updateCuttingOpen" type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#UpdateCuttingModel" >
                    Update Production <ion-icon name="add-circle-outline"></ion-icon>
                </button>
            </div>           
        </div>       
        <div class="panel-body">
            <table id="postsTable" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th >#</th>
                        <th>Purchase Date</th>
                        <th>Vendor Name</th>
                        <th>Hardness</th>
                        <th>Roll Type</th>
                        <th>Roll Size</th>
                        <th>GSM</th>
                        <th>Roll Color</th>
                        <th>Length</th>
                        <th>Roll No</th>
                        <th>Gross Weight</th>
                        <th>Net Weight</th>
                        <th>GSM Variation</th>

                        <th>W</th>
                        <th>L</th>
                        <th>G</th>
                        <th>Bag Type</th>
                        <th>Unit</th>
                        <th>Customer</th>
                        <th>Printing Color</th>
                        <th>Loop Color</th>
                        <th>Cutting Schedule Date</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <x-update-cutting-model />
</main>
<script>
    

    var machineId = window.location.pathname.split('/').pop();
    $(document).ready(function() {  

        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{route('roll.production.cutting',':machineId')}}".replace(':machineId', machineId), // The route where you're getting data from
                data: function(d) {

                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
                    });

                },
                beforeSend: function() {
                    $("#btn_search").val("LOADING ...");
                    $("#loadingDiv").show();
                },
                complete: function() {
                    $("#btn_search").val("SEARCH");
                    $("#loadingDiv").hide();
                },
            },
            columns: [
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
                { data: "purchase_date", name: "purchase_date" },
                { data: "vendor_name", name: "vendor_name" },
                { data: "hardness", name: "hardness" },
                { data: "roll_type", name: "roll_type" },
                { data: "size", name: "size" },
                { data: "gsm", name: "gsm" },
                { data: "roll_color", name: "roll_color" },
                { data: "length", name: "length" },
                { data: "roll_no", name: "roll_no" },
                { data: "gross_weight", name: "gross_weight" },
                { data: "net_weight", name: "net_weight" },
                { data: "gsm_variation", name: "gsm_variation" },
                { data : "w", name: "w" },
                { data : "l", name: "l" },
                { data : "g", name: "g" },
                { data : "bag_type", name: "bag_type" },
                { data : "bag_unit", name: "bag_unit" },
                { data : "client_name", name: "client_name" },
                { data : "print_color", name: "print_color" },
                { data : "loop_color", name: "loop_color" },
                { data : "schedule_date_for_cutting", name: "schedule_date_for_cutting" },
            ],
            dom: 'lBfrtip', // This enables the buttons
            language: {
                lengthMenu: "Show _MENU_" // Removes the "entries" text
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // The internal values
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"] // The display values, replace -1 with "All"
            ],
            buttons: [{
                    extend: 'excel',
                    text: 'Export to Excel',
                    className: 'btn btn-success',
            }],
            createdRow: function(row, data, dataIndex) {
                let td = $('td', row).eq(7); 
                td.attr("title", data?.gsm_json);
                // Apply the custom class to the row
                if (data.row_color) {
                    $(row).addClass(data.row_color);
                    if(data.row_color=="tr-client"){
                        $(row).attr("title", "book for client");
                    }else if(data.row_color=="tr-client-printed"){
                        $(row).attr("title", "roll have booked and printed");
                    }else if(data.row_color=="tr-printed"){
                        $(row).attr("title", "roll is printed");
                    }else if(data.row_color=="tr-primary-print"){
                        $(row).attr("title", "this roll will be delivering soon");
                    }else if(data.row_color=="tr-expiry-print blink"){
                        $(row).attr("title", "this roll  delivery has been expired");
                    }else if(data.row_color=="tr-argent-print"){
                        $(row).attr("title", "this roll  delivery is urgent");
                    }
                }
            },            
            initComplete: function () {
                addFilter('postsTable',[0]);
            },
        });
        
        $("#cuttingUpdateModalForm").validate({
            rules: {
                cuttingUpdateDate: {
                    required: true,
                },
                cuttingUpdateWeight:{
                    required:true
                },
                cuttingUpdateMachineId:{
                    required:true
                },
                cuttingUpdateRollId:{
                    required:true,                    
                },
            },
            submitHandler: function(form) {
                cuttingUpdateModal();
            }
        });

        $("#UpdateCuttingModel").on("click",function(){
            $("#id").val(machineId);
        });
        // addEventListenersToForm();

    });

    // function addEventListenersToForm() {
    //     const form = document.getElementById("rollForm");
    //     // Loop through all elements in the form
    //     Array.from(form.elements).forEach((element) => {
    //         // Add event listeners based on input type
    //         if (element.tagName === "INPUT" || element.tagName === "SELECT" || element.tagName === "TEXTAREA") {
    //             element.addEventListener("input", hideErrorMessage);
    //             element.addEventListener("change", hideErrorMessage);
    //         }
    //     });
    // }

    function hideErrorMessage(event) {
        const element = event.target;
        // Find and hide the error message associated with the element
        const errorMessage = document.getElementById(`${element.id}-error`);
        if (errorMessage) {
            errorMessage.innerHTML = "";
        }
    }

    function openCuttingUpdateModel(id){
        $.ajax({
            type:"GET",
            url: "{{ route('roll.dtl', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    rolDtl = data.data;
                    console.log(rolDtl); 
                    $("#cuttingUpdateRollId").val(rolDtl?.id);
                    $('[display_roll_no="roll_no_display"]').each(function(index, element) {
                        // Use jQuery to wrap the raw DOM element
                        $(element).html(rolDtl?.roll_no || '');
                    });
                    $("#cuttingUpdateModal").modal("show");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function cuttingUpdateModal(){
        $.ajax({
                type: "POST",
                'url': "{{route('roll.cutting.update')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#cuttingUpdateModalForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#cuttingUpdateModalForm").get(0).reset();
                        $('[display_roll_no="roll_no_display"]').each(function(index, element) {
                            // Use jQuery to wrap the raw DOM element
                            $(element).html('');
                        });
                        $("#cuttingUpdateModal").modal('hide');
                        $('#postsTable').DataTable().ajax.reload();
                        modelInfo(data.messages);
                    } else if (data?.errors) {
                        let errors = data?.errors;
                        console.log(data?.errors?.rollNo[0]);
                        modelInfo(data.messages);
                        for (field in errors) {
                            console.log(field);
                            $(`#${field}-error`).text(errors[field][0]);
                        }
                    } else {
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        );
    }

    function searchData(){
        $('#postsTable').DataTable().ajax.reload();
    }
</script>
@include("layout.footer")