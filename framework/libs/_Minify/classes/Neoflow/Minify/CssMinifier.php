<?php

namespace Neoflow\Minify;

class CssMinifier extends AbstractMinifier
{
    /**
     * Minify CSS code.
     *
     * @return self
     */
    public function minify(): AbstractMinifier
    {
        // Remove comments
        $this->code = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $this->code);

        // Remove space after colons
        $this->code = str_replace(': ', ':', $this->code);

        // Remove whitespace
        $this->code = str_replace(["\r\n", "\r", "\n", "\t", '  ', '    ', '    '], '', $this->code);

        return $this;
    }
}
