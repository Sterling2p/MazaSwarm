(function(window, undefined) {
    
    $(function() {
        
        // Enable tooltips:
        $('[data-toggle="tooltip"]').tooltip();
        
        // Enable popovers:
        $('[data-toggle="popover"]').popover();
        
        // Hookup token input:
        $('.tokeninput').tokenfield();
        
        // Hookup datepickers:
        $('.datepicker').datepicker();
        
        // WYSIWYG:
        $('.wysiwyg').each(function() {
            
            var height = $(this).data('height') ? $(this).data('height') : 250,
                name   = $(this).attr('name');
                
            // Insert a new textarea:
            var $textarea = $('<textarea name="'+$(this).attr('name')+'"></textarea>');
            
            $textarea.val($(this).html()).hide().insertAfter($(this));
            
            $(this).summernote({
                dialogsFade: true,
                height: height,
                placeholder: $(this).attr('placeholder'),
                toolbar: [
                    'bold',
                    'italic',
                    'underline',
                    'strikethrough',
                    'superscript',
                    'subscript',
                    'ol',
                    'ul',
                    'paragraph',
                    'picture',
                    'link',
                    'fullscreen'
                ],
                tooltip: false,
                callbacks: {
                    onChange: function(content, $editor)
                    {
                        $textarea.val( content );
                        console.log( content );
                    }
                }
            });
        });

        // Show the search in the masthead:
        $('#masthead .js__toggle-search').on('click', function(e) {
            e.preventDefault();
            
            $('.masthead-search').toggleClass('in');
            
            if( $('.masthead-search').hasClass('in') )
            {
                window.setTimeout(function() {
                    $('.masthead-search-input').trigger('focus');
                }, 200);
            }
            else
            {
                $('.masthead-search-input').blur();
            }
        });
        
        // Notification dropdown:
        $('.notification-dropdown [data-toggle="dropdown"]').on('click', function(e) {
            $(this).parent().toggleClass('dropdown-in');
            return false;
        });
        
        $(document).on('click', function() { $('.notification-dropdown').removeClass('dropdown-in') });
        
        // Sidebar reveal:
        $('.masthead-sidebar-reveal').on('touchstart mousedown', function(e) {
            e.preventDefault();
            $('body').toggleClass('sidebar-in');
            $(this).toggleClass('active')
        });
        
        // Handle sidebar:
        $('#sidebar .sidebar-nav__item-has-children .sidebar-subnav').on('click', function(e) { e.stopPropagation() });
        
        $('#sidebar .sidebar-nav__item-has-children > a').on('click', function(e) {
            
            if( $(this).attr('href') == '#' )
            {
                e.preventDefault();
            }
            
            $('#sidebar .child-in').not( $(this).parent() ).removeClass('child-in');
            $(this).parent().toggleClass('child-in');
            
        });
        
        // Confirm click:
        $('body').on('click', '.action-confirm', function(e) {
            
            var noun = $(this).data('noun'),
                verb = $(this).data('verb');
            
            return confirm( 'Are you sure you want to '+verb.toLowerCase()+' that '+noun+'? This action is irreversible!\nClick OK to '+verb.toLowerCase()+'.' );
        });
    });
    
}) (window, undefined);