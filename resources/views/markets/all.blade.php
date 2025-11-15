@php
    echo view('markets.index', array_merge(
        \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']),
        [
            'pageTitle' => $pageTitle ?? 'All Markets List',
            'pageHeading' => $pageHeading ?? 'All Markets List',
            'pageSubheading' => $pageSubheading ?? 'Browse every market without date limits',
        ]
    ))->render();
@endphp
