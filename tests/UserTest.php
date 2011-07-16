<?php
/**
 * Tests PeerIndex Common
 *
 * @group peerindex
 *
 * @package    PeerIndex
 * @author     Aaron Ott <aaron.ott@gmail.com>
 * @copyright  2011 Aaron Ott
 */
require_once 'PeerIndex.php';

class PeerIndexTest extends PHPUnit_Framework_TestCase
{

  protected $PeerIndex;
  protected $User;

  public function setUp()
  {
    $this->PeerIndex = new PeerIndex;
    $this->User = 'aaronott';
  }

  /**
	 * Tests PeerIndex
	 *
	 * Tests getting the information for a single user
	 *
	 * @test
	 */
  public function testGetUser()
  {
    $response = $this->PeerIndex->getUser($this->User);

    $this->assertTrue($this->PeerIndex->callInfo['http_code'] === 200);
    $this->assertObjectHasAttribute('topics', $response);
    $this->assertSame($response->twitter, $this->User);
  }
}
?>
