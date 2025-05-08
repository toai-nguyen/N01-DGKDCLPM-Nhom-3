import { Link } from "@inertiajs/react";
import NothingToShow from "../NothingToShow";
import ChapterCard from "../ChapterCard";
import Header from "../Header";

export default function ListChapter({chapters, isAuthor, novelId}) {
    const len = chapters.length;

    return (
        <div className="w-3/4 p-4">
            {len > 0 ? (
                <div>
                    <Header title="List of chapter"/>
                    {chapters.map((chapter) => (
                        <Link 
                        href={route("chapter.show", {novel_id: chapter.novel_id, chapter_id:chapter.id})} 
                        key={chapter.id}>
                            <ChapterCard chapter={chapter} isAuthor={isAuthor} />
                        </Link>
                    ))}
                </div>
            ) : (
                <NothingToShow isAuthor = {isAuthor} novelId={novelId} />
            )}
        </div>
    );
}