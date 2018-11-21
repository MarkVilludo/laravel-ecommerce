/*****************************
 * Dedicate $ as jQuery method
 */
(function( $ ) {
  /*****************************
   * Document Ready short code
   */
  $(function() {

     /*************************************
      * Remove Field
      * All forms on submit event listener
      * @return false
      */
     $("form :input").on("keyup, keydown, blur",function(){
        $(this).parent().removeClass('has-error');
        $(this).removeClass('has-error');
        $(this).siblings(".help-block").remove();
        $(this).siblings(".form-group.control").remove();
     });

    /******************************
     * Set error-msg container to empty
     * @return null
     */
    $.fn.emptyError = function(){
      // $(".alert-dismissable").children("a.close").click();
      $(this).find('.has-error').removeClass('has-error');
      $(this).find(".help-block").remove();
      $(this).find(".form-group.control").remove();
    };

    /***********************
     * Display overlay loading
     * and call jquery defined function disableForm
     * @param object
     * @return null
     */
    $.fn.showLoadingOverlay = function(param){
      $(this).siblings('div.loading-overlay').fadeIn(param.speed);
      $(document).find('div.gform-error-msg').delay(60000).fadeOut(200,function(){$(this).html('');});
      
      $(document).find('div.gform-confirm-msg').delay(60000).fadeOut(200,function(){$(this).html('');});

    };

    /*************************
     * Hide overlay loading
     * and call jquery defined function enableForm
     * @param object
     * @return null
     */
    $.fn.hideLoadingOverlay = function(param){
      $(this).siblings('div.loading-overlay').fadeOut(param.speed);
      //$(this).enableForm();
    };

    /***************************
     * Disable submit button
     * @return null
     */
    $.fn.disableForm = function(){
      $(this).find('button,input[type="submit"]').prop('disabled',true);
      $(this).find('input').prop('readonly',true);
    };

    /***************************
     * Enable submit button
     * @return null
     */
    $.fn.enableForm = function(){
      $(this).find('button,input[type="submit"],input').prop('disabled',false);
      $(this).find('input').prop('readonly',false);
    };
  });

  /**
   * Notify Addon definition as jQuery plugin
   * Adapted version to work with Bootstrap classes
   * More information http://getuikit.com/docs/addons_notify.html
   */

  (function($, window, document){

      var containers = {},
          messages   = {},

          notify     =  function(options){

              if ($.type(options) == 'string') {
                  options = { message: options };
              }

              if (arguments[1]) {
                  options = $.extend(options, $.type(arguments[1]) == 'string' ? {status:arguments[1]} : arguments[1]);
              }

              return (new Message(options)).show();
          },
          closeAll  = function(group, instantly){
              if(group) {
                  for(var id in messages) { if(group===messages[id].group) messages[id].close(instantly); }
              } else {
                  for(var id in messages) { messages[id].close(instantly); }
              }
          };

      var Message = function(options){

          var $this = this;

          this.options = $.extend({}, Message.defaults, options); 

          this.uuid    = "ID"+(new Date().getTime())+"RAND"+(Math.ceil(Math.random() * 100000));
          this.element = $([
              // @geedmo: alert-dismissable enables bs close icon
              '<div class="uk-notify-message alert-dismissable">',
                  '<a class="close">&times;</a>',
                  '<div>'+this.options.message+'</div>',
              '</div>'

          ].join('')).data("notifyMessage", this);

          // status
          if (this.options.status) {
              this.element.addClass('alert alert-'+this.options.status);
              this.currentstatus = this.options.status;

          }

          this.group = this.options.group;

          messages[this.uuid] = this;

          if(!containers[this.options.pos]) {
              containers[this.options.pos] = $('<div class="uk-notify uk-notify-'+this.options.pos+'"></div>').insertBefore('form').on("click", ".uk-notify-message", function(){
                  $(this).data("notifyMessage").close();
              });

          }
      };


      $.extend(Message.prototype, {

          uuid: false,
          element: false,
          timout: false,
          currentstatus: "",
          group: false,

          show: function() {

              if (this.element.is(":visible")) return;

              var $this = this;

              containers[this.options.pos].show().prepend(this.element);

              var marginbottom = parseInt(this.element.css("margin-bottom"), 10);

              this.element.css({"opacity":0, "margin-top": -1*this.element.outerHeight(), "margin-bottom":0}).animate({"opacity":1, "margin-top": 0, "margin-bottom":marginbottom}, function(){

                  if ($this.options.timeout) {
                      $(document).find('div.uk-notify-message').delay(500).fadeOut(200,function(){$(this).html('');});  
                      var closefn = function(){ $this.close(); };

                      $this.timeout = setTimeout(closefn, $this.options.timeout);

                      $this.element.hover(
                          function() { clearTimeout($this.timeout); },
                          function() { $this.timeout = setTimeout(closefn, $this.options.timeout);  }
                      );
                  }

              });

              return this;
          },

          close: function(instantly) {

              var $this    = this,
                  finalize = function(){
                      $this.element.remove();

                      if(!containers[$this.options.pos].children().length) {
                          containers[$this.options.pos].hide();
                      }

                      delete messages[$this.uuid];
                  };

              if(this.timeout) clearTimeout(this.timeout);

              if(instantly) {
                  finalize();
              } else {
                  this.element.animate({"opacity":0, "margin-top": -1* this.element.outerHeight(), "margin-bottom":0}, function(){
                      finalize();
                  });
              }
          },

          content: function(html){

              var container = this.element.find(">div");

              if(!html) {
                  return container.html();
              }

              container.html(html);

              return this;
          },

          status: function(status) {

              if(!status) {
                  return this.currentstatus;
              }

              this.element.removeClass('alert alert-'+this.currentstatus).addClass('alert alert-'+status);

              this.currentstatus = status;

              return this;
          }
      });

      Message.defaults = {
          message: "",
          status: "normal",
          timeout: 3000,
          group: null,
          pos: 'top-center'
      };


      $["notify"]          = notify;
      $["notify"].message  = Message;
      $["notify"].closeAll = closeAll;

      return notify;

  }(jQuery, window, document));
  /** Notify function end **/
  
})(jQuery);