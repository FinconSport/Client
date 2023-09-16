<!DOCTYPE html>
<html lang="zn-tw">

<head>
	 <!--     Fonts and icons     -->
	 <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200|Open+Sans+Condensed:700" rel="stylesheet">
    <!-- Jquery -->
    <link href="{{ asset('css/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">
    <!-- COMM CSS Files -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/common.css') }}" rel="stylesheet">
    <link href="{{ asset('css/icon/all.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/semantic.css') }}" rel="stylesheet">
    <link href="{{ asset('css/error__s.css') }}" rel="stylesheet">

</head>

<body>
	<div class='container-fluid'>
		<div class="row">
			<div class="d-flex error-bg text-center">
				<div class="align-self-center mx-auto error-image-container"><img class="img-fluid mx-auto" src="{{ asset('image/error_page/repair.png' ) }}" alt="error icon">
					<p class="error-text">{{ trans('error.main.inmaintenance') }}</p>
				</div>
			</div>
		</div>
	</div>

	<!--  COMM JS Files   -->
	<script src="{{ asset('js/jquery.min.js') }}"></script>
	<script src="{{ asset('js/common.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/semantic.min.js') }}"></script>
</body>

</html>