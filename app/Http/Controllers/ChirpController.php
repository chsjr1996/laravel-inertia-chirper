<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChirpDeleteRequest;
use App\Http\Requests\ChirpStoreRequest;
use App\Http\Requests\ChirpUpdateRequest;
use App\Models\Chirp;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ChirpController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return Inertia::render("Chirps/Index", [
            "chirps" => Chirp::with("user:id,name")->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ChirpStoreRequest $request): RedirectResponse
    {
        $request->user()->chirps()->create($request->validated());

        return redirect(route("chirps.index"));
    }

    /**
     * Display the specified resource.
     */
    public function show(Chirp $chirp)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ChirpUpdateRequest $request,
        Chirp $chirp
    ): RedirectResponse {
        $chirp->update($request->validated());

        return redirect(route("chirps.index"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ChirpDeleteRequest $request, Chirp $chirp)
    {
        $chirp->delete();

        return redirect(route("chirps.index"));
    }
}
