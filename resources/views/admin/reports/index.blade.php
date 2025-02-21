<table>
    <thead>
        <tr>
            <th>Title</th>
            <th>Description</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reports as $report)
            <tr>
                <td>{{ $report->title }}</td>
                <td>{{ $report->description }}</td>
                <td>{{ $report->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
