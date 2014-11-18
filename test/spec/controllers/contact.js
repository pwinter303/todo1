'use strict';

describe('Controller: ContactCtrl', function () {
  var $scope;
  var ROOTScope, ctrl, $timeout;
  var todoFactoryMOCK;
  var contactSubmitDATA = {'msg':'Thanks'};

  // This function will be called before every "it" block. This should be used to "reset" state for your tests.
  beforeEach(function (){
    // Create a "spy object" for our Service.
    /*global jasmine */
    todoFactoryMOCK = jasmine.createSpyObj('todoFactory', ['contactSubmit','msgSuccess', 'msgError']);
    module('todoApp');
    inject(function($httpBackend, $rootScope, $controller, $q, _$timeout_) {
      //needed since angulartics kicks off another unexpected call to main.html and you get the error. Error: Unexpected request: GET views/main.html
      $httpBackend.whenGET('views/todolist.html').respond([]);
      //added this since contactSubmit can go to two places depending on if the users is logged in...
      $httpBackend.whenGET('views/main.html').respond([]);

      $scope = $rootScope.$new();
      ROOTScope = $rootScope;
      // $q.when creates a resolved promise... values in When are what the service should return...

      todoFactoryMOCK.contactSubmit.andReturn($q.when(contactSubmitDATA));

      //todoFactoryMOCK.getAccountDetails.andReturn($q.when({accountType:1, paidThrough:3 }));
      // assign $timeout to a scoped variable so we can use $timeout.flush() later.
      $timeout = _$timeout_;
      ctrl = $controller('ContactCtrl', {
        $scope: $scope,
        ROOTScope: $rootScope,
        todoFactory: todoFactoryMOCK
      });
    });
  });


  it('should call function contactSubmit on the todoFactory and call MsgSuccess', function (){
    // call the function
    $scope.contactSubmit();
    // assert that it called the service method.
    expect(todoFactoryMOCK.contactSubmit).toHaveBeenCalled();
    // call $timeout.flush() to flush the unresolved dependency from our service.
    $timeout.flush();
    // assert that it set $scope correctly
    expect(todoFactoryMOCK.msgSuccess).toHaveBeenCalled();
  });
});