<?php
namespace Terminus;

use Terminus\Site;
use Terminus\Environment;

class Deploy {
  public $from;
  public $cc = false;
  public $annotation = "Deployed from terminus";
  public $deploy_code = true;
  public $updatedb = true;
  public $clone_files = true;
  public $clone_db = false;
  public $env;

  public function __construct(Environment $env, $params = array()) {
    if (!empty($params)) {
      foreach ($params as $key => $value) {
        if (property_exists($this,$key)) {
          $this->$key = $value;
        }
      }
    }
    $this->env = $env;
    return $this;
  }

  public function run() {
    $workflow = new EnvironmentWorkflow('deploy','sites',$this->env);
    $workflow->setMethod('POST');
    $workflow->setParams(array(
        'annotation' => $this->annotation,
        'clear_cache' => $this->cc,
        'updatedb' => $this->updatedb,
    ));
    $workflow->start()->wait();
    return $workflow->status(); 
  }
}
