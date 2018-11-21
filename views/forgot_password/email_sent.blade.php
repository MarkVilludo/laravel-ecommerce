
<!DOCTYPE html>
<html>
    <head>
        <!-- //header page -->
        @include('includes.header')
    </head>
    <body>

        <div class="wrapper-page">

            <div class="text-center">
                <a href="index.html" class="logo-lg"><i class="mdi mdi-radar"></i> <span>FS 21</span> </a>
            </div>

            <div class="card-box m-t-20">
                <div class="text-center">
                    <h5 class="text-uppercase font-bold m-b-0">Confirm Email</h5>
                </div>
                <div class="text-center">
                    <img src="assets/images/mail_confirm.png" alt="img" class="thumb-lg m-t-20 center-block">
                    @if ($existing)
                        <p class="text-muted font-13 m-t-20"> A email has been sent to <b>{{$user->email}}</b>. Please check your email and it include you new password. </p>
                    @else 
                        <p class="text-muted font-13 m-t-20"> Your email <b>{{$email}}</b>. doesn't exist in records. Please double check your email. </p>
                    @endif
                </div>
            </div>

        </div>
        @include('includes.footer_script')
    </body>
</html>