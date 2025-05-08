import PrimaryButton from "./PrimaryButton";
import SecondaryButton from "./SecondaryButton";
import TextInput from "./TextInput";
import UserAvatar from "./UserAvatar";
import { React, useState, useEffect } from "react";
import ApplicationLogo from "./ApplicationLogo";
import { Link, router } from "@inertiajs/react";
import Dropdown from "./Dropdown";
import { CgDetailsMore } from "react-icons/cg";
import { IoMdNotifications } from "react-icons/io";
import image from "../../../public/image/default_avatar.jpg";
import "../../css/components/NavigatorBar.css";

export default function NavigatorBar({
    auth,
    userName,
    isOpen,
    toggleSidebar,
    removeFixed = false,
}) {
    const [isScrolled, setIsScrolled] = useState(false);
    const [searchQuery, setSearchQuery] = useState("");

    // Thêm state cho số thông báo
    const [unreadCount, setUnreadCount] = useState(0);

    // Thêm useEffect để lấy số thông báo chưa đọc
    useEffect(() => {
        if (auth.user) {
            const fetchUnreadCount = () => {
                axios
                    .get(route("notifications.unread-count"))
                    .then((response) => {
                        setUnreadCount(response.data.count);
                    })
                    .catch((error) =>
                        console.error("Error fetching unread count:", error)
                    );
            };

            fetchUnreadCount();

            // Poll cho số thông báo mới mỗi phút
            const interval = setInterval(fetchUnreadCount, 60000);

            return () => {
                clearInterval(interval);
            };
        }
    }, [auth.user]);
    useEffect(() => {
        const handleScroll = () => {
            if (window.scrollY > 0) {
                setIsScrolled(true);
            } else {
                setIsScrolled(false);
            }
        };

        window.addEventListener("scroll", handleScroll);
        return () => {
            window.removeEventListener("scroll", handleScroll);
        };
    }, []);

    //add search function
    const handleSearch = (e) => {
        e.preventDefault();
        if (searchQuery.trim()) {
            router.visit(route("search", { search: searchQuery.trim() }));
        }
    };
    return (
        <nav
            className={`
                navigator-bar
                ${removeFixed ? "fixed" : ""} 
                top-0 
                transition-colors
                duration-300
                ${!isOpen ? "sidebar-closed" : ""}
                ${isScrolled ? "bg-white" : "bg-transparent"}`}
        >
            <div
                className="
                flex
                items-center
                h-full
                px-4
                lg:px-8
                "
            >
                {/* Hiển thị nút mở sidebar khi sidebar đang đóng */}
                {!isOpen && (
                    <div className="flex items-center">
                        <button
                            className="text-gray-700 hover:text-gray-900 mr-4"
                            onClick={toggleSidebar}
                            aria-label="Open sidebar"
                        >
                            <CgDetailsMore size={20} />
                        </button>
                        <Link href={route("home")}>
                            <ApplicationLogo className="h-10" />
                        </Link>
                    </div>
                )}

                <div className="flex-grow"></div>

                <form
                    onSubmit={handleSearch}
                    className="relative flex items-center"
                >
                    <TextInput
                        className="text-input pr-10"
                        placeholder="Search novels..."
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                    />
                </form>

                {auth.user ? (
                    <div className="flex items-center bg-white space-x-4">
                        <Dropdown>
                            <Dropdown.Trigger>
                                <UserAvatar
                                    src={auth.user.avatar_url}
                                    alt={auth.user.name}
                                    className="h-10 w-10 cursor-pointer"
                                />
                            </Dropdown.Trigger>

                            <Dropdown.Content>
                                <Dropdown.Link href={route("profile.edit")}>
                                    Profile
                                </Dropdown.Link>
                                <Dropdown.Link
                                    href={route("logout")}
                                    method="post"
                                    as="button"
                                >
                                    Sign out
                                </Dropdown.Link>
                            </Dropdown.Content>
                        </Dropdown>
                        <Link
                            href={route("notifications")}
                            className="relative"
                        >
                            <IoMdNotifications size={30} />
                            {unreadCount > 0 && (
                                <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-semibold rounded-full px-2">
                                    {unreadCount}
                                </span>
                            )}
                        </Link>
                    </div>
                ) : (
                    <div className="flex items-center space-x-4">
                        <Link href={route("login")}>
                            <SecondaryButton>Login</SecondaryButton>
                        </Link>
                        <Link href={route("register")}>
                            <PrimaryButton>Register</PrimaryButton>
                        </Link>
                    </div>
                )}
            </div>
        </nav>
    );
}
