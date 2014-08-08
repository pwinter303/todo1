'use strict';

angular.module('todoApp')
  .controller('todoAuthenticateCtrl', ['$scope', 'authentication', 'todoFactory', '$location', function($scope, authentication, todoFactory, $location){

        $scope.pwd = {};
        $scope.loggedIn = 0;
        //================================================================================
        $scope.getLoginStatus = function() {
          authentication.getLoginStatus().then(function (data) {
              if (data.login){
                $scope.loggedIn = data.login;
                $scope.$broadcast('LoggedIn', []);
              }
            }, function(error) {
              // promise rejected, could be because server returned 404, 500 error...
              $scope.loggedIn = 0;
              todoFactory.msgError(error);
            });
        };
        $scope.getLoginStatus();

        //================================================================================
        $scope.logIn = function (user){
          $scope.loginmsg='';
          authentication.login(user).then(function (data) {
            $scope.loggedIn = Number(data.login);
            if ($scope.loggedIn) {
              $scope.$broadcast('LoggedIn', []);
              $location.path( '/todolist' );
            } else {
              $scope.loginmsg = 'ERROR - Invalid email/password combination';
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError(error);
          });
        };

        //================================================================================
        $scope.logMeOut = function(){
          authentication.logOut().then(function () {
            $scope.$broadcast('LogOut', []);
            $scope.loggedIn = 0;
            $location.path( '/' );
            todoFactory.msgSuccess('You have logged out. Thanks');
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError(error);
          });
        };

        //================================================================================
        $scope.forgotPassword = function(passworddata){
          authentication.forgotPassword(passworddata).then(function (data) {
            if (data.error){
              todoFactory.msgError(data.error);
            } else {
              todoFactory.msgSuccess('Temporary password has been sent');
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError(error);
          });
        };
        //================================================================================
        $scope.changePassword = function(passworddata){
          authentication.changePassword(passworddata).then(function (data) {
            if (data.error){
              todoFactory.msgError(data.error);
            } else {
              $scope.pwd.old = '';
              $scope.pwd.new1 = '';
              $scope.pwd.new2 = '';
              todoFactory.msgSuccess(data.msg);
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError(error);
          });
        };

        //================================================================================
        $scope.registerMe = function(user){
          authentication.registerUser(user).then(function (data) {
            $scope.errormsg = '';
            if (data.error){
              //$scope.errormsg = data.error;
              todoFactory.msgError(data.errMsg);
            }
            if (data.login){
              $scope.loggedIn = 1;
              $scope.$broadcast('LoggedIn', []);
              todoFactory.msgSuccess(data.msg);
              $location.path( '/todolist' );
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError(error);
          });
        };
      }]);

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