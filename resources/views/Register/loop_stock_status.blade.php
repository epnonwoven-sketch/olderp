@include("layout.header")
<!-- Main Component -->
<main class="p-3">
    <div class="container-fluid">
        <div class="mb-3 text-left">
            <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                <ol class="breadcrumb fs-6">
                    <li class="breadcrumb-item fs-6"><a href="#">Roll</a></li>
                    <li class="breadcrumb-item active fs-6" aria-current="page">Loop Stock</li>
                </ol>
            </nav>

        </div>        
    </div>
    <div class="container">
        <div class="panel-heading">
            <h5 class="panel-title">List</h5>
            <div class="panel-control">
                 <button class="btn btn-danger" onclick="sellLoop()">Sell The Loop</button>
             </div>
        </div>
        <div class="panel-body">
            <input type="hidden" id="selectedLoop">
            <table id="postsTable" class="table table-striped table-bordered" style="text-align: center;">
                <thead>
                    <tr>
                        <th colspan="6" style="text-align:right; font-size:small; color:#3c00ff"> Balance Units(Kg) - {{$total_balance??0}}</th>
                    </tr>
                    <tr>
                        <th>#</th>
                        <th>Loop Color</th>
                        <th>Opening Balance</th>
                        <th>Booked</th>
                        <th>Balance</th>
                        <th>Min Limit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($loop as $key=> $val)
                    <tr>
                        <td>{{$key+1}}</td>
                        <td style="text-align: left">                            
                            {{$val->loop_color}} 
                        </td>
                        <td>{{$val->opening_balance}}</td>
                        <td>{{$val->book_loop}}</td>
                        <td>{{$val->balance}}</td>
                        <td>{{$val->min_limit}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</main>


@include("layout.footer")