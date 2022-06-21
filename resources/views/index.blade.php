<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content={{csrf_token()}}>
  <title>Gudam | Your Warehouse Solution</title>
  <!-- Font Awesome Intigration -->
  <script src="https://kit.fontawesome.com/fbaa2d6b3e.js" crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="{{asset('system/favicon.png')}}">
  <!-- Bootstrap Intigration -->
  <link rel="stylesheet" href="{{asset('/css/bootstrap5.min.css')}}" />
  <!-- CSS Intigration  -->
  <link rel="stylesheet" href="{{asset('/css/website.css')}}" />
  <!-- Responsive CSS  -->
  <link rel="stylesheet" href="{{asset('/css/responsive.css')}}" />
</head>
<body>
  <div id="app">
    
    <website-menu-bar/>

  </div>

  <script src="{{asset('/js/website.js')}}"></script>
  <!-- Bootstrap JS  -->
  <script src="{{asset('/js/bootstrap5.bundle.min.js')}}"></script>
</body>
</html>