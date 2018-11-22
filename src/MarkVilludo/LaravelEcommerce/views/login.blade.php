<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
        <meta name="author" content="Coderthemes">

        <link rel="shortcut icon" href="{{asset("assets/images/favicon.ico")}}">

        <title>FS21 E-commerce</title>

        <link href="{{asset("../plugins/switchery/switchery.min.css")}}" rel="stylesheet" />

        <link href="{{asset("assets/css/bootstrap.min.css")}}" rel="stylesheet" type="text/css">
        <link href="{{asset("assets/css/icons.css")}}" rel="stylesheet" type="text/css">
        <link href="{{asset("assets/css/style.css")}}" rel="stylesheet" type="text/css">

        <script src="{{asset("assets/js/modernizr.min.js")}}"></script>

    </head>
    <body>
        <div class="wrapper-page">

            <div class="text-center">
                <a class="logo-lg"><i class="mdi mdi-radar"></i> <span>Ecommerce</span> </a>
            </div>

            <form class="form-horizontal m-t-20" action="{{url('/login')}}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group row">
                    <div class="col-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-account"></i></span>
                            </div>
                            <input class="form-control" type="email" name="email" placeholder="Email">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-12">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-key"></i></span>
                            </div>
                            <input class="form-control" type="password" name="password" placeholder="Password">
                        </div>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-12">
                        <div class="checkbox checkbox-primary">
                            <input id="checkbox-signup" type="checkbox">
                            <label for="checkbox-signup">
                                Remember me
                            </label>
                        </div>

                    </div>
                </div>

                <div class="form-group text-right m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-primary btn-custom w-md waves-effect waves-light" type="submit">Log In
                        </button>
                    </div>
                </div>

                <div class="form-group row m-t-30">
                    <div class="col-sm-7">
                        <a href="pages-recoverpw.html" class="text-muted"><i class="fa fa-lock m-r-5"></i> Forgot your
                            password?</a>
                    </div>
                    <div class="col-sm-5 text-right">
                        <a href="pages-register.html" class="text-muted">Create an account</a>
                    </div>
                </div>
            </form>
            @if(Session::has('message'))
              <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
            @endif
        </div>


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
        <script src="../plugins/switchery/switchery.min.js"></script>

        <!-- Custom main Js -->
        <script src="{{asset("assets/js/jquery.core.js")}}"></script>
        <script src="{{asset("assets/js/jquery.app.js")}}"></script>
  
  </body>
</html>
