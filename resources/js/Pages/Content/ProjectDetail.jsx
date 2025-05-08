import PopularNovelsCard from "@/Components/PopularNovelsCard";
import ParentsLayout from "@/Layouts/ParentsLayout";
import ListChapter from "@/Components/Novel/ListChapter";
import AuthorInfo from "@/Components/Novel/AuthorInfo";
import Comment from "@/Components/Novel/Comment";
export default function ProjectDetail({auth, novel, isAuthor}) {
    console.log(novel);
    return (
        <ParentsLayout>
            <div className="novel-container">
                <PopularNovelsCard novel={novel} />
                <div className="novel-infomation flex flex-row" style={{marginBottom: '3rem'}}>
                    <ListChapter chapters={novel.chapters} isAuthor={isAuthor} novelId={novel.id}/>
                    <AuthorInfo novel = {novel} isAuthor = {isAuthor} auth={auth}/>
                </div>
                <Comment />
            </div>
        </ParentsLayout>
    );
}