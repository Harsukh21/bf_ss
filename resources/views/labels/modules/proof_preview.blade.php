<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Proof Preview</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
    font-size: 13px;
    color: #1f2937;
    background: #fff;
}

/* ── HEADER ─────────────────────────────────────────── */
.hdr {
    background: #111827;
    padding: 16px 28px;
    display: flex;
    align-items: center;
}
.hdr-logo {
    height: 70px;
    max-width: 180px;
    object-fit: contain;
    display: block;
}
.hdr-name {
    font-size: 22px;
    font-weight: 700;
    color: #fff;
}

/* ── INFO ROW (3 columns) ───────────────────────────── */
.info-wrap {
    padding: 16px 28px;
    border-bottom: 2px solid #e5e7eb;
}
.info-row {
    display: flex;
    align-items: center;
    gap: 16px;
}
.info-col        { flex: 1; font-size: 13px; font-weight: 700; line-height: 2; color: #111827; }
.info-col.center { flex: 1; text-align: center; font-size: 19px; font-weight: 700; color: #111827; }
.info-col.right  { flex: 1; text-align: right; }

/* ── TEMPLATE HTML CONTENT ──────────────────────────── */
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

/* ── NAVIGATION ─────────────────────────────────────── */
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
    white-space: pre-wrap;
}

/* ── FOOTER ─────────────────────────────────────────── */
.ftr {
    background: #111827;
    padding: 8px 28px;
    margin-top: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.ftr span { font-size: 11px; color: rgba(255,255,255,0.7); }
</style>
</head>
<body>

{{-- ── HEADER: dark bg + logo only ── --}}
<div class="hdr">
    @if($proof->whitelabel?->logo_link)
        <img src="{{ $proof->whitelabel->logo_link }}" class="hdr-logo" alt="">
    @else
        <span class="hdr-name">{{ $proof->whitelabel?->name ?? $label->name }}</span>
    @endif
</div>

{{-- ── INFO ROW: 3 columns ── --}}
<div class="info-wrap">
    <div class="info-row">
        <div class="info-col">
            whitelabel user: {{ $proof->whitelabel?->name ?? '—' }}<br>
            Agent: {{ $proof->agent_name ?? '—' }}<br>
            User: {{ $proof->user_name ?? '—' }}
        </div>
        <div class="info-col center">
            Total Amount: {{ $proof->amount !== null ? number_format($proof->amount, 0) : '—' }}
        </div>
        <div class="info-col right">
            Sport Name: {{ $proof->sport?->name ?? '—' }}<br>
            Event Name: {{ $proof->event_name ?? '—' }}<br>
            Market Name: {{ $proof->market_name ?? '—' }}
        </div>
    </div>
</div>

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

{{-- ── FOOTER: dark bg ── --}}
<div class="ftr">
    <span>{{ $proof->whitelabel?->domain ?? $label->name }}@if($whatsappGroup) &bull; {{ $whatsappGroup }}@endif</span>
    <span>{{ now()->format('d M Y') }}@if($proofMaker) &bull; {{ $proofMaker }}@endif</span>
</div>

</body>
</html>
