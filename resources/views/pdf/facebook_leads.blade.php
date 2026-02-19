<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facebook Leads</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <h1>Facebook Leads</h1>
    <table>
        <thead>
            <tr>
                <th>Full Name</th>
                <th>Details</th>
                <th>Phone Number</th>
                <th>Email</th>
                <th>City</th>
                <th>Requirement</th>
                <th>Created Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fbleads as $lead)
                <tr>
                    <td>{{ $lead->full_name }}</td>
                    <td>{{ $lead->details }}</td>
                    <td>{{ $lead->phone_number ?? 'N/A' }}</td>
                    <td>{{ $lead->email ?? 'N/A' }}</td>
                    <td>{{ $lead->city ?? 'N/A' }}</td>
                    <td>{{ $lead->your_requirement ?? 'N/A' }}</td>
                    <td>{{ \Carbon\Carbon::parse($lead->created_time)->format('Y-m-d H:i:s') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
