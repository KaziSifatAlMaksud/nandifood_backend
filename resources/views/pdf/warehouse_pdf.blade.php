<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
</head>
<body>
    <h1>{{ $title }}</h1>
    <h1>{{ $date }}</h1>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Warehouse ID</th>
                <th>Warehouse Name</th>
                <th>Warehouse Address</th>
                <th>Warehouse Capacity</th>
                <th>Warehouse Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($result as $warehouse)
                <tr>
                    <td>{{ $warehouse->id }}</td>
                    <td>{{ $warehouse->warehouse_name }}</td>
                    <td>{{ $warehouse->address1 }}</td>
                    <td>{{ $warehouse->warehouse_capacity_in_kg }}</td>
                    <td>{{ $warehouse->volume }}</td>
                </tr>
            @endforeach
    </table>
    
</body>
</html>