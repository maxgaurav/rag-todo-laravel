<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use App\Repositories\TaskRepository;
use App\Services\PromptManagementService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(protected TaskRepository $taskRepository, protected PromptManagementService $promptService)
    {
    }

    /**
     * Returns list of all tasks saved
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function index(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->taskRepository->listTasks();
    }

    /**
     * Stores the task by creating its embedding
     *
     * @param StoreTaskRequest $storeTask
     * @return Task
     */
    public function store(StoreTaskRequest $storeTask): Task
    {
        return $this->taskRepository->create(
            $storeTask->get('title'),
            $storeTask->get('description'),
            $storeTask->get('completed_on')
        );
    }

    /**
     * Returns first two most relative matching task according to the prompt
     *
     * @param Request $request
     * @return array
     */
    public function list(Request $request): array
    {
        $tasks = $this->taskRepository->filterRag($request->get("prompt"));
        return $this->generateSummaryJson($request->get("prompt"), $tasks);
    }

    protected function generateSummaryJson(string $prompt, $tasks): array
    {
        $task1Title = $tasks[0]->title;
        $task1Id = $tasks[0]->id;
        $task2Title = $tasks[1]->title;
        $task2Id = $tasks[1]->id;
        $content = "For the following prompt and tasks create a summary paragraph and json value for each tasks as json form\n Prompt: $prompt \n Task 1 \n Title: $task1Title \n Id: $task1Id \n Task 2 \n Title: $task2Title \n Id: $task2Id";

        return (json_decode($this->promptService->prompt($content), true));
    }
}
