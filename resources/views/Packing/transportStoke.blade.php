@include("layout.header")
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item fs-6"><a href="#">Stock</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Transport</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container"> 
        <div class="panel-body">
            <form action="" id="transportForm" >
                <div class="row">
                    
                    <div class="row mt-3">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="transPortType">Transport Type</label>
                                <select type="text" id="transPortType" name="transPortType" class="form-select" required onchange="setStatus()">
                                    <option value="">Select</option>
                                    <option value="For Delivery">For Delivery</option>
                                    <option value="For Godown">For Godown</option>
                                </select>
                                <input type="hidden" name="status" id="status" required/>
                                <span class="error-text" id="transPortType-error"></span>
                            </div>
                        </div> 
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="dispatchedDate">Dispatch Date<span class="text-danger">*</span></label>
                                <input type="date" name="dispatchedDate" id="dispatchedDate" class="form-control" max="{{date('Y-m-d')}}" required />
                                <span class="error-text" id="dispatchedDate-error"></span>
                            </div>
                        </div>                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="invoiceNo">Chalan NO.<span class="text-danger">*</span></label>
                                <input type="text" name="invoiceNo" id="invoiceNo" class="form-control" required />
                                <span class="error-text" id="invoiceNo-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-4 billDiv">
                            <div class="form-group">
                                <label class="form-label" for="billNo">Bill No.<span class="text-danger">*</span></label>
                                <input type="text" name="billNo" id="billNo" class="form-control"  />
                                <span class="error-text" id="billNo-error"></span>
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
                        <div class="col-sm-4 billDiv">
                            <div class="form-group">
                                <label class="form-label" for="transporterId">Transporter<span class="text-danger">*</span></label>
                                <select name="transporterId" id="transporterId" class="form-select"  required >
                                    <option value="">select</option>
                                    @foreach($transporterList as $val)
                                        <option value="{{$val->id}}">{{$val->transporter_name}}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="transporterId-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <table class="table table-striped table-bordered table-responsive table-fixed" id="transportTable">
                            <thead>
                                <th>#</th>
                                <th onclick="selectAllCheck()">Select All</th>
                                <th>Packing No</th>
                                <th>Packing Date</th>
                                <th>Client Name</th>
                                <th>Bag Type</th>
                                <th>Bag Unit</th> 
                                <th>Bag Color</th>                      
                                <th>Bag Weight</th>
                                <th>Bag Piece</th>
                                <th>Bag Size</th>
                            </thead>
                            <tbody id="bagTableBody">
                            </tbody>
                        </table>
                    </div>
                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success" id="transport">Transport</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
    let sl = 0;
    var flag = window.location.pathname.split('/').pop();
    $(document).ready(function(){

        const table = $('#transportTable').DataTable({
            processing: true,
            serverSide: false,
            searching:false,
            ajax: {
                url: "{{route('packing.transport.stock',':flag')}}".replace(':flag', flag), // The route where you're getting data from                
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
                { data: "id", name: "check",orderable: false, searchable: false,render:function(row,type,data){ return `<input type='checkbox' name='bag[][id]' value='${data.id}' onclick="showHideSubmit()" />`} },
                { data: "packing_no", name: "packing_no" },
                { data: "packing_date", name: "packing_date" },
                { data: "client_name", name: "client_name" },
                { data : "bag_type", name: "bag_type" },
                { data: "units", name: "units" },
                { data: "bag_color", name: "bag_color" },
                { data: "packing_weight", name: "packing_weight" },
                { data: "packing_bag_pieces", name: "packing_bag_pieces" },
                { data: "bag_size", name: "bag_size",render: function(item) {  return `<pre>${item}</pre>`; } },
            ],
            dom: 'lBfrtip', // This enables the buttons
            language: {
                lengthMenu: "Show _MENU_" // Removes the "entries" text
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // The internal values
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"] // The display values, replace -1 with "All"
            ],
            buttons: [],          
            initComplete: function () {
                addFilter('transportTable',[0,1]);
            },     
        });

        $("#transportForm").validate({
            rules: {
                "packing[][id]": {
                    required: true,
                },
                "transPortType":{
                    required:true,
                },
                "invoiceNo":{
                    required:true,
                },                
                "billNo":{
                    required: function (element) {
                        return $("#transPortType").val() == "For Delivery"; // Adjusted logic for checking "transPortType"
                    },
                },
                status:{
                    required:true
                }
            },
            submitHandler: function(form) { 
                return addInTransport();
            }
        });
        $("#transport").hide();
        setStatus();
    });

    function setStatus(){
        const deliveryType = $("#transPortType").val();
        $("#status").val(deliveryType);
        $("#transPortType").val(deliveryType);
        if(deliveryType=="For Delivery"){
            $(".billDiv").show();
        }
        else{
            $(".billDiv").hide();
            $("#invoiceNo").val("");
        }
    }

    let selectAll = false;

    function selectAllCheck(){
        selectAll = !selectAll;
        $('input[name="bag[][id]"]').prop('checked', selectAll);
        showHideSubmit();
    }

    function showHideSubmit(){
        if ($('input[name="bag[][id]"]:checked').length > 0) {
            $('#transport').show();
        } else {
            $('#transport').hide();
        }
    }

    function checkCheckBox(){
        var selectitem = false;
        $('input[name="bag[][id]"]').each(function() { 
            if ($(this).is(':checked') && !selectitem) {
                selectitem = true;
            }
        });
        return;
    }

    function addInTransport(){
        var selectitem = false;
        $('input[name="bag[][id]"]').each(function() { 
            if ($(this).is(':checked') && !selectitem) {
                selectitem = true;
            }
        });
        if (!selectitem) {
            popupAlert('Pleas Add Attlist One Bag');
            return false;
        }
        $("#transPortType").attr("disabled",false);
        $.ajax({
            url:"{{route('packing.transport.save')}}",
            type:"post",
            data:$("#transportForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
                $("#transPortType").attr("disabled",true);
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data.status){
                    $("#transportForm").get(0).reset();
                    $("#transport").hide();
                    $("#transPortType").attr("disabled",true);
                    $("#transportTable").DataTable().ajax.reload(function(){
                        addFilter('transportTable',[0]);
                    },false);
                    modelInfo(data.messages);
                    setHintDefault();
                }else{
                    console.log(data);
                    modelInfo("Internal Server Error","warning");
                }
            },
            error:function(errors){
                console.log(errors);
                $("#loadingDiv").hide();
                modelInfo("server error","error")
            }
        })
    }
</script>
@include("layout.footer")