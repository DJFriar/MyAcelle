<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>

    @include('layouts.core._head')
    @include('layouts.core._script_vars')
    @yield('head')
</head>

<body class="list-page bg-slate-800">

    <div class="row">
        <div class="col-sm-2 col-md-3">

        </div>
        <div class="col-sm-8 col-md-6">
            @include('layouts.core._errors')

        </div>

        <!-- /subscribe form -->

        </div>
    </div>

    @yield('content')

</body>
</html>
