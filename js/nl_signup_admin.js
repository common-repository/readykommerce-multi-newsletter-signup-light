( function( $ ){

    // multiple image uploader
    // -----------------------
    jQuery('.upload_data').click(function() {
        targetfield = jQuery(this).prev('.upload_link');
        // console.log(targetfield);
        tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        return false;
    });

    window.send_to_editor = function(html) {
        // return false;
        fileurl = jQuery(html).attr('href');
        jQuery(targetfield).val(fileurl);
        var id = jQuery(targetfield).attr('id');
        // console.log(html);
        // console.log(fileurl);
        // console.log(targetfield);
        // console.log(id);
        //preview(targetfield);
        tb_remove();
    }

    jQuery('.nl-help-text').click(function(){
        jQuery(this).parent('.nl-help-wrap').find('.nl-help-data').fadeToggle('400');
    });

} )( jQuery );