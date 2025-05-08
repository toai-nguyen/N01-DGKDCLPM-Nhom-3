import ParentsLayout from "@/Layouts/ParentsLayout";
import Header from "@/Components/Header";
import NovelsCardMD from "@/Components/NovelsCardMD";
import Pagination from "@/Components/Pagination";
import { Link } from "@inertiajs/react";
export default function SearchResult({ search, novels }) {
    return (
        <ParentsLayout>
            <div>
                <Header title={`Result for ${search}`} />
            </div>
            {novels.data.length > 0 ? (
                <>
                    <div className="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-5">
                        {novels.data.map((novel) => (
                            <NovelsCardMD key={novel.id} novel={novel} />
                        ))}
                    </div>

                    <div className="mt-6" style={{ marginBottom: "2rem" }}>
                        <Pagination links={novels.links} />
                    </div>
                </>
            ) : (
                <div className="text-center py-12">
                    <p className="text-xl text-gray-500">
                        Nothing.
                    </p>
                    <Link
                        href="/"
                        className="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                    >
                        Discover Novels
                    </Link>
                </div>
            )}
        </ParentsLayout>
    );
}
