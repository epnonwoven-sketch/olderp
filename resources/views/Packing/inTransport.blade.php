@include("layout.header")
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Transport</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">        
        <div class="panel-body">
            <form id="searchForm">
                @csrf
                <div class="row mt-3"> 
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="packingStatus">Transport Type</label>
                            <select type="text" id="packingStatus" name="packingStatus" class="form-select" onchange="setStatus()">
                                <option value="">Select</option>
                                <option value="For Delivery">For Delivery</option>
                                <option value="For Godown">For Godown</option>
                            </select>
                            <input type="hidden" name="status" id="status" />
                            <span class="error-text" id="packingStatus-error"></span>
                        </div>
                    </div>                   
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="packingNo">Packing No</label>
                            <input type="text" id="packingNo" name="packingNo" class="form-control">
                            <span class="error-text" id="packingNo-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-4 d-flex align-items-end">
                        <button type="button" id="search" class="btn btn-primary w-100">Search</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="panel-body">
            <form action="" id="transportForm" >
                <input type="hidden" name="transPortType" id="transPortType" />
                <div class="row">
                    <div class="row mt-3">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="dispatchedDate">Date<span class="text-danger">*</span></label>
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
                        <div class="col-sm-4" id="billDiv">
                            <div class="form-group">
                                <label class="form-label" for="billNo">Bill No.<span class="text-danger">*</span></label>
                                <input type="text" name="billNo" id="billNo" class="form-control"  />
                                <span class="error-text" id="billNo-error"></span>
                            </div>
                        </div> 
                    </div>
                    <div class="row mt-3">
                        <table class="table table-striped table-bordered table-responsive" id="transportTable">
                            <thead>
                                <th>Packing No</th>
                                <th>Packing Date</th>
                                <th>Client Name</th>
                                <th>Bag Type</th>
                                <th>Bag Unit</th> 
                                <th>Bag Color</th>                      
                                <th>Bag Weight</th>
                                <th>Bag Piece</th>
                                <th>Bag L</th>
                                <th>Bag W</th>
                                <th>Bag G</th>
                                <th>Remove</th>
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
    $(document).ready(function(){
        // Handle Search Button Click
        $("#search").on("click", function () {
            if (packingNo != "") {
                $.ajax({
                    url: "{{ route('packing.transport.search') }}",
                    type: "POST",
                    dataType: "json",
                    data: $("#searchForm").serialize(),
                    beforeSend: function () {
                        $("#loadingDiv").show();
                    },
                    success: function (data) {
                        $("#loadingDiv").hide();
                        if (data.status && data.data) {
                            let item = data.data;
                            const existingRow = $(`#transportTable tbody tr[data-id="${item.id}"]`);
                            if (existingRow.length > 0) {
                                modelInfo("This item is already added.","warning");
                                return; // Exit the function if the item already exists
                            }
                            console.log(item);
                            $("#transport").show();
                            $("#packingStatus").attr("disabled",true);
                            $("#packingNo").val("");
                            const rowId = `row${sl}`;

                            const tr = $("<tr>")
                            .attr("data-id", item.id)
                            .addClass((item.stock=="stock" ?"table-info" : "table-danger"))
                            .append(
                                        `<td>${item.packing_date} <input type='hidden' name='packing[${item.id}][id]' value='${item.id}' /></td>`,
                                        `<td>${item.packing_no}</td>`,
                                        `<td>${item.client_name || "N/A"}</td>`,
                                        `<td>${item.bag_type || "N/A"}</td>`,
                                        `<td>${item.bag_unit || "N/A"}</td>`,
                                        `<td>${item.printing_color || "N/A"}</td>`,
                                        `<td>${item.packing_weight || "N/A"}</td>`,
                                        `<td>${item.packing_bag_pieces || "N/A"}</td>`, 
                                        `<td>${item.l || "N/A"}</td>`, 
                                        `<td>${item.w || "N/A"}</td>`, 
                                        `<td>${item.g || "N/A"}</td>`,                             
                                        `<td><span onclick='removeTr(this)' class='btn btn-sm btn-warning'>X</span></td>`,
                                    );
                            $("#transportTable tbody").append(tr);
                            sl = sl+1;
                        } else {
                            modelInfo(data.messages || "Invalid Roll No", "warning");
                        }
                    }
                });
            }
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
                }
            },
            submitHandler: function(form) { 
                return addInTransport();
            }
        });
        $("#transport").hide();
    });

    function setStatus(){
        const deliveryType = $("#packingStatus").val();
        $("#status").val(deliveryType);
        $("#transPortType").val(deliveryType);
        if(deliveryType=="For Delivery"){
            $("#billDiv").show();
        }
        else{
            $("#billDiv").hide();
            $("#invoiceNo").val("");
        }
    }

    function removeTr(element) {
        $(element).closest("tr").remove();
        if ($("input[type='hidden'][name^='packing']").length === 0){
            $("#transport").hide();            
            $("#packingStatus").attr("disabled",false);
        }
    }

    function addInTransport(){
        if ($("input[type='hidden'][name^='packing']").length === 0) {
            popupAlert('Pleas Add Attlist One Bag');
            return false;
        }
        $.ajax({
            url:"{{route('packing.transport.save')}}",
            type:"post",
            data:$("#transportForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data.status){
                    $("#transportForm").get(0).reset();
                    $("#transport").hide();
                    $("#packingStatus").attr("disabled",false);
                    $("#transportTable tbody").empty();
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