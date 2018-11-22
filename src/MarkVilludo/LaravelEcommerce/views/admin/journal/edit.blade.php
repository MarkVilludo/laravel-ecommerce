@extends('layouts.app')
@section('content')
<!-- Start content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="page-title-box">
                    <h4 class="page-title">Edit Journal</h4>
                    <ol class="breadcrumb float-right">
                        <li class="breadcrumb-item"><a href="{{route('journals.index')}}">Journals</a></li>
                        <li class="breadcrumb-item active">Edit</li>
                    </ol>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
        <template>
            <div class="row">
               <div class="col-lg-8">
                    <div class="form-group">
                        <label for="name">Categories<span class="text-danger">*</span></label>
                        <select v-model="journal_category_id" class="form-control" required="required">
                          <option v-for="category in categories" v-bind:value="category.id" :selected="category.id==journal_category_id? true : false">
                            <p>@{{ category.name }}</p>
                          </option>
                        </select>
                    </div>
                    <div class="card-box">
                        <p>
                            <label for="name">Title<span class="text-danger">*</span></label>
                            <input type="text" name="title" v-model="title" class="form-control">
                        </p>
                        <p>
                            <label for="name">Update Featured image</label>
                            <small>
                                min size of (565x376) and max size of (1024x683)
                            </small>
                            <form action="{{route('api.upload_image_validate.global')}}" accept-charset="UTF-8" id="real-dropzone-featured" enctype="multipart/form-data" class="dropzone dz-clickable">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="savePath" value="/storage/journals">
                                <input type="hidden" id="path_journal" name="path" >
                                <input type="hidden" id="file_name_journal" name="file_name">
                                <div class="dz-message">
                                </div> 
                                <h4 style="text-align: center; color: rgb(66, 139, 202);">Drop images in this area  
                                    <span class="glyphicon glyphicon-hand-down"></span>
                                </h4>
                             </form>    
                        </p>
                        <div class="card m-b-20 col-lg-3">
                            <label>Featured Image</label>
                            <img  :src="journal.featured_image" class="rounded-square img-thumbnail">
                        </div>
                        <quill-editor v-model="content"
                            ref="quillEditorA"
                            :options="editorOption"
                            @blur="onEditorBlur($event)"
                            @focus="onEditorFocus($event)"
                            @ready="onEditorReady($event)">
                        </quill-editor>
                        <br>
                        <h4>Upload more images</h4>
                        <form action="{{route('api.upload_image.global')}}" accept-charset="UTF-8" id="real-dropzone" enctype="multipart/form-data" class="dropzone dz-clickable">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="savePath" value="/storage/journals">
                            <input type="hidden" id="path" name="path" >
                            <input type="hidden" id="file_name" name="file_name">
                            <input type="hidden" name="journal_id" value="{{$journalId}}">
                            <input type="hidden" name="store_journal" value="1">
                            <div class="dz-message">
                            </div> 
                            <h4 style="text-align: center; color: rgb(66, 139, 202);">Drop images in this area  
                                <span class="glyphicon glyphicon-hand-down"></span>
                            </h4>
                        </form>    

                        <div class="col-lg-12">
                            <h4>Slider images</h4>
                            <div class="row">
                                <div  class="col-lg-3" v-if="images" v-for="image in images">
                                    <div class="card m-b-20">
                                        <img  :src="image.original_path" class="rounded-square img-thumbnail">
                                        <div class="card-body">
                                            <button class="btn btn-icon waves-effect waves-light btn-danger  btn-block" @click="removeSelectedImage(image)"> <i class="fa fa-remove"></i> </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary" @click="onUpdateJournal()"> Update </button>
        </template>
    </div>
</div>
<!-- end content -->
@endsection
@section('bottom_scripts')

@include('includes.vue-scripts')
<!-- Include the Quill library -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script src="https://cdn.quilljs.com/1.3.4/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue"></script>
<!-- Quill JS Vue -->
<script src="https://cdn.jsdelivr.net/npm/vue-quill-editor@3.0.4/dist/vue-quill-editor.js"></script>
<!-- Include stylesheet -->
<link href="https://cdn.quilljs.com/1.3.4/quill.core.css" rel="stylesheet">
<link href="https://cdn.quilljs.com/1.3.4/quill.snow.css" rel="stylesheet">
<link href="https://cdn.quilljs.com/1.3.4/quill.bubble.css" rel="stylesheet">
<!-- for dropzone -->
<!-- Include stylesheet -->
<script src="{{url('assets/js/dropzone.js')}}"></script>
<link href="{{url('assets/css/basic.css')}}" rel="stylesheet" type="text/css">
<link href="{{url('assets/css/dropzone.css')}}" rel="stylesheet" type="text/css">
<script>
    Vue.use(VueQuillEditor)
    new Vue({
    el: '.content',
    data: {
        title: 'Title Here..',
        categories: [],
        images: [],
        journal: '',
        journal_category_id: '',
        content: 'Write something..',
        editorOption: {
            theme: 'snow'
        }
    },
    components: {
        LocalQuillEditor: VueQuillEditor.quillEditor
    },
    methods: {
        onEditorBlur(quill) {
            console.log('editor blur!', quill)
        },
        onEditorFocus(quill) {
            console.log('editor focus!', quill)
        },
        onEditorReady(quill) {
            console.log('editor ready!', quill)
        },
        getJournalDetails(){
            axios.get("{{route('api.journal.show', $journalId)}}").then((response) => {
                // console.log(response.data.content)
                this.content = response.data.journal.content;
                this.journal = response.data.journal;
                this.title = response.data.journal.title;
                this.journal_category_id = response.data.journal.journal_category_id;
                this.images = response.data.journal.sliders;
                // swal("Success!",  response.data.message, "success");
                //reset all data in form
            }).catch(error => {
                console.log(error.response.data.status)
                // console.log()
                // swal("Failed!",  JSON.stringify(error.response.data.errors), "info");
            });
        },
        getJournalCategories(){
            axios.get("{{route('api.journal.categories')}}").then((response) => {
                // console.log(response.data.content)
                this.categories = response.data.data;
             
            }).catch(error => {
                console.log(error.response)
            });
        },
        onUpdateJournal() {
            console.log(this.content)
            axios.post("{{route('api.journal.update', $journalId)}}", { 
                    content : this.content,
                    title : this.title,
                    image : $("#file_name_journal").val(),
                    path : $("#path_journal").val(),
                    journal_category_id : this.journal_category_id

                }).then((response) => {
                console.log(response)
                swal("Success!",  response.data.message, "success");
                setTimeout(function(){
                   window.location.reload(1);
                }, 3000);
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
        removeSelectedImage(image) {
            
            swal({
                title: 'Remove image',
                text: "Are you sure you want to remove this image?",
                type: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3c8dbc',
                cancelButtonColor: '#3c8dbc',
                confirmButtonText: 'Yes, Please!',
                cancelButtonText: 'No, cancel!',
                confirmButtonClass: 'btn btn-info',
                cancelButtonClass: 'btn btn-grey',
                buttonsStyling: false
            }).then(function (result) {
                if (result.value) {
                    console.log(image)
                     axios.delete("{{url('/api/v1/journal_sliders')}}/"+image.id, {
                        headers: {
                            'Authorization': this.header_authorization,
                            'Accept': this.header_accept
                        }
                    })
                    .then((response) => {
                        swal("Success!", response.data.data.message, "success");
                        console.log(response.data)
                        setTimeout(function(){
                           window.location.reload(1);
                        }, 3000);
                    })
                    .catch(function (response) {
                        //handle error
                        console.log(response);
                         swal("Failed!", response.data.message, "error");
                    });
                }
            });
        }
    },
    computed: {
        editorA() {
                return this.$refs.quillEditorA.quill
            }
        },
        mounted() {
            this.getJournalDetails();
            this.getJournalCategories();
        }
    })
    //for featured image
    Dropzone.options.realDropzoneFeatured = {
        acceptedFiles: 'image/*, image/jpeg, image/png, image/jpg',
        // uploadMultiple: false,
        parallelUploads: 10,
        // maxFilesize: 2,
        // previewTemplate: document.querySelector('#preview-template').innerHTML,
        addRemoveLinks: true,
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
                    text: 'Uploading "' + file.name + '" encountered a problem, please select valid image file type from : (jpeg, png, jpg) and with min image size of (565x376) and max image size of (1024x683).',
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
            $("#path_journal").attr("value",response.path);
            $("#file_name_journal").attr("value",response.file_name);
        }
    }

    //for sliders
    Dropzone.options.realDropzone = {
        acceptedFiles: 'image/*, image/jpeg, image/png, image/jpg',
        // uploadMultiple: false,
        parallelUploads: 10,
        // maxFilesize: 2,
        // previewTemplate: document.querySelector('#preview-template').innerHTML,
        addRemoveLinks: true,
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
                swal({
                    title: 'Success!',
                    text: 'Uploaded additional image for slider',
                    type: 'success',
                    confirmButtonText: 'Ok'
                });

                setTimeout(function(){
                   window.location.reload(1);
                }, 3000);
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
<style type="text/css" >
    .quill-editor,
    .card-box {
      background-color: white;
    }
</style>
@endsection
