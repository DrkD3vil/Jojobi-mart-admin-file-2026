<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1a1a1a; }
        h1 { font-size: 18px; margin-bottom: 2px; }
        .meta { color: #666; font-size: 10px; margin-bottom: 18px; }
        h2 { font-size: 13px; margin-top: 20px; margin-bottom: 6px; padding-bottom: 4px; border-bottom: 1px solid #ccc; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { text-align: left; padding: 4px 6px; border-bottom: 1px solid #eee; }
        th { background: #f5f5f5; font-weight: bold; }
        tr:nth-child(even) td { background: #fafafa; }
    </style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">Generated {{ $generatedAt }}</div>

    @foreach ($sections as $section)
        <h2>{{ $section['heading'] }}</h2>
        <table>
            @if (!empty($section['columns']))
                <thead>
                    <tr>
                        @foreach ($section['columns'] as $col)
                            <th>{{ $col }}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            <tbody>
                @forelse ($section['rows'] as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td>No data for this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endforeach
</body>
</html>
