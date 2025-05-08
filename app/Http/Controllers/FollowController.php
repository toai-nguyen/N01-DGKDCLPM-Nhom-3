<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Novel;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class FollowController extends Controller
{
    // Hành động theo dõi hoặc hủy theo dõi
    public function toggleFollow(Request $request, $novelId)
    {
        // Kiểm tra đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user();
        $novel = Novel::findOrFail($novelId);

        // Kiểm tra xem user đã theo dõi truyện chưa
        $isFollowing = $user->followedNovels()->where('novel_id', $novelId)->exists();
        if ($isFollowing) {
            // Nếu đã theo dõi, hủy theo dõi
            $user->followedNovels()->detach($novelId);
            
            // Giảm số lượng người theo dõi
            $novel->decrement('followers');
            
            $message = 'Unfollowed successfully';
        } else {
            // Nếu chưa theo dõi, thêm theo dõi
            $user->followedNovels()->attach($novelId);
            
            // Tăng số lượng người theo dõi
            $novel->increment('followers');
            
            $message = 'Followed successfully';
        }

        // Trả về kết quả dạng JSON nếu là request AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'isFollowing' => !$isFollowing,
                'followersCount' => $novel->followers,
                'message' => $message
            ]);
        }

        // Nếu không phải request AJAX, redirect lại trang truyện
        return redirect()->back();
    }
    
    // Kiểm tra trạng thái follow
    public function checkFollowStatus(Request $request, $novelId)
    {
        if (!Auth::check()) {
            return response()->json(['isFollowing' => false]);
        }
        
        $user = Auth::user();
        /**
         * @var \App\Models\User $user
         */
        $isFollowing = $user->followedNovels()->where('novel_id', $novelId)->exists();
        
        return response()->json(['isFollowing' => $isFollowing]);
    }
}