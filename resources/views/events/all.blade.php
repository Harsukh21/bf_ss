@php
    echo view('events.index', array_merge(
        \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']),
        [
            'pageTitle' => $pageTitle ?? 'All Events List',
            'pageHeading' => $pageHeading ?? 'All Events List',
            'pageSubheading' => $pageSubheading ?? 'Browse every scheduled event without date limits',
        ]
    ))->render();
@endphp
