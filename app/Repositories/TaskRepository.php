<?php

namespace App\Repositories;

use App\Models\Task;
use App\Services\PromptManagementService;
use Carbon\Carbon;

class TaskRepository
{
    public function __construct(
        public Task $taskModel,
        private readonly PromptManagementService $promptService
    )
    {
    }

    /**
     * Creates a new task
     *
     * @param string $task
     * @param Carbon|null $completedOn
     * @return Task
     */
    public function create(string $task, string $description, Carbon $completedOn = null): Task
    {
        return $this->taskModel->newModelQuery()->create([
            'task' => $task,
            'description' => $description,
            'embedding' => $this->promptService->generateEmbedding($task),
            'completed_on' => $completedOn ?? null
        ]);
    }

    protected function generateEmbeddingText(string $task, string $description): string
    {
        return "Task Title: $task
        Description: $description";
    }
}
