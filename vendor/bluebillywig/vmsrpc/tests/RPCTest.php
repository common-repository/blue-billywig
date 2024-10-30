<?php

namespace BlueBillywig\VMSRPC;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Exception;
use DOMDocument;

final class RPCTest extends TestCase
{
    protected static $config;
    protected static $cleanupMediaClips = array();

    protected function setUp()
    {
        $config_file = dirname(__FILE__) . '/config.yaml';
        $this->assertFileExists($config_file);
        self::$config = Yaml::parseFile($config_file);
    }

    public function testCanBeCreated()
    {
        // Try to create an instance with the rpctoken.
        try {
            $this->assertInstanceOf(
                RPC::class,
                new RPC('https://'.self::$config['publication'].'.bbvms.com', null, null, self::$config['rpctoken'])
            );
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
        // Try to create an instance with the username and password.
        try {
            $this->assertInstanceOf(
                RPC::class,
                new RPC('https://'.self::$config['publication'].'.bbvms.com', self::$config['user'], self::$config['password'], null)
            );
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testDoActionCreateMediaClip()
    {
        try {
            if (self::$config['useRPCTokenForUnitTests']) {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', null, null, self::$config['rpctoken']);
            }
            else {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', self::$config['user'], self::$config['password'], null);
            }
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
        $entity = 'mediaclip';
        $action = 'put';
        $arProps = array('xml' => '<media-clip title="php-unit-test-clip" status="draft"></media-clip>');
        try {
            $response = $rpc->doAction($entity, $action, $arProps);
            $this->assertArrayHasKey('code', $response);
            $this->assertArrayHasKey('id', $response);
            $this->assertEquals(200, $response['code']);
            self::$cleanupMediaClips[] = $response['id'];
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testXmlCreateDeleteMediaClip()
    {
        try {
            if (self::$config['useRPCTokenForUnitTests']) {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', null, null, self::$config['rpctoken']);
            }
            else {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', self::$config['user'], self::$config['password'], null);
            }
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
        // Create a media-clip.
        try {
            $entity = 'mediaclip';
            $objectId = null;
            $arProps = array(
                'action' => 'put',
                'xml' => '<media-clip title="php-unit-test-clip" status="draft"></media-clip>'
            );
            $response = $rpc->xml($entity, $objectId, $arProps);
            $expected = new DOMDocument;
            $expected->loadXML('<response code="" error="" id=""></response>');
            $actual = new DOMDocument;
            $actual->loadXML($response);
            // Actual xml structure of the response is something like <response code="" error="" id=""><messages time="" context="">Foo Bar<message></message></messages></response>.
            // However, since the amount of messages are unknown and we're only interested in the response code anyways, the contents of <response code="" error="" id=""></response>
            // are removed and only the response tag is checked.
            while ($actual->getElementsByTagName('response')->item(0)->hasChildNodes()) {
                $actual->getElementsByTagName('response')->item(0)->removeChild($actual->getElementsByTagName('response')->item(0)->childNodes->item(0));
            }
            $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, true);
            $responsecode = (int) $actual->getElementsByTagName('response')->item(0)->getAttribute('code');
            if ($responsecode !== 200) {
                $this->fail('Failed to successfully create media-clip. Response: '.$response);
            }
            $clipid = $actual->getElementsByTagName('response')->item(0)->getAttribute('id');
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
        // Delete the created media-clip.
        try {
            $entity = 'mediaclip';
            $objectId = $clipid;
            $arProps = array(
                'action' => 'delete',
            );
            $response = $rpc->xml($entity, $objectId, $arProps);
            $expected = new DOMDocument;
            $expected->loadXML('<response code="" error=""></response>');
            $actual = new DOMDocument;
            $actual->loadXML($response);
            // Actual xml structure of the response is something like <response code="" error="" id=""><messages time="" context="">Foo Bar<message></message></messages></response>.
            // However, since the amount of messages are unknown and we're only interested in the response code anyways, the contents of <response code="" error="" id=""></response>
            // are removed and only the response tag is checked.
            while ($actual->getElementsByTagName('response')->item(0)->hasChildNodes()) {
                $actual->getElementsByTagName('response')->item(0)->removeChild($actual->getElementsByTagName('response')->item(0)->childNodes->item(0));
            }
            $this->assertEqualXMLStructure($expected->firstChild, $actual->firstChild, true);
            $responsecode = (int) $actual->getElementsByTagName('response')->item(0)->getAttribute('code');
            if ($responsecode !== 200) {
                $this->fail('Failed to successfully delete media-clip. Response: '.$response);
            }
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testJsonSearchMediaclips()
    {
        try {
            if (self::$config['useRPCTokenForUnitTests']) {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', null, null, self::$config['rpctoken']);
            }
            else {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', self::$config['user'], self::$config['password'], null);
            }
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
        $entity = 'search';
        $objectId = null;
        $arProps = array(
            'query' => 'type:mediaclip',
            'limit' => '3',
            'sort' => 'createddate desc'
        );
        try {
            $response = $rpc->json($entity, $objectId, $arProps);
            $this->assertJson($response);
            $response = json_decode($response, true);
            $this->assertArrayHasKey('numfound', $response);
            $this->assertArrayHasKey('offset', $response);
            $this->assertArrayHasKey('count', $response);
            $this->assertArrayHasKey('items', $response);
            $this->assertArrayHasKey('facets', $response);
            $this->assertArrayHasKey('facet_queries', $response);
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testSapiGetPublication()
    {
        try {
            if (self::$config['useRPCTokenForUnitTests']) {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', null, null, self::$config['rpctoken']);
            }
            else {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', self::$config['user'], self::$config['password'], null);
            }
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
        $entity = 'publication';
        $objectId = null;
        $action = 'GET';
        $arProps = null;
        $entityAction = null;
        $urlParameters = null;
        try {
            $response = $rpc->sapi($entity, $objectId, $action, $arProps, $entityAction, $urlParameters);
            $this->assertJson($response);
            $response = json_decode($response, true);
            $this->assertArrayHasKey('id', $response);
            $this->assertArrayHasKey('status', $response);
            $this->assertArrayHasKey('type', $response);
            $this->assertArrayHasKey('name', $response);
            $this->assertArrayHasKey('label', $response);
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testUriGetPublication()
    {
        try {
            if (self::$config['useRPCTokenForUnitTests']) {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', null, null, self::$config['rpctoken']);
            }
            else {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', self::$config['user'], self::$config['password'], null);
            }
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
        $apiEntityUrl = 'sapi/publication';
        $qs = null;
        $arProps = null;
        try {
            $response = $rpc->uri($apiEntityUrl, $qs, $arProps);
            $this->assertJson($response);
            $response = json_decode($response, true);
            $this->assertArrayHasKey('id', $response);
            $this->assertArrayHasKey('status', $response);
            $this->assertArrayHasKey('type', $response);
            $this->assertArrayHasKey('name', $response);
            $this->assertArrayHasKey('label', $response);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    public function testCalculateRequestToken()
    {
        if (self::$config['useRPCTokenForUnitTests']) {
            try {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', null, null, self::$config['rpctoken']);
            } catch(Exception $e) {
                $this->fail($e->getMessage());
            }
            $token = $rpc->calculateRequestToken();
            if (!is_string($token)) {
                $this->fail("Token should be of type 'string'.");
            }
            if (strlen($token) !== 44) {
                $this->fail("Token should be 44 characters long.");
            }
            if (strpos($token, '-') !== 3) {
                $this->fail("Token should contains a '-' on the 4th position.");
            }
            if (!is_numeric(substr($token, 0, 3))) {
                $this->fail("Token's first 3 characters should be a number.");
            }
        }
    }

    public function testSapiCleanupMediaClips()
    {
        try {
            if (self::$config['useRPCTokenForUnitTests']) {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', null, null, self::$config['rpctoken']);
            }
            else {
                $rpc = new RPC('https://'.self::$config['publication'].'.bbvms.com', self::$config['user'], self::$config['password'], null);
            }
        } catch(Exception $e) {
            $this->fail($e->getMessage());
        }
        $results = array();
        $fail_messages = array();
        foreach (self::$cleanupMediaClips as $clipid) {
            $entity = 'mediaclip';
            $objectId = $clipid;
            $action = 'DELETE';
            $arProps = null;
            $entityAction = null;
            $urlParameters = null;
            try {
                $response = $rpc->sapi($entity, $objectId, $action, $arProps, $entityAction, $urlParameters);
                $this->assertJson($response);
                $response = json_decode($response, true);
                $this->assertArrayHasKey('type', $response);
                $this->assertArrayHasKey('code', $response);
                $this->assertArrayHasKey('error', $response);
                $this->assertArrayHasKey('body', $response);
                $results[$clipid] = true;
            } catch(Exception $e) {
                $results[$clipid] = false;
                $fail_messages[$clipid] = $e->getMessage();
            }
        }
        if (in_array(false, $results)) {
            $this->fail(json_encode($fail_messages));
        }
    }
}