<?php

declare(strict_types=1);

namespace Lit\Middleware\Cookie;

use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\SetCookies;
use Lit\Nimo\AbstractMiddleware;
use Psr\Http\Message\ResponseInterface;

class CookiesMiddleware extends AbstractMiddleware
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
                $arr = $value;
                $value = SetCookie::create($name, $arr['value']);
                if (isset($arr['domain'])) {
                    $value = $value->withDomain($arr['domain']);
                }
                if (isset($arr['expires'])) {
                    $value = $value->withExpires($arr['expires']);
                }
                if (isset($arr['httpOnly'])) {
                    $value = $value->withHttpOnly($arr['httpOnly']);
                }
                if (isset($arr['maxAge'])) {
                    $value = $value->withMaxAge($arr['maxAge']);
                }
                if (isset($arr['path'])) {
                    $value = $value->withPath($arr['path']);
                }
                if (isset($arr['secure'])) {
                    $value = $value->withSecure($arr['secure']);
                }
            } elseif (count($args = func_get_args()) > 2) {
                $value = SetCookie::create($name, $value);
                if (isset($domain)) {
                    $value = $value->withDomain($domain);
                }
                if (isset($expires)) {
                    $value = $value->withExpires($expires);
                }
                if (isset($httpOnly)) {
                    $value = $value->withHttpOnly($httpOnly);
                }
                if (isset($maxAge)) {
                    $value = $value->withMaxAge($maxAge);
                }
                if (isset($path)) {
                    $value = $value->withPath($path);
                }
                if (isset($secure)) {
                    $value = $value->withSecure($secure);
                }
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
}