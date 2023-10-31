<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Http\Resources\AnimalCollection;
use App\Http\Resources\AnimalResource;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AnimalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $name = $request->input('name');
        $type = $request->input('type');
        $conservationStatus = $request->input('conservationStatus');

        $data = Animal::query()
            ->where('name', 'like', '%'.$name.'%')
            ->where('type', 'like', '%'.$type.'%')
            ->where('conservationStatus', 'like', '%'.$conservationStatus.'%')->get();

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the fields from the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', Rule::in(['Mammal', 'Reptile', 'Bird', 'Fish'])],
            'conservationStatus' => ['required', 'string', Rule::in(['Endangered', 'Vulnerable', 'Least Concern'])]
        ]);

        // If validation fails, return a 400 error response
        if ($validator->fails()) {
            return response()->json([], Response::HTTP_BAD_REQUEST);
        }

        // If validation is successful, proceed with saving new animal.
        $animal = new Animal();
        $animal->name = $request->input('name');
        $animal->type = $request->input('type');
        $animal->conservationStatus = $request->input('conservationStatus');
        $saved = $animal->save();

        if ($saved) {
            // After animal is successfully saved, return a 201 response
            return response()->json(['newAnimal' => new AnimalResource($animal)], Response::HTTP_CREATED);
        } else {
            return response()->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $animal = Animal::find($id);

        if (!$animal) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }

        return response()->json($animal);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validate the fields from the request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => ['required', 'string', Rule::in(['Mammal', 'Reptile', 'Bird', 'Fish'])],
            'conservationStatus' => ['required', 'string', Rule::in(['Endangered', 'Vulnerable', 'Least Concern'])]
        ]);

        // If validation fails, return a 400 error response
        if ($validator->fails()) {
            return response()->json([], Response::HTTP_BAD_REQUEST);
        }

        // Search for animal

        $animal = Animal::find($id);

        // If validation is successful, proceed with saving new animal.
        $updated = $animal->update([
            'name' => $request->input('name'),
            'type' => $request->input('type'),
            'conservationType' => $request->input('conservationType')
        ]);

        if ($updated) {
            return response()->json(['animal' => new AnimalResource($animal)], Response::HTTP_OK);
        } else {
            return response()->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $animal = Animal::find($id);

        if (!$animal) {
            // Animal not found. Error code 404
            return response()->json([], Response::HTTP_NOT_FOUND);
        } else {
            $deleted = $animal->delete();

            if ($deleted) {
                // successful deletion
                return response()->json([], Response::HTTP_OK);
            } else {
                // failed to delete
                return response()->json([], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }
    }
}
