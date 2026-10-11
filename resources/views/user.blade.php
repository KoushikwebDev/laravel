<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <ul style="list-style: none; font-size: 20px;">
        @foreach ($users as $user)
            <li>{{ $user }}</li>
        @endforeach
    </ul>
</body>
</html>