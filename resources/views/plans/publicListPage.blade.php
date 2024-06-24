<!DOCTYPE html>
<html lang="en">
<head>
	<title>@yield('title') - {{ \Acelle\Model\Setting::get("site_name") }}</title>

	@include('layouts.core._head')
    @include('layouts.core._script_vars')
</head>

<body class="" style="">
    

	<!-- Page header -->
	<div class="page-header">
		<div class="page-header-content">

			@yield('page_header')

		</div>
	</div>
	<!-- /page header -->

	<!-- Page container -->
	<div class="page-container" style="min-height: 100vh">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main content -->
			<div class="content-wrapper">
                <div class="px-4">
                    <div class="text-start mb-5 pb-4 border-bottom">
                        <img src="{{ getSiteLogoUrl('dark') }}" alt="" height="50px">
                    </div>

                    {{-- <h1>{{ \Acelle\Model\Setting::get('site_name') }}</h1>
                    <p>{{ \Acelle\Model\Setting::get('site_description') }}</p> --}}

                    <!-- main inner content -->
                    @include('plans.publicView.' . $style, [
                        'plans' => $plans,
                    ])
                </div>
			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

    {!! \Acelle\Model\Setting::get('custom_script') !!}

</body>
</html>

    
    