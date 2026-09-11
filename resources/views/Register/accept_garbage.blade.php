@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Other Register</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Accept Garbage</li>
                </ol>
            </nav>

        </div>        
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
        </div>
        <div class="panel-body">
            <table id="postsTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Roll No</th>
                        <th>Cutting Date</th>
                        <th>Operator Name</th>
                        <th>Helper Name</th>
                        <th>Shift</th>
                        <th>Roll Weight</th>
                        <th>Garbage Weight</th>
                    </tr>
                </thead>
                <tbody>
    
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <x-sector-form />
</main>

<script>
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{route('register.accept.garbage')}}",
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
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "roll_no",
                    name: "roll_no"
                },
                {
                    data: "cutting_date",
                    name: "cutting_date",
                },
                {
                    data: "operator_name",
                    name: "operator_name",
                },
                {
                    data: "helper_name",
                    name: "helper_name",
                },
                {
                    data: "shift",
                    name: "shift",
                },                
                {
                    data: "net_weight",
                    name: "net_weight",
                },                
                {
                    data: "total_qtr",
                    name: "total_qtr",
                },
            ],
        });
    });

</script>

@include("layout.footer")