
@extends('layouts.app')
@section('content')
<!-- Start content -->
<div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title">Customer Care </h4>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="{{route('pages.contact-page.index')}}">Customer Care </a></li>
                    <li class="breadcrumb-item active">Index</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
      </div>
    <template>
        <div class="row">
            <div class="col-lg-6">
                <div class="card-box" >
                    <div class="form-group">
                        <label for="name">Contact Number</label>
                        <input type="text" v-model="contact_number" name="contact_number" class="form-control" pattern="[0-9,[\+-]]">
                    </div>
                    <div class="form-group">
                        <label for="name">Email</label>
                        <input type="email" v-model="email" name="email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="name">Shipping and Delivery Concerns Email</label>
                        <input type="email" v-model="shipping_concern_email" name="shipping_concern_email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="name">PR and Media Inquiries Email</label>
                        <input type="email" v-model="pr_media_inquiry_email" name="pr_media_inquiry_email" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="name">Partnership, Business and Company Inquiries Email</label>
                        <input type="email" v-model="partnership_business_inquery_email" name="partnership_business_inquery_email" class="form-control">
                    </div>
                    <br>
                </div>
                <button class="btn btn-success btn-block pt-2 pb-2" @click="updateContact()"> Update </button>
            </div>
        </div>
    </template>

    </div>
</div>
<!-- end content -->
@endsection
@section('bottom_scripts')

@include('includes.vue-scripts')
<!-- Include the Quill library -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<!-- Include stylesheet -->
<script>
 
    new Vue({
    el: '.content',
    data: {
        contact_number: '',
        email: '',
        shipping_concern_email: '',
        pr_media_inquiry_email: '',
        partnership_business_inquery_email: ''
    },
    methods: {
        getContactDetails(url) {
            axios.get(url, {
                headers: {
                    'Authorization': this.header_authorization,
                    'Accept': this.header_accept
                }
            })
            .then((response) => {
                console.log(response.data.data)
                this.contact_number = response.data.data[0].contact_number;
                this.email = response.data.data[0].email;
                this.shipping_concern_email = response.data.data[0].shipping_concern_email;
                this.pr_media_inquiry_email = response.data.data[0].pr_media_inquiry_email;
                this.partnership_business_inquery_email = response.data.data[0].partnership_business_inquery_email;
            });
        },
       updateContact(){
            axios.post("{{route('api.pages.update.customer_care',1)}}", { 
                    contact_number : this.contact_number,
                    email : this.email,
                    shipping_concern_email: this.shipping_concern_email,
                    pr_media_inquiry_email: this.pr_media_inquiry_email,
                    partnership_business_inquery_email: this.partnership_business_inquery_email
                }).then((response) => {
                console.log(response)
                swal("Success!",  response.data.message, "success");
            }).catch(error => {
                console.log(error.response)
                console.log(error.response.data.errors)
                var errors = [];
                $.each( error.response.data.errors, function( index, error ){
                    errors.push(error.message)
                });
                swal("Failed!",  JSON.stringify(errors.toString()), "info");
            });
        }
    },
    computed: {
        },
        mounted() {
            this.getContactDetails("{{route('api.pages.customer_care')}}");
        }
    })

</script>
@endsection
