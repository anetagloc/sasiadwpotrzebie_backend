<?php

namespace App\Http\Controllers\API;

use OA\RequestBody;
use App\Models\Rating;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Schema;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\MediaType;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/ratings",
        summary: "Get all ratings",
        description: "Fetches a list of all ratings.",
        tags: ["Ratings"],
        responses: [
            new OA\Response(
                response: 200,
                description: "List of all ratings",
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error"
            )
        ]
    )]
    public function index()
    {
        return Rating::all();
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
    #[OA\Post(
        path: "/api/ratings",
        summary: "Create a new rating",
        description: "Adds a new rating to the system.",
        tags: ["Ratings"],
        responses: [
            new OA\Response(
                response: 201,
                description: "Rating created successfully",
            ),
            new OA\Response(
                response: 400,
                description: "Bad request"
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error"
            )
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:255',
        ]);

        $rating = Rating::create([
            'user_id' => auth()->id(),
            'role_id' => $request->role_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Rating added successfully', 'rating' => $rating], 201);
    }

    /**
     * Display the specified resource.
     */
    #[OA\Get(
        path: "/api/ratings/{id}",
        summary: "Get a specific rating",
        description: "Fetches the details of a specific rating by its ID.",
        tags: ["Ratings"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID of the rating",
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Details of the rating",
            ),
            new OA\Response(
                response: 404,
                description: "Rating not found"
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error"
            )
        ]
    )]
    public function show(Rating $rating, Request $id)
    {
        $rating = Rating::find($id);
        if (!$rating) {
            return response()->json(['message' => 'Rating not found'], 404);
        }

        return response()->json($rating);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rating $rating)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Put(
        path: "/api/ratings/{id}",
        summary: "Update a specific rating",
        description: "Updates the details of a specific rating by its ID.",
        tags: ["Ratings"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID of the rating to update",
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Rating updated successfully",
            ),
            new OA\Response(
                response: 400,
                description: "Bad request"
            ),
            new OA\Response(
                response: 404,
                description: "Rating not found"
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error"
            )
        ]
    )]
    public function update(Request $request, Rating $rating)
    {
        $this->authorize('update', $rating);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:255',
        ]);

        $rating->update($validated);

        return response()->json(['message' => 'Rating updated successfully', 'rating' => $rating]);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: "/api/ratings/{id}",
        summary: "Delete a specific rating",
        description: "Deletes a specific rating by its ID.",
        tags: ["Ratings"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                description: "ID of the rating to delete",
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Rating deleted successfully"
            ),
            new OA\Response(
                response: 404,
                description: "Rating not found"
            ),
            new OA\Response(
                response: 500,
                description: "Internal server error"
            )
        ]
    )]
    public function destroy(Rating $rating)
    {
        $rating->delete();

        return response()->json(['message' => 'Rating deleted successfully'], 200);
    }
}
