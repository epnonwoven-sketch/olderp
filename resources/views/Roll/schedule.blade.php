@include("layout.header")
<!-- Main Component -->
<style>
    .move tbody tr {
        cursor: pointer;
    }
    .move tbody tr:hover {
        background-color: #f1f1f1;
    }
</style>
<style>
/* Custom background color for selected rows */
.dataTables_wrapper .selected {
    background-color: #ffc107 !important; /* Change this color as needed */
    color: #fff; /* Optional: Change text color */
}
tr.selected {
    background-color:rgb(202, 109, 27) !important;
}
</style>
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
                <button id="scheduleRoll" type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#datePickerModal" >
                    Schedule <ion-icon name="add-circle-outline"></ion-icon>
                </button>
            </div>           
        </div>
        <div class="panel-body">
            <table id="postsTable" class="table table-striped table-bordered move table-fixed">
                <thead>
                    <tr>
                        <th onclick="selectAllCheck()">#</th>
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
                        <th>Delivery Date</th>
                        <th>Printing Color</th>
                        <th>Loop Color</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <!-- model -->
    <div class="modal fade" id="datePickerModal" tabindex="-1" aria-labelledby="datePickerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="datePickerModalLabel">Select a Date</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dateForm">
                <div class="mb-3">
                    <label for="selectedDate" class="form-label">Date</label>
                    <input type="date" class="form-control" id="selectedDate" name="selectedDate" min="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" required>
                </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveDate">Save</button>
            </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="setScheduleModal" tabindex="-1" aria-labelledby="setScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">                
                <table id="setSchedule" class="table table-striped table-bordered move table-fixed">
                    <thead>
                        <tr>
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
                            <th>Delivery Date</th>
                            <th>Printing Color</th>
                            <th>Loop Color</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitSchedule">Save</button>
            </div>
            </div>
        </div>
    </div>

    <x-printing-schedule-form />
    <x-printing-update-form />
    <x-cutting-schedule-form />
    <x-cutting-update-form />
</main>
<script>

    var flag = window.location.pathname.split('/').pop();
    $(document).ready(function() {        
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            select: {
                style: 'os',
                selector: 'td:not(:last-child)'
            },


            ajax: {
                url: "{{route('roll.schedule',':flag')}}".replace(':flag', flag), // The route where you're getting data from
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
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false ,
                    render: function(data, type, row, meta) {
                            return `${meta.row + 1} <input type="checkbox" name="checkbox[]" class="row-select">`;
                        }
                },
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
                { data : "estimate_delivery_date", name: "estimate_delivery_date" },
                { data : "print_color", name: "print_color" },
                { data : "loop_color", name: "loop_color" },
            ],
            dom: 'lBfrtip', // This enables the buttons
            language: {
                lengthMenu: "Show _MENU_" // Removes the "entries" text
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // The internal values
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"] // The display values, replace -1 with "All"
            ],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel-fill text-success"></i> ',
                    className: 'btn btn-success',
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf-fill text-danger"></i>',
                    title: 'Data Export',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1,2, 3,4,5,6,7,8,9,10]  // Export only Name, Position, and Age columns
                    }

                },
            ],
            

            createdRow: function(row, data, dataIndex) {
                let td = $('td', row).eq(6); 
                td.attr("title", data?.gsm_json);
                // Apply the custom class to the row
                $(row).attr('data-id', data.id);
                $(row).attr('data-item', JSON.stringify(data));
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
                addFilter('postsTable',[0,$('#postsTable thead tr:nth-child(1) th').length - 1]);
            },

        });
        $("#printingScheduleModalForm").validate({
            rules: {
                printingScheduleDate: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                printingScheduleDate();
            }
        });

        $("#printingUpdateModalForm").validate({
            rules: {
                printingUpdateDate: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                printingUpdateModal();
            }
        });

        $("#cuttingScheduleModalForm").validate({
            rules: {
                cuttingScheduleDate: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                cuttingScheduleDate();
            }
        });

        $("#cuttingUpdateModalForm").validate({
            rules: {
                cuttingUpdateDate: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                cuttingUpdateModal();
            }
        });

    });

    let selectAll = false;

    function selectAllCheck(){
        if(selectAll)
        {
            $('input[name="checkbox[]"]').prop("checked",false);             
            selectAll = false;
        }
        else
        {
            $('input[name="checkbox[]"]').prop("checked",true);
            selectAll = true;
        }
    }



    function openModelBookingModel(id) {
        if (id) {
            $("#rollId").val(id);
            $("#rollBookingModal").modal("show");

        }
        return;
    }

    function openPrintingScheduleModel(id){
        $.ajax({
            type:"GET",
            url: "{{ route('roll.dtl', ':id') }}".replace(':id', id),
            data:{"flag":flag},
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    rolDtl = data.data;
                    console.log(rolDtl); 
                    $("#printingScheduleRollId").val(rolDtl?.id);
                    $("#printingScheduleDate").val(rolDtl?.schedule_date_for_print);
                    $("#roll_no_display").html(rolDtl?.roll_no);
                    $("#printingScheduleModal").modal("show");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function printingScheduleDate(){
        $.ajax({
                type: "POST",
                'url': "{{route('roll.printing.schedule')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#printingScheduleModalForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#printingScheduleModalForm").get(0).reset();
                        $("#roll_no_display").html("");
                        $("#printingScheduleModal").modal('hide');
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

    function openPrintingModel(id){
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
                    $("#printingUpdateRollId").val(rolDtl?.id);
                    $('[display_roll_no="roll_no_display"]').each(function(index, element) {
                        // Use jQuery to wrap the raw DOM element
                        $(element).html(rolDtl?.roll_no || '');
                    });
                    $("#printingUpdateModal").modal("show");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function printingUpdateModal(){
        $.ajax({
                type: "POST",
                'url': "{{route('roll.printing.update')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#printingUpdateModalForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#printingUpdateModalForm").get(0).reset();
                        $('[display_roll_no="roll_no_display"]').each(function(index, element) {
                            // Use jQuery to wrap the raw DOM element
                            $(element).html('');
                        });
                        $("#printingUpdateModal").modal('hide');
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



    function openCuttingScheduleModel(id){
        $.ajax({
            type:"GET",
            url: "{{ route('roll.dtl', ':id') }}".replace(':id', id),
            data:{"flag":flag},
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    rolDtl = data.data;
                    console.log(rolDtl); 
                    $("#cuttingScheduleRollId").val(rolDtl?.id);
                    $("#cuttingScheduleDate").val(rolDtl?.schedule_date_for_cutting);                    
                    $('[display_roll_no="roll_no_display"]').each(function(index, element) {
                            // Use jQuery to wrap the raw DOM element
                            $(element).html(rolDtl?.roll_no);
                        });
                    $("#cuttingScheduleModal").modal("show");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function cuttingScheduleDate(){
        $.ajax({
                type: "POST",
                'url': "{{route('roll.cutting.schedule')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#cuttingScheduleModalForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#cuttingScheduleModalForm").get(0).reset();
                        $('[display_roll_no="roll_no_display"]').each(function(index, element) {
                            // Use jQuery to wrap the raw DOM element
                            $(element).html('');
                        });
                        $("#cuttingScheduleModal").modal('hide');
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

    function openCuttingModel(id){
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
                        $("#printingUpdateModalForm").get(0).reset();
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
        $('#postsTable').DataTable().ajax.reload(null, false);
    }

    $(function() {        
        $("#postsTable tbody").sortable({
            helper: function(e, tr) {
                const originals = tr.children();
                const helper = tr.clone();
                helper.children().each(function(index) {
                    $(this).width(originals.eq(index).width());
                });
                return helper;
            },

            update: function(event, ui) {
                let order = [];
                $("#postsTable tbody tr").each(function(index, element) {
                    order.push({
                        id: $(element).data('id'),
                        position: index + 1,
                    });
                });
                console.log(order);
            }
        });
    });

    $("#saveDate").on("click",function(){
        $("#datePickerModal").modal("hide");
        showConfirmDialog('Are you sure you want to '+flag+' schedule?', saveTheOrder);
    });
    function saveTheOrder(){
        let scheduleDate = $("#selectedDate").val();
        let order = [];
            $("#postsTable tbody tr").each(function(index, element) {
                let checkbox = $(element).find('input.row-select');
                if (checkbox.is(':checked')) {
                    order.push({
                        id: $(element).data('id'),
                        position: index + 1,
                        roll_no: $(element).find('td:eq(9)').text(), // Adjust index for `roll_no`
                        rowData : $(element).data('item'),
                    });
                }
            });
        if(order.length === 0){
            modelInfo("Pleas select attlist on roll","info");
            return;
        }
        if(scheduleDate==""){
            modelInfo("Pleas select a date","info");
            return;
        }
        console.log("orderSelected:",order);
        
        const table = $("#setSchedule");
        const data = "";
        const tbody = $("#setSchedule tbody");
        $("#postsTable tbody tr").each(function(index, element) {
                let checkbox = $(element).find('input.row-select');
                if (checkbox.is(':checked')) {
                    item = $(element).data('item');
                    tbody.append(                               
                    $("<tr>").append(
                        `<td>${item.purchase_date}</td>`,
                        `<td>${item.vendor_name}</td>`,
                        `<td>${item.hardness || "N/A"}</td>`,
                        `<td>${item.roll_type}</td>`,
                        `<td>${item.size || "N/A"}</td>`,
                        `<td>${item.gsm}</td>`,
                        `<td>${item.roll_color || "N/A"}</td>`,
                        `<td>${item.length || "N/A"}</td>`,
                        `<td>${item.roll_no || "N/A"}</td>`,
                        `<td>${item.gross_weight || "N/A"}</td>`,
                        `<td>${item?.net_weight || "N/A"}</td>`, 
                        `<td>${item?.gsm_variation || ""}</td>`,  
                        `<td>${item?.w || ""}</td>`,
                        `<td>${item?.l || ""}</td>`,
                        `<td>${item?.g || ""}</td>`,
                        `<td>${item?.bag_type || ""}</td>`,
                        `<td>${item?.bag_unit || ""}</td>`,
                        `<td>${item?.client_name || ""}</td>`,
                        `<td>${item?.estimate_delivery_date || ""}</td>`,
                        `<td>${item?.print_color || ""}</td>`,
                        `<td>${item?.loop_color || ""}</td>`,  
                    )
                );
                }
        });
        $("#setScheduleModal").modal("show");
        return;
        $.ajax({
            url: "{{route('roll.schedule.set',':flag')}}".replace(':flag', flag),
            type:"post",
            data:{"scheduleDate":scheduleDate,"rolls":order},
            dataType:"json",
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data?.status){
                    $('#postsTable').DataTable().ajax.reload();
                    modelInfo(data?.messages);
                }else{
                    modelInfo("Server Error","error");
                    console.log(data);
                }
            },
            error:function(error){
                console.log(error);
                modelInfo("Internal Server Error","error");
                $("#loadingDiv").hide();
            }
        });
    }

    
</script>
@include("layout.footer")