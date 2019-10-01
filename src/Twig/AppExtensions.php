<?php

namespace App\Twig;

use App\Utils\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtensions extends AbstractExtension
{


    private $parser;

    public function __construct(Markdown $parser)
    {
        $this->parser = $parser;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter("price", [$this, "formatPrice"]),
            new TwigFilter("markdown", [$this, "markdown"])
        ];
    }

    public function formatPrice(float $value, string $symbol = "€"): string
    {

        $final = number_format($value, 2, ",", " ");

        return "$final $symbol";
    }

    public function markdown(string $string): string
    {

        $markdowned = $this->parser->toHtml($string);

        return $markdowned;
    }
}
