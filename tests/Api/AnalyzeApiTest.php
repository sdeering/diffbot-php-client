<?php

namespace Swader\Diffbot\Test\Api;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;

class AnalyzeApiTest extends \PHPUnit_Framework_TestCase
{
    use setterUpper;

    protected $validMock;

    /**
     * @var \Swader\Diffbot\Api\Analyze
     */
    protected $apiWithMock;

    protected function setUp()
    {
        $diffbot = $this->preSetUp();

        $this->apiWithMock = $diffbot->createAnalyzeAPI('https://article-mock.com');
    }

    protected function getValidMock()
    {
        if (!$this->validMock) {
            $this->validMock = new MockHandler([
                new Response(200, [],
                    file_get_contents(__DIR__ . '/../Mocks/Articles/hi_quicktip_basic.json'))
            ]);
        }

        return $this->validMock;
    }

    public function testBuildUrlNoCustomFields()
    {
        $url = $this
            ->apiWithMock
            ->buildUrl();
        $expectedUrl = 'https://api.diffbot.com/v3/analyze?token=demo&url=https%3A%2F%2Farticle-mock.com&timeout=30000';
        $this->assertEquals($expectedUrl, $url);
    }

    public function testBuildUrlOneCustomField()
    {
        $url = $this
            ->apiWithMock
            ->setMeta(true)
            ->buildUrl();
        $expectedUrl = 'https://api.diffbot.com/v3/analyze?token=demo&url=https%3A%2F%2Farticle-mock.com&timeout=30000&fields=meta';
        $this->assertEquals($expectedUrl, $url);
    }

    public function testBuildUrlTwoCustomFields()
    {
        $url = $this
            ->apiWithMock
            ->setMeta(true)
            ->setLinks(true)
            ->buildUrl();
        $expectedUrl = 'https://api.diffbot.com/v3/analyze?token=demo&url=https%3A%2F%2Farticle-mock.com&timeout=30000&fields=meta,links';
        $this->assertEquals($expectedUrl, $url);
    }

    public function testBuildUrlFourCustomFields()
    {
        $url = $this
            ->apiWithMock
            ->setMeta(true)
            ->setLinks(true)
            ->setBreadcrumb(true)
            ->setQuerystring(true)
            ->buildUrl();
        $expectedUrl = 'https://api.diffbot.com/v3/analyze?token=demo&url=https%3A%2F%2Farticle-mock.com&timeout=30000&fields=meta,links,breadcrumb,querystring';
        $this->assertEquals($expectedUrl, $url);
    }

    public function testBuildUrlOtherOptionsOnly()
    {
        $url = $this->apiWithMock
            ->setDiscussion(false)
            ->setMode('article')
            ->buildUrl();

        $expectedUrl = 'https://api.diffbot.com/v3/analyze?token=demo&url=https%3A%2F%2Farticle-mock.com&timeout=30000&discussion=false&mode=article';
        $this->assertEquals($expectedUrl, $url);
    }

    public function testBuildUrlOtherOptionsAndCustomFields()
    {
        $url = $this
            ->apiWithMock
            ->setMeta(true)
            ->setLinks(true)
            ->setBreadcrumb(true)
            ->setQuerystring(true)
            ->setDiscussion(false)
            ->setMode('product')
            ->buildUrl();
        $expectedUrl = 'https://api.diffbot.com/v3/analyze?token=demo&url=https%3A%2F%2Farticle-mock.com&timeout=30000&fields=meta,links,breadcrumb,querystring&discussion=false&mode=product';
        $this->assertEquals($expectedUrl, $url);
    }

    public function invalidModes()
    {
        return [
            ['foo'],
            ['bar'],
            ['post'],
            ['discussion']
        ];
    }

    /**
     * @dataProvider invalidModes
     * @param $mode
     */
    public function testInvalidMode($mode)
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->apiWithMock->setMode($mode);
    }

    public function validModes()
    {
        return [
            ['article'],
            ['product'],
            ['image']
        ];
    }

    /**
     * @dataProvider validModes
     * @param $mode
     */
    public function testValidMode($mode)
    {
        try {
            $this->apiWithMock->setMode($mode);
        } catch (\Exception $e) {
            $this->fail('Error should not have happened: ' . $e->getMessage());
        }
    }

}
