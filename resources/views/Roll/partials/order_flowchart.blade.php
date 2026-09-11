<div style="overflow-x: auto; max-width: 100%;">
    <div class="mermaid flowchart-cell" data-order-id="{{ $order->id }}">
        @php            
            $completedNodes = [];
            $statusClass = 'pending';

            if ($order->isFullDelivered) {
                $completedNodes = [($order->isFullBook ? 'FullBooking' : 'PartBooking'), 'Printed', 'Cut', 'OutForDelivery', 'Delivered'];
                $statusClass = 'completed';
            } elseif ($order->isPartDelivered) {
                $completedNodes = [($order->isFullBook ? 'FullBooking' : 'PartBooking'), 'Printed', 'Cut', 'OutForDelivery', 'Delivered'];
                $statusClass = 'completed';
            } elseif ($order->isOutForDelivery) {
                $completedNodes = [($order->isFullBook ? 'FullBooking' : 'PartBooking'), 'Printed', 'Cut', 'OutForDelivery'];
                $statusClass = 'delivery';
            } elseif ($order->isFullBookFullPrintFullCut) {
                $completedNodes = ['FullBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookFullPrintPartCut) {
                $completedNodes = ['FullBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookPartPrintFullCut) {
                $completedNodes = ['FullBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookPartPrintPartCut) {
                $completedNodes = ['FullBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookFullPrintFullCut) {
                $completedNodes = ['PartBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookFullPrintPartCut) {
                $completedNodes = ['PartBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookPartPrintFullCut) {
                $completedNodes = ['PartBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookPartPrintPartCut) {
                $completedNodes = ['PartBooking', 'Printed', 'Cut'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookFullPrint || $order->isPartBookFullPrint) {
                $completedNodes = ['FullBooking', 'Printed'];
                $statusClass = 'printing';
            } elseif ($order->isPartBookPartPrint) {
                $completedNodes = ['PartBooking', 'Printed'];
                $statusClass = 'printing';
            } elseif ($order->isFullBook) {
                $completedNodes = ['FullBooking'];
                $statusClass = 'booked';
            } elseif ($order->isPartBook) {
                $completedNodes = ['PartBooking'];
                $statusClass = 'booked';
            } elseif ($order->isRollBook) {
                $completedNodes = ['PartBooking'];
                $statusClass = 'rollbooked';
            } else{
                $completedNodes = ['Pending'];
                $statusClass = 'pending';
            }
            
        @endphp
        
        graph LR;
            @php
                if($order->is_draft){
                    echo "Draft".(isset($completedNodes[0])?" -->".$completedNodes[0]:"").";\n";
                }
                foreach ($completedNodes as $index => $node) {
                    if (isset($completedNodes[$index + 1])) {
                        echo "$node --> {$completedNodes[$index + 1]};\n";
                    }else{
                        echo "$node;\n";
                    }
                }
            @endphp

        %% Define color classes
        classDef completed fill:#28a745,stroke:#0f5132,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef printing fill:#ffc107,stroke:#856404,stroke-width:3px,color:black,font-weight:bold,font-size:16px;
        classDef booked fill:#007bff,stroke:#0056b3,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef rollbooked fill:#6c757d,stroke:#343a40,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef pending fill:#dc3545,stroke:#721c24,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef delivery fill:#17a2b8,stroke:#0c5460,stroke-width:3px,color:white,font-weight:bold,font-size:16px;

        %% Apply colors dynamically based on order status
        @php
            if($order->is_draft){
                echo "class Draft $statusClass;\n";
            }
            foreach ($completedNodes as $node) {
                echo "class $node $statusClass;\n";
            }
        @endphp
    </div>
</div>
