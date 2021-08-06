<?php


class Person
{
    private string $email;
    private stdClass $address;

    public function __construct(string $email, stdClass $address)
    {
        $this->email=$email;
        $this->address = new stdClass();
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return stdClass
     */
    public function getAddress(): stdClass
    {
        return $this->address;
    }

    /**
     * @param stdClass $address
     */
    public function setAddress(stdClass $address): void
    {
        $this->address = $address;
    }
}