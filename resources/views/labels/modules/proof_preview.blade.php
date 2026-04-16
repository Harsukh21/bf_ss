<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Proof Preview</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; font-size: 13px; color: #1f2937; background: #fff; }

    /* Header */
    .header {
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: {{ $proof->whitelabel?->color ?? '#0f766e' }};
    }
    .header-left  { display: flex; align-items: center; gap: 14px; }
    .header-logo  { height: 48px; width: auto; max-width: 110px; object-fit: contain; background: #fff; border-radius: 6px; padding: 4px 8px; }
    .header-logo-ph { height: 48px; width: 48px; background: rgba(255,255,255,.2); border-radius: 6px; display: flex; align-items: center; justify-content: center; }
    .header-name  { font-size: 20px; font-weight: 700; color: #fff; line-height: 1.1; }
    .header-sub   { font-size: 12px; color: rgba(255,255,255,.75); margin-top: 3px; }
    .header-right { text-align: right; color: rgba(255,255,255,.85); font-size: 12px; line-height: 1.8; }

    /* Info grid */
    .info-table { width: 100%; border-collapse: collapse; }
    .info-table td { padding: 8px 12px; border: 1px solid #e5e7eb; font-size: 12.5px; vertical-align: middle; }
    .lbl { width: 16%; background: #f9fafb; font-weight: 600; color: #374151; white-space: nowrap; }
    .val { width: 34%; color: #111827; }
    .green { color: #059669; font-weight: 700; }
    .red   { color: #dc2626; font-weight: 700; }

    .badge { display: inline-block; padding: 2px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; }
    .badge-pending  { background: #fef3c7; color: #92400e; }
    .badge-approved { background: #d1fae5; color: #065f46; }
    .badge-rejected { background: #fee2e2; color: #991b1b; }

    /* Section */
    .section-title {
        font-size: 11px; font-weight: 700; color: #0f766e;
        text-transform: uppercase; letter-spacing: 1px;
        padding: 16px 24px 8px;
    }
    .proof-box {
        margin: 0 24px 16px;
        padding: 14px 18px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 13px;
        line-height: 1.75;
        color: #1f2937;
        background: #fff;
    }
    .proof-box p { margin: 0 0 8px; }
    .proof-box b, .proof-box strong { font-weight: 700; }
    .proof-box table { width: 100%; border-collapse: collapse; }
    .proof-box td, .proof-box th { padding: 5px 8px; border: 1px solid #d1d5db; }
    .proof-box th { background: #0f766e; color: #fff; }

    .nav-box {
        margin: 0 24px 14px;
        padding: 10px 14px;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        font-size: 13px;
        line-height: 1.6;
        color: #374151;
        white-space: pre-wrap;
    }
    .footer {
        display: flex; justify-content: space-between;
        padding: 10px 24px;
        background: #f8fafc;
        border-top: 2px solid #e5e7eb;
        font-size: 11px;
        color: #9ca3af;
        margin-top: 20px;
    }
</style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div class="header-left">
        @if($proof->whitelabel?->logo_link)
            <img src="{{ $proof->whitelabel->logo_link }}" class="header-logo" alt="{{ $proof->whitelabel->name }}">
        @else
            <div class="header-logo-ph">
                <svg width="26" height="26" fill="none" stroke="rgba(255,255,255,.7)" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        @endif
        <div>
            <div class="header-name">{{ $proof->whitelabel?->name ?? $label->name }}</div>
            <div class="header-sub">{{ $proof->proofType?->name ?? 'Proof Document' }}</div>
        </div>
    </div>
    <div class="header-right">
        <div>Proof #{{ $proof->id }}</div>
        @if($proof->proof_date)<div>{{ $proof->proof_date->format('d M Y') }}</div>@endif
        @if($whatsappGroup)<div>{{ $whatsappGroup }}</div>@endif
    </div>
</div>

{{-- Info table --}}
<table class="info-table">
    <tr>
        <td class="lbl">Whitelabel</td><td class="val">{{ $proof->whitelabel?->name ?? '—' }}</td>
        <td class="lbl">WhatsApp Group</td><td class="val">{{ $whatsappGroup ?: '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Agent Name</td><td class="val">{{ $proof->agent_name ?? '—' }}</td>
        <td class="lbl">User</td><td class="val">{{ $proof->user_name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Sport</td><td class="val">{{ $proof->sport?->name ?? '—' }}</td>
        <td class="lbl">Proof Type</td><td class="val">{{ $proof->proofType?->name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Event Name</td><td class="val">{{ $proof->event_name ?? '—' }}</td>
        <td class="lbl">Market Name</td><td class="val">{{ $proof->market_name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Amount</td><td class="val">{{ $proof->amount !== null ? number_format($proof->amount, 0) : '—' }}</td>
        <td class="lbl">Profit / Loss</td>
        <td class="val @if(($proof->profit_loss??0)>0) green @elseif(($proof->profit_loss??0)<0) red @endif">
            {{ $proof->profit_loss !== null ? number_format($proof->profit_loss, 0) : '—' }}
        </td>
    </tr>
    <tr>
        <td class="lbl">Proof Maker</td><td class="val">{{ $proofMaker ?: '—' }}</td>
        <td class="lbl">Status</td>
        <td class="val"><span class="badge badge-{{ $proof->status ?? 'pending' }}">{{ ucfirst($proof->status ?? 'pending') }}</span></td>
    </tr>
</table>

{{-- Proof Content --}}
@if($templateHtml)
<div class="section-title">Proof Content</div>
<div class="proof-box">{!! $templateHtml !!}</div>
@endif

{{-- Navigation --}}
@if($proof->navigation)
<div class="section-title">Navigation</div>
<div class="nav-box">{{ $proof->navigation }}</div>
@endif
@if($proof->navigation2)
<div class="section-title">Navigation 2</div>
<div class="nav-box">{{ $proof->navigation2 }}</div>
@endif

{{-- Footer --}}
<div class="footer">
    <span>{{ $proof->whitelabel?->domain ?? $label->name }} — Proof #{{ $proof->id }}</span>
    <span>{{ now()->format('d M Y') }}@if($proofMaker) &bull; {{ $proofMaker }}@endif</span>
</div>

</body>
</html>
