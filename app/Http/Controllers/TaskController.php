<?php

namespace App\Http\Controllers;
use App\Http\Requests\Task\StoreTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    
  public function index(Request $request){

    $query = Task :: where('user_id', Auth::id())->with('subject');

    if($request->filled('search')){
        $query->where('title','like','%'.$request->search.'%');
    }

     if($request->filled('status')){
        $query->where('status',$request->status);
     }

    if($request->filled('priority')){
        $query->where('priority',$request->priority);
    }
    if($request->filled('subject')){
        $query->where('subject_id',$request->subject);
    }

     $sort = $request->get('sort', 'deadline');
        $direction = $request->get('direction', 'asc');
        $allowedSorts = ['deadline', 'priority', 'created_at', 'title'];

        if (in_array($sort, $allowedSorts, true)) {
            $query->orderBy($sort, $direction === 'desc' ? 'desc' : 'asc');
        }

        $tasks = $query->paginate(10)->withQueryString();
        $subjects = Subject::where('user_id', Auth::id())->orderBy('name')->get();

        return view('tasks.index', compact('tasks', 'subjects'));
  }

   public function create(){
      $subjects = Subject::where('user_id',Auth::id())->orderBy('name')->get();
      return view('tasks.create', compact('subjects'));
   }

   public function store(StoreTaskRequest $request){
       Auth::user()->tasks()->create($request->validated());

       return redirect()->route('tasks.index')->with('status','Task created'); 
   }

   public function show(Task $task)
    {
        $this->authorize('view', $task);

        return view('tasks.show', compact('task'));
    }


    public function edit(Task $task)
    {
        $this->authorize('update', $task);

        $subjects = Subject::where('user_id', Auth::id())->orderBy('name')->get();

        return view('tasks.edit', compact('task', 'subjects'));
    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validated();

        if ($validated['status'] === 'completed' && $task->status !== 'completed') {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== 'completed') {
            $validated['completed_at'] = null;
        }

        $task->update($validated);

        return redirect()->route('tasks.show', $task)->with('status', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')->with('status', 'Task deleted.');
    }



}
