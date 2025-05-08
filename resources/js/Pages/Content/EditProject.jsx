import ParentsLayout from "@/Layouts/ParentsLayout";
import Header from "@/Components/Header";
import NotAuth from "@/Components/Home/NotAuth";
import NovelForm from "@/Components/Novel/NovelForm";

export default function EditProject({auth, novel, tags}) {
    const headerTitle = `Edit ${novel.title}`;
    return (
        <ParentsLayout>
            {!auth.user ? (
                <NotAuth />
            ) : (
                <div>
                    <Header title={headerTitle} />
                    <NovelForm novel={novel} tags={tags} isEditing = {true} />
                </div>
            )}
        </ParentsLayout>
    )
}