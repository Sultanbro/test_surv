<?php

namespace App\Entities\DataTransferObjects\News;

use Illuminate\Contracts\Support\Arrayable;

class ArticleStoreDTO implements Arrayable
{
    /**
     * @param string $authorId
     * @param string $title
     * @param string $content
     * @param ?array $availableFor
     * @param array $questions
     * @param array $files
     */
    public function __construct(
        protected string $authorId,
        protected string $title,
        protected string $content,
        protected ?array  $availableFor,
        protected array  $questions,
        protected array  $files,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'author_id' => $this->authorId,
            'title' => $this->title,
            'content' => $this->content,
            'available_for' => $this->availableFor
        ];
    }

    public function getAuthorId(): string
    {
        return $this->authorId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getAvailableFor(): ?array
    {
        return $this->availableFor;
    }

    public function getQuestions(): array
    {
        return $this->questions;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}
