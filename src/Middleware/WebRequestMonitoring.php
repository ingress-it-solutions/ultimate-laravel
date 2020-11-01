<?php


namespace Ultimate\Laravel\Middleware;


use Closure;
use Symfony\Component\HttpFoundation\Request as TerminableRequest;
use Symfony\Component\HttpFoundation\Response as TerminableResponse;
use Illuminate\Http\Request;
use Ultimate\Laravel\Facades\Ultimate;
use Illuminate\Support\Facades\Auth;
use Ultimate\Laravel\Filters;
use Symfony\Component\HttpKernel\TerminableInterface;

class WebRequestMonitoring implements TerminableInterface
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (
            Filters::isApprovedRequest(config('ultimate.ignore_url'), $request)
            &&
            $this->shouldRecorded($request)
            &&
            !Ultimate::isRecording()
        ) {
            $this->startTransaction($request);
        }

        return $next($request);
    }

    /**
     * Determine if Ultimate should monitor current request.
     *
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function shouldRecorded($request): bool
    {
        return true;
    }

    /**
     * Start a transaction for the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     */
    protected function startTransaction($request)
    {
        Ultimate::startTransaction(
            $this->buildTransactionName($request)
        );

        if (Auth::check() && config('ultimate.user')) {
            Ultimate::currentTransaction()
                ->withUser(Auth::user()->getAuthIdentifier());
        }
    }

    /**
     * Terminates a request/response cycle.
     *
     * @param TerminableRequest $request
     * @param TerminableResponse $response
     */
    public function terminate(TerminableRequest $request, TerminableResponse $response)
    {
        if (Ultimate::isRecording()) {
            Ultimate::currentTransaction()->setResult($response->getStatusCode());

            Ultimate::currentTransaction()
                ->addContext('Request Body', Filters::hideParameters(
                    $request->request->all(),
                    config('ultimate.hidden_parameters')
                ))
                ->addContext('Response', [
                    'status_code' => $response->getStatusCode(),
                    'version' => $response->getProtocolVersion(),
                    'content' => is_string($response->getContent()) ? substr($response->getContent(), 0, 250) : $response->getContent(),
                    'charset' => $response->getCharset(),
                    'headers' => $response->headers->all(),
                ]);
        }
    }

    /**
     * Generate readable name.
     *
     * @param \Illuminate\Http\Request $request
     * @return string
     */
    protected function buildTransactionName(Request $request)
    {
        $route = $request->route();

        if($route instanceof \Illuminate\Routing\Route) {
            $uri = $request->route()->uri();
        } else {
            $uri = $_SERVER['REQUEST_URI'];
        }

        return $request->method() . ' ' . $this->normalizeUri($uri);
    }

    /**
     * Normalize URI string.
     *
     * @param $uri
     * @return string
     */
    protected function normalizeUri($uri)
    {
        return '/' . trim($uri, '/');
    }
}
