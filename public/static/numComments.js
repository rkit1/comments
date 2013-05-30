jQuery(function(){
    var $ = jQuery;
    var hrefs = [];
    $('a.numComments').each(function(i){
        hrefs[i] = this.pathname;
    })
    alert(hrefs.toSource());
})
