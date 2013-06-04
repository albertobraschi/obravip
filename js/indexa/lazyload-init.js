jQuery.noConflict();
jQuery(document).ready(function(){   
    jQuery('.products-grid, .products-list').find('img').each(
        function(){
            if( jQuery(this).attr('data-original') ){
                jQuery(this).lazyload({
                    effect       : "fadeIn"
                });
            }
        }
    );
});
