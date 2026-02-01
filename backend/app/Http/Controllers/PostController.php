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
            $query = Post::with(['user:id,first_name,last_name,email,profile_picture'])
                        ->withCount(['likes', 'comments']);

            // Filter by visibility based on user role
            $user = Auth::user();
            if ($user->role === 'admin') {
                // Admins see all posts
            } else {
                // Regular users see public and alumni_only posts
                $query->whereIn('visibility', ['public', 'alumni_only']);
            }

            $posts = $query->orderBy('created_at', 'desc')
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
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
                'visibility' => 'nullable|in:public,alumni_only,admin_only',
            ]);

            $imagePath = null;
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                
                // Ensure directory exists
                $publicPath = public_path('storage/posts');
                if (!file_exists($publicPath)) {
                    mkdir($publicPath, 0755, true);
                }
                
                // Move file to public/storage/posts
                $image->move($publicPath, $filename);
                
                // Store relative path for database
                $imagePath = 'posts/' . $filename;
                
                // Log for debugging
                \Log::info('Image uploaded', [
                    'filename' => $filename,
                    'full_path' => $publicPath . '/' . $filename,
                    'db_path' => $imagePath,
                    'file_exists' => file_exists($publicPath . '/' . $filename)
                ]);
            }

            $post = Post::create([
                'user_id' => Auth::id(),
                'content' => $validated['content'],
                'image_path' => $imagePath,
                'visibility' => $validated['visibility'] ?? 'alumni_only',
            ]);

            // Load user relationship
            $post->load('user:id,first_name,last_name,email,profile_picture');

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

