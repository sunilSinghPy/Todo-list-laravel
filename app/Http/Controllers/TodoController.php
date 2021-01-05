<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('index');
    }
    public function index()
    {


        if (auth()->check()) {
            $todos = auth()
                ->user()
                ->todos()
                ->orderBy('completed')
                ->get();

            return view('todos.index', compact('todos'));
        }
        $todos = Todo::orderBy('completed')->get();
        return view('todos.index', compact('todos'));
    }
    public function show(Todo $todo)
    {

        return view('todos.show', compact('todo'));
    }

    public function create()
    {
        $todos = auth()->user()->todos()->orderBy('completed')->get();
        return view('todos.create', compact('todos'));
    }
    public function store(Request $request)
    {

        if ($request->title) {
            $validated = $request->validate([
                'title' => 'required|max:254|min:5',
                'body'  => 'required|min:10|max:500',
            ]);
            auth()->user()->todos()->create($validated);
            // Todo::create($validated);
            return redirect()->back()->with('msg', 'task added successfully');
        }

        return redirect()->back()->with('error', 'somthing went wrong');
    }
    public function edit(Todo $todo)
    {


        return view('todos.edit', compact('todo'));
    }
    public function update(Request $request, Todo $todo)
    {
        if ($request->title) {
            $validated = $request->validate([
                'title' => 'required|max:254|min:5',
                'body' => 'required|min:10|max:500',
            ]);
            $validated['completed'] = false;

            $todo->update($validated);
            return redirect('todos/')->with('msg', 'task updated successfully');
        }
    }
    public function destroy(Todo $todo)
    {


        $todo->delete();
        return redirect()->back()->with('msg', 'task deleted successfully');
    }
    public function complete(Todo $todo)
    {


        if (!$todo->completed) {
            $todo->update(['completed' => true]);
            return redirect('todos/')->with('msg', 'task marked completed successfully');
        } else {
            $todo->update(['completed' => false]);
            return redirect('todos/')->with('msg', 'task maked incompleted successfully');
        }
    }
}
