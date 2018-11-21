@extends('layouts.app')
@section('content')

    <div class="content">
        <div class="container-fluid">
            <!-- Page-Title -->
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-title-box">
                        <h4 class="page-title"></i>Edit Category</h4>
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item"><a href="{{route('categories.index')}}">Categories</a></li>
                            <li class="breadcrumb-item active">Create</li>
                        </ol>
                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
            <template>
                <!-- //Main content page. -->
                <div class='col-lg-6 col-lg-offset-6'>
                    <div class="row">
                        <div class="col-lg-6">
                            <label>Upload New Image</label>
                                <form action="{{route('api.upload_image.global')}}" accept-charset="UTF-8" id="real-dropzone" enctype="multipart/form-data" class="dropzone dz-clickable">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="hidden" name="savePath" value="/storage/categories">
                                    <input type="hidden" id="path" name="path" v-model="path">
                                    <input type="hidden" id="file_name" name="file_name" v-model="file_name">
                                    <div class="dz-message">
                                    </div> 
                                        <h4 style="text-align: center; color: rgb(66, 139, 202);">Drop images in this area  
                                            <span class="glyphicon glyphicon-hand-down"></span>
                                        </h4>
                                </form>    
                            <!-- end row -->
                        </div>
                        <div class="col-lg-6">
                            @if($childSubCategory->file_name) 
                                <label>Category</label>
                                <img src="{{url($childSubCategory->path.'/'.$childSubCategory->file_name)}}" class="rounded-square img-thumbnail">
                            @else
                                <label>No image available</label>
                            @endif
                        </div>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="name">Category Title<span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title"  value="{{$childSubCategory->title}}" parsley-trigger="change" required="required" placeholder="Name" class="form-control" id="name">
                    </div>
                    <div class="form-group">
                        <label for="name">Description</label>
                        <textarea id="description" name="description"  placeholder="Description" class="form-control" style="margin-top: 0px; margin-bottom: 0px; height: 200px;">{{$childSubCategory->description}}</textarea>
                    </div>
                    <input type="button" @click="updateCategories()" class="btn btn-info" value="Submit">
                </div>
            </template>

                <!-- End main content page -->
        </div>
            <!-- end container -->
    </div>
@endsection
@section('bottom_scripts')
@include('includes.vue-scripts')
<!-- Include the Quill library -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<!-- Include stylesheet -->
<script src="{{url('assets/js/dropzone.js')}}"></script>
<link href="{{url('assets/css/basic.css')}}" rel="stylesheet" type="text/css">
<link href="{{url('assets/css/dropzone.css')}}" rel="stylesheet" type="text/css">
<script>
 
    new Vue({
    el: '.content',
    data: {
        journal_category_id: '',
        content: '',
        file_name: '',
        path: ''
    },
    methods: {
        updateCategories() {
            axios.post("{{route('api.category.update', $childSubCategory->id)}}", { 
                    content : this.content,
                    image : $("#file_name").val(),
                    path : $("#path").val(),
                    title : $("#title").val(),
                    description : $("#description").val(),
                }).then((response) => {
                console.log(response)
                swal("Success!",  response.data.message, "success");
                setTimeout(function(){
                   window.location.reload(1);
                }, 3000);
                //reset all data in form
            }).catch(error => {
                console.log(error.response.data.errors)
                swal("Failed!",'Category title is required', "info");
            });
        }
    },
    computed: {
        },
        mounted() {
        }
    })

//for about
Dropzone.options.realDropzone = {
    acceptedFiles: 'image/*, image/jpeg, image/png, image/jpg',
    uploadMultiple: false,
    parallelUploads: 10,
    maxFilesize: 2,
    maxFiles: 1,
    // previewTemplate: document.querySelector('#preview-template').innerHTML,
    addRemoveLinks: false,
    dictRemoveFile: 'Remove',
    dictFileTooBig: 'Image is bigger than 8MB',
    createImageThumbnails: true,

    // The setting up of the dropzone
    init:function() {
        this.on('success', function(file){
           // this.removeFile(file);
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
        console.log(file);
        console.log(response.path);
        $("#path").attr("value",response.path);
        $("#file_name").attr("value",response.file_name);
    }
}
</script>
@endsection

