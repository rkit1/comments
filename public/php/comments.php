<?php
header("Content-type: application/x-javascript");
$root = preg_replace('/php\/comments.php$/', '', $_SERVER['REQUEST_URI']);
?>
commentsRoot = '<?php echo $root;?>';

document.write(
'<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.26/angular.min.js"></script>\
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.2.26/angular-cookies.min.js"></script>\
<script src="'+ commentsRoot +'./static/model.js"></script>\
<div xmlns:ng="http://angularjs.org" id="ng-app" ng-app="Comments">\
    <div ng-include src="\''+ commentsRoot +'./static/comments.htm\'"></div>\
    <div ng-include="\''+ commentsRoot +'./static/auth.htm\'"></div>\
</div>');
