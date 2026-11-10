<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Prüfprotokoll {{ $equipment->name }} – {{ $protocol->checked_at->format('d.m.Y') }}</title>
    <style>
        /* ── Reset & Basis ─────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            background: white;
        }

        /* ── A4-Seite am Bildschirm ────────────────────────────────────── */
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 12mm 14mm 14mm;
            background: white;
        }

        /* ── Druckknopf (nur am Bildschirm) ────────────────────────────── */
        .screen-actions {
            width: 210mm;
            margin: 6mm auto 0;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
        }
        .btn {
            padding: 6px 16px;
            border-radius: 6px;
            font-size: 9pt;
            cursor: pointer;
            border: 1px solid #d1d5db;
            background: white;
            color: #374151;
            text-decoration: none;
        }
        .btn-primary {
            background: #c8010e;
            color: white;
            border-color: #c8010e;
        }

        /* ── Kopfzeile ─────────────────────────────────────────────────── */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: stretch;
            border: 2px solid #1a1a1a;
            margin-bottom: 5mm;
        }
        .header-org {
            padding: 4mm 5mm;
            border-right: 2px solid #1a1a1a;
            font-size: 9pt;
            font-weight: bold;
            min-width: 38mm;
            display: flex;
            align-items: center;
        }
        .header-title {
            flex: 1;
            padding: 3mm 5mm;
            text-align: center;
        }
        .header-title h1 {
            font-size: 14pt;
            font-weight: bold;
            line-height: 1.2;
        }
        .header-title p {
            font-size: 10pt;
            margin-top: 1mm;
        }
        .header-badge {
            background: #c8010e;
            color: white;
            padding: 3mm 5mm;
            border-left: 2px solid #1a1a1a;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            min-width: 32mm;
        }

        /* ── Prüfdaten-Block ───────────────────────────────────────────── */
        .meta-row {
            display: flex;
            gap: 4mm;
            margin-bottom: 4mm;
        }
        .meta-box {
            flex: 1;
            border: 1px solid #9ca3af;
            border-radius: 3px;
            padding: 2mm 3mm;
        }
        .meta-box label {
            display: block;
            font-size: 7pt;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5mm;
        }
        .meta-box span {
            font-size: 10pt;
            font-weight: bold;
        }
        .status-ok    { background: #f0fdf4; border-color: #86efac; }
        .status-issue { background: #fef2f2; border-color: #fca5a5; }

        /* ── Allgemeine Notizen ─────────────────────────────────────────── */
        .notes-block {
            border: 1px solid #e5e7eb;
            border-radius: 3px;
            padding: 2mm 3mm;
            margin-bottom: 4mm;
            font-size: 9pt;
            color: #374151;
        }
        .notes-block strong { font-size: 7pt; color: #6b7280; text-transform: uppercase; display: block; margin-bottom: 1mm; }

        /* ── Kategorie-Tabellen ─────────────────────────────────────────── */
        .category-block {
            margin-bottom: 3.5mm;
            break-inside: avoid;
        }
        .category-header {
            color: white;
            font-size: 9pt;
            font-weight: bold;
            padding: 1.5mm 3mm;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .category-header span { font-size: 7.5pt; font-weight: normal; opacity: 0.85; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        th {
            background: #f3f4f6;
            text-align: left;
            padding: 1.5mm 2.5mm;
            font-size: 7.5pt;
            color: #6b7280;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
        }
        td {
            padding: 1.5mm 2.5mm;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: none; }
        tr.row-issue { background: #fff7f7; }

        .check-cell { text-align: center; width: 10mm; font-size: 11pt; }
        .qty-cell   { text-align: center; width: 18mm; }
        .soll-cell  { text-align: center; width: 14mm; color: #9ca3af; }
        .note-cell  { color: #6b7280; font-style: italic; }

        /* ── Unterschrift ───────────────────────────────────────────────── */
        .signature-section {
            margin-top: 5mm;
            border: 1px solid #e5e7eb;
            border-radius: 3px;
            overflow: hidden;
            break-inside: avoid;
        }
        .signature-header {
            background: #f3f4f6;
            padding: 1.5mm 3mm;
            font-size: 7.5pt;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
        }
        .signature-body {
            padding: 3mm;
            display: flex;
            align-items: flex-end;
            gap: 6mm;
        }
        .signature-img { max-height: 22mm; border: 1px solid #e5e7eb; border-radius: 2px; }
        .signature-line {
            flex: 1;
            border-top: 1px solid #374151;
            padding-top: 1.5mm;
            font-size: 8pt;
            color: #374151;
        }
        .signature-line span { font-size: 7pt; color: #9ca3af; display: block; margin-bottom: 3mm; }

        /* ── Fußzeile ───────────────────────────────────────────────────── */
        .footer {
            margin-top: 6mm;
            padding-top: 2mm;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            font-size: 7pt;
            color: #9ca3af;
        }

        /* ── Druckregeln ────────────────────────────────────────────────── */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 12mm;
            }
            body { background: white; }
            .screen-actions { display: none !important; }
            .page { width: 100%; margin: 0; padding: 0; min-height: unset; }
            .category-block { break-inside: avoid; }
            .signature-section { break-inside: avoid; }
        }
    </style>
</head>
<body>

{{-- Druckknopf (nur am Bildschirm sichtbar) --}}
<div class="screen-actions">
    <a href="{{ route('vehicles.equipment.protocols.show', [$vehicle, $equipment, $protocol]) }}" class="btn">← Zurück</a>
    <button class="btn btn-primary" onclick="window.print()">Drucken / Als PDF speichern</button>
</div>

<div class="page">

    {{-- ── Kopfzeile ─────────────────────────────────────────────────────── --}}
    <div class="header">
        <div class="header-org">ASB<br>Region Düsseldorf</div>
        <div class="header-title">
            <h1>Prüfprotokoll</h1>
            <p>{{ $equipment->name }} &middot; {{ $vehicle->name }}</p>
        </div>
        <div class="header-badge">
            Katastrophenschutz<br>Sanitätsdienst
        </div>
    </div>

    {{-- ── Prüfdaten ───────────────────────────────────────────────────────── --}}
    @php $issueCount = $protocol->items->where('is_ok', false)->count(); @endphp
    <div class="meta-row">
        <div class="meta-box">
            <label>Datum des Checks</label>
            <span>{{ $protocol->checked_at->format('d.m.Y') }}</span>
        </div>
        <div class="meta-box">
            <label>Geprüft durch</label>
            <span>{{ $protocol->checked_by }}</span>
        </div>
        <div class="meta-box">
            <label>Positionen geprüft</label>
            <span>{{ $protocol->items->count() }}</span>
        </div>
        <div class="meta-box {{ $issueCount > 0 ? 'status-issue' : 'status-ok' }}">
            <label>Ergebnis</label>
            <span>{{ $issueCount > 0 ? $issueCount . ' Mangel/Mängel' : 'Alles i.O.' }}</span>
        </div>
    </div>

    @if($protocol->notes)
    <div class="notes-block">
        <strong>Allgemeine Notizen</strong>
        {{ $protocol->notes }}
    </div>
    @endif

    {{-- ── Materialien nach Kategorie ─────────────────────────────────────── --}}
    @foreach($items as $category => $catItems)
    @php $catColor = $catItems->first()->material->category->color ?? '#6B7280'; @endphp
    <div class="category-block">
        <div class="category-header" style="background-color: {{ $catColor }}">
            {{ $category }}
            <span>{{ $catItems->count() }} Position(en)</span>
        </div>
        <table>
            <thead>
                <tr>
                    <th class="check-cell">i.O.</th>
                    <th>Material</th>
                    <th class="qty-cell">Ist</th>
                    <th class="soll-cell">Soll</th>
                    <th class="note-cell">Notizen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($catItems as $item)
                <tr class="{{ !$item->is_ok ? 'row-issue' : '' }}">
                    <td class="check-cell">{{ $item->is_ok ? '✓' : '✗' }}</td>
                    <td>{{ $item->material->name }}</td>
                    <td class="qty-cell">
                        @if($item->current_quantity !== null)
                            {{ $item->current_quantity }} {{ $item->material->unit }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="soll-cell">{{ $item->material->unit ? $item->material->unit : '' }}</td>
                    <td class="note-cell">{{ $item->notes ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    {{-- ── Unterschrift ────────────────────────────────────────────────────── --}}
    <div class="signature-section">
        <div class="signature-header">Unterschrift des Prüfers</div>
        <div class="signature-body">
            @if($protocol->signature_data)
                <img src="{{ $protocol->signature_data }}" alt="Unterschrift" class="signature-img">
            @else
                <div style="width:60mm; height:18mm; border:1px dashed #d1d5db; border-radius:2px;"></div>
            @endif
            <div class="signature-line">
                <span>Datum &amp; Unterschrift</span>
                {{ $protocol->checked_by }} &middot; {{ $protocol->checked_at->format('d.m.Y') }}
            </div>
        </div>
    </div>

    {{-- ── Fußzeile ─────────────────────────────────────────────────────────── --}}
    <div class="footer">
        <span>ASB Region Düsseldorf &middot; Katastrophenschutz / Sanitätsdienst</span>
        <span>Erstellt: {{ $protocol->created_at->format('d.m.Y H:i') }} Uhr</span>
    </div>

</div>
</body>
</html>
