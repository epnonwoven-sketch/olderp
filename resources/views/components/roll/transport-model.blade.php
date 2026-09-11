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
                    <input type="hidden" name="transportStatus" value="0">
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
                                <label class="form-label" for="autoId">Auto<span class="text-danger">*</span></label>
                                <select name="autoId" id="autoId" class="form-select select2"  required >
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
                                <select name="transporterId" id="transporterId" class="form-select select2"  required onchange="toggleBusNoDiv()" >
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
                                    <select name="bookingForClientId" id="bookingForClientId" class="form-control select2">
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
                        <!-- <div class="col-sm-4" id="rateTypeDiv">
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
                        </div> -->
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
            <button type="button" class="btn btn-primary" id="transportRoll">Transport</button>
        </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $(".select2").select2({
            width:"100%",
            display:"block",
            dropdownCssClass: 'form-control',
            dropdownParent: $('#transportModel'), 
        });

        $("#submitTransportModal").on("click",function(){
            $("#transportModelForm").submit();
        });
        $("#transportModelForm").validate({
            rules: {
                dispatchedDate: {
                    required: true,
                },
                bookingForClientId:{
                    required: true,
                },
            },
            submitHandler: function(form) {
                generateChalan();
            }
        });
    });
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

    function generateChalan(){
        let formData = $("#transportModelForm").serialize();
        $.ajax({
            url:"{{route('roll.sell.generate.chalan')}}",
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

                    $("#transportRoll").show().off("click").on("click", function () {
                        let formDataNew = new FormData($("#transportModelForm")[0]); 
                        formDataNew.append("chalanUniqueId", response.data.unique_id);
                        formDataNew.append("invoiceNo", response.data.chalan_no);

                        $.ajax({
                            url: "{{route('roll.sell.selected')}}",
                            type: "post",
                            data: formDataNew,
                            dataType: "json",
                            processData: false, // 🔥 Prevents jQuery from serializing FormData
                            contentType: false, // 🔥 Ensures proper content type for FormData
                            beforeSend: function () {
                                $("#loadingDiv").show();
                                $("#transportRoll").prop("disabled", true);
                            },
                            success: function (data) {
                                $("#loadingDiv").hide();

                                if (data.status) {
                                    if(!isDownload){
                                        $("#downloadChalan").click();
                                    }
                                    $("#selectedRollId").val("");
                                    $(".select2").val(null).trigger("change");
                                    $("#transportModelForm").get(0).reset();
                                    $("#chalanModal").modal("hide");
                                    $("#transportModel").modal("hide");                                    
                                    $("#transportRoll").prop("disabled", false);
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
                                $("#transportRoll").prop("disabled", false);
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
</script>