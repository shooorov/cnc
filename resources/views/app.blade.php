<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">

    <!-- Scripts -->
    @routes
	@vite(["resources/js/app.js", "resources/js/plugins.js", "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased h-full">
    @inertia

	<form id="delete-form" action="#" method="POST" class="hidden">
		@csrf
		@method("delete")
	</form>
</body>

</html>
