<!DOCTYPE html>
<html lang="en">

<head>
	<title>Gudam | Merchant Template</title>

	<meta charset="utf-8">
	<meta http-equiv="content-type" content="text/html;charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="description" content="Gudam merchant template made using Bootstrap 4 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
	<meta name="keywords" content="flat ui, merchant, Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
	<meta name="csrf-token" content={{csrf_token()}}>

	<link rel="icon" href="{{ URL::asset('uploads/application/application_favicon.png') }}" type="image/x-icon" sizes="16x16"/>
	{{-- <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Quicksand:500,700" rel="stylesheet"> --}}
	<link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap.min.css')}}">
	<link rel="stylesheet" href="{{asset('css/waves.min.css')}}" type="text/css" media="all">
	<link rel="stylesheet" type="text/css" href="{{asset('css/feather.css')}}">
	<link rel="stylesheet" href="{{asset('css/font-awesome-4.min.css')}}">
	<link rel="stylesheet" href="{{asset('css/font-awesome.min.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('css/style.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('css/widget.css')}}">
	{{-- <link rel="stylesheet" type="text/css" href="{{asset('css/app.css')}}"> --}}
</head>

<body>

	<div class="loader-bg">
		<div class="loader-bar"></div>
	</div>

	<div id="pcoded" class="pcoded">
		<div class="pcoded-overlay-box"></div>
		<div class="pcoded-container navbar-wrapper">

			<nav class="navbar header-navbar pcoded-header">
				<div class="navbar-wrapper">
					<div class="navbar-logo">
						<a href="{{ route('merchant.home', 'home') }}">
							<img class="img-fluid" src="{{asset('uploads/application/application_logo.png')}}" alt="Theme-Logo" />
						</a>
						<a class="mobile-menu" id="mobile-collapse" href="#!">
							<i class="feather icon-menu icon-toggle-right"></i>
						</a>
						<a class="mobile-options waves-effect waves-light">
							<i class="feather icon-more-horizontal"></i>
						</a>
					</div>
					<div class="navbar-container container-fluid">
						<ul class="nav-left">
							<li class="header-search">
								<div class="main-search morphsearch-search">
									<div class="input-group">
										<span class="input-group-prepend search-close">
											<i class="feather icon-x input-group-text"></i>
										</span>
										<input type="text" class="form-control" placeholder="Enter Keyword">
										<span class="input-group-append search-btn">
											<i class="feather icon-search input-group-text"></i>
										</span>
									</div>
								</div>
							</li>
							<li>
								<a href="#!" onclick="if (!window.__cfRLUnblockHandlers) return false; javascript:toggleFullScreen()" class="waves-effect waves-light" data-cf-modified-d2d1d6e2f87cbebdf4013b26-="">
									<i class="full-screen feather icon-maximize"></i>
								</a>
							</li>
						</ul>
						<ul class="nav-right">
						{{-- 
							<li class="header-notification">
								<div class="dropdown-primary dropdown">
									<div class="dropdown-toggle" data-toggle="dropdown">
										<i class="feather icon-bell"></i>
										<span class="badge bg-c-red">100</span>
									</div>
									<ul class="show-notification notification-view dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
										<li>
											<h6>Notifications</h6>
											<label class="label label-danger">New</label>
										</li>
										<li>
											<div class="media">
												<img class="img-radius" src="{{asset('jpg/avatar-4.jpg')}}" alt="Generic placeholder image">
												<div class="media-body">
													<h5 class="notification-user">John Doe</h5>
													<p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
													<span class="notification-time">30 minutes ago</span>
												</div>
											</div>
										</li>
										<li>
											<div class="media">
												<img class="img-radius" src="{{asset('jpg/avatar-3.jpg')}}" alt="Generic placeholder image">
												<div class="media-body">
													<h5 class="notification-user">Joseph William</h5>
													<p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
													<span class="notification-time">30 minutes ago</span>
												</div>
											</div>
										</li>
										<li>
											<div class="media">
												<img class="img-radius" src="{{asset('jpg/avatar-4.jpg')}}" alt="Generic placeholder image">
												<div class="media-body">
													<h5 class="notification-user">Sara Soudein</h5>
													<p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
													<span class="notification-time">30 minutes ago</span>
												</div>
											</div>
										</li>
									</ul>
								</div>
							</li>
							<li class="header-notification">
								<div class="dropdown-primary dropdown">
									<div class="displayChatbox dropdown-toggle" data-toggle="dropdown">
										<i class="feather icon-message-square"></i>
										<span class="badge bg-c-green">3</span>
									</div>
								</div>
							</li>
 						--}}
							<li class="user-profile header-notification">
								<div class="dropdown-primary dropdown">
									<div class="dropdown-toggle" data-toggle="dropdown">
										<img src="{{ asset(\Auth::guard('merchant')->user()->profilePreview->preview) }}" class="img-radius" alt="User-Profile-Image">
										<span>{{ ucwords(\Auth::guard('merchant')->user()->user_name) }}</span>
										<i class="feather icon-chevron-down"></i>
									</div>
									<ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
										<li>
											<a onclick="showProfile()" href="javascript:void(0)">
												<i class="feather icon-user"></i> Profile
											</a>
										</li>
									{{-- 
										<li>
											<a href="email-inbox.html">
												<i class="feather icon-mail"></i> My Messages
											</a>
										</li>
										<li>
											<a href="auth-lock-screen.html">
												<i class="feather icon-lock"></i> Lock Screen
											</a>
										</li>
 									--}}
										<li>

											<a href="{{ route('logout') }}"
		                                       onclick="event.preventDefault();logout();
		                                                     document.getElementById('merchant-logout-form').submit();">
		                                        <i class="feather icon-log-out"></i>
		                                        {{ __('Logout') }}
		                                    </a>

		                                    <form id="merchant-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
		                                        @csrf
		                                    </form>

										</li>
									</ul>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</nav>

		{{-- 
			<div id="sidebar" class="users p-chat-user showChat">
				<div class="had-container">
					<div class="p-fixed users-main">
						<div class="user-box">
							<div class="chat-search-box">
								<a class="back_friendlist">
									<i class="feather icon-x"></i>
								</a>
								<div class="right-icon-control">
									<div class="input-group input-group-button">
										<input type="text" id="search-friends" name="footer-email" class="form-control" placeholder="Search Friend">
										<div class="input-group-append">
											<button class="btn btn-primary waves-effect waves-light" type="button"><i class="feather icon-search"></i></button>
										</div>
									</div>
								</div>
							</div>
							<div class="main-friend-list">
								<div class="media userlist-box waves-effect waves-light" data-id="1" data-status="online" data-username="Josephin Doe">
									<a class="media-left" href="#!">
										<img class="media-object img-radius img-radius" src="{{asset('jpg/avatar-3.jpg')}}" alt="Generic placeholder image ">
										<div class="live-status bg-success"></div>
									</a>
									<div class="media-body">
										<div class="chat-header">Josephin Doe</div>
									</div>
								</div>
								<div class="media userlist-box waves-effect waves-light" data-id="2" data-status="online" data-username="Lary Doe">
									<a class="media-left" href="#!">
										<img class="media-object img-radius" src="{{asset('jpg/avatar-2.jpg')}}" alt="Generic placeholder image">
										<div class="live-status bg-success"></div>
									</a>
									<div class="media-body">
										<div class="f-13 chat-header">Lary Doe</div>
									</div>
								</div>
								<div class="media userlist-box waves-effect waves-light" data-id="3" data-status="online" data-username="Alice">
									<a class="media-left" href="#!">
										<img class="media-object img-radius" src="{{asset('jpg/avatar-4.jpg')}}" alt="Generic placeholder image">
										<div class="live-status bg-success"></div>
									</a>
									<div class="media-body">
										<div class="f-13 chat-header">Alice</div>
									</div>
								</div>
								<div class="media userlist-box waves-effect waves-light" data-id="4" data-status="offline" data-username="Alia">
									<a class="media-left" href="#!">
										<img class="media-object img-radius" src="{{asset('jpg/avatar-3.jpg')}}" alt="Generic placeholder image">
										<div class="live-status bg-default"></div>
									</a>
									<div class="media-body">
										<div class="f-13 chat-header">Alia<small class="d-block text-muted">10 min ago</small></div>
									</div>
								</div>
								<div class="media userlist-box waves-effect waves-light" data-id="5" data-status="offline" data-username="Suzen">
									<a class="media-left" href="#!">
										<img class="media-object img-radius" src="{{asset('jpg/avatar-2.jpg')}}" alt="Generic placeholder image">
										<div class="live-status bg-default"></div>
									</a>
									<div class="media-body">
										<div class="f-13 chat-header">Suzen<small class="d-block text-muted">15 min ago</small></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		 
			<div class="showChat_inner">
				<div class="media chat-inner-header">
					<a class="back_chatBox">
						<i class="feather icon-x"></i> Josephin Doe
					</a>
				</div>
				<div class="main-friend-chat">
					<div class="media chat-messages">
						<a class="media-left photo-table" href="#!">
							<img class="media-object img-radius img-radius m-t-5" src="{{asset('jpg/avatar-2.jpg')}}" alt="Generic placeholder image">
						</a>
						<div class="media-body chat-menu-content">
							<div class="">
								<p class="chat-cont">I'm just looking around. Will you tell me something about yourself?</p>
							</div>
							<p class="chat-time">8:20 a.m.</p>
						</div>
					</div>
					<div class="media chat-messages">
						<div class="media-body chat-menu-reply">
							<div class="">
								<p class="chat-cont">Ohh! very nice</p>
							</div>
							<p class="chat-time">8:22 a.m.</p>
						</div>
					</div>
					<div class="media chat-messages">
						<a class="media-left photo-table" href="#!">
							<img class="media-object img-radius img-radius m-t-5" src="{{asset('jpg/avatar-2.jpg')}}" alt="Generic placeholder image">
						</a>
						<div class="media-body chat-menu-content">
							<div class="">
								<p class="chat-cont">can you come with me?</p>
							</div>
							<p class="chat-time">8:20 a.m.</p>
						</div>
					</div>
				</div>
				<div class="chat-reply-box">
					<div class="right-icon-control">
						<div class="input-group input-group-button">
							<input type="text" class="form-control" placeholder="Write hear . . ">
							<div class="input-group-append">
								<button class="btn btn-primary waves-effect waves-light" type="button"><i class="feather icon-message-circle"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>  
		--}}
		
			<div id="app">
				<merchant-side-menu-bar></merchant-side-menu-bar>
			</div>
		</div>
	</div>

	<!-- Scripts -->
	<script type="text/javascript">
		window.localStorage.setItem("general_settings", JSON.stringify(@json($general_settings)));
	</script>
    <script src="{{ mix('js/merchant.js') }}"></script>
	<script type="d2d1d6e2f87cbebdf4013b26-text/javascript" src="{{asset('js/jquery-ui.min.js')}}"></script>
	<script src="{{asset('js/waves.min.js')}}" type="d2d1d6e2f87cbebdf4013b26-text/javascript"></script>
	<script type="d2d1d6e2f87cbebdf4013b26-text/javascript" src="{{asset('js/jquery.slimscroll.js')}}"></script>
	<script src="{{asset('js/pcoded.min.js')}}" type="d2d1d6e2f87cbebdf4013b26-text/javascript"></script>
	<script src="{{asset('js/vertical-layout.min.js')}}" type="d2d1d6e2f87cbebdf4013b26-text/javascript"></script>
	<script type="d2d1d6e2f87cbebdf4013b26-text/javascript" src="{{asset('js/script.min.js')}}"></script>
	{{-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13" type="d2d1d6e2f87cbebdf4013b26-text/javascript"></script> --}}
	<script src="{{asset('js/rocket-loader.min.js')}}" data-cf-settings="d2d1d6e2f87cbebdf4013b26-|49" defer=""></script>

</body>

</html>
