/**
 * Created by paul-winter on 2/11/14.
 */
'use strict';

angular.module('todoApp')
  .controller('TodoGroupCtrl', function ($scope, todoFactory, $rootScope ) {

    $scope.newGroup = {};


    $scope.getTodoGroups = function (){
      /* following comment turns off camelcase check for this function.. so it'll be ignored */
      /* jshint camelcase: false */
      todoFactory.getToDoGroups()
        .success(function (data) {
          $scope.groups = data;
          for(var i=0;i<$scope.groups.length;i++){
            if($scope.groups[i].active === true){
              $rootScope.activegroup = $scope.groups[i].group_id;
            }
          }
        })
        .error(function (error) {
          $scope.status = 'Error Getting Todo Groups:' + error.message;
        });
    };

    $scope.addGroup = function (group){
      todoFactory.addGroup(group)
        .success(function (data) {
          if (data){
            $scope.getTodoGroups();
            $scope.newGroup.name = '';
            todoFactory.msgSuccess('Todo Group Added!');
          }
        })
        .error(function (error) {
          $scope.status = 'Error Adding Group:' + error.message;
        });
    };

    $scope.updateGroup = function (group){
      todoFactory.updateGroup(group)
        .success(function (data) {
          if (data){
            $scope.getTodoGroups();
            todoFactory.msgSuccess('Updated!');
          }
        })
        .error(function (error) {
          $scope.status = 'Error Adding Group:' + error.message;
        })
    };

    $scope.deleteGroup = function (group){
      todoFactory.deleteGroup(group)
        .success(function (data) {
          if (data){
            $scope.getTodoGroups();
            todoFactory.msgSuccess('Deleted!');
          }
        })
        .error(function (error) {
          $scope.status = 'Error Adding Group:' + error.message;
        })
    };


    $scope.setTodoGroupActive = function (id){
      /* following comment turns off camelcase check for this function */
      /* jshint camelcase: false */
      for(var i=0;i<$scope.groups.length;i++){
        if($scope.groups[i].group_id === id){
          $scope.groups[i].active = true;
        } else {
          $scope.groups[i].active = false;
        }
      }
      $rootScope.activegroup = id;
      //FixMe: - Add call to update TodoGroups Active in Table.....
    };

    // LoggedIn is broadcast after successful login
    $scope.$on('LoggedIn', function(event, data) {
      /* following comment turns off unused check for this function */
      /* jshint unused: false */
        $scope.getTodoGroups();
      });

    // LogOut is broadcast after person does LogOut
    $scope.$on('LogOut', function(event, data) {
      /* following comment turns off unused check for this function */
      /* jshint unused: false */
      $scope.groups = [];
    });


  });
