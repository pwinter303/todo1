'use strict';

describe('Controller: TodolistCtrl', function () {
  var $scope;
  var ROOTScope, ctrl, $timeout;
  var todoFactoryMOCK;
  var getTodosDATA = {'group_id':'48','group_name':'aaaa','sort_order':'4','active':true};
  var addTodosDATA = {'err':1};
  var deleteTodoDATA = {'RowsDeleted':1};

  // This function will be called before every "it" block. This should be used to "reset" state for your tests.
  beforeEach(function (){
    // Create a "spy object" for our Service.
    /*global jasmine */
    todoFactoryMOCK = jasmine.createSpyObj('todoFactory', ['getTodos', 'addTodo', 'deleteTodo', 'msgSuccess', 'msgError']);
    module('todoApp');
    inject(function($rootScope, $controller, $q, _$timeout_) {
      $scope = $rootScope.$new();
      ROOTScope = $rootScope;
      // $q.when creates a resolved promise... values in When are what the service should return...

      todoFactoryMOCK.getTodos.andReturn($q.when(getTodosDATA));
      todoFactoryMOCK.addTodo.andReturn($q.when(addTodosDATA));
      todoFactoryMOCK.deleteTodo.andReturn($q.when(deleteTodoDATA));

      // assign $timeout to a scoped variable so we can use $timeout.flush() later.
      $timeout = _$timeout_;
      ctrl = $controller('TodolistCtrl', {
        $scope: $scope,
        ROOTScope: $rootScope,
        todoFactory: todoFactoryMOCK
      });
    });
  });


  it('should call getTodos() which should call todoFactory.getTodos and set values on scope', function (){
    // call the function
    $scope.getTodos();
    // assert that it called the service method.
    expect(todoFactoryMOCK.getTodos).toHaveBeenCalled();
    // call $timeout.flush() to flush the unresolved dependency from our service.
    $timeout.flush();
    // assert that it set $scope correctly
    expect($scope.todos).toEqual(getTodosDATA);
  });

  it('should call addTodos() which should call todoFactory.addTodos and set values on scope', function (){
    // call the function
    $scope.addTodo({task:'my new todo'});
//    // assert that it called the service method.
    expect(todoFactoryMOCK.addTodo).toHaveBeenCalled();
//    // call $timeout.flush() to flush the unresolved dependency from our service.
    $timeout.flush();
    // assert that it set $scope correctly
    expect(todoFactoryMOCK.msgError).toHaveBeenCalled();
//    I would like a testcase that would test the happy path... but got an ERROR:  "push is not a function" (push is used to add items to the scope array)
//    expect($scope.todos).toEqual(addTodosDATA);
//    expect($scope.newTodo.task).toEqual('');
  });

  it('should call deleteTodo() which should call todoFactory.deleteTodo and display msg and call getTodos', function (){

    spyOn($scope, 'getTodos').andCallThrough();

    // call the function
    $scope.deleteTodo();
    // assert that it called the service method.
    expect(todoFactoryMOCK.deleteTodo).toHaveBeenCalled();
    // call $timeout.flush() to flush the unresolved dependency from our service.
    $timeout.flush();
    expect(todoFactoryMOCK.msgSuccess).toHaveBeenCalled();
    expect($scope.getTodos).toHaveBeenCalled();
  });


});
