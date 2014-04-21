'use strict';

angular.module('todoApp')
  .controller('todoAuthenticateCtrl', function ($scope, todoFactory, $location) {

        $scope.pwd = {};

        $scope.getLoginStat = function (){
            var stat = todoFactory.getLocalLoginStatus();

            if (typeof stat === 'undefined'){
              todoFactory.getLoginStatus().then(function(data) {
              //this will execute when the AJAX call completes.
                  $scope.loggedIn = data.login;
                  todoFactory.setLoginStatus($scope.loggedIn);
                  if (!$scope.loggedIn){
                    $location.path( '/' );
                  }
                });
            } else {
              $scope.loggedIn = stat;
            }
          };

        $scope.getLoginStat();


        $scope.logMeIn = function (user){
            $scope.loginmsg='';

            todoFactory.login(user).then(function(data) {
                // php returns a string.. must convert to number
                $scope.loggedIn = Number(data.login);
                todoFactory.setLoginStatus($scope.loggedIn);
                if ($scope.loggedIn) {
                  //$('#myModal').modal('hide');
                  $scope.$broadcast('LoggedIn', []);
                  $location.path( '/todolist' );
                } else {
                  $scope.loginmsg = 'ERROR - Invalid email/password combination';
                }
              });
          };

        $scope.logMeOut = function(){
          todoFactory.logOut().then(function() {
            todoFactory.setLoginStatus(0);
            $scope.$broadcast('LogOut', []);
            $scope.loggedIn = 0;
            $location.path( '/' );
          });
        };

        $scope.changePassword = function(passworddata){
          todoFactory.changePassword(passworddata).then(function(data) {
            if (data.error){
              todoFactory.msgError(data.error);
            } else {
              $scope.pwd.old = '';
              $scope.pwd.new1 = '';
              $scope.pwd.new2 = '';
              todoFactory.msgSuccess(data.msg);
            }
          });
        };

        $scope.registerMe = function(user){
          todoFactory.registerUser(user).then(function(data) {
            $scope.errormsg = '';
            if (data.error){
              $scope.errormsg = data.error;
            }
            if (data.login){
              $scope.loggedIn = 1;
              todoFactory.setLoginStatus(1);
              $scope.$broadcast('LoggedIn', []);
              //$('#myModalRegister').modal('hide');
              todoFactory.msgSuccess(data.msg);
              $location.path( '/todolist' );
            }
          });
        };

      });

angular.module('todoApp')
.directive('formAutofillFix', function() {
    return function(scope, elem, attrs) {
        // Fixes Chrome bug: https://groups.google.com/forum/#!topic/angular/6NlucSskQjY
        elem.prop('method', 'POST');

        // Fix autofill issues where Angular doesn't know about autofilled inputs
        if(attrs.ngSubmit) {
          setTimeout(function() {
                elem.unbind('submit').submit(function(e) {
                    e.preventDefault();
                    elem.find('input, textarea, select').trigger('input').trigger('change').trigger('keydown');
                    scope.$apply(attrs.ngSubmit);
                  });
              }, 0);
        }
      };
  });