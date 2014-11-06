var comments = angular.module('Comments', ['ngCookies']);
comments.controller('CommentsController', ['$scope', '$http', '$cookies', '$window'
    , function($scope, $http, $cookies, $window){
    ////
    // common setup
    ////
    //delete $http.defaults.headers.common['X-Requested-With'];
    $scope.key = $window.location.pathname;
    $scope.isAdmin = $cookies.hasOwnProperty('adminToken');
    $scope.isIE10 = !!navigator.userAgent.match(/MSIE 10/);

    ////
    // comments display
    ////
    $scope.comments = [];
    $scope.displayError = false;
    $scope.noComments = false;
    $scope.fetchComments = function() {
        $http({
            method:'get',
            url:commentsRoot + './php/get.php',
            params: {k: $scope.key},
            cache: false
        }).success(function(data){
                $scope.comments = data;
                $scope.noComments = $scope.comments.length == 0;
            }).error(function(data){
                $scope.displayError = true;
            })
    };
    $scope.formatDate = function(ts){
        d = new Date(ts * 1000);
        return d.toLocaleString();
    };

    ////
    // form
    ////

    // "captcha", "ready", "busy", "message"
    $scope.formState = "ready";

    $scope.toSubmit = {};
    $scope.toSubmit.author = $cookies.author;
    $scope.$watch('toSubmit.author', function(newV, oldV){
        $cookies.author = newV;
    });

    $scope.readMessage = function(){
        $scope.formState = "ready";
    };

    $scope.submit = function() {
        $scope.formState = "busy";
        $http({
            method: 'post',
            url:commentsRoot + './php/post.php',
            params: {k: $scope.key},
            data:$scope.toSubmit,
            cache:false
        }).success(function() {
                $scope.formState = "ready";
                $scope.fetchComments();
                $scope.toSubmit.comment="";
            }).error(function(data){
                $scope.formState = "message";
                if (data.result == "error") {
                    $scope.message = data.message;
                }
            })

    };

    ////
    // remove
    ////

    $scope.remove = function (comment) {
        if (confirm("Точно удалить комментарий \n" + comment.comment)){
            $http({
                method: 'get',
                url:commentsRoot + './php/remove.php',
                params: {k: $scope.key, id: comment.idComments},
                data:$scope.toSubmit,
                cache:false
            }).success(function() {
                    $scope.fetchComments();
                }).error(function(data){
                    if (data.result == "error") {
                        alert(data.message);
                    }
                })
        }
    };

    ////
    // init app
    ////

    $scope.fetchComments();
}]);
comments.controller('AuthController', ['$scope', '$http', '$cookies', '$window', function($scope, $http, $cookies, $window){
    $scope.state = "ready";
    $scope.form.remember = true;
    $scope.form.prototype.submit = function(){
        $scope.state = "busy";
        $http({
            method: 'post',
            url:commentsRoot + './php/auth.php',
            data:$scope.form,
            cache:false
        }).success(function() {
            $scope.formState = "ready";
        }).error(function(data){
            $scope.formState = "ready";
            if (data.result == "error") {
                $scope.message = data.message;
            } else {
                $scope.message = "Сетевая ошибка";
            }
        })
    };
}]);