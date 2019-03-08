<!DOCTYPE html>
<html lang="ru" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Blockchain - @yield('title')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap.min.css" />
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.3.1/css/bootstrap-reboot.min.css" /> -->
    <style media="screen">
      .alert{
        display: block;
        width: 100%;

      }
      p{
        display: block;
        width: 100%;
      }
    </style>
  </head>
  <body>
    <div class="container">
      @yield('content')
    </div>
  </body>
</html>
