var app = angular.module('myApp', ['ngAnimate']);
app.controller('myCtrl', function($scope) {

    $scope.courseIndex = 0;
    $scope.invisibleCC = false;

    $scope.getCourses = function() {
        stringArray = parseSchedule();
        $scope.courseArray = [];
        for (var i = 0; i < stringArray.length; i++) {
            $scope.courseArray.push(new Course(stringArray[i]));
        } //for

        //Hide button and textbox after clicking it
        document.getElementById('get-button').style.display = 'none';
        document.getElementById('textArea-id').style.display = 'none';
        document.getElementById('courseTable').style.display = 'block';

    } //createTable()

    $scope.incrementIndex = function() {
        if($scope.courseIndex >= $scope.courseArray.length - 1){
            $scope.courseIndex = 0;
        }
        else {
            $scope.courseIndex++;
        }
    } //incrementIndex()

    $scope.insertCourse = function(index) {

        var currentCourse = $scope.courseArray[index];
        var event;
        var exam;

        for(var i = 0; i < currentCourse.meeting_array.length; i++) {
            event = currentCourse.getEventJSON(i);
            insertEvent(event);
        } //for

        exam = currentCourse.getFinalJSON();
        insertEvent(exam);
        $scope.courseArray.splice(index, 1);
        $scope.courseIndex--;
        
    } //insertCourse()

    $scope.insertCal = function() {
        insertCalendar();
        $scope.invisibleCC = true;
    } //insertCal()

});

