'use strict';

angular.module('todoApp')
  .controller('TodolistCtrl', ['$scope', 'todoFactory', '$rootScope', function ($scope, todoFactory, $rootScope ) {

        $scope.frequencies = function (){
          todoFactory.getfrequencies().then(function (data) {
            $scope.frequencies = data;
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError('Error Getting Frequencies:' + error);
          });
        };

        $scope.priorities = function (){
          todoFactory.getpriorities().then(function (data) {
            $scope.priorities = data;
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError('Error Getting Priorities:' + error);
          });
        };

        if ($scope.loggedIn){
          $scope.priorities();
          $scope.frequencies();
        }

        $scope.addTodo = function (newTodo){
          newTodo.taskName = newTodo.task;
          newTodo.activegroup = $rootScope.activegroup;
          todoFactory.addTodo(newTodo).then(function (data) {
              if (data){
                todoFactory.msgSuccess('Todo Added!');
              }
              $scope.todos.push(data);
              $scope.newTodo.task = '';
            }, function(error) {
              // promise rejected, could be because server returned 404, 500 error...
              todoFactory.msgError('Error Saving:' + error);
            });
        };

        $scope.updateDone = function (todo){
          todoFactory.updateTodo(todo).then(function (data) {
            if (data){
              if (todo.done){
                todoFactory.msgSuccess('Well Done!');
              }
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError('Error Saving:' + error);
          });
        };

        $scope.updateTask = function (todo){
          /* following comment turns off camelcase check for this function.. so it'll be ignored */
          /* jshint camelcase: false */
          todoFactory.updateTodo(todo).then(function (data) {
            if (data){
              todoFactory.msgSuccess('Updated');
              // update the date since it may have changed (ie - pass in Monday and the backend service will translate)
              for(var i=0;i<$scope.todos.length;i++){
                if($scope.todos[i].todo_id === data.todo_id){
                  $scope.todos[i].due_dt = data.due_dt;
                }
              }
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError('Error Saving:' + error);
          });
        };

        $scope.getMyTodos = function (){
          todoFactory.getToDos().then(function (data) {
            if (data){
              $scope.todos = data;
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError('Error Retrieving ToDos:' + error);
          });
        };
        $scope.getMyTodos();

        $scope.moveTodos = function (passedData){
          if (typeof passedData.fromGroup === 'undefined'){
            todoFactory.msgError('Select group to move FROM');
          } else {
            if (typeof passedData.toGroup === 'undefined'){
              todoFactory.msgError('Select group to move TO');
            } else {
              todoFactory.moveTodos(passedData).then(function (data) {
                if (data.error){
                  todoFactory.msgError(data.error);
                } else {
                  todoFactory.msgSuccess(data.msg);
                }
              }, function(error) {
                // promise rejected, could be because server returned 404, 500 error...
                todoFactory.msgError('Error Moving ToDos:' + error);
              });
            }
          }
        };

      }]);


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

