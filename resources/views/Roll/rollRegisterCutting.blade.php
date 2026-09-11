@include("layout.header")
<!-- Main Component -->

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Production</a></li>
                    <li class="breadcrumb-item fs-6"><a href="#">Register</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">{{$machine->name??""}} Register</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <div class="panel-body">
            <form id="searchForm">
                <div class="row g-3">
                    <!-- From Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="fromDate">From Date</label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$fromDate??''}}" />
                        </div>
                    </div>

                    <!-- Upto Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="uptoDate">Upto Date</label>
                            <input type="date" name="uptoDate" id="uptoDate" class="form-control" max="{{date('Y-m-d')}}" value="{{$uptoDate??''}}" />
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-sm-3">
                        <!-- Search Button -->
                        <input type="button" id="btn_search" class="btn btn-primary w-100" onclick="searchData()" value="Search"/>
                    </div>
                </div>
            </form>

        </div>
        <div class="panel-body">
            <div class="tableStickyDiv">
                <table id="postsTable" class="table table-striped table-bordered table-fixed">
                    <thead>
                        <tr>
                            <th>Cutting Date</th>
                            <th>Roll No</th>
                            <th>Vendor Name</th>
                            <th>Roll Size</th>
                            <th>GSM</th>
                            <th>Roll Color</th>
                            <th>Net Weight</th>
                            <th>Weight After Print</th>
                            <th>Bag Size</th>
                            <th>Bag Type</th>
                            <th>Client Name</th>
                            <th>Loop Color</th>
                            <th>Possible Piece</th>
                            <th>Operator Name</th>
                            <th>Helper Name</th>
                            <th>Shift</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <x-printing-update-form />
    <x-cutting-update-form />
</main>
<script>
    

    var machineId = window.location.pathname.split('/').pop();
    $(document).ready(function() {  

        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ordering:false,
            ajax: {
                url: "{{route('roll.register.cutting',':machineId')}}".replace(':machineId', machineId), // The route where you're getting data from
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
                { data: "cutting_date", name: "cutting_date" },
                { data: "roll_no", name: "roll_no" },
                { data: "vendor_name", name: "vendor_name" },
                { data: "size", name: "size" },
                { data: "gsm", name: "gsm" },
                { data: "roll_color", name: "roll_color" },
                { data: "net_weight", name: "net_weight" },
                { data : "weight_after_print", name: "weight_after_print" },
                { data: "bag_size", name: "bag_size" },
                { data : "bag_type", name: "bag_type" },
                { data : "client_name", name: "client_name" },
                { data : "loop_color", name: "loop_color" },
                { data : "possible_piece", name: "possible_piece" },
                { data : "operator_name", name: "operator_name" },
                { data : "helper_name", name: "helper_name" },
                { data : "shift", name: "shift" },
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
                        columns: [0, 1,2, 3,4,5,6,7,8,9,10,11,12,13,14,15]  // Export only Name, Position, and Age columns
                    }

                },
            ], 
            createdRow: function(row, data, dataIndex) {
                let td = $('td', row).eq(5); 
                td.attr("title", data?.gsm_json);
            },           
            initComplete: function () {
                addFilter('postsTable');
            },
        });

    });

    function searchData(){
        $('#postsTable').DataTable().ajax.reload();
    }
</script>
@include("layout.footer")