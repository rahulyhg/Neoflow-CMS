<?php
namespace Neoflow\Minify;

class JsMinifier extends AbstractMinifier
{

    /**
     * Minify Javascript code.
     *
     * @return self
     */
    public function minify(): AbstractMinifier
    {
        /* remove comments */
        $this->code = preg_replace('/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/', '', $this->code);

        /* remove tabs, spaces, newlines, etc. */
        $this->code = str_replace(["\r\n", "\r", "\t", "\n", '  ', '    ', '     '], '', $this->code);

        /* remove other spaces before/after ) */
        $this->code = preg_replace(['(( )+\))', '(\)( )+)'], ')', $this->code);

        return $this;
    }
}
