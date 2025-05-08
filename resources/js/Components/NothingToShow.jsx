import image from "../../../public/image/nothing.webp";
import image_guess from "../../../public/image/nothing_guess.webp";
import { Link } from "@inertiajs/react";
import PrimaryButton from "./PrimaryButton";
import SecondaryButton from "./SecondaryButton";

export default function NothingToShow({ isAuthor, novelId }) {
    return (
        <div className="bg-accent nothing-to-show h-full flex items-center justify-center p-4">
            {isAuthor ? (
                <div className="flex flex-col items-center justify-center w-full">
                    <img
                        src={image}
                        alt="Nothing to show"
                        className="mx-auto mb-4"
                        style={{ height: "200px" }}
                    />
                    <div className="flex flex-row items-center justify-center">
                        <p className="text-center">Write your first chapter</p>
                        <Link href={route("chapter.create", novelId )} style={{ paddingLeft: "1rem" }}>
                            <SecondaryButton>Write</SecondaryButton>
                        </Link>
                    </div>
                </div>
            ) : (
                <div className="flex flex-col items-center justify-center w-full">
                    <img
                        src={image_guess}
                        alt="Nothing to show"
                        className="mx-auto mb-4"
                        style={{ height: "200px" }}
                    />
                    <p className="text-center">The author has not thought of anything yet</p>
                </div>
            )}
        </div>
    );
}
