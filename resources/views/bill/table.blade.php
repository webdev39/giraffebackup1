<table class="info-table" width="100%">
    <thead>
        <tr>
            <th width="5%">#</th>
            @if($isLong)
            <th width="30%">{table.header.task}</th>
            <th width="30%">{table.header.comment}</th>
            @else
            <th width="60%">{table.header.task}</th>
            @endif
            <th>{table.header.logged}</th>
            <th width="20%">{table.header.rate}</th>
            <th width="20%">{table.header.sum}</th>
        </tr>
    </thead>

    <tbody>
    @foreach ($bill->billTimers as $key => $billTimer)
        @php
            $projectName    = 'N/A';
            $taskName       = 'N/A';

            $timer = optional($billTimer->timerBilling)->timer;
            $task  = optional($timer)->task;

            if ($task) {
                $createAt       = app('BillPdfSer')->getCreatedAt($timer->end_time);

                $projectName    = "{$task->board[0]->name}";
                $taskName       = "{$task->name} ({$createAt})";
            }
        @endphp

        <tr>
            <td align="center"><b>{{ $key + 1 }}</b></td>
            @if(empty($billTimer->title))
                <td>
                    {table.label.project}: {{ $projectName }}<br />
                    {table.label.task}: {{ $taskName }}
                </td>
            @endif

            @if($billTimer->title)
                <td>
                    {{ $billTimer->title  }}
                </td>
            @endif

            @if($isLong)
                <td align="center">{!! nl2br($billTimer->comment) !!}</td>
            @endif

            <td align="center">
                {{ $billTimer->computed_time }} {{ $billTimer->unit }}
            </td>
            <td align="center">
                {{ app('BillPdfSer')->numberToFormat($billTimer->rate) }} {currency}/{{ $billTimer->unit }}
            </td>
            <td align="right">
                {{ app('BillPdfSer')->numberToFormat($billTimer->sum) }} {currency}
            </td>
        </tr>
    @endforeach
    </tbody>

    <tfoot>
        <tr>
            <td width="5%"></td>
            @if($isLong)
                <td width="80%" colspan="4"><b>{table.label.sum}</b></td>
            @else
                <td width="80%" colspan="3"><b>{table.label.sum}</b></td>
            @endif
            <td width="15%" align="right"><b>{{ app('BillPdfSer')->numberToFormat($bill['sum']) }} {currency}</b></td>
        </tr>

        <tr>
            <td width="5%"></td>
            @if($isLong)
                <td width="80%" colspan="4"><b>{table.label.vat}</b></td>
            @else
                <td width="80%" colspan="3"><b>{table.label.vat}</b></td>
            @endif
            <td width="15%" align="right"><b>{{ app('BillPdfSer')->numberToFormat($bill['mwst']) }} {currency}</b></td>
        </tr>

        <tr>
            <td width="5%"></td>
            @if($isLong)
                <td width="80%" colspan="4"><b>{table.label.total}</b></td>
            @else
                <td width="80%" colspan="3"><b>{table.label.total}</b></td>
            @endif
            <td width="15%" align="right"><b>{{ app('BillPdfSer')->numberToFormat($bill['total']) }} {currency}</b></td>
        </tr>
    </tfoot>
</table>
