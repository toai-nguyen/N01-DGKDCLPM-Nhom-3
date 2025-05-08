import { MdModeEditOutline } from "react-icons/md";
import "../../css/components/ChapterCard.css";
import { Link } from "@inertiajs/react";
export default function ChapterCard({chapter, isAuthor}) {
    return (
        <div className="flex bg-accent m-2 p-2 chapter-card chapter-card" >
            <p className="font-montserrat ml-2 chapter-title" >
                Chapter {chapter.chapter_number}: {chapter.title}
            </p>
            <span className="font-montserrat mr-2 chapter-date">
                    {chapter.updated_at}
            </span>
            <div className="flex">
                {isAuthor && (
                    <Link
                        href={route("chapter.edit", {novel_id: chapter.novel_id, chapter_id: chapter.id})}
                    >
                        <MdModeEditOutline className="ml-2 author-button"/>
                    </Link>
                )}
            </div>
        </div>
    );
}