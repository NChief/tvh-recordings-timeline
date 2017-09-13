<?php
class Tvh {
  /* AUTH */
  const USER = '';
  const PASS = '';

  /* URLs */
  const BASE = 'http://host:9981';

  const MUX_INDEX = 8; // Index in params array where you find mux.

  const UPCOMMING_LIST = self::BASE . '/api/dvr/entry/grid_upcoming';
  const CHANNEL_LIST = self::BASE . '/api/channel/grid';
  const SERVICE_LIST = self::BASE . '/api/service/list';

  const SERVICE_MUX = 'service_mux.json';
  
  public $channelList = null;
  public $upcommingList = null;
  public $serviceToMux = null;
  
  function __construct($forceCreateServiceToMux = false) {
    $this->channelList = $this->_getApiData(self::CHANNEL_LIST);
    $this->upcommingList = $this->_getApiData(self::UPCOMMING_LIST);
    if(file_exists(self::SERVICE_MUX) && $forceCreateServiceToMux === false)
      $this->serviceToMux = $this->_getApiData(self::SERVICE_MUX);
    else
      $this->_createServiceToMux();
  }
  
  private function _getApiData($url) {
    $postdata = http_build_query(
        array(
            'limit' => 100,
        )
    );
    
    $context = stream_context_create(array(
      'http' => array(
        'method'  => 'POST',
        'header'  => array("Authorization: Basic " . base64_encode(self::USER.':'.self::PASS), "Content-type: application/x-www-form-urlencoded"),
        'content' => $postdata
      )
    ));

    return json_decode(file_get_contents($url, false, $context));
  }
  
  private function _createServiceToMux() {
    $streamData = $this->_getApiData(self::SERVICE_LIST);
    $this->serviceToMux = array();
    foreach($streamData->entries as $entry) {
      $this->serviceToMux[$entry->id] = $entry->params[self::MUX_INDEX]->value;
    }
    file_put_contents(self::SERVICE_MUX, json_encode($this->serviceToMux));
  }
  
  public function serviceIdToMux($id) {
    return $this->serviceToMux->{$id};
  }
  
  public function channelIdToMux($id) {
    $muxs = array();
    foreach($this->channelList->entries as $channel) {
      if($id == $channel->uuid) {
        //return serviceIdToMux($channel->services[0]);
        foreach($channel->services as $service) {
          $muxs[] = $this->serviceIdToMux($service);
        }
      }
    }
    return $muxs;
  }
  
  public function getUpcomming() {
    return $this->upcommingList;
  }
}
