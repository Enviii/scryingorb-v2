<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Scrying Orb</title>
        <meta name="description" content="Latest sale date for League of Legends champions and skins.">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="google" value="notranslate" />
        <link rel="stylesheet" href="http://bootswatch.com/darkly/bootstrap.min.css">
        <link rel="stylesheet" href="{{ URL::asset('css/main.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('css/typeahead.css') }}">
        <script src="{{ URL::asset('js/vendor/modernizr-2.6.2-respond-1.1.0.min.js') }}"></script>

        <style>
            body {
                /* padding-top: 70px; */
                padding-bottom: 20px;
            }

            .carousel-control {
                width: 25%;
            }
        </style>
    </head>
    <body>
        @include('layout.navigation')

        @yield('content')

        <div class="container" id="footer">
            <div class="row text-center">
                <hr>
                <footer>
                    <br>
                    <div class="col-md-8 col-md-offset-2">
                        <p><small class="fine-print">Scrying Orb isn’t endorsed by Riot Games and doesn’t reflect the views or opinions of Riot Games or anyone officially involved in producing or managing League of Legends. League of Legends and Riot Games are trademarks or registered trademarks of Riot Games, Inc. League of Legends © Riot Games, Inc.</small></p>
                    </div>
                </footer>
            </div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
        <script src="{{ URL::asset('js/main.js') }}"></script>
        <script src="{{ URL::asset('js/vendor/typeahead.min.js') }}"></script>
        @yield('js')
    </body>
</html>