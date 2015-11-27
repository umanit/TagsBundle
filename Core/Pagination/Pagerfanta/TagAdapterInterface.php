<?php

namespace Netgen\TagsBundle\Core\Pagination\Pagerfanta;

use Netgen\TagsBundle\API\Repository\Values\Tags\Tag;

interface TagAdapterInterface
{
    /**
     * Set tag field
     *
     * @param \Netgen\TagsBundle\API\Repository\Values\Tags\Tag $tag
     */
    public function setTag(Tag $tag);
}