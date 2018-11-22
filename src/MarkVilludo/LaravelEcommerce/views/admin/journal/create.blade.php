@extends('layouts.app')
@section('content')
<!-- Start content -->
<div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">
            <div class="page-title-box">
                <h4 class="page-title">Add Journal</h4>
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item"><a href="{{route('journals.index')}}">Journals</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
                <div class="clearfix"></div>
            </div>
        </div>
      </div>
    <template>
        <div class="row">
           <div class="col-lg-8">
                <div class="form-group row">
                    <div class="col-lg-8">
                        <label for="name">Categories<span class="text-danger">*</span></label>
                        <select v-model="journal_category_id" class="form-control" required="required">
                          <option v-for="category in categories" v-bind:value="category.id">    
                            <p>@{{ category.name }}</p>
                          </option>
                        </select>
                    </div>
                    <div class="col-lg-4 mt-4 pt-2">
                        <button class="btn btn-primary btn-sm" @click="addCategory()"><i class="fa fa-plus">Add new category</i> </button>
                    </div>
                </div>
                <div class="form-group row" v-if="viewAddCategory">
                    <div class="col-lg-8">
                        <label for="name">Name<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" v-model="journal_category" name="journal_category">
                    </div>
                    <div class="col-lg-4 mt-4 pt-2">
                        <button class="btn btn-success btn-sm" @click="onSaveJournalCategory()"><i class="fa fa-save"> Save</i> </button>
                    </div>
                </div>
                <div class="card-box">
                    <p>
                      <label for="name">Title<span class="text-danger">*</span></label>
                      <input type="text" name="title" v-model="title" class="form-control">
                    </p>
                    <p>
                        <label for="name">Featured image
                            <small>
                               <span class="text-danger">*</span>
                                min size of (565x376) and max size of (1024x683)
                            </small>
                        </label>
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
                    <quill-editor v-model="content"
                      ref="quillEditorA"
                      :options="editorOption"
                      @blur="onEditorBlur($event)"
                      @focus="onEditorFocus($event)"
                      @ready="onEditorReady($event)">
                    </quill-editor>
                    <br>
                    <form action="{{route('api.upload_image.global')}}" accept-charset="UTF-8" id="real-dropzone" enctype="multipart/form-data" class="dropzone dz-clickable">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="savePath" value="/storage/journals">
                            <input type="hidden" id="path" name="path" >
                            <input type="hidden" id="file_name" name="file_name">
                            <div class="dz-message">
                            </div> 
                            <h4 style="text-align: center; color: rgb(66, 139, 202);">Drop images in this area  
                                <span class="glyphicon glyphicon-hand-down"></span>
                            </h4>
                     </form>    
                </div>
            </div>
        </div>
        <button class="btn btn-primary" @click="onSaveJournal()"> Save </button>
    </template>

    </div>
</div>
<!-- end content -->
@endsection
@section('bottom_scripts')

@include('includes.vue-scripts')
<!-- Include the Quill library -->
<script src="{{url('assets/js/sweetalert2.all.min.js')}}"></script>
<script src="{{url('assets/js/quill.js')}}"></script>
<!-- Quill JS Vue -->
<script src="{{url('assets/js/vue-quill-editor.js')}}"></script>
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

    //for journal images
    var journalSliderImages = [];

    Vue.use(VueQuillEditor)
    new Vue({
    el: '.content',
    data: {
        title: '',
        categories: [],
        journal_category_id: '',
        content: '',
        viewAddCategory: false,
        journal_category: '',
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
        getJournalCategories(){
            axios.get("{{route('api.journal.categories')}}").then((response) => {
                // console.log(response.data.content)
                this.categories = response.data.data;
             
            }).catch(error => {
                console.log(error.response)
            });
        },
        addCategory(){
            if (this.viewAddCategory == false) {
                this.viewAddCategory = true;
            } else {
                this.viewAddCategory = false;
            }
        },
        onSaveJournalCategory() {
            axios.post("{{route('api.store.journal_categories')}}", { 
                    name : this.journal_category,
                }).then((response) => {
                console.log(response)
                swal("Success!",  response.data.message, "success");
                
                this.getJournalCategories();
                this.viewAddCategory = false;
                //reset all data in form
            }).catch(error => {
                console.log(error.response.data.errors)
                var errors = [];
                $.each( error.response.data.errors, function( index, error ){
                    errors.push(error.message)
                });
                swal("Failed!",  JSON.stringify(errors.toString()), "info");
            });
        },
        onSaveJournal() {
            console.log(this.content)
            axios.post("{{route('api.journal.store')}}", { 
                    content : this.content,
                    title : this.title,
                    journal_category_id : this.journal_category_id,
                    image : $("#file_name_journal").val(),
                    path : $("#path_journal").val(),
                    sliders : journalSliderImages,
                }).then((response) => {
                console.log(response)
                swal("Success!",  response.data.message, "success");
                setTimeout(function(){
                   window.location.reload(1);
                }, 3000);
                //reset all data in form
            }).catch(error => {
                console.log(error.response.data.status)
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
          editorA() {
            return this.$refs.quillEditorA.quill
          }
        },
        mounted() {
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
            // if (this.files.length > 1) {
            //   this.removeFile(this.files[0]);
            // }
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

        journalSliderImages.push({'path': response.path, 'file_name': response.file_name});
        console.log(journalSliderImages)
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
