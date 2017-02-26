var app = angular.module('myApp', []);
app.controller('myCtrl', function($scope) {

    $scope.createTable = function() {
        stringArray = parseSchedule();
        $scope.courseArray = [];
        for (var i = 0; i < stringArray.length; i++) {
            $scope.courseArray.push(new Course(stringArray[i]));
        } //for

    } //createTable()

    $scope.x = 5;

    $scope.insertCourse = function(index) {

        var currentCourse = $scope.courseArray[index];

        for(var i = 0; i < currentCourse.meeting_array.length; i++) {
            var event = currentCourse.getEventJSON(i);

            

            gapi.client.load('calendar', 'v3', function(){
                var request = gapi.client.calendar.events.insert({
                    'calendarId': CALENDAR_ID,
                    'resource': event
                });

                request.execute(function(event) {
                    console.log(currentCourse);
                });
            });
            

        } //for
    } //insertCourse()

});

