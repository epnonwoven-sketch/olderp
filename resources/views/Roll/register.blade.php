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
        </div>
        <div class="panel-body">
            <form id="searchForm">
                <div class="row g-3">
                    <!-- From Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="fromDate">From Date</label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$fromDate}}" />
                        </div>
                    </div>

                    <!-- Upto Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="uptoDate">Upto Date</label>
                            <input type="date" name="uptoDate" id="uptoDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$uptoDate}}"/>
                        </div>
                    </div>
                </div>
                <div class="row text-center">
                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <!-- Search Button -->
                            <input type="button" id="btn_search" class="btn btn-primary w-100" onclick="searchData()" value="Search"/>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <div class="panel-body">
            <div class="tableStickyDiv">
                <table id="postsTable" class="table table-striped table-bordered table-fixed table-nowrap">
                    <thead>
                        <tr>
                            <th >#</th>
                            <th>Purchase Date</th>
                            <th>Vendor Name</th>
                            <th>Hardness</th>
                            <th>Quality</th>
                            <th>Roll Type</th>
                            <th>Roll Size</th>
                            <th>GSM </th>
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
                            <th>Printing Machine</th>
                            <th>Cutting Machine</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<script>
    var flag = window.location.pathname.split('/').pop();
    $(document).ready(function() {
        if (flag != "stoke") {
            $("#addRoll").hide();
            $("#addRollImport").hide();
        }
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ordering:false,
            ajax: {
                // url: "{{route('roll.list',':flag')}}".replace(':flag', flag), // The route where you're getting data from
                url:"{{route('roll.register')}}",
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
                { data: "quality", name: "quality" },
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
                { data : "printing_machine_name", name: "printing_machine_name" },
                { data : "cutting_machine_name", name: "cutting_machine_name" },
                // { data: "action", name: "action", orderable: false, searchable: false },
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
                        columns: [0, 1,2, 3,4,5,6,7,8,9,10,11]  // Export only Name, Position, and Age columns
                    }

                },
            ],
            createdRow: function(row, data, dataIndex) {
                // Apply the custom class to the row
                let td = $('td', row).eq(7); 
                td.attr("title", data?.gsm_json);
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
        if(flag=="history"){
            table.column(24).visible(false);
            table.column(23).visible(false);
            table.column(22).visible(false);
            table.column(21).visible(false);
            table.column(20).visible(false);
            table.column(19).visible(false);
            table.column(18).visible(false);
            table.column(17).visible(false);
            table.column(16).visible(false);
            table.column(15).visible(false);
            table.column(14).visible(false);
            table.column(13).visible(false);
            table.column(12).visible(false);
            table.column(11).visible(false);

        }

    });

    function searchData(){
        $('#postsTable').DataTable().ajax.reload(function(){
            addFilter('postsTable',[0]);
        }, false);
    }
</script>
@include("layout.footer")