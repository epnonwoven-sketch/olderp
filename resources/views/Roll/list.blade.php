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
            <!-- <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#rollSwapSwapModal">Swap The Roll</button> -->
             <div class="panel-control">

                 <div class="form-check form-switch" style="cursor:pointer">
                    <label class="form-check-label" for="printedRollOnly">Only Printed</label> <input class="form-check-input" type="checkbox" id="printedRollOnly" name="printedRollOnly" onclick="searchData()" />
                 </div>
                 <div class="form-check form-switch" style="cursor:pointer">
                    <label class="form-check-label" for="notBookedRollOnly">Not Book Only</label> <input class="form-check-input" type="checkbox" id="notBookedRollOnly" name="notBookedRollOnly" onclick="searchData()" />
                 </div>
                 <button class="btn btn-primary" onclick="swapSelectedRoll()">Swap The Roll</button>
                 <button class="btn btn-danger" onclick="sellRoll()">Sell The Roll</button>
             </div>
            
        </div>
        <div class="panel-body">
            <div class="panel-control justify-content-end">
                <span class="fw-bold">Total Weight: </span><span id="total_weight">0</span> <i id="total_weight1" class="text-info"></i>
            </div>
            <input type="hidden" id="selectedRollId">
            <div class="tableStickyDiv">
                <table id="postsTable" class="table table-striped table-bordered text-center table-fixed">
                    <thead>
                        
                        <tr>
                            <th onclick="selectAllCheck()">Roll No</th>
                            <th>Quality</th>
                            <th>Roll Size</th>
                            <th>GSM</th>
                            <th>Roll Color</th>
                            
                            <th>Net Weight</th>
                            <th>Bag Size</th>
                            <th>Bag Type</th>
                            <th>Client Name</th>
                            <th>Unit</th>
                            <!-- <th>Hardness</th> -->
                            <th>Roll Type</th>
                            <th>Grade</th>
                            <th>Length</th>
                            <th>Gross Weight</th>
                            <th>GSM Variation</th>
                            <th>Action</th>
                        </tr> 
                    </thead>
                    <tbody>
    
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    <!-- Modal -->

    
    

    <x-pending-order-book />
    <x-roll.edit-roll />
    <x-roll.roll-swap />
    <x-roll.transport-component-model />
    <x-roll.split-roll />
</main>
<script>
    const rules = {
        rollNo: {
            required: true,
        },
        vendorId: {
            required: true,
        },
        purchaseDate: {
            required: true,
        },
        rollSize: {
            required: true,
        },
        rollGsm: {
            required: true,
        },
        rollColor: {
            required: true,
        },
        netWeight: {
            required: true,
        },
        grossWeight: {
            required: true,
        },
        estimatedDespatchDate:{
            required: (element) => {
                return $("#forClientId").val() != "";
            },
        },
        bagUnits: {
            required: (element) => {
                return $("#forClientId").val() != "";
            },
        },
        bagTypeId: {
            required: (element) => {
                return $("#forClientId").val() != "";
            },
        },
        "printingColor[]": {
            required: (element) => {
                return $("#forClientId").val() != "";
            },
        },
    };

    var flag = window.location.pathname.split('/').pop();
    $(document).ready(function() {
        
        $('#clientModal').on('hidden.bs.modal', function() {
            $('#rollBookingModal').css("z-index","");
        });

        if (flag != "stoke") {
            $("#addRoll").hide();
            $("#addRollImport").hide();
        }
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            // responsive: true,
            ordering:false,
            ajax: {
                url: "{{route('roll.list',':flag')}}".replace(':flag', flag), // The route where you're getting data from
                data: function(d) {

                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
                    });
                    if($("#printedRollOnly").is(":checked")){
                        d["printedRollOnly"] = $("#printedRollOnly").is(":checked");
                    }
                    if($("#notBookedRollOnly").is(":checked")){
                        d["notBookedRollOnly"] = $("#notBookedRollOnly").is(":checked");
                    }

                },
                dataSrc: function (json) {
                    // Get extra data from server
                    // $('#totalAmount').text(json?.totalAmount.toFixed(2)); 
                    $('#total_weight').text(json?.totalWeight); 
                    return json.data;
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
                // { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false,
                //     render:function(row,type,data){
                //         return `${data?.DT_RowIndex} <input type="checkbox" value='${data.id}' onchange='updateSelection(event)' />`
                //     } 
                // },
                { data: "roll_no", name: "roll_no" ,render:function(row,type,data){return (data.roll_no ? data.roll_no + `<input type="checkbox" class="checkbox" value='${data.id}' onchange='updateSelection(event)' />` :"N/A")} },
                // { data: "purchase_date", name: "purchase_date" ,render:function(row,type,data){return (data.purchase_date ? data.purchase_date :"N/A")}},
                // { data: "vendor_name", name: "vendor_name",render:function(row,type,data){return (data.vendor_name ? data.vendor_name :"N/A")} },
                { data : "quality", name: "quality" ,render:function(row,type,data){return (data.quality ? data.quality :"N/A")}},
                { data: "size", name: "size" ,render:function(row,type,data){return (data.size ? data.size :"N/A")}},
                { data: "gsm", name: "gsm",render:function(row,type,data){return (data.gsm ? data.gsm :"N/A")} },
                { data: "roll_color", name: "roll_color" ,render:function(row,type,data){return (data.roll_color ? data.roll_color :"N/A")}},
                
                { data: "net_weight", name: "net_weight" ,render:function(row,type,data){return (data.net_weight ? data.net_weight :"N/A")}},
                { data: "bag_size", name: "bag_size" ,render:function(row,type,data){return (data.bag_size ? "<pre>"+data.bag_size+"</pre>" :"N/A")}},
                { data: "bag_type", name: "bag_type" ,render:function(row,type,data){return (data.bag_type ? data.bag_type :"N/A")}},
                { data: "client_name", name: "client_name" ,render:function(row,type,data){return (data.client_name ? data.client_name :"N/A")}},
                { data: "bag_unit", name: "bag_unit" ,render:function(row,type,data){return (data.bag_unit ? data.bag_unit :"N/A")}},
                // { data: "hardness", name: "hardness" ,render:function(row,type,data){return (data.hardness ? data.hardness :"N/A")} },
                { data: "roll_type", name: "roll_type",render:function(row,type,data){return (data.roll_type ? data.roll_type :"N/A")} },
                { data : "grade", name: "grade" ,render:function(row,type,data){return (data.grade ? data.grade :"N/A")}},
                { data: "length", name: "length",render:function(row,type,data){return (data.length ? data.length :"N/A")} },
                { data: "gross_weight", name: "gross_weight" ,render:function(row,type,data){return (data.gross_weight ? data.gross_weight :"N/A")}},
                { data: "gsm_variation", name: "gsm_variation" ,render:function(row,type,data){return (data.gsm_variation ? data.gsm_variation :"N/A")}},
                { data: "action", name: "action", orderable: false, searchable: false,render:function(row,type,data){return (`<span style="display:flex;">${data?.action}</span>`)} },
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
                let td = $('td', row).eq(5); 
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
                hideColumn(table);
                addFilter('postsTable',[flag=="history"?0:$('#postsTable thead tr:nth-child(1) th').length - 1],sum);
            },     
        });

        function sum(table){

            let total = 0;

            // Check global search
            let globalSearch = table.search();

            // Check column filters
            let columnSearch = table.columns().search().toArray();

            // Test if any filter/search applied
            let isFilterApplied = globalSearch !== '' ||
                columnSearch.some(val => val !== '');

            console.log("Filter Applied:", isFilterApplied);

            table.rows({ search: 'applied' }).every(function () {

                let data = this.data();

                total += parseFloat(data.net_weight || 0);
            });

            console.log(total);

            $('#total_weight1').html(total.toFixed(2));

            // Example UI
            if(isFilterApplied){
                $('#total_weight1').show();
            }else{
                $('#total_weight1').hide();
            }
        }

        function hideColumn(table){
            // return;
            const columnsToHide = [13,14];
            columnsToHide.forEach(index => table.column(index).visible(false));
        }        

        $("#addMenu").on("click", function() {
            $("#myForm").submit();
        });

        $('button[data-bs-target="#addMenuModel"]').on("click", () => {
            $("#myForm").get(0).reset();
        });
        $("#rollForm").validate({
            rules: rules,
            messages: {
                menu_name: {
                    required: "Please enter a menu name",
                    minlength: "Menu name must be at least 3 characters long"
                },
                order_no: {
                    required: "Please enter an order number",
                    number: "Please enter a valid number for the order"
                },
                parent_menu_mstr_id: {
                    required: "Please select a parent menu"
                },
                parent_sub_menu_mstr_id: {
                    required: "Please select a parent sub-menu"
                },
                url_path: {
                    required: "Please enter the menu path"
                },
                menu_icon: {
                    required: "Please select a menu icon"
                },
                "user_type_mstr_id[]": {
                    required: "Please select at least one user type"
                }
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addRoll();
            }
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

        $("#rollBookingForm").validate({
            rules: {
                bookingForClientId: {
                    required: true,
                },
                bookingEstimatedDespatchDate:{
                    required:true
                },
                bookingBagUnits: {
                    required: true,
                },
                bookingBagTypeId: {
                    required: true,
                },
                bookingPrintingColor: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                bookForClient();
            }
        });
        // addEventListenersToForm();

    });

    

    function hideErrorMessage(event) {
        const element = event.target;
        // Find and hide the error message associated with the element
        const errorMessage = document.getElementById(`${element.id}-error`);
        if (errorMessage) {
            errorMessage.innerHTML = "";
        }
    }

    function addRoll() {
        $.ajax({
                type: "POST",
                'url': "{{route('roll.add')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#rollForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#rollForm").get(0).reset();
                        $("#rollModal").modal('hide');
                        $('#postsTable').DataTable().draw();
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

        )
    }

    var loadSubMenuMstr = () => {
        subMenuLoadCount++;
        if ($('#parent_menu_mstr_id').val() != 0 && $('#parent_menu_mstr_id').val() != -1) {
            $.ajax({
                type: "get",
                url: "{{route('submenu-list')}}",
                dataType: "json",
                data: {
                    "id": $('#parent_menu_mstr_id').val(),
                },
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    if (data.status == true) {
                        $("#parent_sub_menu_mstr_id").html(data.data);
                        if (parent_sub_menu_mstr_id != '' && subMenuLoadCount == 1) {
                            $("#parent_sub_menu_mstr_id").val(parent_sub_menu_mstr_id);
                        }
                    } else {
                        $("#parent_sub_menu_mstr_id").html('<option value="0">#</option>');
                    }
                    $("#loadingDiv").hide();
                }
            });
        } else {
            $("#parent_sub_menu_mstr_id").val("0");
        }
    };


    function openModelBookingModel(id) {
        if (id) {
            $("#rollBookingModal").on("show.bs.modal",function(){
                resetFormBook("myForm");
            })
            $("#id").val(id);
            $("#rollBookingModal").modal("show");

        }
        return;
    }

    function openPrintingScheduleModel(id){
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
                        $('#postsTable').DataTable().draw();
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
                        $('#postsTable').DataTable().draw();
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
                        $('#postsTable').DataTable().draw();
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
                        $('#postsTable').DataTable().draw();
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

    function bookForClient() {
        $.ajax({
                type: "POST",
                'url': "{{route('roll.book')}}",

                "deferRender": true,
                "dataType": "json",
                'data': $("#rollBookingForm").serialize(),
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success: function(data) {
                    $("#loadingDiv").hide();
                    if (data.status) {
                        $("#rollBookingForm").get(0).reset();
                        $("#rollBookingModal").modal('hide');
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

        )
    }
    function searchData(){
        $('#postsTable').DataTable().ajax.reload();
    }

    function removeBooking(id){
        $.ajax({
            url:"{{route('roll.order.remove.booking')}}",
            type:"post",
            data:{"id":id},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){                
                $("#loadingDiv").hide();
                if(data?.status){
                    $('#postsTable').DataTable().ajax.reload();
                }else{
                    modelInfo(data?.message,"warning");
                }
            },
            error:function(errors){
                console.log(errors);
                $("#loadingDiv").hide();
                modelInfo("server error","error")
            }
        })
    }

    function transferInTransit(id){
        $.ajax({
            url:"{{route('roll.stock.to.transit')}}",
            type:"post",
            data:{"id":id},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){                
                $("#loadingDiv").hide();
                if(data?.status){
                    $('#postsTable').DataTable().ajax.reload();
                }else{
                    modelInfo(data?.message,"warning");
                }
            },
            error:function(errors){
                console.log(errors);
                $("#loadingDiv").hide();
                modelInfo("server error","error")
            }
        })
    }

    function updateSelection(event) {
        let id = event.target.value;
        let rollId = $("#selectedRollId").val().split(",").filter(Boolean); // Ensure no empty values

        if (event.target.checked) {
            rollId.push(id);
        } else {
            rollId = rollId.filter(item => item !== id); // Remove only the unchecked item
        }

        $("#selectedRollId").val(rollId.join(",")); // Update the hidden input field

        console.log(rollId);
    }

    let selectAll = false;
    function selectAllCheck(){
        if(selectAll)
        {
            $('.checkbox').prop("checked",false).trigger('change');             
            selectAll = false;
        }
        else
        {
            $('.checkbox').prop("checked",true).trigger('change');;
            selectAll = true;
        }
    }

    function swapSelectedRoll() {
        let rollId = $("#selectedRollId").val().split(",").filter(Boolean);

        if (rollId.length < 2) {
            alert("Please select 2 rolls");
            return;
        } else if (rollId.length > 2) {
            alert("Selected rolls are more than 2");
            return;
        }

        // Create FormData object
        let formData = new FormData();
        formData.append("roll[firstRoll][]", rollId[0]);
        formData.append("roll[secondRoll][]", rollId[1]);

        $.ajax({
            url: "{{ route('roll.swap.selected') }}",
            type: "POST",
            data: formData,
            processData: false,  // ⬅️ Prevent jQuery from processing FormData
            contentType: false,  // ⬅️ Prevent jQuery from setting content type
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF Token for Laravel
            },
            beforeSend: function () {
                $("#loadingDiv").show();
            },
            success: function (response) {
                $("#loadingDiv").hide();

                if (response.status) {
                    $("#selectedRollId").val("");
                    $("#rollSwapSwapModal").modal("hide");
                    $("#swap_tbl tbody").empty();
                    searchData();
                } else if (response?.errors) {
                    console.log(response?.errors);
                    modelInfo(response.message, "warning");

                    for (let field in response.errors) {
                        let fieldId = field.replace(/\./g, '\\.');
                        $(`#${fieldId}-error`).text(response.errors[field][0]);
                    }
                } else {
                    modelInfo("Server error", "error");
                }
            },
            error: function (errors) {
                console.log(errors);
                $("#loadingDiv").hide();
                modelInfo("Server error", "error");
            }
        });
    }

    function sellRoll(){
        let rollId = $("#selectedRollId").val().split(",").filter(Boolean);

        if (rollId.length < 1) {
            alert("Please select 1 rolls");
            return;
        } 
        $("#hiddenDiv").empty();
        // Append hidden inputs for each rollId
        rollId.forEach(function (id) {
            $("#hiddenDiv").append(
                `<input type="hidden" name="rolls[][id]" value="${id}">`
            );
        });
        $("#transportModel").modal("show");
    }

    function splitRole(rollId){
        $("#rollSplitId").val(rollId);
        $("#rollSplitModal").modal("show");
    }



    

</script>
@include("layout.footer")