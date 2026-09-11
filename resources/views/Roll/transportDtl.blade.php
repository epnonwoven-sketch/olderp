<style>
    .crossed-out td {
        text-decoration: line-through;
        color: red;
    }
</style>
<div class="panel-heading">
    <span class="panel-title">Chalan Detail</span>
</div>                        
<div class ="panel-body">
    <div class="row mb-3">
        <label class="control-label col-md-2" for="clientName">Chalan No :</label>
        <b class="col-md-4">{{$transport->invoice_no}}</b>
    </div>
    <div class="row mb-3">
        <label class="control-label col-md-2" for="clientName">Total Role :</label>
        <b class="col-md-4">{{$transport->total_roll}}</b>
        <label class="control-label col-md-2" for="clientName">Return Role :</label>
        <b class="col-md-4 text-danger">{{$transport->total_return_roll}}</b>
    </div>
    <div class="row mb-3">
        @if($auto)
            <label class="control-label col-md-2" for="clientName">Auto Name :</label>
            <b class="col-md-2">{{$transport->auto_name}}</b>
        @endif
        <label class="control-label col-md-2" for="clientName">{{$transport->transporterLabel}}</label>
        <b class="col-md-2">{{$transport->transporter_name}}</b>
        <label class="control-label col-md-2" for="clientName">{{$transport->gstLabel}}</label>
        <b class="col-md-2">{{$transport->gst_no}}</b>
    </div>
</div>
<div class ="panel-body">
    @if($clientDtl)
        <div class="panel-heading">
            <span class="panel-title">Client Detail</span>
        </div>
        <div class ="panel-body">
            <div class="row mb-3">
                <label class="control-label col-md-2" for="clientName">Client Name :</label>
                <b class="col-md-4">{{$clientDtl->client_name??""}}</b>
                <label class="control-label col-md-2" for="clientName">Mobile No :</label>
                <b class="col-md-4">{{$clientDtl->mobile_no??""}}</b>
            </div>
            <div class="row mb-3">
                <label class="control-label col-md-2" for="clientName">Address :</label>
                <b class="col-md-6">{{$clientDtl->address??""}}</b>
                <label class="control-label col-md-2" for="clientName">Sector :</label>
                <b class="col-md-2 ">{{$clientDtl->sector??""}}</b>
            </div>
            <div class="row mb-3">
                <label class="control-label col-md-2" for="clientName">State :</label>
                <b class="col-md-4">{{$clientDtl->state??""}}</b>
                <label class="control-label col-md-2" for="clientName">City</label>
                <b class="col-md-4">{{$clientDtl->city??""}}</b>
            </div>
        </div>  
    @endif
    @if($vendorDtl)
        <div class="panel-heading">
            <span class="panel-title">Vendor Detail</span>
        </div>
        <div class ="panel-body">
            <div class="row mb-3">
                <label class="control-label col-md-2" for="clientName">Vendor Name :</label>
                <b class="col-md-4">{{$vendorDtl->vendor_name??""}}</b>
                <label class="control-label col-md-2" for="clientName">Mobile No :</label>
                <b class="col-md-4">{{$vendorDtl->mobile_no??""}}</b>
            </div>
            <div class="row mb-3">
                <label class="control-label col-md-2" for="clientName">Address :</label>
                <b class="col-md-6">{{$vendorDtl->address??""}}</b>
            </div>            
        </div>
    @endif
    
    <div class="panel-heading">
        <span class="panel-title">Roll Detail</span>
    </div>
    <div class="panel-body">
        <table class="table table-bordered  table-responsive table-fixed">
            <thead>
                <tr>
                    <th>Roll No</th>
                    <th>Role Type</th>
                    <th>Quality</th>
                    <th>Color</th>
                    <th>GSM</th>
                    <th>Size</th>
                    <th>Net Weight</th>
                    <th>Gross Weight</th>
                    <th>Hardness</th>
                    <th>Is Roll Returned</th>

                </tr>
            </thead>
            <tbody>
                @foreach($rolls as $roll)
                <tr {{$roll->is_roll_return ? "class=crossed-out":""}}>
                    <td>{{$roll->roll_no}}</td>
                    <td>{{$roll->roll_type}}</td>
                    <td>{{$roll->quality}}</td>
                    <td>{{$roll->roll_color}}</td>
                    <td>{{$roll->gsm}} {{collect(json_decode($roll->gsm_json,true))->implode(",")}}</td>
                    <td>{{$roll->size}}</td>
                    <td>{{$roll->net_weight}}</td>
                    <td>{{$roll->gross_weight}}</td>                    
                    <td>{{$roll->hardness}}</td>
                    <td>{{$roll->is_roll_return ?'Yes':"No"}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>