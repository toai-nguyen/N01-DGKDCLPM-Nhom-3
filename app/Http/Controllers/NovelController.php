<?php

namespace App\Http\Controllers;

use App\Helpers\CloudinaryHelper;
use Illuminate\Http\Request;
use App\Models\Novel;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Tag; 
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Repositories\NovelRepository;
use App\Repositories\ChapterRepository;
use App\Repositories\TagRepository;
use App\Http\Requests\NovelRequest;
class NovelController extends Controller
{
    protected $novelRepository;
    protected $chapterRepository;
    protected $tagRepository;
    public function __construct(
        NovelRepository $novelRepository, 
        ChapterRepository $chapterRepository,
        TagRepository $tagRepository
    )
    {
        $this->novelRepository = $novelRepository;
        $this->chapterRepository = $chapterRepository;
        $this->tagRepository = $tagRepository;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //home page: show top 5 novels with most followers, 15 random novels
        $topNovels = $this->novelRepository->getTopNovels();

        //get latest chapters
        $latestChapters = $this->chapterRepository->getLastestChapters();

        //get random novels
        $randomNovels = $this->novelRepository->getRandomNovels();
        return Inertia::render('Home', [
            'topNovels' => $topNovels, 
            'latestChapters' => $latestChapters,
            'randomNovels' => $randomNovels]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tags = $this->tagRepository->getAll();
        return Inertia::render('Content/CreateProject' , ['tags' => $tags]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NovelRequest $request)
    { 
        try {
            $validateRequest = $request->validated();
            $image = $request->file('image');
            $imageFile = CloudinaryHelper::uploadImageToCloudinary($image);
            $data = [
                'title' => $validateRequest['title'],
                'description' => $validateRequest['description'],
                'author_id' => Auth::user()->id,
                'image_url' => $imageFile['secure_url'],
                'image_public_id' => $imageFile['public_id'],
                'status' => 'ongoing',
                'followers' => 0,
                'number_of_chapters' => 0,
            ];
            $novel = $this->novelRepository->createNovel($data);
            $novel->tags()->attach($validateRequest['tags']);
            // $this->tagRepository
            return redirect()->route('view-novel', $novel -> id )->with('success', 'Novel created successfully.');
        } catch (\Exception $e) {
            Log::error('Validation error:', ['message' => $e->getMessage()]);
            return redirect()->back()->withErrors(['general' => 'Validation failed.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //show selected novel with all chapters
        $novel = $this->novelRepository->getNovelById($id);
        if (Auth::user()) {
            $isAuthor = Auth::user()->id === $novel['author_id'];
        }
        else {
            $isAuthor = false;
        }
        return Inertia::render('Content/ProjectDetail', [
            'novel' => $novel,
            'isAuthor' => $isAuthor,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {   
        $novelData = Novel::with('tags')->find($id);
        if (Auth::user()->id !== $novelData->author_id) {
            return redirect()->route('home')->with('error', 'You do not have permission to edit this novel.');
        }
        $novel = [
            'id' => $novelData->id,
            'title' => $novelData->title,
            'description' => $novelData->description,
            'image_url' => $novelData->image_url,
            'status' => $novelData->status,
            'tags' => $novelData->tags->map(function ($tag) {
                return ['id' => $tag->id, 'name' => $tag->tag_name];
            }),
        ];
        $tags = Tag::all()
        ->map(function ($tag) {
            return [
                'id' => $tag->id, 
                'name' => $tag->tag_name
            ];
        });
        return Inertia::render('Content/EditProject' , 
        ['novel' => $novel, 
        'tags' => $tags]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateRequest = $request->validate([
            'title' => 'required|string|min:1|max:255',
            'description' => 'required|string|min:1',   
            'status' => 'required|in:ongoing,completed',
            'tags' => 'required|array|min:1',           
            'image' => 'nullable|image|max:5012',       // Không bắt buộc, nhưng nếu có phải là ảnh
        ]);
        
        $novel = Novel::findOrFail($id);
        // Kiểm tra quyền sở hữu
        if (Auth::user()->id !== $novel->author_id) {
            return redirect()->route('home')->with('error', 'You do not have permission to edit this novel.');
        }
        
        $updateData = [
            'title' => $validateRequest['title'],
            'description' => $validateRequest['description'],
            'status' => $validateRequest['status'],
        ];
        
        // Chỉ xử lý ảnh nếu người dùng đã tải lên ảnh mới
        if ($request->hasFile('image')) {            
            // Upload ảnh mới
            try {
                $image = $request->file('image');
                $cloudinaryImage = CloudinaryHelper::updateImage($novel->image_public_id, $image);
                
                $updateData['image_url'] = $cloudinaryImage['secure_url'];
                $updateData['image_public_id'] = $cloudinaryImage['public_id'];
            } catch (\Exception $e) {
                Log::error('Failed to upload new image: ' . $e->getMessage());
                return redirect()->back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
            }
        }
        
        try {
            // Cập nhật novel
            $novel->update($updateData);
            
            // Cập nhật tags
            $novel->tags()->sync($validateRequest['tags']);
            
            return redirect()->route('view-novel', $id)->with('success', 'Novel updated successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to update novel: ' . $e->getMessage());
            return redirect()->back()->withErrors(['general' => 'Failed to update novel: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $novel = Novel::findOrFail($id);
        if (Auth::user()->id !== $novel->author_id) {
            return redirect()->route('home')->with('error', 'You do not have permission to delete this novel.');
        }
        // xóa ảnh trên cloudinary
        if ($novel->image_public_id) {
            try {
                Cloudinary::destroy($novel->image_public_id);
            } catch (\Exception $e) {
                Log::warning('Failed to delete image: ' . $e->getMessage());
            }
        }
        //xóa tags
        $novel->tags()->detach();
        //xóa người theo dõi ở bảng user_follows
        $novel->followers()->detach();
        //xóa các chapter
        $novel->chapters()->delete();
        //xóa novel
        $novel->delete();

        return redirect()->route('home')->with('success', 'Novel deleted successfully.');
    }
}
