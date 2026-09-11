@include("layout.header")
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Entry</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>            
        </div>
        <div class="panel-body">
            <form id="searchForm" class="row g-3">
                @csrf
                <div class="row">                    
                    <div class="row mt-3">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="clientId">Client</label>
                                <select name="clientId" id="clientId" class="form-select select-option">
                                    <option value="">Select</option>
                                    @foreach ($clientList as $val)
                                        <option value="{{ $val->id }}">{{ $val->client_name }}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="clientId-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="bagTypeId">Bag Type</label>
                                <select name="bagTypeId" id="bagTypeId" class="form-select select-option">
                                    <option value="">Select</option>
                                    @foreach ($bagList as $val)
                                        <option value="{{ $val->id }}">{{ $val->bag_type }}</option>
                                    @endforeach
                                </select>
                                <span class="error-text" id="bagTypeId-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="l">L</label>
                                <input type="text" name="l" id="l" class="form-control" onkeypress="return isNumDot(event);" />                                  
                                <span class="error-text" id="l-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="w">W</label>
                                <input name="w" id="w" class="form-control" onkeypress="return isNumDot(event);" />                                                                                                            
                                <span class="error-text" id="w-error"></span>
                            </div>
                        </div>                        
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
        <div class="panel-body">
            <div style="text-align: center;" id="orderHistory">
                <div class="example-box movable" style="right: 30px; width:500px">
                    <div class="header">
                        <span>Old Orders</span>
                        <span class="icons">▼</span>
                    </div>
                    <div class="content">
                        <div id="history">
                        </div>
                    </div>
                </div>
            </div>
            <form action="" id="entryForm">
                <div class="row">
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
                                <th>Bag Weight</th>
                                <th>Bag Piece</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary" id="submit">Submit</button>
                </div>
            </form>
        </div>
    </div>
</main>
<script>
    let sl=0;
    $(document).ready(function(){
        $("#searchForm").validate({
            rules: {
                clientId: {
                    required: true,
                },

                bagTypeId: {
                    required: true,
                },
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                searchForm();
            }
        });

        $("#entryForm").validate({
            rules: {
                "roll[][id]": {
                    required: true,
                    number: true,
                },
                "roll[][weight]":{
                    required:true,
                    number: true,
                },
            },
            submitHandler: function(form) {
                entryFormSubmit();
            }
        });

        $("#submit").hide();
        $("#orderHistory").hide(); 

        addEventListenersToForm("orderHistory");
    });

    function searchForm(){
        $.ajax({
            url:"{{route('packing.entry.search')}}",
            type:"post",
            dataType:"json",
            data:$("#searchForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(data){
                $("#loadingDiv").hide();
                if(data?.status){
                    if(data?.data.length>0){
                        $("#orderHistory").show();                        
                        setHintDefault("orderHistory");
                        $("#history").empty();

                        // Create the table
                        const table = $("<table>").addClass("history-table");
                        const thead = $("<thead>").append(
                            $("<tr>").append(
                                "<th>Sl</th>",
                                "<th>Entry</th>",
                                "<th>Roll No</th>",
                                "<th>GSM</th>",
                                "<th>Roll Color</th>",
                                "<th>Length</th>",
                                "<th>Size</th>",
                                "<th>Net Weight</th>",
                                "<th>roll Type</th>",
                                "<th>Hardness</th>",
                                "<th>Bag</th>",
                                "<th>Bag Unit</th>",
                                "<th>W</th>",
                                "<th>L</th>",
                                "<th>G</th>",
                                "<th>Printing Colors</th>",
                            )
                        );

                        const tbody = $("<tbody>");
                        
                        // Populate the rows
                        data.data.forEach((item,index) => {
                            tbody.append(                               
                                $("<tr>").append(
                                    `<td>${index+1}</td>`,
                                    `<td><button data-item='${JSON.stringify(item)}' id="or${index}" onclick="addToBook('or${index}')" class="btn btn-sm btn-info">Place Entry</button></td>`,
                                    `<td>${item.roll_no}</td>`,
                                    `<td>${item.gsm}</td>`,
                                    `<td>${item.roll_color || "N/A"}</td>`,
                                    `<td>${item.length || "N/A"}</td>`,
                                    `<td>${item.size || "N/A"}</td>`,
                                    `<td>${item.net_weight || "N/A"}</td>`,
                                    `<td>${item.roll_type || "N/A"}</td>`,
                                    `<td>${item.hardness || "N/A"}</td>`,
                                    `<td>${item.bag_type || "N/A"}</td>`,
                                    `<td>${item.bag_unit || "N/A"}</td>`,
                                    `<td>${item.w || "N/A"}</td>`,
                                    `<td>${item.l || "N/A"}</td>`,
                                    `<td>${item.g || "N/A"}</td>`,
                                    `<td>${JSON.parse(item.printing_color || "[]").join(", ")}</td>`,
                                )
                            );
                        });

                        // Append the table structure
                        table.append(thead).append(tbody);
                        $("#history").append(table);

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

                    }else{
                        $("#orderHistory").hide();
                        $("#history").html("<p>No records found.</p>");
                        modelInfo("Data Not Find","warning");
                    }
                }else{
                    modelInfo("Server Error","error");
                    console.log(data);
                }
            },
            error:function(errors){
                modelInfo("Server Error","error");
                console.log(data);
            }
        })
    }

    function addToBook(id){
        sl = sl+1;
        const item = JSON.parse($(event.target).attr("data-item"));
        const existingRow = $(`#history tbody tr[data-id="${item.id}"]`);
        const tr = $("<tr>")
                    .attr("data-id", item.id)
                    .addClass((item.stock=="stock" ?"table-info" : "table-danger"))
                    .append(
                                `<td>${item.roll_no} <input type='hidden' name='roll[${sl}][id]' value='${item.id}' /></td>`,
                                `<td>${item.gsm}</td>`,
                                `<td>${item.roll_color || "N/A"}</td>`,
                                `<td>${item.length || "N/A"}</td>`,
                                `<td>${item.size || "N/A"}</td>`,
                                `<td>${item.net_weight || "N/A"}</td>`,
                                `<td>${item.roll_type || "N/A"}</td>`,
                                `<td>${item.hardness || "N/A"}</td>`,  
                                `<td><input type='text' id='roll[${sl}][weight]' name='roll[${sl}][weight]' required onkeypress="return isNumDot(event);" /> <span class="error-text" id="roll[${sl}][weight]-error"></span></td>`, 
                                `<td><input type='text' id ='roll[${sl}][pieces]' name='roll[${sl}][pieces]' ${item.bag_unit=='Piece' ? 'required' :''} onkeypress="return isNumDot(event);" /> <span class="error-text" id="roll[${sl}][pieces]-error"></span> </td>`,                              
                                `<td><span onclick='removeTr(this)' class='btn btn-sm btn-warning'>X</span></td>`,
                            );
        $("#orderRoll tbody").append(tr);
        $("#submit").show();
        
    }

    function removeTr(element) {
        $(element).closest("tr").remove();
        if ($("input[type='hidden'][name^='roll']").length === 0) {
            $("#submit").hide();
        }
    }

    function entryFormSubmit(){
        if ($("input[type='hidden'][name^='roll']").length === 0) {
            return false;
        }
        $.ajax({
            url:"{{route('packing.entry.add')}}",
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
                    $("#orderRoll tbody").empty();
                    $("#orderHistory").hide(); 
                    sl=0;
                }else{
                    modelInfo("Server Error","error");
                    console.log(data);
                }
            },
            error:function(errors){
                modelInfo("Server Error","error");
                console.log(data);
            }
        });
    }
</script>
@include("layout.footer")