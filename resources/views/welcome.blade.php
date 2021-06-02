<?php
/**
 * Copyright (c) NMS PRIME GmbH ("NMS PRIME Community Version")
 * and others – powered by CableLabs. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at:
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
?>
<!doctype html>
<html>

	<head>
		<meta charset="utf-8">
		<title>NMS</title>
		@include ('bootstrap.header')

		<script>setTimeout("document.getElementById('error').style.display='none';", 3000);</script>
	</head>

	@include ('bootstrap.header')

	<body class="pace-top">

	{{-- Background Image --}}
	<div class="login-cover">
		<div class="login-cover-image"><img alt="" data-id="login-cover-image" src="{{asset('images/main-pic-1.png')}}"></div>
		<div class="login-cover-bg"></div>
	</div>

		{{-- begin login --}}
		<div class="login login-v2 animated fadeInDown">

			{{-- begin brand --}}
			<div class="login-header">
				<div class="brand">
					<span class="logo"></span> {{ $head1 }}
					<small>{{ $head2 }}</small>
				</div>
				<div class="icon">
					<i class="fa fa-sign-in"></i>
				</div>
			</div>

			{{-- end brand --}}
			<div class="login-content">
				<div align="center">

					<div class="login-buttons">
						<a href='admin' class="btn btn-success btn-block btn-lg" role="button">Admin Center</a>
						<br><br><br>
						<a href='customer' class="btn btn-success btn-block btn-lg" role="button">Customer Control Center</a>
					</div>

					<br>
					<div class="quote">{{ Inspiring::quote() }}</div>

				</div>
			</div>

		</div>
		{{-- end login --}}

		@include ('bootstrap.footer')
	</body>
</html>
