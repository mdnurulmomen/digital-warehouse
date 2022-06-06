<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Gudam | Your Warehouse Solution</title>
  <!-- Font Awesome Intigration -->
  <script src="https://kit.fontawesome.com/fbaa2d6b3e.js" crossorigin="anonymous"></script>
  <link rel="icon" type="image/x-icon" href="{{asset('/icon/favicon.svg')}}">
  <!-- Bootstrap Intigration -->
  <link
  href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/css/bootstrap.min.css"
  rel="stylesheet"
  />
  <!-- CSS Intigration  -->
  <link rel="stylesheet" href="{{asset('/css/style2.css')}}" />
  <!-- Responsive CSS  -->
  <link rel="stylesheet" href="{{asset('/css/responsive.css')}}" />
  <!-- AOS Animation CSS  -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body>
  <div id="app">
        
    <website-menu-bar/>

  </div>
<!-- AOS Animation Js  -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  /*AOS.init({once: true});*/
</script>

<!-- Owl Carousel Js  -->
<script src="{{asset('/js/jquery.min.js')}}"></script>
<script src="{{asset('/js/app.js')}}"></script>
<script src="{{asset('/js/website.js')}}"></script>
<!-- Bootstrap JS  -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>