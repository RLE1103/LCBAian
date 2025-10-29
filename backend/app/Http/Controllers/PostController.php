<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $posts = Post::with(['user:id,first_name,last_name,email'])
                        ->withCount(['likes', 'comments'])
                        ->orderBy('created_at', 'desc')
                        ->paginate($request->get('per_page', 15));

            // Get user's like status for each post
            foreach ($posts as $post) {
                $post->is_liked = Like::where('post_id', $post->post_id)
                                     ->where('user_id', Auth::id())
                                     ->exists();
                $post->user_likes = $post->likes()->count();
                $post->user_comments = $post->comments()->with('user:id,first_name,last_name')->get();
            }

            return response()->json([
                'success' => true,
                'data' => $posts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string',
                'image_path' => 'nullable|string',
            ]);

            $post = Post::create([
                'user_id' => Auth::id(),
                ...$validated
            ]);

            return response()->json([
                'success' => true,
                'data' => $post,
                'message' => 'Post created successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function toggleLike($id): JsonResponse
    {
        try {
            $like = Like::where('post_id', $id)
                       ->where('user_id', Auth::id())
                       ->first();

            if ($like) {
                $like->delete();
                $liked = false;
            } else {
                Like::create([
                    'post_id' => $id,
                    'user_id' => Auth::id()
                ]);
                $liked = true;
            }

            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => Like::where('post_id', $id)->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function addComment(Request $request, $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'content' => 'required|string',
            ]);

            $comment = Comment::create([
                'post_id' => $id,
                'user_id' => Auth::id(),
                'content' => $validated['content']
            ]);

            return response()->json([
                'success' => true,
                'data' => $comment,
                'message' => 'Comment added successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

