<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 13px;
    color: #1f2937;
    background: #fff;
}

/* ── HEADER ─────────────────────────────────────────── */
.hdr {
    background: #111827;
    padding: 16px 28px;
    width: 100%;
}
.hdr-logo {
    height: 70px;
    max-width: 180px;
    display: block;
}

/* ── INFO ROW ───────────────────────────────────────── */
.info-tbl {
    width: 100%;
    border-collapse: collapse;
    padding: 16px 28px;
    margin: 0;
}
.info-tbl td {
    border: none;
    padding: 4px 8px;
    vertical-align: middle;
}
.col-left {
    width: 33%;
    font-size: 13px;
    font-weight: 700;
    line-height: 2;
    color: #111827;
    vertical-align: top;
    padding-left: 28px;
    padding-top: 14px;
    padding-bottom: 12px;
}
.col-center {
    width: 34%;
    font-size: 19px;
    font-weight: 700;
    color: #111827;
    text-align: center;
    vertical-align: middle;
}
.col-right {
    width: 33%;
    font-size: 13px;
    font-weight: 700;
    line-height: 2;
    color: #111827;
    text-align: right;
    vertical-align: top;
    padding-right: 28px;
    padding-top: 14px;
    padding-bottom: 12px;
}
.info-divider {
    width: 100%;
    border: none;
    border-top: 2px solid #e5e7eb;
    margin: 0;
}

/* ── TEMPLATE CONTENT ──────────────────────────────── */
.content {
    padding: 18px 28px;
    font-size: 13px;
    line-height: 1.8;
    color: #1f2937;
}
.content p   { margin: 0 0 8px; }
.content b, .content strong { font-weight: 700; }
.content table { width: 100%; border-collapse: collapse; margin: 8px 0; }
.content td, .content th { padding: 5px 8px; border: 1px solid #d1d5db; font-size: 12px; }
.content th { background: #374151; color: #fff; font-weight: 700; }

/* ── NAVIGATION ────────────────────────────────────── */
.nav-title {
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 10px 28px 4px;
}
.nav-box {
    margin: 0 28px 12px;
    padding: 10px 14px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    font-size: 12px;
    line-height: 1.6;
    color: #374151;
}

/* ── FOOTER ────────────────────────────────────────── */
.ftr { background: #111827; padding: 8px 28px; margin-top: 20px; width: 100%; }
.ftr-tbl { width: 100%; border-collapse: collapse; }
.ftr-tbl td { border: none; padding: 0; font-size: 11px; color: rgba(255,255,255,0.7); }
</style>
</head>
<body>

{{-- ── HEADER ── --}}
<div class="hdr">
    @if($proof->whitelabel?->logo_link)
        <img src="{{ public_path(ltrim($proof->whitelabel->logo_link, '/')) }}"
             class="hdr-logo" alt="">
    @else
        <span style="color:#fff;font-size:22px;font-weight:700;">{{ $proof->whitelabel?->name ?? $label->name }}</span>
    @endif
</div>

{{-- ── INFO ROW ── --}}
<table class="info-tbl">
    <tr>
        <td class="col-left">
            whitelabel user: {{ $proof->whitelabel?->name ?? '—' }}<br>
            Agent: {{ $proof->agent_name ?? '—' }}<br>
            User: {{ $proof->user_name ?? '—' }}
        </td>
        <td class="col-center">
            Total Amount: {{ $proof->amount !== null ? number_format($proof->amount, 0) : '—' }}
        </td>
        <td class="col-right">
            Sport Name: {{ $proof->sport?->name ?? '—' }}<br>
            Event Name: {{ $proof->event_name ?? '—' }}<br>
            Market Name: {{ $proof->market_name ?? '—' }}
        </td>
    </tr>
</table>
<hr class="info-divider">

{{-- ── TEMPLATE HTML CONTENT ── --}}
@if($templateHtml)
<div class="content">{!! $templateHtml !!}</div>
@endif

{{-- ── NAVIGATION ── --}}
@if($proof->navigation)
<div class="nav-title">Navigation</div>
<div class="nav-box">{{ $proof->navigation }}</div>
@endif
@if($proof->navigation2)
<div class="nav-title">Navigation 2</div>
<div class="nav-box">{{ $proof->navigation2 }}</div>
@endif

{{-- ── FOOTER ── --}}
<div class="ftr">
    <table class="ftr-tbl">
        <tr>
            <td>{{ $proof->whitelabel?->domain ?? $label->name }}@if($whatsappGroup) &bull; {{ $whatsappGroup }}@endif</td>
            <td style="text-align:right;">Generated: {{ now()->format('d M Y, H:i') }}@if($proofMaker) &bull; {{ $proofMaker }}@endif</td>
        </tr>
    </table>
</div>

</body>
</html>
