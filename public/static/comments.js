commentsRoot = '';

document.write(
'<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.6/angular.min.js"></script>\
<script src="http://ajax.googleapis.com/ajax/libs/angularjs/1.0.6/angular-cookies.min.js"></script>\
<script src="'+ commentsRoot +'/static/model.js"></script>\
<div xmlns:ng="http://angularjs.org" id="ng-app" ng-app="Comments">\
    <div ng-include src="\''+ commentsRoot +'/static/comments.htm\'"></div>\
</div>');
