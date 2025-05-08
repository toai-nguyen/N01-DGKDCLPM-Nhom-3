import { Link, router } from "@inertiajs/react";
import SecondaryButton from "@/Components/SecondaryButton";
import PrimaryButton from "../PrimaryButton";
import DangerButton from "../DangerButton";
import DeleteNovelsForm from "./DeleteNovelsForm";
import { useEffect, useState } from "react";
import axios from "axios";
import UserAvatar from "../UserAvatar";

export default function AuthorInfo({ novel, isAuthor, auth }) {
    const [isFollowing, setIsFollowing] = useState(false);
    const [followersCount, setFollowersCount] = useState(novel.followers);
    const [loading, setLoading] = useState(false);
    console.log("novel", novel);
    // Kiểm tra trạng thái follow khi component được render
    useEffect(() => {
        // Chỉ kiểm tra nếu người dùng đã đăng nhập và không phải tác giả
        if (auth.user && !isAuthor) {
            axios.get(route('novel.follow-status', novel.id))
                .then(response => {
                    setIsFollowing(response.data.isFollowing);
                })
                .catch(error => {
                    console.error("Error checking follow status:", error);
                });
        }
    }, [novel.id, auth.user, isAuthor]);

    // Hàm xử lý follow/unfollow
    const handleToggleFollow = () => {
        if (!auth.user) {
            // Chuyển hướng tới trang đăng nhập nếu chưa đăng nhập
            window.location.href = route('login');
            return;
        }

        setLoading(true);
        
        axios.post(route('novel.toggle-follow', novel.id))
            .then(response => {
                setIsFollowing(response.data.isFollowing);
                setFollowersCount(response.data.followersCount);
                setLoading(false);
            })
            .catch(error => {
                console.error("Error toggling follow:", error);
                setLoading(false);
            });
    };

    // Phần JSX còn lại
    return (
        <div className="author-info w-1/3 ml-4 p-4 bg-white rounded shadow">
            <h3 className="text-lg font-bold mb-3">Author</h3>
            
            <div className="flex items-center mb-4">
                <div className="w-10 h-10 rounded-full mr-3">
                    <UserAvatar src={novel.avatar_url} />
                </div>
                <span className="font-medium">{novel.author_name}</span>
            </div>
            
            <div className="mb-4">
                <div className="flex justify-between py-1">
                    <span className="text-gray-600">Status:</span>
                    <span>{novel.status}</span>
                </div>
                <div className="flex justify-between py-1">
                    <span className="text-gray-600">Followers:</span>
                    <span>{followersCount}</span>
                </div>
                <div className="flex justify-between py-1">
                    <span className="text-gray-600">Chapters:</span>
                    <span>{novel.number_of_chapters}</span>
                </div>
            </div>
            
            {isAuthor ? (
                <div className="author-actions mt-4 flex" style={{ justifyContent: "space-between" }}>
                    <Link href={route("chapter.create", novel.id)}>
                        <PrimaryButton className="">
                            Add new chapter
                        </PrimaryButton>
                    </Link>
                    <Link href={route("edit-novel", novel.id)}>
                        <SecondaryButton className="">
                            Edit
                        </SecondaryButton>
                    </Link>
                    <DeleteNovelsForm novel={novel} />
                </div>
            ) : (
                <div className="author-actions mt-4">
                    <PrimaryButton 
                        className={`${isFollowing ? 'bg-gray-500' : ''}`}
                        onClick={handleToggleFollow}
                        disabled={loading}
                    >
                        {loading ? 'Processing...' : isFollowing ? 'Unfollow' : 'Follow'}
                    </PrimaryButton>
                </div>
            )}
            
            <div className="mt-4">
                <h4 className="font-medium mb-2">Tags</h4>
                <div className="flex flex-wrap gap-1">
                    {novel.tags.map(tag => (
                        <span key={tag.id} className="px-2 py-1 bg-gray-100 text-sm rounded">
                            {tag.tag_name || tag.name}
                        </span>
                    ))}
                </div>
            </div>
        </div>
    );
}