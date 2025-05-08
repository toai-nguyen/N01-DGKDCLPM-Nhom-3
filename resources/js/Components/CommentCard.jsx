import TextInput from "./TextInput";
import SecondaryButton from "./SecondaryButton";
import { MdEmojiEmotions } from "react-icons/md";
import { Card, CardContent } from "./Novel/Card";
export default function CommentCard() {
    return (
        <Card className="p-4 w-full max-w-lg">
            <CardContent className="flex items-center space-x-2">
                <img
                    src="https://www.comfortzone.com/behavior-blog/cat-behavior/why-is-my-cat-meowing"
                    alt="Avatar"
                    className="w-10 h-10 rounded-full"
                />
                <TextInput
                    className="flex-1 p-2 border rounded-lg"
                    placeholder="Write a comment..."
                    // value={comment}
                    // onChange={(e) => setComment(e.target.value)}
                />
            </CardContent>
            <div
                className="flex items-center mt-4 w-full"
                style={{ justifyContent: "space-between" }}
            >
                <MdEmojiEmotions className="w-5 h-5 cursor-pointer" />
                <SecondaryButton>Post</SecondaryButton>
            </div>
        </Card>
    );
}
