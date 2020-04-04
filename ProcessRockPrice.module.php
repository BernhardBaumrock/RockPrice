<?php namespace ProcessWire;
// info snippet
class ProcessRockPrice extends Process {
  public static function getModuleInfo() {
    return [
      'title' => 'RockPrice dev module',
      'version' => '0.0.1',
      'summary' => '',
      'icon' => 'code',
      'requires' => [],
      'installs' => [],
      
      // page that you want created to execute this module
      'page' => [
        'name' => 'rockprice',
        'parent' => 'setup', 
        'title' => 'RockPrice DEV'
      ],
    ];
  }

  public function execute() {
    $this->headline('RockPrice');
    $this->browserTitle('RockPrice');
    /** @var InputfieldForm $form */
    $form = $this->modules->get('InputfieldForm');
    
    $form->add([
      'type' => 'RockPrice',
      'name' => 'test',
      'label' => 'foo',
      'value' => 'bar',
      'taxStep' => '0.01',
      'taxSelect' => "0\n10\n19\n20",
      'notes' => 'For testing and developing the UI',
    ]);
    
    return $form->render();
  }
}