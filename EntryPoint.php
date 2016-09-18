<?php

namespace Elevator;

use Elevator\Http\HttpRequestFactory;
use Elevator\Http\HttpResponseFactory;
use Elevator\Http\HttpResponseInterface;
use Exception;

class EntryPoint implements EntryPointInterface
{
    /**
     * @var HttpRequestFactory
     */
    protected $requestFactory;

    /**
     * @var HttpResponseFactory
     */
    protected $responseFactory;

    /**
     * @var DispatcherInterface
     */
    protected $dispatcher;

    public function __construct(
        HttpRequestFactory $requestFactory,
        HttpResponseFactory $responseFactory,
        DispatcherInterface $dispatcher
    ) {
        $this->requestFactory = $requestFactory;
        $this->responseFactory = $responseFactory;
        $this->dispatcher = $dispatcher;
    }

    public function handle(array $requestData)
    {
        $response = $this->safeHandle($requestData);

        return $response;
    }

    /**
     * @param array $requestData
     * @return HttpResponseInterface
     */
    private function safeHandle(array $requestData)
    {
        try {
            $response = $this->_handle($requestData);
        } catch(Exception $e) {
            $response = new \Elevator\Http\HttpResponse;    //factory not used to minimize uncertainty in a case of failure

            $message = $this->buildErrorMessage($e);

            $response->setError($message);
        }

        return $response;
    }

    private function _handle(array $requestData)
    {
        $request = $this->requestFactory->create($requestData);
        $response = $this->responseFactory->create();

        $this->dispatcher->dispatch($request, $response);

        return $response;
    }

    private function buildErrorMessage(Exception $e)
    {
        $steps = [];
        $counter = 0;
        foreach($e->getTrace() as $step) {
            $args = [];
            foreach($step['args'] as $arg) {
                if (is_object($arg)) {
                    $argString = get_class($arg);
                } elseif(is_array($arg)) {
                    $argString = 'Array(' . count($arg) . ')';
                } else {
                    $argString = $arg;
                }

                if (strlen($argString) > 50) {
                    $argString = substr($argString, 0, 20) . '...' . substr($argString, -20);
                }

                $args[] = $argString;
            }
            $steps[] = sprintf("#%d %s(%s): %s%s%s(%s)\n", $counter++, $step['file'], $step['line'], $step['class'], $step['type'], $step['function'], join(', ', $args));
        }

        $message = $e->getMessage() . PHP_EOL . str_repeat('-', 250) . join(str_repeat('-', 250), $steps);

        return $message;
    }
}