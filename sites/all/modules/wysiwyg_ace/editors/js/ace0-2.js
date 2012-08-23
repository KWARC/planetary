(function($) {

/**
 * Attach this editor to a target element.
 */
Drupal.wysiwyg.editor.attach.ace = function(context, params, settings) {
  // Attach editor.
  var editorID = "#"+params.field;
  var mode = "";
  var toolbardiv, editordiv, editorwrapper;
  
  cSettings = {
  	"mode" : "latex",
  	"ShareJS" : false,
  };
  
  if (typeof settings["enabled"]!="undefined") {
  	for (var i=0; i<settings["enabled"].length; ++i) {
  	  t = settings["enabled"][i].split("_");
  	  cSettings[t[0]]=t[1];
  	}
  }
  
  $(editorID).each(function (c, obj) {
  	  jQuery(obj).hide();
  	  
  	  toolbardiv = jQuery("<div>").attr("id","ace_toolbar_"+params.field).addClass("ui-widget-header ui-corner-all");
  	  editordiv = jQuery("<div>").attr("id","ace_"+params.field).attr("style"," height:200px; position:relative");
	  editorwrapper = jQuery("<div>").addClass("ace_wrapper").append(toolbardiv).append(editordiv);
  	  
  	  jQuery(obj).after(editorwrapper);
  	  var editor = ace.edit("ace_"+params.field);
  	  editor.getSession().setValue(obj.value);
	  editor.setTheme("ace/theme/twilight");
	  editor.getSession().setMode("ace/mode/"+cSettings["mode"]);
	  if (cSettings["ShareJS"]) {
	  	  async.waterfall([
	  	  		  function (callback) {
	  	  		  	  Drupal.ShareJS.connectServices("test-doc", "ace", editor, editor.getSession().getValue(), callback)
	  	  		  },
	  	  		  function (conn, callback) {	
	  	  		  	  conn.initToolbar(toolbardiv);
	  	  		  	  callback(null);
	  	  		  }
	  	  ]);
	  }
	  jQuery.data(obj, 'editor', editor);
  });
};

/**
 * Detach a single or all editors.
 *
 * See Drupal.wysiwyg.editor.detach.none() for a full desciption of this hook.
 */
Drupal.wysiwyg.editor.detach.ace = function(context, params) {
  if (typeof params != 'undefined') {
    var editorID = "#"+params.field;
    $(editorID).each(function (c, obj) {
    	var editor = jQuery.data(obj, 'editor');
    	if (editor != null) {
    		obj.value = editor.getSession().getValue()  
        	jQuery.data(obj, 'editor', null);
        	jQuery("#ace_"+params.field).remove();
        }
    	jQuery(obj).show();
    });
  }
};

})(jQuery);