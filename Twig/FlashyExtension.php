<?php

namespace Lexty\FlashyBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class FlashyExtension extends \Twig_Extension
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('lexty_flashy_render', [$this, 'render'], ['safe' => 'html']),
        );
    }

    public function render($withJs = false)
    {
        $css = $this->container->get('templating.helper.assets')->getUrl('bundles/lextyflashy/css/flashy.css');
        $js = $this->container->get('templating.helper.assets')->getUrl('bundles/lextyflashy/js/flashy.js');
        $data = json_encode($this->container->get('lexty_flashy.flashy')->all());

        $html = <<< HTML
        <link href="$css" rel="stylesheet">
        <script>window._lexty_flashy_data = {$data};</script>
        <script src="$js"></script>
HTML;

        return $html;
    }

    public function getName()
    {
        return 'lexty_flashy_extension';
    }
}