<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use App\Repositories\TaskRepository;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(protected TaskRepository $taskRepository)
    {
    }

    public function index(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->taskRepository->listTasks();
    }

    public function store(StoreTaskRequest $storeTask): Task
    {
        return $this->taskRepository->create(
            $storeTask->get('title'),
            $storeTask->get('description'),
            $storeTask->get('completed_on')
        );
    }

    /**
     * @param Request $request
     * @return @return \Illuminate\Database\Eloquent\Collection<int, Task>
     */
    public function list(Request $request): \Illuminate\Database\Eloquent\Collection
    {
        return $this->taskRepository->filterRag($request->get("prompt"));
    }
}
