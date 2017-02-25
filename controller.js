var app = angular.module('myApp', []);
app.controller('myCtrl', function($scope) {

    $scope.createTable = function() {
        stringArray = parseSchedule();
        $scope.courseArray = [];
        for (var i = 0; i < stringArray.length; i++) {
            $scope.courseArray.push(new Course(stringArray[i]));
        } //for

    } //createTable()

});