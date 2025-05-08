import ParentsLayout from "@/Layouts/ParentsLayout";
import ChapterHeader from "@/Components/Novel/ChapterHeader";
import ChapterContent from "@/Components/Novel/ChapterContent";
import ToolBar from "@/Components/Novel/ToolBar";
import "../../../css/layouts/ChapterView.css";
export default function ChapterView({ chapter }) {
    console.log(chapter);
    return (
        <ParentsLayout>
            <div className="
            chapter-container 
            flex flex-col
            justify-center items-center relative bg-accent">
                <div className="chapter-view bg-accent">
                    <ChapterHeader 
                        number={chapter.chapter_number} 
                        title={chapter.title} 
                        updatedAt={chapter.updated_at}
                        className="flex 
                        flex-col justify-center items-center"
                    />
                    <ChapterContent content={chapter.content}
                        className=""
                    />
                </div>
                <ToolBar className = "absolute right-2 bottom-2" 
                    novelId={chapter.novel_id}
                    previousChapter={chapter.previousChapter}
                    nextChapter={chapter.nextChapter}
                />
            </div>
        </ParentsLayout>
    );
}
