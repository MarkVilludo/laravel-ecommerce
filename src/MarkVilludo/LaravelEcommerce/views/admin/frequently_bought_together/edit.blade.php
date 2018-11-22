@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title">FBT</h4>
                            <ol class="breadcrumb float-right">
                                <li class="breadcrumb-item"><a href="{{route('fbt.index')}}">FBT</a></li>
                                <li class="breadcrumb-item active">Create</li>
                            </ol>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <template>
                    <div class="row">
                        <div class='col-lg-6 col-lg-offset-6'>
                            <hr>
                            <div class="form-group">
                                <label>Name</label>
                                <input type="text" name="name" v-model="name" class="form-control">
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="category">Select from categories</label>
                                        <select name="child_sub_category_id" v-model="child_sub_category_id"  @change="onChangeCategory(child_sub_category_id)" class="form-control">
                                            <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{$category->id}}">{{$category->title}}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="card-box">
                                    <div class="row">
                                        <div class="col-lg-9 mt-4">
                                            <label for="status">Select Frequently Bought Together Product <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="row col-md-12 col-lg-12">
                                        <div class="col-md-12 col-lg-12 pl-0">
                                            <multiselect
                                                v-model="product_items" 
                                                :options="productOptions"
                                                :multiple="true"
                                                track-by="name"
                                                :custom-label="customLabel"
                                                class="form-control"
                                                >
                                              </multiselect>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-primary" @click="updateFBT()">Update</button>
                        </div>
                    </div>
                </template>
            </div>
                <!-- End main content page -->
                <!-- end row -->
            </div>
    </div>
    <!-- end container -->
@endsection

@section('bottom_scripts')
@include('includes.vue-scripts')
<script src="{{url('assets/js/vue-infinite-loading.js')}}"></script>
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script src="{{url('assets/js/vue-multiselect.min.js')}}"></script>

<script>
    var vue = new Vue({
        el: '.content',
        mounted() {
         this.products();
         this.getFBTDetails();
        },
        components: {
            Multiselect: window.VueMultiselect.default
        },
        data: {
            productOptions: [],
            child_sub_category_id: 0,
            file : '',
            name : '',
            description : '',
            warranty : '',
            warranty_type : '',
            price : '',
            status : '',
            product_items : [],
            package_image : ''
        },
        methods: {
            filtered: function(status) {
                alert(status)
            },
            customLabel: function (option) {
              return `${option.name}`
            },
            onChangeCategory: function(child_sub_category_id) {
                this.productOptions = [];
                console.log(child_sub_category_id)  
                var subCategory = child_sub_category_id;
                axios.get("{{route('api.sub_category_details')}}/" + subCategory)
                .then((response) => {
                    console.log(response.data.data)
                    this.productOptions = response.data.data;
                });
            },
            products: function() {
                this.productOptions = [];
                axios.get("{{route('api.products')}}")
                .then((response) => {
                    console.log(response.data.data)
                    this.productOptions = response.data.data;
                });
            },
            getFBTDetails: function() {
                this.productOptions = [];
                axios.get("{{route('api.fbt_details',$fbtId)}}")
                .then((response) => {
                    console.log(response.data)
                    this.name = response.data.fbt.name;
                    this.product_items = response.data.product_items;
                });
            },
            updateFBT: function() {
                
                console.log('update set of FBT')      
                axios.post("{{route('fbt.update',$fbtId)}}", {
                    name: this.name,
                    products: this.product_items,
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                }).then((response) => {
                    console.log(response)
                    swal("Success!",  response.data.message, "success");
                    //reset all data in form
                   // this.resetFormData();
                }).catch(error => {
                    console.log(error.response.data.status)
                    console.log(error.response.data.errors)
                    var errors = [];
                    $.each( error.response.data.errors, function( index, error ){
                        errors.push(error.message)
                    });
                    swal("Failed!",  JSON.stringify(errors.toString()), "info");
                });
            },
            resetFormData: function() {
                this.productOptions =  [];
                this.name =  '';
                this.product_items =  '';
            }
        }
    });
</script>
<style type="text/css" ></style>
@endsection
