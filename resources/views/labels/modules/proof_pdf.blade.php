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
    padding: 18px 20px;
    width: 100%;
}
.header-table { width: 100%; border-collapse: collapse; }
.header-table td { vertical-align: middle; padding: 0; border: none; background: transparent; }
.header-logo-img {
    height: 48px;
    width: auto;
    max-width: 100px;
    object-fit: contain;
    border-radius: 4px;
    background: #fff;
    padding: 3px 6px;
    display: block;
}
.header-logo-box {
    background: #fff;
    border-radius: 4px;
    padding: 4px 10px;
    font-size: 10px;
    font-weight: 700;
    color: #0f766e;
    display: inline-block;
}
.header-name {
    font-size: 20px;
    font-weight: 700;
    color: #fff;
    line-height: 1.1;
}
.header-sub {
    font-size: 11px;
    color: rgba(255,255,255,0.75);
    margin-top: 3px;
}
.header-right-cell {
    text-align: right;
    color: rgba(255,255,255,0.85);
    font-size: 11px;
    line-height: 1.9;
    white-space: nowrap;
    width: 130px;
    vertical-align: top;
}

/* ===== INFO TABLE ===== */
.info-table { width: 100%; border-collapse: collapse; }
.info-table td {
    padding: 7px 10px;
    border: 1px solid #e5e7eb;
    font-size: 12px;
    vertical-align: middle;
}
.info-table .lbl {
    width: 17%;
    background: #f9fafb;
    font-weight: 700;
    color: #374151;
    white-space: nowrap;
}
.info-table .val { width: 33%; color: #111827; }
.val-green { color: #059669; font-weight: 700; }
.val-red   { color: #dc2626; font-weight: 700; }

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
    padding: 14px 20px 8px;
}

/* ===== PROOF CONTENT — inherit proof type HTML styles ===== */
.proof-content-wrap {
    margin: 0 20px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    padding: 14px 16px;
    font-size: 12px;
    line-height: 1.75;
    color: #1f2937;
    background: #fff;
}

/* Allow proof type's own table/box styles to render */
.proof-content-wrap table { width: 100%; border-collapse: collapse; }
.proof-content-wrap td, .proof-content-wrap th { padding: 5px 8px; border: 1px solid #d1d5db; font-size: 11px; }
.proof-content-wrap th { background: #0f766e; color: #fff; }
.proof-content-wrap p  { margin: 0 0 8px; }
.proof-content-wrap b, .proof-content-wrap strong { font-weight: 700; }

/* ===== NAV BOX ===== */
.nav-box {
    margin: 0 20px 12px;
    padding: 10px 14px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    font-size: 12px;
    line-height: 1.6;
    color: #374151;
}

/* ===== FOOTER ===== */
.footer-table { width: 100%; border-collapse: collapse; margin-top: 18px; background: #f8fafc; border-top: 2px solid #e5e7eb; }
.footer-table td { padding: 8px 20px; border: none; font-size: 10px; color: #9ca3af; }
</style>
</head>
<body>

{{-- ===== HEADER ===== --}}
<div class="header-wrap">
    <table class="header-table">
        <tr>
            <td style="width:60px;padding-right:12px;">
                @if($proof->whitelabel?->logo_link)
                    <img src="{{ public_path('storage/' . ltrim($proof->whitelabel->logo_link, '/')) }}"
                         class="header-logo-img" alt="Logo">
                @else
                    <span class="header-logo-box">Logo</span>
                @endif
            </td>
            <td>
                <div class="header-name">{{ $proof->whitelabel?->name ?? $label->name }}</div>
                <div class="header-sub">{{ $proof->proofType?->name ?? 'Proof Document' }}</div>
            </td>
            <td class="header-right-cell">
                <div>Proof #{{ $proof->id }}</div>
                @if($proof->proof_date)<div>{{ $proof->proof_date->format('d M Y') }}</div>@endif
                @if($whatsappGroup)<div>{{ $whatsappGroup }}</div>@endif
            </td>
        </tr>
    </table>
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

{{-- ===== PROOF TYPE HTML CONTENT (rendered as-is) ===== --}}
@if($templateHtml)
<div class="section-title">Proof Content</div>
<div class="proof-content-wrap">{!! $templateHtml !!}</div>
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
<table class="footer-table">
    <tr>
        <td>{{ $proof->whitelabel?->domain ?? $label->name }} — Proof #{{ $proof->id }}</td>
        <td style="text-align:right;">
            Generated: {{ now()->format('d M Y, H:i') }}
            @if($proofMaker) &bull; By: {{ $proofMaker }}@endif
        </td>
    </tr>
</table>

</body>
</html>
