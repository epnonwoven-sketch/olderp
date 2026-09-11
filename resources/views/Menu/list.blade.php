@include("layout.header")
<!-- Main Component -->



<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Library</li>
                </ol>
            </nav>

        </div>
        <div class="panel-heading">
            <h5 class="panel-title">Menu List</h5>
            <div class="panel-control">
                <a href="{{route('menu.update.user.list')}}" class="btn btn-primary">User Type Update Menu</a>
                <button type="button" class="btn btn-primary fa fa-arrow-right" data-bs-toggle="modal" data-bs-target="#addMenuModel" onclick="resetFormById('myForm')">
                    Add <ion-icon name="add-circle-outline"></ion-icon>
                </button>
            </div>
        </div>
    </div>
    <div class="container">
        <table id="postsTable" class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Icon</th>
                    <th>Menu Name</th>
                    <th>Menu Path</th>
                    <th>Permitted To</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade modal-lg"  id="addMenuModel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Add Menu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id ="myForm" action="">
                        @csrf
                        <div class="panel-body">
                            <input type="hidden" id="menu_id" name="id" value="">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Menu Name</label>
                                        <input type="text" maxlength="100" id="menu_name" name="menu_name" class="form-control" placeholder="Enter Menu Name" value="<?=(isset($menuDtl['menu_name']))?$menuDtl['menu_name']:'';?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Order No</label>
                                        <input type="text" maxlength="2" id="order_no" name="order_no" class="form-control" placeholder="Enter Order No" value="<?=(isset($menuDtl['order_no']))?$menuDtl['order_no']:'';?>" onkeypress="return isNum(event);">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Under Menu Name</label>
                                        <select id="parent_menu_mstr_id" name="parent_menu_mstr_id" class="form-control">
                                            <option value="0">#</option>
                                            <option value="-1" <?=(isset($menuDtl['parent_menu_mstr_id']))?("-1"==$menuDtl['parent_menu_mstr_id'])?'selected':'':'';?> >DIRECT ACTIVE MENU</option>
                                        <?php
                                        if(isset($underMenuNameList)){
                                            foreach ($underMenuNameList as $values){
                                        ?>
                                            <option value="<?=$values['id']?>" <?=(isset($menuDtl['parent_menu_mstr_id']))?($values['id']==$menuDtl['parent_menu_mstr_id'])?'selected':'':'';?> ><?=$values['menu_name']?></option>
                                        <?php
                                            }
                                        }
                                        ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Under Sub-Menu Name</label>
                                        <select id="parent_sub_menu_mstr_id" name="parent_sub_menu_mstr_id" class="form-control">
                                            <option value="0" <?=(isset($menuDtl['parent_sub_menu_mstr_id']))?("-1"==$menuDtl['parent_sub_menu_mstr_id'])?'selected':'':'';?>>#</option>
                                            <?php
                                            if(isset($parentSubMenuList)){
                                                foreach ($parentSubMenuList as $values){
                                            ?>
                                                <option value="<?=$values['id']?>" <?=(isset($menuDtl['parent_sub_menu_mstr_id']))?($values['id']==$menuDtl['parent_sub_menu_mstr_id'])?'selected':'':'';?> ><?=$values['menu_name']?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label class="control-label">Menu Path</label>
                                        <input type="text" id="url_path" name="url_path" class="form-control" placeholder="Enter Menu Path" value="<?=(isset($menuDtl['url_path']))?$menuDtl['url_path']:'';?>"/>
                                    </div>
                                </div>
                                <div class="col-sm-6" id="menu_icon_hide_show" style="display: none;">
                                    <div class="form-group">
                                        <label class="control-label">Menu Icon <span class="text-danger">(like : fa fa-home)</span></label>
                                        <input type="text" id="menu_icon" name="menu_icon" class="form-control" placeholder="Enter Menu Icon" value="<?=(isset($menuDtl['menu_icon']))?$menuDtl['menu_icon']:'';?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <label class="control-label"><b><u>Permission To</u></b></label>
                                </div>
                                @if($user_type_list??"")
                                    @foreach ($user_type_list as $values) 
                                        <div class="col-sm-3">
                                            <div class="checkbox">
                                                <input type="checkbox" id="user_type_mstr_id{{$values['id']}}" name="user_type_mstr_id[]" class="magic-checkbox" value="{{$values['id']}}"  />
                                            <label for="user_type_mstr_id{{$values['id']}}">{{$values['user_type']}}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>                            
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id ="addMenu">Submit</button>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    var parent_sub_menu_mstr_id = "{{$menuDtl['parent_sub_menu_mstr_id']??''}}";
    var subMenuLoadCount = "{{$menuDtl['parent_sub_menu_mstr_id']??1}}";
    $(document).ready(function() {

        $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('menu-list')}}",
            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "menu_icon",
                    name: "menu_icon"
                },
                {
                    data: "menu_name",
                    name: "menu_name"
                },
                {
                    data: "url_path",
                    name: "url_path"
                },
                {
                    data: "role_name",
                    data: "role_name",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                },
            ],
            createdRow: function(row, data, dataIndex) {
                // Apply the custom class to the row
                if (data?.menu_type==0 && data?.url_path!='#') {
                    var cell = $('td', row).eq(2); // Targeting the 3rd column (0-based index)
                    cell.html(cell.html() + '<br> <span class="text-danger text-xs" style="font-size:10px;">(LINK)</span>');
                }else if (data?.parent_menu_mstr_id==0) {
                    var cell = $('td', row).eq(2); // Targeting the 3rd column (0-based index)
                    cell.html(cell.html() + '<br> <span class="text-danger text-xs" style="font-size:10px;">(Menu)</span>');                
                }else if (data?.parent_menu_mstr_id && data?.parent_menu_mstr_id!=0) {
                    var cell = $('td', row).eq(2); // Targeting the 3rd column (0-based index)
                    cell.html(cell.html() + '<br> <span class="text-danger text-xs"  style="font-size:10px;">('+data.parent_menu+')</span>');                
                }
            },  
        });
        shoHidIcon();

        $("#addMenu").on("click",function(){
            $("#myForm").submit();
        });

        $('button[data-bs-target="#addMenuModel"]').on("click",()=>{
            $("#myForm").get(0).reset();
        });

        $("#myForm").validate({
            rules: {
                menu_name: {
                    required: true,
                    minlength: 1
                },
                order_no: {
                    required: true,
                    number: true,
                },
                parent_menu_mstr_id: {
                    required: true,
                },
                parent_sub_menu_mstr_id: {
                    required: true,
                },
                menu_path: {
                    required: true,
                },
                menu_icon: {
                    required: true,
                },
                "user_type_mstr_id[]": {
                    required: true
                }
            },
            messages: {
                menu_name: {
                    required: "Please enter a menu name",
                    minlength: "Menu name must be at least 3 characters long"
                },
                order_no: {
                    required: "Please enter an order number",
                    number: "Please enter a valid number for the order"
                },
                parent_menu_mstr_id: {
                    required: "Please select a parent menu"
                },
                parent_sub_menu_mstr_id: {
                    required: "Please select a parent sub-menu"
                },
                url_path: {
                    required: "Please enter the menu path"
                },
                menu_icon: {
                    required: "Please select a menu icon"
                },
                "user_type_mstr_id[]": {
                    required: "Please select at least one user type"
                }
            },
            submitHandler: function(form) {
                // If form is valid, prevent default form submission and submit via AJAX
                addMenu();
            }
        });

        $('#parent_menu_mstr_id').change(function(){  
            shoHidIcon();          
            loadSubMenuMstr();
        });

    });

    function addMenu(){
        $.ajax({
                type: "POST",
                'url':"{{route('menu-add')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': $("#myForm").serialize(),
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        $("#myForm").get(0).reset();
                        $("#addMenuModel").modal('hide');
                        $('#postsTable').DataTable().draw();
                        modelInfo(data.messages);
                    }else{
                        modelInfo("Something Went Wrong!!");
                    }
                },
            }

        ) 
    }
    function shoHidIcon(){        
        var parent_menu_mstr_id = $('#parent_menu_mstr_id').val();
        if(parent_menu_mstr_id == 0) {
            $('#menu_path').attr("readonly", true);
            $('#menu_path').val('');
            $('#menu_icon_hide_show').show();

            $('#parent_sub_menu_mstr_id').attr("disabled", true);
        } else if(parent_menu_mstr_id=="-1") {
            $('#menu_path').attr("readonly", false);
            $('#menu_icon_hide_show').show();

            $('#parent_sub_menu_mstr_id').attr("disabled", true);
        } else {
            $('#menu_path').attr("readonly", false);
            $('#menu_icon').val('');
            $('#menu_icon_hide_show').show();

            $('#parent_sub_menu_mstr_id').attr("disabled", false);
        }
    }
    var loadSubMenuMstr = (sub_parent_id=0) => {
        subMenuLoadCount++;
        if ($('#parent_menu_mstr_id').val()!=0 && $('#parent_menu_mstr_id').val()!=-1) {
            $.ajax({
                type:"get",
                url: "{{route('submenu-list')}}",
                dataType: "json",
                data: {
                    "id": $('#parent_menu_mstr_id').val(),
                },
                beforeSend: function() {
                    $("#loadingDiv").show();
                },
                success:function(data){
                    if(data.status==true) {
                        $("#parent_sub_menu_mstr_id").html(data?.data?.response);
                        if (sub_parent_id!='') {
                            $("#parent_sub_menu_mstr_id").val(sub_parent_id);
                        }
                    } else {
                        $("#parent_sub_menu_mstr_id").html('<option value="0">#</option>');
                    }
                    $("#loadingDiv").hide();
                }
            });
        } else {
            $("#parent_sub_menu_mstr_id").val("0");
        }
    };

    function openModelEdit(id){
        $.ajax({
            type:"get",
            url: "{{ route('menu-edit', ':id') }}".replace(':id', id),
            dataType: "json",
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    menuDtl = data.data;
                    console.log(menuDtl); 
                    $("#menu_id").val(menuDtl?.id);
                    $("#menu_name").val(menuDtl?.menu_name);
                    $("#menu_name").val(menuDtl?.menu_name);
                    $("#order_no").val(menuDtl?.order_no);
                    $("#parent_menu_mstr_id").val(menuDtl?.parent_menu_mstr_id);
                    $("#parent_sub_menu_mstr_id").val(menuDtl?.parent_sub_menu_mstr_id);
                    $("#url_path").val(menuDtl?.url_path);
                    $("#menu_icon").val(menuDtl?.menu_icon);
                    $("#url_path").val(menuDtl?.url_path);
                    $("input[type='checkbox'][id^='user_type_mstr_id']").prop("checked", false);
                    if (Array.isArray(menuDtl?.user_type_mstr_id)) {
                        menuDtl.user_type_mstr_id.forEach(val => {
                            $(`#user_type_mstr_id${val?.id}`).prop("checked", true);
                        });
                    }
                    loadSubMenuMstr(menuDtl?.sub_parent_id);
                    $("#addMenuModel").modal("show");
                
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }

    function deactivateMenu(id){
        $.ajax({
            type:"post",
            url: "{{ route('menu-deactivate', ':id') }}".replace(':id', id),
            dataType: "json",
            data:{lock_status:true},
            beforeSend: function() {
                $("#loadingDiv").show();
            },
            success:function(data){
                if(data.status==true) {
                    $('#postsTable').DataTable().draw();
                    modelInfo(data?.messages,"success");
                } 
                $("#loadingDiv").hide();
            },
            error:function(error){
                $("#loadingDiv").hide();
            }
        });
    }
</script>

@include("layout.footer")