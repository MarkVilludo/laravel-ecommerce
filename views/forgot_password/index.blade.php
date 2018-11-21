
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="Coderthemes">

        <link rel="shortcut icon" href="assets/images/favicon.ico">

        <title>Minton - Responsive Admin Dashboard Template</title>

        <link href="../plugins/switchery/switchery.min.css" rel="stylesheet" />

        <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css">
        <link href="assets/css/icons.css" rel="stylesheet" type="text/css">
        <link href="assets/css/style.css" rel="stylesheet" type="text/css">

        <script src="assets/js/modernizr.min.js"></script>

    </head>
    <body>

        <div class="wrapper-page">

            <div class="text-center">
                <a href="index.html" class="logo-lg"><i class="mdi mdi-radar"></i> <span>Minton</span> </a>
            </div>

            <form method="post" action="{{route('send-email-forgot-password')}}" role="form" class="text-center m-t-20">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="alert alert-success alert-dismissable">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                    Enter your <b>Email</b> and instructions will be sent to you!
                </div>
                <div class="form-group m-b-0">
                    <div class="input-group">
                        <input type="email" name="email" class="form-control" placeholder="Enter Email" required="">
                        <span class="input-group-append"> <button type="submit" class="btn btn-email btn-primary waves-effect waves-light">Reset</button> </span>
                    </div>
                </div>

            </form>
        </div>


        <script>
            var resizefunc = [];
        </script>

        <!-- Plugins  -->
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/popper.min.js"></script><!-- Popper for Bootstrap -->
        <script src="assets/js/bootstrap.min.js"></script>
        <script src="assets/js/detect.js"></script>
        <script src="assets/js/fastclick.js"></script>
        <script src="assets/js/jquery.slimscroll.js"></script>
        <script src="assets/js/jquery.blockUI.js"></script>
        <script src="assets/js/waves.js"></script>
        <script src="assets/js/wow.min.js"></script>
        <script src="assets/js/jquery.nicescroll.js"></script>
        <script src="assets/js/jquery.scrollTo.min.js"></script>
        <script src="../plugins/switchery/switchery.min.js"></script>

        <!-- Custom main Js -->
        <script src="assets/js/jquery.core.js"></script>
        <script src="assets/js/jquery.app.js"></script>

    </body>
</html>