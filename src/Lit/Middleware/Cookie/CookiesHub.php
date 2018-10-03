<?php

declare(strict_types=1);

namespace Lit\Middleware\Cookie;

use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\SetCookies;
use Lit\Nimo\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface;

class CookiesHub extends AbstractMiddleware
{
    /**
     * @var Cookies
     */
    protected $requestCookies;
    /**
     * @var SetCookies
     */
    protected $responseCookies;

    /**
     * @return Cookies
     */
    public function getRequestCookies()
    {
        return $this->requestCookies;
    }

    /**
     * @return SetCookies
     */
    public function getResponseCookies()
    {
        return $this->responseCookies;
    }

    public function setResponseCookies(
        array $cookies,
        $domain = null,
        $path = null,
        $expires = null,
        $maxAge = null,
        $httpOnly = null,
        $secure = null
    ) {
        foreach ($cookies as $key => $value) {
            $this->setResponseCookie($key, $value, $domain, $path, $expires, $maxAge, $httpOnly, $secure);
        }
        return $this;
    }

    public function getRequestCookie($name, $default = null)
    {
        $cookie = $this->requestCookies->get($name);
        return $cookie ? $cookie->getValue() : $default;
    }

    /**
     * @param $name
     * @param mixed $value
     * @param $domain
     * @param $path
     * @param $expires
     * @param $maxAge
     * @param $httpOnly
     * @param $secure
     */
    public function setResponseCookie(
        $name,
        $value,
        $domain = null,
        $path = null,
        $expires = null,
        $maxAge = null,
        $httpOnly = null,
        $secure = null
    ) {
        if (!$value instanceof SetCookie) {
            if (is_array($value)) {
                $value = self::arrayToSetCookie($name, $value);
            } elseif (count($args = func_get_args()) > 2) {
                $value = self::arrayToSetCookie($name, get_defined_vars());
            } elseif (is_string($value)) {
                $value = SetCookie::create($name, $value);
            }
        }
        $this->responseCookies = $this->responseCookies->with($value);
    }

    public function unsetResponseCookie($name)
    {
        $this->responseCookies = $this->responseCookies->without($name);
        return $this;
    }

    protected function main(): ResponseInterface
    {
        $this->attachToRequest();

        $this->requestCookies = Cookies::fromRequest($this->request);
        $this->responseCookies = new SetCookies();

        $response = $this->delegate();

        $cookies = SetCookies::fromResponse($response);
        foreach ($this->responseCookies->getAll() as $setCookie) {
            $cookies = $cookies->with($setCookie);
        }

        return $cookies->renderIntoSetCookieHeader($response);
    }

    protected static function arrayToSetCookie(string $name, array $arr): SetCookie
    {
        $cookie = SetCookie::create($name, $arr['value']);
        if (isset($arr['domain'])) {
            $cookie = $cookie->withDomain($arr['domain']);
        }
        if (isset($arr['expires'])) {
            $cookie = $cookie->withExpires($arr['expires']);
        }
        if (isset($arr['httpOnly'])) {
            $cookie = $cookie->withHttpOnly($arr['httpOnly']);
        }
        if (isset($arr['maxAge'])) {
            $cookie = $cookie->withMaxAge($arr['maxAge']);
        }
        if (isset($arr['path'])) {
            $cookie = $cookie->withPath($arr['path']);
        }
        if (isset($arr['secure'])) {
            $cookie = $cookie->withSecure($arr['secure']);
        }

        return $cookie;
    }
}
