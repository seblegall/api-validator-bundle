<?php

namespace Seblegall\ApiValidatorBundle\Decoder;

/**
 * Interface for decoders.
 */
interface DecoderInterface
{
    /**
     * Decodes a string into an array.
     *
     * @param string $data
     *
     * @return array|bool Flase if decoding failed
     */
    public function decode($data);
}
