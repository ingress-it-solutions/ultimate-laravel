<?php


namespace Ultimate\Laravel\Views;


use Illuminate\Contracts\View\Engine;
use Illuminate\View\Factory;
use Ultimate\Laravel\Facades\Ultimate;
use Ultimate\Models\Segment;

final class ViewEngineDecorator implements Engine
{
    public const SHARED_KEY = '__ultimate_view_name';

    /**
     * @var Engine
     */
    private $engine;

    /**
     * @var Factory
     */
    private $viewFactory;

    public function __construct(Engine $engine, Factory $viewFactory)
    {
        $this->engine = $engine;
        $this->viewFactory = $viewFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function get($path, array $data = [])
    {
        if (!Ultimate::canAddSegments()) {
            return $this->engine->get($path, $data);
        }

        $label = 'view::'.$this->viewFactory->shared(self::SHARED_KEY, basename($path));

        return Ultimate::addSegment(function (Segment $segment) use ($path, $data) {
            $segment->addContext('info', compact('path'))
                ->addContext('data', $data);

            return $this->engine->get($path, $data);
        }, 'view', $label, true);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->engine, $name], $arguments);
    }
}