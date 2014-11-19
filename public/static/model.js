var comments = angular.module('Comments', ['ngCookies']);
var x;
comments.controller('CommentsController', ['$scope', '$window', 'Auth', '$http', function($scope, $window, Auth, $http){
    x = $scope;
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
            if (data.result="error") $scope.errorMessage = data.message;
            else $scope.errorMessage = "Сетевая ошибка"
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
        submit: function() {
            $scope.postFormCtl.state = "busy";
            $http({
                method: 'post',
                url:commentsRoot + './php/post.php',
                params: {k: $scope.key},
                data:$scope.postFormCtl.data,
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
            Auth.login($scope.authCtl.data).then(function(data){
                $scope.authData = data;
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
                data:$scope.registerCtl.data,
                cache:false
            }).success(function(data){
                if (data.result == 'success'){
                    $scope.registerCtl.state = 'ready';
                    $scope.tab = 'RegisterSuccess';
                } else {
                    $scope.registerCtl.state = 'error';
                    $scope.registerCtl.message = "Внутренняя ошибка";
                }
            }).error(function(data){
                $scope.registerCtl.state = 'error';
                if (data.result == 'error')
                    $scope.registerCtl.message = data.message;
                else
                    $scope.registerCtl.message = "Сетевая ошибка";
            });
        }
    };

    $scope.$watch('authData.name', function(newV){
        $scope.settingsCtl.data.name = newV;
    });
    $scope.settingsCtl = {
        state: 'ready',
        data: {},
        changeName: function(){
            sData = {
                name: $scope.settingsCtl.data.name
            };
            $scope.settingsCtl.state = 'working';
            $http({
                method: 'post',
                url:commentsRoot + './php/settings.php',
                data:sData,
                cache:false
            }).success(function() {
                $scope.settingsCtl.state = "ready";
                $scope.settingsCtl.savedNameMessage = true;
                $scope.authData.name = $scope.settingsCtl.data.name;
            }).error(function(data){
                $scope.settingsCtl.state = "nMessage";
                if (data.result == "error") $scope.settingsCtl.nameMessage = data.message;
                else $scope.settingsCtl.nameMessage = "Сетевая ошибка";
            });
        },
        changePassword: function(){
            sData = {
                password: $scope.settingsCtl.data.password,
                password1: $scope.settingsCtl.data.password1,
                password2: $scope.settingsCtl.data.password2
            };
            $scope.settingsCtl.state = 'working';
            $http({
                method: 'post',
                url:commentsRoot + './php/settings.php',
                data:sData,
                cache:false
            }).success(function() {
                $scope.settingsCtl.state = "ready";
                $scope.settingsCtl.savedPasswordMessage = true;
            }).error(function(data){
                $scope.settingsCtl.state = "pMessage";
                if (data.result == "error") $scope.settingsCtl.passwordMessage = data.message;
                else $scope.settingsCtl.passwordMessage = "Сетевая ошибка";
            });
        }
    };



    // "Post", "Auth", "Register", "Settings", "RegisterSuccess", "ChangePassword"
    $scope.tab = "Loading";
    Auth.checkSession().then(function(data){
        $scope.authData = data;
        $scope.tab = 'Post';
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
                pr.resolve(data);
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
                pr.resolve(data);
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
comments.directive('match', function () {
    return {
        require: 'ngModel',
        restrict: 'A',
        scope: {
            match: '='
        },
        link: function(scope, elem, attrs, ctrl) {
            scope.$watch(function() {
                var modelValue = ctrl.$modelValue || ctrl.$$invalidModelValue;
                return (ctrl.$pristine && angular.isUndefined(modelValue)) || scope.match === modelValue;
            }, function(currentValue) {
                ctrl.$setValidity('match', currentValue);
            });
        }
    };
});