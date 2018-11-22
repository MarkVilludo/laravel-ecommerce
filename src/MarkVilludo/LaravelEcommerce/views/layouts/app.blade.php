<!DOCTYPE html>
<html>
	<head>
		<!-- //header page -->
		@include('includes.header')
	</head>

	<body class="fixed-left">
        <!-- Begin page -->
        <div id="wrapper">
            <!-- //topbar -->
            @include('includes.topbar')
            <!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->               
            <!-- left sidebar content -->
            
            <!-- right sidebar content -->
            
            @include('includes.left_sidebar')


			<!-- ============================================================== -->
            <!-- Start right Content here -->
            <!-- ============================================================== -->
            <div class="content-page">
                <!-- Start content -->
				@yield('content')
				@include('includes.footer')
			</div>
            <!-- ============================================================== -->
            <!-- End Right content here -->
            <!-- ============================================================== -->

            @include('includes.right_sidebar') 
		
        </div>
        <!-- END wrapper -->

        
		@include('includes.footer_script')
		
	</body>
</html>
