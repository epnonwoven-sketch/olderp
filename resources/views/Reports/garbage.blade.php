@include("layout.header")

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Report</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Garbage</li>
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
                <table id="postsTable" class="table table-striped table-bordered table-fixed">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Client Name</th>
                            <th>Cutting Date</th>
                            <th>Machine</th>
                            <th>Bag Size</th>
                            <th>Roll No.</th>
                            <th>Shift</th>
                            <th>Operator</th>
                            <th>Helper</th>
                            <th>Garbage</th>
                            <th>%</th>
                            <th>WIP Garbage</th>
                            <th>%</th>
                            <th>Total Garbage</th>
                            <th>Remarks</th>
                            <th>Verify By</th>
                            <th>Verify Date</th>
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
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ordering:false,
            ajax: {
                url:"{{route('report.garbage')}}",
                data: function(d) {
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; 
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
                { data: "DT_RowIndex",  name: "DT_RowIndex", orderable: false, searchable: false },
                { data: "client_name", name: "client_name" },
                { data: "cutting_date", name: "cutting_date"},
                { data: "machine", name: "machine"},
                { data: "bag_size", name: "bag_size"},
                { data: "roll_no", name: "roll_no"},
                { data: "shift", name: "shift"},
                { data: "operator_name", name: "operator_name"}, 
                { data: "helper_name", name: "helper_name"},
                { data: "garbage", name: "garbage"},
                { data: "percent", name: "percent"},
                { data: "wip_disbursed_in_kg", name: "wip_disbursed_in_kg"},
                { data: "wip_percent", name: "wip_percent"},
                { data: "total_garbage", name: "total_garbage"},
                { data: "remarks", name: "remarks"},
                { data: "verify_by", name: "verify_by"},
                { data: "verify_date", name: "verify_date"},
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

                },
            ],
            initComplete: function () {
                addFilter('postsTable',[0]);
            }, 

        });

        $("#garbageUpdateForm").validate({
            rules: {
                id: {
                    required: true,
                },
                remarks: {
                    required: true,
                    // minlength:10,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                showConfirmDialog("Are sure to submit??",garbageUpdateModal);
            }
        });
    });

    function searchData(){
        $('#postsTable').DataTable().ajax.reload(function(){
            addFilter('postsTable',[0]);
        },false);
        
    }
</script>

@include("layout.footer")