import ParentsLayout from "@/Layouts/ParentsLayout";
import Header from "@/Components/Header";
import UserAvatar from "@/Components/UserAvatar";
import NovelsCardMD from "@/Components/NovelsCardMD";
import Pagination from "@/Components/Pagination";
import NothingToShow from "@/Components/NothingToShow";
export default function UserDetail({ user, novels }) {
    // console.log("user", user);
    return (
        <ParentsLayout>
            <div>
                <Header title={`Author: ${user.name}`} />
                <UserAvatar
                    src={user.avatar}
                    alt={user.name}
                    className="w-40 h-40"
                />
                <div className="mt-4" style={{ margin: "3rem 0" }}>
                    <h3>{user.name} project</h3>
                </div>
                <div>
                    {novels.data.length > 0 ? (
                        <>
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-5">
                                {novels.data.map((novel) => (
                                    <NovelsCardMD
                                        key={novel.id}
                                        novel={novel}
                                    />
                                ))}
                            </div>

                            <div
                                className="mt-6"
                                style={{ marginBottom: "2rem" }}
                            >
                                <Pagination links={novels.links} />
                            </div>
                        </>
                    ) : (
                        <div className="text-center py-12">
                            <NothingToShow isAuthor={false} />
                        </div>
                    )}
                </div>
            </div>
        </ParentsLayout>
    );
}

{
    /* <div className="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-5">
{novels.data.map((novel) => (
    <NovelsCardMD
        key={novel.id}
        novel={novel}
    />
))}
</div>
<div className="mt-6" style={{ marginBottom: "2rem" }}>
<Pagination links={novels.links} />
</div> */
}
