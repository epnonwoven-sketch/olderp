@include("layout.header")
<style>
    .tabs {
        display: flex;
        border-bottom: 2px solid #ccc;
    }

    .tab-link {
        background: #f1f1f1;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
        font-size: 16px;
        transition: background 0.3s;
    }

    .tab-link.active {
        background: #007bff;
        color: white;
        border-bottom: 3px solid #0056b3;
    }

    .tab-link:hover {
        background: #ddd;
    }

    .tab-content {
        display: none;
        padding: 20px;
        border: 1px solid #ddd;
    }

    .tab-content.active {
        display: block;
    }

</style>
    <main class="p-3">
        <div class="container-fluid">
            <div class="mb-3 text-left">
                <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
                    <ol class="breadcrumb fs-6">
                        <li class="breadcrumb-item fs-6"><a href="#">Report</a></li>
                        <li class="breadcrumb-item active fs-6" aria-current="page">Production</li>
                    </ol>
                </nav>

            </div>
        </div>
        <div class="container">          
            <div class="panel-body">
                

                <div class="tabs">
                    @foreach($machineList as $index=>$val)
                    <button class="tab-link {{$index==0?'active':''}}" onclick="openTab(event, 'tab_{{$val->id}}')">{{$val->name}}</button>
                    @endforeach
                </div>

                @foreach($machineList as $index=>$val)
                    <div id="tab_{{$val->id}}" class="tab-content {{$index==0?'active':''}}">
                        <div>
                            Machine Type <span style="font-weight: bolder;">{{$val->is_printing ? 'Printing' : ($val->is_cutting ?'Cutting' : '')}}</span>
                        </div>
                        <div class="container" style=" text-align:center;"> 
                            <!-- display: flex; -->
                            <div class="row">
                                <div class="card text-center col-md-4" style=" box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5);">
                                    <h5 class="card-header">Daily Production</h5>
                                    <div class="card-body">
                                        <p class="card-text">{{$val->daily}}</p>
                                    </div>
                                </div>
                                <div class="card text-center col-md-4" style=" box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5);">
                                    <h5 class="card-header">Weakly Production</h5>
                                    <div class="card-body">
                                        <p class="card-text">{{$val->weakly}}</p>
                                    </div>
                                </div>
                                <div class="card text-center col-md-4" style=" box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.5);">
                                    <h5 class="card-header">Monthly Production</h5>
                                    <div class="card-body">
                                        <p class="card-text">{{$val->monthly}}</p>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                        <div class="container">
                            <div class="panel-heading" style="padding: 0px; background-color:#f3eeee0a">
                                <h5 class="panel-title"></h5>
                                <h5 class="panel-control">Today Production</h5>            
                            </div>
                            <div class="panel-body row">
                                <table id="{{$val->id}}_table" class="table table-responsive table-border">
                                    <thead>
                                        <tr>
                                            <th>Purchase Date</th>
                                            <th>Vendor Name</th>
                                            <th>Quality</th>
                                            <th>Roll Size</th>
                                            <th>Gsm</th>
                                            <th>Roll Color</th>
                                            <th>Roll No</th>
                                            <th>Net Weight</th>
                                            <th>Bag Size</th>
                                            <th>Client Name</th>
                                            <th>Unit</th>
                                            <th>Hardness</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($val->todayProduction as $roll)
                                            <tr>
                                                <td>{{$roll->purchase_date}}</td>
                                                <td>{{$roll->vendor_name}}</td>
                                                <td>{{$roll->quality}}</td>
                                                <td>{{$roll->size}}</td>
                                                <td>{{$roll->gsm}}</td>
                                                <td>{{$roll->roll_color}}</td>
                                                <td>{{$roll->roll_no}}</td>
                                                <td>{{$roll->net_weight}}</td>
                                                <td>{{$roll->bag_size}}</td>
                                                <td>{{$roll->client_name}}</td>
                                                <td>{{$roll->bag_unit}}</td>
                                                <td>{{$roll->hardness}}</td>
                                            </tr>
                                            @endforeach
                                    </tbody>
                                </table>
                                <script>
                                    $('#{{$val->id}}_table').DataTable({
                                        searching:false,
                                        ordering:false,
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </main>

<script>
    function openTab(event, tabId) {
        // Hide all tab content
        document.querySelectorAll(".tab-content").forEach(tab => tab.classList.remove("active"));
        
        // Remove "active" class from all tabs
        document.querySelectorAll(".tab-link").forEach(tab => tab.classList.remove("active"));

        // Show the selected tab content
        document.getElementById(tabId).classList.add("active");

        // Highlight the clicked tab button
        event.currentTarget.classList.add("active");
    }

</script>
@include("layout.footer")