@include("layout.header")
<?php

    $r = [
        ["thisClassId", "thisClassId", 0],
        ["thisSubjectId", "thisSubjectId", 0],
        ["thisRecordId", "thisRecordId", 0],
        ["acad_year", "acad_year", "2022-2023"],
        ["print_type[]", "print_type", 318],
        ["view_type", "view_type", 0],
        ["sub_exam_type", "sub_exam_type2", 7045],
        [
            "sem_type[]", "sem_type", [
                "Sem 1,Sem 2,Year"
            ]
        ],
        ["date_opt", "date_opt", 0],
        ["date_from", "date_from", ""],
        ["date_to", "date_to", ""],
        ["sub_type", "sub_type", "all"],
        [
            "sub_id[]", "sub_id", [
                "7699,8806,8816,8817,8820,8821,8822,8901,8930,454288,8802,5116,8819,8898,8899,8903,8904,8928,8902,8925,8927,8929,8931,8926,9047,9035,9044,9045,9046,9048,9043,9078,9080,454290,454291,456464,455390,456417,456424,456427,456435,456467,456520,456521,456522,455394,456465,455403,456426,456434,456418,456425,456468,456551,455397,455400,456453,456519,456419,456443,456445,456447,456450,456451,456452,456444,456446,455441,456420,456523,456537,456538,456454,456457,456458,456460,456462,456423,456428,456430,456431,456432,456436,456439,456440,456441,456429,456438,8840,8841,8842,8844,8846,456539,456541,455196,455197,455198,455200,455202,455209,455279,455281,455283,455285,456754,456755,456756,456757,456758,456760,456761,456801"
            ]
        ]
    ];

    foreach ($r as $item) {
        
        $varName = $item[1]; // Extract variable name
        $value = $item[2]; // Extract value
        $$varName = $value; // Create dynamic variable
        
    }
    print_var($sub_id);die;
    

?>

<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Bag</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Bag History</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container"> 
        <div class="panel-body">
            <form action="" id="searchForm" >
                <div class="row">                    
                    <div class="row mt-3">                        

                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="bagStatusId">Transport Type</label>
                                <select type="text" id="bagStatusId" name="bagStatusId[]" multiple class="form-select">
                                    <option value="">Select</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    
                                </select>
                                <span class="error-text" id="bagStatusId-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="form-label" for="bagStatusId">Transport Type</label>
                                <select type="text" id="bagStatusId3" name="bagStatusId3[]" multiple class="form-select">
                                    <option value="">Select</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    
                                </select>
                                <span class="error-text" id="bagStatusId-error"></span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                iii <input type="text" name="inputs">
                            </div>

                        </div>
                    </div>                   
                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="button" class="btn btn-success" id="btn_search" onclick="searchData()">Search</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
    </div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
    Launch demo modal
    </button>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" id="searchForm2" >
                    <div class="row">                    
                        <div class="row mt-3">                        

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="form-label" for="bagStatusId">Transport Type</label>
                                    <select type="text" id="bagStatusId2" name="bagStatusId[]" multiple class="form-select">
                                        <option value="">Select</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                        <option value="6">6</option>
                                        <option value="7">7</option>
                                        <option value="8">8</option>
                                        
                                    </select>
                                    <span class="error-text" id="bagStatusId-error"></span>
                                </div>
                            </div>
                            
                        </div>                   
                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-sm-12 text-end">
                                <button type="button" class="btn btn-success" id="btn_search" onclick="searchData2()">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
            </div>
        </div>
    </div>
</main>
<script>
    $(document).ready(function () {
        $("#bagStatusId2").select2({
            placeholder: "Select tags",
            allowClear: false,
            maximumSelectionLength: 4,
            dropdownCssClass: 'form-control',
            dropdownParent: $('#exampleModal'),            
            width:"100%",
        });

        $("#bagStatusId").select2();
        $("#bagStatusId3").select2();
    });

    // Copy selected values from bagStatusId to bagStatusId2
    function searchData() {
        let selectedValues = $("#bagStatusId").val(); // Get selected values
        $("#bagStatusId2").val(selectedValues).trigger('change'); // Set values & update Select2
    }

    // Serialize form data correctly
    // function searchData2() {
    //     let form1 = $("#searchForm").serializeArray();
    //     let form2 = $("#searchForm2").serializeArray();
    //     let formData = {};

    //     $.each(form1, function (_, field) {
    //         $.each(form2, function (_, field2) {
    //             if(field.name.match(/\[\]$/)){
    //                 if(field.name==field2.name){

    //                 }
    //                 else{
                        
    //                     if(formData[field.name]){
    //                         formData[field.name].push(field.value);
    //                     }
    //                     else{
    //                         formData[field.name] = [field.value];
    //                     }
    //                 }
    //             }
    //             else{
    //                 if(field.name==field2.name){

    //                 }else{
    //                     formData[field.name] = field.value;
    //                 }
    //             }
    //         });
            
    //     });

    //     $.each(form2, function (_, field) {
    //         if(field.name.match(/\[\]/)){
    //             if(formData[field.name]){
    //                 formData[field.name].push(field.value);
    //             }else{
    //                 formData[field.name] = [field.value];
    //             }
    //         }
    //         else{
    //             formData[field.name] = field.value;
    //         }
    //     });

    //     console.log(formData); // Check the combined form data
    //     $.ajax({
    //         url:"hello.com",
    //         type:"post",
    //         data:formData,
    //         success:function(res){

    //         }
    //     })
    // }


    function searchData2() {
    let form1 = $("#searchForm").serializeArray();
    let form2 = $("#searchForm2").serializeArray();
    let formData = {};

    // Convert form2 into a quick lookup map for field existence check
    let form2Keys = {};
    $.each(form2, function (_, field) {
        form2Keys[field.name] = true;
    });

    // Process form1 first (Only if the key does NOT exist in form2)
    $.each(form1, function (_, field) {
        if (field.name.match(/\[\]$/)) { // Handle multi-value fields (Array)
            if (!form2Keys[field.name]) { // Only add if NOT in form2
                if (!formData[field.name]) {
                    formData[field.name] = [];
                }
                if (!formData[field.name].includes(field.value)) { // Prevent duplicates
                    formData[field.name].push(field.value);
                }
            }
        } else { // Single-value fields
            if (!form2Keys[field.name] && !(field.name in formData)) { 
                formData[field.name] = field.value;
            }
        }
    });

    // Process form2 (Overwrite values from form1)
    $.each(form2, function (_, field) {
        if (field.name.match(/\[\]$/)) { // Handle multi-value fields
            if (!formData[field.name]) {
                formData[field.name] = [];
            }
            if (!formData[field.name].includes(field.value)) { // Prevent duplicates
                formData[field.name].push(field.value);
            }
        } else { // Single-value fields
            formData[field.name] = field.value; // Overwrite if exists
        }
    });

    console.log(formData); // Debugging: Check the final result

    $.ajax({
        url: "hello.com",
        type: "POST",
        data: formData,
        success: function (res) {
            console.log("Success:", res);
        }
    });
}


//     function searchData2() {
//     let form1 = $("#searchForm").serializeArray();
//     let form2 = $("#searchForm2").serializeArray();
//     let formData = {};

//     function processForm(form, overwrite = false) {
//         $.each(form, function (_, field) {
//             if (field.name.match(/\[\]$/)) { // Handle multiple values (fields with [])
//                 if (!formData[field.name] || overwrite) {
//                     formData[field.name] = []; // Initialize or overwrite
//                 }
//                 if (!formData[field.name].includes(field.value)) { // Prevent duplicates
//                     formData[field.name].push(field.value);
//                 }
//             } else { // Handle single-value fields
//                 if (!formData[field.name] || overwrite) {
//                     formData[field.name] = field.value;
//                 }
//             }
//         });
//     }

//     // Process form1 first (lower priority)
//     processForm(form1);

//     // Process form2 second (higher priority, overwrites form1 values)
//     processForm(form2, true);

//     console.log(formData); // Debugging: Check the combined form data

//     $.ajax({
//         url: "hello.com",
//         type: "POST",
//         data: formData,
//         success: function (res) {
//             console.log("Success:", res);
//         }
//     });
// }


    


</script>
@include("layout.footer")