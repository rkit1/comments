commentsRoot = "/";
jQuery(function(){
    var $ = jQuery;
    var hrefs = [];
    $('a.numComments').each(function(i){
        hrefs[i] = this.pathname;
    })
    $.get(commentsRoot+"./php/numComments.php", {'hrefs[]':hrefs}, function(data){
        alert(data);
    })
})
