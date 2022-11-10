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

    private array $commits;

    /**
     * constructor
     */
    public function __construct($label)
    {
        $this->label = $label;
        $this->startAt = Carbon::now();

        $this->commits = [];
    }

    /**
     * Add a commit
     * @param string $label Label of commit
     * @param array $content
     * @param CommitStatus $status
     * @return ARayPush
     */
    public function addCommit(string $label, array $content, CommitStatus $status): ARayPush
    {
        if(count($this->commits) > 20) {
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
            'isAt' => Carbon::now(),
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
     * Return json of push
     * @return string|false
     */
    public function toJson(): array
    {
        $result = [
            'label' => $this->label,
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
