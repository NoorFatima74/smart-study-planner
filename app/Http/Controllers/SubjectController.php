<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Subject\StoreSubjectRequest;
use App\Http\Requests\Subject\UpdateSubjectRequest;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index(){
        $subjects = Subject::where('user_id',Auth::id())
         ->latest()->get();

         return view('subjects.index',compact('subjects'));
    }

    public function create(){
        return view('subjects.create');
    }

    public function store(StoreSubjectRequest $request){
     
     Auth::user()->subjects()->create($request->validated());

     return redirect()->route('subjects.index')->with('status','subject created');

    }

    public function show(Subject $subject){

      $this->authorize('view',$subject);

     return view('subjects.show', compact('subject'));

    }

    public function edit(Subject $subject)
    {
        $this->authorize('update', $subject);

        return view('subjects.edit', compact('subject'));
    }


    public function update(UpdateSubjectRequest $request, Subject $subject)
    {
        $this->authorize('update', $subject);

        $subject->update($request->validated());

        return redirect()->route('subjects.index')->with('status', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $this->authorize('delete', $subject);

        $subject->delete();

        return redirect()->route('subjects.index')->with('status', 'Subject deleted.');
    }


}
