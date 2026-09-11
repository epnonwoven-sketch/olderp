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
        <label class="control-label col-md-2" for="clientName">Type :</label>
        <b class="col-md-4">{{$transport->transition_type}}</b>
    </div>
    <div class="row mb-3">
        <label class="control-label col-md-2" for="clientName">Total bags :</label>
        <b class="col-md-4">{{$transport->total_bag}}</b>
        <label class="control-label col-md-2" for="clientName">Return Bag :</label>
        <b class="col-md-4 text-danger">{{$transport->total_return_bag}}</b>
    </div>
    <div class="row mb-3">
        <label class="control-label col-md-2" for="clientName">Auto Name :</label>
        <b class="col-md-2">{{$transport->auto_name}}</b>
        <label class="control-label col-md-2" for="clientName">{{$transport->transporterLabel}}</label>
        <b class="col-md-2">{{$transport->transporter_name}}</b>
        <label class="control-label col-md-2" for="clientName">{{$transport->gstLabel}}</label>
        <b class="col-md-2">{{$transport->gst_no}}</b>
    </div>
</div>
@foreach($order as $index=>$val)
    <div class="panel-heading">
        <span class="panel-title">Order Detail {{$index+1}}</span>
    </div> 
    <div class ="panel-body">
        <div class="panel-heading">
            <span class="panel-title">Client Detail</span>
        </div>
        <div class ="panel-body">
            <div class="row mb-3">
                <label class="control-label col-md-2" for="clientName">Client Name :</label>
                <b class="col-md-4">{{$val->clientDtl->client_name??""}}</b>
                <label class="control-label col-md-2" for="clientName">Mobile No :</label>
                <b class="col-md-4">{{$val->clientDtl->mobile_no??""}}</b>
            </div>
            <div class="row mb-3">
                <label class="control-label col-md-2" for="clientName">Address :</label>
                <b class="col-md-6">{{$val->clientDtl->address??""}}</b>
                <label class="control-label col-md-2" for="clientName">Sector :</label>
                <b class="col-md-2 ">{{$val->clientDtl->sector??""}}</b>
            </div>
            <div class="row mb-3">
                <label class="control-label col-md-2" for="clientName">State :</label>
                <b class="col-md-4">{{$val->clientDtl->state??""}}</b>
                <label class="control-label col-md-2" for="clientName">City</label>
                <b class="col-md-4">{{$val->clientDtl->city??""}}</b>
            </div>
        </div>  
        
        <div class="panel-heading">
            <span class="panel-title">Bag Detail</span>
        </div>
        <div class="panel-body">
            <table class="table table-bordered  table-responsive table-fixed">
                <thead>
                    <tr>
                        <th>Bag No</th>
                        <th>Bag Color</th>
                        <th>Bag Weight</th>
                        <th>Is Return</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($val->bags as $bag)
                    <tr {{$bag->is_bag_return ? "class=crossed-out":""}}>
                        <td>{{$bag->packing_no}}</td>
                        <td>{{collect(json_decode($val->bag_color,true))->implode(",")}}</td>
                        <td>{{$bag->packing_weight}}</td>
                        <td>{{$bag->is_bag_return ?'Yes':"No"}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach