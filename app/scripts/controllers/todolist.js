'use strict';

angular.module('todoApp')
  .controller('TodolistCtrl', function ($scope, todoFactory, $rootScope ) {

        $scope.frequencies = function (){
          todoFactory.getfrequencies().then(function(data) {
            //this will execute when the AJAX call completes.
            $scope.frequencies = data;
          });
        };
        $scope.frequencies();

        $scope.priorities = function (){
          todoFactory.getpriorities().then(function(data) {
            //this will execute when the AJAX call completes.
            $scope.priorities = data;
          });
        };
        $scope.priorities();

        $scope.addIt = function (newTodo){
            newTodo.taskName = newTodo.task;
            newTodo.activegroup = $rootScope.activegroup;
            todoFactory.addTodo(newTodo).then(function(data){
              if (data){
                todoFactory.msgSuccess('Todo Added!');
              }
              $scope.todos.push(data);
              //console($scope.todos);
              //$scope.getMyTodos();
              $scope.newTodo.task = '';
            });
          };

        $scope.updateDone = function (todo){
          todoFactory.updateTodo(todo).then(function(data){
            if (data){
              if (todo.done){
                todoFactory.msgSuccess('Well Done!');
              }
            }
            // since the to do has been updated... there's no reason to refresh
            //$scope.getMyTodos();
          });
        };

        $scope.updateTask = function (todo){
          /* following comment turns off camelcase check for this function.. so it'll be ignored */
          /* jshint camelcase: false */
          todoFactory.updateTodo(todo).then(function(data){
              if (data){
                todoFactory.msgSuccess('Updated');
                // update the date since it may have changed (ie - pass in Monday and the backend service will translate)
                for(var i=0;i<$scope.todos.length;i++){
                  if($scope.todos[i].todo_id === data.todo_id){
                    $scope.todos[i].due_dt = data.due_dt;
                  }
                }
              }
              // since the to do has been updated... there's no reason to refresh
              //$scope.getMyTodos();
            });
        };

        $scope.getMyTodos = function (){
            todoFactory.getToDos().then(function(data) {
                //this will execute when the AJAX call completes.
                $scope.todos = data;
                //console.log(data);
              });
          };

        $scope.getMyTodos();

        $scope.getOneTodo = function (){
            todoFactory.getOneTodo().then(function(data) {
                //this will execute when the AJAX call completes.
                $scope.onetodo = data;
                //console.log(data);
              });
          };

    $scope.moveTodos = function (passedData){

          if (typeof passedData.fromGroup === 'undefined'){
            todoFactory.msgError('Select group to move FROM');
          } else {
            if (typeof passedData.toGroup === 'undefined'){
              todoFactory.msgError('Select group to move TO');
            } else {
              todoFactory.moveTodos(passedData).then(function(data) {
                if (data.error){
                  todoFactory.msgError(data.error);
                } else {
                  todoFactory.msgSuccess(data.msg);
                }
              });
            }
          }
        };

      });


angular.module('todoApp')
  .directive('ngEnter', function() {
  return function(scope, element, attrs) {
    element.bind('keydown keypress', function(event) {
      if(event.which === 13) {
        scope.$apply(function(){
          scope.$eval(attrs.ngEnter, {'event': event});
        });

        event.preventDefault();
      }
    });
  };
});

