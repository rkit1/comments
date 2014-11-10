var comments = angular.module('Comments', ['ngCookies']);
var x;
comments.controller('CommentsController', ['$scope', '$window', '$http', function($scope, $window, $http){
    $scope.key = $window.location.pathname;
    $scope.isIE10 = !!navigator.userAgent.match(/MSIE 10/);

    $scope.comments = [];
    $scope.displayError = false;
    $scope.fetchComments = function() {
        $http({
            method:'get',
            url:commentsRoot + './php/get.php',
            params: {k: $scope.key},
            cache: false
        }).success(function(data){
                $scope.comments = data;
            }).error(function(data){
                $scope.displayError = true;
            })
    };
    $scope.fetchComments();

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


    $scope.postFormCtl = {
        state: "ready",
        data: {},
        readMessage: function(){
            this.state = "ready";
        },
        submit: function() {
            this.state = "busy";
            $http({
                method: 'post',
                url:commentsRoot + './php/post.php',
                params: {k: $scope.key},
                data:this.data,
                cache:false
            }).success(function() {
                this.state = "ready";
                $scope.fetchComments();
                this.data.comment="";
            }).error(function(data){
                this.state = "message";
                if (data.result == "error")
                    this.message = data.message;
                //TODO handle 403
            });
        }
    };

    var st = 'unauthorized';
    if ($cookies['commentsUser']) st = 'authorized';
    $scope.authCtl = {
        data: {remember:true},
        state: st,
        message: null,
        submit: function(){
            this.state = "working";
            $http({
                method: 'post',
                url:commentsRoot + './php/auth.php',
                data:this.data,
                cache:false
            }).success(function() {
                this.state = "authorized";
                $scope.$emit('authSuccess');
            }).error(function(data){
                this.state = "error";
                if (data.result == "error") {
                    this.message = data.message;
                } else {
                    this.message = "Сетевая ошибка";
                }
            });
        },
        logout: function(){
            $cookies['commentsUser'] = null;
            this.state = 'unauthorized';
            $http({
                method: 'get',
                url: commentsRoot + './php/logout.php',
                cache: false
            });
        }
    };

    // "Post", "Auth", "Register", "Settings"
    if ($scope.authCtl.state == "authorized") $scope.tab = "Post";
    else $scope.tab = "Auth";
}]);