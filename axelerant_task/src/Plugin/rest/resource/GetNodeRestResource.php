<?php

namespace Drupal\axelerant_task\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\rest\ModifiedResourceResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\system\Form\SiteInformationForm;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Component\Serialization\Json;
use Drupal\node\Entity\Node;
use Psr\Log\LoggerInterface;

/**
 * Provides a resource to get node details .
 *
 * @RestResource(
 *   id = "get_node_rest_resource",
 *   label = @Translation("Get node rest resource"),
 *   uri_paths = {
 *     "canonical" = "/page_json/{api_key}/{id}"
 *   }
 * )
 */
class GetNodeRestResource extends ResourceBase {
  protected $currentUser;
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('axelerant_task'),
      $container->get('current_user'),
      $container->get('request_stack')->getCurrentRequest()

    );
  }
  public function get() {
    //load site information configration
    $config = \Drupal::config('system.site');
    //get value of site API key
    $site_api_key = $config->get('site_api_key_value');
    //get api key and nid from url
    $path = \Drupal::request()->getpathInfo();
    $arg  = explode('/',$path);
    $api_key = $arg[2];
    $nid = $arg[3];
    $response = 'Access Denied';
    //if API key match will load node and check type 
    if($api_key == $site_api_key){
        $node = Node::load($nid);
        if(!empty($node)) {
          $type = $node->bundle();
          //As per requrement we will sent node details only if type is page
          if($type == 'page')
            $response = $node;
        }
    }
    $response = new ResourceResponse($response);
    return $response;
  }

}