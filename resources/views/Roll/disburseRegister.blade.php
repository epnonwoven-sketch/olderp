@include("layout.header")
<!-- Main Component -->


<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Order</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Disbursed</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>            
        </div>
        
        <div class="panel-body">
            <table id="postsTable" class="table table-striped table-bordered text-center table-fixed">
                <thead>
                    <tr>
                        <th >#</th>
                        <th>Booking Date</th>
                        <th>Estimate Delivery Date</th>
                        <th>Client Name</th>
                        <th>Bag Type</th>
                        <th>Bag Color</th>
                        <th>Bag Size</th>
                        <th>Order Qty</th>
                        <th>Booked Qty</th>
                        <th>Disbursed Qty</th>
                        <th>Bag Unit</th>
                        <!-- <th>Roll No</th> -->
                        <th>Disbursed By</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</main>
<script>
    
    $(document).ready(function() {        
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            // searching:false,
            ajax: {
                url: "{{route('order.disabused.register')}}", // The route where you're getting data from
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
                // { data: "order_no", name: "order_no" },
                { data: "created_at", name: "created_at" },
                { data: "estimate_delivery_date", name: "estimate_delivery_date" },
                { data: "client_name", name: "client_name" },
                { data: "bag_type", name: "bag_type" },
                { data: "bag_color", name: "bag_color" },
                { data: "bag_size", name: "bag_size",render: function(item) {  return `<pre>${item}</pre>`; } },
                { data: "total_units", name: "total_units" },
                { data: "booked_units", name: "booked_units" },
                { data: "disbursed_units", name: "disbursed_units" },
                { data: "units", name: "units" },
                // { data: "roll_no", name: "roll_no" },
                { data: "name", name: "name" },
                
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
            initComplete: function () {
                addFilter('postsTable',[0,($('#postsTable thead tr:nth-child(1) th').length - 1)]);
            },
        });

    });
    function searchData(){
        $('#postsTable').DataTable().ajax.reload();
    }
  

</script>
@include("layout.footer")