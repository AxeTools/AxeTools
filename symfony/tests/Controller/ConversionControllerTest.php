<?php

namespace App\Tests\Controller;

use App\Utils\Type\DumpType;
use App\Utils\Type\SerializeType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConversionControllerTest extends WebTestCase {
    /**
     * @test
     *
     * @return void
     */
    public function convertFromSerialize() {
        // Arrange
        $client = static::createClient();

        // Execute
        $crawler = $client->request('GET', '/convert/fromSerialize');

        // Assert
        $this->assertResponseIsSuccessful();
        $buttonCrawlerNode = $crawler->selectButton('Convert');
        $form = $buttonCrawlerNode->form();
        $formName = $form->getName();
        $crawler = $client->submit($form, [
            $formName.'[data]' => 'a:0:{}',
            $formName.'[dump_type]' => DumpType::PRINT_R,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('.language-php');
        $this->assertEquals('Array ( )', $crawler->filter('.language-php')->innerText());
    }

    /**
     * @test
     *
     * @return void
     */
    public function convertFromJson() {
        // Arrange
        $client = static::createClient();

        // Execute
        $crawler = $client->request('GET', '/convert/fromJson');

        // Assert
        $this->assertResponseIsSuccessful();
        $buttonCrawlerNode = $crawler->selectButton('Convert');
        $form = $buttonCrawlerNode->form();
        $formName = $form->getName();
        $crawler = $client->submit($form, [
            $formName.'[data]' => '[]',
            $formName.'[dump_type]' => DumpType::PRINT_R,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('#convert-result');
        $this->assertEquals('Array ( )', $crawler->filter('#convert-result')->innerText());
    }

    /**
     * @test
     *
     * @dataProvider convertToStringDataProvider
     *
     * @return void
     */
    public function convertToString(string $input, string $serializer, string $expected) {
        // Arrange
        $client = static::createClient();

        // Execute
        $crawler = $client->request('GET', '/convert/toString');

        // Assert
        $this->assertResponseIsSuccessful();
        $buttonCrawlerNode = $crawler->selectButton('Convert');
        $form = $buttonCrawlerNode->form();
        $formName = $form->getName();
        $crawler = $client->submit($form, [
            $formName.'[data]' => $input,
            $formName.'[dump_type]' => $serializer,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('#convert-result');
        $this->assertEquals($expected, $crawler->filter('#convert-result')->innerText());
    }

    /**
     * @test
     *
     * @return void
     */
    public function convertFromYaml() {
        // Arrange
        $client = static::createClient();

        // Execute
        $crawler = $client->request('GET', '/convert/fromYaml');

        // Assert
        $this->assertResponseIsSuccessful();
        $buttonCrawlerNode = $crawler->selectButton('Convert');
        $form = $buttonCrawlerNode->form();
        $formName = $form->getName();

        $crawler = $client->submit($form, [
            $formName.'[data]' => 'test: test',
            $formName.'[dump_type]' => DumpType::PRINT_R,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('#convert-result');
        $this->assertEquals('Array ( [test] => test )', $crawler->filter('#convert-result')->innerText());
    }

    /**
     * @test
     *
     * @return void
     */
    public function convertFromUrl() {
        // Arrange
        $client = static::createClient();

        // Execute
        $crawler = $client->request('GET', '/convert/fromUrl');

        // Assert
        $this->assertResponseIsSuccessful();

        $buttonCrawlerNode = $crawler->selectButton('Convert');
        $form = $buttonCrawlerNode->form();
        $formName = $form->getName();

        $crawler = $client->submit($form, [
            $formName.'[data]' => 'This+Is+a+Test',
            $formName.'[dump_type]' => DumpType::PRINT_R,
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('#convert-result');
        $this->assertEquals('This Is a Test', $crawler->filter('#convert-result')->innerText());
    }

    /**
     * @return array<string, mixed>
     */
    public static function convertToStringDataProvider(): array {
        return [
            'json test' => ['$result = ["test" => "test"];', SerializeType::TYPE_JSON, '{"test":"test"}'],
            'serialize test' => ['$result = ["test" => "test"];', SerializeType::TYPE_SERIALIZE, 'a:1:{s:4:"test";s:4:"test";}'],
            'yaml test' => ['$result = ["test" => "test"];', SerializeType::TYPE_YAML, 'test: test'],
            'url array test' => ['$result = ["test" => "test"];', SerializeType::TYPE_URL, 'test=test'],
            'url string test' => ['$result = "This Is A Test";', SerializeType::TYPE_URL, 'This+Is+A+Test'],
        ];
    }
}
