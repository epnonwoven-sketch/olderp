@include("layout.header")

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Godown</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="container">        
        <div class="panel-body">            
            <table id="postsTable" class="table table-striped table-bordered" >
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Transport Date</th>
                        <th>Auto</th>
                        <th>Chalan No</th>
                        <th>Total Bag</th>
                        <th>Total Un-Verify Bag</th>
                        <th>Action</th>
                    </tr>                    
                </thead>
                <tbody>
                    
                </tbody>
            </table>
        </div>
    </div>
</main>
<x-bag-godown-receiving />
<script>
    let godownTypeId = <?=$godownTypeId??0;?>;
    $(document).ready(function(){
        const table = $("#postsTable").DataTable({
            processing: true,
            serverSide: false,
            ajax: {
                url: "{{route('packing.godown.reiving',':godownTypeId')}}".replace(':godownTypeId', godownTypeId), // The route where you're getting data from
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
                { data: "transport_date", name: "transport_date" },
                { data: "auto_name", name: "auto_name" },
                { data: "invoice_no", name: "invoice_no" },
                { data: "total_bag", name: "total_bag" },
                { data: "total_unverified_bag", name: "total_unverified_bag" },                
                { data: "action", name: "action", orderable: false, searchable: false },
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
                extend: 'csv',
                text: 'Export to Excel',
                className: 'btn btn-success',                
            }],
            createdRow: function(row, data, dataIndex) {
                // Apply the custom class to the row
                $(row).attr("data-item",JSON.stringify(data));
            }, 
        });
    });


    function openReceivingModel(id) {
        $.ajax({
            url: "{{route('packing.godown.transport')}}",
            type: "post",
            dataType: "json",
            data: { id: id },
            beforeSend: function () {
                $("#loadingDiv").show();
            },
            success: function (data) {
                $("#loadingDiv").hide();
                if (data?.status && data?.data.length > 0) {
                    $("#receivingModel").modal("show");
                    $("#transportDtl tbody").empty();
                    data.data.forEach((item, index) => {
                        const tr = $("<tr>");
                        tr.attr("id", 'tr_'+item.id);
                        tr.append(
                            `<td>${index + 1}</td>`,
                            `<td title="${item.packing_no}">${item.client_name}</td>`,
                            `<td>${item.bag_size}</td>`,
                            `<td>${item.bag_type}</td>`,
                            `<td>${item.bag_color}</td>`,
                            `<td>${item.packing_weight}</td>`,
                            `<td><button data-item='${JSON.stringify(item)}' id="${item.id}" onclick="verifyBag('${item.id}')" class="btn btn-sm btn-info">Verify</button></td>`
                        );
                        $("#transportDtl tbody").append(tr);
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                $("#loadingDiv").hide();
            },
        });
    }

    function verifyBag(bagId){
        $.ajax({
            url:"{{route('packing.godown.add')}}",
            type:"post",
            data:{"id":bagId,godownTypeId:godownTypeId},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data?.status){
                    modelInfo(data.message);
                    removeTr(bagId);
                }else{
                    modelInfo("Server Error","error");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
                $("#loadingDiv").hide();
                modelInfo("Server Error","error");
            },
        });
        removeTr("tr_"+bagId);
    }

    function removeTr(id) {
        $("#" + id).remove();
        if ($("#transportDtl tbody tr").length === 0) { // Check if there are no rows in the tbody
            $("#receivingModel").modal("hide");
            $("#postsTable").DataTable().ajax.reload(function(){
                addFilter('postsTable',[0]);
            },false); // Reload the DataTable
        }

    }

</script>
@include("layout.footer")
