
<script>
    var resizefunc = [];
</script>

<!-- Plugins  -->
<script src="{{asset("assets/js/jquery.min.js")}}"></script>
<script src="{{asset("assets/js/popper.min.js")}}"></script><!-- Popper for Bootstrap -->
<script src="{{asset("assets/js/bootstrap.min.js")}}"></script>
<script src="{{asset("assets/js/detect.js")}}"></script>
<script src="{{asset("assets/js/fastclick.js")}}"></script>
<script src="{{asset("assets/js/jquery.slimscroll.js")}}"></script>
<script src="{{asset("assets/js/jquery.blockUI.js")}}"></script>
<script src="{{asset("assets/js/waves.js")}}"></script>
<script src="{{asset("assets/js/wow.min.js")}}"></script>
<script src="{{asset("assets/js/jquery.nicescroll.js")}}"></script>
<script src="{{asset("assets/js/jquery.scrollTo.min.js")}}"></script>

<!-- Counter Up  -->
<script src="{{asset("plugins/waypoints/lib/jquery.waypoints.min.js")}}"></script>
<script src="{{asset("plugins/counterup/jquery.counterup.min.js")}}"></script>

<!-- circliful Chart -->
<script src="{{asset("plugins/jquery-circliful/js/jquery.circliful.min.js")}}"></script>
<script src="{{asset("plugins/jquery-sparkline/jquery.sparkline.min.js")}}"></script>

<!-- skycons -->
<script src="{{asset("plugins/skyicons/skycons.min.js")}}" type="text/javascript"></script>

<!-- Page js  -->
<script src="{{asset("assets/pages/jquery.dashboard.js")}}"></script>

<!-- Custom main Js -->
<script src="{{asset("assets/js/jquery.core.js")}}"></script>
<script src="{{asset("assets/js/jquery.app.js")}}"></script>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.counter').counterUp({
            delay: 100,
            time: 1200
        });
        $('.circliful-chart').circliful();
    });

    // BEGIN SVG WEATHER ICON
    if (typeof Skycons !== 'undefined'){
        var icons = new Skycons(
                {"color": "#3bafda"},
                {"resizeClear": true}
                ),
                list  = [
                    "clear-day", "clear-night", "partly-cloudy-day",
                    "partly-cloudy-night", "cloudy", "rain", "sleet", "snow", "wind",
                    "fog"
                ],
                i;

        for(i = list.length; i--; )
            icons.set(list[i], list[i]);
        icons.play();
    };

</script>

@yield('bottom_scripts')
