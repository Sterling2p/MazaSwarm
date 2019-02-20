(function( $ ) {
    
    // Toast notification:
    $.toast = function( opts )
    {
        let options = $.extend({
            content: '',
            location: 'bottom',
            timeout: 3500
        }, opts);
        
        switch( options.location )
        {
            case 'bottom':
            default:
                var containerID = 'toast-container-bottom';
                break;
            
            case 'top':
                var containerID = 'toast-container-top';
                break;
        }
        
        // Container doesn't exist, we need to add it:
        if( ! $('#'+containerID).length )
        {
            $('<div />', { id: containerID, class: 'toast-container' }).appendTo('body');
        }
        
        var tpl = '<div class="toast-notification"><span class="toast-notification-text"></span></div>';
        
        // Make the notification:
        var notificationID = 'toast'+(Math.random().toString(36).substr(2, 10)),
            $notification = $(tpl).attr('id', notificationID),
            $container    = $('#'+containerID);
            
        $notification.find('.toast-notification-text').html( options.content );
        
        // Set the timeout:
        var timer = window.setTimeout(function() { $('#'+notificationID).removeClass('in'); }, options.timeout);
        
        $notification.on('click', '.toast-notification-close', function() {
                        $(this).parent().removeClass('in')
                    })
                     .hover(function() { window.clearTimeout(timer); },
                            function() { timer = window.setTimeout(function() { $('#'+notificationID).removeClass('in'); }, options.timeout); }
                     )
                     .on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function() {
                        
                        $(this).remove();
                        
                        // No other children, remove the container:
                        if( !$(this).siblings().length )
                        {
                            $(this).parent().remove();
                        }
                    });
        
        // If there's already one, remove it:
        $container.empty();
        
        $notification.appendTo( $container ).addClass('in');
    }
    
    // Growl-type notifications:
    $.growl = function( opts )
    {
        let options = $.extend({
            theme: '',
            content: '',
            location: 'tr',
            timeout: 5000,
            title: '',
        }, opts);
        
        let tpl = '<div class="growl-notification">\
                       <a class="growl-notification-close">&times;</a>\
                       <div class="growl-notification-title"></div>\
                       <div class="growl-notification-body"></div>\
                   </div>';
        
        switch( options.location )
        {
            case 'tr':
            default:
                var containerID = 'growl-container-tr';
                break;
            
            case 'br':
                var containerID = 'growl-container-br';
                break;
            
            case 'bl':
                var containerID = 'growl-container-bl';
                break;
            
            case 'tl':
                var containerID = 'growl-container-tl';
                break;
        }
        
        // Container doesn't exist, we need to add it:
        if( ! $('#'+containerID).length )
        {
            $('<div />', { id: containerID, class: 'growl-container' }).appendTo('body');
        }
        
        // Make the notification:
        var notificationID = 'growl'+(Math.random().toString(36).substr(2, 10)),
            $notification = $(tpl).attr('id', notificationID),
            $container    = $('#'+containerID);
        
        if( options.title == '' || options.title == null )
        {
            $notification.find('.growl-notification-title').remove();
        }
        else
        {
            $notification.find('.growl-notification-title').html( options.title );
        }
        
        if( options.content == '' || options.content == null )
        {
            $notification.find('.growl-notification-body').remove();
        }
        else
        {
            $notification.find('.growl-notification-body').html( options.content );
        }
        
        if( options.theme != '' )
        {
            $notification.addClass('growl-notification-'+options.theme);
        }
        
        // Set the timeout:
        var timer = window.setTimeout(function() { $('#'+notificationID).removeClass('in'); }, options.timeout);
        
        $notification.on('click', '.growl-notification-close', function() {
                        $(this).parent().removeClass('in')
                    })
                     .hover(function() { window.clearTimeout(timer); },
                            function() { timer = window.setTimeout(function() { $('#'+notificationID).removeClass('in'); }, options.timeout); }
                     )
                     .on('webkitTransitionEnd otransitionend oTransitionEnd msTransitionEnd transitionend', function() {
                        
                        $(this).remove();
                        
                        // No other children, remove the container:
                        if( !$(this).siblings().length )
                        {
                            $(this).parent().remove();
                        }
                    });
        
        $notification.addClass(options.className).prependTo( $container ).addClass('in');
        
        
    }
    
} (jQuery));