@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Bag</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">Bag List</h5>
            <div class="panel-control">
                <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#bagModal" onclick="resetModelForm()">
                    Add <ion-icon name="add-circle-outline"></ion-icon>
                </button>
            </div>
        </div>
    </div>
    <div class="container">
        <table id="postsTable" class="table table-striped table-bordered table-fixed">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bag Type</th>
                    <th>GSM Variation</th>
                    <th>Roll Finding</th>
                    <th>Finding Roll By Weight</th>
                    <th>Finding Roll size</th>
                    <th>Bag Weight</th>
                    <th>cylinder size</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <x-bag-form />
</main>

<script>
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('bag.list')}}",
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "bag_type",
                    name: "bag_type"
                },
                {
                    data: "gsm_variation",
                    name: "gsm_variation"
                },
                {
                    data: "roll_find",
                    name: "roll_find"
                },
                {
                    data: "roll_find_as_weight",
                    name: "roll_find_as_weight"
                },
                {
                    data: "roll_size_find",
                    name: "roll_size_find"
                },                
                {
                    data: "weight_of_bag_per_piece",
                    name: "weight_of_bag_per_piece"
                },
                {
                    data: "cylinder_size",
                    name: "cylinder_size"
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                },
            ],
            createdRow: function(row, data, dataIndex) {
                row.style.fontSize = "x-small";
            },
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
                    text: '<i class="bi bi-file-earmark-excel-fill text-success"></i>',
                    className: 'btn btn-success',
                    action: function () {
                        let dt = $('#postsTable').DataTable();
                        let ajaxUrl = dt.ajax.url(); 
                        let params = dt.ajax.params();

                        let columns = [];
                        let headings = [];


                        dt.columns().every(function () {
                            const col = this;
                            const settings = col.settings()[0].aoColumns[col.index()];
                            const colData = settings.data;

                            if (col.visible() && colData && colData !== 'action' && colData !== 'DT_RowIndex') {
                                columns.push(colData);
                                const thText = $(col.header()).text().trim();
                                headings.push(thText);

                            }
                        });

                        params.export = 'excel';
                        params.export_columns = JSON.stringify(columns);
                        params.export_headings = JSON.stringify(headings); 

                        // Now trigger an AJAX call to export and handle download
                        $.ajax({
                            url: ajaxUrl,
                            method: 'GET',
                            data: params,
                            xhrFields: {
                                responseType: 'blob' // Important: receive binary
                            },
                            success: function (blob, status, xhr) {
                                const filename = xhr.getResponseHeader('Content-Disposition')
                                    ?.split('filename=')[1]
                                    ?.replace(/['"]/g, '') || 'auto-list.xlsx';

                                const url = window.URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.href = url;
                                a.download = filename;
                                document.body.appendChild(a);
                                a.click();
                                a.remove();
                                window.URL.revokeObjectURL(url);
                            },
                            error: function (xhr) {
                                alert('Export failed!');
                            }
                        });
                    }
                }
            ],
        });
        $('button[data-bs-target="#bagModal"]').on("click",()=>{
            $("#bagForm").get(0).reset();
        });

        $("#bagForm").validate({
            rules: {
                clientName: {
                    required: true,
                    minlength: 3
                },

                clientMobileNo: {
                    required: true,
                    number: true,
                    minlength:10,
                    minlength:10
                },
                clientAddress: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addBag();
            }
        });
    });
    function addBag(){
        $.ajax({
                type: "POST",
                'url':"{{route('bag.add')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#bagForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        $("#bagForm").get(0).reset();
                        $("#bagModal").modal('hide');
                        $('#postsTable').DataTable().draw();
                        modelInfo(data.messages);
                    }else{
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        ) 
    }   

    function openModelEdit(id){
        $.ajax({
            type:"get",
            url: "{{ route('bag.edit', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    bagDtl = data.data;
                    console.log(bagDtl); 
                    $("#id").val(bagDtl?.id);
                    $("#bagType").val(bagDtl?.bag_type);
                    $("#gsmVariation").val(bagDtl?.gsm_variation);
                    $("#rollFind").val(bagDtl?.roll_find);
                    $("#rollFindAsWeight").val(bagDtl?.roll_find_as_weight);
                    $("#rollSizeFind").val(bagDtl?.roll_size_find);
                    $("#weightOfBagPerPiece").val(bagDtl?.weight_of_bag_per_piece);
                    $("#cylinderSize").val(bagDtl?.cylinder_size);
                    $("#bagModal").modal("show");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function resetModelForm(){
        $("#id").val("");
    }

</script>

@include("layout.footer")