@extends('layouts.app')
@section('content')
    <!-- Start content -->
    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title">Profile</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="#">User</a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            @if (Session::has('message'))
               <div class="alert alert-success">{{ Session::get('message') }}</div>
            @endif
            <template>
            <!-- //Main content page. -->
                <div class="row">
                    <div class="col-xl-3 col-lg-4">
                        <div class="text-center card-box">
                            <div class="member-card">
                                <div class="thumb-xl member-thumb m-b-10 center-block">
                                    <img v-if="user.image_path" :src="user.image_path" class="rounded-circle img-thumbnail" alt="profile-image">
                                    <img v-if="!user.image_path" src="assets/images/users/avatar-1.jpg" class="rounded-circle img-thumbnail" alt="profile-image">
                                </div>

                                <div class="">
                                    <h5 class="m-b-5">@{{user.first_name+' '+user.last_name}}</h5>
                                    <p class="text-muted">@Backend Developer</p>
                                </div>

                                <div class="text-left m-t-40">
                                    <p class="text-muted font-13"><strong>Full Name :</strong> <span class="m-l-15">@{{user.first_name+' '+user.last_name}}</span></p>

                                    <p class="text-muted font-13"><strong>Mobile :</strong><span class="m-l-15">(123) 123 1234</span></p>

                                    <p class="text-muted font-13"><strong>Email :</strong> <span class="m-l-15">@{{user.email}}</span></p>

                                    <p class="text-muted font-13"><strong>Location :</strong> <span class="m-l-15">Phillippines</span></p>
                                </div>
                            </div>

                        </div> <!-- end card-box -->

                    </div> <!-- end col -->
                    <div class="col-lg-8 col-xl-9">
                        <div class="">
                            <div class="card-box">
                                <ul class="nav nav-tabs tabs-bordered">
                                    <li class="nav-item">
                                        <a href="#home" data-toggle="tab" aria-expanded="false" class="nav-link">
                                            ABOUT ME
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#settings" data-toggle="tab" aria-expanded="false" class="nav-link">
                                            SETTINGS
                                        </a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div class="tab-pane active" id="home">
                                        <p class="m-b-5">Hi I'm @{{user.first_name+' '+user.last_name}},has been the industry's standard dummy text ever
                                            since the 1500s, when an unknown printer took a galley of type.
                                            Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu.
                                            In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo.
                                            Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras
                                            dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend
                                            tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend
                                            ac, enim.</p>

                                        <div class="m-t-30">
                                            <h5>Experience</h5>

                                            <div class=" p-t-10">
                                                <h6 class="text-primary m-b-5">Lead designer / Developer</h6>
                                                <p class="">websitename.com</p>
                                                <p><b>2010-2015</b></p>

                                                <p class="text-muted font-13 m-b-0">Lorem Ipsum is simply dummy text
                                                    of the printing and typesetting industry. Lorem Ipsum has
                                                    been the industry's standard dummy text ever since the
                                                    1500s, when an unknown printer took a galley of type and
                                                    scrambled it to make a type specimen book.
                                                </p>
                                            </div>

                                            <hr>

                                            <div class="">
                                                <h6 class="text-primary m-b-5">Senior Graphic Designer</h6>
                                                <p class="">coderthemes.com</p>
                                                <p><b>2007-2009</b></p>

                                                <p class="text-muted font-13">Lorem Ipsum is simply dummy text
                                                    of the printing and typesetting industry. Lorem Ipsum has
                                                    been the industry's standard dummy text ever since the
                                                    1500s, when an unknown printer took a galley of type and
                                                    scrambled it to make a type specimen book.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="settings">
                                        <form role="form">
                                            <div class="form-group">
                                                <label for="FullName">First Name</label>
                                                <input type="text" v-model="user.first_name" id="first_name" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="FullName">Full Name</label>
                                                <input type="text" v-model="user.last_name" id="last_name" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="Email">Email</label>
                                                <input type="email" v-model="user.email" id="Email" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="Password">Password</label>
                                                <input type="password" v-model="password" placeholder="6 - 15 Characters" id="Password" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="RePassword">Re-Password</label>
                                                <input type="password"  v-model="password_confirmation" placeholder="6 - 15 Characters" id="RePassword" class="form-control">
                                            </div>
                                            <button class="btn btn-primary waves-effect waves-light w-md" type="button" @click="updateProfile()">Update</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div> <!-- end col -->
                </div>
            </template>
            <!-- End main content page -->
        <!-- end container -->
        </div>
    <!-- end content -->
    </div>
@endsection
@section('bottom_scripts')
@include('includes.vue-scripts')
<!-- sweet alert and infinite loading -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script>

    var vue = new Vue({
        el: '.content',
        mounted() {
            this.getProfileDetails();
        },
        data: {
            user: '',
            image_path: '',
            password: '',
            password_confirmation: '',
            noResultFound : false,
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}'
        },
        methods: {
            filtered(orderStatus) {
                alert(orderStatus)
            }, 
            getProfileDetails() {
                axios.get("{{route('api.user.profile')}}", {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data.data)
                    this.user = response.data.data;
                    this.image_path = this.user.image_path;
                });
            },
            updateProfile(){
                console.log('goes here..');
                axios.post("{{route('api.update.profile', auth()->user()->id)}}", 
                    {
                        first_name: this.user.first_name,
                        last_name: this.user.last_name,
                        email: this.user.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation
                    }, {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                })
                .then((response) => {
                    console.log(response.data)
                    swal("Success!", response.data.message, "info");
                })
                .catch(function (response) {
                    //handle error
                    console.log(response);
                    swal("Failed!", JSON.stringify(response.response.data.message), "error");
                });
            }
        },
    });
</script>
@endsection

