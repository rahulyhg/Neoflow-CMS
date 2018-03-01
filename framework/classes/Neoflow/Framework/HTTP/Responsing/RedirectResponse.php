<?php

namespace Neoflow\Framework\HTTP\Responsing;

class RedirectResponse extends Response
{
    /**
     * Constructor.
     *
     * @param string $url  Redirect URL
     * @param int    $code HTTP status code
     */
    public function __construct(string $url, int $code = 302)
    {
        $content = str_replace('[url]', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'), '<!DOCTYPE html>
                        <html>
                            <head>
                                <meta charset="UTF-8" />
                                <meta http-equiv="refresh" content="1;url=[url]" />
                                <title>Redirecting to [url]</title>
                            </head>
                            <body>
                                Redirecting to <a href="[url]">[url]</a>.
                            </body>
                        </html>');

        $this->setStatusCode($code);
        $this->setHeader('Location: '.$url);
        $this->setContent($content);
    }
}
