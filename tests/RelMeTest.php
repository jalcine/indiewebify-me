<?php

namespace IndieWeb;

use PHPUnit_Framework_TestCase;

/**
 * RelMeTest
 *
 * @author barnabywalters
 */
class RelMeTest extends PHPUnit_Framework_TestCase {
	public function testUnparseUrl() {
		$this->assertEquals('http://example.com/', unparseUrl(parse_url('http://example.com')));
		$this->assertEquals('http://example.com/?thing&amp;more', unparseUrl(parse_url('http://example.com?thing&amp;more')));
	}
	
	public function testNormaliseUrl() {
		$this->assertEquals('http://example.com/', normaliseUrl('http://example.com'));
		$this->assertEquals('http://example.com/?thing=1', normaliseUrl('http://example.com?thing=1'));
	}
	
	public function testHttpParseHeaders() {
		$test = <<<EOT
content-type: text/html; charset=UTF-8
Server: Funky/1.0
Set-Cookie: foo=bar
Set-Cookie: baz=quux
Folded: works
	too
EOT;
		$expected = array(
			'Content-Type' => 'text/html; charset=UTF-8',
			'Server' => 'Funky/1.0',
			'Set-Cookie' => array('foo=bar', 'baz=quux'),
			'Folded' => "works\r\n\ttoo"
		);
		$result = http_parse_headers($test);
		$this->assertEquals($expected, $result);
	}
	
	/** @group network */
	public function testFollowOneRedirect() {
		$this->assertEquals('https://brennannovak.com/', followOneRedirect('http://brennannovak.com'));
	}
	
	public function testRelMeDocumentUrlHandlesNoRedirect() {
		$chain = mockFollowOneRedirect(array(null));
		$meUrl = normaliseUrl('http://example.com');
		list($url, $isSecure, $previous) = relMeDocumentUrl($meUrl, $chain);
		$this->assertEquals($meUrl, $url);
		$this->assertTrue($isSecure);
		$this->assertCount(0, $previous);
	}
}