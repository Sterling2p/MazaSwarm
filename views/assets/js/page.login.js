// Login page JS:
$(function() {
    
    //loadQuote();
    $('.login-field input').on('focus', function() {
        
        $(this).parent().addClass('focus');
        
    }).on('blur', function() {
        
        if( $.trim($(this).val()) == '' )
        {
            $(this).val('');
            $(this).parent().removeClass('focus');
        }
        else
        {
            $(this).parent().removeClass('focus').addClass('has-value');
        }
        
    });
});
