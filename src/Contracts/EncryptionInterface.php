<?php
namespace Humphries\Contracts;

interface EncryptionInterface
{

    /**
     * Encrypts a text
     *
     * @param string $text
     * @param mixed $key
     * @return string
     */
    public function encrypt($text, $key = null);

    /**
     * Decrypts a text
     *
     * @param string $text
     * @param string $key
     * @return string
     */
    public function decrypt($text, $key = null);

}