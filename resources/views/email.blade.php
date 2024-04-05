<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    У вас новая попытка входа с данного IP адреса: {{$ipv4}}
    ссылка: <a href="{{env('APP_URL')}}/api/v1/verify/{{$token}}">Нажать</a>
</body>
</html>
