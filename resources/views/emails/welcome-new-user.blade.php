<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to ISM</title>
</head>
<body>
    <div>
    Welcome {{$user->first_name}},
    You have been granted access
    <div>
        <h3>Login Details</h3>
        <p>email:  {{$user->email}}</p>
        <p>password:  {{$password}}</p>

    </div>
</div>
</body>
</html>