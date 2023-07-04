<?php

namespace App\Utilities;

use Stringable;

/**
 * Sanitizes HTML content.
 * Removes specified HTML tags and attributes along with their content
 *
 * @author Wali Razzaq
 */
class HTMLSanitizer implements Stringable
{
    public function __construct(private string $content, private array $tags, private array $attributes)
    {
    }

    public function tags(...$tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    public function attributes(...$attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    protected function cleanup(): self
    {
        //Takes care of nested tags as well
        $tagWithContentPattern = function (string $tag) {
            $tag = preg_quote($tag);

            return '/<\s*'.$tag.'\b[^<]*(?:(?!<\/'.$tag.'>)<[^<]*)*<\/'.$tag.'>/i';
        };

        foreach ($this->tags as $tag) {
            $this->content = preg_replace($tagWithContentPattern($tag), '', $this->content);
        }

        //Takes care of double(\x22) and single(\x27) quotes
        $attributeWithContentPattern = fn (string $attribute) => '/'.preg_quote($attribute).'\s*=\s*([\x22\x27])([\s\S]*?)\1/i';
        foreach ($this->attributes as $attribute) {
            $this->content = preg_replace($attributeWithContentPattern($attribute), '', $this->content);
        }

        return $this;
    }

    public function getContent(): string
    {
        return $this->cleanup()->content;
    }

    public function __toString(): string
    {
        return $this->getContent();
    }
}
