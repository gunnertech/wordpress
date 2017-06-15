(function($){
		  
	$(document).ready(function(){
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////						   
		
		// -------------------------------------------------------------------------------------------------------
		// Cufon - font replacement
		// -------------------------------------------------------------------------------------------------------
		
		if (themeCufon == 'true'){
		
			if ( ! ( $.browser.msie && ($.browser.version == 6) ) ){ // only apply cufon on modern browsers
			
				Cufon.replace('h1, h2, h3, h4, h5, h6, #contact, #dropdown-menu li a', {hover: true});
			}
			Cufon.now();
		}
		
		// -------------------------------------------------------------------------------------------------------
		// Tipsy - facebook like tooltips jQuery plugin
		// -------------------------------------------------------------------------------------------------------
		
		$('.tip').tipsy({gravity: 'w', fade: true});
		
		// -------------------------------------------------------------------------------------------------------
		// pretyPhoto - jQuery lightbox plugin
		// -------------------------------------------------------------------------------------------------------
		
		$("a[rel^='prettyPhoto']").prettyPhoto({
			opacity: 0.80, 						// Value between 0 and 1
			default_width: 500,
			default_height: 344,
			theme: 'light_square', 				// light_rounded / dark_rounded / light_square / dark_square / facebook 
			hideflash: false, 					// Hides all the flash object on a page, set to TRUE if flash appears over prettyPhoto 
			modal: false 						// If set to true, only the close button will close the window 
		});
		
		// -------------------------------------------------------------------------------------------------------
		// Cycle - slider jQuery plugin 
		// -------------------------------------------------------------------------------------------------------
		
		if ($('#slideshow').size()){
			$('#slideshow ul').cycle({
				timeout: themeSliderTimeout,		// milliseconds between slide transitions (0 to disable auto advance)
				fx:   'fade',						// choose your transition type, ex: none, fade, scrollUp, shuffle, etc... 
				prev: '#slideshow-nav .prev',		// selector for element to use as click trigger for next slide  
				next: '#slideshow-nav .next',		// selector for element to use as click trigger for previous slide
				delay: 0,      						// additional delay (in ms) for first transition (hint: can be negative)
				speed: 600,  						// speed of the transition (any valid fx speed value) 
				pause: 0,							// true to enable "pause on hover"
				cleartypeNoBg: true,				// set to true to disable extra cleartype fixing (leave false to force background color setting on slides) 
				pauseOnPagerHover: 0 				// true to pause when hovering over pager link
			});
		}
		
		if ($('#portfolio-item-preview-slideshow').size()){
			$('#portfolio-item-preview-slideshow ul').cycle({
				timeout: themeSliderTimeout,// milliseconds between slide transitions (0 to disable auto advance)
				fx: 'fade',// choose your transition type, ex: fade, scrollUp, shuffle, etc... 
				prev: '#portfolio-item-preview-slideshow-prev',// selector for element to use as click trigger for next slide  
				next: '#portfolio-item-preview-slideshow-next',// selector for element to use as click trigger for previous slide
				delay: 0,      // additional delay (in ms) for first transition (hint: can be negative)
				speed: 1000,  // speed of the transition (any valid fx speed value) 
				pause: 0,// true to enable "pause on hover"
				cleartypeNoBg: true,// set to true to disable extra cleartype fixing (leave false to force background color setting on slides) 
				pauseOnPagerHover: 0 // true to pause when hovering over pager link
			});
		}	
		
		// -------------------------------------------------------------------------------------------------------
		//  Tabify - jQuery tabs plugin
		// -------------------------------------------------------------------------------------------------------
		
		$('#tab-1').tabify();
		$('#tab-2').tabify();
		$('#tab-3').tabify();
		$('#tab-4').tabify();
		$('#tab-5').tabify();
		
		// -------------------------------------------------------------------------------------------------------
		//  Accordeon - jQuery accordeon plugin
		// -------------------------------------------------------------------------------------------------------
		
		$('#accordion-1').accordion();
		$('#accordion-2').accordion();
		$('#accordion-3').accordion();
		$('#accordion-4').accordion();
		$('#accordion-5').accordion();
		
		// -------------------------------------------------------------------------------------------------------
		//  Comment Form - Validation
		// -------------------------------------------------------------------------------------------------------
		
		$("#comment-form").validity(function() {
			$("#author").require();                      
			$("#email").require().match("email");                      
			$("#comment").require();                      
		});

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////	
	});

})(window.jQuery);

// non jQuery plugins below

