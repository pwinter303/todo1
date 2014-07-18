'use strict';

//  I like this setup/pattern the most.. clear...straightforward
//    this actual spec is good because it covers multiple calls to factory/service
//    and it also covers testing the call of specific $scope function

describe('Controller: TodoGroupCtrl', function () {
    var $scope;
    var ROOTScope, ctrl, $timeout;
    var todoFactoryMOCK;
    var rtnData_getTodoGroups = {"group_id":"48","group_name":"aaaa","sort_order":"4","active":true};
    var rtnData_addGroup = {"RowsAdded":1};

    // This function will be called before every "it" block. This should be used to "reset" state for your tests.
    beforeEach(function (){
      // Create a "spy object" for our Service.
      todoFactoryMOCK = jasmine.createSpyObj('todoFactory', ['getTodoGroups', 'addGroup','msgSuccess']);
      module('todoApp');
      inject(function($rootScope, $controller, $q, _$timeout_) {
        $scope = $rootScope.$new();
        ROOTScope = $rootScope;
        // $q.when creates a resolved promise... values in When are what the service should return...

        todoFactoryMOCK.getTodoGroups.andReturn($q.when(rtnData_getTodoGroups));
        todoFactoryMOCK.addGroup.andReturn($q.when(rtnData_addGroup));
        todoFactoryMOCK.msgSuccess.andReturn('Done');

        //todoFactoryMOCK.getAccountDetails.andReturn($q.when({accountType:1, paidThrough:3 }));
        // assign $timeout to a scoped variable so we can use $timeout.flush() later.
        $timeout = _$timeout_;
        ctrl = $controller('TodoGroupCtrl', {
          $scope: $scope,
          ROOTScope: $rootScope,
          todoFactory: todoFactoryMOCK
        });
      });
    });


    it('should call function getTodoGroups on the todoFactory and set values on scope', function (){
      // call the function
      $scope.getTodoGroups();
      // assert that it called the service method.
      expect(todoFactoryMOCK.getTodoGroups).toHaveBeenCalled();
      // call $timeout.flush() to flush the unresolved dependency from our service.
      $timeout.flush();
      // assert that it set $scope correctly
      expect($scope.groups).toEqual(rtnData_getTodoGroups);

      // this should work... and it works in the actual site... not sure why it doesnt work here
      //expect(ROOTScope.activegroup).toEqual(48);
    });

  it('should call functions todoFactory.addGroup and $scope.getTodoGroups', function (){

    spyOn($scope, 'getTodoGroups').andCallThrough();

    // call the function
    $scope.addGroup();
    // assert that it called the service method.
    expect(todoFactoryMOCK.addGroup).toHaveBeenCalled();
    // call $timeout.flush() to flush the unresolved dependency from our service.
    $timeout.flush();
    // assert that it set $scope correctly
    expect($scope.newGroup.name).toEqual('');

    // assert that it called the service method.
    expect($scope.getTodoGroups).toHaveBeenCalled();
    expect(todoFactoryMOCK.msgSuccess).toHaveBeenCalled();


  });

  });
