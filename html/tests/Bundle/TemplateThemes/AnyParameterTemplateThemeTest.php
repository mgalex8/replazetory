<?php
namespace Tests\Bundle\TemplateThemes;

use App\Bundle\TemplateThemes\AnyParameterTemplateTheme;
use Tests\ProjectTestCase;

final class AnyParameterTemplateThemeTest extends ProjectTestCase
{
    /**
     * @return void
     * @throws \Exception
     */
    public function test_AnyParameterTemplateTheme_Constructor(): void
    {
        $name = 'basis';
        $path = $this->abs_path('/templates/theme/basis');
        $params = array(
            'title' => 'Title for basis',
            'description' => 'Description for basis',
            'categories' => 'category1|category2| category3 | category4 ',
            'tags' => 'tag1|tag2| tag3 | tag 4 | ',
            'keywords' => 'keyword1 | keyword2 | change apple| index & special`s | all news ',
            'preview_image' => 'https://image.com/2424.png',
            'author' => 'Author Name',
            'reviews' => '..{Data}..',
            'view_count' => 20022,
            'download_count' => 1260,
            'download_url' => 'https://dload.example-server.com/themes/basis.tar.gz',
            'colors' => 'red|gren',
        );
        $theme = new AnyParameterTemplateTheme($name, $path, $params);
        $theme->setKeywords('profit|change apple|index & special`s|all news');
        $additional_parameters = $theme->getAdditionalParameters();
        dump($additional_parameters);

        $this->assertEquals('basis', $theme->getName());
        $this->assertEquals($this->abs_path('/templates/theme/basis'), $theme->getPath());

        $this->assertIsArray($additional_parameters);
        $this->assertArrayHasKey('title', $additional_parameters);
        $this->assertArrayHasKey('description', $additional_parameters);
        $this->assertArrayHasKey('categories', $additional_parameters);
        $this->assertArrayHasKey('tags', $additional_parameters);
        $this->assertArrayHasKey('keywords', $additional_parameters);
        $this->assertArrayHasKey('preview_image', $additional_parameters);
        $this->assertArrayHasKey('author', $additional_parameters);
        $this->assertArrayHasKey('view_count', $additional_parameters);
        $this->assertArrayHasKey('download_count', $additional_parameters);
        $this->assertArrayHasKey('download_url', $additional_parameters);
        $this->assertArrayHasKey('colors', $additional_parameters);

        $this->assertEquals($additional_parameters['title'], 'Title for basis');
        $this->assertEquals($additional_parameters['description'], 'Description for basis');
//        $this->assertEquals($additional_parameters['categories'], 'category1|category2| category3 | category4 ');
//        $this->assertEquals($additional_parameters['tags'], 'tag1|tag2| tag3 | tag 4 | ');
//        $this->assertEquals($additional_parameters['keywords'], 'keyword1 | keyword2 | change apple| index & special`s | all news ');
        $this->assertEquals($additional_parameters['preview_image'], 'https://image.com/2424.png');
        $this->assertEquals($additional_parameters['author'], 'Author Name');
        $this->assertEquals($additional_parameters['view_count'], 20022);
        $this->assertEquals($additional_parameters['download_count'], 1260);
        $this->assertEquals($additional_parameters['download_url'], 'https://dload.example-server.com/themes/basis.tar.gz');
        $this->assertEquals($additional_parameters['colors'], 'red|gren');
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function test_AnyParameterTemplateTheme_Setters()
    {
        $name = 'basis';
        $path = $this->abs_path('/templates/theme/basis');
        $theme = new AnyParameterTemplateTheme($name, $path);

        $theme->setKeywords('keyword12 | keyword2 | change apple| index & special`s | all news ');
        $additional_parameters = $theme->getAdditionalParameters();
        dump($additional_parameters);

        $theme->setTags('tag1|tag2| tag3 | tag 4 | ');
        $additional_parameters = $theme->getAdditionalParameters();
        $this->assertIsArray($additional_parameters['tags']);
        $this->assertArrayContainsString($additional_parameters['tags'], 'tag1');
        $this->assertArrayContainsString($additional_parameters['tags'], 'tag2');
        $this->assertArrayContainsString($additional_parameters['tags'], 'tag3');
        $this->assertArrayContainsString($additional_parameters['tags'], 'tag4');

        $theme->setCategories('category1|category2| category3 | category4 ');
        $additional_parameters = $theme->getAdditionalParameters();
        $this->assertIsArray($additional_parameters['categories']);
        $this->assertArrayContainsString($additional_parameters['categories'], 'category1');
        $this->assertArrayContainsString($additional_parameters['categories'], 'category2');
        $this->assertArrayContainsString($additional_parameters['categories'], 'category3');
        $this->assertArrayContainsString($additional_parameters['categories'], 'category4');


    }

}