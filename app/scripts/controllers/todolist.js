'use strict';

angular.module('todoApp')
  .controller('TodolistCtrl', ['$scope', 'todoFactory', '$rootScope', function ($scope, todoFactory, $rootScope ) {

        $scope.frequencies = function (){
          todoFactory.getfrequencies()
            .success(function (data) {
              $scope.frequencies = data;
            })
            .error(function (error) {
              $scope.status = 'Error Saving:' + error.message;
            });
        };

        $scope.priorities = function (){
          todoFactory.getpriorities()
            .success(function (data) {
              $scope.priorities = data;
            })
            .error(function (error) {
              $scope.status = 'Error Saving:' + error.message;
            });
        };


        if ($scope.loggedIn){
          $scope.priorities();
          $scope.frequencies();
        }

        $scope.addTodo = function (newTodo){
            newTodo.taskName = newTodo.task;
            newTodo.activegroup = $rootScope.activegroup;
            todoFactory.addTodo(newTodo)
              .success(function (data) {
                if (data){
                  todoFactory.msgSuccess('Todo Added!');
                }
                $scope.todos.push(data);
                $scope.newTodo.task = '';
              })
              .error(function (error) {
                $scope.status = 'Error Saving:' + error.message;
              });
          };

        $scope.updateDone = function (todo){
          todoFactory.updateTodo(todo)
            .success(function (data) {
              if (data){
                if (todo.done){
                  todoFactory.msgSuccess('Well Done!');
                }
              }
            })
            .error(function (error) {
              $scope.status = 'Error Saving:' + error.message;
            });
        };

        $scope.updateTask = function (todo){
          /* following comment turns off camelcase check for this function.. so it'll be ignored */
          /* jshint camelcase: false */
          todoFactory.updateTodo(todo)
            .success(function (data) {
              if (data){
                todoFactory.msgSuccess('Updated');
                // update the date since it may have changed (ie - pass in Monday and the backend service will translate)
                for(var i=0;i<$scope.todos.length;i++){
                  if($scope.todos[i].todo_id === data.todo_id){
                    $scope.todos[i].due_dt = data.due_dt;
                  }
                }
              }
            })
            .error(function (error) {
              $scope.status = 'Error Saving:' + error.message;
            });
        };

        $scope.getMyTodos = function (){
            todoFactory.getToDos()
              .success(function (data) {
                if (data){
                  $scope.todos = data;
                }
              })
              .error(function (error) {
                $scope.status = 'Error Retrieving ToDos:' + error.message;
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
              todoFactory.moveTodos(passedData)
                .success(function (data) {
                  if (data.error){
                    todoFactory.msgError(data.error);
                  } else {
                    todoFactory.msgSuccess(data.msg);
                  }
                })
                .error(function (error) {
                  $scope.status = 'Error Moving ToDos:' + error.message;
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

