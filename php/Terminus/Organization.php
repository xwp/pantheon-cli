<?php
namespace Terminus;

use Terminus\User;
use Terminus\Site;

class Organization {

  public function __construct( $id_or_name ) {
    // if the org id is passed in then we need to fetch it from the user object
    if (is_string($id_or_name)) {
      $user = User::instance();
      $orgs = $user->organizations();
      foreach($orgs as $test) {
        if($test->id === $id_or_name || $test->organization->profile->name === $id_or_name) {
          $org = $test;
          break;
        }
      }
    }

    if (!isset($org)) {
      throw new \Terminus\Iterators\Exception("Could not find organization $id_or_name");
    }

    // hydrate the object
    $properties = get_object_vars($org);
    foreach (get_object_vars($org) as $key => $value) {
      if(!property_exists($this,$key)) {
        $this->$key = $properties[$key];
      }
    }

    return $this;
  }

  public function addSite( Site $site ) {
    $workflow = new Workflow('add_organization_site_membership', 'organizations', $this);
    $workflow->setParams(array('site_id' => $site->getId(), 'role' => 'team_member'));
    $workflow->setMethod("POST");
    $workflow->start();
    $workflow->wait();
    return $workflow;
  }

  public function removeSite( Site $site ) {
    $workflow = new Workflow('remove_organization_site_membership', 'organizations', $this);
    $workflow->setParams(array('site_id' => $site->getId()));
    $workflow->setMethod("POST");
    $workflow->start();
    $workflow->wait();
    return $workflow;
  }

  public function getSites() {
    $path = 'memberships/sites';
    $method = 'GET';
    $response = \Terminus_Command::request('organizations', $this->id, $path, $method);
    return $response['data'];
  }

  public function getId() {
    return $this->id;
  }

}
