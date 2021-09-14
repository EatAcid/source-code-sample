<!DOCTYPE html>
<html ⚡ lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <script async src="https://cdn.ampproject.org/v0.js"></script>
        <link rel="canonical" href="{{ url()->current() }}">
        <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1,maximum-scale=1,user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes"/>
        <meta name="apple-mobile-web-app-status-bar-style" content="black">

        <title> @yield('title', config('app.name')) </title>

        <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>

        @include('layouts.parts.favicons')

        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Oswald:300,400,700|Roboto:300,400">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

        <!-- <script async custom-element="amp-install-serviceworker" src="https://cdn.ampproject.org/v0/amp-install-serviceworker-0.1.js"></script> -->
        <script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
        <script async custom-element="amp-accordion" src="https://cdn.ampproject.org/v0/amp-accordion-0.1.js"></script>
        <script async custom-element="amp-user-notification" src="https://cdn.ampproject.org/v0/amp-user-notification-0.1.js"></script>
        @yield('scriptsInclude')

        <script type="application/ld+json">
            {
                "@context": "http://schema.org",
                "@type": "WebSite",
                "name": "DobrejMatros",
                "alternateName": "",
                "description": "",
                "url": "https://dobrejmatros.com"
            }
        </script>

        <style amp-custom>
            @include('layouts.parts.css') 
        </style>

    <!-- DESCRIPTION META -->
    @yield('description')

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    </head>
<body dir="ltr">
@include('layouts.parts.topnavbar')
<main>

    <div class="container-fluid">
        @yield('content')
    </div>
    <div class="container-fluid">
        @include('layouts.parts.footer')
    </div>

    @include('layouts.parts.menu')

    <amp-install-serviceworker src="sw.js"
                               data-iframe-src="/sw.html"
                               layout="nodisplay">
    </amp-install-serviceworker>

    <amp-user-notification  
        layout="nodisplay"
        id="amp-user-notification1"
        class="salmon-bg sun-color cookie-banner">
        Tento web používá k personalizaci obsahu soubory cookies.
        <button class="light-bg" on="tap:amp-user-notification1.dismiss">Souhlasím</button>
    </amp-user-notification>

</main>
</body>
</html>
