<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1f2937; background: #fff; }

/* ===== HEADER ===== */
.header-wrap {
    background: #0f766e;
    padding: 20px 24px 16px;
    width: 100%;
}
.header-logo-box {
    float: left;
    background: #fff;
    border-radius: 6px;
    padding: 5px 10px;
    font-size: 11px;
    font-weight: 700;
    color: #0f766e;
    margin-right: 14px;
    margin-top: 2px;
    line-height: 1.2;
}
.header-logo-img {
    height: 44px;
    width: auto;
    max-width: 100px;
    object-fit: contain;
}
.header-info-right {
    float: right;
    text-align: right;
    color: rgba(255,255,255,0.85);
    font-size: 11px;
    line-height: 1.8;
}
.header-title-name {
    font-size: 22px;
    font-weight: 700;
    color: #fff;
    line-height: 1.1;
    display: block;
}
.header-subtitle {
    font-size: 12px;
    color: rgba(255,255,255,0.75);
    display: block;
    margin-top: 3px;
}
.clearfix::after { content: ''; display: table; clear: both; }

/* ===== INFO TABLE ===== */
.info-table {
    width: 100%;
    border-collapse: collapse;
    margin: 0;
}
.info-table td {
    padding: 7px 10px;
    border: 1px solid #e5e7eb;
    font-size: 12px;
    vertical-align: middle;
}
.info-table .lbl {
    width: 18%;
    background: #f9fafb;
    font-weight: 700;
    color: #374151;
    white-space: nowrap;
}
.info-table .val {
    width: 32%;
    color: #111827;
}
.val-green { color: #059669; font-weight: 700; }
.val-red   { color: #dc2626; font-weight: 700; }

/* Badge */
.badge {
    display: inline-block;
    padding: 2px 10px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 700;
}
.badge-pending  { background: #fef3c7; color: #92400e; }
.badge-approved { background: #d1fae5; color: #065f46; }
.badge-rejected { background: #fee2e2; color: #991b1b; }

/* ===== SECTION TITLE ===== */
.section-title {
    font-size: 11px;
    font-weight: 700;
    color: #0f766e;
    text-transform: uppercase;
    letter-spacing: 1px;
    padding: 14px 24px 8px;
}

/* ===== PROOF CONTENT BOX ===== */
.proof-box {
    margin: 0 24px 16px;
    padding: 14px 16px;
    background: #f0fdfa;
    border: 1px solid #99f6e4;
    border-radius: 4px;
    font-size: 12px;
    line-height: 1.75;
    color: #134e4a;
}
.proof-box p  { margin: 0 0 8px; }
.proof-box b, .proof-box strong { font-weight: 700; }
.proof-box table { width: 100%; border-collapse: collapse; margin-top: 6px; }
.proof-box td, .proof-box th { padding: 5px 8px; border: 1px solid #99f6e4; font-size: 11px; }
.proof-box th { background: #0f766e; color: #fff; }

/* ===== NAV SECTION ===== */
.nav-box {
    margin: 0 24px 12px;
    padding: 10px 14px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    font-size: 12px;
    line-height: 1.6;
    color: #374151;
}

/* ===== FOOTER ===== */
.footer-wrap {
    margin-top: 20px;
    background: #f8fafc;
    border-top: 2px solid #e5e7eb;
    padding: 8px 24px;
    width: 100%;
}
.footer-left  { float: left;  font-size: 10px; color: #9ca3af; }
.footer-right { float: right; font-size: 10px; color: #9ca3af; }
</style>
</head>
<body>

{{-- ===== HEADER ===== --}}
<div class="header-wrap clearfix">
    <div class="header-info-right">
        <span>Proof #{{ $proof->id }}</span><br>
        @if($proof->proof_date)<span>{{ $proof->proof_date->format('d M Y') }}</span><br>@endif
        @if($whatsappGroup)<span>{{ $whatsappGroup }}</span>@endif
    </div>

    @if($proof->whitelabel?->logo_link)
        <img src="{{ public_path('storage/' . ltrim($proof->whitelabel->logo_link, '/')) }}"
             class="header-logo-img" style="float:left;margin-right:12px;border-radius:4px;background:#fff;padding:3px;" alt="Logo">
    @else
        <div class="header-logo-box" style="float:left;">Logo</div>
    @endif

    <div style="overflow:hidden;">
        <span class="header-title-name">{{ $proof->whitelabel?->name ?? $label->name }}</span>
        <span class="header-subtitle">{{ $proof->proofType?->name ?? 'Proof Document' }}</span>
    </div>
</div>

{{-- ===== INFO TABLE ===== --}}
<table class="info-table">
    <tr>
        <td class="lbl">Whitelabel</td>
        <td class="val">{{ $proof->whitelabel?->name ?? '—' }}</td>
        <td class="lbl">WhatsApp Group</td>
        <td class="val">{{ $whatsappGroup ?: '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Agent Name</td>
        <td class="val">{{ $proof->agent_name ?? '—' }}</td>
        <td class="lbl">User</td>
        <td class="val">{{ $proof->user_name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Sport</td>
        <td class="val">{{ $proof->sport?->name ?? '—' }}</td>
        <td class="lbl">Proof Type</td>
        <td class="val">{{ $proof->proofType?->name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Event Name</td>
        <td class="val">{{ $proof->event_name ?? '—' }}</td>
        <td class="lbl">Market Name</td>
        <td class="val">{{ $proof->market_name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Amount</td>
        <td class="val">{{ $proof->amount !== null ? number_format($proof->amount, 0) : '—' }}</td>
        <td class="lbl">Profit / Loss</td>
        <td class="val @if(($proof->profit_loss ?? 0) > 0) val-green @elseif(($proof->profit_loss ?? 0) < 0) val-red @endif">
            {{ $proof->profit_loss !== null ? number_format($proof->profit_loss, 0) : '—' }}
        </td>
    </tr>
    <tr>
        <td class="lbl">Proof Maker</td>
        <td class="val">{{ $proofMaker ?: '—' }}</td>
        <td class="lbl">Status</td>
        <td class="val">
            <span class="badge badge-{{ $proof->status ?? 'pending' }}">{{ ucfirst($proof->status ?? 'pending') }}</span>
        </td>
    </tr>
</table>

{{-- ===== PROOF CONTENT ===== --}}
@if($templateHtml)
<div class="section-title">Proof Content</div>
<div class="proof-box">{!! $templateHtml !!}</div>
@endif

{{-- ===== NAVIGATION ===== --}}
@if($proof->navigation)
<div class="section-title" style="padding-top:8px;">Navigation</div>
<div class="nav-box">{{ $proof->navigation }}</div>
@endif

@if($proof->navigation2)
<div class="section-title" style="padding-top:8px;">Navigation 2</div>
<div class="nav-box">{{ $proof->navigation2 }}</div>
@endif

{{-- ===== FOOTER ===== --}}
<div class="footer-wrap clearfix">
    <span class="footer-left">{{ $proof->whitelabel?->domain ?? $label->name }} &mdash; Proof #{{ $proof->id }}</span>
    <span class="footer-right">
        Generated: {{ now()->format('d M Y, H:i') }}
        @if($proofMaker) &nbsp;&bull;&nbsp; By: {{ $proofMaker }}@endif
    </span>
</div>

</body>
</html>
