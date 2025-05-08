import Header from "../Header";
import CommentCard from "../CommentCard";
import CommentView from "../CommentView.jsx";
export default function Comment() {
    return (
        <div className="">
            <Header title="Comment" />
            <div className="p-4">
                <CommentCard />
            </div>
            <CommentView />
        </div>
    );
}
