<?php
    // Crear sesión de Laravel
    // session(['idScheduleSession' => '1234'])
    // Regenerar sesión de Laravel. Laravel ya lo hace por defecto, pero podemos hacerlo manualmente
    // session()->regenerate();
?>

    <!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Schedule form</title>
</head>
<body>
<h1>@lang('Schedule form')</h1>
<form action="{{url('/schedule/contacts')}}" method="POST">
    <label for="name">@lang('Name'):
        <input type="text" name="name">
    </label>
    <br>
    <label for="phone">@lang('Phone')::
        <input type="text" name="phone">
    </label>
    <br>
    <button>@lang('Send')</button>
</form>

<?php
//    var_dump(session());
//    if (session()->has('idScheduleSession')) {
//        echo 'Schedule session id: '. session()->get('idScheduleSession');
//    }
//    check_cookie();
//    render_form();
?>
</body>
</html>

<?php
//function check_cookie()
//{
//    if (isset($_COOKIE['laravel_session'])) {
//        var_dump($_COOKIE);
//    }
//}
?>

