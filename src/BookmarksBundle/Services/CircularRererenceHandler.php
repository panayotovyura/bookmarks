<?php

namespace BookmarksBundle\Services;

class CircularRererenceHandler
{
    public function handle($object)
    {
        return $object->getUid();
    }
}
