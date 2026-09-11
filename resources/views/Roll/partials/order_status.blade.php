

    @if($order->isFullDelivered)
        <span class="badge bg-success">Completed ({{$order->isPartBook?'Part':'Full'}} - Full Delivered)</span>
    @elseif($order->isPartDelivered)
        <span class="badge bg-success">Completed ({{$order->isPartBook?'Part':'Full'}} - Full Delivered)</span>
    @elseif($order->isFullBookFullPrintFullCut)
        <span class="badge bg-success">Completed (Full - Full Cut)</span>
    @elseif($order->isFullBookFullPrintPartCut)
        <span class="badge bg-success">Completed (Full - Part Cut)</span>
    @elseif($order->isFullBookPartPrintFullCut)
        <span class="badge bg-success">Completed (Full - Part Print, Full Cut)</span>
    @elseif($order->isFullBookPartPrintPartCut)
        <span class="badge bg-success">Completed (Full - Part Print, Part Cut)</span>
    @elseif($order->isPartBookFullPrintFullCut)
        <span class="badge bg-success">Completed (Part - Full Print, Full Cut)</span>
    @elseif($order->isPartBookFullPrintPartCut)
        <span class="badge bg-success">Completed (Part - Full Print, Part Cut)</span>
    @elseif($order->isPartBookPartPrintFullCut)
        <span class="badge bg-success">Completed (Part - Part Print, Full Cut)</span>
    @elseif($order->isPartBookPartPrintPartCut)
        <span class="badge bg-success">Completed (Part - Part Print, Part Cut)</span>
    @elseif($order->isFullBookFullPrint)
        <span class="badge bg-warning text-dark">Printing (Full - Full Print)</span>
    @elseif($order->isFullBookPartPrint)
        <span class="badge bg-warning text-dark">Printing (Full - Part Print)</span>
    @elseif($order->isPartBookFullPrint)
        <span class="badge bg-warning text-dark">Printing (Part - Full Print)</span>
    @elseif($order->isPartBookPartPrint)
        <span class="badge bg-warning text-dark">Printing (Part - Part Print)</span>
    @elseif($order->isFullBook)
        <span class="badge bg-primary">Full Booked</span>
    @elseif($order->isPartBook)
        <span class="badge bg-primary">Part Booked</span>
    @elseif($order->isRollBook)
        <span class="badge bg-secondary">Roll Booked</span>
    @else
        <span class="badge bg-danger">Pending</span>
    @endif
