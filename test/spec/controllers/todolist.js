'use strict';

describe('Controller: TodolistCtrl', function () {
  var $scope;
  var ROOTScope, ctrl, $timeout;
  var todoFactoryMOCK;
  var getTodosDATA = {'group_id':'48','group_name':'aaaa','sort_order':'4','active':true};
  var addGroupDATA = {'RowsAdded':1};

  // This function will be called before every "it" block. This should be used to "reset" state for your tests.
  beforeEach(function (){
    // Create a "spy object" for our Service.
    /*global jasmine */
    todoFactoryMOCK = jasmine.createSpyObj('todoFactory', ['getTodos', 'addTodo','msgSuccess']);
    module('todoApp');
    inject(function($rootScope, $controller, $q, _$timeout_) {
      $scope = $rootScope.$new();
      ROOTScope = $rootScope;
      // $q.when creates a resolved promise... values in When are what the service should return...

      todoFactoryMOCK.getTodos.andReturn($q.when(getTodosDATA));
      todoFactoryMOCK.addTodo.andReturn($q.when(addGroupDATA));
      todoFactoryMOCK.msgSuccess.andReturn('Done');

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

});
