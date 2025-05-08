import { FaHome } from "react-icons/fa";
import { MdNavigateNext, MdNavigateBefore } from "react-icons/md";
import { Link } from "@inertiajs/react";
export default function ToolBar({previousChapter, nextChapter, novelId, className}) {
    return (
        <div className={`${className}`}>
            <div className="flex flex-col items-center button-container">
                {previousChapter ? (
                    <Link
                        className="button-item"
                        href={route("chapter.show", {
                            novel_id: novelId,
                            chapter_id: previousChapter,
                        })}
                    >
                        <MdNavigateBefore size={35} />
                    </Link>
                ) : (
                    <div className="button-item opacity-50 cursor-not-allowed">
                        <MdNavigateBefore size={35} />
                    </div>
                )}
                <Link
                    className="button-item"
                    href={route("view-novel", novelId)}
                >
                    <FaHome size={35} />
                </Link>
                {nextChapter ? (
                    <Link
                        className="button-item"
                        href={route("chapter.show", {
                            novel_id: novelId,
                            chapter_id: nextChapter,
                        })}
                    >
                        <MdNavigateNext size={35} />
                    </Link>
                ) : (
                    <div className="button-item opacity-50 cursor-not-allowed">
                        <MdNavigateNext size={35} />
                    </div>
                )}
            </div>
        </div>
    );
}