'use strict';

describe('Controller: AcctdetailCtrl ', function () {
    var $scope, ctrl, $timeout;
    var todoFactoryMOCK;

    // This function will be called before every "it" block. This should be used to "reset" state for your tests.
    beforeEach(function (){
      // Create a "spy object" for our Service.
      /*global jasmine */
      todoFactoryMOCK = jasmine.createSpyObj('todoFactory', ['getAccountPeriod','getEmail']);
      module('todoApp');
      inject(function($rootScope, $controller, $q, _$timeout_) {
        $scope = $rootScope.$new();
        /* following comment turns off camelcase check for this function.. so it'll be ignored */
        /* jshint camelcase: false */
        // $q.when creates a resolved promise... values in When are what the service should return...
        todoFactoryMOCK.getAccountPeriod.andReturn($q.when([{description:'Trial (Premium)',begin_dt:'2014-07-29',end_dt:'2014-08-29'}]));
        todoFactoryMOCK.getEmail.andReturn($q.when({email: 'fakeuser@yahoo.com'}));
        // assign $timeout to a scoped variable so we can use $timeout.flush() later.
        $timeout = _$timeout_;
        ctrl = $controller('AcctdetailCtrl', {
          $scope: $scope,
          todoFactory: todoFactoryMOCK
        });
      });
    });


    it('should call function getAccountPeriod on the todoFactory and set values on scope', function (){
      // call the function
      $scope.getAccountPeriod();
      // assert that it called the service method.
      expect(todoFactoryMOCK.getAccountPeriod).toHaveBeenCalled();
      // call $timeout.flush() to flush the unresolved dependency from our service.
      $timeout.flush();
      // assert that it set $scope correctly
      expect($scope.accountPeriods).toBeDefined();
      //expect($scope.paidThrough).toEqual(3);
    });

    it('should call function getEmail on the todoFactory and set values on scope', function (){
      // call the function
      $scope.getEmail();
      // assert that it called the service method.
      expect(todoFactoryMOCK.getEmail).toHaveBeenCalled();
      // call $timeout.flush() to flush the unresolved dependency from our service.
      $timeout.flush();
      // assert that it set $scope correctly
      expect($scope.email).toEqual('fakeuser@yahoo.com');
    });
  });
