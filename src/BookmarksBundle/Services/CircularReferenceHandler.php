<?php

namespace BookmarksBundle\Services;

class CircularReferenceHandler
{
    public function handle($object)
    {
        return $object->getUid();
    }
}
