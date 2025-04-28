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

    public function store(StoreTaskRequest $storeTask): Task
    {
        return $this->taskRepository->create($storeTask->get('task'), $storeTask->get('completed_on'));
    }
}
