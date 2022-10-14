<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
#[ORM\Table(name: 'question')]
class Question
{
    use TimestampableEntity;

    const QUESTION_VOTE_UP = 'up';
    const QUESTION_VOTE_DOWN = 'down';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING,length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING,length: 100, unique: true)]
    #[Gedmo\Slug(fields: ['name'])]
    private ?string $slug = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $question = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $askedAt = null;

    #[ORM\Column(type:Types::INTEGER)]
    private int $votes;

    public function __construct()
    {
        $this->votes = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;
        return $this;
    }

    public function getAskedAt(): ?\DateTimeInterface
    {
        return $this->askedAt;
    }

    public function setAskedAt(?\DateTimeInterface $askedAt): self
    {
        $this->askedAt = $askedAt;
        return $this;
    }

    public function getVotes(): int
    {
        return $this->votes;
    }

    public function setVotes(int $votes): self
    {
        $this->votes = $votes;
        return $this;
    }

    public function getVotesString(): string
    {
        //return $this->votes === 1 ? '1 vote' : sprintf('%d votes', $this->votes);
        $prefix = $this->getVotes() >=0 ? '+' : '-';
        return sprintf('%s%d: ', $prefix, abs($this->getVotes()));
    }

    public function upVote(): self
    {
        $this->votes++;
        return $this;
    }

    public function downVote(): self
    {
        $this->votes--;
        return $this;
    }

    /**
     * @throws \Exception
     */
    public function voteQuestion(string $direction): self
    {
        if ($direction === self::QUESTION_VOTE_UP) {
            $this->upVote();
        } elseif ($direction === self::QUESTION_VOTE_DOWN) {
            $this->downVote();
        } else {
            throw new \Exception('Invalid vote!');
        }
        return $this;
    }


    public function __toString(): string
    {
        return $this->name;
    }


}
