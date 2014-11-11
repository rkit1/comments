var comments = angular.module('Comments', ['ngCookies']);
var x;
comments.controller('CommentsController', ['$scope', '$window', 'Auth', '$http', function($scope, $window, Auth, $http){
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
            $scope.postFormCtl.state = "ready";
        },
        submit: function() {
            $scope.postFormCtl.state = "busy";
            $http({
                method: 'post',
                url:commentsRoot + './php/post.php',
                params: {k: $scope.key},
                data:this.data,
                cache:false
            }).success(function() {
                $scope.postFormCtl.state = "ready";
                $scope.fetchComments();
                $scope.postFormCtl.data.comment="";
            }).error(function(data){
                $scope.postFormCtl.state = "message";
                if (data.result == "error")
                    $scope.postFormCtl.message = data.message;
                //TODO handle 403
            });
        }
    };

    $scope.authCtl = {
        data: {remember:true},
        state: 'ready',
        submit: function(){
            $scope.authCtl.state = 'working';
            Auth.authorize(this.data).then(function(name){
                $scope.name = name;
                $scope.tab = 'Post';
                $scope.authCtl.state = 'ready';
            }, function(message){
                $scope.authCtl.message = message;
                $scope.authCtl.state = 'error';
            });
        }
    };

    $scope.registerCtl = {
        state: 'ready',
        data: {},
        submit: function(){
            $scope.registerCtl.state = 'working';
            $http({
                method: 'post',
                url:commentsRoot + './php/register.php',
                data:data,
                cache:false
            }).success(function(data){
                $scope.registerCtl.state = 'ready';
                $scope.tab = 'RegisterSuccess';
            }).error(function(data){
                $scope.registerCtl.state = 'error';
                if (data.result == 'error')
                    $scope.registerCtl.message = data.message;
                else
                    $scope.registerCtl.message = "Сетевая ошибка";
            });
        }
    };

    // "Post", "Auth", "Register", "Settings", "RegisterSuccess"
    $scope.tab = "Loading";
    Auth.checkSession().then(function(name){
        $scope.name = name;
        $scope.tab = 'Post';
        $scope.tab = "Register";
    }, function(fail){
        $scope.tab = 'Auth'
    });
}]);
comments.factory('Auth', ['$http', '$q', function($http, $q){
    return {
        login: function(authData){
            var pr = $q.defer();
            $http({
                method: 'post',
                url:commentsRoot + './php/auth.php',
                data:authData,
                cache:false
            }).success(function(data) {
                pr.resolve(data.name);
            }).error(function(data){
                if (data.result == "error") {
                    pr.reject(data.message);
                } else {
                    pr.reject("Сетевая ошибка");
                }
            });
            return pr.promise;
        },
        checkSession: function(){
            var pr = $q.defer();
            $http({
                method: 'get',
                url:commentsRoot + './php/checkAuth.php',
                cache:false
            }).success(function(data) {
                pr.resolve(data.name);
            }).error(function(data){
                pr.reject();
            });
            return pr.promise;
        },
        logout: function(){
            var pr = $q.defer();
            $http({
                method: 'get',
                url: commentsRoot + './php/logout.php',
                cache: false
            }).success(function(){
                pr.resolve();
            }).error(function(){
                pr.reject();
            });
            return pr.promise;
        }
    };
}]);