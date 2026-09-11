@include("layout.header")
<style>
    #ordersTable td:last-child,
    #ordersTable th:last-child {
        text-align: left;
    }

</style>
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Order</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Order Status</li>
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
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="fromDate">From Date</label>
                            <input type="date" name="fromDate" id="fromDate" class="form-control" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}" />
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="uptoDate">Upto Date</label>
                            <input type="date" name="uptoDate" id="uptoDate" class="form-control" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}" />
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-sm-3">
                        <input type="button" id="btn_search" class="btn btn-primary w-100" onclick="searchData()" value="Search"/>
                    </div>
                </div>
            </form>

        </div>

        <div class="panel-body">
            <table id="ordersTable" class="table table-striped table-bordered text-center table-fixed">
                <thead class="table-dark">
                    <tr>
                        <th>Order No</th>
                        <th>Client Name</th>
                        <th>Order Date</th>
                        <th>Dispatch Date</th>
                        <!-- <th>Status</th> -->
                        <th style="width: 100%;">Flowchart</th>
                    </tr>
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</main>

@include("layout.footer")

<!-- Load jQuery -->
 <!-- Load Mermaid (Non-Vite Setup) -->
<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>

<script>
    $(document).ready(function () {  
        mermaid.initialize({ startOnLoad: false });

        let table = $("#ordersTable").DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{ route('order.status') }}", // The route where you're getting data from
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
                { data: "order_no", name: "order_no" },
                { data: "client_name", name: "client_name" },
                { data: "order_date", name: "order_date" },
                { data: "estimate_delivery_date", name: "estimate_delivery_date" },
                // { data: "status", name: "status", orderable: false, searchable: false },
                { data: "flowchart", name: "flowchart", orderable: false, searchable: false }
            ],
            createdRow: function(row, data, dataIndex) {
                $('td', row).eq(4).attr({
                    "class": "flowchart-cell",
                    "data-order-id": data.id,
                }).css('max-width', '200px').css('word-break', 'break-word');

                // $('td', row).eq(4).attr({
                //     "class": "status-cell",
                //     "data-order-id": data.id,
                //     "style": "cursor: pointer;"
                // });                
                
            },
            drawCallback: function () {
                mermaid.init(); // Reinitialize Mermaid on DataTable redraw
                $(".status-cell").each(function () {
                    // $(this).click();
                });
            },
            initComplete: function () {
                addFilter('ordersTable',[$('#ordersTable thead tr:nth-child(1) th').length - 1]);
            }, 

        });

        // Toggle flowchart on clicking status
        $(document).on("click", ".status-cell1", function () {
            let orderId = $(this).data("order-id");
            let flowchartCell = $(".flowchart-cell[data-order-id='" + orderId + "']");

            if (flowchartCell.is(":visible")) {
                flowchartCell.animate({ height: "toggle", opacity: 0 }, 500, function () {
                    $(this).hide();
                });
            } else {
                flowchartCell.show().css({ opacity: 0 }).animate({ opacity: 1 }, 500, function () {
                    mermaid.init(undefined, flowchartCell.find(".mermaid")); // Ensure Mermaid renders properly
                });
            }
        });
    });

    function searchData() {
        $('#ordersTable').DataTable().ajax.reload(function(){
            addFilter('ordersTable',[$('#ordersTable thead tr:nth-child(1) th').length - 1]);
        }, false);
    }
</script>


