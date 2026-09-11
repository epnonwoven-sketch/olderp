<td class="flowchart-cell" data-order-id="{{ $order->id }}" style="display: none;">
    <div class="mermaid">
        graph TD;
            Start(Start) --> Decision1[Is Roll Booked?];
            Decision1 -->|No| Exit;
            Decision1 -->|Yes| Decision2[Booking Type];

            Decision2 -->|Full Book| FullBooking;
            Decision2 -->|Part Book| PartBooking;

            FullBooking --> Decision3_Full[Printing Status];
            PartBooking --> Decision3_Part[Printing Status];

            Decision3_Full -->|Full Print| FullPrint;
            Decision3_Full -->|Part Print| PartPrint;
            
            Decision3_Part -->|Full Print| FullPrint;
            Decision3_Part -->|Part Print| PartPrint;

            FullPrint --> Decision4_Full[Cutting Required?];
            PartPrint --> Decision4_Part[Cutting Required?];

            Decision4_Full -->|Full Cut| FullCut;
            Decision4_Full -->|Part Cut| PartCut;

            Decision4_Part -->|Full Cut| FullCut;
            Decision4_Part -->|Part Cut| PartCut;

            FullCut --> DeliveryStatus;
            PartCut --> DeliveryStatus;

            DeliveryStatus[Delivery Status] -->|Out for Delivery| OutForDelivery;
            DeliveryStatus -->|Delivered| Delivered;

            OutForDelivery --> Delivered;
            Delivered --> End;

        %% Define color classes
        classDef completed fill:#28a745,stroke:#0f5132,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef printing fill:#ffc107,stroke:#856404,stroke-width:3px,color:black,font-weight:bold,font-size:16px;
        classDef booked fill:#007bff,stroke:#0056b3,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef rollbooked fill:#6c757d,stroke:#343a40,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef pending fill:#dc3545,stroke:#721c24,stroke-width:3px,color:white,font-weight:bold,font-size:16px;
        classDef delivery fill:#17a2b8,stroke:#0c5460,stroke-width:3px,color:white,font-weight:bold,font-size:16px;

        %% Apply colors dynamically based on status
        @php
            $completedNodes = [];
            $statusClass = '';

            if ($order->isFullDelivered) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', ($order->isFullBook ? 'FullBooking' : 'PartBooking'), 'Decision3_Full', 'FullPrint', 'Decision4_Full', 'FullCut', 'DeliveryStatus', 'OutForDelivery', 'Delivered', 'End'];
                $statusClass = 'completed';
            } elseif ($order->isPartDelivered) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'PartBooking', 'Decision3_Part', 'PartPrint', 'Decision4_Part', 'PartCut', 'DeliveryStatus', 'OutForDelivery', 'Delivered', 'End'];
                $statusClass = 'completed';
            } elseif ($order->isOutForDelivery) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', ($order->isFullBook ? 'FullBooking' : 'PartBooking'), 'Decision3_Full', 'FullPrint', 'Decision4_Full', 'FullCut', 'DeliveryStatus', 'OutForDelivery'];
                $statusClass = 'delivery';
            } elseif ($order->isFullBookFullPrintFullCut) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'FullBooking', 'Decision3_Full', 'FullPrint', 'Decision4_Full', 'FullCut', 'DeliveryStatus'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookFullPrintPartCut) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'FullBooking', 'Decision3_Full', 'FullPrint', 'Decision4_Full', 'PartCut', 'DeliveryStatus'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookPartPrintFullCut) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'FullBooking', 'Decision3_Full', 'PartPrint', 'Decision4_Part', 'FullCut', 'DeliveryStatus'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookPartPrintPartCut) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'FullBooking', 'Decision3_Full', 'PartPrint', 'Decision4_Part', 'PartCut', 'DeliveryStatus'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookFullPrintFullCut) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'PartBooking', 'Decision3_Part', 'FullPrint', 'Decision4_Full', 'FullCut', 'DeliveryStatus'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookFullPrintPartCut) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'PartBooking', 'Decision3_Part', 'FullPrint', 'Decision4_Full', 'PartCut', 'DeliveryStatus'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookPartPrintFullCut) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'PartBooking', 'Decision3_Part', 'PartPrint', 'Decision4_Part', 'FullCut', 'DeliveryStatus'];
                $statusClass = 'completed';
            } elseif ($order->isPartBookPartPrintPartCut) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'PartBooking', 'Decision3_Part', 'PartPrint', 'Decision4_Part', 'PartCut', 'DeliveryStatus'];
                $statusClass = 'completed';
            } elseif ($order->isFullBookFullPrint) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'FullBooking', 'Decision3_Full', 'FullPrint'];
                $statusClass = 'printing';
            } elseif ($order->isPartBookPartPrint) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'PartBooking', 'Decision3_Part', 'PartPrint'];
                $statusClass = 'printing';
            } elseif ($order->isFullBook) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'FullBooking'];
                $statusClass = 'booked';
            } elseif ($order->isPartBook) {
                $completedNodes = ['Start', 'Decision1', 'Decision2', 'PartBooking'];
                $statusClass = 'booked';
            } elseif ($order->isRollBook) {
                $completedNodes = ['Start', 'Decision1'];
                $statusClass = 'rollbooked';
            } else {
                $statusClass = 'pending';
            }

            foreach ($completedNodes as $node) {
                echo "class $node $statusClass;\n";
            }
        @endphp
    </div>
</td>
