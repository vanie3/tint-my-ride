var not_resizes = false;

jQuery(document).ready(function($)
{
    var _window = $(window),
        menuItem = $('#topnav > li'),
        filterItem = $('.filter-wrapper .nav-pills li'),
        paginationItem = $('.pagination ul li');
        
    /* 
        menuItem.addCustomClass(5, 'custom-color');
        filterItem.addCustomClass(4, 'custom-color');
        paginationItem.addCustomClass(4, 'custom-color');
    */
    
    menuItem.each(function(){
        _this = $(this);
        _this.append('<div class="menu-item-hover-plane-1"></div>');
        _this.append('<div class="menu-item-hover-plane-2"></div>');
    })   
    
    var portfolio = $('.portfolio-shortcode .portfolio_wrapper'),
        portfolio_item_selector = '.portfolio-item',
        portfolio_item = $(portfolio_item_selector, portfolio),
        portfolio_columns_init = portfolio.data('columns'),
        transitionDuration = '0.5',
        filterButtons = $('.portfolio-shortcode .portfolio_filter_buttons > .filter_button'),
        currentCategory = '*';
    
    if(portfolio.length > 0) {
        portfolio.imagesLoaded( function() {
            setTimeout(function(){
                setColumnsNumber();
                resizePortfolioItem();
                portfolio.isotope({
                    itemSelector: portfolio_item_selector,
        			resizable : true,
        			layoutMode: 'masonry'
        		}).bind("resize.rainbows", function(){
  		            setColumnsNumber();
        		    resizePortfolioItem();
                    portfolio.isotope('reLayout');
        		});
            },10);
        });
        
        filterButtons.on( 'click', function() {
            var _this = $(this);
            var category = _this.attr('data-filter');
            
            if(currentCategory != category){
                filterButtons.removeClass('current-category');
                _this.addClass('current-category');
                currentCategory = category;
                if(category != '*') category = '.'+category;
                portfolio.isotope({ filter: category});
            }
        });
        
        $('.portfolio_wrapper').magnificPopup({
    		delegate: '.thumbnail > a',
    		type: 'image',
    		removalDelay: 500,
    		mainClass: 'mfp-zoom-in',
  		    callbacks: {
    			beforeOpen: function() {
    				// just a hack that adds mfp-anim class to markup 
    				this.st.image.markup = this.st.image.markup.replace('mfp-figure', 'mfp-figure mfp-with-anim');
    			},
                open: function() {
                  not_resizes = true;
                },
                close: function() {
                  not_resizes = false;
                }
    		},
    		gallery: {enabled:true}
    	});
    }
    
    function setColumnsNumber() {
        if(_window.width() < 450){
            portfolio_columns = Math.ceil(portfolio_columns_init/3);  
        } else if (_window.width() < 767){
            portfolio_columns = Math.ceil(portfolio_columns_init/2);  
        } else {
            portfolio_columns = portfolio_columns_init;  
        }
    }
    function resizePortfolioItem(){
        item_width = parseInt(portfolio.width() / portfolio_columns) - 30;
        portfolio_item.each(function(){
            $(this).width(item_width).height(item_width/1.38);
        })
    }
    
});


$.fn.addCustomClass = function(number, customClass)
{ 
    var items = $(this);
    
    for( i=0; i<items.length; i++ ){
        _this = items.eq(i);
        _this.addClass(customClass+((i % number)+1));
    }
}