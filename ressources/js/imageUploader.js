jQuery(document).ready(function($){
    $('.vehiclePhoto').click(function(e) {
        e.preventDefault();
        var $baliseImage = $(this);
        var image = wp.media({
            title: 'Choissisez une photo',
            multiple: false
        }).open()
        .on('select', function(e){
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            $baliseImage.attr('src',image_url);
            $baliseImage.next().next().val(image_url);
        });
    });

    $('.deleteVehiclePhoto').click(function(e){
        e.preventDefault();
        $(this).next().val('');
        $(this).prev().attr('src', util.pluginUrl + 'ressources/img/noVehicleImage.png');
        $(this).addClass('hidden');
    });

    $('.vehiclePhoto').hover(function(e){
        if($(this).attr('src') !== util.pluginUrl + 'ressources/img/noVehicleImage.png')
        {
            $('.deleteVehiclePhoto').addClass('hidden');
            $(this).next().removeClass('hidden');
        }
    });
});
