$(document).ready(function() {
    
    $('a.thumb').fancybox({
        overlayColor: '#000',
        overlayOpacity: '0.8'
    });
    
    /* aviaSlider */
    $('#slider').nivoSlider({
	directionNav:true,
        effect:'fold',
        directionNavHide:false
    });  
    
    /* input focus */  
    $('input[type="text"]').focus(function() {  
        if (this.value == this.defaultValue){ this.value = ''; }  
    });
    
});