<!-- Modal Form -->
 
<div class="modal fade modal-lg" id="orderPunchModel" tabindex="-1" aria-labelledby="orderPunchModelLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderPunchModelLabel">Order Punch</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rollBookingForm">
                    @csrf
                    <div class="row">
                    <div class="row mt-3">
                        <!-- Vendor Address -->
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="length">O <span style="font-size:small;color:aquamarine">(In Meter)</span><span class="text-danger">*</span></label>
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
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="control-label" for="forClientId">
                                    Book For Client 
                                    <span onclick="openClineModel()"  style="font-weight: bolder; font-size:small; text-decoration: underline;"> 
                                        <i class="bi bi-person-add"></i> Add Client
                                    </span>
                                </label>
                                <select name="forClientId" id="forClientId" class="form-control" onchange="openCloseClientMode()">
                                    <option value="" >Select</option>
                                   
                                </select>
                                <span class="error-text" id="forClientId-error"></span>
                            </div>
                        </div>                        
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
            dropdownParent: $('#orderPunchModel'),
            templateResult: formatOption,
            templateSelection: formatOption 
        });

        $('#clientModal').on('hidden.bs.modal', function() {
            $('#orderPunchModel').css("z-index","");
        });

    });
    function openRollBookingClineModel(){
        $('#orderPunchModel').css("z-index",0);
        $('#clientModal').css("z-index",1060);
        $('#clientModal').modal('show');
    }
    

    $('#bookingForClientId').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue === '') {
            $('#orderPunchModel').css("z-index", 0);
            $('#clientModal').css("z-index", 1060);
            $('#clientModal').modal('show'); // Open modal when "Add" option is selected
        }
    });

    function formatOption(option) {
        if (!option.id) {
            return option.text; // return default option text if no ID
        }
        var color = $(option.element).data('color');
        return $('<span style="background-color: ' + color + '; padding: 3px 10px; color: white; border-radius: 3px; z-index:40000">' + option.text + '</span>');
    }
</script>