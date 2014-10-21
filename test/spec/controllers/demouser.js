'use strict';

describe('Controller: DemoUserCtrl', function () {
  var $scope;
  var ROOTScope, ctrl, $timeout;
  var todoFactoryMOCK;
  var rtnDatagetDemoUser = {'email':'demo102@yahoo.com','password':'demopassword'};

  // This function will be called before every "it" block. This should be used to "reset" state for your tests.
  beforeEach(function (){
    // Create a "spy object" for our Service.
    /*global jasmine */

    todoFactoryMOCK = jasmine.createSpyObj('todoFactory', ['getDemoUser']);
    module('todoApp');
    inject(function($httpBackend, $rootScope, $controller, $q, _$timeout_) {

      //needed since angulartics kicks off another unexpected call to main.html and you get the error. Error: Unexpected request: GET views/main.html
      $httpBackend.whenGET('views/main.html').respond([]);

      $scope = $rootScope.$new();
      ROOTScope = $rootScope;
      // $q.when creates a resolved promise... values in When are what the service should return...

      todoFactoryMOCK.getDemoUser.andReturn($q.when(rtnDatagetDemoUser));

      $timeout = _$timeout_;
      ctrl = $controller('DemoUserCtrl', {
        $scope: $scope,
        ROOTScope: $rootScope,
        todoFactory: todoFactoryMOCK
      });
    });
  });


  it('should call function getDemoUser on the todoFactory and set values on scope', function (){
    // call the function
    $scope.getDemoUser();
    // assert that it called the service method.
    expect(todoFactoryMOCK.getDemoUser).toHaveBeenCalled();
    // call $timeout.flush() to flush the unresolved dependency from our service.
    $timeout.flush();
    // assert that it set $scope correctly
    expect($scope.user).toEqual(rtnDatagetDemoUser);

  });

});
