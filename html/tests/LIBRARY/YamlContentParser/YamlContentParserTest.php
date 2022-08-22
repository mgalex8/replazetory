<?php
namespace Tests\LIBRARY\YamlContentParser;

use Tests\ProjectTestCase;

final class YamlContentParserTest extends ProjectTestCase
{
    public function test_GetTextContentFilter(): void
    {
        $filter = new \App\Bundle\YamlReplacerParser\Filters\GetTextContentFilter();
        $text = "<div class=\"test\">Nody<span>";
        $same = "Nody";
        $this->assertEquals($same, $filter->filter($text));
    }
//
//    public function test_TrimContentFilter(): void
//    {
//        $filter = new \App\Bundle\YamlReplacerParser\Filters\TrimContentFilter();
//        $text = "\n\r\<div class=\"test\"> Homemade </div>\t\t\t<span> Nody <span>\r\n \t ";
//        $same = "<div class=\"test\"> Homemade </div>\t\t\t<span> Nody <span>";
//        $this->assertSame($same, $filter->filter($text));
//    }
}