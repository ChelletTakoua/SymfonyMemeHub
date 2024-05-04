<?php

namespace App\EventListener;


use MongoDB\Driver\Session;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelInterface;

class RequestResponseDebugListener
{

    private $logger;
    private $requestStack;
    private $session;
    private $currentRequest;
    private $kernel;

    public function __construct(RequestStack $requestStack,LoggerInterface $logger,KernelInterface $kernel)
        //used requestStack instead of the session directly because the session is not yet available at this point
    {
        $this->requestStack = $requestStack;
        $this->logger = $logger;
        $this->kernel = $kernel;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $this->session = $event->getRequest()->getSession();
        $request = $event->getRequest();


        $requestData = [
            'method' => $request->getMethod(),
            'headers' => $request->headers->all(),
            'content' => $request->getContent(),
            'query' => $request->query->all(),
            'request' => $request->request->all(),
            'server' => $request->server->all(),
            'cookies' => $request->cookies->all(),
           'files' => $request->files->all(),
        ];
        /*
        $memoryBefore = memory_get_usage();
        //$this->session->set('debug_request', $requestData);
        $this->session->set('debug_request', json_encode($requestData));
        $memoryAfter = memory_get_usage();
        dd($memoryBefore, $memoryAfter, $memoryAfter - $memoryBefore);
*/
        //$this->logger->info('Request Data: ' . json_encode($requestData));
        // Get the existing array from the session

        $this->currentRequest = [
            'request' => $requestData,
            'routing' => null,
            'response' => null
        ];

    }

    public function onKernelResponse(ResponseEvent $event)
    {
        echo "onKernelResponse called";


        $response = $event->getResponse();

        $responseData = [
            'headers' => $response->headers->all(),
            'content' => $response->getContent(),
            'status' => $response->getStatusCode(),
        ];

        $this->currentRequest['response'] = $responseData;
        $this->currentRequest['routing'] = $event->getRequest()->attributes->get('_route');
        $this->saveCurrentRequest();
/*        $existingData = $this->session->get('debug_request', []);
        $existingData=[];
        $existingData[] = $this->currentRequest;
        $this->session->set('debug_request', $existingData);
        dd($this->session->get('debug_request'));
*/

    }

    /**
     * Save the current request in a json file in the var directory
     * @return void
     */
    private function saveCurrentRequest()
    {
/*
        $filePath = $this->kernel->getProjectDir() . '/var/debug/debug_requests.json';

        $data = file_exists($filePath) ? json_decode(file_get_contents($filePath), true) : [];
        $data[] = $this->currentRequest;
        file_put_contents($filePath, json_encode($data));
*/
    }
}
