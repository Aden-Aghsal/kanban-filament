<?php
use App\Models\Task;
use App\Enums\TaskStatus;

class TaskController extends Controller
{
    public function index()
    {
        return view('tasks.index', [
            'tasks' => Task::where('user_id', auth()->id())->get()
        ]);
    }

    public function store(Request $request)
    {
        Task::create([
            'title' => $request->title,
            'status' => TaskStatus::TODO,
            'user_id' => auth()->id(),
        ]);
    }
}