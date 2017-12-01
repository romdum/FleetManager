jQuery(document).ready(function($){
	$('select[name=FM_brand]').change(function(){
		let parentSlug = $('select[name=FM_brand] option:selected').val();
		$.post({
	        url     : displayModelUtil.ajaxurl,
	        dataType: "json",
	        data    : {
	            action  : "displayModels",
	            nonce: displayModelUtil.nonce,
	            parentSlug : parentSlug,
	        },
	        success : function (response)
	        {
	            $('select[name=FM_model] > option').remove();
	        	$('select[name=FM_model]').append('<option value="none">Non renseigné(e)</option>');
	            $.each(response, function(slug, model){
	            	$('select[name=FM_model]').append('<option value="' + slug + '">' + model + '</option>');
	            });
	        },
	        error   : function (response)
	        {
	            console.log('error :');
	            console.log(response);
	        }
	    });
	});
});