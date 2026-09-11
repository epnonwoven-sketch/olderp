@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Clint</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">Clint List</h5>
            <div class="panel-control">
                <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#clientModal" onclick="resetModelForm()">
                    Add <ion-icon name="add-circle-outline"></ion-icon>
                </button>
            </div>
        </div>
    </div>
    <div class="container">
        <table id="postsTable" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Clint Name</th>
                    <th>Mobile No</th>
                    <th>Email</th>
                    <th>State</th>
                    <th>City</th>
                    <th>Address</th>
                    <th>Sector</th>
                    <th>Trade Name</th>
                    <th>Has Multiple Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <x-client-form />
</main>

<script>
    $(document).ready(function(){
        $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('client.list')}}",
            columns: [{
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "client_name",
                    name: "client_name"
                },
                {
                    data: "mobile_no",
                    name: "mobile_no"
                },
                {
                    data: "email",
                    name: "email"
                },
                {
                    data: "state_name",
                    name: "state_name",
                },
                {
                    data: "city_name",
                    name: "city_name",
                },
                {
                    data: "address",
                    name: "address",
                },
                {
                    data: "sector",
                    name: "sector",
                },
                {
                    data: "trade_name",
                    name: "trade_name",
                },
                {
                    data: "has_address_two",
                    name: "has_address_two",
                    render:function(row,type,data){return (data.has_address_two ? "Yes" :"No")}
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                },
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
        $('button[data-bs-target="#clientModal"]').on("click",()=>{
            $("#clientForm").get(0).reset();
        });

        $("#clientForm").validate({
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
                addClint();
            }
        });
    });
    function addClint(){
        $.ajax({
                type: "POST",
                'url':"{{route('client.add')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#clientForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        $("#clientForm").get(0).reset();
                        $("#clientModal").modal('hide');
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
            url: "{{ route('client.edit', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    clientDtl = data.data;
                    console.log(clientDtl); 
                    $("#id").val(clientDtl?.id);
                    $("#clientName").val(clientDtl?.client_name);
                    $("#email").val(clientDtl?.email);
                    $("#mobileNo").val(clientDtl?.mobile_no);
                    $("#address").val(clientDtl?.address);
                    $("#hasAddressTwo").attr("checked",(clientDtl?.has_address_two ? true:false));
                    $("#location").val(clientDtl?.location);
                    $("#secondaryMobileNo").val(clientDtl?.secondary_mobile_no);
                    $("#temporaryMobileNo").val(clientDtl?.temporary_mobile_no);
                    $("#stateId").val(clientDtl?.state_id).trigger("change",[clientDtl?.city_id]);
                    $("#cityId").val(clientDtl?.city_id).trigger("change");
                    $("#cityHidden").val(clientDtl?.city_id);
                    $("#sectorId").val(clientDtl?.sector_id);
                    $("#tradeName").val(clientDtl?.trade_name);
                    $("#clientModal").modal("show");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function resetModelForm(inits="0"){
        $("#id").val("");
        $("#clientForm").find("input[type=hidden]").val('');
        $("#clientForm").get(0).reset();
        $('#clientForm select').each(function() {
            if ($(this).data('select2')) {
                if(this.id=="bookingForClientId"){
                    if(inits!="1"){ 
                        $(this).val(null).trigger('change',["1"]);
                    }
                }else{
                    $(this).val(null).trigger('change');
                }
            }
        });
    }

    // function deactivate(id){
        
    // }

    function deactivate(id){
        $.ajax({
            type:"get",
            url: "{{ route('client.delete', ':id') }}".replace(':id', id),
            dataType: "json",
            data:{lock_status:true},
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    $('#postsTable').DataTable().draw();
                    modelInfo(data?.message,"success");
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

</script>

@include("layout.footer")