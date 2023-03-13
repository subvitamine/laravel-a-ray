<?php

namespace Subvitamine\LaravelARay;

use Illuminate\Support\Carbon;

enum CommitStatus: string
{
    case SUCCESS = 'SUCCESS';
    case INFO = 'INFO';
    case WARNING = 'WARNING';
    case ERROR = 'ERROR';
}

class ARayPush
{
    private string $label = "";
    private $startAt;
    private $endAt;

    private array $meta;

    private $type;

    private array $commits;

    /**
     * constructor
     */
    public function __construct($label, $startAt = null)
    {
        $this->label = $label;
        $this->startAt = $startAt ? $startAt : Carbon::now();
        $this->type = "DEBUG";
        $this->meta = [];

        $this->commits = [];
    }

    /**
     * Add a commit
     * @param string $label Label of commit
     * @param array $content
     * @param CommitStatus $status
     * @return ARayPush
     */
    public function addCommit(string $label, array $content, CommitStatus $status, Carbon $startAt = null): ARayPush
    {
        if (count($this->commits) > 20) {
            throw new \Exception('You can\'t add more than 20 commits');
        }

        $behaviorClass = debug_backtrace()[1];

        $this->commits[] = [
            'label' => $label,
            'content' => [
                'currentClass' => [
                    'class' => $behaviorClass['class'],
                    'line' => isset($behaviorClass['line']) ? $behaviorClass['line'] : null,
                ],
                'content' => $content
            ],
            'isAt' => $startAt ? $startAt : Carbon::now(),
            'status' => $status
        ];

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * @return Carbon
     */
    public function getStartAt(): Carbon
    {
        return $this->startAt;
    }

    /**
     * @param Carbon $startAt
     */
    public function setStartAt(Carbon $startAt): void
    {
        $this->startAt = $startAt;
    }

    /**
     * @return mixed
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * @param mixed $endAt
     */
    public function setEndAt($endAt): void
    {
        $this->endAt = $endAt;
    }

    /**
     * @return array
     */
    public function getCommits(): array
    {
        return $this->commits;
    }

    /**
     * @param array $commits
     */
    public function setCommits(array $commits): void
    {
        $this->commits = $commits;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @param array $meta
     */
    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * @return mixed|string
     */
    public function getType(): mixed
    {
        return $this->type;
    }

    /**
     * @param mixed|string $type
     */
    public function setType(mixed $type): void
    {
        if (in_array($type, ['DEBUG', 'REQUEST'])) {
            $this->type = $type;
        } else (
        throw new \Exception('Type must be DEBUG or REQUEST')
        );
    }


    /**
     * Return json of push
     * @return string|false
     */
    public function toJson(): array
    {
        $result = [
            'label' => $this->label,
            'type' => $this->type,
            'meta' => $this->meta,
            'startAt' => $this->startAt->format('Y-m-d H:i:s.u'),
            'endAt' => $this->endAt->format('Y-m-d H:i:s.u'),
            'commits' => []
        ];

        foreach ($this->commits as $commit) {
            $result['commits'][] = [
                'label' => $commit['label'],
                'content' => $commit['content'],
                'isAt' => $commit['isAt']->format('Y-m-d H:i:s.u'),
                'status' => $commit['status']
            ];
        }

        return $result;
    }
}
