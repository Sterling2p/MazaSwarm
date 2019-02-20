// Hook up the tabs:
$(function() {
    
    $('.js__tabs .nav-tabs__item-link').on('click', function(e) {
        
        if( ! $(this).parent().hasClass('active') )
        {
            var $target = $( $(this).attr('href') );
            
            if( $target.length )
            {
                e.preventDefault();
                $(this).parent('.nav-tabs__item').addClass('active').siblings().removeClass('active');
                
                // Display it:
                $(this).parents('.tab-container').find('.tab-contents > .active').removeClass('active');
                $target.addClass('active');
            }
        }
    });
    
});