import PrimaryButton from "../PrimaryButton";
import SecondaryButton from "../SecondaryButton";
import { Link } from "@inertiajs/react";

export default function NotAuth() {
    return (
        <div className="
            min-h-screen 
            flex 
            items-center 
            justify-center
            
        ">
            <div className="
                p-8 
                rounded-lg 
                shadow-md 
                bg-white 
                text-center
                border-2
                border-red-200
            ">
                <p className="genshin-font text-lg font-semibold mb-6 text-gray-800">
                    You must login to use this feature
                </p>
                <div className="flex items-center justify-center space-x-4">
                    <Link href={route("login")}>
                        <SecondaryButton>Login</SecondaryButton>
                    </Link>
                    <Link href={route("register")}>
                        <PrimaryButton>Register</PrimaryButton>
                    </Link>
                </div>
            </div>
        </div>
    );
}
