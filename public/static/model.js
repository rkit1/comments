angular.module('Comments', ['ngCookies']);
function CommentsController($scope, $http, $cookies, $window) {
    ////
    // common setup
    ////
    //delete $http.defaults.headers.common['X-Requested-With'];
    $scope.key = $window.location.pathname;

    $scope.isIE10 = !!navigator.userAgent.match(/MSIE 10/);

    ////
    // comments display
    ////
    $scope.comments = [];
    $scope.displayError = false;
    $scope.noComments = false;
    $scope.fetchComments = function() {

        var params = {k: $scope.key};

        ////
        // Говнит, портит даты со стороны php/mysql.
        ////
        // if ($scope.comments.length > 0)
        //    params.last = $scope.comments[$scope.comments.length-1].time;

        $http({
            method:'get',
            url:commentsRoot + './php/get.php',
            params: params,
            cache: false
        }).success(function(data){
            $scope.comments = data; //$scope.comments.concat(data);
                                    //См. выше
            $scope.noComments = $scope.comments.length == 0;
        }).error(function(data){
            $scope.displayError = true;
        })
    };
    $scope.formatDate = function(ts){
        d = new Date(ts * 1000);
        return d.toLocaleString();
    };

    $scope.isAdmin = $cookies.hasOwnProperty('adminToken');

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
}