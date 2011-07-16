<?php
/**
 * API Implementation for PeerIndex
 *
 * @author      Aaron Ott <aaron.ott@gmail.com>
 * @copyright   2011 Aaron Ott
 * @link http://dev.peerindex.net
 */
class PeerIndex {

  /**
   * API-Key required to make the calls
   *
   * @access private
   */
  private $APIKEY = '';

  /**
   * URI to call for service
   *
   * @access protected
   */
  protected $uri = 'http://api.peerindex.net';

  /**
   * Version of the API
   *
   * @access private
   */
  private $version = 1;


  public $lastCall = '';
  public $lastResponse = '';
  public $callInfo = '';

  /**
   * Send the HTTP Request to gather the information from the API
   *
   * @access protected
   * @param
   *   String endpoint of the API Call ('show')
   *
   * @param  
   *   An array of data to be passed to the API. Currently this is only a list
   *   of usernames
   *
   * @throws
   *   PeerIndex_Exception
   *
   * @returns
   *   Object containing response data
   */
  protected function send_request($endpoint, array $data=array()) {
    $uri = $this->uri . '/' . $this->version . '/' . $endpoint . '.json';

    $data += array(
      'api_key' => $this->APIKEY,
    );

    $params = array();
    foreach ($data as $key => $val) {
      $params[] = $key . '=' . urlencode($val);
    }
    $uri .= '?' . implode('&', $params);

    $this->lastCall = $uri;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $uri);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    //curl_setopt($ch, CURLOPT_VERBOSE, TRUE);

    $this->lastResponse = curl_exec($ch);
    $this->callInfo = curl_getinfo($ch);

    if ($this->lastResponse === FALSE) {
      throw new PeerIndex_Exception('Curl error: ' . curl_error($ch), curl_errno($ch));
    }

    if ($this->callInfo['http_code'] != 200) {
      throw new PeerIndex_Exception('HTTP_CODE != 200: ' . $this->callInfo['http_code']);
    }

    curl_close($ch);
    return $this->_parse();
  }

  /**
   * Parse the output and return a PHP object containing the resulting data
   */
  protected function _parse() {
    return json_decode($this->lastResponse);
  }

  /**
   * Get the PeerIndex score for a single user.
   *
   * @param
   *   username
   */
  public function getUser($username) {
    return $this->send_request('profile/show', array('id' => $username));
  }

}

/**
 * Exception class
 */
class PeerIndex_Exception extends Exception {}
