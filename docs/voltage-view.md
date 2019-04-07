---
id: voltage-view
title: View
sidebar_label: View
---

In different framework, the concept of **view** varies from html itself, the template engine, to the adapter of the template engine, or the response itself. In lit, the **view** is responsible to generate http response (with `render` method) 

## Headers

`render` is not limited to body, HTTP headers is a part of response, too. Thus, you may populate response headers in view class.

```php
class RestfulView extends AbstractView
{
    public function render(array $data = []): ResponseInterface
    {
        $this->getEmptyBody()->write(json_encode($data['body']));
        if (!empty($data['link'])) {
            $headerline = implode(', ', array_map(function($link){
                return sprintf('<%s; rel="%s">', $link['url'], $link['rel'])
            }, $data['link']));
            $this->response = $this->response
                ->withHeader('Link', $headerline);
        }

        return $this->response
            ->withHeader('Content-Type', 'application/json');
    }
}

```

## Pass values

`render` accept an array of data, but sometime you might want have type hint, signature, or code completion for some kind of data or metadata, or dependency. We recommend you pass these data by either constructor parameter, or dedicated setter function.

```php
class PlateView extends AbstractView
{
    protected $plate;
    protected $templateName;

    public function __construct(League\Plates\Engine $plate)
    {
        $this->plate = plate;
    }
    
    public function setTemplateName(string $name): self
    {
        $this->templateName = $name;
        return $this;
    }
    
    public function render(array $data = []): ResponseInterface
    {
        $this->getEmptyBody()->write($this->plate->render($this->templateName, $data));
        
        return $this->response;
    }
}
```

## Creating response

**ViewInterface** requires a `setResponse` method. Implementation (see `ViewTrait`) should receive the response instance and use it as the response prototype in `render` method. By this way, view leaves constructor to have no any requirement, and no need to care about the response / responseFactory.
