<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Report - {{ $activity->title }}</title>
    <style>
        /* Set A4 page size and margins */
        @page {
            size: A4 portrait;
            margin: 20mm 15mm 20mm 15mm;
        }

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11pt;
            line-height: 1.4;
            color: #000;
        }

        /* Header */
        .header {
            border-bottom: 2px solid #1a3a5f;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .header-title {
            font-size: 18pt;
            font-weight: bold;
            color: #1a3a5f;
            margin-bottom: 10px;
            text-align: center;
        }

        .header-info {
            font-size: 10pt;
            color: #333;
        }

        .header-info table {
            width: 100%;
            margin-top: 10px;
        }

        .header-info td {
            padding: 3px 0;
            vertical-align: top;
        }

        .header-info .label {
            font-weight: bold;
            width: 100px;
        }

        /* Table */
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .report-table thead {
            background-color: #1a3a5f;
            color: #fff;
        }

        .report-table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            font-size: 10pt;
            border: 1px solid #1a3a5f;
        }

        .report-table td {
            padding: 10px 8px;
            border: 1px solid #333;
            vertical-align: middle;
            font-size: 10pt;
        }

        .report-table tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        /* Photo styling */
        .photo-cell {
            width: 60px;
            text-align: center;
        }

        .photo-cell img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border: 1px solid #ccc;
        }

        .photo-placeholder {
            width: 50px;
            height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #e5e7eb;
            color: #9ca3af;
            font-size: 8pt;
            border: 1px solid #ccc;
        }

        /* Footer */
        .footer {
            position: fixed;
            bottom: 20mm;
            left: 15mm;
            right: 15mm;
            border-top: 1px solid #ccc;
            padding-top: 10px;
            font-size: 9pt;
            color: #666;
            text-align: center;
        }

        .footer .page-number:before {
            content: counter(page);
        }

        /* Summary stats */
        .summary {
            background-color: #f0f4f8;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 3px;
        }

        .summary table {
            width: 100%;
        }

        .summary td {
            padding: 5px;
        }

        .summary-count {
            font-size: 14pt;
            font-weight: bold;
            color: #1a3a5f;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-title">ACTIVITY REPORT</div>
        <div class="header-info">
            <table>
                <tr>
                    <td class="label">Activity:</td>
                    <td>{{ $activity->title }}</td>
                </tr>
                <tr>
                    <td class="label">Location:</td>
                    <td>{{ $activity->location }}</td>
                </tr>
                <tr>
                    <td class="label">Date:</td>
                    <td>{{ $activity->activity_date->format('d F Y') }}</td>
                </tr>
                @if($activity->description)
                <tr>
                    <td class="label">Description:</td>
                    <td>{{ $activity->description }}</td>
                </tr>
                @endif
                <tr>
                    <td class="label">Generated:</td>
                    <td>{{ now()->format('d F Y, H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Summary -->
    <div class="summary">
        <table>
            <tr>
                <td>Total Respondents:</td>
                <td class="summary-count" style="text-align: right;">{{ $reports->count() }}</td>
            </tr>
        </table>
    </div>

    <!-- Reports Table -->
    <table class="report-table">
        <thead>
            <tr>
                <th style="width: 50px; text-align: center;">No.</th>
                <th>Respondent Name</th>
                <th style="width: 70px; text-align: center;">Photo</th>
                <th style="width: 120px; text-align: center;">Submitted At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $index => $report)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    <td>{{ $report->respondent_name }}</td>
                    <td class="photo-cell" style="text-align: center;">
                        @if($report->photo_path && Storage::disk('public')->exists($report->photo_path))
                            <img src="{{ asset('storage/' . $report->photo_path) }}" alt="Photo">
                        @else
                            <div class="photo-placeholder">No Photo</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $report->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($reports->count() == 0)
        <p style="text-align: center; color: #666; font-style: italic; padding: 40px 0;">
            No reports have been submitted yet.
        </p>
    @endif

    <!-- Footer -->
    <div class="footer">
        Page {PAGE_NUM} of {PAGE_COUNT} | Generated by BPS Office Report System | {{ config('app.name', 'Laravel') }}
    </div>
</body>
</html>