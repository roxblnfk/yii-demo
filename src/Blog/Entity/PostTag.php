<?php

declare(strict_types=1);

namespace App\Blog\Entity;

use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Entity;

/**
 * @Entity
 */
class PostTag
{
    /**
     * @Column(type="primary")
     */
    private ?int $id = null;
    private $post_id = null;
    private $tag_id = null;
}
