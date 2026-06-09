<?php

namespace App\Http\Controllers;

use App\Task;
use App\Comment;
use App\Like;
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

    // Добавить комментарий к задаче
    public function addComment(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        
        // Проверяем, что задача принадлежит пользователю
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        $request->validate([
            'content' => 'required|string|min:1'
        ]);
        
        $comment = Comment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'content' => $request->content
        ]);
        
        return response()->json($comment, 201);
    }

    // Получить комментарии к задаче
    public function getComments($id)
    {
        $task = Task::findOrFail($id);
        
        if ($task->user_id !== Auth::id()) {
            return response()->json(['message' => 'Forbidden'], 403);
        }
        
        $comments = $task->comments()->with('user')->orderBy('created_at', 'desc')->get();
        
        return response()->json($comments);
    }

    // Добавить лайк/дизлайк к задаче
    public function rateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        
        $request->validate([
            'type' => 'required|in:like,dislike'
        ]);
        
        // Проверяем, не оценил ли пользователь уже эту задачу
        $existingLike = Like::where('task_id', $task->id)
                            ->where('user_id', Auth::id())
                            ->first();
        
        if ($existingLike) {
            // Если уже оценил, обновляем оценку
            $existingLike->update(['type' => $request->type]);
            return response()->json(['message' => 'Rating updated', 'like' => $existingLike]);
        }
        
        // Иначе создаём новую оценку
        $like = Like::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'type' => $request->type
        ]);
        
        return response()->json($like, 201);
    }
}