<!-- Modal Form -->
 
<div class="modal fade modal-lg" id="rollModal" tabindex="-1" aria-labelledby="rollModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rollModalLabel">Add New Roll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rollForm">
                    @csrf
                    <!-- Hidden field for roll ID -->
                    <input type="hidden" id="id" name="id" value="">

                    <div class="row">
                                                
                        <!-- Vendor Name -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="venderId">Vender Name <span class="text-danger">*</span></label>
                                <select name="venderId" id="venderId" class="form-control" required onchange="getRollQuality()">
                                    <option value="">Select</option>
                                    @foreach ($vendorList as $val)
                                        <option value="{{ $val->id }}">{{ $val->vendor_name }}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="venderId-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Vendor Email -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="purchaseDate">Purchase Date<span class="text-danger">*</span></label>
                                <input type="date" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" id="purchaseDate" name="purchaseDate" class="form-control" required />
                                <span class="error-text" id="purchaseDate-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="qualityId">Roll Quality<span class="text-danger">*</span></label>
                                <select id="qualityId" name="qualityId" class="form-select"required >
                                    <option value="">select</option>
                                </select>
                                <span class="error-text" id="size-error"></span>
                            </div>
                        </div>
                        <!-- Vendor Mobile Number -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="size">Roll Size<span class="text-danger">*</span></label>
                                <input type="text" id="size" name="size" class="form-control" placeholder="Roll Size" required onkeypress="return isNumDot(event);">
                                <span class="error-text" id="size-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Vendor Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="gsm">Roll GSM<span class="text-danger">*</span></label>
                                <input id="gsm" name="gsm" class="form-control" placeholder="Roll GSM" required onkeypress="return isNumDot(event);"/>
                                <span class="error-text" id="gsm-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="rollColor">Roll Color<span class="text-danger">*</span></label>
                                <select name="rollColor" id="rollColor" class="form-control" required>
                                    <option value="" >Select</option>
                                    @foreach ($rollColor as $val)
                                        <option value="{{ $val->color }}">{{ $val->color }}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="rollColor-error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <!-- Vendor Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="length">Roll Length <span style="font-size:small;color:aquamarine">(In Meter)</span><span class="text-danger">*</span></label>
                                <input id="length" name="length" class="form-control" placeholder="Roll Length" required onkeypress="return isNumDot(event);"/>
                                <span class="error-text" id="length-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="netWeight">Net Weight <span style="font-size:small;color:aquamarine">(In Kg)</span><span class="text-danger">*</span></label>
                                <input id="netWeight" name="netWeight" class="form-control" placeholder="Roll Net Weight" required onkeypress="return isNumDot(event);"/>
                                <span class="error-text" id="netWeight-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <!-- Vendor Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="grossWeight">Gross Weight <span style="font-size:small;color:aquamarine">(In Meter)</span><span class="text-danger">*</span></label>
                                <input id="grossWeight" name="grossWeight" class="form-control" placeholder="Roll Gross Weight" required onkeypress="return isNumDot(event);"/>
                                <span class="error-text" id="grossWeight-error"></span>
                            </div>
                        </div>
                        <!-- <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="clientDetailId">
                                    Book For Client 
                                    <span onclick="openClineModel()"  style="font-weight: bolder; font-size:small; text-decoration: underline;"> 
                                        <i class="bi bi-person-add"></i> Add Client
                                    </span>
                                </label>
                                <select name="clientDetailId" id="clientDetailId" class="form-control " onchange="openCloseClientMode()">
                                    <option value="" >Select</option>
                                    @foreach ($clientList as $val)
                                        <option value="{{ $val->id }}">{{ $val->client_name }}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="clientDetailId-error"></span>
                            </div>
                        </div>                         -->
                    </div>
                    <div  client="client" style="display: none;">
                        <div class="row mt-3">
                            <!-- Vendor Address -->
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="control-label" for="estimatedDespatchDate">Dispatch Date</label>
                                    <input type="date" min="{{date('Y-m-d')}}" name="estimatedDespatchDate" id="estimatedDespatchDate" class="form-control" required/>                                  
                                    <span class="error-text" id="estimatedDespatchDate-error"></span>
                                </div>
                            </div>                        
                        </div>
                        
                        <div class="row mt-3">                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="bookingBagTypeId">Bag Type </label>
                                    <select name="bookingBagTypeId" id="bookingBagTypeId" class="form-select">
                                        <option value="">Select</option>
                                        @foreach ($bagType as $val)
                                            <option value="{{ $val->id }}">{{ $val->bag_type }}</option>
                                        @endforeach
                                    </select>                                                                       
                                    <span class="error-text" id="bookingBagTypeId-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="bookingBagUnits">Bag Unit</label>
                                    <select name="bookingBagUnits" id="bookingBagUnits" class="form-select">
                                        <option value="">Select</option>
                                        <option value="Kg">Kg</option>
                                        <option value="Piece">Piece</option>
                                    </select>                                    
                                    <span class="error-text" id="bookingBagUnits-error"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="l">L </label>
                                    <input name="l" id="l" class="form-control" onkeypress="return isNumDot(event);" required />                                                                    
                                    <span class="error-text" id="l-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="w">W</label>
                                    <input name="w" id="w" class="form-control" onkeypress="return isNumDot(event);" required />                                
                                    <span class="error-text" id="w-error"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="g">G </label>
                                    <input name="g" id="g" class="form-control" onkeypress="return isNumDot(event);" required />                                                                    
                                    <span class="error-text" id="g-error"></span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="bookingPrintingColor">Printing Color</label>
                                    <div class="col-md-12">
                                        <select name="bookingPrintingColor[]" id="bookingPrintingColor" class="form-select" multiple="multiple" required> 
                                            <option value="">Select</option>                                     
                                            @foreach($color as $val)
                                            <option data-color="{{$val->color}}" value="{{$val->color}}" style="background-color:{{$val->color}};">{{$val->color}}</option>
                                            @endforeach
                                        </select>
                                    </div>                                                                                                          
                                    <span class="error-text" id="bookingPrintingColor-error"></span>
                                </div>
                            </div>
                            
                        </div>    
                    </div>

                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success">Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<x-client-form/>
<script>
    $(document).ready(function(){
        $('.select2').select2();
        $('#bookingPrintingColor').select2({
            placeholder: "Select tags",
            allowClear: true,
            dropdownParent: $('#rollModal'),
            templateResult: formatOption,
            templateSelection: formatOption 
        });
        openCloseClientMode();
        $('#clientModal').on('hidden.bs.modal', function() {
            $('#rollModal').css("z-index","");
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

    function openClineModel(){
        $('#rollModal').css("z-index",0);
        $('#clientModal').css("z-index",1060);
        $('#clientModal').modal('show');
    }

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
                        var newOptionValue = data?.data?.client?.id;
                        var clientName = data?.data?.client?.client_name;
                        if (newOptionValue !== "") {
                            // Check if the option already exists
                            if ($('#clientDetailId option[value="' + newOptionValue + '"]').length === 0) {
                                // Add the new option to the select list
                                $('#clientDetailId').append('<option value="' + newOptionValue + ' selected ">' + clientName + '</option>');                                
                            } 
                        } 
                        modelInfo(data.messages);
                    }else{
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        ) 
    } 

    function openCloseClientMode(){
        forClientId = $("#clientDetailId").val();
        if(forClientId!="" && forClientId!=undefined){
            $("div[client='client']").show();
        }
        else{
            $("div[client='client']").hide();
        }
    }

    function formatOption(option) {
        if (!option.id) {
            return option.text; // return default option text if no ID
        }
        var color = $(option.element).data('color');
        return $('<span style="background-color: ' + color + '; padding: 3px 10px; color: white; border-radius: 3px;">' + option.text + '</span>');
    }

    function getRollQuality(){
        let venderId = $("#venderId").val();
        $.ajax({
            url:"{{route('master.quality.vender.map.list',':vendorId')}}".replace(':vendorId', venderId),
            type:"get",
            dataType:"json",
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data?.status){
                    $("#qualityId").empty();  
                    let option="<option value=''>select</option>";
                    data?.data.forEach((item)=>{
                        option+=`<option value="${item?.id}">${item?.quality}</option>`;
                    });
                    $("#qualityId").append(option);
                }
            },
            error: function (error) {
                $("#loadingDiv").hide();
                console.log(error);
            }

        });
    }
</script>