<!-- Modal Form -->

 
<div class="modal fade modal-lg" id="rollBookingModal" tabindex="-1" aria-labelledby="rollBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rollBookingModalLabel">Booking Roll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rollBookingForm">
                    @csrf
                    <!-- Hidden field for roll ID -->
                    <input type="hidden" id="rollId" name="rollId" value="">

                    <div class="row">

                        <div class="row mt-3">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="bookingForClientId">Book For Client 
                                        <span onclick="openRollBookingClineModel()"  style="font-weight: bolder; font-size:small; text-decoration: underline;"> 
                                            <i class="bi bi-person-add"></i> Add Client
                                        </span> 
                                    </label>
                                    <div class="col-md-12">
                                        <select name="bookingForClientId" id="bookingForClientId" class="form-select select-option">
                                            <option value="">Select</option>
                                            @foreach ($clientList as $val)
                                                <option value="{{ $val->id }}">{{ $val->client_name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="error-text" id="bookingForClientId-error"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="bookingEstimatedDespatchDate">Dispatch Date</label>
                                    <input type="date" min="{{date('Y-m-d')}}" name="bookingEstimatedDespatchDate" id="bookingEstimatedDespatchDate" class="form-control" required/>                                  
                                    <span class="error-text" id="bookingEstimatedDespatchDate-error"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">                            
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="form-label" for="bookingBagTypeId">Bag Type </label>
                                    <select name="bookingBagTypeId" id="bookingBagTypeId" class="form-select" onchange="showHideLoop()">
                                        <option value="">Select</option>
                                        @foreach ($bagType as $val)
                                            <option value="{{ $val->id }}">{{ $val->bag_type }}</option>
                                        @endforeach
                                    </select>                                                                       
                                    <span class="error-text" id="bookingBagTypeId-error"></span>
                                </div>
                            </div>
                            
                            <div class="col-sm-6" id="loopColorDiv">
                                <div class="form-group">
                                    <label class="form-label" for="looColor">Loop Color</label>
                                    <input name="looColor" id="looColor" class="form-control" required />                               
                                    <span class="error-text" id="looColor-error"></span>
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
                                        <select name="bookingPrintingColor[]" id="bookingPrintingColor" class="form-select select22" multiple="multiple" required> 
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
        $('.select22').select2();       

        $('#bookingPrintingColor').select2({
            placeholder: "Select tags",
            allowClear: true,
            dropdownParent: $('#rollBookingModal'),
            templateResult: formatOption,
            templateSelection: formatOption 
        });

        $('#clientModal').on('hidden.bs.modal', function() {
            $('#rollBookingModal').css("z-index","");
        });
        showHideLoop();
        $('#bookingForClientId').select2({
            placeholder: "Select tags",
            allowClear: true,
            dropdownParent: $('#rollBookingModal'),
            // templateResult: formatOption,
            // templateSelection: formatOption 
        });  

        // addSearch("bookingForClientId");

    });
    function openRollBookingClineModel(){
        $('#rollBookingModal').css("z-index",0);
        $('#clientModal').css("z-index",1060);
        $('#clientModal').modal('show');
    }
    

    // $('#bookingForClientId').on('change', function() {
    //     var selectedValue = $(this).val();
    //     if (selectedValue === '') {
    //         $('#rollBookingModal').css("z-index", 0);
    //         $('#clientModal').css("z-index", 1060);
    //         $('#clientModal').modal('show'); // Open modal when "Add" option is selected
    //     }
    // });

    function showHideLoop(){
        var bagType = $("#bookingBagTypeId").val();
        console.log(bagType);
        if(["2"].includes(bagType)){
            $("#loopColorDiv").show();
        }else{
            $("#looColor").val("");
            $("#loopColorDiv").hide();
        }
    }

    function formatOption(option) {
        if (!option.id) {
            return option.text; // return default option text if no ID
        }
        var color = $(option.element).data('color');
        return $('<span style="background-color: ' + color + '; padding: 3px 10px; color: white; border-radius: 3px; z-index:40000">' + option.text + '</span>');
    }

</script>
