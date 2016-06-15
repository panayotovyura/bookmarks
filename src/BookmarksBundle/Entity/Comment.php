<?php

namespace BookmarksBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comment
 *
 * @ORM\Table(name="comments")
 * @ORM\Entity(repositoryClass="BookmarksBundle\Repository\CommentRepository")
 */
class Comment
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
     * @ORM\Column(name="ip", type="string", length=255)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="Bookmark", inversedBy="comments")
     * @ORM\JoinColumn(name="bookmark_uid", referencedColumnName="uid")
     */
    private $bookmark;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Comment
     */
    public function setCreatedAt($createdAt)
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
     * Set ip
     *
     * @param string $ip
     *
     * @return Comment
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Comment
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set bookmark
     *
     * @return Bookmark
     */
    public function getBookmark()
    {
        return $this->bookmark;
    }

    /**
     * Get bookmark
     *
     * @param Bookmark $bookmark
     *
     * @return Comment
     */
    public function setBookmark(Bookmark $bookmark)
    {
        $this->bookmark = $bookmark;

        return $this;
    }
}