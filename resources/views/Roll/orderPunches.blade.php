@include("layout.header")
<!-- Main Component -->
<style>
    .collapsible {
        background-color: #f1f1f1;
        border: 1px solid #ccc;
        padding: 10px;
        width: 100%;
        margin-top: 10px;
        cursor: pointer;
    }
    .collapsible .content {
        display: none;
        overflow: hidden;
        padding-top: 10px;
    }
    .collapsible button {
        background-color: transparent;
        color: #4CAF50;
        /* font-size: 24px; */
        border: none;
        cursor: pointer;
        padding: 0;
        margin: 0;
    }

</style>
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Order Punch</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">Order Punch</h5>            
        </div>
    </div>
    <div class="container">
        <div class="panel-body">

            <form action="" id="myForm" class="row g-3">                
                @csrf
                <div class="row mb-3">                    
                    <div class="row mt-3">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="bookingForClientId">Client 
                                    <span onclick="openRollBookingClineModel()"  style="font-weight: bolder; font-size:small; text-decoration: underline;"> 
                                        <i class="bi bi-person-add"></i> Add Client
                                    </span> 
                                </label>
                                <div class="col-md-12">
                                    <select name="bookingForClientId" id="bookingForClientId" class="form-control" onchange="showPrivOrder(event)" >
                                        <option value="">Select</option>
                                        @foreach ($clientList as $val)
                                            <option value="{{ $val->id }}">{{ $val->client_name }}</option>
                                        @endforeach
                                    </select><br>
                                    <label class="error-text" id="bookingForClientId-error"></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="orderDate">Order Date</label>
                                <input type="date" max="{{date('Y-m-d')}}" value="{{date('Y-m-d')}}" name="orderDate" id="orderDate" class="form-control" required/>                                  
                                 <!-- <input type="date" max="{{date('Y-m-d')}}" value="2025-04-01" name="orderDate" id="orderDate" class="form-control" required/>  -->
                                <span class="error-text" id="orderDate-error"></span>
                            </div>
                        </div>                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="form-label" for="bookingEstimatedDespatchDate">Dispatch Date</label>
                                <input type="date" min="{{date('Y-m-d')}}" name="bookingEstimatedDespatchDate" id="bookingEstimatedDespatchDate" class="form-control" value="{{date('Y-m-d',strtotime(date('Y-m-d').' 10 days'))}}" required/> 
                                <!-- <input type="date" min="{{date('Y-m-d')}}" name="bookingEstimatedDespatchDate" id="bookingEstimatedDespatchDate" class="form-control" value="2025-04-10" required/>                                   -->
                                <span class="error-text" id="bookingEstimatedDespatchDate-error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="" id="orderHistory" >
                        <div class="collapsible">                            
                            <button type="button" class="collapsible-btn"><i class="bi bi-eye-fill" class="collapsible-btn"></i></button>
                            <div class="collapsible-content content" id="history" style="overflow: scroll;">                                
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="row mb-3">
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="bookingBagTypeId">Bag Type </label>
                            <select name="bookingBagTypeId" id="bookingBagTypeId" class="form-select" onchange="showHideLoop();showAlternativeOption();getBalance()">
                                <option value="">Select</option>
                                @foreach ($bagType as $val)
                                    <option value="{{ $val->id }}">{{ $val->bag_type }}</option>
                                @endforeach
                            </select>                                                                       
                            <span class="error-text" id="bookingBagTypeId-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group" id='singleGsm'>
                            <label class="form-label" for="bagGsm">GSM</label>
                            <select name="bagGsm[]" id="bagGsm" class="form-select" multiple="multiple" onchange="showAlternativeOption()">                                    
                                @foreach($gsm as $val)
                                <option value="{{$val}}">{{$val}}</option>
                                @endforeach
                            </select>                                
                            <span class="error-text" id="bagGsm-error"></span>
                        </div>
                        <div class="form-group" id='multipleGsm' style="display: none;">
                            <label class="form-label" for="bagGsmJson">GSM</label>
                            <input name="bagGsmJson" id="bagGsmJson" class="form-control" placeholder="gsm/lamination/boop" required onkeypress="return gsmJson(event); "  onkeyup="setGsm();"/>                                 
                            <span class="error-text" id="bagGsmJson-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="bookingBagColor">Bag Color </label>
                            <div class="col-md-12">
                                <select name="bookingBagColor[]" id="bookingBagColor" class="form-select" multiple="multiple" onchange="showAlternativeOption()" > 
                                    <option value="">All</option>                                     
                                    @foreach($rollColor as $val)
                                    <option data-color="{{$val->color}}" value="{{$val->color}}">{{$val->color}}</option>
                                    @endforeach
                                </select>
                            </div>                                                                       
                            <span class="error-text" id="bookingBagColor-error"></span>
                        </div>
                    </div>
                    
                </div>

                <div class="row mb-3">                     
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="w">W</label>
                            <input name="w" id="w" class="form-control" onkeypress="return isNumDot(event);" required onchange="showAlternativeOption()" />                                
                            <span class="error-text" id="w-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <label class="form-label" for="l">L </label>
                            <input name="l" id="l" class="form-control" onkeypress="return isNumDot(event);" required onchange="showAlternativeOption()"/>                                                                    
                            <span class="error-text" id="l-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-3" id="gussetDiv">
                        <div class="form-group">
                            <label class="form-label" for="g">G </label>
                            <input name="g" id="g" class="form-control" onkeypress="return isNumDot(event);" required onchange="showAlternativeOption()" />                                                                    
                            <span class="error-text" id="g-error"></span>
                        </div>
                    </div>

                    <div class="col-sm-3" id="loopColorDiv">
                        <div class="form-group">
                            <label class="form-label" for="looColor">Loop Color</label>
                            <select name="looColor" id="looColor" class="form-control"  required onchange="testLoop()">
                                <option value="">select</option>
                                @foreach($loopColor as $val)
                                    <option value="{{$val->loop_color}}" id="{{$val->loop_color}}" data-item="{{json_encode($val)}}">{{$val->loop_color}}</option>
                                @endforeach
                            </select>
                            <!-- <input name="looColor" id="looColor" class="form-control" required />                                -->
                            <span class="error-text" id="looColor-error"></span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3"> 
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="bagQuality">Roll Configuration </label>
                            <select name="bagQuality" id="bagQuality" class="form-select" required onchange="showHidePrintingColorDiv();showAlternativeOption()">
                                <option value="">Select</option>                                    
                                <option value="NW">NW</option>
                                <option value="BOPP">BOPP</option>
                                <option value="LAM">LAM</option>
                            </select>                                                                       
                            <span class="error-text" id="bagQuality-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="gradeId">Roll Quality</label>
                            <select name="gradeId" id="gradeId" class="form-select" required onchange="showAlternativeOption()" >
                                <option value="">Select</option>
                                @foreach($grade as $val)
                                <option value="{{$val->id}}">{{$val->grade}}</option>
                                @endforeach
                            </select>                                                                       
                            <span class="error-text" id="bagQuality-error"></span>
                        </div>
                    </div>
                    
                </div>
                <div class="row mb-3">
                    <div class="col-sm-4" id="bookingPrintingColorDiv">
                        <div class="form-group">
                            <label class="form-label" for="bookingPrintingColor">Printing Color 
                                <span class="text-info text-xs text" style="font-size: x-small; text-decoration-line: underline;"> 
                                    <i> if Plane Bag Then Check It <input type="checkbox" onchange="makeReadOlyPrinting(event)"/></i>
                                </span>
                            </label>
                            <div class="col-md-12">
                                <select name="bookingPrintingColor[]" id="bookingPrintingColor" class="form-select select22" multiple="multiple" required> 
                                                                        
                                    @foreach($color as $val)
                                    <option data-color="{{$val->color}}" value="{{$val->color}}" style="background-color:{{$val->color}};">{{$val->color}}</option>
                                    @endforeach
                                </select>
                            </div>                                                                                                          
                            <span class="error-text" id="bookingPrintingColor-error"></span>
                        </div>
                    </div>
                    
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="totalUnits">QTY</label>
                            <input name="totalUnits" id="totalUnits" class="form-control" required onkeypress="return isNumDot(event);" onchange="getBalance()"/>                                 
                            <span class="error-text" id="bookingBagUnits-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="bookingBagUnits">Bag Unit</label>
                            <select name="bookingBagUnits" id="bookingBagUnits" class="form-select" onchange="emptyTable()">
                                <option value="">Select</option>
                                <option value="Kg">Kg</option>
                                <option value="Piece">Piece</option>
                            </select>                                    
                            <span class="error-text" id="bookingBagUnits-error"></span>
                        </div>
                    </div>
                </div>
                <div class="row mb-3"> 
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="brokerId">Agent</label>
                            <div class="col-md-12">
                                <select name="brokerId" id="brokerId" class="form-select" required> 
                                    <option value="">Select</option>                                     
                                    @foreach($broker as $val)
                                    <option value="{{$val->id}}">{{$val->broker_name}}</option>
                                    @endforeach
                                </select>
                            </div>                                                                                                          
                            <span class="error-text" id="brokerId-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="ratePerUnit">Rate Per Unit</label>
                            <input type="text" name="ratePerUnit" id="ratePerUnit" class="form-control" required onkeypress="return isNumDot(event);" />                                                     
                            <span class="error-text" id="ratePerUnit-error"></span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="rateTypeId">Rate Type </label>
                            <select name="rateTypeId" id="rateTypeId" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($rateType as $val)
                                <option value="{{$val->id}}">{{$val->rate_type}}</option>
                                @endforeach                                    
                            </select>                                                                       
                            <span class="error-text" id="rateTypeId-error"></span>
                        </div>
                    </div> 
                </div>
                <div class="row mb-3"> 
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="stereoTypeId">Stereo</label>
                            <select name="stereoTypeId" id="stereoTypeId" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($stereo as $val)
                                <option value="{{$val->id}}">{{$val->stereo_type}}</option>
                                @endforeach                                  
                            </select>                                                                       
                            <span class="error-text" id="stereoTypeId-error"></span>
                        </div>
                    </div>  
                    <div class="col-sm-4">
                        <div class="form-group">
                            <label class="form-label" for="fareTypeId">Fare</label>
                            <select name="fareTypeId" id="fareTypeId" class="form-select" required>
                                <option value="">Select</option>
                                @foreach($fare as $val)
                                <option value="{{$val->id}}">{{$val->fare_type}}</option>
                                @endforeach                                    
                            </select>                                                                       
                            <span class="error-text" id="fareTypeId-error"></span>
                        </div>
                    </div>     
                </div>
                <div class="row">
                    <div class="row mt-3">
                        <div class="col-sm-4">
                            <!-- <div class="form-group">
                                <label class="form-label" for="altBagColor">Alternate Bag Color</label>
                                <div class="col-md-12">
                                    <select name="altBagColor[]" id="altBagColor" class="form-select select22" multiple="multiple" onchange="showAlternativeOptionGsm()"> 
                                        
                                        @foreach($altRollColor as $val)
                                        <option value="{{$val->color}}">{{$val->color}}</option>
                                        @endforeach
                                    </select>
                                </div>                                                                                                          
                                <span class="error-text" id="altBagColor-error"></span>
                            </div> -->
                        </div>
                        <div class="col-sm-4">
                            <!-- <div class="form-group">
                                <label class="form-label" for="altBagGsm">Alternate Gsm</label>
                                <div class="col-md-12">
                                    <select name="altBagGsm[]" id="altBagGsm" class="form-select select22" multiple="multiple" > 
                                                                              
                                        @foreach($altGsm as $val)
                                        <option value="{{$val}}">{{$val}}</option>
                                        @endforeach
                                    </select>
                                </div>                                                                                                          
                                <span class="error-text" id="altBagGsm-error"></span>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div   id="suggestion" style="display:none;">
                        <div class="collapsible"  id="suggestion2"> 
                            <div class="panel-title">Transit</div>                                
                            <button type="button" class="collapsible-btn"><i class="bi bi-eye-fill" class="collapsible-btn"></i></button>                            
                            <div class="collapsible-content content" id="suggestionRollTransit" style="overflow: scroll;">
                            </div>
                        </div>
                        <div class="collapsible"  id="suggestion1"> 
                            <div class="panel-title">Stock</div>                                
                            <button type="button" class="collapsible-btn"><i class="bi bi-eye-fill" class="collapsible-btn"></i></button>                            
                            <div class="collapsible-content content" id="suggestionRoll" style="overflow: scroll;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="row mt-3" style="text-align:right">
                        <div > Balance <span id="balance" style="color: red;"> </span> </div>
                    </div>
                    <div class="row mt-3">
                        <table class="table table-bordered  table-responsive " id="orderRoll">
                            <thead>
                                <tr>
                                    <th>Roll No</th>
                                    <th>GSM</th>
                                    <th>Roll Color</th>
                                    <th>Length</th>
                                    <th>Size</th>
                                    <th>Net Weight</th>
                                    <th>roll Type</th>
                                    <th>Hardness</th>
                                    <th>Possible Production</th>
                                    <th>Remove</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-12">
                    <button type="button" class="btn btn-primary" onclick="showRollSuggestion()">Check</button>
                    <button type="button" name="draft" value="draft" class="btn btn-warning" onclick="savAsDraft();">Save As Draft</button>
                    <button type="submit" class="btn btn-primary" onclick="setHintCollapse();">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <x-client-form/>
</main>


<script>

    var collapsibleButtons = document.querySelectorAll('.collapsible-btn');
            
    collapsibleButtons.forEach(function(button) {
        button.addEventListener("click", function() {
            var content = button.nextElementSibling;
            
            if (content.style.display === "block") {
                content.style.display = "none";
                button.innerHTML = '<i class="bi bi-eye-fill" class="collapsible-btn"></i>'; // Change icon to plus
            } else {
                content.style.display = "block";
                button.innerHTML = '<i class="bi bi-eye-slash-fill" class="collapsible-btn"></i>';; // Change icon to minus
            }
        });
    });

    $(document).ready(function(){
        getBalance();
        $('.select22').select2({            
            width:"100%",
            display:"block"
        }); 
        $("#orderHistory").hide();      

        $('#bookingPrintingColor').select2({
            placeholder: "Select tags",
            allowClear: false,
            maximumSelectionLength: 4,
            dropdownCssClass: 'form-control',
            // dropdownParent: $('#rollBookingModal'),            
            width:"100%",
            templateResult: formatOption,
            templateSelection: formatOption 
        });

        $("#bookingBagColor").select2({
            placeholder: "Select tags",
            allowClear: false,
            maximumSelectionLength: 4,
            dropdownCssClass: 'form-control',            
            width:"100%",
        });
        $("#bagGsm").select2({
            placeholder: "Select tags",
            allowClear: false,
            maximumSelectionLength: 4,
            dropdownCssClass: 'form-control',            
            width:"100%",
        })

        $('#clientModal').on('hidden.bs.modal', function() {
            $('#rollBookingModal').css("z-index","");
        });
        showHideLoop();

        $("#myForm").validate({
            rules: {
                bookingForClientId: {
                    required: true,
                },
                bookingEstimatedDespatchDate:{
                    required:true
                },
                bookingBagUnits: {
                    required: true,
                },
                bookingBagTypeId: {
                    required: true,
                },
                bookingPrintingColor: {
                    required: function(){
                        return $("#bagQuality").val()!=="BOPP"
                    },
                },
                roll_id:{
                    required:true,
                }
            },
            submitHandler: function(form) {
                bookForClient();
            }
        });

        $('#bookingForClientId').select2({
            width:"100%",
        }); 
    });

    function makeReadOlyPrinting(event){
        if (event.target.checked) {
            $("#bookingPrintingColor")
                .val("")
                .trigger("change")
                .attr("disabled", true);
        } else {
            $("#bookingPrintingColor").attr("disabled", false);
        }

    }
    function openRollBookingClineModel(){
        $('#rollBookingModal').css("z-index",0);
        $('#clientModal').css("z-index",1060);
        $('#clientModal').modal('show');
    }
    

    function showHideLoop(){
        var bagType = $("#bookingBagTypeId").val();
        console.log(bagType);
        if(["2","4","5"].includes(bagType)){
            $("#loopColorDiv").show();
            $("#gussetDiv").show();
            if(["1","4","3"].includes(bagType)){
                $("#gussetDiv").hide();
            }
        }else{
            $("#looColor").val("");
            $("#g").val("");
            $("#loopColorDiv").hide();
            $("#gussetDiv").hide();
        }
    }

    function formatOption(option) {
        if (!option.id) {
            return option.text; // return default option text if no ID
        }
        var color = $(option.element).data('color');
        return $('<span style="background-color: ' + color + '; padding: 3px 10px; color: white; border-radius: 3px; z-index:40000">' + option.text + '</span>');
    }
    function showPrivOrder(event) {
        const clientId = $(event.target).val();
        if(clientId==1){
            $("#rateTypeId").attr("required",false);
            $("#ratePerUnit").attr("required",false);
            $("#brokerId").attr("required",false);
        }else{
            $("#rateTypeId").attr("required",true);
            $("#ratePerUnit").attr("required",true);
            $("#brokerId").attr("required",true);
        }
        if (clientId != "") {
            $.ajax({
                url: "{{route('client.old.order')}}",
                type: "post",
                data: { "clientId": clientId },
                dataType: "json",
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (response) {
                    $("#loadingDiv").hide();

                    if (response.status && response.data.length > 0) {
                        // Clear previous content
                        $("#orderHistory").show();                       
                        setHintDefault("orderHistory");
                        $("#history").empty();
                        button = $("#orderHistory button");                        
                        var content = button.next();console.log(content)
                        if(content.css("display")==="none"){
                            button.click();
                        }

                        // Create the table
                        const table = $("<table>").addClass("history-table");
                        const thead = $("<thead>").append(
                            $("<tr>").append(
                                "<th>Bag Size</th>",
                                "<th>Bag Color</th>",
                                "<th>Loop Color</th>",
                                "<th>GSM</th>",
                                "<th>Bag Type</th>",
                                "<th>Roll Quality</th>",
                                "<th>Rate</th>",
                                "<th>Action</th>",
                            )
                        );

                        const tbody = $("<tbody>");
                        
                        // Populate the rows
                        response.data.forEach((item,index) => {
                            console.log("item",item);
                            const bag_gsm = JSON.parse(item?.bag_gsm || "[]").map(value => parseInt(value, 10) || 0);
                            const bagGsmString = bag_gsm.join(",");
                            const bag_color = JSON.parse(item?.bag_color || "[]");
                            const bagColorString = bag_color.join(",");


                            tbody.append(                               
                                $("<tr>").append(
                                    `<td>${parseFloat(item.bag_w)} X ${ parseFloat(item.bag_l) + (item?.bag_g ? (" X "+parseFloat(item?.bag_g)) :"")} </td>`,
                                    `<td>${bagColorString}</td>`,
                                    `<td>${item?.bag_loop_color ? item?.bag_loop_color : ""}</td>`,
                                    `<td>${bagGsmString || "N/A"}</td>`,
                                    `<td>${item.bag_type || "N/A"}</td>`,
                                    `<td>${item.roll_quality || ""}</td>`,
                                    `<td>${item.rate_per_unit || "N/A"}</td>`,
                                    `<td><button type="button" data-item='${JSON.stringify(item)}' id="or${index}" onclick="setOrderValue('or${index}')" class="btn btn-sm btn-info">Place Order</button></td>`,
                                    
                                )
                            );
                        });

                        // Append the table structure
                        table.append(thead).append(tbody);
                        $("#history").append(table);
                        table.DataTable(
                            {
                                "ordering": false
                            }
                        );

                        // Optionally, style the table
                        $(".history-table").css({
                            width: "100%",
                            borderCollapse: "collapse",
                            margin: "20px 0",
                            fontSize: "16px",
                            textAlign: "left"
                        });

                        $(".history-table th, .history-table td").css({
                            border: "1px solid #ddd",
                            padding: "8px"
                        });

                        $(".history-table th").css({
                            backgroundColor: "#f2f2f2",
                            fontWeight: "bold"
                        });
                    } else {
                        $("#orderHistory").hide();
                        $("#history").html("<p>No records found.</p>");
                    }
                },
                error: function () {
                    $("#loadingDiv").hide();
                    // $("#history").html("<p>An error occurred while fetching data.</p>");
                }
            });
        }else{
            $("#orderHistory").hide();
        }
    }


    function setOrderValue(id) {        
        const item = JSON.parse($(event.target).attr("data-item"));
        console.log(item);

        // Set individual field values
        $("#bookingBagTypeId").val(item?.bag_type_id).trigger("change");
        $("#bookingBagUnits").val(item?.units);
        $("#l").val(item?.bag_l);
        $("#g").val(item?.bag_g);
        $("#w").val(item?.bag_w);
        $("#bookingBagColor").val(item?.bag_color);

        $("#ratePerUnit").val(item?.rate_per_unit);
        $("#totalUnits").val(item?.total_units);
        $("#bookingBagUnits").val(item?.units);
        $("#bagGsm").val(item?.bag_gsm);
        $("#bagQuality").val(item?.bag_quality);

        $("#gradeId").val(item?.grade_id);
        $("#rateTypeId").val(item?.rate_type_id);
        $("#fareTypeId").val(item?.fare_type_id);
        $("#stereoTypeId").val(item?.stereo_type_id);
        $("#looColor").val(item?.bag_loop_color);
        showHidePrintingColorDiv();
        getBalance();
        
        try {
            // Parse the printing_color string to an array
            const bag_gsm = JSON.parse(item?.bag_gsm ||"[]").map(value => parseInt(value, 10) || 0);
            $("#bagGsm").val(bag_gsm).trigger("change"); // Set the selected options
        } catch (error) {
            console.error("Error parsing printing_color:", error);
            $("#bagGsm").val([]).trigger("change");; // Clear in case of error
        }

        try {
            // Parse the printing_color string to an array
            const bag_color = JSON.parse(item?.bag_color) || [];
            $("#bookingBagColor").val(bag_color).trigger("change"); // Set the selected options
        } catch (error) {
            console.error("Error parsing printing_color:", error);
            $("#bookingBagColor").val([]).trigger("change");; // Clear in case of error
        }
        // Set the multi-select field for 'bookingPrintingColor'
        try {
            // Parse the printing_color string to an array
            const printingColors = JSON.parse(item?.bag_printing_color) || [];
            $("#bookingPrintingColor").val(printingColors).trigger("change"); // Set the selected options
        } catch (error) {
            console.error("Error parsing printing_color:", error);
            $("#bookingPrintingColor").val([]).trigger("change");; // Clear in case of error
        }
    }

    function showRollSuggestion(){
        let itsOk = true;
        let inputs = [
            { id: "#bookingBagTypeId", name: "Bag Type" },
            { id: "#bookingBagUnits", name: "Bag Units" },
            // { id: "#totalUnits", name: "Total Units" },
            // { id : "#bagQuality" , name : "bag Quality"},
            { id: "#l", name: "Bag Length" },
            { id: "#w", name: "Bag Width" },
            { id: "#bagGsm", name: "Bag GSM" },
            // { id: "#bookingBagColor", name:"Bag Color"},
        ];
        if($("#bookingBagTypeId").val()=="2"){
            inputs.push({ id: "#g", name: "Bag Gusset" });
        }
        if($("#bagQuality").val()=="BOPP"){
            inputs.push({ id: "#bagGsmJson", name: "Bag Gusset" });
        }

        for (let input of inputs) {
            $(input.id).css("border", "1px solid #cbcaca");
            if (!$(input.id).val()) {
                $(input.id).focus();
                $(input.id).css("border", "2px solid red");                
                if(itsOk){
                    itsOk= false;
                }
                console.log(input.id,itsOk);
                //return;  // Exit the function after the first empty field
            }
        }
        if(!itsOk){
            return false;
        }
        $.ajax({
            url:"{{route('client.order.suggestion')}}",
            type:"post",
            data:$("#myForm").serialize(),
            dataType:"json",
            beforeSend:function(){
                $("#loadingDiv").show();
                $("#suggestionRoll").html(`<div class="loading text-center">Loading.....</div>`);
                $("#suggestionRollTransit").html(`<div class="loading text-center">Loading.....</div>`);
            },
            success:function(response){
                console.log(response.data.rollTransit.length);
                $("#loadingDiv").hide();
                $("#suggestion").show();

                if (response.status) {
                    // Clear previous content
                    $("#suggestion1").show();
                    setHintDefault("suggestion1");
                    $("#suggestionRoll").empty();
                    button = $("#suggestion1 button");                        
                    var content = button.next();console.log(content)
                    if(content.css("display")==="none"){
                        button.click();
                    }

                    // Create the table
                    const table = $("<table id='tblStockSuggestion' class='table table-responsive table-fixed'>").addClass("history-table");
                    const thead = $("<thead>").append(
                        $("<tr>").append(
                            "<th>Sl</th>",
                            "<th>Vender Name</th>",
                            "<th>Quality</th>",
                            "<th>Size</th>",
                            "<th>GSM</th>",
                            "<th>Roll Color</th>",
                            "<th>Roll No</th>",
                            "<th>Net Weight</th>",
                            "<th>Length</th>",
                            "<th>roll Type</th>",
                            "<th>Hardness</th>", 
                            "<th>Total Possible Product</th>", 
                            "<th>Book For</th>",                            
                            "<th>Add To Book</th>",
                        )
                    );

                    const tbody = $("<tbody>");
                    
                    // Populate the rows
                    if(response.data.roll.length > 0){
                        response.data.roll.forEach((item,index) => {
                            tbody.append(                               
                                $("<tr>").append(
                                    `<td>${index+1}</td>`,
                                    `<td>${item.vendor_name}</td>`,
                                    `<td>${item.quality}</td>`,
                                    `<td>${item.size || "N/A"}</td>`,
                                    `<td>${item.gsm}</td>`,
                                    `<td>${item.roll_color || "N/A"}</td>`,
                                    `<td>${item.roll_no}</td>`,
                                    `<td>${item.net_weight || "N/A"}</td>`,
                                    `<td>${item.length || "N/A"}</td>`,
                                    `<td>${item.roll_type || "N/A"}</td>`,
                                    `<td>${item.hardness || "N/A"}</td>`,
                                    `<td>${item?.unit || "N/A"}</td>`, 
                                    `<td>${item?.client_name || ""}</td>`,                                
                                    `<td><button type="button" data-item='${JSON.stringify(item)}' id="rl${index}" onclick="addToBook('rl${index}')" class="btn btn-sm btn-info">Place Order</button></td>`,
                                )
                            );
                        });
                    }else{
                        tbody.append(                               
                                $("<tr>").append(
                                    `<td colspan="14">No Data</td>`,
                                )
                            );
                    }

                    // Append the table structure
                    table.append(thead).append(tbody);
                    $("#suggestionRoll").append(table);
                    if(response.data.roll.length > 0){
                        if ($.fn.DataTable.isDataTable("#tblStockSuggestion")) {
                            $("#tblStockSuggestion").DataTable().destroy();
                        }
                        $("#tblStockSuggestion").DataTable({
                            "ordering": true,  // Enables sorting
                            "order": []        // Default order (unsorted initially)
                        });

                        addFilter('tblStockSuggestion',[0,($('#tblStockSuggestion thead tr:nth-child(1) th').length - 1)]);
                        
                    }
                    // Optionally, style the table
                    $(".history-table").css({
                        width: "100%",
                        borderCollapse: "collapse",
                        margin: "20px 0",
                        fontSize: "16px",
                        textAlign: "left"
                    });

                    $(".history-table th, .history-table td").css({
                        border: "1px solid #ddd",
                        padding: "8px"
                    });

                    $(".history-table th").css({
                        backgroundColor: "#f2f2f2",
                        fontWeight: "bold"
                    });
                    
                } else {
                    $("#suggestion1").hide();
                    // $("#suggestionRoll").html("<p>No records found.</p>");
                }
                if (response.status) {
                    // Clear previous content
                    $("#suggestion2").show();                    
                    setHintDefault("suggestion2");
                    $("#suggestionRollTransit").empty();
                    button = $("#suggestion2 button");                        
                    var content = button.next();console.log(content)
                    if(content.css("display")==="none"){
                        button.click();
                    }

                    // Create the table
                    const table = $("<table id='tblTransitSuggestion' class='table table-responsive table-fixed'>").addClass("history-table");
                    const thead = $("<thead>").append(
                        $("<tr>").append(
                            "<th>Sl</th>",
                            "<th>Vender Name</th>",
                            "<th>Quality</th>",
                            "<th>Size</th>",
                            "<th>GSM</th>",
                            "<th>Roll Color</th>",
                            "<th>Roll No</th>",
                            "<th>Net Weight</th>",
                            "<th>Length</th>",
                            "<th>roll Type</th>",
                            "<th>Hardness</th>", 
                            "<th>Total Possible Product</th>", 
                            "<th>Book For</th>",                            
                            "<th>Add To Book</th>",
                        )
                    );

                    const tbody = $("<tbody>");
                    
                    // Populate the rows
                    if(response.data.rollTransit.length > 0){
                        response.data.rollTransit.forEach((item,index) => {
                            tbody.append(                               
                                $("<tr>").append(
                                    `<td>${index+1}</td>`,
                                    `<td>${item.vendor_name}</td>`,
                                    `<td>${item.quality}</td>`,
                                    `<td>${item.size || "N/A"}</td>`,
                                    `<td>${item.gsm}</td>`,
                                    `<td>${item.roll_color || "N/A"}</td>`,
                                    `<td>${item.roll_no}</td>`,
                                    `<td>${item.net_weight || "N/A"}</td>`,
                                    `<td>${item.length || "N/A"}</td>`,
                                    `<td>${item.roll_type || "N/A"}</td>`,
                                    `<td>${item.hardness || "N/A"}</td>`,
                                    `<td>${item?.unit || "N/A"}</td>`, 
                                    `<td>${item?.client_name || ""}</td>`,
                                    `<td><button type="button" data-item='${JSON.stringify(item)}' id="tl${index}" onclick="addToBook('tl${index}')" class="btn btn-sm btn-info">Place Order</button></td>`,
                                )
                            );
                        });
                    }
                    else{
                        tbody.append(                               
                                $("<tr>").append(
                                    `<td colspan="14">No Data</td>`,
                                )
                            );
                    }

                    // Append the table structure
                    table.append(thead).append(tbody);
                    $("#suggestionRollTransit").append(table);
                    if(response.data.rollTransit.length > 0){
                        
                        if ($.fn.DataTable.isDataTable("#tblTransitSuggestion")) {
                            $("#tblTransitSuggestion").DataTable().destroy();
                        }
                        $("#tblTransitSuggestion").DataTable({
                            "ordering": true,  // Enables sorting
                            "order": []        // Default order (unsorted initially)
                        });

                        addFilter('tblTransitSuggestion',[0,($('#tblTransitSuggestion thead tr:nth-child(1) th').length - 1)]);
                    }
                    // Optionally, style the table
                    $(".history-table").css({
                        width: "100%",
                        borderCollapse: "collapse",
                        margin: "20px 0",
                        fontSize: "16px",
                        textAlign: "left"
                    });

                    $(".history-table th, .history-table td").css({
                        border: "1px solid #ddd",
                        padding: "8px"
                    });

                    $(".history-table th").css({
                        backgroundColor: "#f2f2f2",
                        fontWeight: "bold"
                    });
                } else {
                    $("#suggestion2").hide();
                    // $("#suggestionRollTransit").html("<p>No records found.</p>");
                }
            },
            error:function(errors){
                console.log(errors);
                $("#loadingDiv").hide();
                $("#suggestion1").hide();
                $("#suggestion2").hide();
            }
        });
    }

    function showAlternativeOption(){
        showAlternativeOptionGsm();
        selectedAlternateBagColor = $("#altBagColor").val();
        $.ajax({
            url:"{{route('roll.order.alternate.options.color')}}",
            type:"post",
            data:$("#myForm").serialize(),
            dataType:"json",
            beforeSend:function(){
                $("#altBagColor").empty().append('<option>Loading...</option>');  // Show temporary loading option
                $("#altBagColor").trigger('change');  // Trigger change for Select2 to refresh
                // $("#loadingDiv").show();
            },
            success:function(response){;
                $("#loadingDiv").hide();
                if (response.status) {
                    $("#altBagColor").empty();
                    let option =`<option value=''> Select </option>`;
                    response?.data?.altBagColor.forEach((val)=>{
                        option+=`<option value='${val}'>${val}</option>`;
                    });
                    $("#altBagColor").append(option);
                    if(selectedAlternateBagColor && selectedAlternateBagColor.length>0){
                        $("#altBagColor").val(selectedAlternateBagColor);
                    }
                }
            },
            error:function(errors){
                console.log(errors);
                $("#loadingDiv").hide();
            }
        });
    }

    function showAlternativeOptionGsm(){
        selectedAlternateBagGsm = $("#altBagGsm").val();
        $.ajax({
            url:"{{route('roll.order.alternate.options.gsm')}}",
            type:"post",
            data:$("#myForm").serialize(),
            dataType:"json",
            beforeSend:function(){
                // $("#loadingDiv").show();
                $("#altBagGsm").empty().append('<option>Loading...</option>');  // Show temporary loading option
                $("#altBagGsm").trigger('change');  // Trigger change for Select2 to refresh
            },
            success:function(response){;
                $("#loadingDiv").hide();
                if (response.status) {
                    $("#altBagGsm").empty();
                    let option =`<option value=''> Select </option>`;                    
                    response?.data?.altGsm.forEach((val)=>{
                        option+=`<option value='${val}'>${val}</option>`;
                    });
                    $("#altBagGsm").append(option);                    
                    if(selectedAlternateBagGsm && selectedAlternateBagGsm.length>0){
                        $("#altBagGsm").val(selectedAlternateBagGsm);
                    }
                }
            },
            error:function(errors){
                console.log(errors);
                $("#loadingDiv").hide();
            }
        });
    }

    function addToBook(id){
        const item = JSON.parse($(event.target).attr("data-item"));
        const existingRow = $(`#orderRoll tbody tr[data-id="${item.id}"]`);
        if (existingRow.length > 0) {
            popupAlert("This item is already added.");
            return; // Exit the function if the item already exists
        }
        const tr = $("<tr>")
                    .attr("data-id", item.id)
                    .addClass((item.stock=="stock" ?"table-info" : "table-danger"))
                    .append(
                                `<td>${item.roll_no} <input type='hidden' name='roll[${item.id}][id]' value='${item.id}' /></td>`,
                                `<td>${item.gsm}</td>`,
                                `<td>${item.roll_color || "N/A"}</td>`,
                                `<td>${item.length || "N/A"}</td>`,
                                `<td>${item.size || "N/A"}</td>`,
                                `<td>${item.net_weight || "N/A"}</td>`,
                                `<td>${item.roll_type || "N/A"}</td>`,
                                `<td>${item.hardness || "N/A"}</td>`, 
                                `<td>${item.result || "N/A"}</td>`,                                
                                `<td><span onclick='removeTr(this)' class='btn btn-sm btn-warning'>X</span></td>`,
                            );
        $("#orderRoll tbody").append(tr);
        getBalance();
        testAccessBooking();
    }

    function removeTr(element) {
        $(element).closest("tr").remove();
        getBalance();
    }

    function bookForClient(){        
        if ($("input[type='hidden'][name^='roll']").length === 0) {
            (showConfirmDialog('Are you sure you want to add On Pending Order?', saveOrder));
        }
        else{
            saveOrder();
        }
        return false;
    }

    function savAsDraft(){
        if(!$("#bookingForClientId").val()){
            alert("please select client");
            $("#bookingForClientId").focus();
            return false;
        }
        (showConfirmDialog('Are you sure you want to As Draft Order?', function(){saveOrder(true);}));
    }
    function saveOrder(isDraft=false){
        let data = new FormData($("#myForm")[0]);
        if(isDraft){
            data.append("saveAsDraft", true);
        }
        $.ajax({
            url:"{{route('order.punches.save')}}",
            type:"post",
            data:data,
            processData: false,     // ✅ must be false for FormData
            contentType: false,     // ✅ must be false for FormData
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data.status){
                    $("#myForm").get(0).reset();
                    $("#orderHistory").hide();
                    $("#suggestion").hide();
                    $("#orderRoll tbody").empty();
                    modelInfo(data.message);
                    setHintDefault();
                    getBalance();
                    resetForm("myForm");
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

    function emptyTable(){
        $("#orderRoll tbody").empty();
        getBalance();
    }
    function getBalance() {
        let bookedQtr = 0;
        
        $("#orderRoll tbody tr").each(function () {
            let value = $(this).find('td').eq(8).text(); // Adjust the index to the correct column
            if (!isNaN(value) && value.trim() !== '') {
                bookedQtr += parseFloat(value);
            }
        });
        let towevePer = 0;
        if($("#bookingBagTypeId").val()==3){
            towevePer = $("#totalUnits").val() * 0.12;
        }
        console.log($("#bookingBagTypeId").val()==3);

        let totalUnits = parseFloat($("#totalUnits").val()) || 0;
        let bookingBagUnits = $("#bookingBagUnits").val() || ""; // If it's not a number, keep as string

        $("#balance").html((totalUnits + towevePer - bookedQtr) + " " + bookingBagUnits);     
        return ($("#totalUnits").val()-bookedQtr);
    }

    function showHidePrintingColorDiv(){
        $("#bagGsm").attr("required",false);
        $("#bagGsmJson").attr("required",false);
        if($("#bagQuality").val()=="BOPP" || $("#bagQuality").val()=="LAM"){
            // $("#bookingPrintingColorDiv").hide();
            $("#singleGsm").hide();
            $("#multipleGsm").show();
            $("#bagGsm").val("");
            $("#bagGsmJson").attr("required",true);
        }else{
            // $("#bookingPrintingColorDiv").show();
            $("#singleGsm").show();
            $("#multipleGsm").hide();
            $("#bagGsmJson").val("");
            $("#bagGsm").attr("required",true);
        }
        setGsm();
    }

    function setGsm(){
        let gsmJson = $("#bagGsmJson").val();
        let gsm = 0;
        const parts = gsmJson.split(/\/+/);
        for (let part of parts) {
            gsm+= parseFloat(part);
        }
        if(gsmJson){
            $("#bagGsm").val(gsm);
        }
        console.log("gsm:",gsm);
    }

    function resetForm(id){
        $("#"+id).get(0).reset();
        $('#'+id+' select').each(function() {
            if ($(this).data('select2')) {
                $(this).val(null).trigger('change');
            }
        });
    }

    function testLoop(){
        let loopColor = $("#looColor").val();
        if(loopColor){
            $.ajax({
                url : "{{route('master.loop.stock.booking.test')}}",
                type : "post",
                data : {"loopColor":loopColor},
                beforeSend:function(){
                    $("loadingDiv").show();
                },
                success:function(data){
                    if(data?.status){
                        modelInfo(data?.message,data?.data);
                    }
                    else{                        
                        testTemp(loopColor);
                    }
                },
                error:function(error){
                    console.log(error);
                    testTemp(loopColor);
                }
            })
        }
    }

    function testTemp(color){
        item = JSON.parse($("#"+color).attr("data-item"));
        if(item?.balance < item?.min_limit){
            modelInfo(item?.loop_color+" is very short","warning");
        }else if(item?.balance < (item?.min_limit+100)){
            modelInfo(item?.loop_color+" is nearly sorted.","info");
        }
    }

    function testAccessBooking(){
       let balance =  getBalance();
       if(balance<0){
        popupAlert("Order Quantity Excide");
       }
    }


</script>


@include("layout.footer")