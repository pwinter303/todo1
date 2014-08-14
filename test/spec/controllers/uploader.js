'use strict';

describe('Controller: FileUploadCtrl', function () {
  var $scope;
  var ROOTScope, ctrl, $timeout;
  var uploadManagerMOCK;
  //fixme: put real data here...
  var uploadDATA = {'group_id':'48','group_name':'aaaa','sort_order':'4','active':true};
  //var deleteBatchDATA = {'RowsAdded':1};

  // This function will be called before every "it" block. This should be used to "reset" state for your tests.
  beforeEach(function (){
    // Create a "spy object" for our Service.
    /*global jasmine */

    uploadManagerMOCK = jasmine.createSpyObj('uploadManager', ['upload']);
    module('todoApp');
    inject(function($httpBackend, $rootScope, $controller, $q, _$timeout_) {
      //needed since angulartics kicks off another unexpected call to main.html and you get the error. Error: Unexpected request: GET views/main.html
      $httpBackend.whenGET('views/main.html').respond([]);

      $scope = $rootScope.$new();
      ROOTScope = $rootScope;
      // $q.when creates a resolved promise... values in When are what the service should return...

      uploadManagerMOCK.upload.andReturn($q.when(uploadDATA));

      // assign $timeout to a scoped variable so we can use $timeout.flush() later.
      $timeout = _$timeout_;
      ctrl = $controller('FileUploadCtrl', {
        $scope: $scope,
        ROOTScope: $rootScope,
        uploadManager: uploadManagerMOCK
      });
    });
  });


  it('should call getBatches() which should call todoFactory.getBatches and set values on scope', function (){
    // call the function
    $scope.upload();
    // assert that it called the service method.
    expect(uploadManagerMOCK.upload).toHaveBeenCalled();
    // call $timeout.flush() to flush the unresolved dependency from our service.
    $timeout.flush();
    // assert that it set $scope correctly
    expect($scope.files).toEqual([]);
  });

});
