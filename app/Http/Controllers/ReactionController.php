<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostReaction;
use App\Support\Visitor;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReactionController extends Controller
{
    /** Toggle the visitor's reaction on a post. */
    public function store(Request $request, string $slug)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(array_keys(PostReaction::TYPES))],
        ]);

        $post = Post::query()->published()->where('slug', $slug)->firstOrFail();
        $visitor = Visitor::hash($request);

        $existing = $post->reactions()->where('type', $data['type'])->where('visitor_hash', $visitor)->first();

        if ($existing) {
            $existing->delete();
        } else {
            try {
                $post->reactions()->create(['type' => $data['type'], 'visitor_hash' => $visitor]);
            } catch (UniqueConstraintViolationException) {
                // A double click raced us; the reaction already exists.
            }
        }

        return response()->json([
            'counts' => $post->reactionCounts(),
            'mine' => self::mine($post, $visitor),
        ]);
    }

    /** Reaction types the visitor has used on the post. */
    public static function mine(Post $post, string $visitor): array
    {
        return $post->reactions()->where('visitor_hash', $visitor)->pluck('type')->all();
    }
}
