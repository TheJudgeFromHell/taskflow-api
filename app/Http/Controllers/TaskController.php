<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->tasks();
        
        // Фильтр по статусу (выполнено/не выполнено)
        if ($request->has('completed') && $request->completed !== '') {
            $query->where('is_completed', $request->completed);
        }
        
        // Фильтр по приоритету
        if ($request->has('priority') && $request->priority !== '') {
            $query->where('priority', $request->priority);
        }
        
        // Сортировка
        $sortBy = $request->get('sort_by', 'due_date');
        $sortOrder = $request->get('sort_order', 'asc');
        
        if (in_array($sortBy, ['due_date', 'created_at', 'priority', 'title'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('due_date', 'asc');
        }
        
        $tasks = $query->paginate(10);
        
        return $tasks;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'in:low,medium,high'
        ]);

        $task = Auth::user()->tasks()->create($request->all());
        return response()->json($task, 201);
    }

    public function show(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        return $task;
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->update($request->all());
        return $task;
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->delete();
        return response()->json(['message' => 'Task deleted']);
    }
}