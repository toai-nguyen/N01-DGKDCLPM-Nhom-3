import ApplicationLogo from "@/Components/ApplicationLogo";
import SecondaryButton from "@/Components/SecondaryButton";
import { usePage, Link } from "@inertiajs/react";

export default function GuestLayout({ children }) {
    const { url } = usePage();
    const isLoginPage = url === '/login';
    return (
        <div
            className="
        flex 
        min-h-screen 
        flex-col 
        items-center 
        bg-gray-100 
        pt-6 
        sm:justify-center 
        sm:pt-0 
        dark:bg-gray-900
        bg-[url('..\..\public\image\background.jpg')]
        bg-cover
        relative
        "
        >
            <Link
                href={route(isLoginPage ? "register" : "login")}
                className="absolute right-4 top-4"
            >
                <SecondaryButton>
                    {isLoginPage ? "Create an account" : "Log in"}
                </SecondaryButton>
            </Link>
            <Link href="/" className="right-4 bottom-4 absolute ">
                <SecondaryButton>Use guess mode</SecondaryButton>
            </Link>
            <div
                className="
            mt-6 w-full
            overflow-hidden 
            bg-white
            px-6 
            py-4 
            shadow-md 
            sm:max-w-md 
            sm:rounded-lg 
            dark:bg-gray-800"
            >
                <div className="flex justify-center">
                    <Link href="/login">
                        <ApplicationLogo className="h-20 fill-current text-gray-500" />
                    </Link>
                </div>
                {children}
            </div>
        </div>
    );
}
