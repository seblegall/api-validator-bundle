<?php

namespace Seblegall\ApiValidatorBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class ContentTypeListener.
 */
class ContentTypeListener
{
    /**
     * @var
     */
    protected $decoderProvider;

    /**
     * ContentTypeListener constructor.
     *
     * @param $decoderProvider
     */
    public function __construct($decoderProvider)
    {
        $this->decoderProvider = $decoderProvider;
    }

    /**
     * @param GetResponseEvent $event kernel.request event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $method = $request->getMethod();
        $contentType = $request->headers->get('Content-Type');

        if (in_array($method, array('POST', 'PUT', 'PATCH'))) {
            $format = null === $contentType
                ? $request->getRequestFormat()
                : $request->getFormat($contentType);

            $content = $request->getContent();

            if (!$this->decoderProvider->supports($format)) {
                return;
            }

            if (!empty($content)) {
                $decoder = $this->decoderProvider->getDecoder($format);
                $data = $decoder->decode($content);

                if (is_array($data)) {
                    $request->request = new ParameterBag($data);
                } else {
                    throw new \InvalidArgumentException('Invalid '.$format.' message received');
                }
            }
        }
    }
}
