import { Link } from "@inertiajs/react";
import "../../css/components/NovelsCard.css";

export default function NovelsCard({ novel }) {
    return (
        <div className="flex gap-2">
            <div style={{ height: "80px", minWidth: "56px", maxWidth: "56px" }}>
                <Link href={route("view-novel", novel.novel_id)}>
                    <img src={novel.image_url} alt={novel.title} />
                </Link>
            </div>
            <div className="flex-grow flex flex-col justify-evenly">
                {/* Link to the novel */}
                <Link href={route("view-novel", novel.novel_id)}>
                    <p className="text-base line-clamp-1 break-all">
                        {novel.title}
                    </p>
                </Link>
                {/* Link to the chapter */}
                <Link
                    href={route("chapter.show", {
                        novel_id: novel.novel_id,
                        chapter_id: novel.chapter_id,
                    })}
                >
                    <p className="line-clamp-1 font-montserrat">
                        Ch. {novel.chapter_number}
                    </p>
                </Link>
                {/* Link to the author */}
                <Link href= {route("view-user", novel.author_id)} >
                    <p
                        className="line-clamp-1 font-montserrat"
                        style={{ fontWeight: "500" }}
                    >
                        {novel.author_name}
                    </p>
                </Link>
            </div>
        </div>
    );
}
// href={route("view-user", novel.author_id)}
