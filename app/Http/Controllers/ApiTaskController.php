<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tasks = Task::where('user_id', Auth::id())->orderBy('created_at', "desc")->get();

        return response()->json([
            'tasks' => $tasks
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'done' => 'required|boolean',
            'body' => 'required|string'
        ]);

        $tasks = Task::create([
            'body' => $request->body,
            'done' => $request->done,
            'user_id' => $request->user()->id
        ]);

        if(!$request->user()->tokenCan('tasks:write')){
            return response()->json(['message' => "You don't have the ability to do that. Please contact administrator"], 403);
        }

        $tasks->save();
        return response()->json([$tasks], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tasks = Task::find($id);
        if(!$tasks){
            return response()->json("La tâche n'existe pas", 404);
        }
        if($tasks->user->id !== Auth::id()){
            return response()->json('Accès a la tâche non autorisé', 403);
        }
        return response()->json([
            $tasks
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        $tasks = Task::find($id);
        if(!$tasks){
            return response()->json("La tâche n'existe pas", 404);
        }
        if($tasks->user->id !== Auth::id()){
            return response()->json('Accès a la tâche non autorisé', 403);
        }
        $tasks->body = $request->content;
        $tasks->save();
        return response()->json([
            $tasks
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tasks = Task::find($id);
        if(!$tasks){
            return response()->json("La tâche n'existe pas", 404);
        }
        if($tasks->user->id !== Auth::id()){
            return response()->json('Accès a la tâche non autorisé', 403);
        }
        $tasks->delete();
        return response()->json([
            $tasks
        ]);
    }
}
