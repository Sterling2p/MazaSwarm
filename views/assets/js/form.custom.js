$(function() {
    
    $('.custom-check').prettyCheck();
    $('.toggle-switch').toggleSwitch();
    $('.custom-file').prettyFile();
    $('.custom-select').prettySelect();
});

(function( $ ) {
    
    // jQuery case-insensitive :contains selector:
    $.expr[":"].contains = $.expr.createPseudo(function(arg) {
        return function( elem ) {
            return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
        };
    });
 
    $.fn.prettyCheck = function()
    {
        var tpl = '<div class="form-control-pretty-check"></div>';
        
        return this.each(function() {
            
            var $input = $(this).find('input'),
                $label = $('label[for="'+$input.attr('id')+'"]'),
                $div   = $(tpl);
                
            $div.addClass( $input.attr('type') );
            $div.add($label).data('input', $input);
            
            $label.removeAttr('for');
            
            $div.prependTo(this);
            $input.data('div', $div);
            
            if( $input.attr('disabled') ) { $div.addClass('disabled'); }
            if( $input.is(':checked') )   { $div.addClass('checked');  }
            
            $(this).off('click').on('click', function() {
                
                var $input = $(this).find('input');
                
                console.log( $input.length );

                if( !$input.attr('disabled') )
                {   
                    if( $input.attr('type') == 'radio' )
                    {
                        if( !$input.is(':checked') )
                        {
                            //$input.data('div').toggleClass('checked');
                            
                            $('input[name="'+$input.attr('name')+'"]').removeAttr('checked').siblings().removeClass('checked')
                            $input.attr('checked', true).data('div').addClass('checked');
                        }
                    }
                    else
                    {
                        $input.data('div').toggleClass('checked');
                        
                        if( $input.is(':checked') )
                        {
                            $input.attr('checked', false);
                        }
                        else
                        {
                            $input.attr('checked', true);
                        }
                    }
                }
            });
        });
    };
    
    $.fn.toggleSwitch = function( opts, value )
    {
        // Permitted themes:
        var themes = [ 'default', 'square', 'outline', 'square-outline' ],
            sizes  = [ 'medium', 'small', 'large', 'xlarge' ];
            
        if( typeof opts == 'object' || opts == undefined )
        {
            var settings = $.extend({
                bindLabel: true,
                className: 'jq-toggle-switch',
                theme:     'default',
                size:      'medium'
            }, opts);
            
            // Loop over them:
            this.filter('input[type="checkbox"]').not( settings.className ).each(function() {
                
                var $toggle = $('<div class="jq-toggle-switch" />' ),
                    id      = $(this).attr('id');
                    
                $toggle.append( '<span class="jq-toggle-button" />' )
                       .data('checkbox', $(this) )
                       .insertBefore( $(this) );
                
                // Colours based on classname:
                if( $(this).hasClass('toggle-switch-success') ) { $toggle.addClass('toggle-switch-success');    }
                if( $(this).hasClass('toggle-switch-danger') ) { $toggle.addClass('toggle-switch-danger');      }
                if( $(this).hasClass('toggle-switch-warning') ) { $toggle.addClass('toggle-switch-warning');    }
                if( $(this).hasClass('toggle-switch-purple') ) { $toggle.addClass('toggle-switch-purple');      }
                
                // Sizes based on classname:
                if( $(this).hasClass('toggle-switch-xs') ) { $toggle.addClass('jq-toggle-switch-size-xsmall');  }
                if( $(this).hasClass('toggle-switch-sm') ) { $toggle.addClass('jq-toggle-switch-size-small');   }
                if( $(this).hasClass('toggle-switch-lg') ) { $toggle.addClass('jq-toggle-switch-size-large');   }
                if( $(this).hasClass('toggle-switch-xl') ) { $toggle.addClass('jq-toggle-switch-size-xlarge');  }
                
                // Theme based on classname:
                if( $(this).hasClass('toggle-switch-outline') ) { $toggle.addClass('jq-toggle-switch-theme-outline'); }
                if( $(this).hasClass('toggle-switch-square') ) { $toggle.addClass('jq-toggle-switch-theme-square');   }
                
                
                $(this).appendTo($toggle);
                
                if( $(this).is(':checked') )
                {
                    $toggle.addClass('checked');
                }
                
                // Add the theme class:
                if( settings.theme != 'default' && $.inArray(settings.theme, themes) )
                {
                    $toggle.data('theme', settings.theme);
                    
                    if( settings.theme == 'square-outline' )
                    {
                        $toggle.addClass('jq-toggle-switch-theme-square');
                        $toggle.addClass('jq-toggle-switch-theme-outline');
                    }
                    else
                    {
                        $toggle.addClass('jq-toggle-switch-theme-'+settings.theme);
                    }
                }
                
                // Set the size:
                if( settings.size == 'small' || settings.size == 'large' || settings.size == 'xlarge' )
                {
                    $toggle.data('size', settings.size);
                    $toggle.addClass('jq-toggle-switch-size-'+settings.size);
                }
                
                if( id != undefined )
                {
                    var $label = $('label[for="'+id+'"]');
                    $toggle.attr('id', 'toggle_'+id);
                    
                    // If there's a label:
                    if( $label.length && settings.bindLabel )
                    {
                        $label.on('click', function(e) { e.preventDefault(); $toggle.data('checkbox').toggleSwitch('toggle'); });
                    }
                }
                
                $(this).data('toggle', $toggle);
                
                if( $(this).attr('disabled') )
                {
                    $toggle.addClass('disabled');
                }
                else
                {
                    // Handle the checking:
                    $toggle.on('click', function() { $(this).data('checkbox').toggleSwitch('toggle'); });
                }
            });
        }
        
        else if( typeof opts == 'string' && this.data('toggle') )
        {
            this.each(function() {
                
                var $toggle = $(this).data('toggle');
            
                // Make sure it's valid:
                switch( opts )
                {
                    // Check the toggle switch:
                    case 'check':
                        $toggle.addClass('checked').data('checkbox').attr('checked', true).trigger('change');
                        break;
                    
                    // Uncheck the toggle switch:
                    case 'uncheck':
                        $toggle.removeClass('checked').data('checkbox').removeAttr('checked').trigger('change');
                        break;
                    
                    // Disable:
                    case 'disable':
                        $toggle.addClass('disabled');
                        break;
                    
                    // Enable:
                    case 'enable':
                        $toggle.removeClass('disabled');
                        break;
                    
                    // Toggle the toggle switch:
                    case 'toggle':
                        $toggle.toggleClass('checked');
                        
                        if( $toggle.hasClass('checked') )
                        {
                            $toggle.data('checkbox').attr('checked', true);
                        }
                        else
                        {
                            $toggle.data('checkbox').removeAttr('checked');
                        }
                        
                        $toggle.data('checkbox').trigger('change');
                        break;
                    
                    // Check if the element is checked:
                    case 'checked':
                        return $toggle.hasClass('checked');
                        break;
                    
                    // Set the size:
                    case 'setSize':
                        if( $.inArray(value, sizes) )
                        {
                            if( $toggle.data('size') )
                            {
                                $toggle.removeClass('jq-toggle-switch-size-'+$toggle.data('size'));
                            }
                            
                            $toggle.addClass('jq-toggle-switch-size-'+value);
                            $toggle.data('size', value);
                        }
                        else
                        {
                            (console.error || console.log).call(console, 'Invalid size specified.');
                        }
                        break;
                    
                    // Set the theme:
                    case 'setTheme':
                        if( $.inArray(value, themes) )
                        {
                            if( $toggle.data('theme') )
                            {
                                if( $toggle.data('theme') == 'square-outline' )
                                {
                                    $toggle.removeClass('jq-toggle-switch-theme-square');
                                    $toggle.removeClass('jq-toggle-switch-theme-outline');
                                }
                                else
                                {
                                    $toggle.removeClass('jq-toggle-switch-theme-'+$toggle.data('theme'));
                                }
                            }
                            
                            if( value == 'square-outline' )
                            {
                                $toggle.addClass('jq-toggle-switch-theme-square');
                                $toggle.addClass('jq-toggle-switch-theme-outline');
                            }
                            else
                            {
                                $toggle.addClass('jq-toggle-switch-theme-'+value);
                            }
                            
                            $toggle.data('theme', value);
                        }
                        else
                        {
                            (console.error || console.log).call(console, 'Invalid theme specified.');
                        }
                        break;
                    
                    // Destory the toggle switch:
                    case 'destroy':
                        $toggle.data('checkbox').insertBefore( $toggle );
                        $toggle.remove();
                        break;
                    
                    // Throw an error since it does not exist:
                    default:
                        (console.error || console.log).call(console, 'Invalid function name provided.');
                        break;
                }
            });
        }
        
        return this;
    };
    
    $.fn.prettyFile = function()
    {
        this.each(function() {
            
            $(this).wrap( '<label class="btn" />' );
            
            var $btn = $(this).parent();
            
            $btn.append('<i class="custom-file-input__icon ion-image"></i><span class="custom-file-input__text">Choose file'+( ($(this).attr('multiple')) ? 's' :'' )+'</span>')
                .wrap('<div class="custom-file-input" />');
                
            $('<span class="custom-file-input__value"></span>').insertAfter($btn);
            
            // Add the classes:
            if( $(this).hasClass('primary') )        { $btn.addClass('btn-primary');   }
            else if( $(this).hasClass('secondary') ) { $btn.addClass('btn-secondary'); }
            else if( $(this).hasClass('success') )   { $btn.addClass('btn-success');   }
            else if( $(this).hasClass('warning') )   { $btn.addClass('btn-warning');   }
            else if( $(this).hasClass('danger') )    { $btn.addClass('btn-danger');    }
            else                                     { $btn.addClass('btn-primary');   }
            
            if( $(this).hasClass('rounded') )        { $btn.addClass('btn-rounded') }
            if( $(this).hasClass('outline') )        { $btn.addClass('btn-outline') }
            
            // Handle the change:
            $(this).on('change', function() {
                
                var $value = $(this).parent().siblings('.custom-file-input__value'),
                    value  = '';
                
                if( $(this).attr('multiple') )
                {
                    if( this.files.length > 1 )
                    {
                        value = this.files.length + ' files';
                    }
                    else
                    {
                        value = $(this).val().replace('C:\\fakepath\\', '');
                    }
                }
                else
                {
                    value = this.value.replace('C:\\fakepath\\', '');
                }
                
                if( value.length > 60 )
                {
                    var start = value.substring(0, 30),
                        end   = value.substring((value.length - 10), value.length);
                    
                    value = start+'...'+end;
                }
                
                $value.text( value );
                
            });
            
        });
        
        // Preserve chaining:
        return this;
    }
    
    $.fn.prettySelect = function()
    {
        this.each(function() {
            
            var $select = $(this),
                $wrapper = $('<div class="dropdown custom-select-dropdown" />'),
                dropdown = '<div class="dropdown-menu">';
                
            $select.children('option').each(function(i) {
                if( $(this).attr('disabled') )
                {
                    dropdown+= '<a class="dropdown-item dropdown-item-disabled" href="#" data-index="'+i+'">'+$(this).text()+'</a>';
                }
                else
                {
                    dropdown+= '<a class="dropdown-item" href="#" data-index="'+i+'">'+$(this).text()+'</a>';
                }
            });
            
            dropdown+= '</div>';
            
            var $dropdown = $(dropdown).data('select', $select);
            
            $select.wrap( $wrapper );
            $wrapper = $select.parent();
            
            $wrapper.prepend('<button class="dropdown-toggle" data-toggle="dropdown"><span class="custom-select-value">'+$select.children('option:selected').text()+'</span></button>');
            $wrapper.append($dropdown);
            
            if( $select.hasClass('custom-select-inline') )
            {
                $wrapper.addClass('inline');
            }
            
            if( $select.hasClass('searchable') )
            {
                $dropdown.wrapInner( '<div class="scroller" />' );
                $dropdown.addClass('no-scroll');
                $dropdown.prepend('<div class="dropdown-search"><input type="text" placeholder="Search options..." /><a href="#" class="clear-search"><i class="ion-close-circled"></i></a></div>');
                
                $wrapper.on('shown.bs.dropdown', function() {
                    $(this).find('.dropdown-search input').focus();
                }).on('hide.bs.dropdown', function() {
                    $dropdown.find('.dropdown-item').show();
                    $dropdown.find('.scroller').get(0).scrollTo(0,0);
                    $(this).find('.dropdown-search input').val('').blur();
                });
                
                // Handle keyup:
                $dropdown.find('.dropdown-search input').on('keyup', function(e) {
                    
                    var value = $(this).val();
                    
                    if( value.length )
                    {
                        $(this).siblings('.clear-search').addClass('in');
                        
                        // Now filter the elements:
                        $(this).parents('.dropdown').find('.scroller .dropdown-item').show().not(':contains('+value+')').hide()
                    }
                    else
                    {
                        $(this).siblings('.clear-search').removeClass('in');
                    }
                    
                });
                
                $dropdown.find('a.clear-search').on('click', function(e) {
                    $(this).parents('.dropdown').find('.dropdown-item').show();
                    $(this).parents('.dropdown').find('.scroller').get(0).scrollTo(0,0);
                    $(this).siblings('input').val('').focus();
                    $(this).removeClass('in');
                    
                    return false;
                });
            }
            
            $dropdown.on('click', '.dropdown-item', function(e) {
                e.preventDefault();
                
                if( $(this).hasClass('dropdown-item-disabled') )
                {
                    return false;
                }
                else
                {
                    var $select = $(this).parents('.dropdown').find('select');
                    $select.find('option').removeAttr('selected').eq($(this).data('index')).attr('selected', true);
                    $(this).parents('.dropdown').find('.dropdown-toggle').find('.custom-select-value').html( $(this).html() );
                }
            });
            
        });
        
        return this;
    }
 
}( jQuery ));