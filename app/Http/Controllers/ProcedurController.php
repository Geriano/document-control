<?php

namespace App\Http\Controllers;

use App\Models\Procedur;
use App\Models\Revision;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProcedurController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'revision_id' => 'required|integer|exists:revisions,id',
            'name' => 'required|string',
        ]);

        $procedur = Procedur::create([
            'name' => $request->name,
            'revision_id' => $request->revision_id, 
            'position' => Procedur::where('revision_id', $request->revision_id)
                                    ->whereNull('parent_id')
                                    ->count() + 1,
        ]);

        if ($procedur) {
            return redirect()->back()->with('success', __(
                'procedur `:name` has been created', [
                    'name' => $procedur->name,
                ],
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t create procedur',
        ));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Procedur  $procedur
     * @return \Illuminate\Http\Response
     */
    public function show(Procedur $procedur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Procedur  $procedur
     * @return \Illuminate\Http\Response
     */
    public function edit(Procedur $procedur)
    {
        return Inertia::render('Procedur/Edit')->with([
            'procedur' => $procedur,
            'content' => $procedur->content,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Procedur  $procedur
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Procedur $procedur)
    {
        $request->validate([
            'name' => 'string|required',
        ]);

        if ($procedur->update([ 'name' => $request->name ])) {
            return redirect()->back()->with('success', __(
                'procedur has been updated',
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t update procedur',
        ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Procedur  $procedur
     * @return \Illuminate\Http\Response
     */
    public function destroy(Procedur $procedur)
    {
        if ($procedur->delete()) {
            Procedur::where('revision_id', $procedur->revision_id)
                    ->where('parent_id', $procedur->parent_id)
                    ->where('position', '>=', $procedur->position)
                    ->decrement('position');

            return redirect()->back()->with('success', __(
                'procedur `:name` has been deleted', [
                    'name' => $procedur->name,
                ]
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t delete procedur',
        ));
    }

    /**
     * @param \App\Models\Procedur $procedur
     * @return \Illuminate\Http\Response
     */
    public function left(Procedur $procedur)
    {
        $parent = $procedur->parent;

        if (!$parent) {
            return redirect()->back()->with('error', __(
                'can\'t find parent element',
            ));
        }

        Procedur::where('revision_id', $parent->revision_id)
                ->where('parent_id', $parent->parent_id)
                ->where('position', '>', $parent->position)
                ->increment('position');

        Procedur::where('revision_id', $parent->revision_id)
                ->where('parent_id', $parent->id)
                ->where('position', '>', $procedur->position)
                ->decrement('position');
        
        $updated = $procedur->update([
            'parent_id' => $parent->parent_id,
            'position' => $parent->position + 1,
        ]);

        if ($updated) {
            return redirect()->back()->with('success', __(
                'position has been updated',
            ));
        }

        return redirect()->back()->with('error', __(
            'can\'t update position',
        ));
    }

    /**
     * @param \App\Models\Procedur $procedur
     * @return \Illuminate\Http\Response
     */
    public function right(Procedur $procedur)
    {
        $parent = Procedur::where('revision_id', $procedur->revision_id)
                            ->where('parent_id', $procedur->parent_id)
                            ->where('position', $procedur->position - 1)
                            ->first();

        if (! $parent || $procedur->position === 1) {
            return redirect()->back()->with('error', __(
                'parent procedur not exists',
            ));
        }

        $childs = $parent->childs;
        $procedur->update([
            'parent_id' => $parent->id,
            'position' => $childs->count() + 1,
        ]);

        Procedur::where('revision_id', $procedur->revision_id)
                ->where('parent_id', $parent->parent_id)
                ->where('position', '>', $parent->position)
                ->decrement('position');

        return redirect()->back()->with('success', __(
            'position has been updated',
        ));
    }

    /**
     * @param \App\Models\Procedur $procedur
     * @return \Illuminate\Http\Response
     */
    public function up(Procedur $procedur)
    {
        $before = Procedur::where('revision_id', $procedur->revision_id)
                            ->where('parent_id', $procedur->parent_id)
                            ->where('position', $procedur->position - 1)
                            ->first();

        if (!$before) {
            return redirect()->back()->with('error', __(
                'can\'t find sibling element',
            ));
        }

        Procedur::where('id', $before->id)->update([
            'position' => $procedur->position,
        ]);

        Procedur::where('id', $procedur->id)->update([
            'position' => $before->position,
        ]);

        return redirect()->back()->with('success', __(
            'position has been updated',
        ));
    }

    /**
     * @param \App\Models\Procedur $procedur
     * @return \Illuminate\Http\Response
     */
    public function down(Procedur $procedur)
    {
        $after = Procedur::where('revision_id', $procedur->revision_id)
                            ->where('parent_id', $procedur->parent_id)
                            ->where('position', $procedur->position + 1)
                            ->first();

        if (!$after) {
            return redirect()->back()->with('error', __(
                'can\'t find sibling element',
            ));
        }

        Procedur::where('id', $after->id)->update([
            'position' => $procedur->position,
        ]);

        Procedur::where('id', $procedur->id)->update([
            'position' => $after->position,
        ]);

        return redirect()->back()->with('success', __(
            'position has been updated',
        ));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function drill(Request $request)
    {
        $drag = Procedur::findOrFail($request->drag);
        $drop = Procedur::findOrFail($request->drop);

        if ($drag->position < $drop->position) {
            Procedur::where('revision_id', $drag->revision_id)
                        ->where('parent_id', $drag->parent_id)
                        ->where('position', '<=', $drop->position)
                        ->where('id', '!=', $drag->position)
                        ->decrement('position');
                                    
            $drag->update([
                'position' => $drop->position,
            ]);
        } else {
            Procedur::where('revision_id', $drag->revision_id)
                        ->where('parent_id', $drag->parent_id)
                        ->where('position', '<=', $drag->position)
                        ->where('id', '!=', $drag->id)
                        ->increment('position');

            $drag->update([
                'position' => $drop->position,
            ]);
        }
        
        return redirect()->back()->with('success', __(
            'position has been updated',
        ));
    }
}
