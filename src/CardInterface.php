<?php

namespace Pebble\Teams;

interface CardInterface
{
    /**
     * Returns message card array
     *
     * @return array
     */
    public function getMessage(): array;
}
