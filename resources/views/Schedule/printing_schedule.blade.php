@include("layout.header")
<!-- Main Component -->
<style>
    .move tbody tr {
        cursor: pointer;
    }
    .move tbody tr:hover {
        background-color: #f1f1f1;
    }
</style>
<style>
/* Custom background color for selected rows */
.dataTables_wrapper .selected {
    background-color: #ffc107 !important; /* Change this color as needed */
    color: #fff; /* Optional: Change text color */
}
tr.selected {
    background-color:rgb(202, 109, 27) !important;
}
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Schedule</a></li>
                    @if($machine)
                    <li class="breadcrumb-item fs-6"><a href="#">{{$machine->name}}</a></li>
                    @endif
                    <li class="breadcrumb-item active fs-6" aria-current="page">Printing</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">Roll List</h5> 
            <div class="panel-control">
                <button id="scheduleRoll" type="button" class="btn btn-primary fa fa-arrow-right" onclick="saveTheOrder()" >
                    Set Schedule
                </button>
                <button id="rescheduleRoll" type="button" class="btn btn-primary fa fa-arrow-right" style="display:none;" onclick="reSetOrderRollDivShow()" >
                    Re-Schedule
                </button>
            </div>           
        </div>
        <div class="panel-body" id="rollDive">
            <div class="tableStickyDiv">
                <table id="postsTable" class="table table-striped table-bordered move table-fixed">
                    <thead>
                        <tr>
                            <th onclick="selectAllCheck()">#</th>
                            <th>Purchase Date</th>
                            <th>Vendor Name</th>
                            <th>Hardness</th>
                            <th>Roll Type</th>
                            <th>Roll Size</th>
                            <th>GSM</th>
                            <th>Quality</th>
                            <th>Roll Color</th>
                            <th>Length</th>
                            <th>Roll No</th>
                            <th>Gross Weight</th>
                            <th>Net Weight</th>
                            <th>GSM Variation</th>

                            <th>W</th>
                            <th>L</th>
                            <th>G</th>
                            <th>Bag Type</th>
                            <th>Unit</th>
                            <th>Customer</th>
                            <th>Delivery Date</th>
                            <th>Printing Color</th>
                            <th>Loop Color</th>
                            <th>Cylinder Size</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
        <div id="setOrderRollDiv" class="panel-body" style="display:none;">
            <div class="tableStickyDiv">
                <table id="setSchedule" class="table table-striped table-bordered move table-fixed">
                    <thead>
                        <tr>
                            <th>Purchase Date</th>
                            <th>Vendor Name</th>
                            <th>Hardness</th>
                            <th>Roll Type</th>
                            <th>Roll Size</th>
                            <th>GSM</th>
                            <th>Quality</th>
                            <th>Roll Color</th>
                            <th>Length</th>
                            <th>Roll No</th>
                            <th>Gross Weight</th>
                            <th>Net Weight</th>
                            <th>GSM Variation</th>

                            <th>W</th>
                            <th>L</th>
                            <th>G</th>
                            <th>Bag Type</th>
                            <th>Unit</th>
                            <th>Customer</th>
                            <th>Delivery Date</th>
                            <th>Printing Color</th>
                            <th>Loop Color</th>
                            <th>Cylinder Size</th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-sm btn-primary" id="saveSchedule" onclick="saveScheduleRoll()">Save schedule</button>
        </div>
    </div>
</main>
<script>
    let machineId = {{$machineId}};
    let selectAll = false;
    let checkedTr = [];
    $(document).ready(function() {        
        const table = $('#postsTable').DataTable({
            processing: true,
            serverSide: false,
            select: {
                style: 'os',
                selector: 'td:not(:last-child)'
            },


            ajax: {
                url: "{{route('schedule.printing.get',':machineId')}}".replace(':machineId', machineId), // The route where you're getting data from
                data: function(d) {

                    // Add custom form data to the AJAX request
                    var formData = $("#searchForm").serializeArray();
                    $.each(formData, function(i, field) {
                        d[field.name] = field.value; // Corrected: use d[field.name] instead of d.field.name
                    });

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
                { data: "DT_RowIndex", name: "DT_RowIndex", orderable: false, searchable: false ,
                    render: function(data, type, row, meta) {
                        const rowDataEncoded = base64Encode(JSON.stringify(row));
                    
                        if (!checkedTr.find(item => item.id === row.id) && row?.sl) {
                            checkedTr.push({ id: row.id, data: row });
                        }


                            return `${meta.row + 1} <input type="checkbox" name="checkbox[]" data-row='${rowDataEncoded}' onclick='updateCheckTr(event)' class="row-select" ${row?.sl ? 'checked':''}>`;
                        }
                },
                { data: "purchase_date", name: "purchase_date" },
                { data: "vendor_name", name: "vendor_name" },
                { data: "hardness", name: "hardness" },
                { data: "roll_type", name: "roll_type" },
                { data: "size", name: "size" },
                { data: "gsm", name: "gsm" },
                { data: "quality", name: "quality" },
                { data: "roll_color", name: "roll_color" },
                { data: "length", name: "length" },
                { data: "roll_no", name: "roll_no" },
                { data: "gross_weight", name: "gross_weight" },
                { data: "net_weight", name: "net_weight" },
                { data: "gsm_variation", name: "gsm_variation" },
                { data : "w", name: "w" },
                { data : "l", name: "l" },
                { data : "g", name: "g" },
                { data : "bag_type", name: "bag_type" },
                { data : "bag_unit", name: "bag_unit" },
                { data : "client_name", name: "client_name" },
                { data : "estimate_delivery_date", name: "estimate_delivery_date" },
                { data : "print_color", name: "print_color" },
                { data : "loop_color", name: "loop_color" },
                { data : "cylinder_size", name: "cylinder_size" },
            ],
            dom: 'lBfrtip', // This enables the buttons
            language: {
                lengthMenu: "Show _MENU_" // Removes the "entries" text
            },
            lengthMenu: [
                [10, 25, 50, 100, -1], // The internal values
                ["10 Row", "25 Row", "50 Row", "100 Row", "All"] // The display values, replace -1 with "All"
            ],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="bi bi-file-earmark-excel-fill text-success"></i> ',
                    className: 'btn btn-success',
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf-fill text-danger"></i>',
                    title: 'Data Export',
                    orientation: 'portrait',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1,2, 3,4,5,6,7,8,9,10,11]  // Export only Name, Position, and Age columns
                    }

                },
            ],
            

            createdRow: function(row, data, dataIndex) {
                let td = $('td', row).eq(6); 
                td.attr("title", data?.gsm_json);
                // Apply the custom class to the row
                $(row).attr('data-id', data.id);
                $(row).attr('data-item', JSON.stringify(data));
                if (data.row_color) {
                    $(row).addClass(data.row_color);
                    if(data.row_color=="tr-client"){
                        $(row).attr("title", "book for client");
                    }else if(data.row_color=="tr-client-printed"){
                        $(row).attr("title", "roll have booked and printed");
                    }else if(data.row_color=="tr-printed"){
                        $(row).attr("title", "roll is printed");
                    }else if(data.row_color=="tr-primary-print"){
                        $(row).attr("title", "this roll will be delivering soon");
                    }else if(data.row_color=="tr-expiry-print blink"){
                        $(row).attr("title", "this roll  delivery has been expired");
                    }else if(data.row_color=="tr-argent-print"){
                        $(row).attr("title", "this roll  delivery is urgent");
                    }
                }
            },            
            initComplete: function () {
                addFilter('postsTable',[0]);
            },

        });
        $("#printingScheduleModalForm").validate({
            rules: {
                printingScheduleDate: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                printingScheduleDate();
            }
        });

        $("#printingUpdateModalForm").validate({
            rules: {
                printingUpdateDate: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                printingUpdateModal();
            }
        });

        $("#cuttingScheduleModalForm").validate({
            rules: {
                cuttingScheduleDate: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                cuttingScheduleDate();
            }
        });

        $("#cuttingUpdateModalForm").validate({
            rules: {
                cuttingUpdateDate: {
                    required: true,
                }
            },
            submitHandler: function(form) {
                cuttingUpdateModal();
            }
        });

    });


    function updateCheckTr(event) {
        const checkbox = event.target;
        const rowDataEncoded = checkbox.getAttribute('data-row');

        try {
            const rowDataDecoded = base64Decode(rowDataEncoded);
            const row = JSON.parse(rowDataDecoded); // Parse the decoded string into a JSON object
            const isChecked = checkbox.checked;

            console.log(`Checkbox for row ID ${row.id} is ${isChecked ? "checked" : "unchecked"}`);

            if (isChecked) {
                if (!checkedTr.find(item => item.id === row.id)) {
                    checkedTr.push({ id: row.id, data: row });
                }
            } else {
                checkedTr = checkedTr.filter(item => item.id !== row.id);
            }
        } catch (error) {
            console.error("Error parsing row data:", error);
        }
    }





    function selectAllCheck() {
        $('input[name="checkbox[]"]').each(function () {
            $(this).prop("checked", selectAll).click();  // Set the checkbox state and trigger change event
        });
        selectAll = !selectAll;  // Toggle the selectAll flag
    }


    $(function() {        
        $("#setSchedule tbody").sortable({
            helper: function(e, tr) {
                const originals = tr.children();
                const helper = tr.clone();
                helper.children().each(function(index) {
                    $(this).width(originals.eq(index).width());
                });
                return helper;
            },

            update: function(event, ui) {
                let order = [];
                $("#setSchedule tbody tr").each(function(index, element) {
                    order.push({
                        id: $(element).data('id'),
                        position: index + 1,
                    });
                });
                console.log(order);
            }
        });
    });
    

    function searchData(){
        $('#postsTable').DataTable().ajax.reload(null, false);
    }

    function saveTheOrder(){
        let scheduleDate = $("#selectedDate").val();
        let order = [];
        checkedTr.forEach(function (item, index) {
            order.push({
                id: item?.id,
                position: index + 1,
                roll_no: item?.roll_no || "N/A",
                rowData: item,
            });
        });
        if(order.length === 0){
            modelInfo("Pleas select attlist on roll","info");
            return;
        }
        if(scheduleDate==""){
            modelInfo("Pleas select a date","info");
            return;
        }
        console.log("orderSelected:",order);
        
        const table = $("#setSchedule");
        const data = "";
        const tbody = $("#setSchedule tbody");
        tbody.empty();
        order.forEach(function (item, index) {
            item = item?.rowData?.data;
                tbody.append(                              
                    $("<tr>")
                    .attr('data-id', item.id)
                    .append(
                        `<td>${item.purchase_date}</td>`,
                        `<td>${item.vendor_name}</td>`,
                        `<td>${item.hardness || "N/A"}</td>`,
                        `<td>${item.roll_type}</td>`,
                        `<td>${item.size || "N/A"}</td>`,
                        `<td>${item.gsm}</td>`,
                        `<td>${item.quality}</td>`,
                        `<td>${item.roll_color || "N/A"}</td>`,
                        `<td>${item.length || "N/A"}</td>`,
                        `<td>${item.roll_no || "N/A"}</td>`,
                        `<td>${item.gross_weight || "N/A"}</td>`,
                        `<td>${item?.net_weight || "N/A"}</td>`, 
                        `<td>${item?.gsm_variation || ""}</td>`,  
                        `<td>${item?.w || ""}</td>`,
                        `<td>${item?.l || ""}</td>`,
                        `<td>${item?.g || ""}</td>`,
                        `<td>${item?.bag_type || ""}</td>`,
                        `<td>${item?.bag_unit || ""}</td>`,
                        `<td>${item?.client_name || ""}</td>`,
                        `<td>${item?.estimate_delivery_date || ""}</td>`,
                        `<td>${item?.print_color || ""}</td>`,
                        `<td>${item?.loop_color || ""}</td>`,
                        `<td>${item?.cylinder_size||""}</td>`,  
                    )
                );
        });
        setOrderRollDivShow();
    }

    function setOrderRollDivShow(){
        $("#scheduleRoll").hide();
        $("#rollDive").hide();
        $("#rescheduleRoll").show();
        $("#setOrderRollDiv").show();
    }

    function reSetOrderRollDivShow(){
        $("#scheduleRoll").show();        
        $("#rollDive").show();
        $("#rescheduleRoll").hide();
        $("#setOrderRollDiv").hide();
    }

    function saveScheduleRoll(){
        let order=[];
        $("#setSchedule tbody tr").each(function(index, element) {
            order.push({
                id: $(element).data('id'),
                position: index + 1,
            });
        });
        if(order.length>0){
            $.ajax({
                url:"{{route('schedule.printing.save',':machineId')}}".replace(':machineId', machineId),
                type:"post",
                data:{"rolls":order},
                beforeSend:function(){
                    $("#loadingDiv").show();
                },
                success:function(response){
                    $("#loadingDiv").hide();
                    if(response?.status){
                        // exportSchedule();
                        exportSchedulePdf();
                        modelInfo(response?.message);
                        searchData();
                        reSetOrderRollDivShow();
                    }else if(response?.error){
                        modelInfo(response?.message,"warning");
                    }
                    else{
                        modelInfo("server error","error");
                    }
                },
                error:function(error){
                    $("#loadingDiv").hide();
                    console.log(error);
                    modelInfo("server error","error");
                }
            })
        }
    }

    function exportSchedule(){
        let table = document.getElementById("setSchedule");
        if (!table) {
            console.error("Table element not found!");
            return;
        }

        let wb = XLSX.utils.book_new();
        let ws = XLSX.utils.table_to_sheet(table);

        XLSX.utils.book_append_sheet(wb, ws, "Sheet 1");
        XLSX.writeFile(wb, "PrintingSchedule.xlsx");

    }

    function exportSchedulePdf() {
        let table = document.getElementById("setSchedule");
        if (!table) {
            console.error("Table element not found!");
            return;
        }

        // Define the column indexes you want to include in the PDF (0-based index)
        let columnsToPrint = [1, 2, 3, 4, 5,6,7,8,9,10,11,13,14,15,16,17,18,19,20,21,22];

        let body = [];

        // Get table headers for specified columns
        let headers = [];
        let headerCells = table.rows[0].cells;
        columnsToPrint.forEach(index => {
            headers.push({ text: headerCells[index].innerText.trim(), bold: true, fillColor: '#f3f3f3' });
        });
        body.push(headers);

        // Get table rows for specified columns
        for (let i = 1; i < table.rows.length; i++) {  // Skip header row
            let row = table.rows[i];
            let rowData = [];
            columnsToPrint.forEach(index => {
                rowData.push(row.cells[index].innerText.trim());
            });
            body.push(rowData);
        }

        // Create PDF document definition
        let docDefinition = {
            content: [
                { text: 'Printing Schedule Report', style: 'header' },
                { text: `Generated on: ${new Date().toLocaleString()}`, style: 'subheader' },
                {
                    table: {
                        headerRows: 1,
                        widths: Array(columnsToPrint.length).fill('auto'),  // Auto width for each column
                        body: body
                    },
                    layout: 'lightHorizontalLines'
                }
            ],
            styles: {
                header: {
                    fontSize: 16,
                    bold: true,
                    alignment: 'center',
                    margin: [0, 0, 0, 10]
                },
                subheader: {
                    fontSize: 10,
                    italics: true,
                    alignment: 'right',
                    margin: [0, 0, 0, 10]
                }
            },
            defaultStyle: {
                fontSize: 7
            },
            pageSize: 'A4',
            pageOrientation: 'landscape',  // Use 'portrait' if you prefer
            pageMargins: [15, 15, 15, 15]  // Margins for better spacing
        };

        // Generate and download the PDF
        pdfMake.createPdf(docDefinition).download(`PrintingSchedule.pdf`);
    }

    
</script>
@include("layout.footer")