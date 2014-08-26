'use strict';

angular.module('todoApp')
  .controller('TodolistCtrl', ['$scope', 'todoFactory', '$rootScope', function ($scope, todoFactory, $rootScope ) {

        $scope.addTodo = function (newTodo){
          /* following comment turns off camelcase check for this function.. so it'll be ignored */
          /* jshint camelcase: false */
          newTodo.task_name = newTodo.task;
          newTodo.activegroup = $rootScope.activegroup;
          todoFactory.addTodo(newTodo).then(function (data) {
              if (data){
                if (data.err){
                  todoFactory.msgError(data.errMsg);
                } else {
                  todoFactory.msgSuccess('Todo Added!');
                  $scope.todos.push(data);
                  $scope.newTodo.task = '';
                }
              }
            }, function(error) {
              // promise rejected, could be because server returned 404, 500 error...
              todoFactory.msgError('Error Saving:' + error);
            });
        };

        $scope.deleteTodo = function (Todo){
          todoFactory.deleteTodo(Todo).then(function (data) {
            if (data){
              if (data.RowsDeleted === 0){
                todoFactory.msgError(data.Msg);
              } else {
                todoFactory.msgSuccess('Todo Deleted!');
              }
            }
            //fixme: may want to just iterate through the todos and delete locally
            $scope.getTodos();
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError('Error Deleting Todo:' + error);
          });
        };

        $scope.updateDone = function (todo){
              todoFactory.updateTodo(todo).then(function (data) {
                if (data){
                  if (todo.done){
                    todoFactory.msgSuccess('Well Done!');
                    //Call getTodos because a new todo may have been generated (eg: completed a Monthly task)
                    /* following comment turns off camelcase check for this function.. so it'll be ignored */
                    /* jshint camelcase: false */
                    for(var iData=0; iData< data.length;iData++){
                      if (todo.todo_id === data[iData].todo_id){
                        //FixMe: Instead of looping through all the TodoS... Can JSON be Keyed..
                        for(var i=0;i<$scope.todos.length;i++){
                          if($scope.todos[i].todo_id === data[iData].todo_id){
                            $scope.todos[i].due_dt = data[iData].due_dt;
                            $scope.todos[i].glyph = data[iData].glyph;
                          }
                        }
                      } else {
                        $scope.todos.push(data[iData]);
                      }
                    }
                    //previously called getTodos but now update is returning the newly added
                    //$scope.getTodos();
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
                  $scope.todos[i].glyph = data.glyph;
                }
              }
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError('Error Saving:' + error);
          });
        };

        $scope.getTodos = function (){
          todoFactory.getTodos().then(function (data) {
            if (data){
              $scope.todos = data;
            }
          }, function(error) {
            // promise rejected, could be because server returned 404, 500 error...
            todoFactory.msgError('Error Retrieving Todos:' + error);
          });
        };
        $scope.getTodos();

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

