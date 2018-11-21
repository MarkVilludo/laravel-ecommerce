
@extends('layouts.app')
@section('content')
<!-- Start content -->
<div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title">Privacy Policy</h4>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="#">Privacy Policy</a></li>
                    <li class="breadcrumb-item active">Index</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
      </div>
    <template>
        <div class="row">
            <div class="col-lg-12">
                <div class="card-box">
                    <textarea v-model="content" class="form-control" style="margin-top: 0px; margin-bottom: 0px; height: 385px;"></textarea> 
                    <br>
                </div>
            </div>
        </div>
        <button class="btn btn-success btn-block pt-2 pb-2" @click="updatePrivacyPolicy()"> Update </button>
    </template>

    </div>
</div>
<!-- end content -->
@endsection
@section('bottom_scripts')

@include('includes.vue-scripts')
<!-- Include the Quill library -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>

<script>
    new Vue({
    el: '.content',
    data: {
        content: ''
    },
    methods: {
        getTermsCondition(url) {
            axios.get(url, {
                headers: {
                    'Authorization': this.header_authorization,
                    'Accept': this.header_accept
                }
            })
            .then((response) => {
                console.log(response.data.data)
                this.content = response.data.data[0].content;
            });
        }, updatePrivacyPolicy(){
            axios.post("{{route('pages.update_privacy-policy',1)}}", { 
                    content : this.content,
                    file_name : $("#file_name").val(),
                    path : $("#path").val(),
                }).then((response) => {
                console.log(response)
                swal("Success!",  response.data.message, "success");
                //reset all data in form
            }).catch(error => {
                console.log(error)
                console.log(error.response.data.errors)
                var errors = [];
                $.each( error.response.data.errors, function( index, error ){
                    errors.push(error.message)
                });

                var errorMsg = errors ? JSON.stringify(errors.toString()).replace(/[0-9]/g, " ") : error.response.data.message;
                swal("Failed!",  errorMsg, "info");
            });
        }
    },
    computed: {
        },
        mounted() {
            this.getTermsCondition("{{route('api.privacy_policy')}}");
        }
    })
</script>
@endsection
