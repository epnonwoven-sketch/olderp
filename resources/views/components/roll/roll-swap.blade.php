<div class="modal fade modal-lg" id="rollSwapSwapModal" tabindex="-1" aria-labelledby="rollSwapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rollSwapModalLabel">Swap Roll</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rollSwapEditForm">
                    @csrf
                    <!-- Hidden field for rollSwap ID -->
                    <div class="row">                                                
                        <table id="swap_tbl" class="table table-sm table-bordered " style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Roll No.1</th>
                                    <th>Roll No.2</th>
                                    <th><i class="btn btn-sm bi bi-plus-circle-fill" style="color: #4f85dc;" onclick="addSwapTr()"></i></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <!-- Submit Button -->
                    <div class="row mt-4">
                        <div class="col-sm-12 text-end">
                            <button type="submit" class="btn btn-success" id="swap" >Swap</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let sl=0;
    $('#rollSwapSwapModal').on('shown.bs.modal', function () {
        // Your code to execute after the modal is shown
        resetModel();
    });

    function resetModel(){
        sl =0;     
        $("#swap_tbl tbody").empty();
    }

    function addSwapTr(){        
        let tr = `<tr>
                    <td>
                        <input type="text" class="form-control" id="firstRoll_${sl}" name="roll[firstRoll][${sl}]" value="" required />
                        <span class="error-text" id="roll.firstRoll.${sl}-error"></span>
                    </td>    
                    <td> 
                        <input type="text" class="form-control" id="secondRoll_${sl}" name="roll[secondRoll][${sl}]" value="" required />
                        <span class="error-text" id="roll.secondRoll.${sl}-error"></span>
                    </td>                         
                    <td>
                        <i class=" btn btn-sm bi bi-trash-fill color-danger" style="color: red;" onclick='removeSwapTr(this)'></i>
                    </td>
                </tr>
                `;
        $("#swap_tbl tbody").append(tr);
        sl = sl+1;        
    }

    function removeSwapTr(element) {
        $(element).closest("tr").remove();
    }
    
    $("#rollSwapEditForm").validate({
        rules: {
            "roll[firstRoll][]": {
                required: true,
            },
            "roll[secondRoll][]": {
                required: true,
            },
        },
        submitHandler: function(form) {
            // If form is valid, prevent default form submission and submit via AJAX
            addEventListenersToForm("rollSwapEditForm");
            showConfirmDialog("are you sure want to swap roll no",swapRoll);
        }
    });

    function swapRoll(){
        $.ajax({
            url:"{{route('roll.swap')}}",
            type:"post",
            data:$("#rollSwapEditForm").serialize(),
            beforeSend:function(){
                $("#loadingDiv").show();
            },
            success:function(response){
                $("#loadingDiv").hide();
                if(response.status){
                    $("#rollSwapSwapModal").modal("hide");
                    $("#swap_tbl tbody").empty();
                    searchData();
                }else if (response?.errors) {
                    let errors = response?.errors;
                    console.log(response?.errors);
                    modelInfo(response.message,"warning");
                    for (field in errors) {
                        console.log(field);
                        var fieldId = field.replace(/\./g, '\\.'); 
                        $(`#${fieldId}-error`).text(errors[field][0]);
                    }
                } else{
                    modelInfo("server error","error");
                }
            },
            error:function(errors){
                console.log(errors);
                $("#loadingDiv").hide();
                modelInfo("server error","error");
            }
        })
    }
    function searchData(){
        $('#postsTable').DataTable().ajax.reload(function(){
            addFilter('postsTable',[0]);
        },false);
        
    }
</script>