<!-- Modal Form -->

<div class="modal fade modal-lg" id="rollBookingModal" tabindex="-1" aria-labelledby="rollBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rollBookingModalLabel"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            @livewire('pending-order-book')
            @livewireScripts
            </div>
        </div>
    </div>
</div>

<script>


    $(document).ready(function(){
        getBalance();
        // $('.select22').select2(); 
        select2Init();

        showHideLoop();

        $("#myForm").validate({
            rules: {
                bookingForClientId: {
                    required: true,
                },
                id:{
                    required:true,
                }
            },
            submitHandler: function(form) {
                (showConfirmDialog('Are you sure you??', saveOrder));
                return false;
            }
        });

        
        
        $('#bookingForClientId').on('change', function(event, extraData) {
            // Access the extra data passed
            setOrderValue(event,extraData); 
        });
        
    });
   
    function select2Init(){
        $('#bookingPrintingColor').select2({
            placeholder: "Select tags",
            allowClear: true,
            maximumSelectionLength: 4,
            dropdownCssClass: 'form-control',            
            width: "100%"  ,   
            dropdownParent: $('#rollBookingModal'),
            templateResult: formatOption,
            templateSelection: formatOption 
        });
        $('#bagGsm').select2({
            placeholder: "Select tags",
            allowClear: true,
            maximumSelectionLength: 4,
            dropdownCssClass: 'form-control',            
            width: "100%"  ,   
            dropdownParent: $('#rollBookingModal'),
        });

        $('#bookingBagColor').select2({
            placeholder: "Select tags",
            allowClear: true,
            maximumSelectionLength: 4,
            dropdownCssClass: 'form-control',            
            width: "100%"  ,   
            dropdownParent: $('#rollBookingModal'),
        });

        $('#bookingForClientId').select2({
            dropdownParent: $('#rollBookingModal'),
            dropdownCssClass: 'form-control',
            width: "100%"     
        }); 
        
        $('.select22').select2({
            dropdownParent: $('#rollBookingModal'),
            dropdownCssClass: 'form-control',
            width: "100%"
        }); 
    }

    function showHideLoop(){
        var bagType = $("#bookingBagTypeId").val();
        console.log(bagType);
        if(["2","4"].includes(bagType)){
            $("#loopColorDiv").show();
            $("#gussetDiv").show();
            if(bagType=="4"){
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

    function setOrderValue(event,inits="0") {  
        id = event.target.value;
        $("#submit").attr("disabled",true);
        if(event.target.value==""){
            resetFormBook("myForm",inits);
            return false;
        }
        let selectedOption = event.target.options[event.target.selectedIndex];
        // Parse the data-item attribute
        let rawData = $("#or"+id).attr("data-item");

        // Decode HTML-escaped characters
        let decodedData = rawData
            .replace(/&quot;/g, '"')  // Replace &quot; with "
            .replace(/\\\//g, '/');

        let item = JSON.parse(decodedData);
        console.log(item,event.target.selectedIndex);

        // Set individual field values
        $("#bookingBagTypeId").val(item?.bag_type_id);
        $("#orderDate").val(item?.order_date);
        $("#bookingEstimatedDespatchDate").val(item?.estimate_delivery_date);
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
        $("#balance").val(item?.balance_units + item?.units);
        $("#possibleProduction").val("");
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
            const altBagColor = JSON.parse(item?.alt_bag_color) || [];
            $("#altBagColor").val(altBagColor).trigger("change"); // Set the selected options
        } catch (error) {
            console.error("Error parsing printing_color:", error);
            $("#altBagColor").val([]).trigger("change");; // Clear in case of error
        }

        try {
            // Parse the printing_color string to an array
            const altBagGsm = JSON.parse(item?.alt_bag_gsm) || [];
            $("#altBagGsm").val(altBagGsm).trigger("change"); // Set the selected options
        } catch (error) {
            console.error("Error parsing printing_color:", error);
            $("#altBagGsm").val([]).trigger("change");; // Clear in case of error
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
            let printingColors = (item?.bag_printing_color) || [];
            $("#bookingPrintingColor").val(printingColors).trigger("change");; // Set the selected options
        } catch (error) {
            console.error("Error parsing printing_color:", error);
            $("#bookingPrintingColor").val([]).trigger("change");; // Clear in case of error
        }
        $.ajax({
            url :"{{route('roll.order.test')}}",
            type:"post",
            data:$("#myForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){                
                $("#loadingDiv").hide();                
                console.log(data);
                $("#balance").val(data?.data?.balanceUnit);
                if(data?.status && data?.data?.test){
                    $("#possibleProduction").val(data?.data?.units);                    
                    modelInfo(data?.message);
                    $("#submit").attr("disabled",false);
                    if((data?.data?.balance - data?.data?.production) < 12 && item?.units=='Kg' ){
                        $("#submit").attr("disabled",true);
                        showConfirmDialog("Are you sure want to book access quantity ",function(){$("#submit").attr("disabled",false);});
                    }
                    else if((data?.data?.balance - data?.data?.production) < 50 && item?.units!='Kg' ){
                        $("#submit").attr("disabled",true);
                        showConfirmDialog("Are you sure want to book access quantity ",function(){$("#submit").attr("disabled",false);});
                    }
                }
                else if(data?.status && !data?.data?.test){
                    modelInfo(data?.message,"warning");
                }
                else{
                    modelInfo("Roll is not tested","error");
                }
            },
            error:function(errors){
                $("#loadingDiv").hide();
                console.log(errors);
            }

        })
        
    }

    function saveOrder(){  
        $.ajax({
            url:"{{route('roll.order.to.book')}}",
            type:"post",
            data:$("#myForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data.status){ 
                    
                    Livewire.dispatchTo('pending-order-book', 'refreshComponent');                 
                    $("#rollBookingModal").modal("hide");
                    resetFormBook("myForm");
                    modelInfo(data.messages);
                    searchData();
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

    function getBalance() {
        let bookedQtr = 0;
        
        $("#orderRoll tbody tr").each(function () {
            let value = $(this).find('td').eq(8).text(); // Adjust the index to the correct column
            if (!isNaN(value) && value.trim() !== '') {
                bookedQtr += parseFloat(value);
            }
        });
        let totalUnits = parseFloat($("#totalUnits").val()) || 0;
        let bookingBagUnits = $("#bookingBagUnits").val() || ""; // If it's not a number, keep as string

        $("#balance").html((totalUnits - bookedQtr) + " " + bookingBagUnits); 

        // $("#balance").html(($("#totalUnits").val()-bookedQtr)+" "+$("#bookingBagUnits").val());        
        return bookedQtr;
    }

    function showHidePrintingColorDiv(){
        if($("#bagQuality").val()=="BOPP"){
            $("#bookingPrintingColorDiv").hide();
            $("#singleGsm").hide();
            $("#multipleGsm").show();
            $("#bagGsm").val("");
        }else{
            $("#bookingPrintingColorDiv").show();
            $("#singleGsm").show();
            $("#multipleGsm").hide();
            $("#bagGsmJson").val("");
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

    function resetFormBook(id,inits="0"){
        $("#"+id).get(0).reset();
        $('#'+id+' select').each(function() {
            if ($(this).data('select2')) { 
                if(this.id=="bookingForClientId"){
                    if(inits!="1"){ 
                        // $(this).val(null).trigger('change',["1"]);
                    }
                }else{
                    $(this).val(null).trigger('change');
                }
            }
        });
        select2Init();
        // $('#bookingPrintingColor').select2({
        //     placeholder: "Select tags",
        //     allowClear: true,
        //     maximumSelectionLength: 4,
        //     dropdownCssClass: 'form-control',            
        //     width: "100%"  ,   
        //     dropdownParent: $('#rollBookingModal'),
        //     templateResult: formatOption,
        //     templateSelection: formatOption 
        // });
    }

    function searchData(){
        $('#postsTable').DataTable().ajax.reload(function(){
            addFilter('postsTable',[0]);
        },false);
        
    }
</script>