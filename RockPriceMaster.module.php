<?php namespace ProcessWire;
/**
 * RockPrice Master Module for attaching hooks
 *
 * @author Bernhard Baumrock, 04.04.2020
 * @license Licensed under MIT
 * @link https://www.baumrock.com
 */
class RockPriceMaster extends WireData implements Module, ConfigurableModule {

  public static function getModuleInfo() {
    return [
      'title' => 'RockPriceMaster',
      'version' => '0.0.1',
      'summary' => 'RockPrice Master Module (autoload)',
      'autoload' => true,
      'singular' => true,
      'icon' => 'bolt',
      'requires' => [],
      'installs' => [
        'FieldtypeRockPrice',
        'InputfieldRockPrice',
      ],
    ];
  }

  public function init() {
    $this->addHookBefore("ProcessPageView::execute", $this, "tplSaveHook");
  }

  /**
   * Save template to cache
   */
  public function tplSaveHook(HookEvent $event) {
    if(!$this->config->ajax) return;
    if(!$this->user->isLoggedin()) return;

    $data = (object)[
      'user' => $this->user->id,
      'name' => strtolower($this->input->post('name', 'fieldName')),
      'title' => $this->input->post('name', 'text'),
      'json' => "_".$this->input->post('json', 'text'), // save json as string
      'field' => $this->input->post('field', 'fieldName'),
    ];

    if(!$data->user) return;
    if(!$data->name) return;
    if(!$data->field) return;

    $key = "RP{$data->user}_{$data->field}_{$data->name}#";
    $this->cache->save($key, (array)$data);
    die($key);
  }

  /**
  * Config inputfields
  * @param InputfieldWrapper $inputfields
  */
  public function getModuleConfigInputfields($inputfields) {
    return $inputfields;
  }
}