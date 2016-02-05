(function() {
"use strict";   
	
	var rs_val = [];
	
	for(var i in revslider_shortcodes){
		rs_val[i] = {text: revslider_shortcodes[i], onclick : function() {
			tinymce.execCommand('mceInsertContent', false, revslider_shortcodes[i]);
		}};
	}
	
	tinymce.PluginManager.add( 'revslider', function( editor, url ) {

		editor.addButton( 'revslider', {
			type: 'listbox',
			text: 'RevSlider',
			icon: false,
			onselect: function(e) {
			}, 
			values: rs_val
 
		});
	});
 
})();