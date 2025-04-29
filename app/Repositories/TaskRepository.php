<?php

namespace App\Repositories;

use App\Models\Task;
use App\Services\PromptManagementService;
use Carbon\Carbon;

class TaskRepository
{
    public function __construct(
        public Task                              $taskModel,
        private readonly PromptManagementService $promptService
    )
    {
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Task>
     */
    public function listTasks(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->taskModel->newModelQuery()->get();
    }

    /**
     * Creates a new task
     *
     * @param string $title
     * @param Carbon|null $completedOn
     * @return Task
     */
    public function create(string $title, string $description, ?Carbon $completedOn = null): Task
    {

        $task = new Task;
        $task->fill([
            'title' => $title,
            'description' => $description,
            'embeddings' => $this->promptService->generateEmbedding($this->generateEmbeddingText($title, $description)),
            'completed_on' => $completedOn ?? null
        ]);

        $task->save();

        return $task;
    }

    /**
     * Generates embedding text
     *
     * @param string $task
     * @param string $description
     * @return string
     */
    protected function generateEmbeddingText(string $task, string $description): string
    {
        return "Following is a task\\nTitle:$task\\nDescription:$description";
    }

    /**
     * @param $prompt
     * @return \Illuminate\Database\Eloquent\Collection<int, Task>
     */
    public function filterRag(string $prompt): \Illuminate\Database\Eloquent\Collection
    {
        $promptEmbedding = $this->promptService->generateEmbedding("$prompt");
        \DB::enableQueryLog();
        $matchingTaskIds = $this->taskModel->newModelQuery()
            ->select(['id', 'title'])
            ->addSelect([\DB::raw("(embeddings <=> '[" . implode(",", $promptEmbedding) . "]') as similarity")])
            ->addSelect([\DB::raw("(1 - (embeddings <=> '[" . implode(",", $promptEmbedding) . "]')) * 100 as percentageSimilarity")])
//            ->whereRaw("(embeddings <=> ?) <= 0.25", ["[" . implode(",", $promptEmbedding) . "]"])
            ->whereRaw("(1 - (embeddings <=> ?)) >= 0.75", ["[" . implode(",", $promptEmbedding) . "]"])
            ->orderBy('similarity')
            ->limit(2)
            ->get();

        dd($matchingTaskIds->toArray(), \DB::getRawQueryLog());

        return $this->taskModel->newModelQuery()->whereIn('id', $matchingTaskIds->map(fn ($item) => $item["id"]))->get();
    }
}
