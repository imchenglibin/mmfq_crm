/**
 * Created by sheldon on 2016/5/4.
 */
app.controller('EmployeeAddCtrl', ['$scope', '$state',function ($scope,$state) {

    $scope.customer = {};

    $scope.addCustomer = function () {
        if (!!$scope.customer.sign_date) {
            var signDate = Date.parse($scope.customer.sign_date);
            console.log(signDate / 1000);
        }

        $.post('/mmfq/api/users/add_user', {
            username: $scope.employee.name,
            password: $scope.employee.password,
            real_name:$scope.employee.real_name
        }).then(function (res) {
            if (res.code == 0) {
                console.log(res);
                if ($scope.submitState == 1) {
                    $state.go('app.employee.list')
                } else if ($scope.submitState == 0) {
                    alert('创建成功');
                    $state.reload()
                }
            } else {
                console.log(res);
            }

        }, function (error) {
            console.log(error)
        })

    };

    $scope.backList = function () {
        $scope.submitState = 1;
        console.log($scope.submitState);
    };

    $scope.createNext = function () {
        $scope.submitState = 0;
        console.log($scope.submitState);
    }

}]);