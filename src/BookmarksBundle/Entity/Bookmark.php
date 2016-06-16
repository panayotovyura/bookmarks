<?php

namespace BookmarksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Bookmark
 *
 * @ORM\Table(name="bookmarks")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Bookmark
{
    /**
     * @var int
     *
     * @ORM\Column(name="uid", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $uid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255)
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="bookmark")
     */
    private $comments;

    /**
     * Bookmark constructor
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    /**
     * Get uid
     *
     * @return int
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @ORM\PrePersist
     *
     * @return $this
     */
    public function preSetCreatedAt()
    {
        if (null === $this->createdAt) {
            $this->createdAt = new \DateTime();
        }

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Bookmark
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Bookmark
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get comments
     *
     * @return Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     *
     * @return Bookmark
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param Comment $comment
     */
    public function removeTag(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }
}
