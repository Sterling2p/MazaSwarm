(function( $ ) {
    
    $.fn.tagger = function()
    {
        this.each(function() {
            
            // Wrap it all:
            var $wrap     = $('<div class="tagger-container" />'),
                $original = $(this).hide();
                
                $form     = $('<div class="input-group">\
                                   <input type="text" class="form-control tag-value" />\
                                   <div class="input-group-append">\
                                       <button class="btn btn-primary">Add</button>\
                                   </div>\
                               </div>');
                
            $original.wrap( $wrap );
            
            var $container = $original.parent('.tagger-container');
            
            // Copy the placehold:
            $form.find('.form-control').prop('placeholder', $(this).prop('placeholder'));
            
            $container.data('input', $original);
            $container.append( $form );
            
            // Now insert the list:
            var $tagList = $('<ul class="tag-list mt-20" />');
            $tagList.insertAfter( $form );
            
            // Add it when they click the button:
            $form.find('button').on('click', function(e) {
                e.preventDefault();
                add_tag( $(this).parent().siblings('input'), $(this).parents('.tagger-container').data('input'), $(this).parents('.tagger-container').find('.tag-list') );
            });
            
            // Add it when they hit return:
            $form.find('input').on('keydown', function(e) {
                if( e.keyCode == 13 )
                {
                    add_tag( $(this), $(this).parents('.tagger-container').data('input'), $(this).parents('.tagger-container').find('.tag-list') );
                }
            });
            
            // If it's not empty onload:
            if( $.trim($original.val()) != '' )
            {
                var sep = $original.data('sep') ? $original.data('sep') : ',';
                
                $.trim( $original.val() ).split(sep).forEach(function(item) {
                    
                    var $item = $('<li class="tag-list-item"><span class="tag-list-item-text">'+item+'</span><a href="#" class="tag-list-item-remove">&times;</a></li>');
                        
                    // Allow the removing of a tag:
                    $item.find('.tag-list-item-remove').on('click', function(e) {
                        e.preventDefault();
                        remove_tag( $(this).parents('.tag-list-item'), $original );
                    });
                    
                    $item.appendTo( $original.parents('.tagger-container').find('.tag-list') );
                });
            }
            
            // Handle the form:
            function add_tag( $field, $input, $list )
            {
                var value = $.trim( $field.val() ),
                    sep   = $input.data('sep') ? $input.data('sep') : ',',
                    list  = ($input.val() == '') ? [] : $input.val().split( sep );
                    
                if( value != '' )
                {
                    // If it's not in the array, add it:
                    if( $.inArray(value, list) < 0 )
                    {
                        var $item = $('<li class="tag-list-item"><span class="tag-list-item-text">'+value+'</span><a href="#" class="tag-list-item-remove">&times;</a></li>');
                        
                        // Allow the removing of a tag:
                        $item.find('.tag-list-item-remove').on('click', function(e) {
                            e.preventDefault();
                            remove_tag( $(this).parents('.tag-list-item'), $input );
                        });
                        
                        $item.appendTo( $list );
                        
                        list.push( value );
                        $input.val( list.join( sep ) );
                    }
                    
                    $field.val('').focus();
                }
            }
            
            // Remove a tag:
            function remove_tag( $tag, $input )
            {
                var value = $tag.find('.tag-list-item-text').text(),
                    sep   = $input.data('sep') ? $input.data('sep') : ',',
                    arr   = $input.val().split( sep ),
                    index = $.inArray(value, arr);
                    
                // Remove it:
                arr.splice(index, 1);
                
                $input.val( arr.join(sep) );
                
                // Re-focus:
                $tag.parents('.tag-list').parents('.tagger-container').find('.tag-value').focus();
                
                // Remove the tag:
                $tag.remove();
            }
        });
        
        return this;
    }
 
}( jQuery ));