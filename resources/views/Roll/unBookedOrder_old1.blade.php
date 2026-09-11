@include("layout.header")
<!-- Main Component -->


<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Order</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Un-Book Order</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>            
        </div>
        <div class="panel-body">
            <form id="searchForm">
                <div class="row g-3">
                    <!-- From Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="fromDate">From Date</label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}" />
                        </div>
                    </div>

                    <!-- Upto Date -->
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="uptoDate">Upto Date</label>
                            <input type="date" name="uptoDate" id="uptoDate" class="form-control" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}" />
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="orderNo">Order No</label>
                            <input type="text" name="orderNo" id="orderNo" class="form-control"  />
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
            <table id="postsTable" class="table table-striped table-bordered text-center">
                <thead>
                    <tr>
                        <th >#</th>
                        <th>Order No</th>
                        <th>Booking Date</th>
                        <th>Client Name</th>
                        <th>Estimate Delivery Date</th>
                        <th>Bag Type</th>
                        <th>Bag Unit</th>
                        <th>Bag Color</th>
                        <th>Roll No</th>
                        <th>Is Delivered</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <x-roll-booking />
</main>
<script>
    
    $(document).ready(function() {        
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{route('order.unbook')}}", // The route where you're getting data from
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
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false },
                { data: "order_no", name: "order_no" },
                { data: "created_at", name: "created_at" },
                { data: "client_name", name: "client_name" },
                { data: "estimate_delivery_date", name: "estimate_delivery_date" },
                { data: "bag_type", name: "bag_type" },
                { data: "bag_unit", name: "bag_unit" },
                { data: "printing_color", name: "printing_color" },
                { data: "roll_no", name: "roll_no" },
                { data: "is_delivered", name: "is_delivered" },
                
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
                extend: 'csvHtml5', // Correct button extension for exporting to CSV
                text: 'Export to Excel', // Button label
                className: 'btn btn-success', // Custom button class
            }],                        
            // initComplete: function () {
            //     addFilter('postsTable',[0]);
            // },
        });

    });
    function searchData(){
        $('#postsTable').DataTable().ajax.reload();
    }
    

</script>
@include("layout.footer")