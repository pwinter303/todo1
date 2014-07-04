/**
 * Created by paul-winter on 2/11/14.
 */
'use strict';

angular.module('todoApp')
  .controller('TodoGroupCtrl', function ($scope, todoFactory, $rootScope ) {

    $scope.newGroup = {};


    $scope.getTodoGroups = function (){
      todoFactory.getToDoGroups().then(function(data) {
        /* following comment turns off camelcase check for this function.. so it'll be ignored */
        /* jshint camelcase: false */
          $scope.groups = data;
          for(var i=0;i<$scope.groups.length;i++){
            if($scope.groups[i].active === true){
              $rootScope.activegroup = $scope.groups[i].group_id;
            }
          }
        });
    };

    // needed for page reloads

    // this isnt needed since we've changed the getloginstatus (in authenticate) to broadcast logged in
    // which will call get groups

    //this seems hacky... but.... cant find a better way to do it...
//    getLogin();
//    function getLogin() {
//      todoFactory.getLoginStatusNew()
//        .success(function (data) {
//          if (data.login){
//            $scope.getTodoGroups();
//          }
//        })
//        .error(function (error) {
//        });
//    }

    $scope.addGroup = function (group){
      todoFactory.addGroup(group).then(function(data){
        if (data){
          //$('#myModalAddGroup').modal('hide');
          $scope.getTodoGroups();
          $scope.newGroup.name = '';
          todoFactory.msgSuccess('Todo Group Added!');
        }
      });
    };

    $scope.updateGroup = function (group){
      todoFactory.updateGroup(group).then(function(data){
        if (data){
          $scope.getTodoGroups();
          todoFactory.msgSuccess('Updated!');
        }
      });
    };

    $scope.deleteGroup = function (group){
      todoFactory.deleteGroup(group).then(function(data){
        if (data){
          $scope.getTodoGroups();
          todoFactory.msgSuccess('Deleted!');
        }
      });
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
