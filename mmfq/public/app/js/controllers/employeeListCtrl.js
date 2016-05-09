/**
 * Created by sheldon on 2016/5/4.
 */

app.controller('EmployeeListCtrl', ['$scope','$state', function ($scope,$state) {
    $.get('/mmfq/api/users/get_users').then(function (res) {
        if (res.code == 0){
            $scope.$apply(function () {
                console.log(res);
                $scope.employeeListData = res.data;
            });
        }else {
            console.log(res.message)
        }
    }, function (error) {
        console.log(error)
    });

    $scope.jumpToAdd = function () {
        $state.go('app.employee.add')
    };

    $scope.deleteEmployee = function (x) {
        $.post('/mmfq/api/users/delete_user',{
            user_id: x.id
        }).then(function (res) {
            if (res.code == 0){
                console.log(res);
                $state.reload();
            }else {
                console.log(res.message)
            }
        }, function (error) {
            console.log(error)
        });
    }
}]);