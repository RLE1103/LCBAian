<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Cloudinary\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Post::with(['user:id,first_name,last_name,email,profile_picture']);

            // Filter by visibility based on user role
            $user = Auth::user();
            if (!$user) {
                $query->where('visibility', 'public');
            } elseif ($user->role === 'admin') {
                // Admins see all posts
            } else {
                // Regular users see public and alumni_only posts
                $query->whereIn('visibility', ['public', 'alumni_only']);
            }

            $posts = $query->orderBy('created_at', 'desc')
                          ->paginate($request->get('per_page', 15));

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
            $user = $request->user();
            if (!$user || $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'visibility' => 'nullable|in:public,alumni_only,admin_only',
                'media' => 'nullable|array',
                'media.*' => 'file|max:204800',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $mediaFiles = $request->file('media', []);
            $validationError = $this->validateMediaFiles($mediaFiles);
            if ($validationError) {
                return $validationError;
            }

            $mediaItems = [];
            if ($mediaFiles) {
                $cloudinary = $this->cloudinary();
                if (!$cloudinary) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cloudinary configuration is missing.'
                    ], 500);
                }
                $mediaItems = $this->uploadMediaFiles($mediaFiles, $cloudinary);
            }

            $post = Post::create([
                'user_id' => $user->id,
                'title' => $request->input('title'),
                'content' => $request->input('content'),
                'image_path' => null,
                'media' => $mediaItems,
                'visibility' => $request->input('visibility', 'public'),
            ]);

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

    public function update(Request $request, int $postId): JsonResponse
    {
        try {
            $user = $request->user();
            if (!$user || $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $post = Post::find($postId);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'title' => 'sometimes|required|string|max:255',
                'content' => 'sometimes|required|string',
                'visibility' => 'nullable|in:public,alumni_only,admin_only',
                'media' => 'nullable|array',
                'media.*' => 'file|max:204800',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $mediaFiles = $request->file('media', []);
            $validationError = $this->validateMediaFiles($mediaFiles);
            if ($validationError) {
                return $validationError;
            }

            $payload = [];
            if ($request->has('title')) {
                $payload['title'] = $request->input('title');
            }
            if ($request->has('content')) {
                $payload['content'] = $request->input('content');
            }
            if ($request->has('visibility')) {
                $payload['visibility'] = $request->input('visibility');
            }

            if ($mediaFiles) {
                $cloudinary = $this->cloudinary();
                if (!$cloudinary) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cloudinary configuration is missing.'
                    ], 500);
                }

                $mediaItems = $this->uploadMediaFiles($mediaFiles, $cloudinary);
                $payload['media'] = $mediaItems;
                $payload['image_path'] = null;

                if ($post->media && is_array($post->media)) {
                    $this->deleteMediaItems($post->media, $cloudinary);
                }
            }

            if ($payload) {
                $post->update($payload);
            }

            $post->load('user:id,first_name,last_name,email,profile_picture');

            return response()->json([
                'success' => true,
                'data' => $post,
                'message' => 'Post updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(int $postId): JsonResponse
    {
        try {
            $user = Auth::user();
            if (!$user || $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $post = Post::find($postId);
            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            if ($post->media && is_array($post->media)) {
                $cloudinary = $this->cloudinary();
                if (!$cloudinary) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Cloudinary configuration is missing.'
                    ], 500);
                }
                $this->deleteMediaItems($post->media, $cloudinary);
            }

            if ($post->image_path) {
                $legacyPath = public_path('storage/' . ltrim($post->image_path, '/'));
                if (file_exists($legacyPath)) {
                    unlink($legacyPath);
                }
            }

            $post->delete();

            return response()->json([
                'success' => true,
                'message' => 'Post deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function cloudinary(): ?Cloudinary
    {
        $cloudName = config('services.cloudinary.cloud_name');
        $apiKey = config('services.cloudinary.api_key');
        $apiSecret = config('services.cloudinary.api_secret');

        if (!$cloudName || !$apiKey || !$apiSecret) {
            return null;
        }

        return new Cloudinary([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    private function validateMediaFiles(array $mediaFiles): ?JsonResponse
    {
        if (!$mediaFiles) {
            return null;
        }

        $allowedImageMimes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
        $allowedVideoMimes = [
            'video/mp4',
            'video/quicktime',
            'video/webm',
            'video/ogg'
        ];
        $imageMax = 5 * 1024 * 1024;
        $videoMax = 200 * 1024 * 1024;

        foreach ($mediaFiles as $file) {
            if (!$file) {
                continue;
            }

            $mime = $file->getMimeType() ?? '';
            $size = $file->getSize() ?? 0;

            if (in_array($mime, $allowedImageMimes, true)) {
                if ($size > $imageMax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'media' => ['Image files must be 5MB or smaller.']
                        ]
                    ], 422);
                }
                continue;
            }

            if (in_array($mime, $allowedVideoMimes, true)) {
                if ($size > $videoMax) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => [
                            'media' => ['Video files must be 200MB or smaller.']
                        ]
                    ], 422);
                }
                continue;
            }

            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => [
                    'media' => ['Only JPG, PNG, GIF, WebP, MP4, MOV, or WebM files are allowed.']
                ]
            ], 422);
        }

        return null;
    }

    private function uploadMediaFiles(array $mediaFiles, Cloudinary $cloudinary): array
    {
        $uploaded = [];

        foreach ($mediaFiles as $file) {
            if (!$file) {
                continue;
            }

            $mime = $file->getMimeType() ?? '';
            $isVideo = str_starts_with($mime, 'video/');
            $options = [
                'folder' => 'posts',
                'resource_type' => $isVideo ? 'video' : 'image',
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ];

            if ($isVideo) {
                $options['transformation'] = [
                    [
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                    ]
                ];
            } else {
                $options['transformation'] = [
                    [
                        'width' => 1600,
                        'height' => 1600,
                        'crop' => 'limit',
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                    ]
                ];
            }

            $upload = $cloudinary->uploadApi()->upload($file->getRealPath(), $options);

            $uploaded[] = [
                'public_id' => $upload['public_id'] ?? null,
                'resource_type' => $upload['resource_type'] ?? ($isVideo ? 'video' : 'image'),
                'secure_url' => $upload['secure_url'] ?? null,
                'format' => $upload['format'] ?? null,
            ];
        }

        return $uploaded;
    }

    private function deleteMediaItems(array $mediaItems, Cloudinary $cloudinary): void
    {
        foreach ($mediaItems as $item) {
            if (!is_array($item)) {
                continue;
            }
            $publicId = $item['public_id'] ?? null;
            if (!$publicId) {
                continue;
            }
            $resourceType = $item['resource_type'] ?? 'image';
            try {
                $cloudinary->uploadApi()->destroy($publicId, [
                    'resource_type' => $resourceType,
                    'invalidate' => true
                ]);
            } catch (\Exception) {
            }
        }
    }

}
