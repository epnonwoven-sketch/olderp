@include('layout.header')
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>'" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Entry</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">WIP List</h5>  
            <div class="panel-control">
                <div class="form-check form-switch" style="cursor:pointer">
                    <label class="form-check-label" for="dividend">Only 2 - 10%</label> <input class="form-check-input" type="checkbox" id="dividend" name="dividend" onclick="searchData()" />
                </div>          
            </div>
        </div>        
        <div class="panel-body">            
            <form action="" id="entryForm">
                <div class="row mb-3">
                    <label for="packing_date" class="form-label col-md-3">Packing Date</label>
                    <div class="col-md-4">
                        <input type="date"  class="form-control" name="packing_date" id="packing_date" value="{{date('Y-m-d')}}" max="{{date('Y-m-d')}}" required/>
                    </div>
                </div>
                <div class="row">
                    <div class="panel-control justify-content-end" >
                        <strong>Total Weight:</strong> (<span id="total_weight">0</span>)
                    </div>
                    <table class="table table-bordered table-responsive table-fixed" id="orderRoll">
                        <thead>
                            <tr>
                                <!-- <th>Order No</th> -->
                                <th>Client Name</th>
                                <th>Bag Size</th>
                                <th>Bag Type</th>
                                <th>Bag Color</th>
                                <th>Bag GSM</th>
                                <th>Total Bag Weight</th>
                                <th>Total Bag In Pieces</th>
                                <th>Garbage</th>
                                <th>Unit</th>
                                <th>%</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                    </table>
                </div>
                <div class="col-12 mt-3 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary" id="submit" style="display:none;">Submit</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    $(document).ready(function(){
        $('#orderRoll').DataTable({
            responsive: false,
            processing: false,
            ordering: false,
            ajax: {
                url: "{{route('packing.wip')}}", // The route where you're getting data from
                data: function(d) {
                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
                    });
                    if($("#dividend").is(":checked")){
                        d["dividend"] = $("#dividend").is(":checked");
                    }

                },
                dataSrc: function (json) {
                    $('#total_weight').text(json?.totalWeight); 
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
                // { data: "order_no", name: "order_no", orderable: false, searchable: false },
                { data: "client_name", name: "client_name" },
                { data: "bag_size", name: "bag_size" ,render: function(item) {  return `<pre>${item}</pre>`; }},
                { data: "bag_type", name: "bag_type" },
                { data: "bag_color", name: "bag_color" },
                { data: "bag_gsm", name: "bag_gsm" },
                { data: "balance",name: "balance",render: function(row, type, data) { return parseFloat(data.balance).toFixed(2);}},
                { data: "balance_in_pieces",name: "balance_in_pieces",render: function(row, type, data) { return (data.balance_in_pieces ? data?.balance_in_pieces : "N/A");}},
                { data: "total_garbage",name: "total_garbage",render: function(row, type, data) { return (data.total_garbage ? data?.total_garbage : "0");}},
                { data: "units", name: "units" },
                {data : "balance_prc", name: "balance_prc"},
                { data: null, orderable: false, searchable: false, render: function(row, type, data) {
                        return `
                            <table class="mt-2 table table-bordered table-fixed" style="display:none;" id="table_${data.id}">
                            </table>
                            <button type="button" data-item='${JSON.stringify(data)}' title="${parseFloat(parseFloat(data.roll_weight)-parseFloat(data.total_garbage)+parseFloat(data.loop_weight)).toFixed(2)}"
                                    id="button_${data.id}" 
                                    onclick="addTr('${data.id}')" 
                                    class="btn btn-sm btn-primary">+
                            </button>
                            <button type="button" class="btn btn-sm btn-danger" onclick="disburseOrderConform(${data.id},'${parseFloat(data.balance).toFixed(2)} Kg')">D</button>
                            ${
                            (parseFloat(parseFloat(data.roll_weight)-parseFloat(data.total_garbage) - parseFloat(data.u_cute_garbage) +parseFloat(data.loop_weight)).toFixed(2) == parseFloat(data.balance).toFixed(2))
                            ?`<button class="btn btn-sm btn-warning" onclick="deleteWipConform(${data.id})">delete</button>`
                            :""}
                        `;
                    }
                }
            ],

            dom: 'lBfrtip',
            language: {
                lengthMenu: "Show _MENU_"
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                ["10 Rows", "25 Rows", "50 Rows", "100 Rows", "All"]
            ],
            buttons: [
                {
                    extend: 'csv',
                    text: 'Export to Excel',
                    className: 'btn btn-success'
                }
            ],
            createdRow: function(row, data, dataIndex) {
                let td = $('td', row).eq(0); 
                td.attr("title", data?.order_no);
            },
            initComplete: function () {
                addFilter('orderRoll',[$("#orderRoll th").length-1]);
            }, 
        });

        $("#entryForm").validate({
            rules: {
                "roll[][id]": {
                    required: true,
                    number: true,
                },
            },
            submitHandler: function(form) {
                let testIdealWeight = false;
                let message = "";

                $(".idealWeight").each((index, element) => { 
                    let $element = $(element);
                    console.log($element.attr("id"));  // Logs the ID of the idealWeight input
                    
                    // Get values and convert them to numbers
                    let bagWeight = parseFloat($element.closest("tr").find('input[name*="[weight]"]').val()) || 0;
                    let idealWeight = parseFloat($element.val()) || 0;
                    
                    // Check if the absolute difference is greater than 1
                    if (Math.abs(bagWeight - idealWeight) > 1) {
                        testIdealWeight = true;
                        message = "Bag weight and Ideal weight mismatch. Are you sure you want to submit?";
                    }
                });

                if (testIdealWeight) {
                    if (confirm(message)) {                        
                        entryFormSubmit();
                    } else {
                        return false; // Prevents form submission
                    }
                } else {                    
                    entryFormSubmit();
                }
            }
        });

    });

    let sl = 0;
    async function addTr(id) {
        let packingDate = $("#packing_date").val();
        let slObj = [];
        await $(".bag_sl_no").each(function () {
            if (this.tagName === "INPUT") {
                slObj.push($(this).val());
            }
        });

        if(packingDate==""){
            alert("please select packing date");
            return;
        }
        packing_sl = 0;
        try {
            const response = await $.ajax({
                url: "{{ route('packing.serial') }}",
                method: "GET",
                dataType: "json",
                data: {
                    packing_date:packingDate,
                    sl_nos:slObj
                },
                beforeSend: function () {
                    $("#loadingDiv").show();
                }
            });

            console.log(response);

            if (response.status) {
                $("#packing_date").attr("readonly",true);
                packing_sl = response.data.sl;
            }
        } catch (error) {
            console.log("AJAX error:", error);
        } finally {
            $("#loadingDiv").hide();
        }

        if (!packing_sl) {
            alert("Serial number not generated");
            return false;
        }
        sl++;
        let table = $("#table_" + id);
        let buttonElement = document.getElementById("button_" + id); 
        console.log(buttonElement.getAttribute('data-item'));
        let item = JSON.parse(buttonElement.getAttribute('data-item'));
        if (table.find("tbody").length === 0) {
            table.append("<tbody></tbody>");
        }
        let formula = item?.formula_ideal_weight;
        let valueObj = item?.valueObject;
 
        let is_piece = item.units === 'Piece'?true:false;
        let colspan = 2;
        let td = `<td> <input type='text' class="form-control bag_sl_no" style="width:100px" name='roll[${sl}][sl_no]' value="${packing_sl}" required onkeypress="return isNum(event);" placeholder="Sl No."  /></td>`;
        if(is_piece){
            colspan=0;
            td+=` 
                <td>
                    <input data-id="${sl}" type='text' class="form-control" style="width:100px" placeholder="Piece" id='roll_${sl}_pieces' name='roll[${sl}][pieces]' ${item.units === 'Piece' ? 'required' : ''} onkeypress="return isNumDot(event);" onkeyup="calculateIdealWeight(event,${id})" />
                    <span class="error-text" id="roll_${sl}_pieces-error"></span>
                </td>                
                <td>
                    <input type='text' class="form-control idealWeight" style="width:100px" placeholder="IdealWeight" id='roll_${sl}_idealWeight' name='roll[${sl}][idealWeight]' readonly value='' />
                </td>
            `;
        }
        td+=`
                <td colspan='${colspan}'>
                    <input type='hidden' name='roll[${sl}][id]' value='${item.id}' />
                    <input type='text' class="form-control" style="width:100px" placeholder="Weight" id='roll_${sl}_weight' name='roll[${sl}][weight]' required onkeypress="return isNumDot(event);" onkeyup="addColorInput(${sl})" />
                    <span class="error-text" id="roll_${sl}_weight-error"></span>
                </td>
        `;
        let tr = $("<tr>").attr("data-id", item.id)
            .append(`${td}                           
                <td><button type='button' onclick='removeTr(this, ${id})' class='btn btn-sm btn-warning'>X</button></td>
            `);
        
        table.find("tbody").append(tr);
        table.show();
        $("#submit").show();
    }

    function removeTr(element, id) {
        $(element).closest("tr").remove();
        let table = $("#table_" + id);
        
        if (table.find("input[type='hidden']").length === 0) {
            table.hide();
        }

        if ($("input[type='hidden'][name^='roll']").length === 0) {
            $("#submit").hide();
            $("#packing_date").attr("readonly",false);
        }
    }

    function entryFormSubmit(){
        if ($("input[type='hidden'][name^='roll']").length === 0) {
            return false;
        }
        $.ajax({
            url:"{{route('packing.entry.wip.add')}}",
            type:"post",
            dataType:"json",
            data:$("#entryForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data?.status){
                    modelInfo(data?.message);
                    $("#entryForm").get(0).reset();
                    $("#packing_date").attr("readonly",false);
                    searchData();
                    // $("#orderRoll").DataTable().ajax.reload();
                    sl=0;
                }else{
                    modelInfo("Server Error","error");
                    console.log(data);
                }
            },
            error:function(errors){
                $("#loadingDiv").hide();
                modelInfo("Server Error","error");
                console.log(data);
            }
        });
    }

    function disburseOrderConform(orderId,message){
        showConfirmDialog("Are You Sure Discard "+message+"??",function(){ 
            disburseOrder(orderId);
        });
    }

    function deleteWipConform(orderId){
        showConfirmDialog("Are You Sure Delete??",function(){ 
            deleteWip(orderId);
        });
    }

    function deleteWip(orderId){
        let buttonElement = document.getElementById("button_" + orderId); 
        let item = JSON.parse(buttonElement.getAttribute('data-item'));
        $.ajax({
            url:"{{route('packing.wip.delete')}}",
            type:"post",
            dataType:"json",
            data:{"orderId":orderId,"roll_ids":item.roll_ids},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                console.log(response);                
                $("#loadingDiv").hide();
                if(response.status){
                    modelInfo(response.message);
                    searchData();
                }else{
                    modelInfo(response?.message||"server error!!","error");
                }
            },
            error:function(errors){
                console.log(errors);
                modelInfo("server error!!","error");
                $("#loadingDiv").hide();
            }
        });

    }

    function disburseOrder(orderId){
        let buttonElement = document.getElementById("button_" + orderId); 
        let item = JSON.parse(buttonElement.getAttribute('data-item'));
        $.ajax({
            url:"{{route('packing.wip.disburse.order')}}",
            type:"post",
            dataType:"json",
            data:{"id":orderId,"balance":item.balance,"balance_pieces":item.balance_in_pieces,"roll_ids":item.roll_ids},
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                console.log(response);                
                $("#loadingDiv").hide();
                if(response.status){
                    modelInfo(response.message);
                    searchData();
                }else{
                    modelInfo("server error!!","error");
                }
            },
            error:function(errors){
                console.log(errors);
                modelInfo("server error!!","error");
                $("#loadingDiv").hide();
            }
        })
    }

    function searchData(){
        $('#orderRoll').DataTable().ajax.reload(function(){
            addFilter('orderRoll',[0]);
        },false);
    }

    function calculateIdealWeight(event,id){
        let value = event.target.value;
        let dataId = event.target.getAttribute('data-id') ;
        let buttonElement = document.getElementById("button_" + id); 
        let item = JSON.parse(buttonElement.getAttribute('data-item'));
        let weightPerBag = item?.weight_per_bag||0;
        $("#roll_"+dataId+"_idealWeight").val((weightPerBag*value).toFixed(2));
        console.log(id);
        console.log(value);
        console.log(weightPerBag);
        addColorInput(dataId);
    }

    function addColorInput(slNo){
        let weight = $("#roll_"+slNo+"_weight").val();
        let picess = $("#roll_"+slNo+"_pieces").val();
        let idealWeight = $("#roll_"+slNo+"_idealWeight").val();
        $("#roll_"+slNo+"_idealWeight").css("color","black");
        if(weight !=idealWeight && picess){
            $("#roll_"+slNo+"_idealWeight").css("color","red");
        }
    }
</script>

@include('layout.footer')
