'use strict';

describe('Controller: BatchesCtrl', function () {
  var $scope;
  var ROOTScope, ctrl, $timeout;
  var todoFactoryMOCK;
  //fixme: put real data here...
  var getBatchesDATA = {'group_id':'48','group_name':'aaaa','sort_order':'4','active':true};
  var deleteBatchDATA = {'RowsAdded':1};

  // This function will be called before every "it" block. This should be used to "reset" state for your tests.
  beforeEach(function (){
    // Create a "spy object" for our Service.
    /*global jasmine */
    todoFactoryMOCK = jasmine.createSpyObj('todoFactory', ['getBatches', 'deleteBatch','msgSuccess', 'msgError']);
    module('todoApp');
    inject(function($rootScope, $controller, $q, _$timeout_) {
      $scope = $rootScope.$new();
      ROOTScope = $rootScope;
      // $q.when creates a resolved promise... values in When are what the service should return...

      todoFactoryMOCK.getBatches.andReturn($q.when(getBatchesDATA));
      todoFactoryMOCK.deleteBatch.andReturn($q.when(deleteBatchDATA));
      todoFactoryMOCK.msgSuccess.andReturn('Done');

      // assign $timeout to a scoped variable so we can use $timeout.flush() later.
      $timeout = _$timeout_;
      ctrl = $controller('BatchesCtrl', {
        $scope: $scope,
        ROOTScope: $rootScope,
        todoFactory: todoFactoryMOCK
      });
    });
  });


  it('should call getBatches() which should call todoFactory.getBatches and set values on scope', function (){
    // call the function
    $scope.getBatches();
    // assert that it called the service method.
    expect(todoFactoryMOCK.getBatches).toHaveBeenCalled();
    // call $timeout.flush() to flush the unresolved dependency from our service.
    $timeout.flush();
    // assert that it set $scope correctly
    expect($scope.batches).toEqual(getBatchesDATA);
  });

});
