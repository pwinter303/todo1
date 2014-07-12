'use strict';

describe('Controller: todoAuthenticateCtrl', function () {

  // load the controller's module
  beforeEach(module('todoApp'));

  var todoAuthenticateCtrl,  scope;

  // Initialize the controller and a mock scope
  beforeEach(inject(function ($controller, $rootScope) {
    scope = $rootScope.$new();
    todoAuthenticateCtrl = $controller('todoAuthenticateCtrl', {
      $scope: scope
    });
  }));


  it('should get login success',
    inject(function(authentication, $httpBackend) {

      $httpBackend.expect('POST', 'login.php')
        .respond(200, "[{ success : 'true', login : 1 }]");

      LoginService.login('test@test.com', 'password')
        .then(function(data) {
          expect(data.success).toBeTruthy();
        });

      $httpBackend.flush();
    }));


});
