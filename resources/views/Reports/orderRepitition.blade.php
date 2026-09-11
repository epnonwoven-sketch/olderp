@include("layout.header")

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Report</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Production</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">            
        <div class="panel-body"> 
            <table id="postTable" class="table table-responsive table-border">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client Name</th>
                        <th>Bag Quality</th>
                        <th>Bag Type</th>
                        <th>Total Units</th>
                        <th>Bag Size</th>
                        <th>Gsm</th>
                        <th>Repeated</th>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
    $(document).ready(function(){
        $("#postTable").DataTable({
            processing: true,
            serverSide: false,
            searching:true,
            ajax: {
                url: "{{route('report.repeated.order')}}", // The route where you're getting data from  
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
                { data: "client_name", name: "client_name" },
                { data: "bag_quality", name: "bag_quality" },
                { data: "bag_type", name: "bag_type" },                
                { data: "total_units", name: "total_units" },                
                { data: "bag_size", name: "bag_size" },
                { data: "bag_gsm_value", name: "bag_gsm_value" },
                { data: "count", name: "count" },
                
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
                    // exportOptions: {
                    //     columns: [0, 1,2, 3,4,5,6,7,8,9,10]  // Export only Name, Position, and Age columns
                    // }

                },
            ],  
            initComplete: function () {
                addFilter('postTable',[0]);
            },
        })
    });
</script>

@include("layout.footer")