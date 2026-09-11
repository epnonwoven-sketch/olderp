@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Master</a></li>
                    <li class="breadcrumb-item fs-6"><a href="{{route('menu-list')}}">Master List</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Update Menu</li>
                </ol>
            </nav>

        </div>
    </div>
    <div class="container">
        <table id="postsTable" class="table table-striped table-border">
            <thead>
                <tr>
                    <th>#</th>
                    <th>User Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</main>
<script>
    $(document).ready(function() {

        $('#postsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{route('menu.update.user.list')}}",
            columns: [
                {
                    data: "DT_RowIndex",
                    name: "DT_RowIndex",
                    orderable: false,
                    searchable: false
                },
                {
                    data: "user_type",
                    name: "user_type"
                },
                {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false
                },
            ], 
        });
    });

    function updateMenu(id){
        $.ajax({
                type: "POST",
                'url':"{{route('menu.update.user.type')}}",            
                                
                "deferRender": true,
                "dataType": "json",
                'data': {"id":id},
                beforeSend: function () {
                    $("#loadingDiv").show();
                },
                success: function (data) {
                    $("#loadingDiv").hide();
                    console.log(data);
                    if(data.status){
                        $('#postsTable').DataTable().draw();
                        modelInfo(data.message);
                    }else{
                        modelInfo("Something Went Wrong!!","warning");
                    }
                },
                error:function(error){
                    console.log(error);
                    $("#loadingDiv").hide();
                    modelInfo("Server Error","error");
                }
            }

        ) 
    }
</script>

@include("layout.footer")