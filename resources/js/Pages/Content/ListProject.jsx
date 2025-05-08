import HomeLayout from '@/Layouts/ParentsLayout';
import NotAuth from '@/Components/Home/NotAuth';
import Header from '@/Components/Header';
import NovelsCardMD from '@/Components/NovelsCardMD';
import { Link } from '@inertiajs/react';
import Pagination from '@/Components/Pagination';
import PrimaryButton from '@/Components/PrimaryButton';
export default function ListProject({ auth, projects }) {
    return (
        <HomeLayout>
            {!auth.user ? (
                <NotAuth />
            ) : (
                <div>
                    <Header title="Your Project" />
                    {projects.data.length > 0 ? (
                        <>
                            <div className="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-5">
                                {projects.data.map((novel) => (
                                    <NovelsCardMD
                                        key={novel.id}
                                        novel={novel}
                                    />
                                ))}
                            </div>

                            <div className="mt-6" style={{ marginBottom: "2rem" }}>
                                <Pagination links={projects.links} />
                            </div>
                        </>
                    ) : (
                        <div className="text-center py-12">
                            <p className="text-xl text-gray-500">
                                You haven't any project yet.
                            </p>
                            <Link
                                href={route('create-project')}
                                className="mt-4 inline-block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                            >
                                Create Project
                            </Link>
                        </div>
                    )}
                </div>
            )}
        </HomeLayout>
    );
}