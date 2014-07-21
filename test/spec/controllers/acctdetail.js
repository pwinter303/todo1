'use strict';

describe('Controller: AcctdetailCtrl ', function () {
    var $scope, ctrl, $timeout;
    var todoFactoryMOCK;

    // This function will be called before every "it" block. This should be used to "reset" state for your tests.
    beforeEach(function (){
      // Create a "spy object" for our Service.
      /*global jasmine */
      todoFactoryMOCK = jasmine.createSpyObj('todoFactory', ['getAccountDetails']);
      module('todoApp');
      inject(function($rootScope, $controller, $q, _$timeout_) {
        $scope = $rootScope.$new();
        // $q.when creates a resolved promise... values in When are what the service should return...
        todoFactoryMOCK.getAccountDetails.andReturn($q.when({accountType:1, paidThrough:3 }));
        // assign $timeout to a scoped variable so we can use $timeout.flush() later.
        $timeout = _$timeout_;
        ctrl = $controller('AcctdetailCtrl', {
          $scope: $scope,
          todoFactory: todoFactoryMOCK
        });
      });
    });


    it('should call function getAccountDetails on the todoFactory and set values on scope', function (){
      // call the function
      $scope.getAccountDetails();
      // assert that it called the service method.
      expect(todoFactoryMOCK.getAccountDetails).toHaveBeenCalled();
      // call $timeout.flush() to flush the unresolved dependency from our service.
      $timeout.flush();
      // assert that it set $scope correctly
      expect($scope.accountType).toEqual(1);
      expect($scope.paidThrough).toEqual(3);
    });
  });
