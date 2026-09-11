@include("layout.header")
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Godown {{$godownTypeId}}</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>   
            <div class="panel-control">
                <a href="{{route('packing.godown.reiving',[$godownTypeId??0])}}" class="btn btn-primary btn-sm">Verify <span id="intTransPort" class="badge bg-danger"></span></a>
                <!-- <a href="{{route('packing.transport.for','For Delivery')}}" class="btn btn-warning btn-sm">Transport Bag</a> -->
                <button type="button" class="btn btn-sm btn-warning" onclick="openTransportModel('For Factory')">Factory</button>
                <button type="button" class="btn btn-sm btn-warning" onclick="openTransportModel('For Godown',<?=$godownTypeId==1?2:1?>)">Godown {{$godownTypeId==1?2:1}}</button>
                <button type="button" class="btn btn-sm btn-success" onclick="openTransportModel('For Delivery')">Client</button>
            </div>         
        </div>
        <div class="panel-body">       
            <div class="panel-control justify-content-end" >
                <strong>Total Weight:</strong> (<span id="total_weight">0</span>)
                <span class="filterSum" style="display: none;">
                    <i id="total_weight1" class="text-info"></i> 
                    </br>
                    In Pice: <i id="total_pics1" class="text-info"></i>
                </span>
            </div>     
            <table class="table table-bordered  table-responsive table-fixed" id="postsTable">
                <thead>
                    <tr>
                        <!-- <th>#</th> -->
                        <th>Receiving Date</th> 
                        <th onclick="selectAllCheck()">Packing No</th>
                        <th>Client Name</th>
                        <th>Bag Size </th>
                        <th>Bag Type</th>
                        <th>Bag Color</th> 
                        <th>Bag GSM</th>                     
                        <th>Bag Weight</th>
                        <th>Bag Piece</th>
                        <!-- <th>Bag Unit</th>  -->
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editBagModal" tabindex="-1" aria-labelledby="editBagModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBagModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="editBagForm">
                    @csrf
                    <!-- Hidden field for Client ID -->
                    <input type="hidden" id="id" name="id" value="">
                    <div class="row">
                        <div class="form-group col-md-6 packing_no">
                            <label for="packing_no" class="control-label">Packing No<span class="text-danger">*</span></label>
                            <input name="packing_no"  id="packing_no" class="form-control" required />
                            <span class="error-text" id="packing_no-error"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="packing_weight" class="control-label">Weight<span class="text-danger">*</span></label>
                            <input name="packing_weight"  id="packing_weight" class="form-control" required onkeypress="return isNum(event);"/>
                            <span class="error-text" id="packing_weight-error"></span>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="packing_bag_pieces" class="control-label">Piece<span class="text-danger" style="display:none;">*</span></label>
                            <input name="packing_bag_pieces"  id="packing_bag_pieces" class="form-control" onkeypress="return isNum(event);"/>
                            <span class="error-text" id="packing_bag_pieces-error"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitEditForm">Edit</button>
            </div>
            </div>
        </div>
    </div>

    <!-- transportModel -->
    <div class="modal fade modal-lg" id="transportModel" tabindex="-1" aria-labelledby="transportModelLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="transportModelLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" id="transportModelForm">
                        @csrf
                        <!-- Hidden field for Client ID -->
                        <div id="hiddenDiv">

                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="dispatchedDate">Dispatch Date<span class="text-danger">*</span></label>
                                    <input type="date" name="dispatchedDate" id="dispatchedDate" class="form-control" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" required />
                                    <span class="error-text" id="dispatchedDate-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="chalanNo">Chalane No<span class="text-danger">*</span></label>
                                    <input type="text" name="chalanNo" id="chalanNo" class="form-control"  required onkeypress="return isNum(event);" />
                                    <span class="error-text" id="chalanNo-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="form-label" for="autoId">Auto<span class="text-danger">*</span></label>
                                    <select name="autoId" id="autoId" class="form-select"  required >
                                        <option value="">select</option>
                                        @foreach($autoList as $val)
                                            <option value="{{$val->id}}">{{$val->auto_name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" id="autoId-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-4" id="isLocalTransportDiv">
                                <label class="form-label" for="isLocalTransport">Is Local Transport</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isLocalTransport" name="isLocalTransport" onclick="toggleTransporterDiv()" />
                                </div>                        
                            </div>
                            <div class="col-sm-4 transposerDiv">
                                <div class="form-group">
                                    <label class="form-label" for="transporterId">Transporter<span class="text-danger">*</span></label>
                                    <select name="transporterId" id="transporterId" class="form-select"  required onchange="toggleBusNoDiv()" >
                                        <option value="" data-item="">select</option>
                                        @foreach($transporterList as $val)
                                            <option value="{{$val->id}}" data-item="{{$val->is_bus}}" >{{$val->transporter_name}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" id="transporterId-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-4 transposerDiv BussNo">
                                <div class="form-group">
                                    <label class="form-label" for="busNo">Buss No<span class="text-danger">*</span></label>
                                    <input name="busNo" id="busNo" class="form-control" placeholder="Enter Bus No"/>                                        
                                    <span class="error-text" id="busNo-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-4 client">
                                <div class="form-group">
                                    <label class="form-label" for="bookingForClientId">Client Name<span class="text-danger">*</span></label>
                                    <div class="col-md-12">
                                        <select name="bookingForClientId" id="bookingForClientId" class="form-control">
                                            <option value="">Select</option>
                                            @foreach ($clientList as $val)
                                                <option value="{{ $val->id }}">{{ $val->client_name }}</option>
                                            @endforeach
                                        </select><br>
                                        <label class="error-text" id="bookingForClientId-error"></label>
                                    </div>                                       
                                    <span class="error-text" id="busNo-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-4" id="rateTypeDiv">
                                <div class="form-group">
                                    <label class="form-label" for="rateTypeId">Rate Type</label>
                                    <select name="rateTypeId" id="rateTypeId" class="form-select" >
                                        <option value="">select</option>
                                        @foreach($rateType as $val)
                                            <option value="{{$val->id}}">{{$val->rate_type}}</option>
                                        @endforeach
                                    </select>
                                    <span class="error-text" id="rateTypeId-error"></span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitTransportModal">Submit</button>
                </div>
            </div>
        </div>
    </div>
    <!-- model chalan -->
    <div class="modal fade modal-lg" id="chalanModal" tabindex="-1" aria-labelledby="chalanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chalanModalLabel">Chalan Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="pdfPreview" style="width: 100%; height: 500px; display: none;"></iframe>
            </div>
            <div class="modal-footer">
                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                <button type="button" class="btn btn-primary" id="downloadChalan">Download</button>
                <button type="button" class="btn btn-primary" id="transportBag">Transport</button>
            </div>
            </div>
        </div>
    </div>
</main>
<script>
    let godownTypeId = <?=$godownTypeId??0;?>;
    $(document).ready(function(){
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            ordering:false,
            ajax: {
                url: "{{route('packing.godown',':godownTypeId')}}".replace(':godownTypeId', godownTypeId),// The route where you're getting data from
                data: function(d) {

                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
                    });

                },
                dataSrc: function (json) {
                    $('#total_weight').text(json?.totalWeight); 
                    $('#intTransPort').text(json?.intTransPort); 
                    return json.data;
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
                { data:"reiving_date",name:"reiving_date" },
                { data: "packing_no", name: "packing_no",render: function(data, type, row, meta) {
                            const rowDataEncoded = base64Encode(JSON.stringify(row));
                            return `${row.packing_no} <i class="bi bi-info-circle-fill" data-placement="bottom" data-toggle="tooltip" title="${row.order_no}"></i> <input type="checkbox" name="checkbox[]" data-row='${rowDataEncoded}' value="${row?.id}" class="row-select checkbox" >`;
                        }
                },
                { data: "client_name", name: "client_name" },
                { data: "bag_size", name: "bag_size",render: function(item) {  return `<pre>${item}</pre>`; }},                
                { data: "bag_type", name: "bag_type" },
                { data: "bag_color", name: "bag_color" },
                { data: "bag_gsm", name: "bag_gsm" },
                { data: "packing_weight", name: "packing_weight",render:function(data, type, row, meta){return `${row.packing_weight} (Kg)`} },
                { data: "packing_bag_pieces", name: "packing_bag_pieces",render:function(data, type, row, meta){return `${row.packing_bag_pieces ? row.packing_bag_pieces +" (Pcs)" : "NA"} `} },
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
            initComplete: function () {
                addFilter('postsTable',[0,$('#postsTable thead tr:nth-child(1) th').length - 1],sum);
            },
        });
        $('#bookingForClientId').select2({
            width:"100%",
            dropdownParent: $('#transportModel'),
        }); 
        $("#submitEditForm").on("click",function(){
            $("#editBagForm").submit();
        });
        $("#editBagForm").validate({
            rules: {
                packing_weight: {
                    required: true,
                    number: true,
                },
            },
            submitHandler: function(form) {
                showConfirmDialog("Are sure want to edit??",editBagSubmit);
            }
        });
        $("#submitTransportModal").on("click",function(){
            $("#transportModelForm").submit();
        });
        $("#transportModelForm").validate({
            rules: {
                dispatchedDate: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                generateChalan();
            }
        });
    });

    function sum(table){

        let total = 0;
        let totalPice = 0;

        // Check global search
        let globalSearch = table.search();

        // Check column filters
        let columnSearch = table.columns().search().toArray();

        // Test if any filter/search applied
        let isFilterApplied = globalSearch !== '' ||
            columnSearch.some(val => val !== '');

        console.log("Filter Applied:", isFilterApplied);

        table.rows({ search: 'applied' }).every(function () {

            let data = this.data();

            total += parseFloat(data.packing_weight || 0);
            totalPice += parseFloat(data.packing_bag_pieces || 0);
        });

        console.log(total);

        $('#total_weight1').html(total.toFixed(2));
        $('#total_pics1').html(totalPice.toFixed(2));

        // Example UI
        if(isFilterApplied){
            $('.filterSum').show();
        }else{
            $('.filterSum').hide();
        }
    }

    function toggleTransporterDiv(){
        $(".transposerDiv").show();
        $("#transporterId").attr("required",true);
        if($("#isLocalTransport").is(":checked")){
            $(".transposerDiv").hide();
            $("#transporterId").val("").trigger("change");
            $("#transporterId").attr("required",false);
        }
    }

    function toggleBusNoDiv(){
        let option= $("#transporterId").find("option:selected");
        $(".BussNo").show();
        $("#busNo").attr("required",true);
        if(!option.attr("data-item")){
            $(".BussNo").hide();
            $("#busNo").attr("required",false).val("");
        }
    }

    function searchData(){
        $('#postsTable').DataTable().ajax.reload(function(){
            addFilter('postsTable',[0]);
        },false);
        
    }

    function editBag(id) {
        $.ajax({
            url: "{{ route('packing.bag.dtl', ['id' => ':id']) }}".replace(':id', id),
            type: "GET",
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success: function(response) {
                $("#loadingDiv").hide();
                $("#packing_bag_pieces").parent("label").find("span").css("display","none");
                $("#packing_bag_pieces").closest(".form-group").find("label span.text-danger").css("display", "none");
                $("#packing_bag_pieces").attr("required",false);
                $("#packing_no").attr("readonly",true);
                if(response.status && response.data.id){
                    let item = response.data;
                    $("#id").val(item.id);
                    $("#packing_no").val(item.packing_no);
                    if(!item?.packing_no){
                        $("#packing_no").attr("readonly",false);
                    }
                    $("#packing_weight").val(item.packing_weight);
                    $("#packing_bag_pieces").val(item.packing_bag_pieces);
                    if(item.units!="Kg"){
                        $("#packing_bag_pieces").closest(".form-group").find("label span.text-danger").css("display", "inline"); // Show the asterisk
                        $("#packing_bag_pieces").attr("required",true);
                    }
                    $("#editBagModal").modal("show");
                }
                else{
                    modelInfo("server error!!","warning");
                }
                console.log(response); // Handle the response here
            },
            error: function(errors) {
                $("#loadingDiv").hide();
                console.log(errors);
                modelInfo("server error!!","error");
            }
        });
    }

    function editBagSubmit(){
        $.ajax({
            url:"{{route('packing.bag.edit')}}",
            type:"post",
            dataType:"json",
            data:$("#editBagForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                if(response.status){
                    $("#editBagModal").modal("hide");
                    modelInfo(response?.message);
                    searchData();
                }else{
                    modelInfo("server Error","warning");
                }
            },
            error:function(error){
                $("#loadingDiv").hide();
                console.log(error);
                modelInfo("server Error","error");
            }
        })
    }

    function deleteBag(id){
        $.ajax({
            url:"{{route('packing.bag.delete')}}",
            type:"post",
            dataType:"json",
            data:{id:id},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                modelInfo(response.message,response.status?"success":"warning");
                if(response.status){
                    searchData();
                }
            },
            error:function(errors){
                console.log(error);
                modelInfo("server error","error");
            }
        })
    }

    function openTransportModel(transportType,godownType='') {
        let sequence = [];
        const storageType = ["For Godown", "For Factory"];

        $(".checkbox").each(function () {
            if ($(this).is(":checked")) {
                let jsonData = JSON.parse(base64Decode($(this).attr("data-row")));
                sequence.push(jsonData);
            }
        });

        // ✅ Fix: Check if sequence is empty
        if (sequence.length === 0) {
            popupAlert("Please select at least one bag");
            return;
        }
        $("#isLocalTransportDiv").show();
        if(storageType.includes(transportType)){
            $("#isLocalTransportDiv").hide();
            $("#isLocalTransport").attr("checked",true).trigger("click");
        }

        let hidden = "";
        let is_local_order=$("#isLocalTransport").is(":checked");;
        let rateType = [];
        let client=[];
        let clientId="";
        let rate = "";

        // ✅ Fix: Use `forEach` correctly
        sequence.forEach((item) => {
            
            rate=item.rate_type_id;
            clientId = item.client_detail_id;
            if (!rateType[item.rate_type_id]) {
                rateType[item.rate_type_id] = item.rate_type;
            }
            if (!client[item.client_detail_id]) {
                client[item.client_detail_id] = item.client_name;
            }

            hidden += `<input type='hidden' name="bag[][id]" value="${item.id}" />`;
        });
        hidden+=`<input type='hidden' name='rateTypeIdNew' value="${rate}" />`;
        hidden+=`<input type='hidden' id='transPortType' name='transPortType' value="${transportType}" />`;
        hidden += `<input type='hidden' name="godownTypeId" value="${godownType}" />`;

        console.log(rateType);
        if (Object.keys(rateType).length > 1 && transportType=="For Delivery") {
            popupAlert("Cannot generate different rate type Chalan");
            return;
        }
        if (Object.keys(client).length > 1 && transportType=="For Delivery") {
            popupAlert("Cannot generate Chalan for more then one client");
            return;
        }
        $(".transposerDiv").show();
        $("#transporterId").attr("required",true);
        $("#rateTypeDiv").show();
        $("#rateTypeId").val(rate);
        if(is_local_order || storageType.includes(transportType)){
            $(".transposerDiv").hide();            
            $("#transporterId").attr("required",false);
            $("#rateTypeDiv").hide();
        }
        $("#bookingForClientId").attr({"disabled":true,"required":false});
        $(".client").hide();
        if(clientId==1 && !storageType.includes(transportType)){
            $("#bookingForClientId").attr({"disabled":false,"required":true});
            $(".client").show();
        }
        $("#hiddenDiv").html(hidden);
        $("#transportModelLabel").html(transportType);
        $("#transportModel").modal("show");

    }

    function generateChalan(){
        let formData = $("#transportModelForm").serialize();
        $.ajax({
            url:"{{route('packing.generate.chalan')}}",
            type:"post",
            dataType:"json",
            data:formData,
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                console.log(response);
                if(response.status){
                    console.log("Extra Data:", response.data);
                    let chalanNo = response.data.chalan_no;
                    let pdfBase64 = response.data.pdf_base64;
                    let isDownload = false;
                    let pdfDataUri = "data:application/pdf;base64," + pdfBase64;
                    $("#pdfPreview").attr("src", pdfDataUri).show();

                    $("#downloadChalan").show().off("click").on("click", function () {
                        isDownload= true;
                        let pdfBlob = base64ToBlob(pdfBase64, "application/pdf");
                        let link = document.createElement("a");
                        link.href = URL.createObjectURL(pdfBlob);
                        link.download = chalanNo+".pdf";
                        link.click();
                    });

                    $("#transportBag").show().off("click").on("click", function () {
                        let formDataNew = new FormData($("#transportModelForm")[0]); 
                        formDataNew.append("chalanUniqueId", response.data.unique_id);
                        formDataNew.append("invoiceNo", response.data.chalan_no);

                        $.ajax({
                            url: "{{route('packing.transport.save')}}",
                            type: "post",
                            data: formDataNew,
                            dataType: "json",
                            processData: false, // 🔥 Prevents jQuery from serializing FormData
                            contentType: false, // 🔥 Ensures proper content type for FormData
                            beforeSend: function () {
                                $("#loadingDiv").show();
                                $("#transportBag").prop("disabled", true);
                            },
                            success: function (data) {
                                $("#loadingDiv").hide();

                                if (data.status) {
                                    if(!isDownload){
                                        $("#downloadChalan").click();
                                    }
                                    $("#transportModelForm").get(0).reset();
                                    $("#chalanModal").modal("hide");
                                    $("#transportModel").modal("hide");                                    
                                    $("#transportBag").prop("disabled", false);
                                    modelInfo(data.messages);
                                    searchData();
                                } else {
                                    console.log(data);
                                    modelInfo("Internal Server Error", "warning");
                                }
                            },
                            error: function (errors) {
                                console.log(errors);
                                $("#loadingDiv").hide();
                                modelInfo("Server error", "error");
                                $("#transportBag").prop("disabled", false);
                            }
                        });
                    });

                    $("#chalanModal").modal("show");
                }
            },
            error: function (xhr, status, error) {
                $("#loadingDiv").hide();
                console.error("AJAX error:", error);
                popupAlert("An error occurred while generating the PDF.");
            }

        });
    }

    function base64ToBlob(base64, mimeType) {
        let byteCharacters = atob(base64);
        let byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        let byteArray = new Uint8Array(byteNumbers);
        return new Blob([byteArray], { type: mimeType });
    }

    $("#chalanModal").on("hidden.bs.modal", function () {
        $("#transportBag").prop("disabled", false);
    });

    let selectAll = false;
    function selectAllCheck(){
        if(selectAll)
        {
            $('.row-select').prop("checked",false).trigger('change');             
            selectAll = false;
        }
        else
        {
            $('.row-select').prop("checked",true).trigger('change');;
            selectAll = true;
        }
    }
</script>
@include("layout.footer")