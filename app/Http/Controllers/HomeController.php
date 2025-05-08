<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Novel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Tag;

class HomeController extends Controller
{
    public function follow()
    {
        $user = Auth::user();

        $followNovels = [];
        if ($user) {
            $followNovels = Novel::with('tags', 'user')
                ->join('user_follows', 'novels.id', '=', 'user_follows.novel_id')
                ->where('user_follows.user_id', $user->id)
                ->orderBy('novels.updated_at', 'desc')
                ->paginate(15)
                ->through(function ($novel) {
                    return [
                        'id' => $novel->novel_id,
                        'title' => $novel->title,
                        'image_url' => $novel->image_url
                    ];
                });
        }

        return Inertia::render('Content/Following', ['followNovels' => $followNovels]);
    }

    public function viewAdvancedSearch()
    {
        $tags = Tag::all();
        return Inertia::render('Content/AdvancedSearch', ['tags' => $tags]);    
    }
    public function query(Request $request)
    {        
        // Xử lý dữ liệu từ form - kiểm tra dữ liệu được gửi trong request->data
        $searchData = $request->input('data', []);
                
        $query = Novel::query()->with('tags', 'user');
        
        // Tìm theo tiêu đề - chỉ khi có title không rỗng
        if (!empty($searchData['title'])) {
            $query->where('title', 'like', '%' . $searchData['title'] . '%');
        }
        
        // Tìm theo tác giả - chỉ khi có author không rỗng
        if (!empty($searchData['author'])) {
            $query->whereHas('user', function($q) use ($searchData) {
                $q->where('name', 'like', '%' . $searchData['author'] . '%');
            });
            Log::info('Filtering by author:', ['author' => $searchData['author']]);
        }
        
        // Tìm theo trạng thái - chỉ khi có status và khác "all"
        if (!empty($searchData['status']) && $searchData['status'] != 'all') {
            $query->where('status', $searchData['status']);
        }
        
        // Tìm theo tags - chỉ khi có tags và là mảng không rỗng
        if (!empty($searchData['tags']) && is_array($searchData['tags']) && count($searchData['tags']) > 0) {
            $query->whereHas('tags', function($q) use ($searchData) {
                $q->whereIn('tags.id', $searchData['tags']);
            });
        }
        
        $novels = $query->orderBy('updated_at', 'desc')
            ->paginate(15);
        
        
        return Inertia::render('Content/SearchResult', [
            'search' => 'Advanced search',
            'novels' => $novels
        ]);
    }

    //show all user projects
    public function listProject()
    {
        $user = Auth::user();
        $projects = [];
        if ($user) {
            $projects = Novel::where('author_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->paginate(15)
                ->through(function ($project) {
                    return [
                        'id' => $project->id,
                        'title' => $project->title,
                        'image_url' => $project->image_url
                    ];
                });
        }
        return Inertia::render('Content/ListProject', ['projects' => $projects]);
    }
    public function notifications()
    {
        return Inertia::render('Content/Notifications');
    }
    public function search($search)
    {
        $novels = Novel::where('title', 'LIKE', "%{$search}%")
        ->with('tags', 'user')
        ->orderBy('updated_at', 'desc')
        ->paginate(15)
        ->through(function ($novel) {
            return [
                'id' => $novel->id,
                'title' => $novel->title,
                'image_url' => $novel->image_url,
            ];
        });
        return Inertia::render('Content/SearchResult', 
        ['search' => $search,
        'novels' => $novels]
    );
    }
    public function settings()
    {
        return Inertia::render('Content/Settings');
    }
}
