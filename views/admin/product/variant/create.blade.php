@extends('layouts.app')
@section('content')

    <!-- Start content -->
    <div class="content">
            <div class="container-fluid">
                <!-- Page-Title -->
                <div class="row">
                    <div class="col-sm-12">
                        <div class="page-title-box">
                            <h4 class="page-title"></i>Add Product Variant</h4>
                            <ol class="breadcrumb float-right">
                                <a href="{{route('product.edit', $product->id)}}">
                                    <li class="breadcrumb-item"> Product : "{{$product->name}}" /</li>
                                </a>
                                <li class="breadcrumb-item">&emsp;Variant</li>
                                <li class="breadcrumb-item active">Add</li>
                            </ol>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <template>
                    <div class="row">
                       <div class="col-lg-6">
                            <div class="card-box">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <p class="text-muted font-14 m-b-20">
                                            Legend  (*) required fields.
                                        </p>
                                    </div>
                                    <div class="col-lg-6">
                                       <form action="{{route('api.product.upload_image')}}" accept-charset="UTF-8" id="real-dropzone" enctype="multipart/form-data" class="dropzone dz-clickable">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="hidden" name="product_id" value="{{$product->id}}">
                                             <div class="dropzone-previews" id="dropzonePreview"></div>
                                            <div class="dz-message">
                                                
                                            </div> 
                                            <h4 style="text-align: center; color: rgb(66, 139, 202);">Drop images in this area  
                                                <span class="glyphicon glyphicon-hand-down"></span>
                                            </h4>
                                        </form>
                                        <span id="ShowVariantImage" @click="checkuploads()" class="btn btn-icon waves-effect waves-light btn-primary  btn-block" style="display: none;"> <i class="fa fa-file-o"></i> Shows Uploaded Images </span>
                                    </div>
                                    
                                </div>
                                    <div class="form-group">
                                        <label for="name">Colors<span class="text-danger">*</span></label>
                                        <input type="color" name="color" parsley-trigger="change" required="required" v-model="color" @change="addEditColors(color)" class="form-control color" id="color">
                                    </div>
                                    <div class="form-group">
                                        <multiselect
                                            v-bind:id="color" 
                                            v-model="colors" 
                                            :options="colors"
                                            :multiple="true"
                                            track-by="name"
                                            :custom-label="customLabel"
                                            >
                                        </multiselect>
                                    </div>
                                    <div class="form-group">
                                        <label for="name">Inventory<span class="text-danger">*</span></label>
                                        <input type="number" name="inventory" parsley-trigger="change" required="required" v-model="inventory" placeholder="Stocks" class="form-control" id="inventory">
                                    </div>
                                    <div class="form-group text-right" style="padding-top: 100px">
                                        <button class="btn btn-primary waves-effect waves-light" type="submit"  v-on:click="updateVariantDetails()">
                                            Save
                                        </button>
                                    </div>
                            </div> <!-- end card-box -->
                        </div>
                        <div class="col-lg-6">
                            <label>Variant images</label>
                            <div class="row">
                                <div  class="col-lg-3" v-if="images" v-for="image in images">
                                    <div class="card m-b-20">
                                        <img  :src="image.url_path" class="rounded-square img-thumbnail">
                                        <div class="card-body">
                                            <button class="btn btn-icon waves-effect waves-light btn-danger  btn-block"> <i class="fa fa-remove"></i> </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
<script src="{{url('assets/js/dropzone.js')}}"></script>
<link href="{{url('assets/css/basic.css')}}" rel="stylesheet" type="text/css">
<link href="{{url('assets/css/dropzone.css')}}" rel="stylesheet" type="text/css">
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script src="{{url('assets/js/vue-multiselect.min.js')}}"></script>

<script>
    var vue = new Vue({
        el: '.content',
        mounted() {
           
        },
        components: {
            Multiselect: window.VueMultiselect.default
        },
        data: {
            inventory : '',
            color : '',
            showDropZone: false,
            image_default : '',
            colors: [],
            images: [],
            selectedColors: [],
            header_accept : 'application/json',
            header_authorization : 'Bearer {{session("token")}}',
        },
        methods: {
            updateVariantDetails: function() {
                //call variant images added
                this.checkuploads();
                axios.post('{{route('api.add_variant', $product->id)}}', {
                    colors: this.colors,
                    inventory: this.inventory,
                    images: this.images
                },
                {
                    headers: {
                        'Authorization': this.header_authorization,
                        'Accept': this.header_accept
                    }
                }).then((response) => {
                    // console.log(response.data)
                    swal({ title: 'Success!', text: response.data.message, type: 'success', confirmButtonText: 'Ok'});
                    setTimeout(function(){
                       window.location.reload(1);
                    }, 2000);
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
            },
            checkuploads(){
                // console.log(variantImages)
                this.images = variantImages;
            },
            deleteImage:function ($productId, $variantId) {
                // api.delete_variant_image
            },
            customLabel: function (color) {
              return `${color}`
            },
            addEditColors (color) {
               this.colors.push(color);
            }
        }
    });
//for product
var variantImages = [];
Dropzone.options.realDropzone = {

    acceptedFiles: 'image/*, image/jpeg, image/png, image/jpg',
    uploadMultiple: false,
    parallelUploads: 10,
    maxFilesize: 2,
    maxFiles: 1,
    // previewsContainer: '#dropzonePreview',
    // previewTemplate: document.querySelector('#preview-template').innerHTML,
    addRemoveLinks: true,
    dictRemoveFile: 'Remove',
    dictFileTooBig: 'Image is bigger than 8MB',
    createImageThumbnails: true,

    // The setting up of the dropzone
    init:function() {
        // Add server images
        this.on('success', function(file){
           this.removeFile(file);
        })
        this.on('error', function(file){
            swal({
             title: 'Error!',
             text: 'Uploading "' + file.name + '" encountered a problem, Please select valid image file type from : (jpeg, png, jpg)',
             type: 'error',
             confirmButtonText: 'Ok'
           })
        })
        this.on('addedfile', function(file) {
            if (this.files.length > 1) {
              this.removeFile(this.files[0]);
            }
       });
    },
    error: function(file, response) {
        this.removeFile(file);
        if ($.type(response) === "string")
            var message = response; //dropzone sends it's own error messages in string
        else
            var message = response.message;
         file.previewElement.classList.add("dz-error");
         _ref = file.previewElement.querySelectorAll("[data-dz-errormessage]");
         _results = [];
        for (_i = 0, _len = _ref.length; _i < _len; _i++) {
             node = _ref[_i];
             _results.push(node.textContent = message);
        }
        return _results;
    },
    success: function(file,response) {
        // alert('Upload success!')
        $('#ShowVariantImage').show();
        swal({ title: 'Success!', text: 'Successfully uploaded.', type: 'success',confirmButtonText: 'Ok'});

        variantImages.push({'path': response.path, 'file_name': response.file_name, 'url_path': response.url_path});
        console.log(variantImages)
    }
}
</script>
<style type="text/css">
    .color {
        height:40px;
    }
</style>@endsection
